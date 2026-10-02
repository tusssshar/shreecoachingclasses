<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Data for the teacher, parent and student portals. Exam/marks logic is reused
 * from Exam_model; pure rules live in sms_portal_helper / sms_exam_helper.
 */
class Portal_model extends CI_Model
{
    /** Saved menu permission matrix [role => [menu => 0|1]]. */
    function permissions()
    {
        static $p = null;
        if ($p === null) {
            $row = $this->db->get_where('settings', array('type' => 'menu_permissions'))->row();
            $p = sms_parse_menu_permissions($row ? $row->description : '');
        }
        return $p;
    }

    function save_permissions($matrix)
    {
        $json = json_encode($matrix);
        if ($this->db->get_where('settings', array('type' => 'menu_permissions'))->row())
            $this->db->where('type', 'menu_permissions')->update('settings', array('description' => $json));
        else
            $this->db->insert('settings', array('type' => 'menu_permissions', 'description' => $json));
    }

    /* ---------------- Teacher ---------------- */

    /** Subjects a teacher teaches, with class names. */
    function teacher_subjects($teacher_id)
    {
        return $this->db->select('s.subject_id, s.name, s.class_id, c.name class_name, c.name_numeric', false)
            ->from('subject s')->join('class c', 'c.class_id = s.class_id')
            ->where('s.teacher_id', (int)$teacher_id)
            ->order_by('c.name_numeric + 0', 'ASC', false)->order_by('s.name', 'ASC')->get()->result_array();
    }

    /** Classes where the teacher is class teacher (class or section teacher). */
    function class_teacher_classes($teacher_id)
    {
        $ids = array_column($this->db->select('class_id')->get_where('class', array('teacher_id' => (int)$teacher_id))->result_array(), 'class_id');
        $ids = array_merge($ids, array_column($this->db->select('class_id')->get_where('section', array('teacher_id' => (int)$teacher_id))->result_array(), 'class_id'));
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (!$ids) return array();
        return $this->db->where_in('class_id', $ids)->order_by('name_numeric + 0', 'ASC', false)->get('class')->result_array();
    }

    /** Every class the teacher is connected to (class teacher or subject teacher). */
    function teacher_class_ids($teacher_id)
    {
        $ids = array_column($this->class_teacher_classes($teacher_id), 'class_id');
        $ids = array_merge($ids, array_column($this->teacher_subjects($teacher_id), 'class_id'));
        return array_values(array_unique(array_map('intval', $ids)));
    }

    /** Sections (batches) the teacher takes, with timings. */
    function teacher_sections($teacher_id)
    {
        return $this->db->select('sec.*, c.name class_name', false)->from('section sec')->join('class c', 'c.class_id = sec.class_id')
            ->where('sec.teacher_id', (int)$teacher_id)->order_by('sec.start_time', 'ASC')->get()->result_array();
    }

    function students_of_classes($class_ids)
    {
        if (!$class_ids) return array();
        return $this->db->select('s.student_id, s.name, s.roll, s.sex, s.email, s.fmobile, s.class_id, c.name class_name, p.name parent_name, p.phone parent_phone', false)
            ->from('student s')->join('class c', 'c.class_id = s.class_id')->join('parent p', 'p.parent_id = s.parent_id', 'left')
            ->where_in('s.class_id', array_map('strval', $class_ids))->where('s.is_active', 1)
            ->order_by('c.name_numeric + 0', 'ASC', false)->order_by('s.name', 'ASC')->get()->result_array();
    }

    /* ---------------- Attendance ---------------- */

    /** [student_id => status] for a class on a date. */
    function class_attendance($class_id, $date)
    {
        $out = array();
        $ids = array_column($this->students_of_classes(array((int)$class_id)), 'student_id');
        if (!$ids) return $out;
        foreach ($this->db->where_in('student_id', $ids)->where('date', $date)->get('attendance')->result_array() as $r)
            $out[(int)$r['student_id']] = (int)$r['status'];
        return $out;
    }

    /** Save statuses (1 present, 2 absent) for students of one class. Returns rows saved. */
    function save_class_attendance($class_id, $date, $statuses)
    {
        $allowed = array_map('intval', array_column($this->students_of_classes(array((int)$class_id)), 'student_id'));
        $n = 0;
        foreach ((array)$statuses as $sid => $status) {
            $sid = (int)$sid; $status = (int)$status === 2 ? 2 : 1;
            if (!in_array($sid, $allowed, true)) continue;
            $row = $this->db->get_where('attendance', array('student_id' => $sid, 'date' => $date))->row();
            if ($row) $this->db->where('attendance_id', $row->attendance_id)->update('attendance', array('status' => $status));
            else      $this->db->insert('attendance', array('student_id' => $sid, 'date' => $date, 'status' => $status));
            $n++;
        }
        return $n;
    }

    /** One student's attendance rows (newest first) between two dates. */
    function student_attendance($student_id, $from, $to)
    {
        return $this->db->where('student_id', (int)$student_id)->where('date >=', $from)->where('date <=', $to)
            ->order_by('date', 'DESC')->get('attendance')->result_array();
    }

    /* ---------------- Fees ---------------- */

    /** Total / paid / balance (same rule as Modal::getStudentFeeSummary) and payment history. */
    function fee_summary($student_id)
    {
        $st = $this->db->get_where('student', array('student_id' => (int)$student_id))->row_array();
        if (!$st) return null;
        $hist = $this->db->select_sum('amount')->where('student_id', (int)$student_id)->get('student_payment_history')->row();
        $legacy = $this->db->table_exists('payment')
            ? $this->db->select_sum('amount')->where('student_id', (int)$student_id)->get('payment')->row() : null;
        $total = (float)($st['total_fees'] ?: 0);
        $paid = sms_total_paid($hist && $hist->amount ? (float)$hist->amount : 0, $legacy && $legacy->amount ? (float)$legacy->amount : 0,
                               (float)($st['payment_done'] ?: 0));
        return array(
            'total' => $total, 'paid' => $paid, 'balance' => sms_fee_remaining($total, $paid),
            'history' => $this->db->order_by('timestamp', 'DESC')->get_where('student_payment_history', array('student_id' => (int)$student_id))->result_array(),
        );
    }

    /* ---------------- Notices ---------------- */

    function notices($limit = 20)
    {
        return $this->db->order_by('create_timestamp', 'DESC')->limit($limit)->get('noticeboard')->result_array();
    }

    function holidays($limit = 20)
    {
        return $this->db->order_by('holiday_id', 'DESC')->limit($limit)->get('holiday')->result_array();
    }

    /* ---------------- Parent ---------------- */

    function children($parent_id)
    {
        return $this->db->select('s.student_id, s.name, s.sex, s.roll, s.class_id, c.name class_name', false)
            ->from('student s')->join('class c', 'c.class_id = s.class_id', 'left')
            ->where('s.parent_id', (int)$parent_id)->where('s.is_active', 1)->order_by('s.name', 'ASC')->get()->result_array();
    }

    /* ---------------- Results for one student ---------------- */

    /** Published CBT exams of a student with attempt data and state. */
    function student_cbt($student_id)
    {
        $this->load->model('exam_model');
        $now = time();
        $out = array();
        $rows = $this->db->select('a.status attempt_status, a.started_at, a.submitted_at, a.score, a.total, e.exam_id', false)
            ->from('exam_assignment a')->join('cbt_exam e', 'e.exam_id = a.exam_id')
            ->where('a.student_id', (int)$student_id)->where('e.status', 'published')->get()->result_array();
        foreach ($rows as $r) {
            $exam = $this->exam_model->cbt_exam($r['exam_id']);
            if ($exam) $out[] = $exam + array('state' => sms_cbt_state($exam, $now), 'attempt' => $r);
        }
        usort($out, function ($a, $b) { return strcmp($b['exam_date'] . $b['start_time'], $a['exam_date'] . $a['start_time']); });
        return $out;
    }

    /** Published written-exam results of a student: [exam, subjects, row]. */
    function student_written($student_id)
    {
        $this->load->model('exam_model');
        $st = $this->db->get_where('student', array('student_id' => (int)$student_id))->row_array();
        $out = array();
        if (!$st) return $out;
        foreach ($this->db->order_by('exam_date', 'DESC')->get_where('exam', array('results_published' => 1))->result_array() as $exam) {
            $tab = $this->exam_model->tabulation($exam['exam_id'], $st['class_id']);
            foreach ($tab['rows'] as $row)
                if ((int)$row['student']['student_id'] === (int)$student_id && $row['percent'] !== null)
                    $out[] = array('exam' => $exam, 'subjects' => $tab['subjects'], 'row' => $row);
        }
        return $out;
    }
}
