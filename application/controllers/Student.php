<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Student portal: dashboard, online (CBT) exams with timer and auto-marking,
 * published results, written-exam marks and own profile/password.
 * Timing / marking rules live in sms_exam_helper (unit tested).
 */
class Student extends Portal_Controller
{
    protected $role = 'student';
    protected $base = 'student';

    /** Seconds accepted after the deadline (slow network on the final autosave/submit). */
    const GRACE_SECONDS = 60;

    /** My CBT exams (assigned + published) with state and my attempt. */
    private function my_cbt_exams()
    {
        $now = time();
        $rows = $this->db->select('a.status attempt_status, a.started_at, a.submitted_at, a.score, a.total, e.exam_id', false)
            ->from('exam_assignment a')->join('cbt_exam e', 'e.exam_id = a.exam_id')
            ->where('a.student_id', $this->me())->where('e.status', 'published')->get()->result_array();
        $out = array();
        foreach ($rows as $r) {
            $exam = $this->exam_model->cbt_exam($r['exam_id']);
            if (!$exam) continue;
            if ($r['attempt_status'] === 'in_progress' && $now >= sms_cbt_deadline($exam, $r['started_at']) + self::GRACE_SECONDS) {
                $this->exam_model->submit_attempt($exam['exam_id'], $this->me(), sms_cbt_deadline($exam, $r['started_at']));
                $r = array_merge($r, $this->exam_model->assignment($exam['exam_id'], $this->me()));
                $r['attempt_status'] = $r['status'];
            }
            $out[] = $exam + array('state' => sms_cbt_state($exam, $now), 'attempt' => $r);
        }
        usort($out, function ($a, $b) { return strcmp($b['exam_date'] . $b['start_time'], $a['exam_date'] . $a['start_time']); });
        return $out;
    }

    function index()
    {
        redirect(base_url() . 'index.php?student/dashboard', 'refresh');
    }

    function dashboard()
    {
        $exams = $this->my_cbt_exams();
        $page_data['upcoming'] = array_values(array_filter($exams, function ($e) {
            return in_array($e['state'], array('upcoming', 'open'), true) && in_array($e['attempt']['attempt_status'], array('assigned', 'in_progress'), true);
        }));
        $page_data['results'] = array_slice(array_values(array_filter($exams, function ($e) {
            return $e['results_published'] && in_array($e['attempt']['attempt_status'], array('submitted', 'checked'), true);
        })), 0, 5);
        $page_data['written'] = $this->written_results();
        $page_data['student'] = $this->exam_model->student($this->me());
        $page_data['page_name']  = 'dashboard';
        $page_data['page_title'] = get_phrase('student_dashboard');
        $this->load->view('backend/index', $page_data);
    }

    function exams()
    {
        $this->allow('online_exams');
        $page_data['exams']      = $this->my_cbt_exams();
        $page_data['page_name']  = 'exams';
        $page_data['page_title'] = get_phrase('my_online_exams');
        $this->load->view('backend/index', $page_data);
    }

    /** Load an exam the student may sit now, or bounce back with the reason. */
    private function sittable($exam_id)
    {
        $exam = $this->exam_model->cbt_exam($exam_id);
        $a = $exam ? $this->exam_model->assignment($exam_id, $this->me()) : null;
        if (!$exam || !$a || $exam['status'] !== 'published') $this->back('exams', get_phrase('exam_not_found'), true);
        if (in_array($a['status'], array('submitted', 'checked'), true)) $this->back('exams', get_phrase('you_have_already_submitted_this_exam'), true);
        $state = sms_cbt_state($exam, time());
        if ($a['status'] === 'assigned' && $state === 'upcoming') $this->back('exams', get_phrase('this_exam_has_not_started_yet'), true);
        if ($a['status'] === 'assigned' && $state === 'closed') $this->back('exams', get_phrase('this_exam_has_closed'), true);
        return array($exam, $a);
    }

    function take_exam($exam_id = 0)
    {
        $this->allow('online_exams');
        list($exam, $a) = $this->sittable($exam_id);
        $now = time();
        $a = $this->exam_model->start_attempt($exam_id, $this->me(), $now);
        $deadline = sms_cbt_deadline($exam, $a['started_at']);
        if ($now >= $deadline + self::GRACE_SECONDS) {
            $this->exam_model->submit_attempt($exam_id, $this->me(), $deadline);
            $this->back('exams', get_phrase('time_is_up_your_answers_were_submitted'));
        }
        $questions = $this->exam_model->cbt_questions($exam_id);
        foreach ($questions as &$q) unset($q['correct_answers']);   // never send answers to the browser
        unset($q);
        $saved = array();
        foreach ($this->exam_model->student_answers($exam_id, $this->me()) as $qid => $r) $saved[$qid] = $r['answer'];
        $page_data['exam']         = $exam;
        $page_data['questions']    = $questions;
        $page_data['saved']        = $saved;
        $page_data['seconds_left'] = max(0, $deadline - $now);
        $page_data['page_name']    = 'take_exam';
        $page_data['page_title']   = $exam['title'];
        $this->load->view('backend/index', $page_data);
    }

    /** AJAX autosave of one answer. */
    function save_answer($exam_id = 0)
    {
        if (!$this->can('online_exams')) return $this->json(array('ok' => false, 'error' => 'no_access'));
        $exam = $this->exam_model->cbt_exam($exam_id);
        $a = $exam ? $this->exam_model->assignment($exam_id, $this->me()) : null;
        if (!$a || $a['status'] !== 'in_progress') return $this->json(array('ok' => false, 'error' => 'not_in_progress'));
        $deadline = sms_cbt_deadline($exam, $a['started_at']);
        if (time() > $deadline + self::GRACE_SECONDS) return $this->json(array('ok' => false, 'error' => 'time_up'));
        $ok = $this->exam_model->save_answer($exam_id, $this->me(), (int)$this->input->post('question_id'), (string)$this->input->post('answer'));
        $this->json(array('ok' => $ok, 'seconds_left' => max(0, $deadline - time())));
    }

    function submit_exam($exam_id = 0)
    {
        $this->allow('online_exams');
        $exam = $this->exam_model->cbt_exam($exam_id);
        $a = $exam ? $this->exam_model->assignment($exam_id, $this->me()) : null;
        if (!$a || $a['status'] !== 'in_progress') $this->back('exams', get_phrase('exam_not_in_progress'), true);
        $deadline = sms_cbt_deadline($exam, $a['started_at']);
        // Answers posted with the form cover any autosave that did not reach the server.
        if (time() <= $deadline + self::GRACE_SECONDS) {
            foreach ((array)$this->input->post('answer') as $qid => $label)
                if (trim((string)$label) !== '') $this->exam_model->save_answer($exam_id, $this->me(), (int)$qid, $label);
        }
        $this->exam_model->submit_attempt($exam_id, $this->me(), min(time(), $deadline));
        $msg = get_phrase('exam_submitted_successfully');
        $msg .= $exam['results_published'] ? '' : '. ' . get_phrase('your_result_will_be_shown_once_published');
        $this->back('exams', $msg);
    }

    function exam_result($exam_id = 0)
    {
        $this->allow('online_exams');
        $exam = $this->exam_model->cbt_exam($exam_id);
        $a = $exam ? $this->exam_model->assignment($exam_id, $this->me()) : null;
        if (!$a || !in_array($a['status'], array('submitted', 'checked'), true)) $this->back('exams', get_phrase('result_not_available'), true);
        if (!$exam['results_published']) $this->back('exams', get_phrase('result_not_published_yet'), true);
        $rank = null;
        foreach ($this->exam_model->cbt_results($exam_id) as $r) if ((int)$r['student_id'] === $this->me()) $rank = $r['rank'];
        $page_data['exam']       = $exam;
        $page_data['attempt']    = $a;
        $page_data['rank']       = $rank;
        $page_data['questions']  = $this->exam_model->cbt_questions($exam_id);
        $page_data['answers']    = $this->exam_model->student_answers($exam_id, $this->me());
        $page_data['page_name']  = 'exam_result';
        $page_data['page_title'] = get_phrase('exam_result') . ': ' . $exam['title'];
        $this->load->view('backend/index', $page_data);
    }

    /** Published written-exam results for my class: one row per exam. */
    private function written_results()
    {
        $st = $this->exam_model->student($this->me());
        $out = array();
        foreach ($this->db->order_by('exam_date', 'DESC')->get_where('exam', array('results_published' => 1))->result_array() as $exam) {
            $tab = $this->exam_model->tabulation($exam['exam_id'], $st['class_id']);
            foreach ($tab['rows'] as $row) {
                if ((int)$row['student']['student_id'] !== $this->me() || $row['percent'] === null) continue;
                $out[] = array('exam' => $exam, 'subjects' => $tab['subjects'], 'row' => $row);
            }
        }
        return $out;
    }

    function marks()
    {
        $this->allow('marks');
        $page_data['results']    = $this->written_results();
        $page_data['page_name']  = 'marks';
        $page_data['page_title'] = get_phrase('my_marks');
        $this->load->view('backend/index', $page_data);
    }

    function manage_profile($action = '')
    {
        if ($action == 'change_password') $this->change_password('student', 'student_id');
        $page_data['student']    = $this->exam_model->student($this->me());
        $page_data['page_name']  = 'manage_profile';
        $page_data['page_title'] = get_phrase('manage_profile');
        $this->load->view('backend/index', $page_data);
    }
    function attendance($month = '')
    {
        $this->allow('attendance');
        $month = preg_match('/^\d{4}-\d{2}$/', $month) ? $month : date('Y-m');
        $rows = $this->portal_model->student_attendance($this->me(), $month . '-01', date('Y-m-t', strtotime($month . '-01')));
        $this->render('../portal/attendance', get_phrase('my_attendance'), array('rows' => $rows, 'summary' => sms_attendance_summary($rows), 'month' => $month));
    }

    function fees()
    {
        $this->allow('fees');
        $this->render('../portal/fees', get_phrase('fees'), array('fees' => $this->portal_model->fee_summary($this->me())));
    }

    function notices()
    {
        $this->allow('notices');
        $this->render('../portal/notices', get_phrase('noticeboard'), array('notices' => $this->portal_model->notices(), 'holidays' => $this->portal_model->holidays()));
    }
}
