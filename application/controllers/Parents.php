<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Parent portal (read-only). A parent only sees their own children: dashboard,
 * published results, online exams, attendance, fees and notices. With several
 * children, a switcher picks the child. Menus follow Settings > Menu Permissions.
 */
class Parents extends Portal_Controller
{
    protected $role = 'parent';
    protected $base = 'parents';

    function index()
    {
        redirect(base_url() . 'index.php?parents/dashboard', 'refresh');
    }

    /** Children of this parent, and the one currently selected. */
    private function child()
    {
        $children = $this->portal_model->children($this->me());
        $want = (int)$this->session->userdata('parent_child');
        $sel = null;
        foreach ($children as $c) if ((int)$c['student_id'] === $want) $sel = $c;
        if (!$sel && $children) $sel = $children[0];
        return array($children, $sel);
    }

    /** Switch child: parents/child/<student_id> (only own children). */
    function child_select($student_id = 0)
    {
        foreach ($this->portal_model->children($this->me()) as $c)
            if ((int)$c['student_id'] === (int)$student_id) $this->session->set_userdata('parent_child', (int)$student_id);
        $back = $this->input->server('HTTP_REFERER');
        redirect($back && strpos($back, base_url()) === 0 ? $back : base_url() . 'index.php?parents/dashboard', 'refresh');
    }

    private function page($view, $title, $data = array())
    {
        list($children, $child) = $this->child();
        $this->render($view, $title, $data + array('children' => $children, 'child' => $child));
    }

    function dashboard()
    {
        list(, $child) = $this->child();
        $data = array('parent' => $this->db->get_where('parent', array('parent_id' => $this->me()))->row_array());
        if ($child) {
            $sid = (int)$child['student_id'];
            $cbt = $this->portal_model->student_cbt($sid);
            $data['upcoming'] = array_values(array_filter($cbt, function ($e) {
                return in_array($e['state'], array('upcoming', 'open'), true) && $e['attempt']['attempt_status'] === 'assigned'; }));
            $data['cbt_results'] = array_slice(array_values(array_filter($cbt, function ($e) {
                return $e['results_published'] && in_array($e['attempt']['attempt_status'], array('submitted', 'checked'), true); })), 0, 5);
            $data['written'] = array_slice($this->portal_model->student_written($sid), 0, 5);
            $data['fees'] = $this->can('fees') ? $this->portal_model->fee_summary($sid) : null;
            $data['attendance'] = $this->can('attendance')
                ? sms_attendance_summary($this->portal_model->student_attendance($sid, date('Y-m-01'), date('Y-m-d'))) : null;
            $data['notices'] = $this->can('notices') ? $this->portal_model->notices(3) : array();
        }
        $this->page('dashboard', get_phrase('parent_dashboard'), $data);
    }

    function results()
    {
        $this->allow('results');
        list(, $child) = $this->child();
        $data = array();
        if ($child) {
            $data['written'] = $this->portal_model->student_written($child['student_id']);
            $data['cbt'] = array_values(array_filter($this->portal_model->student_cbt($child['student_id']), function ($e) {
                return $e['results_published'] && in_array($e['attempt']['attempt_status'], array('submitted', 'checked'), true); }));
        }
        $this->page('results', get_phrase('results'), $data);
    }

    /** Detailed answers of a published online exam of the selected child. */
    function exam_result($exam_id = 0)
    {
        $this->allow('results');
        list(, $child) = $this->child();
        $exam = $this->exam_model->cbt_exam($exam_id);
        $a = ($exam && $child) ? $this->exam_model->assignment($exam_id, $child['student_id']) : null;
        if (!$a || !$exam['results_published'] || !in_array($a['status'], array('submitted', 'checked'), true))
            $this->back('results', get_phrase('result_not_published_yet'), true);
        $rank = null;
        foreach ($this->exam_model->cbt_results($exam_id) as $r) if ((int)$r['student_id'] === (int)$child['student_id']) $rank = $r['rank'];
        $this->page('../student/exam_result', get_phrase('exam_result') . ': ' . $exam['title'], array(
            'exam' => $exam, 'attempt' => $a, 'rank' => $rank, 'back_url' => base_url() . 'index.php?parents/results',
            'questions' => $this->exam_model->cbt_questions($exam_id), 'answers' => $this->exam_model->student_answers($exam_id, $child['student_id'])));
    }

    function exams()
    {
        $this->allow('online_exams');
        list(, $child) = $this->child();
        $this->page('exams', get_phrase('online_exams'), array('exams' => $child ? $this->portal_model->student_cbt($child['student_id']) : array()));
    }

    function attendance($month = '')
    {
        $this->allow('attendance');
        list(, $child) = $this->child();
        $month = preg_match('/^\d{4}-\d{2}$/', $month) ? $month : date('Y-m');
        $rows = $child ? $this->portal_model->student_attendance($child['student_id'], $month . '-01', date('Y-m-t', strtotime($month . '-01'))) : array();
        $this->page('../portal/attendance', get_phrase('attendance'), array('rows' => $rows, 'summary' => sms_attendance_summary($rows), 'month' => $month));
    }

    function fees()
    {
        $this->allow('fees');
        list(, $child) = $this->child();
        $this->page('../portal/fees', get_phrase('fees'), array('fees' => $child ? $this->portal_model->fee_summary($child['student_id']) : null));
    }

    function notices()
    {
        $this->allow('notices');
        $this->page('../portal/notices', get_phrase('noticeboard'), array('notices' => $this->portal_model->notices(), 'holidays' => $this->portal_model->holidays()));
    }

    function manage_profile($action = '')
    {
        if ($action == 'change_password') $this->change_password('parent', 'parent_id');
        $p = $this->db->get_where('parent', array('parent_id' => $this->me()))->row_array();
        $names = array_column($this->portal_model->children($this->me()), 'name');
        $this->page('../portal/profile', get_phrase('my_profile'), array('profile' => array(
            get_phrase('name') => $p['name'], get_phrase('email') => $p['email'], get_phrase('phone') => $p['phone'],
            get_phrase('children') => implode(', ', $names))));
    }
}
