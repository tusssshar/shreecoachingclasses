<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Teacher portal. A teacher only sees their own classes and subjects:
 * timetable, students, attendance (class teacher; today .. 7 days back), written-exam
 * marks and online paper checking for subjects they teach (locked once results are
 * published), results and notices. Menus follow Settings > Menu Permissions.
 */
class Teacher extends Portal_Controller
{
    protected $role = 'teacher';
    protected $base = 'teacher';

    function index()
    {
        redirect(base_url() . 'index.php?teacher/dashboard', 'refresh');
    }

    private function my_subject_ids()
    {
        return array_map('intval', array_column($this->portal_model->teacher_subjects($this->me()), 'subject_id'));
    }

    function dashboard()
    {
        $me = $this->me();
        $subjects = $this->portal_model->teacher_subjects($me);
        $sub_ids = array_map('intval', array_column($subjects, 'subject_id'));
        $today = date('l');
        $sections = array_filter($this->portal_model->teacher_sections($me), function ($s) use ($today) {
            return $s['days'] === null || $s['days'] === '' || stripos($s['days'], $today) !== false;
        });
        $pending = 0; $upcoming = array();
        foreach ($this->exam_model->cbt_exams(array('e.status' => 'published')) as $e) {
            if (!in_array((int)$e['subject_id'], $sub_ids, true)) continue;
            $state = sms_cbt_state($e, time());
            if (in_array($state, array('upcoming', 'open'), true)) $upcoming[] = $e + array('state' => $state);
            $pending += $this->db->where(array('exam_id' => $e['exam_id'], 'status' => 'submitted'))->count_all_results('exam_assignment');
        }
        $this->render('dashboard', get_phrase('teacher_dashboard'), array(
            'teacher'        => $this->db->get_where('teacher', array('teacher_id' => $me))->row_array(),
            'subjects'       => $subjects,
            'today_sections' => array_values($sections),
            'student_count'  => count($this->portal_model->students_of_classes($this->portal_model->teacher_class_ids($me))),
            'class_teacher'  => $this->portal_model->class_teacher_classes($me),
            'upcoming'       => $upcoming,
            'pending_checks' => $pending,
            'notices'        => $this->can('notices') ? $this->portal_model->notices(3) : array(),
        ));
    }

    function timetable()
    {
        $this->allow('timetable');
        $this->render('timetable', get_phrase('my_timetable'), array(
            'sections' => $this->portal_model->teacher_sections($this->me()),
            'subjects' => $this->portal_model->teacher_subjects($this->me()),
        ));
    }

    function students($class_id = '')
    {
        $this->allow('students');
        $ids = $this->portal_model->teacher_class_ids($this->me());
        if ($class_id !== '' && in_array((int)$class_id, $ids, true)) $ids = array((int)$class_id);
        $this->render('students', get_phrase('my_students'), array(
            'students' => $this->portal_model->students_of_classes($ids),
            'classes'  => $this->db->where_in('class_id', $this->portal_model->teacher_class_ids($this->me()) ?: array(0))->order_by('name_numeric + 0', 'ASC', false)->get('class')->result_array(),
            'class_id' => (int)$class_id,
        ));
    }

    /** Class teacher marks attendance for one class and date. */
    function attendance($class_id = '', $date = '')
    {
        $this->allow('attendance');
        $classes = $this->portal_model->class_teacher_classes($this->me());
        $allowed = array_map('intval', array_column($classes, 'class_id'));
        $date = $date !== '' ? $date : date('Y-m-d');
        if ($this->input->post('class_id')) {
            $class_id = (int)$this->input->post('class_id'); $date = (string)$this->input->post('date');
            redirect(base_url() . 'index.php?teacher/attendance/' . $class_id . '/' . $date, 'refresh');
        }
        $data = array('classes' => $classes, 'class_id' => (int)$class_id, 'date' => $date,
                      'date_error' => sms_attendance_date_error($date, date('Y-m-d')));
        if ($class_id !== '') {
            if (!in_array((int)$class_id, $allowed, true)) $this->back('attendance', get_phrase('you_are_not_the_class_teacher_of_this_class'), true);
            if ($this->input->post('save')) {
                if ($data['date_error']) $this->back("attendance/$class_id/$date", get_phrase($data['date_error']), true);
                $n = $this->portal_model->save_class_attendance($class_id, $date, (array)$this->input->post('status'));
                $this->back("attendance/$class_id/$date", get_phrase('attendance_saved') . ' (' . $n . ')');
            }
            $data['students'] = $this->portal_model->students_of_classes(array((int)$class_id));
            $data['marked']   = $this->portal_model->class_attendance($class_id, $date);
        }
        $this->render('attendance', get_phrase('mark_attendance'), $data);
    }

    /** Written-exam marks for subjects the teacher teaches; locked once results are published. */
    function marks($exam_id = '', $subject_id = '')
    {
        $this->allow('marks');
        $subjects = $this->portal_model->teacher_subjects($this->me());
        $by_id = array(); foreach ($subjects as $s) $by_id[(int)$s['subject_id']] = $s;
        if ($this->input->post('operation') == 'selection') {
            redirect(base_url() . 'index.php?teacher/marks/' . (int)$this->input->post('exam_id') . '/' . (int)$this->input->post('subject_id'), 'refresh');
        }
        $data = array('exams' => $this->exam_model->classic_exams(), 'subjects' => $subjects, 'exam_id' => (int)$exam_id, 'subject_id' => (int)$subject_id);
        if ($exam_id && $subject_id) {
            if (!isset($by_id[(int)$subject_id])) $this->back('marks', get_phrase('you_do_not_teach_this_subject'), true);
            $exam = $this->exam_model->classic_exam($exam_id);
            $sub = $by_id[(int)$subject_id];
            if (!$exam) $this->back('marks', get_phrase('exam_not_found'), true);
            if (!in_array((string)$sub['class_id'], explode(',', (string)$exam['class_ids']), true))
                $this->back('marks', get_phrase('this_exam_is_not_for_that_class'), true);
            $here = 'marks/' . (int)$exam_id . '/' . (int)$subject_id;
            if ($this->input->post('operation') == 'update') {
                if ($exam['results_published']) $this->back($here, get_phrase('results_are_published_marks_are_locked'), true);
                $errors = array(); $saved = 0; $comments = (array)$this->input->post('comment'); $totals = (array)$this->input->post('mark_total');
                foreach ((array)$this->input->post('mark_obtained') as $mark_id => $value) {
                    $m = $this->db->get_where('mark', array('mark_id' => (int)$mark_id, 'exam_id' => (int)$exam_id, 'subject_id' => (int)$subject_id))->row();
                    if (!$m) continue;
                    $value = trim((string)$value);
                    $total = isset($totals[$mark_id]) ? (int)$totals[$mark_id] : (int)$m->mark_total;
                    if ($err = sms_mark_error($value, $total)) {
                        $st = $this->db->get_where('student', array('student_id' => $m->student_id))->row();
                        $errors[] = ($st ? $st->name : '#' . $m->student_id) . ': ' . get_phrase($err);
                        continue;
                    }
                    $this->db->where('mark_id', (int)$mark_id)->update('mark', array('mark_obtained' => $value === '' ? null : $value,
                        'mark_total' => $total, 'comment' => trim((string)($comments[$mark_id] ?? ''))));
                    $saved++;
                }
                if ($errors) {
                    $this->session->set_flashdata('mark_errors', $errors);
                    $this->back($here, $saved . ' ' . get_phrase('saved') . ', ' . count($errors) . ' ' . get_phrase('need_correction'), true);
                }
                $this->back($here, get_phrase('marks_saved') . ' (' . $saved . ')');
            }
            $data['exam'] = $exam; $data['subject'] = $sub;
            $data['students'] = $this->exam_model->subject_marks($exam_id, $sub['class_id'], $subject_id);
            $data['errors'] = (array)$this->session->flashdata('mark_errors');
        }
        $this->render('marks', get_phrase('enter_marks'), $data);
    }

    /** Online exams of the teacher's subjects: review answers and adjust marks until results are published. */
    function paper_checking($exam_id = '', $student_id = '', $action = '')
    {
        $this->allow('paper_checking');
        $sub_ids = $this->my_subject_ids();
        $exams = array_values(array_filter($this->exam_model->cbt_exams(array('e.status' => 'published')), function ($e) use ($sub_ids) {
            return in_array((int)$e['subject_id'], $sub_ids, true);
        }));
        $data = array('exams' => $exams);
        if ($exam_id !== '') {
            $exam = $this->exam_model->cbt_exam($exam_id);
            if (!$exam || !in_array((int)$exam['subject_id'], $sub_ids, true)) $this->back('paper_checking', get_phrase('you_do_not_teach_this_subject'), true);
            $this->exam_model->auto_submit_expired($exam, time());
            if ($student_id !== '') {
                $a = $this->exam_model->assignment($exam_id, $student_id);
                if (!$a || !in_array($a['status'], array('submitted', 'checked'), true))
                    $this->back('paper_checking/' . (int)$exam_id, get_phrase('this_student_has_not_submitted_yet'), true);
                if ($action == 'save') {
                    if ($exam['results_published']) $this->back('paper_checking/' . (int)$exam_id, get_phrase('results_are_published_marks_are_locked'), true);
                    $score = $this->exam_model->override_marks($exam_id, $student_id, (array)$this->input->post('awarded'));
                    $this->back('paper_checking/' . (int)$exam_id, get_phrase('marks_saved') . ': ' . $score);
                }
                $data['student'] = $this->exam_model->student($student_id);
                $data['attempt'] = $a;
                $data['questions'] = $this->exam_model->cbt_questions($exam_id);
                $data['answers'] = $this->exam_model->student_answers($exam_id, $student_id);
            }
            $data['exam'] = $this->exam_model->cbt_exam($exam_id);
            $data['assignments'] = $this->exam_model->assignments($exam_id);
        }
        $this->render('paper_checking', get_phrase('paper_checking'), $data);
    }

    /** Read-only results: CBT exams of my subjects and tabulation of my classes. */
    function results($type = '', $exam_id = '', $class_id = '')
    {
        $this->allow('results');
        $sub_ids = $this->my_subject_ids();
        $class_ids = $this->portal_model->teacher_class_ids($this->me());
        $data = array(
            'cbt_exams' => array_values(array_filter($this->exam_model->cbt_exams(array('e.status' => 'published')), function ($e) use ($sub_ids) {
                return in_array((int)$e['subject_id'], $sub_ids, true); })),
            'written'   => $this->exam_model->classic_exams(),
            'classes'   => $class_ids ? $this->db->where_in('class_id', $class_ids)->order_by('name_numeric + 0', 'ASC', false)->get('class')->result_array() : array(),
            'type' => $type,
        );
        if ($type === 'cbt' && $exam_id) {
            $exam = $this->exam_model->cbt_exam($exam_id);
            if (!$exam || !in_array((int)$exam['subject_id'], $sub_ids, true)) $this->back('results', get_phrase('you_do_not_teach_this_subject'), true);
            $data['exam'] = $exam; $data['results'] = $this->exam_model->cbt_results($exam_id);
        }
        if ($type === 'written' && $exam_id && $class_id) {
            if (!in_array((int)$class_id, $class_ids, true)) $this->back('results', get_phrase('you_do_not_teach_this_class'), true);
            $data['tab'] = $this->exam_model->tabulation($exam_id, $class_id);
            $data['class'] = $this->db->get_where('class', array('class_id' => (int)$class_id))->row_array();
        }
        $this->render('results', get_phrase('results'), $data);
    }

    function notices()
    {
        $this->allow('notices');
        $this->render('../portal/notices', get_phrase('noticeboard'), array('notices' => $this->portal_model->notices(), 'holidays' => $this->portal_model->holidays()));
    }

    function manage_profile($action = '')
    {
        if ($action == 'change_password') $this->change_password('teacher', 'teacher_id');
        $t = $this->db->get_where('teacher', array('teacher_id' => $this->me()))->row_array();
        $this->render('../portal/profile', get_phrase('my_profile'), array('profile' => array(
            get_phrase('name') => $t['name'], get_phrase('designation') => $t['designation'], get_phrase('email') => $t['email'],
            get_phrase('phone') => $t['phone'], get_phrase('joining_date') => $t['joining_date'])));
    }
}
