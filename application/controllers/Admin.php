<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 *	@author 	: Optimum Linkup Universal Concepts
 *	date		: 27 June, 2016
 *	Optimum Linkup Universal Concepts
 *	http://optimumlinkup.com.ng/school/Optimum Linkup Universal Concepts
 *	optimumproblemsolver@gmail.com
 */

class Admin extends CI_Controller
{
    public $export_service;
    public $exam_model;
    public $portal_model;

    /**
     * Weekly timetable — one page showing every teacher's lectures across the week.
     * Rows = teachers, columns = days Mon-Sun, cells list each lecture with class +
     * start-end times. Auto-prints when ?print=1 is appended.
     */
    function weekly_timetable($mode = '', $start = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_teacher_timetable_columns();

        if ($start === '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$mode)) {
            $start = $mode;
            $mode = '';
        }

        $start_raw = $start ?: $this->input->get('start');
        $start_ts = $start_raw ? strtotime($start_raw) : strtotime('monday this week');
        if (!$start_ts) $start_ts = strtotime('monday this week');
        $week_start = date('Y-m-d', $start_ts);
        $week_end   = date('Y-m-d', strtotime($week_start . ' +6 days'));

        // pull every timetable row with its teacher + class
        $this->db->select('s.section_id, s.name section_name, s.nick_name, s.days, s.start_time, s.end_time, '
                        . 's.session_name, s.revision_section, s.lecture_subject, '
                        . 'c.name class_name, c.name_numeric, t.teacher_id, t.name teacher_name');
        $this->db->from('section s');
        $this->db->join('class c',   'c.class_id   = s.class_id',   'left');
        $this->db->join('teacher t', 't.teacher_id = s.teacher_id', 'left');
        $this->db->order_by('c.name_numeric, c.name, s.start_time');
        $sections = $this->db->get()->result_array();

        $day_dates = array();
        for ($i = 0; $i < 7; $i++) {
            $ts = strtotime($week_start . ' +' . $i . ' days');
            $day_dates[date('l', $ts)] = array(
                'date'  => date('Y-m-d', $ts),
                'label' => date('d/m/Y', $ts),
                'day'   => strtoupper(date('l', $ts))
            );
        }

        $by_day = array();
        foreach ($day_dates as $day => $meta) {
            $by_day[$day] = array('meta' => $meta, 'morning' => array(), 'afternoon' => array());
        }

        foreach ($sections as $r) {
            $days = !empty($r['days']) ? explode(',', $r['days']) : array();
            foreach ($days as $d) {
                $d = trim($d);
                if ($d === '' || !isset($by_day[$d])) continue;

                $session = strtolower((string)($r['session_name'] ?? ''));
                if ($session !== 'morning' && $session !== 'afternoon') {
                    $hour = !empty($r['start_time']) ? (int)date('G', strtotime($r['start_time'])) : 0;
                    $session = ($hour > 0 && $hour < 12) ? 'morning' : 'afternoon';
                }
                $by_day[$d][$session][] = array(
                    'std'              => $r['class_name'] ?: '-',
                    'time'             => $this->format_timetable_time_range($r['start_time'], $r['end_time']),
                    'revision_section' => $r['revision_section'] ?: '',
                    'teacher'          => $r['teacher_name'] ?: '',
                    'lecture_subject'  => $r['lecture_subject'] ?: ($r['nick_name'] ?: $r['section_name']),
                    'start'            => $r['start_time'],
                    'class_sort'       => (int)($r['name_numeric'] ?? 0)
                );
            }
        }

        foreach ($by_day as &$day_group) {
            foreach (array('morning', 'afternoon') as $bucket) {
                usort($day_group[$bucket], function ($a, $b) {
                    $time_cmp = strcmp((string)$a['start'], (string)$b['start']);
                    if ($time_cmp !== 0) return $time_cmp;
                    return $a['class_sort'] <=> $b['class_sort'];
                });
            }
        }
        unset($day_group);

        $page_data['by_day']       = $by_day;
        $page_data['week_start']   = $week_start;
        $page_data['week_end']     = $week_end;
        $page_data['month_title']  = strtoupper(date('F', strtotime($week_start))) . ' = ' . date('Y', strtotime($week_start));
        $page_data['date_range']   = date('d/m/Y', strtotime($week_start)) . ' TO ' . date('d/m/Y', strtotime($week_end));
        $page_data['auto_print']   = ($mode === 'print') || (int)$this->input->get('print') === 1;

        if ($mode === 'print' || $this->input->get('view') === 'print' || $page_data['auto_print']) {
            $this->load->view('backend/admin/weekly_timetable_print', $page_data);
            return;
        }

        $page_data['page_name']  = 'weekly_timetable';
        $page_data['page_title'] = 'Weekly Timetable';
        $this->load->view('backend/index', $page_data);
    }

    private function format_timetable_time_range($start, $end)
    {
        if (empty($start) && empty($end)) return '';
        $fmt = function ($value) {
            if (empty($value)) return '';
            return strtoupper(str_replace(' ', '', date('g:i A', strtotime($value))));
        };
        $left = $fmt($start);
        $right = $fmt($end);
        return trim($left . ($right ? ' TO ' . $right : ''));
    }

    /**
     * Reports — Teachers report. Lists every teacher with key salary / contact
     * details, supports name search, has a print view and an Excel/CSV download.
     *
     * URLs:
     *   admin/report_teachers                          -> HTML report (search by name)
     *   admin/report_teachers?q=Sakshi                 -> filtered HTML report
     *   admin/report_teachers/excel?q=Sakshi           -> CSV download (Excel-friendly)
     *   admin/report_teachers/print?q=Sakshi           -> print-friendly view
     */
    function report_teachers($mode = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        // With uri_protocol=QUERY_STRING, a URL like ?admin/report_teachers/excel&ids=5
        // arrives as $mode = 'excel&ids=5'. Strip query-string remnants so the mode check still works.
        if (($amp = strpos($mode, '&')) !== false) {
            $mode = substr($mode, 0, $amp);
        }
        $mode = trim($mode);

        $this->ensure_teacher_salary_columns();
        $q = trim((string)$this->input->get('q'));
        $ids_raw = trim((string)$this->input->get('ids'));
        $selected_ids = array();
        if ($ids_raw !== '') {
            foreach (explode(',', $ids_raw) as $id) {
                $id = (int)trim($id);
                if ($id > 0) $selected_ids[] = $id;
            }
            $selected_ids = array_values(array_unique($selected_ids));
        }

        $this->db->select('teacher_id, name, email, phone, sex, blood_group, designation, joining_date, basic_salary, total_salary');
        if (!empty($selected_ids)) {
            $this->db->where_in('teacher_id', $selected_ids);
        }
        if ($q !== '') {
            $this->db->group_start();
            $this->db->like('name', $q);
            $this->db->or_like('email', $q);
            $this->db->or_like('phone', $q);
            $this->db->or_like('designation', $q);
            $this->db->group_end();
        }
        $this->db->order_by('name', 'asc');
        $rows = $this->db->get('teacher')->result_array();

        if ($mode === 'excel') {
            $filename = (!empty($selected_ids) ? 'selected_teachers_' : 'teachers_report_') . date('Ymd') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');
            $out = fopen('php://output', 'w');
            fputcsv($out, array('#','Teacher ID','Name','Designation','Email','Phone','Sex','Blood Group','Joining Date','Basic Salary','Net Salary'));
            $i = 1;
            foreach ($rows as $r) {
                fputcsv($out, array(
                    $i++,
                    'TCH-' . str_pad((int)$r['teacher_id'], 4, '0', STR_PAD_LEFT),
                    $r['name'],
                    $r['designation'] ?? '',
                    $r['email'],
                    $r['phone'],
                    $r['sex'],
                    $r['blood_group'] ?? '',
                    !empty($r['joining_date']) && $r['joining_date'] !== '0000-00-00' ? $r['joining_date'] : '',
                    (float)($r['basic_salary'] ?? 0),
                    (float)($r['total_salary'] ?? 0),
                ));
            }
            fclose($out);
            return;
        }

        $page_data['rows']       = $rows;
        $page_data['q']          = $q;
        $page_data['selected_ids'] = $selected_ids;
        $page_data['print_mode'] = ($mode === 'print');
        if ($mode === 'print') {
            $this->load->view('backend/admin/report_teachers_print', $page_data);
            return;
        }

        $page_data['page_name']  = 'report_teachers';
        $page_data['page_title'] = 'Teachers Report';
        $this->load->view('backend/index', $page_data);
    }

    /**
     * Reports — Students Report. Mirrors the Teachers Report shape: searchable HTML
     * page, printable view, and CSV export.
     *
     * URLs:
     *   admin/report_students                        -> HTML report
     *   admin/report_students?q=...                  -> filtered HTML
     *   admin/report_students/excel?q=...&ids=1,2    -> CSV
     *   admin/report_students/print?q=...            -> print view
     */
    function report_students($mode = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        // Strip query-string tail off mode so '?admin/.../excel&q=foo' still routes correctly
        if (($amp = strpos($mode, '&')) !== false) $mode = substr($mode, 0, $amp);
        $mode = trim($mode);

        $this->ensure_student_alumni_column();
        $q = trim((string)$this->input->get('q'));
        $ids_raw = trim((string)$this->input->get('ids'));
        $selected_ids = array();
        if ($ids_raw !== '') {
            foreach (explode(',', $ids_raw) as $id) {
                $id = (int)trim($id);
                if ($id > 0) $selected_ids[] = $id;
            }
            $selected_ids = array_values(array_unique($selected_ids));
        }

        $this->db->select('student_id, name, first_name, last_name, standard, academic_year, sex, fmobile, email, total_fees, payment_done');
        $this->db->where('is_active', 1);
        if ($this->db->field_exists('is_alumni', 'student')) $this->db->where('is_alumni', 0);
        if (!empty($selected_ids)) $this->db->where_in('student_id', $selected_ids);
        if ($q !== '') {
            $this->db->group_start();
            $this->db->like('name', $q);
            $this->db->or_like('first_name', $q);
            $this->db->or_like('last_name', $q);
            $this->db->or_like('email', $q);
            $this->db->or_like('fmobile', $q);
            $this->db->group_end();
        }
        $this->db->order_by('name', 'asc');
        $rows = $this->db->get('student')->result_array();

        if ($mode === 'excel') {
            $filename = (!empty($selected_ids) ? 'selected_students_' : 'students_report_') . date('Ymd') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');
            $out = fopen('php://output', 'w');
            fputcsv($out, array('#','Student ID','Name','Standard','Academic Year','Sex','Father Mobile','Email','Total Fees','Paid','Balance'));
            $i = 1;
            foreach ($rows as $r) {
                $bal = (float)($r['total_fees'] ?? 0) - (float)($r['payment_done'] ?? 0);
                fputcsv($out, array(
                    $i++,
                    'STU-' . str_pad((int)$r['student_id'], 5, '0', STR_PAD_LEFT),
                    $r['name'],
                    $r['standard'],
                    $r['academic_year'] ?? '',
                    $r['sex'],
                    $r['fmobile'],
                    $r['email'],
                    (float)($r['total_fees'] ?? 0),
                    (float)($r['payment_done'] ?? 0),
                    $bal,
                ));
            }
            fclose($out);
            return;
        }

        $page_data['rows']         = $rows;
        $page_data['q']            = $q;
        $page_data['selected_ids'] = $selected_ids;
        $page_data['print_mode']   = ($mode === 'print');
        if ($mode === 'print') {
            $this->load->view('backend/admin/report_students_print', $page_data);
            return;
        }
        $page_data['page_name']  = 'report_students';
        $page_data['page_title'] = 'Students Report';
        $this->load->view('backend/index', $page_data);
    }

    /**
     * Reports — Alumni Report. All students currently flagged as is_alumni = 1.
     * Same shape as Students Report (search, print, CSV).
     */
    function report_alumni($mode = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if (($amp = strpos($mode, '&')) !== false) $mode = substr($mode, 0, $amp);
        $mode = trim($mode);

        $this->ensure_student_alumni_column();
        $q = trim((string)$this->input->get('q'));

        $this->db->select('student_id, name, standard, academic_year, sex, fmobile, email, total_fees, payment_done');
        if ($this->db->field_exists('is_alumni', 'student')) {
            $this->db->where('is_alumni', 1);
        } else {
            $this->db->where('1 = 0', null, false);
        }
        if ($q !== '') {
            $this->db->group_start();
            $this->db->like('name', $q);
            $this->db->or_like('email', $q);
            $this->db->or_like('fmobile', $q);
            $this->db->group_end();
        }
        $this->db->order_by('name', 'asc');
        $rows = $this->db->get('student')->result_array();

        if ($mode === 'excel') {
            $filename = 'alumni_report_' . date('Ymd') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            $out = fopen('php://output', 'w');
            fputcsv($out, array('#','Student ID','Name','Standard','Academic Year','Sex','Father Mobile','Email','Total Fees','Paid'));
            $i = 1;
            foreach ($rows as $r) {
                fputcsv($out, array(
                    $i++,
                    'STU-' . str_pad((int)$r['student_id'], 5, '0', STR_PAD_LEFT),
                    $r['name'],
                    $r['standard'],
                    $r['academic_year'] ?? '',
                    $r['sex'],
                    $r['fmobile'],
                    $r['email'],
                    (float)($r['total_fees'] ?? 0),
                    (float)($r['payment_done'] ?? 0),
                ));
            }
            fclose($out);
            return;
        }

        $page_data['rows'] = $rows;
        $page_data['q']    = $q;
        if ($mode === 'print') {
            $this->load->view('backend/admin/report_alumni_print', $page_data);
            return;
        }
        $page_data['page_name']  = 'report_alumni';
        $page_data['page_title'] = 'Alumni Report';
        $this->load->view('backend/index', $page_data);
    }

    /**
     * Reports — Re-register History. Every student row with previous_student_id
     * set (i.e. a re-registration record), joined to the previous row so admins
     * can audit what came from where.
     */
    function report_reregister($mode = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if (($amp = strpos($mode, '&')) !== false) $mode = substr($mode, 0, $amp);
        $mode = trim($mode);

        $this->ensure_student_alumni_column();
        $q = trim((string)$this->input->get('q'));

        $sql = "SELECT n.student_id new_id, n.name new_name, n.standard new_standard, n.academic_year new_ay, "
             . "n.total_fees new_total, n.payment_done new_paid, "
             . "p.student_id prev_id, p.name prev_name, p.standard prev_standard, p.academic_year prev_ay, "
             . "p.total_fees prev_total, p.payment_done prev_paid "
             . "FROM student n "
             . "LEFT JOIN student p ON p.student_id = n.previous_student_id "
             . "WHERE n.previous_student_id IS NOT NULL AND n.previous_student_id > 0 ";
        if ($q !== '') {
            $like = '%' . $this->db->escape_like_str($q) . '%';
            $sql .= "AND (n.name LIKE '$like' OR p.name LIKE '$like' OR n.fmobile LIKE '$like' OR p.fmobile LIKE '$like') ";
        }
        $sql .= "ORDER BY n.student_id DESC";
        $rows = $this->db->query($sql)->result_array();

        if ($mode === 'excel') {
            $filename = 'reregister_history_' . date('Ymd') . '.csv';
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            $out = fopen('php://output', 'w');
            fputcsv($out, array(
                '#', 'Previous Student ID', 'Previous Name', 'Previous Standard', 'Previous AY', 'Previous Fees', 'Previous Paid',
                'New Student ID', 'New Name', 'New Standard', 'New AY', 'New Fees', 'New Paid'
            ));
            $i = 1;
            foreach ($rows as $r) {
                fputcsv($out, array(
                    $i++,
                    'STU-' . str_pad((int)$r['prev_id'], 5, '0', STR_PAD_LEFT),
                    $r['prev_name'],
                    $r['prev_standard'],
                    $r['prev_ay'],
                    (float)$r['prev_total'],
                    (float)$r['prev_paid'],
                    'STU-' . str_pad((int)$r['new_id'], 5, '0', STR_PAD_LEFT),
                    $r['new_name'],
                    $r['new_standard'],
                    $r['new_ay'],
                    (float)$r['new_total'],
                    (float)$r['new_paid'],
                ));
            }
            fclose($out);
            return;
        }

        $page_data['rows'] = $rows;
        $page_data['q']    = $q;
        if ($mode === 'print') {
            $this->load->view('backend/admin/report_reregister_print', $page_data);
            return;
        }
        $page_data['page_name']  = 'report_reregister';
        $page_data['page_title'] = 'Re-register History';
        $this->load->view('backend/index', $page_data);
    }

    /**
     * Re-registering flow — search any past student (alumni or current) by name,
     * student ID, or mobile, pick one, and create a fresh student row for the new academic year.
     * The new row keeps family data and links back via `previous_student_id`.
     * Previous fees are calculated and can be carried forward if unpaid.
     *
     * URLs:
     *   admin/student_reregister                              -> search screen
     *   admin/student_reregister?q=<term>                     -> search results (GET)
     *   admin/student_reregister/save (POST)                  -> save new admission
     */
    function student_reregister($mode = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_student_alumni_column();

        if ($mode === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->create_student_payment_history_table();
            $this->ensure_student_alumni_column();

            $prev_id = (int)$this->input->post('previous_student_id');
            $prev = $prev_id ? $this->db->get_where('student', array('student_id' => $prev_id))->row_array() : null;
            if (!$prev) {
                $this->session->set_flashdata('error_message', 'Previous student not found.');
                redirect(base_url() . 'index.php?admin/student_reregister', 'refresh');
            }

            $new_class_id = (int)$this->input->post('class_id') ?: (int)$prev['class_id'];
            $ay           = $this->input->post('academic_year') ?: $this->academic_year_for();
            
            // Calculate pending fees from previous registration
            $prev_total_fees = (float)($prev['total_fees'] ?? 0);
            $prev_paid = (float)($prev['payment_done'] ?? 0);
            if ($this->db->table_exists('student_payment_history')) {
                $prev_sum = $this->db->select_sum('amount')->where('student_id', $prev_id)->get('student_payment_history')->row();
                if ($prev_sum && $prev_sum->amount !== null) {
                    $prev_paid = (float)$prev_sum->amount;
                }
            }
            $pending_fees = max(0, $prev_total_fees - $prev_paid);
            
            // Check if user wants to carry forward pending fees
            $carry_forward_pending = (int)$this->input->post('carry_forward_pending_fees') ?? 0;
            $new_total_fees = (float)$this->input->post('total_fees');
            if ($carry_forward_pending && $pending_fees > 0) {
                $new_total_fees += $pending_fees;
            }

            // ---- GUARD: payment must not exceed total fees ----
            // Bug fix (student 29 had total_fees=0, payment_done=10000 -> -10,000 balance).
            // Compute the payment sum BEFORE inserting the student so we can reject early
            // and never leave the student row with an impossible negative balance.
            $payments_preview = $this->extractPaymentsFromPost();
            $payment_sum_preview = 0.0;
            foreach ($payments_preview as $pp) $payment_sum_preview += (float)$pp['amount'];

            if ($payment_sum_preview > 0 && $new_total_fees <= 0) {
                $this->session->set_flashdata('error_message',
                    'Cannot re-register: a payment of ₹' . number_format($payment_sum_preview, 2) .
                    ' was entered but new academic-year Total Fees is 0. Set Total Fees first.');
                redirect(base_url() . 'index.php?admin/student_reregister', 'refresh');
            }
            if ($payment_sum_preview > $new_total_fees + 0.01) {
                $this->session->set_flashdata('error_message',
                    'Cannot re-register: payment (₹' . number_format($payment_sum_preview, 2) .
                    ') exceeds Total Fees (₹' . number_format($new_total_fees, 2) .
                    '). Increase Total Fees or reduce the payment.');
                redirect(base_url() . 'index.php?admin/student_reregister', 'refresh');
            }

            $first_name  = trim((string)$this->input->post('first_name'));
            $middle_name = trim((string)$this->input->post('middle_name'));
            $last_name   = trim((string)$this->input->post('last_name'));
            if ($first_name === '') {
                $first_name = $prev['first_name'] ?? $prev['name'];
            }
            $full_name = trim($first_name . ' ' . $middle_name . ' ' . $last_name);
            if ($full_name === '') {
                $full_name = $prev['name'];
            }

            $data = array(
                'first_name'        => $first_name,
                'middle_name'       => $middle_name,
                'last_name'         => $last_name,
                'name'              => $full_name,
                'birthday'          => $this->input->post('birthday') ?: ($prev['birthday'] ?? null),
                'sex'               => $this->input->post('sex') ?: ($prev['sex'] ?? ''),
                'address'           => $this->input->post('home') ?: ($prev['address'] ?? ''),
                'father_name'       => $this->input->post('father_name') ?: ($prev['father_name'] ?? ''),
                'fmobile'           => $this->input->post('fmobile') ?: ($prev['fmobile'] ?? ''),
                'mother_name'       => $this->input->post('mother_name') ?: ($prev['mother_name'] ?? ''),
                'mmobile'           => $this->input->post('mmobile') ?: ($prev['mmobile'] ?? ''),
                'emergency_contact' => $this->input->post('emergency_contact') ?: ($prev['emergency_contact'] ?? ''),
                'email'             => $this->input->post('email') ?: ($prev['email'] ?? ''),
                'class_id'          => $new_class_id,
                'section_id'        => $this->input->post('section_id') ?: ($prev['section_id'] ?? null),
                'standard'          => $this->get_class_name_for_student($new_class_id),
                'medium'            => $this->input->post('medium') ?: ($prev['medium'] ?? ''),
                'board'             => $this->input->post('board')  ?: ($prev['board'] ?? ''),
                'school'            => $this->input->post('school') ?: ($prev['school'] ?? ''),
                'is_alumni'         => 0,
                'is_active'         => 1,
                'is_reregister'     => 1,                // re-register flow always sets this
                'academic_year'     => $ay,
                'previous_student_id' => $prev_id,
                'total_fees'        => $new_total_fees,
                'payment_done'      => 0,
                'student_mobile'    => $this->student_mobile_value_for($new_class_id, $this->input->post('student_mobile') ?: ($prev['student_mobile'] ?? '')),
                'password'          => $prev['password'] ?: password_hash('password', PASSWORD_BCRYPT),
            );

            $this->db->insert('student', $data);
            $new_id = (int)$this->db->insert_id();

            // Reuse the preview we already computed (validated above)
            $payments = $payments_preview;
            $total_payment = 0;
            foreach ($payments as $p) {
                $total_payment += $p['amount'];
                $this->db->insert('student_payment_history', array(
                    'student_id'     => $new_id,
                    'invoice_id'     => 0,
                    'title'          => 'Payment',
                    'payment_type'   => $p['type'],
                    'method'         => $p['mode'],
                    'description'    => 'Re-registration payment',
                    'amount'         => $p['amount'],
                    'timestamp'      => $p['date'] ? strtotime($p['date']) : time(),
                    'transaction_id' => isset($p['transaction_id']) ? $p['transaction_id'] : null,
                    'cheque_number'  => isset($p['cheque_number']) ? $p['cheque_number'] : null,
                    'cheque_bank'    => isset($p['cheque_bank']) ? $p['cheque_bank'] : null,
                    'cheque_date'    => isset($p['cheque_date']) && $p['cheque_date'] ? $p['cheque_date'] : null,
                ));
            }
            $this->db->where('student_id', $new_id)->update('student', array('payment_done' => $total_payment));
            $this->handleStudentFiles($new_id);

            // mark previous as alumni so it disappears from current Student List
            $this->db->where('student_id', $prev_id)->update('student', array('is_alumni' => 1));

            $msg = 'Re-registered ' . $data['name'] . ' for ' . $ay . '.';
            if ($carry_forward_pending && $pending_fees > 0) {
                $msg .= ' Pending fees (₹' . number_format($pending_fees, 2) . ') carried forward.';
            }
            $this->session->set_flashdata('flash_message', $msg);
            redirect(base_url() . 'index.php?admin/student_information/' . $new_class_id, 'refresh');
        }

        // Handle search - from GET or POST
        // Only show ACTIVE students (not alumni/not previously re-registered)
        $q = trim((string)($this->input->get('q') ?? $this->input->post('q')));
        $matches = array();
        if ($q !== '') {
            // Check if search term is numeric (student_id)
            $is_numeric = is_numeric($q);
            
            $this->db->where('(is_alumni = 0 OR is_alumni IS NULL)'); // Only active students
            $this->db->group_start();
            if ($is_numeric) {
                $this->db->where('student_id', (int)$q);
                $this->db->or_where('CAST(student_id AS CHAR)', $q);
            }
            $this->db->like('name', $q);
            $this->db->or_like('first_name', $q);
            $this->db->or_like('last_name', $q);
            $this->db->or_like('fmobile', $q);
            $this->db->or_like('mmobile', $q);
            $this->db->or_like('email', $q);
            $this->db->group_end();
            $this->db->order_by('student_id', 'desc');
            $matches = $this->db->limit(40)->get('student')->result_array();
            
            // Calculate pending fees for each match
            foreach ($matches as &$match) {
                $match['payment_history'] = $this->db->table_exists('student_payment_history')
                    ? $this->db->order_by('timestamp', 'asc')->get_where('student_payment_history', array('student_id' => $match['student_id']))->result_array()
                    : array();
                if (!empty($match['payment_history'])) {
                    $sum = 0;
                    foreach ($match['payment_history'] as $payment_row) {
                        $sum += (float)($payment_row['amount'] ?? 0);
                    }
                    $match['payment_done'] = $sum;
                }
                $total_fees = (float)($match['total_fees'] ?? 0);
                $paid = (float)($match['payment_done'] ?? 0);
                $match['pending_fees'] = max(0, $total_fees - $paid);
            }
        }

        $page_data['q']            = $q;
        $page_data['matches']      = $matches;
        $page_data['classes']      = $this->db->get('class')->result_array();
        $page_data['boards']       = $this->db->order_by('sort_order','asc')->order_by('name','asc')->get('board')->result_array();
        $page_data['default_ay']   = $this->academic_year_for();
        $this->load->model('crud_model');
        $page_data['academic_years'] = $this->crud_model->academic_years(array($page_data['default_ay']));
        $page_data['page_name']    = 'student_reregister';
        $page_data['page_title']   = 'Re-register Student';
        $this->load->view('backend/index', $page_data);
    }

    /**
     * Student Registration History — shows all previous registrations (archived students)
     * in a read-only report format. This allows viewing past enrollment records.
     *
     * URLs:
     *   admin/student_registration_history                    -> search screen
     *   admin/student_registration_history (POST with q)      -> search results
     */
    function student_registration_history()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        // Handle search - from GET or POST
        $q = trim((string)($this->input->get('q') ?? $this->input->post('q')));
        $matches = array();
        if ($q !== '') {
            $this->create_student_payment_history_table();
            // Check if search term is numeric (student_id)
            $is_numeric = is_numeric($q);
            
            $this->db->where('is_alumni = 1'); // Only PREVIOUS/archived registrations
            $this->db->group_start();
            if ($is_numeric) {
                $this->db->where('student_id', (int)$q);
                $this->db->or_where('CAST(student_id AS CHAR)', $q);
            }
            $this->db->like('name', $q);
            $this->db->or_like('first_name', $q);
            $this->db->or_like('last_name', $q);
            $this->db->or_like('fmobile', $q);
            $this->db->or_like('mmobile', $q);
            $this->db->or_like('email', $q);
            $this->db->group_end();
            $this->db->order_by('student_id', 'desc');
            $matches = $this->db->limit(40)->get('student')->result_array();
            
            // Calculate pending fees for each match
            foreach ($matches as &$match) {
                $match['payment_history'] = $this->db->table_exists('student_payment_history')
                    ? $this->db->order_by('timestamp', 'asc')->get_where('student_payment_history', array('student_id' => $match['student_id']))->result_array()
                    : array();
                if (!empty($match['payment_history'])) {
                    $sum = 0;
                    foreach ($match['payment_history'] as $payment_row) {
                        $sum += (float)($payment_row['amount'] ?? 0);
                    }
                    $match['payment_done'] = $sum;
                }
                $total_fees = (float)($match['total_fees'] ?? 0);
                $paid = (float)($match['payment_done'] ?? 0);
                $match['pending_fees'] = max(0, $total_fees - $paid);
            }
        }

        $page_data['q']            = $q;
        $page_data['matches']      = $matches;
        $page_data['page_name']    = 'student_registration_history';
        $page_data['page_title']   = 'Student Registration History (Read-Only)';
        $this->load->view('backend/index', $page_data);
    }

    /**
    function student_payment_add($mode = '', $student_id = 0)
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $student_id = (int)$student_id;
        $student = $student_id ? $this->db->get_where('student', array('student_id' => $student_id))->row_array() : null;
        if (!$student) {
            $this->session->set_flashdata('error_message', 'Student not found.');
            redirect(base_url() . 'index.php?admin/student_information/2', 'refresh');
        }

        if ($mode === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $amount = (float)$this->input->post('amount');
            if ($amount <= 0) {
                $this->session->set_flashdata('error_message', 'Amount must be greater than zero.');
                redirect(base_url() . 'index.php?admin/student_information/' . (int)$student['class_id'], 'refresh');
            }
            $payment_date = $this->input->post('payment_date');
            $ts = $payment_date ? strtotime($payment_date) : time();

            $payload = array(
                'student_id'     => $student_id,
                'invoice_id'     => 0,
                'title'          => 'Payment',
                'payment_type'   => $this->input->post('payment_type'),
                'method'         => $this->input->post('payment_mode'),
                'description'    => $this->input->post('description') ?: 'Payment entry',
                'amount'         => $amount,
                'timestamp'      => $ts ?: time(),
                'transaction_id' => $this->input->post('transaction_id') ?: null,
                'cheque_number'  => $this->input->post('cheque_number')  ?: null,
                'cheque_bank'    => $this->input->post('cheque_bank')    ?: null,
                'cheque_date'    => $this->input->post('cheque_date')    ?: null,
            );

            $this->db->insert('student_payment_history', $payload);
            $hist_id = (int)$this->db->insert_id();

            // recompute payment_done
            $sum_row = $this->db->select_sum('amount')->where('student_id', $student_id)->get('student_payment_history')->row();
            $paid = ($sum_row && $sum_row->amount) ? (float)$sum_row->amount : 0;
            $this->db->where('student_id', $student_id)->update('student', array('payment_done' => $paid));

            $this->session->set_flashdata('flash_message', 'Payment recorded.');
            // Redirect into the printable A4-half receipt for this newly-created payment
            redirect(base_url() . 'index.php?admin/payment_receipt/' . $hist_id, 'refresh');
        }
    }

    /**
     * Renders an A4 half-page payment receipt (two copies on one page — Student Copy + Office Copy).
     */
    function payment_receipt($history_id = 0)
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $history_id = (int)$history_id;
        $payment = $this->db->get_where('student_payment_history', array('id' => $history_id))->row_array();
        if (!$payment) {
            show_error('Payment record not found', 404);
            return;
        }
        $student = $this->db->get_where('student', array('student_id' => $payment['student_id']))->row_array();

        $sum_row = $this->db->select_sum('amount')->where('student_id', $payment['student_id'])->get('student_payment_history')->row();
        $paid    = ($sum_row && $sum_row->amount) ? (float)$sum_row->amount : 0;
        $total   = (float)($student['total_fees'] ?? 0);

        $school = array();
        $school['name']    = ($r = $this->db->get_where('settings', array('type' => 'system_name'))->row()) ? $r->description : 'School';
        $school['address'] = ($r = $this->db->get_where('settings', array('type' => 'address'))->row())     ? $r->description : '';

        $view_data = array(
            'payment' => $payment,
            'student' => $student,
            'paid'    => $paid,
            'total'   => $total,
            'due'     => max(0, $total - $paid),
            'school'  => $school,
        );
        $this->load->view('backend/admin/payment_receipt_a4_half', $view_data);
    }

    /**
     * Student's own mobile is only meaningful from Class 10 upwards. For lower classes
     * we drop whatever the form posted so the column stays NULL. Returns NULL or
     * the trimmed mobile string.
     */
    public function student_mobile_value_for($class_id, $posted_mobile)
    {
        $mobile = trim((string)$posted_mobile);
        if ($mobile === '') return null;

        $class_id = (int)$class_id;
        if (!$class_id) return null;
        $row = $this->db->get_where('class', array('class_id' => $class_id))->row();
        if (!$row) return null;

        // name_numeric is the canonical class number; fallback to extracting digits from name
        $n = (int)($row->name_numeric ?? 0);
        if (!$n && !empty($row->name) && preg_match('/(\d+)/', $row->name, $m)) {
            $n = (int)$m[1];
        }
        // Decision (class 10+ keeps own mobile) lives in sms_core_helper (unit tested).
        return sms_mobile_for_class_number($n, $mobile);
    }

    /**
     * Indian academic year string for a given timestamp (April-March).
     * Example: 12 May 2026 -> "2026-2027"; 5 Feb 2026 -> "2025-2026".
     */
    public function academic_year_for($ts = null)
    {
        // Implemented in sms_core_helper (unit tested).
        return sms_academic_year($ts ?: time());
    }

    /**
     * Returns the distinct academic years that appear on the student table,
     * always including the current one even if no rows reference it yet.
     */
    public function student_academic_years()
    {
        $years = array($this->academic_year_for());
        if ($this->db->table_exists('student') && $this->db->field_exists('academic_year', 'student')) {
            $rows = $this->db->distinct()->select('academic_year')->where('academic_year IS NOT NULL', null, false)
                          ->where('academic_year !=', '')->order_by('academic_year', 'desc')->get('student')->result_array();
            foreach ($rows as $r) $years[] = $r['academic_year'];
            $years = array_values(array_unique($years));
            rsort($years);
        }
        return $years;
    }

    private function get_class_name_for_student($class_id)
    {
        if ($class_id === '' || $class_id === null) {
            return $this->input->post('standard');
        }

        $class = $this->db->get_where('class', array('class_id' => $class_id))->row_array();
        if (!empty($class)) {
            return $class['name'];
        }

        return $this->input->post('standard');
    }

    private function ensure_board_table()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `board` (
                `board_id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `sort_order` int(11) NOT NULL DEFAULT 0,
                PRIMARY KEY (`board_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        ");

        $boards = array('CBSE', 'ICSE', 'State Board', 'Mumbai University', 'SPPU', 'IB', 'IGCSE');
        foreach ($boards as $index => $board) {
            $exists = $this->db->get_where('board', array('name' => $board))->num_rows();
            if ($exists == 0) {
                $this->db->insert('board', array(
                    'name'       => $board,
                    'sort_order' => $index + 1
                ));
            }
        }
    }

    /**
     * Thin wrapper around the model's salary_structure() so existing
     * controller methods can keep calling $this->salary_structure().
     */
    public function salary_structure()
    {
        $this->load->model('crud_model');
        return $this->crud_model->salary_structure();
    }

    /**
     * Persist a single salary setting (upsert into settings table).
     */
    private function set_salary_setting($key, $value)
    {
        $type = 'salary_' . $key;
        $existing = $this->db->get_where('settings', array('type' => $type))->row();
        if ($existing) {
            $this->db->where('type', $type)->update('settings', array('description' => (string)$value));
        } else {
            $this->db->insert('settings', array('type' => $type, 'description' => (string)$value));
        }
    }

    /**
     * Editable salary structure page. Allows the admin to tune the % of Basic
     * applied to each allowance, plus the fixed PF / Tax values. Validates that
     * the sum of earning percentages is exactly 100% before saving.
     */
    function salary_settings($mode = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        if ($mode == 'save') {
            $pct_keys = array('hra','da','conveyance','medical_allowance','other_allowance');
            $earning_sum = 0.0;
            $values_pct  = array();
            foreach ($pct_keys as $k) {
                $v = max(0, (float)$this->input->post($k));
                $values_pct[$k] = $v;
                $earning_sum   += $v;
            }
            $other_deduction = max(0, (float)$this->input->post('other_deduction'));
            $pf_fixed        = max(0, (float)$this->input->post('pf_deduction'));
            $tax_fixed       = max(0, (float)$this->input->post('tax_deduction'));

            // Validation: sum of earning percentages must equal 100% (allow tiny FP slack)
            if (abs($earning_sum - 100.0) > 0.001) {
                $this->session->set_flashdata(
                    'error_message',
                    'Earning percentages must total 100%. Current total: ' . rtrim(rtrim(number_format($earning_sum, 2), '0'), '.') . '%.'
                );
                redirect(base_url() . 'index.php?admin/salary_settings', 'refresh');
            }

            foreach ($values_pct as $k => $v) {
                $this->set_salary_setting($k, $v);
            }
            $this->set_salary_setting('other_deduction', $other_deduction);
            $this->set_salary_setting('pf_deduction',    $pf_fixed);
            $this->set_salary_setting('tax_deduction',   $tax_fixed);

            $this->session->set_flashdata('flash_message', 'Salary structure updated.');
            redirect(base_url() . 'index.php?admin/salary_settings', 'refresh');
        }

        $page_data['salary']     = $this->salary_structure();
        $page_data['page_name']  = 'salary_settings';
        $page_data['page_title'] = 'Salary Structure Settings';
        $this->load->view('backend/index', $page_data);
    }

    /**
     * Master Data — generic editor for the lookup_value table.
     * URLs:
     *   admin/lookup_values                          -> default to medium
     *   admin/lookup_values/<category>               -> list values for a category
     *   admin/lookup_values/<category>/add (POST)    -> add new value
     *   admin/lookup_values/<category>/edit/<id> (POST) -> rename
     *   admin/lookup_values/<category>/toggle/<id>   -> active/inactive
     *   admin/lookup_values/<category>/delete/<id>   -> delete row
     */
    function lookup_values($category = 'medium', $mode = '', $id = 0)
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_lookup_values_table();

        $allowed = array('medium', 'payment_type', 'payment_mode');
        if (!in_array($category, $allowed, true)) $category = 'medium';

        $back = base_url() . 'index.php?admin/lookup_values/' . $category;

        if ($mode === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $value      = trim((string)$this->input->post('value'));
            $sort_order = (int)$this->input->post('sort_order');
            if ($value === '') {
                $this->session->set_flashdata('error_message', 'Value cannot be empty.');
                redirect($back, 'refresh');
            }
            $exists = $this->db->get_where('lookup_value', array('category' => $category, 'value' => $value))->num_rows();
            if ($exists) {
                $this->session->set_flashdata('error_message', 'That value already exists.');
                redirect($back, 'refresh');
            }
            $this->db->insert('lookup_value', array(
                'category'   => $category,
                'value'      => $value,
                'sort_order' => $sort_order ?: 0,
                'is_active'  => 1,
            ));
            $this->session->set_flashdata('flash_message', 'Added.');
            redirect($back, 'refresh');
        }

        if ($mode === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST' && (int)$id > 0) {
            $value      = trim((string)$this->input->post('value'));
            $sort_order = (int)$this->input->post('sort_order');
            if ($value === '') {
                $this->session->set_flashdata('error_message', 'Value cannot be empty.');
                redirect($back, 'refresh');
            }
            $clash = $this->db->where('category', $category)
                              ->where('value', $value)
                              ->where('lookup_id !=', (int)$id)
                              ->count_all_results('lookup_value');
            if ($clash) {
                $this->session->set_flashdata('error_message', 'Another row already uses that value.');
                redirect($back, 'refresh');
            }
            $this->db->where('lookup_id', (int)$id)
                     ->update('lookup_value', array('value' => $value, 'sort_order' => $sort_order));
            $this->session->set_flashdata('flash_message', 'Updated.');
            redirect($back, 'refresh');
        }

        if ($mode === 'toggle' && (int)$id > 0) {
            $row = $this->db->get_where('lookup_value', array('lookup_id' => (int)$id))->row();
            if ($row) {
                $this->db->where('lookup_id', (int)$id)
                         ->update('lookup_value', array('is_active' => $row->is_active ? 0 : 1));
            }
            redirect($back, 'refresh');
        }

        if ($mode === 'delete' && (int)$id > 0) {
            $this->db->where('lookup_id', (int)$id)->delete('lookup_value');
            $this->session->set_flashdata('flash_message', 'Deleted.');
            redirect($back, 'refresh');
        }

        $page_data['active_category'] = $category;
        $page_data['categories'] = array(
            'medium'       => 'Medium',
            'payment_type' => 'Type of Payment',
            'payment_mode' => 'Mode of Payment',
        );
        $page_data['values'] = $this->db->where('category', $category)
                                        ->order_by('sort_order', 'asc')
                                        ->order_by('value', 'asc')
                                        ->get('lookup_value')->result_array();
        $page_data['page_name']  = 'lookup_values';
        $page_data['page_title'] = 'Master Data';
        $this->load->view('backend/index', $page_data);
    }

    /**
     * Collects the standard teacher form fields (including blood group + salary structure)
     * into a normalised data array. All derived salary fields are computed server-side
     * from Basic Salary + the fixed PF / Tax constants, so client-side tampering is ignored.
     */
    private function collect_teacher_post()
    {
        $basic  = max(0, (float)$this->input->post('basic_salary'));
        $struct = $this->salary_structure();

        $hra        = $basic * $struct['percentages']['hra'] / 100;
        $da         = $basic * $struct['percentages']['da'] / 100;
        $conveyance = $basic * $struct['percentages']['conveyance'] / 100;
        $medical    = $basic * $struct['percentages']['medical_allowance'] / 100;
        $other_a    = $basic * $struct['percentages']['other_allowance'] / 100;
        $other_d    = $basic * $struct['percentages']['other_deduction'] / 100;
        $pf         = $struct['fixed']['pf_deduction'];
        $tax        = $struct['fixed']['tax_deduction'];

        $ctc = $basic + $hra + $da + $conveyance + $medical + $other_a; // Cost to Company (gross)
        $net = max(0, $ctc - ($pf + $tax + $other_d));                  // Net take-home

        return array(
            'name'              => $this->input->post('name'),
            'birthday'          => $this->input->post('birthday'),
            'sex'               => $this->input->post('sex'),
            'blood_group'       => $this->input->post('blood_group'),
            'address'           => $this->input->post('address'),
            'phone'             => $this->input->post('phone'),
            'email'             => $this->input->post('email'),
            'designation'       => $this->input->post('designation'),
            'joining_date'      => $this->normalise_date_for_db($this->input->post('joining_date')),
            'pan_number'        => $this->input->post('pan_number'),
            'bank_account'      => $this->input->post('bank_account'),
            'basic_salary'      => $basic,
            'hra'               => $hra,
            'da'                => $da,
            'conveyance'        => $conveyance,
            'medical_allowance' => $medical,
            'other_allowance'   => $other_a,
            'pf_deduction'      => $pf,
            'tax_deduction'     => $tax,
            'other_deduction'   => $other_d,
            'total_salary'      => $net,
        );
    }

    /**
     * Converts a free-form date string from the form (e.g. MM/DD/YYYY, DD-MM-YYYY,
     * YYYY-MM-DD) into MySQL DATE format. Returns NULL when blank or unparseable
     * so the DB does not store '0000-00-00'.
     */
    private function normalise_date_for_db($value)
    {
        if ($value === null) return null;
        $value = trim((string)$value);
        if ($value === '' || $value === '0000-00-00') return null;
        $ts = strtotime($value);
        if (!$ts) return null;
        return date('Y-m-d', $ts);
    }

    /**
     * Common helper to upload a user photo (teacher / accountant / librarian / etc.)
     * into a per-entity folder under uploads/, storing the filename in <table>.<column>.
     *
     * Mirrors the add/edit student photo flow (uploads/student_files/, student.student_photo).
     *
     * @param string $table       db table (e.g. 'teacher')
     * @param string $id_col      primary key column (e.g. 'teacher_id')
     * @param int    $id          row id
     * @param string $folder      target folder under uploads/ (e.g. 'teacher_photo')
     * @param string $column      column in the table to store filename (e.g. 'teacher_photo')
     * @param string $input_name  $_FILES key (default 'userfile')
     */
    public function handle_user_photo($table, $id_col, $id, $folder, $column, $input_name = 'userfile')
    {
        if (!isset($_FILES[$input_name]) || $_FILES[$input_name]['error'] != 0 || empty($_FILES[$input_name]['tmp_name'])) {
            return;
        }

        $upload_path = FCPATH . 'uploads/' . $folder . '/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        if (!$this->db->field_exists($column, $table)) {
            $this->db->query("ALTER TABLE `" . $table . "` ADD `" . $column . "` VARCHAR(255) DEFAULT NULL");
        }

        $existing = $this->db->get_where($table, array($id_col => $id))->row();
        if (!empty($existing) && !empty($existing->{$column}) && file_exists($upload_path . $existing->{$column})) {
            @unlink($upload_path . $existing->{$column});
        }

        $ext = strtolower(pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION));
        if ($ext === '') {
            $ext = 'jpg';
        }
        $file_name = $id . '_photo_' . time() . '.' . $ext;

        if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $upload_path . $file_name)) {
            $this->db->where($id_col, $id);
            $this->db->update($table, array($column => $file_name));
        }
    }

    /**
     * Creates the generic lookup_value table used for admin-configurable dropdowns
     * (Medium, Payment Type, Payment Mode, etc.) and seeds initial values on first run.
     */
    private function ensure_lookup_values_table()
    {
        if (!$this->db->table_exists('lookup_value')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `lookup_value` (
                    `lookup_id` int(11) NOT NULL AUTO_INCREMENT,
                    `category` varchar(64) NOT NULL,
                    `value` varchar(128) NOT NULL,
                    `sort_order` int(11) NOT NULL DEFAULT 0,
                    `is_active` tinyint(1) NOT NULL DEFAULT 1,
                    PRIMARY KEY (`lookup_id`),
                    KEY `category` (`category`),
                    UNIQUE KEY `cat_val` (`category`, `value`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
            ");
        }

        // Seed defaults only if a category is completely empty.
        $seeds = array(
            'medium'       => array('English', 'Hindi'),
            'payment_type' => array('Admission', 'Installment'),
            'payment_mode' => array('Cash', 'Online', 'UPI', 'Cheque'),
        );
        foreach ($seeds as $cat => $values) {
            $count = (int)$this->db->where('category', $cat)->count_all_results('lookup_value');
            if ($count === 0) {
                foreach ($values as $i => $v) {
                    $this->db->insert('lookup_value', array(
                        'category'   => $cat,
                        'value'      => $v,
                        'sort_order' => $i + 1,
                        'is_active'  => 1,
                    ));
                }
            }
        }
    }

    /**
     * Creates the teacher_attendance table on first use. Parallel structure to the
     * existing `attendance` (student) table.
     */
    private function ensure_teacher_attendance_table()
    {
        if (!$this->db->table_exists('teacher_attendance')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `teacher_attendance` (
                    `attendance_id` int(11) NOT NULL AUTO_INCREMENT,
                    `teacher_id` int(11) NOT NULL,
                    `date` date NOT NULL,
                    `status` int(11) NOT NULL DEFAULT 0 COMMENT '0 undefined, 1 present, 2 absent',
                    PRIMARY KEY (`attendance_id`),
                    UNIQUE KEY `teacher_date` (`teacher_id`, `date`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
            ");
        }
    }

    private function ensure_teacher_timetable_columns()
    {
        if (!$this->db->table_exists('section')) {
            return;
        }

        $fields = $this->db->list_fields('section');
        if (!in_array('days', $fields)) {
            $this->db->query("ALTER TABLE `section` ADD `days` varchar(255) NULL");
        }
        if (!in_array('start_time', $fields)) {
            $this->db->query("ALTER TABLE `section` ADD `start_time` time NULL");
        }
        if (!in_array('end_time', $fields)) {
            $this->db->query("ALTER TABLE `section` ADD `end_time` time NULL");
        }
        if (!in_array('session_name', $fields)) {
            $this->db->query("ALTER TABLE `section` ADD `session_name` varchar(20) NULL");
        }
        if (!in_array('revision_section', $fields)) {
            $this->db->query("ALTER TABLE `section` ADD `revision_section` varchar(255) NULL");
        }
        if (!in_array('lecture_subject', $fields)) {
            $this->db->query("ALTER TABLE `section` ADD `lecture_subject` varchar(255) NULL");
        }
    }

    /**
     * Adds salary-related columns to the teacher table on first use.
     */
    private function ensure_teacher_salary_columns()
    {
        if (!$this->db->table_exists('teacher')) {
            return;
        }
        $cols = array(
            'designation'    => "varchar(100) NULL",
            'joining_date'   => "date NULL",
            'pan_number'     => "varchar(20) NULL",
            'bank_account'   => "varchar(40) NULL",
            'basic_salary'   => "decimal(10,2) NULL DEFAULT 0",
            'hra'            => "decimal(10,2) NULL DEFAULT 0",
            'da'             => "decimal(10,2) NULL DEFAULT 0",
            'conveyance'     => "decimal(10,2) NULL DEFAULT 0",
            'medical_allowance' => "decimal(10,2) NULL DEFAULT 0",
            'other_allowance'   => "decimal(10,2) NULL DEFAULT 0",
            'pf_deduction'   => "decimal(10,2) NULL DEFAULT 0",
            'tax_deduction'  => "decimal(10,2) NULL DEFAULT 0",
            'other_deduction'=> "decimal(10,2) NULL DEFAULT 0",
            'total_salary'   => "decimal(10,2) NULL DEFAULT 0",
        );
        foreach ($cols as $name => $def) {
            if (!$this->db->field_exists($name, 'teacher')) {
                $this->db->query("ALTER TABLE `teacher` ADD `" . $name . "` " . $def);
            }
        }
    }

    private function create_student_payment_history_table() {
        if (!$this->db->table_exists('student_payment_history')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `student_payment_history` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `student_id` int(11) NOT NULL,
                  `invoice_id` int(11) DEFAULT '0',
                  `title` varchar(255) DEFAULT NULL,
                  `description` text DEFAULT NULL,
                  `payment_type` varchar(100) DEFAULT NULL,
                  `method` varchar(100) DEFAULT NULL,
                  `amount` decimal(10,2) DEFAULT '0.00',
                  `timestamp` int(11) DEFAULT NULL,
                  `transaction_id` VARCHAR(64) NULL,
                  `cheque_number` VARCHAR(64) NULL,
                  `cheque_bank` VARCHAR(128) NULL,
                  `cheque_date` DATE NULL,
                  PRIMARY KEY (`id`),
                  KEY `student_id` (`student_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            ");
        }
    }

    /**
     * Expands the legacy `enquiry` table into the admission-enquiry model
     * (year, enquiry no, course, source, assign/handle, status, remark, ...)
     * and creates the enquiry activity-log table. Idempotent; runs on page load.
     */
    private function ensure_enquiry_columns()
    {
        if (!$this->db->table_exists('enquiry')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `enquiry` (
                  `enquiry_id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` longtext NULL,
                  `mobile` longtext NULL,
                  `category` longtext NULL,
                  `purpose` longtext NULL,
                  `whom_to_meet` longtext NULL,
                  `date` timestamp NOT NULL DEFAULT current_timestamp(),
                  PRIMARY KEY (`enquiry_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            ");
        }

        $cols = array(
            'session_name'   => "varchar(20) NULL",
            'enquiry_no'     => "varchar(50) NULL",
            'enquiry_date'   => "date NULL",
            'enquiry_for'    => "varchar(255) NULL",
            'course'         => "varchar(255) NULL",
            'source'         => "varchar(100) NULL",
            'source_student' => "varchar(255) NULL",
            'gender'         => "varchar(20) NULL",
            'address'        => "longtext NULL",
            'assign_to'      => "int(11) NULL",
            'handled_by'     => "int(11) NULL",
            'status'         => "varchar(30) NULL DEFAULT 'in_progress'",
            'remark'         => "longtext NULL",
            'created_by'     => "varchar(150) NULL",
        );
        foreach ($cols as $name => $def) {
            if (!$this->db->field_exists($name, 'enquiry')) {
                $this->db->query("ALTER TABLE `enquiry` ADD `" . $name . "` " . $def);
            }
        }

        if (!$this->db->table_exists('enquiry_activity')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `enquiry_activity` (
                  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
                  `enquiry_id` int(11) NOT NULL,
                  `status` varchar(30) NULL,
                  `note` longtext NULL,
                  `created_by` varchar(150) NULL,
                  `created_at` datetime NULL,
                  PRIMARY KEY (`activity_id`),
                  KEY `enquiry_id` (`enquiry_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            ");
        }
    }

    /**
     * Creates the Course module tables: course, its subjects and its
     * fee installments. Idempotent; runs on page load.
     */
    private function ensure_course_tables()
    {
        if (!$this->db->table_exists('course')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `course` (
                  `course_id` int(11) NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `session_name` varchar(20) NULL,
                  `class_id` int(11) NULL,
                  `standard_name` varchar(50) NULL,
                  `total_fees` decimal(10,2) NULL DEFAULT 0,
                  `installments` int(11) NULL DEFAULT 1,
                  `description` longtext NULL,
                  `created_by` varchar(150) NULL,
                  `created_at` datetime NULL,
                  PRIMARY KEY (`course_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            ");
        }

        if (!$this->db->table_exists('course_subject')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `course_subject` (
                  `csubject_id` int(11) NOT NULL AUTO_INCREMENT,
                  `course_id` int(11) NOT NULL,
                  `subject_name` varchar(255) NOT NULL,
                  `subject_code` varchar(50) NULL,
                  PRIMARY KEY (`csubject_id`),
                  KEY `course_id` (`course_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            ");
        }

        if (!$this->db->table_exists('course_installment')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `course_installment` (
                  `installment_id` int(11) NOT NULL AUTO_INCREMENT,
                  `course_id` int(11) NOT NULL,
                  `title` varchar(100) NULL,
                  `amount` decimal(10,2) NULL DEFAULT 0,
                  `due_date` date NULL,
                  PRIMARY KEY (`installment_id`),
                  KEY `course_id` (`course_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
            ");
        }
    }

    private function ensure_student_alumni_column()
    {
        if (!$this->db->table_exists('student')) {
            return;
        }

        if (!$this->db->field_exists('is_alumni', 'student')) {
            $this->db->query("ALTER TABLE `student` ADD `is_alumni` tinyint(1) NOT NULL DEFAULT 0");
        }
        if (!$this->db->field_exists('academic_year', 'student')) {
            $this->db->query("ALTER TABLE `student` ADD `academic_year` VARCHAR(16) NULL");
        }
        if (!$this->db->field_exists('previous_student_id', 'student')) {
            $this->db->query("ALTER TABLE `student` ADD `previous_student_id` INT NULL");
        }
        if (!$this->db->field_exists('is_reregister', 'student')) {
            $this->db->query("ALTER TABLE `student` ADD `is_reregister` TINYINT(1) NOT NULL DEFAULT 0");
            // Backfill: any existing row that came through the re-register flow already
            // has previous_student_id set — mark those as re-registered.
            $this->db->query("UPDATE `student` SET `is_reregister` = 1 WHERE `previous_student_id` IS NOT NULL AND `previous_student_id` > 0");
        }
        if (!$this->db->field_exists('student_mobile', 'student')) {
            $this->db->query("ALTER TABLE `student` ADD `student_mobile` VARCHAR(15) NULL");
        }
        // Form/UI already captures school name on Add Student + Bulk import, but the
        // base schema dump never had a column for it. Add it lazily so the inserts work.
        if (!$this->db->field_exists('school', 'student')) {
            $this->db->query("ALTER TABLE `student` ADD `school` VARCHAR(255) NULL");
        }

        // Payment detail columns on history table (for cheque / online ref)
        if ($this->db->table_exists('student_payment_history')) {
            foreach (array(
                'transaction_id'  => "VARCHAR(64) NULL",
                'cheque_number'   => "VARCHAR(64) NULL",
                'cheque_bank'     => "VARCHAR(128) NULL",
                'cheque_date'     => "DATE NULL",
            ) as $col => $def) {
                if (!$this->db->field_exists($col, 'student_payment_history')) {
                    $this->db->query("ALTER TABLE `student_payment_history` ADD `" . $col . "` " . $def);
                }
            }
        }
    }

    private function session_debug($stage, $extra = array())
    {
        $payload = array(
            'time'        => date('Y-m-d H:i:s'),
            'stage'       => $stage,
            'session_id'  => session_id(),
            'cookie'      => isset($_COOKIE[$this->config->item('sess_cookie_name')]) ? $_COOKIE[$this->config->item('sess_cookie_name')] : '',
            'userdata'    => $this->session->all_userdata(),
            'request_uri' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''
        );

        if (!empty($extra)) {
            $payload['extra'] = $extra;
        }

        @file_put_contents(APPPATH . 'logs/session_debug.log', json_encode($payload) . PHP_EOL, FILE_APPEND);
    }
    
    
	function __construct()
	{
		parent::__construct();
		$this->load->database();
        $this->load->library('session');
        $this->ensure_board_table();
        $this->ensure_teacher_timetable_columns();
        $this->ensure_student_alumni_column();
		
       /*cache control*/
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
        $this->session_debug('admin_construct');
		
    }
    
    /***default functin, redirects to login page if no admin logged in yet***/
    public function index()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'index.php?login');
        if ($this->session->userdata('admin_login') == 1)
            redirect(base_url() . 'index.php?admin/dashboard');
    }
    
    /***ADMIN DASHBOARD***/
    function dashboard()
    {
        $this->session_debug('admin_dashboard');
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url());
        $page_data['page_name']  = 'dashboard';
        $page_data['page_title'] = get_phrase('admin_dashboard');
        $this->load->view('backend/index', $page_data);
    }
    
    /****MANAGE STUDENTS CLASSWISE*****/
	function student_add()
	{
		if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
			
		$page_data['page_name']  = 'student_add';
		$page_data['page_title'] = get_phrase('add_student');
		$this->load->view('backend/index', $page_data);
	}
	
	/**
	 * Canonical column set for the bulk-add Excel/CSV template.
	 * Mirrors the Add Student form (file-upload fields excluded).
	 * Order = the order they appear in the downloaded template.
	 */
	private function bulk_student_template_columns()
	{
		return array(
			'first_name', 'middle_name', 'last_name',
			'birthday',                         // YYYY-MM-DD
			'father_name', 'fmobile',
			'mother_name', 'mmobile',
			'emergency_contact',
			'email',
			'home_address',
			'medium',                           // configurable in Master Data
			'board',                            // CBSE | ICSE | State Board | ...
			'sex',                              // male | female
			'school_name',
			'academic_year',                    // 2025-2026 (defaults to current Indian AY if blank)
			'total_fees',
			'payment_amount',                   // optional - creates a payment_history row if > 0
			'payment_date',                     // optional - YYYY-MM-DD
			'payment_type',                     // configurable in Master Data
			'payment_mode',                     // configurable in Master Data
		);
	}

	/**
	 * Emits the blank template as CSV (Excel opens CSV natively).
	 * URL: admin/student_bulk_add/template
	 */
	private function send_bulk_template_csv()
	{
		$filename = 'student_bulk_template.csv';
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Pragma: no-cache');
		header('Expires: 0');
		$out = fopen('php://output', 'w');
		fputcsv($out, $this->bulk_student_template_columns());
		// One sample row to demonstrate the format (admin can delete before saving)
		fputcsv($out, array(
			'Rahul', '', 'Sharma',
			'2012-04-15',
			'Suresh Sharma', '9876543210',
			'Anita Sharma', '9876543211',
			'Mama 9999999999',
			'rahul.sharma@example.com',
			'12, Sector A, Thane',
			'English', 'CBSE', 'male',
			'Saraswati Vidyalaya',
			$this->academic_year_for(),
			'25000',
			'5000', date('Y-m-d'), 'Admission', 'Cash',
		));
		fclose($out);
	}

	function student_bulk_add($param1 = '')
	{
		if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

		if ($param1 == 'template') {
			$this->send_bulk_template_csv();
			return;
		}

		if ($param1 == 'import_excel')
		{
			if (empty($_FILES['userfile']['tmp_name'])) {
				$this->session->set_flashdata('error', 'Please choose a file.');
				redirect(base_url() . 'index.php?admin/student_bulk_add', 'refresh');
			}

			$orig_name = strtolower($_FILES['userfile']['name']);
			$is_csv    = (substr($orig_name, -4) === '.csv') ||
			             (isset($_FILES['userfile']['type']) && stripos($_FILES['userfile']['type'], 'csv') !== false);

			$ext = $is_csv ? 'csv' : 'xlsx';
			$target = 'uploads/student_import.' . $ext;
			move_uploaded_file($_FILES['userfile']['tmp_name'], $target);

			// Read rows: each row is an indexed array of cell strings.
			$rows = array();
			if ($is_csv) {
				if (($h = fopen($target, 'r')) !== false) {
					while (($line = fgetcsv($h)) !== false) $rows[] = $line;
					fclose($h);
				}
			} else {
				include_once 'simplexlsx.class.php';
				$xlsx = new SimpleXLSX($target);
				$rows = $xlsx->rows();
			}

			if (empty($rows)) {
				$this->session->set_flashdata('error', 'No rows found in the uploaded file.');
				redirect(base_url() . 'index.php?admin/student_bulk_add', 'refresh');
			}

			// Detect header row: first non-empty row.
			$header_row = null;
			$first_data_index = 0;
			foreach ($rows as $idx => $r) {
				$has_value = false;
				foreach ($r as $cell) { if (trim((string)$cell) !== '') { $has_value = true; break; } }
				if ($has_value) {
					$header_row = $r;
					$first_data_index = $idx + 1;
					break;
				}
			}
			if (!$header_row) {
				$this->session->set_flashdata('error', 'Could not find a header row.');
				redirect(base_url() . 'index.php?admin/student_bulk_add', 'refresh');
			}

			// Header parsing shared with enquiry import via sms_admissions_helper (unit tested).
			$header_map = sms_build_header_map($header_row);

			$form_class_id = (int)$this->input->post('class_id');
			$class_row = $form_class_id ? $this->db->get_where('class', array('class_id' => $form_class_id))->row() : null;
			$class_name = $class_row ? $class_row->name : '';

			$imported = 0;
			$updated  = 0;

			for ($idx = $first_data_index; $idx < count($rows); $idx++) {
				$r = $rows[$idx];
				$row_has_value = false;
				foreach ($r as $cell) { if (trim((string)$cell) !== '') { $row_has_value = true; break; } }
				if (!$row_has_value) continue;

				$cell = function ($keys) use ($r, $header_map) {
					return sms_cell_value($r, $header_map, $keys);
				};

				$first_name  = $cell(array('first_name'));
				$middle_name = $cell(array('middle_name'));
				$last_name   = $cell(array('last_name'));
				$full_name   = $cell(array('name', 'full_name'));
				if ($first_name === '' && $full_name !== '') {
					// Name splitting lives in sms_core_helper (unit tested).
					$split = sms_split_full_name($full_name);
					$first_name  = $split['first'];
					$middle_name = $split['middle'];
					$last_name   = $split['last'];
				}

				$data = array(
					'first_name'        => $first_name,
					'middle_name'       => $middle_name,
					'last_name'         => $last_name,
					'name'              => sms_full_name($first_name, $middle_name, $last_name),
					'birthday'          => $cell(array('birthday', 'dob', 'date_of_birth')),
					'sex'               => strtolower($cell(array('sex', 'gender'))),
					'address'           => $cell(array('home_address', 'home', 'address', 'residence')),
					'father_name'       => $cell(array('father_name')),
					'fmobile'           => $cell(array('fmobile', 'father_mobile', 'phone', 'phone_number')),
					'mother_name'       => $cell(array('mother_name')),
					'mmobile'           => $cell(array('mmobile', 'mother_mobile')),
					'emergency_contact' => $cell(array('emergency_contact')),
					'email'             => $cell(array('email')),
					'school'            => $cell(array('school_name', 'school')),
					'medium'            => $cell(array('medium')),
					'board'             => $cell(array('board')),
					'total_fees'        => $cell(array('total_fees', 'fees')),
					'is_alumni'         => 0,
					'academic_year'     => $cell(array('academic_year', 'batch')) ?: $this->academic_year_for(),
					'class_id'          => $form_class_id,
					'standard'          => $class_name,
					'is_active'         => 1,
				);
				$data['phone'] = $data['fmobile'];

				if (empty($data['email']))     $data['email']    = 'student@example.com';
				if (empty($data['first_name']) && empty($data['name'])) continue; // skip empty rows

				$student_id_in = $cell(array('student_id', 'id'));

				if (!empty($student_id_in) && $this->db->get_where('student', array('student_id' => (int)$student_id_in))->num_rows() > 0) {
					$this->db->where('student_id', (int)$student_id_in);
					$this->db->update('student', $data);
					$student_id = (int)$student_id_in;
					$updated++;
				} else {
					$data['password'] = password_hash('password', PASSWORD_BCRYPT);
					$this->db->insert('student', $data);
					$student_id = (int)$this->db->insert_id();
					$imported++;
				}

				// Optional initial payment
				$pay_amount = (float)$cell(array('payment_amount', 'payment_done', 'payment'));
				if ($pay_amount > 0 && $student_id > 0) {
					$pay_date_raw = $cell(array('payment_date', 'payment date'));
					$pay_ts = $pay_date_raw ? strtotime($pay_date_raw) : time();
					$this->db->insert('student_payment_history', array(
						'student_id'   => $student_id,
						'invoice_id'   => 0,
						'title'        => 'Payment',
						'payment_type' => $cell(array('payment_type')),
						'method'       => $cell(array('payment_mode', 'mode_of_payment')),
						'description'  => 'Bulk import',
						'amount'       => $pay_amount,
						'timestamp'    => $pay_ts ?: time(),
					));
					// Recompute total paid
					$sum_row = $this->db->select_sum('amount')->where('student_id', $student_id)->get('student_payment_history')->row();
					$paid = ($sum_row && $sum_row->amount) ? (float)$sum_row->amount : 0;
					$this->db->where('student_id', $student_id)->update('student', array('payment_done' => $paid));
				}
			}

			$this->session->set_flashdata('flash_message',
				'Bulk import complete — added: ' . $imported . ', updated: ' . $updated . '.');
			redirect(base_url() . 'index.php?admin/student_information/' . $form_class_id, 'refresh');
		}

		$page_data['template_columns'] = $this->bulk_student_template_columns();
		$page_data['page_name']  = 'student_bulk_add';
		$page_data['page_title'] = get_phrase('add_bulk_student');
		$this->load->view('backend/index', $page_data);
	}
	
	function student_information($class_id = '')
	{
		if ($this->session->userdata('admin_login') != 1)
            redirect('login', 'refresh');
			
		$filters = array();
		if ($this->input->get('first_name')) {
			$filters['first_name'] = $this->input->get('first_name');
		}
		if ($this->input->get('last_name')) {
			$filters['last_name'] = $this->input->get('last_name');
		}
		if ($this->input->get('email')) {
			$filters['email'] = $this->input->get('email');
		}
		if ($this->input->get('academic_year')) {
			$filters['academic_year'] = $this->input->get('academic_year');
		}

		$page_data['page_name']      = 'student_information';
		$page_data['page_title']     = get_phrase('student_information');
		$page_data['class_id']       = $class_id;
		$page_data['students']       = $this->crud_model->get_students($filters);
		// Pull AYs from the Manage Academic Year (session) table, plus the AY currently being filtered on
		$page_data['academic_years'] = $this->crud_model->academic_years(
			array_filter(array(
				$this->input->get('academic_year'),
				$this->academic_year_for(),
			))
		);
		$this->load->view('backend/index', $page_data);
	}

    function export_list($list_name = '', $format = 'excel', $identifier = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect('login', 'refresh');

        $this->load->library('export_service');
        $config = $this->get_export_config($list_name, $identifier);

        if (empty($config)) {
            show_error(get_phrase('export_not_configured_for_this_list'));
            return;
        }

        switch (strtolower($format)) {
            case 'excel':
                $this->export_service->exportExcel($config['filename'], $config['title'], $config['columns'], $config['rows']);
                break;

            case 'pdf':
                $this->export_service->exportPdf($config['filename'], $config['title'], $config['columns'], $config['rows']);
                break;

            case 'print':
                $this->export_service->exportPrint($config['title'], $config['columns'], $config['rows']);
                break;

            default:
                show_error(get_phrase('unknown_export_format'));
        }
    }

    private function get_export_config($list_name, $identifier = '')
    {
        switch ($list_name) {
            case 'student_information':
                $filters = array();
                if ($this->input->get('first_name')) {
                    $filters['first_name'] = $this->input->get('first_name');
                }
                if ($this->input->get('last_name')) {
                    $filters['last_name'] = $this->input->get('last_name');
                }
                if ($this->input->get('email')) {
                    $filters['email'] = $this->input->get('email');
                }
                $rows = $this->crud_model->get_students($filters);
                foreach ($rows as &$row) {
                    $row['remaining_fees'] = number_format(floatval($row['total_fees']) - floatval($row['payment_done']), 2);
                    $row['age'] = '-';
                    if (!empty($row['birthday'])) {
                        $dob = date_create($row['birthday']);
                        if ($dob) {
                            $row['age'] = date_diff(new DateTime(), $dob)->y;
                        }
                    }
                }

                return [
                    'title'   => get_phrase('student_information'),
                    'filename'=> 'student_information_' . ($identifier === '' ? 'all' : $identifier),
                    'columns' => [
                        ['label' => 'Student ID', 'key' => 'student_id'],
                        ['label' => 'First Name', 'key' => 'first_name'],
                        ['label' => 'Middle Name', 'key' => 'middle_name'],
                        ['label' => 'Last Name', 'key' => 'last_name'],
                        ['label' => 'Name', 'key' => 'name'],
                        ['label' => 'Birthday', 'key' => 'birthday'],
                        ['label' => 'Age', 'key' => 'age'],
                        ['label' => 'Sex', 'key' => 'sex'],
                        ['label' => 'Religion', 'key' => 'religion'],
                        ['label' => 'Blood Group', 'key' => 'blood_group'],
                        ['label' => 'Address', 'key' => 'address'],
                        ['label' => 'Phone', 'key' => 'phone'],
                        ['label' => 'Father Mobile', 'key' => 'fmobile'],
                        ['label' => 'Email', 'key' => 'email'],
                        ['label' => 'Password', 'key' => 'password'],
                        ['label' => 'Father Name', 'key' => 'father_name'],
                        ['label' => 'Mother Name', 'key' => 'mother_name'],
                        ['label' => 'Class ID', 'key' => 'class_id'],
                        ['label' => 'Standard', 'key' => 'standard'],
                        ['label' => 'Medium', 'key' => 'medium'],
                        ['label' => 'Board', 'key' => 'board'],
                        ['label' => 'Birth Certificate', 'key' => 'birth_certificate'],
                        ['label' => 'Marksheet', 'key' => 'marksheet'],
                        ['label' => 'Aadhar Card', 'key' => 'aadhar_card'],
                        ['label' => 'Section ID', 'key' => 'section_id'],
                        ['label' => 'Parent ID', 'key' => 'parent_id'],
                        ['label' => 'Roll', 'key' => 'roll'],
                        ['label' => 'Transport ID', 'key' => 'transport_id'],
                        ['label' => 'Dormitory ID', 'key' => 'dormitory_id'],
                        ['label' => 'Dormitory Room Number', 'key' => 'dormitory_room_number'],
                        ['label' => 'Authentication Key', 'key' => 'authentication_key'],
                        ['label' => 'Student Photo', 'key' => 'student_photo'],
                        ['label' => 'Total Fees', 'key' => 'total_fees'],
                        ['label' => 'Payment Done', 'key' => 'payment_done'],
                        ['label' => 'Remaining Fees', 'key' => 'remaining_fees']
                    ],
                    'rows'    => $rows
                ];

            case 'alumni':
                $rows = $this->crud_model->get_alumni_students();
                foreach ($rows as &$row) {
                    $row['remaining_fees'] = number_format(floatval($row['total_fees']) - floatval($row['payment_done']), 2);
                    $row['age'] = '-';
                    if (!empty($row['birthday'])) {
                        $dob = date_create($row['birthday']);
                        if ($dob) {
                            $row['age'] = date_diff(new DateTime(), $dob)->y;
                        }
                    }
                }

                return [
                    'title'   => get_phrase('manage_alumni'),
                    'filename'=> 'alumni_' . date('Ymd'),
                    'columns' => [
                        ['label' => 'Student ID', 'key' => 'student_id'],
                        ['label' => 'First Name', 'key' => 'first_name'],
                        ['label' => 'Middle Name', 'key' => 'middle_name'],
                        ['label' => 'Last Name', 'key' => 'last_name'],
                        ['label' => 'Name', 'key' => 'name'],
                        ['label' => 'Birthday', 'key' => 'birthday'],
                        ['label' => 'Age', 'key' => 'age'],
                        ['label' => 'Sex', 'key' => 'sex'],
                        ['label' => 'Address', 'key' => 'address'],
                        ['label' => 'Phone', 'key' => 'phone'],
                        ['label' => 'Father Mobile', 'key' => 'fmobile'],
                        ['label' => 'Email', 'key' => 'email'],
                        ['label' => 'Father Name', 'key' => 'father_name'],
                        ['label' => 'Mother Name', 'key' => 'mother_name'],
                        ['label' => 'Class ID', 'key' => 'class_id'],
                        ['label' => 'Standard', 'key' => 'standard'],
                        ['label' => 'Medium', 'key' => 'medium'],
                        ['label' => 'Board', 'key' => 'board'],
                        ['label' => 'Roll', 'key' => 'roll'],
                        ['label' => 'Total Fees', 'key' => 'total_fees'],
                        ['label' => 'Payment Done', 'key' => 'payment_done'],
                        ['label' => 'Remaining Fees', 'key' => 'remaining_fees']
                    ],
                    'rows'    => $rows
                ];

            case 'teacher':
                return [
                    'title'   => get_phrase('teacher_information_page'),
                    'filename'=> 'teachers_' . date('Ymd'),
                    'columns' => [
                        ['label' => 'Teacher ID', 'key' => 'teacher_id'],
                        ['label' => get_phrase('name'), 'key' => 'name'],
                        ['label' => get_phrase('email'), 'key' => 'email'],
                        ['label' => get_phrase('sex'), 'key' => 'sex'],
                        ['label' => get_phrase('address'), 'key' => 'address'],
                        ['label' => get_phrase('phone'), 'key' => 'phone']
                    ],
                    'rows'    => $this->db->get('teacher')->result_array()
                ];

            case 'parent':
                return [
                    'title'   => get_phrase('parent_information_page'),
                    'filename'=> 'parents_' . date('Ymd'),
                    'columns' => [
                        ['label' => 'Parent ID', 'key' => 'parent_id'],
                        ['label' => get_phrase('name'), 'key' => 'name'],
                        ['label' => get_phrase('email'), 'key' => 'email'],
                        ['label' => get_phrase('phone'), 'key' => 'phone'],
                        ['label' => get_phrase('profession'), 'key' => 'profession'],
                        ['label' => get_phrase('address'), 'key' => 'address']
                    ],
                    'rows'    => $this->db->get('parent')->result_array()
                ];

            case 'accountant':
                return [
                    'title'   => get_phrase('accountant_information_page'),
                    'filename'=> 'accountants_' . date('Ymd'),
                    'columns' => [
                        ['label' => 'Accountant ID', 'key' => 'accountant_id'],
                        ['label' => get_phrase('name'), 'key' => 'name'],
                        ['label' => get_phrase('email'), 'key' => 'email'],
                        ['label' => get_phrase('phone'), 'key' => 'phone']
                    ],
                    'rows'    => $this->db->get('accountant')->result_array()
                ];

            case 'librarian':
                return [
                    'title'   => get_phrase('librarian_information_page'),
                    'filename'=> 'librarians_' . date('Ymd'),
                    'columns' => [
                        ['label' => 'Librarian ID', 'key' => 'librarian_id'],
                        ['label' => get_phrase('name'), 'key' => 'name'],
                        ['label' => get_phrase('email'), 'key' => 'email'],
                        ['label' => get_phrase('phone'), 'key' => 'phone']
                    ],
                    'rows'    => $this->db->get('librarian')->result_array()
                ];

            case 'hostel':
                return [
                    'title'   => get_phrase('hestel_information_page'),
                    'filename'=> 'hostels_' . date('Ymd'),
                    'columns' => [
                        ['label' => 'Hostel ID', 'key' => 'hostel_id'],
                        ['label' => get_phrase('name'), 'key' => 'name'],
                        ['label' => get_phrase('email'), 'key' => 'email'],
                        ['label' => get_phrase('phone'), 'key' => 'phone']
                    ],
                    'rows'    => $this->db->get('hostel')->result_array()
                ];

            case 'expense_category':
                return [
                    'title'   => get_phrase('expense_information_page'),
                    'filename'=> 'expense_categories_' . date('Ymd'),
                    'columns' => [
                        ['label' => 'Expense Category ID', 'key' => 'expense_category_id'],
                        ['label' => get_phrase('name'), 'key' => 'name']
                    ],
                    'rows'    => $this->db->get('expense_category')->result_array()
                ];

            /* ---- Exams & CBT (identifier carries ids, e.g. exam_class "3_2") ---- */
            case 'cbt_exams':
                $this->load->model('exam_model');
                $rows = array();
                foreach ($this->exam_model->cbt_exams() as $e) {
                    list($o, $c) = sms_cbt_window($e);
                    $rows[] = array('title' => $e['title'], 'class' => $e['class_name'], 'subject' => $e['subject_name'],
                        'when' => $o ? date('d M Y h:i A', $o) . ' - ' . date('h:i A', $c) : '', 'duration' => $e['duration'] . ' min',
                        'questions' => $e['question_count'], 'marks' => sms_num($e['total_marks']),
                        'submitted' => $e['submitted_count'] . ' / ' . $e['assigned_count'], 'status' => ucfirst(sms_cbt_state($e, time())));
                }
                return ['title' => get_phrase('cbt_exams'), 'filename' => 'cbt_exams_' . date('Ymd'), 'rows' => $rows, 'columns' => [
                    ['label' => get_phrase('exam'), 'key' => 'title'], ['label' => get_phrase('class'), 'key' => 'class'],
                    ['label' => get_phrase('subject'), 'key' => 'subject'], ['label' => get_phrase('date'), 'key' => 'when'],
                    ['label' => get_phrase('duration'), 'key' => 'duration'], ['label' => get_phrase('questions'), 'key' => 'questions'],
                    ['label' => get_phrase('marks'), 'key' => 'marks'], ['label' => get_phrase('submitted'), 'key' => 'submitted'],
                    ['label' => get_phrase('status'), 'key' => 'status']]];

            case 'cbt_results':
                $this->load->model('exam_model');
                $exam = $this->exam_model->cbt_exam((int)$identifier);
                if (!$exam) return [];
                $results = $this->exam_model->cbt_results($exam['exam_id']);
                usort($results, function ($a, $b) { return ($a['rank'] ?: 9999) - ($b['rank'] ?: 9999); });
                $rows = array();
                foreach ($results as $r) {
                    $done = $r['percent'] !== null;
                    $rows[] = array('rank' => $r['rank'] ?: '-', 'student' => $r['student_name'], 'roll' => $r['roll'],
                        'score' => $done ? sms_num($r['score']) . ' / ' . sms_num($r['total']) : '-',
                        'percent' => $done ? number_format($r['percent'], 2) : '-',
                        'result' => $r['pass'] === null ? ucfirst(str_replace('_', ' ', $r['status'])) : ($r['pass'] ? 'Pass' : 'Fail'));
                }
                return ['title' => $exam['title'] . ' - ' . $exam['class_name'] . ' / ' . $exam['subject_name'] . ' (' . date('d M Y', strtotime($exam['exam_date'])) . ')',
                    'filename' => 'cbt_results_' . $exam['exam_id'] . '_' . date('Ymd'), 'rows' => $rows, 'columns' => [
                    ['label' => get_phrase('rank'), 'key' => 'rank'], ['label' => get_phrase('student'), 'key' => 'student'],
                    ['label' => get_phrase('roll'), 'key' => 'roll'], ['label' => get_phrase('score'), 'key' => 'score'],
                    ['label' => '%', 'key' => 'percent'], ['label' => get_phrase('result'), 'key' => 'result']]];

            case 'written_exams':
                $this->load->model('exam_model');
                $classes = array_column($this->db->get('class')->result_array(), 'name', 'class_id');
                $rows = array();
                foreach ($this->exam_model->classic_exams() as $e) {
                    $names = array();
                    foreach (array_filter(explode(',', (string)$e['class_ids'])) as $cid) if (isset($classes[$cid])) $names[] = $classes[$cid];
                    $rows[] = array('name' => $e['name'], 'date' => $e['exam_date'] ? date('d M Y', strtotime($e['exam_date'])) : $e['date'],
                        'classes' => implode(', ', $names), 'total' => $e['total_marks'], 'pass' => $e['pass_percent'] . '%',
                        'published' => $e['results_published'] ? 'Yes' : 'No', 'comment' => $e['comment']);
                }
                return ['title' => get_phrase('written_exams'), 'filename' => 'written_exams_' . date('Ymd'), 'rows' => $rows, 'columns' => [
                    ['label' => get_phrase('exam'), 'key' => 'name'], ['label' => get_phrase('date'), 'key' => 'date'],
                    ['label' => get_phrase('classes'), 'key' => 'classes'], ['label' => get_phrase('total_marks'), 'key' => 'total'],
                    ['label' => get_phrase('pass_percentage'), 'key' => 'pass'], ['label' => get_phrase('results_published'), 'key' => 'published'],
                    ['label' => get_phrase('comment'), 'key' => 'comment']]];

            case 'marks':
                $this->load->model('exam_model');
                list($eid, $cid, $sid) = array_map('intval', array_pad(explode('_', (string)$identifier), 3, 0));
                $exam = $this->exam_model->classic_exam($eid);
                $sub = $this->db->get_where('subject', array('subject_id' => $sid))->row();
                $cls = $this->db->get_where('class', array('class_id' => $cid))->row();
                if (!$exam || !$sub || !$cls) return [];
                $rows = array();
                foreach ($this->exam_model->subject_marks($eid, $cid, $sid) as $s)
                    $rows[] = array('roll' => $s['roll'], 'student' => $s['name'],
                        'obtained' => $s['mark']['mark_obtained'] === null ? '' : sms_num($s['mark']['mark_obtained']),
                        'total' => $s['mark']['mark_total'], 'comment' => $s['mark']['comment']);
                return ['title' => $exam['name'] . ' - ' . $cls->name . ' - ' . $sub->name, 'filename' => 'marks_' . $eid . '_' . $cid . '_' . $sid,
                    'rows' => $rows, 'columns' => [
                    ['label' => get_phrase('roll'), 'key' => 'roll'], ['label' => get_phrase('student'), 'key' => 'student'],
                    ['label' => get_phrase('marks_obtained'), 'key' => 'obtained'], ['label' => get_phrase('total_marks'), 'key' => 'total'],
                    ['label' => get_phrase('comment'), 'key' => 'comment']]];

            case 'tabulation':
                $this->load->model('exam_model');
                list($eid, $cid) = array_map('intval', array_pad(explode('_', (string)$identifier), 2, 0));
                $tab = $this->exam_model->tabulation($eid, $cid);
                $cls = $this->db->get_where('class', array('class_id' => $cid))->row();
                if (!$tab['exam'] || !$cls) return [];
                $columns = [['label' => get_phrase('rank'), 'key' => 'rank'], ['label' => get_phrase('student'), 'key' => 'student']];
                foreach ($tab['subjects'] as $sub) $columns[] = ['label' => $sub['name'], 'key' => 'sub_' . $sub['subject_id']];
                $columns = array_merge($columns, [['label' => get_phrase('total'), 'key' => 'total'], ['label' => '%', 'key' => 'percent'],
                    ['label' => get_phrase('grade'), 'key' => 'grade'], ['label' => get_phrase('result'), 'key' => 'result']]);
                $rows = array();
                usort($tab['rows'], function ($a, $b) { return ($a['rank'] ?: 9999) - ($b['rank'] ?: 9999); });
                foreach ($tab['rows'] as $r) {
                    $row = array('rank' => $r['rank'] ?: '-', 'student' => $r['student']['name'],
                        'total' => $r['entered'] ? sms_num($r['obtained']) . ' / ' . sms_num($r['total']) : '-',
                        'percent' => $r['percent'] !== null ? number_format($r['percent'], 2) : '-', 'grade' => $r['grade'] ?: '-',
                        'result' => $r['pass'] === null ? '-' : ($r['pass'] ? 'Pass' : 'Fail'));
                    foreach ($tab['subjects'] as $sub) {
                        $c = $r['cells'][$sub['subject_id']];
                        $row['sub_' . $sub['subject_id']] = $c ? sms_num($c['obtained']) . ' / ' . sms_num($c['total']) : '-';
                    }
                    $rows[] = $row;
                }
                return ['title' => get_phrase('tabulation_sheet') . ': ' . $tab['exam']['name'] . ' - ' . $cls->name,
                    'filename' => 'tabulation_' . $eid . '_' . $cid, 'columns' => $columns, 'rows' => $rows];

            case 'grades':
                $rows = $this->db->order_by('mark_from', 'DESC')->get('grade')->result_array();
                return ['title' => get_phrase('manage_grade'), 'filename' => 'grades_' . date('Ymd'), 'rows' => $rows, 'columns' => [
                    ['label' => get_phrase('grade_name'), 'key' => 'name'], ['label' => get_phrase('grade_point'), 'key' => 'grade_point'],
                    ['label' => get_phrase('mark_from') . ' (%)', 'key' => 'mark_from'], ['label' => get_phrase('mark_upto') . ' (%)', 'key' => 'mark_upto'],
                    ['label' => get_phrase('comment'), 'key' => 'comment']]];

            case 'email_log':
                $rows = $this->db->select('created_at, to_email, subject, event, status, error')->order_by('log_id', 'DESC')->limit(1000)->get('email_log')->result_array();
                return ['title' => get_phrase('email_log'), 'filename' => 'email_log_' . date('Ymd'), 'rows' => $rows, 'columns' => [
                    ['label' => get_phrase('date'), 'key' => 'created_at'], ['label' => get_phrase('to'), 'key' => 'to_email'],
                    ['label' => get_phrase('subject'), 'key' => 'subject'], ['label' => get_phrase('type'), 'key' => 'event'],
                    ['label' => get_phrase('status'), 'key' => 'status'], ['label' => get_phrase('error'), 'key' => 'error']]];

            case 'banner':
                return [
                    'title'   => get_phrase('banner_information_page'),
                    'filename'=> 'banners_' . date('Ymd'),
                    'columns' => [
                        ['label' => 'Banner ID', 'key' => 'banner_id'],
                        ['label' => get_phrase('b_text_one'), 'key' => 'b_namea'],
                        ['label' => get_phrase('b_text_two'), 'key' => 'b_nameb']
                    ],
                    'rows'    => $this->db->get('banner')->result_array()
                ];

            case 'student_payment':
                $invoices = $this->db->order_by('creation_timestamp', 'desc')->get('invoice')->result_array();
                foreach ($invoices as &$invoice) {
                    $payment_rows = [];
                    if ($this->db->table_exists('payment')) {
                        $payment_rows = $this->db->get_where('payment', ['invoice_id' => $invoice['invoice_id']])->result_array();
                    }
                    if ($this->db->table_exists('student_payment_history')) {
                        $payment_rows = array_merge($payment_rows, $this->db->get_where('student_payment_history', ['invoice_id' => $invoice['invoice_id']])->result_array());
                    }
                    $history = [];
                    $paid = 0;
                    foreach ($payment_rows as $payment) {
                        $paid += floatval($payment['amount']);
                        $method = $payment['method'];
                        if ($method == 1) {
                            $method = get_phrase('cash');
                        } elseif ($method == 2) {
                            $method = get_phrase('check');
                        } elseif ($method == 3) {
                            $method = get_phrase('card');
                        }
                        $history[] = $payment['amount'] . ' (' . $method . ' ' . date('d M,Y', $payment['timestamp']) . ')';
                    }
                    $invoice['student_name'] = $this->crud_model->get_type_name_by_id('student', $invoice['student_id']);
                    $invoice['amount_paid'] = number_format($paid, 2);
                    $invoice['due'] = number_format(floatval($invoice['amount']) - $paid, 2);
                    $invoice['payment_history'] = implode('; ', $history);
                    $invoice['creation_date'] = date('d M,Y', $invoice['creation_timestamp']);
                    if (floatval($invoice['due']) <= 0) {
                        $invoice['status'] = 'paid';
                    }
                }

                return [
                    'title' => get_phrase('student_payments'),
                    'filename' => 'student_payments_' . date('Ymd'),
                    'columns' => [
                        ['label' => get_phrase('student'), 'key' => 'student_name'],
                        ['label' => get_phrase('title'), 'key' => 'title'],
                        ['label' => get_phrase('total_fees'), 'key' => 'amount'],
                        ['label' => get_phrase('paid'), 'key' => 'amount_paid'],
                        ['label' => get_phrase('due'), 'key' => 'due'],
                        ['label' => get_phrase('status'), 'key' => 'status'],
                        ['label' => get_phrase('payment_history'), 'key' => 'payment_history'],
                        ['label' => get_phrase('date'), 'key' => 'creation_date']
                    ],
                    'rows' => $invoices
                ];

            default:
                return [];
        }
    }

    function student_marksheet($student_id = '') {
        if ($this->session->userdata('admin_login') != 1)
            redirect('login', 'refresh');
        $class_id     = $this->db->get_where('student' , array('student_id' => $student_id))->row()->class_id;
        $student_name = $this->db->get_where('student' , array('student_id' => $student_id))->row()->name;
        $class_name   = $this->db->get_where('class' , array('class_id' => $class_id))->row()->name;
        $page_data['page_name']  =   'student_marksheet';
        $page_data['page_title'] =   get_phrase('marksheet_for') . ' ' . $student_name . ' (' . get_phrase('class') . ' ' . $class_name . ')';
        $page_data['student_id'] =   $student_id;
        $page_data['class_id']   =   $class_id;
        $this->load->view('backend/index', $page_data);
    }

    function student_marksheet_print_view($student_id , $exam_id) {
        if ($this->session->userdata('admin_login') != 1)
            redirect('login', 'refresh');
        $class_id     = $this->db->get_where('student' , array('student_id' => $student_id))->row()->class_id;
        $class_name   = $this->db->get_where('class' , array('class_id' => $class_id))->row()->name;

        $page_data['student_id'] =   $student_id;
        $page_data['class_id']   =   $class_id;
        $page_data['exam_id']    =   $exam_id;
        $this->load->view('backend/admin/student_marksheet_print_view', $page_data);
    }
	
 public function student($param1 = '', $param2 = '', $param3 = '')
{
    if ($this->session->userdata('admin_login') != 1) {
        redirect('login', 'refresh');
    }

    $this->load->library('form_validation');
    /* =========================================================
       CREATE
    ========================================================= */
    if ($param1 == 'create') {

        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('birthday', 'DOB', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('sex', 'Gender', 'required');
        $this->form_validation->set_rules('home', 'Address', 'required');
        $this->form_validation->set_rules('fmobile', 'Father Mobile', 'required|regex_match[/^[0-9]{10}$/]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(base_url().'index.php?admin/student_add', 'refresh');
        }

        $data = [
            'first_name'        => $this->input->post('first_name'),
            'middle_name'       => $this->input->post('middle_name'),
            'last_name'         => $this->input->post('last_name'),
            'name'              => sms_full_name(
                $this->input->post('first_name'),
                $this->input->post('middle_name'),
                $this->input->post('last_name')
            ),
            'birthday'          => $this->input->post('birthday'),
            'sex'               => $this->input->post('sex'),
            'address'           => $this->input->post('home'),
            'father_name'       => $this->input->post('father_name'),
            'fmobile'           => $this->input->post('fmobile'),
            'mother_name'       => $this->input->post('mother_name'),
            'mmobile'           => $this->input->post('mmobile'),
            'emergency_contact' => $this->input->post('emergency_contact'),
            'email'             => $this->input->post('email'),
            'class_id'          => $this->input->post('class_id'),
            'section_id'        => $this->input->post('section_id'),
            'standard'          => $this->get_class_name_for_student($this->input->post('class_id')),
            'medium'            => $this->input->post('medium'),
            'board'             => $this->input->post('board'),
            'total_fees'        => $this->input->post('total_fees'),
            'is_alumni'         => $this->input->post('is_alumni') ? 1 : 0,
            'academic_year'     => $this->input->post('academic_year') ?: $this->academic_year_for(),
            'student_mobile'    => $this->student_mobile_value_for($this->input->post('class_id'), $this->input->post('student_mobile')),
            'password'          => password_hash('password', PASSWORD_BCRYPT)
        ];

        $this->db->insert('student', $data);
        $student_id = $this->db->insert_id();

        // ================= INSERT PAYMENTS (HISTORY) =================
        $payments = $this->extractPaymentsFromPost();
        $total_payment = sms_payments_total($payments); // unit tested

        foreach ($payments as $p) {
            $this->db->insert('student_payment_history', [
                'student_id'     => $student_id,
                'invoice_id'     => 0,
                'title'          => 'Payment',
                'payment_type'   => $p['type'],
                'method'         => $p['mode'],
                'description'    => 'Payment entry',
                'amount'         => $p['amount'],
                'timestamp'      => $p['date'] ? strtotime($p['date']) : time(),
                'transaction_id' => isset($p['transaction_id']) ? $p['transaction_id'] : null,
                'cheque_number'  => isset($p['cheque_number'])  ? $p['cheque_number']  : null,
                'cheque_bank'    => isset($p['cheque_bank'])    ? $p['cheque_bank']    : null,
                'cheque_date'    => isset($p['cheque_date']) && $p['cheque_date'] ? $p['cheque_date'] : null,
            ]);
        }

        // update total paid
        $this->db->where('student_id', $student_id)
                 ->update('student', ['payment_done' => $total_payment]);

        $this->handleStudentFiles($student_id);

        // Send confirmation email
        try {
            $this->load->model('email_model');
            $this->email_model->student_registration_email($data['email']);
        } catch (Exception $e) {
            // Log error but don't fail the save
            log_message('error', 'Email sending failed: ' . $e->getMessage());
        }

        // Send WhatsApp confirmation message (if enabled) and log the response
        if (!empty($data['fmobile'])) {
            $whatsapp_message = '';
            $wa_response = null;
            try {
                $this->load->model('sms_model');

                $tpl_row = $this->db->get_where('settings', array('type' => 'whatsapp_welcome_message'))->row();
                $template = ($tpl_row && $tpl_row->description !== '')
                    ? $tpl_row->description
                    : "Dear Parent,\nThis is to confirm that your student {{studentname}} successfully registered. We welcome you.";

                $school_row = $this->db->get_where('settings', array('type' => 'system_name'))->row();
                $school_name = $school_row ? $school_row->description : '';

                // Placeholder rendering lives in sms_core_helper (unit tested).
                $whatsapp_message = sms_render_template($template, array(
                    'studentname' => $data['name'],
                    'studentid'   => $student_id,
                    'schoolname'  => $school_name,
                    'class'       => $data['standard'],
                ));

                $wa_response = $this->sms_model->send_whatsapp($whatsapp_message, $data['fmobile']);
            } catch (Exception $e) {
                log_message('error', 'WhatsApp sending failed: ' . $e->getMessage());
                $wa_response = array('success' => false, 'error_message' => $e->getMessage());
            }

            $this->db->insert('whatsapp_log', array(
                'student_id'    => $student_id,
                'event_type'    => 'student_registration',
                'phone_to'      => isset($wa_response['to']) ? $wa_response['to'] : ('whatsapp:' . $data['fmobile']),
                'phone_from'    => isset($wa_response['from']) ? $wa_response['from'] : null,
                'message_body'  => $whatsapp_message,
                'success'       => !empty($wa_response['success']) ? 1 : 0,
                'http_code'     => isset($wa_response['http_code']) ? $wa_response['http_code'] : null,
                'twilio_sid'    => isset($wa_response['sid']) ? $wa_response['sid'] : null,
                'twilio_status' => isset($wa_response['status']) ? $wa_response['status'] : null,
                'error_code'    => isset($wa_response['error_code']) ? $wa_response['error_code'] : null,
                'error_message' => isset($wa_response['error_message']) ? $wa_response['error_message'] : null,
                'raw_response'  => isset($wa_response['raw_response']) ? $wa_response['raw_response'] : null,
                'response_payload' => is_array($wa_response) ? json_encode($wa_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : null,
                'created_at'    => date('Y-m-d H:i:s'),
            ));
        }

        $this->session->set_flashdata('flash_message', 'Student created successfully');
        redirect(base_url().'index.php?admin/student_information', 'refresh');
    }

    /* =========================================================
       UPDATE (HISTORY SAFE)
    ========================================================= */
    if ($param2 == 'do_update') {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(base_url().'index.php?admin/student_information/'.$param1, 'refresh');
        }

        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('sex', 'Gender', 'required');
        $this->form_validation->set_rules('home', 'Address', 'required');
        $this->form_validation->set_rules('fmobile', 'Father Mobile', 'required|regex_match[/^[0-9]{10}$/]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect(base_url().'index.php?admin/student_information/'.$param1, 'refresh');
        }

        // ================= UPDATE STUDENT =================
        $data = [
            'first_name'        => $this->input->post('first_name'),
            'middle_name'       => $this->input->post('middle_name'),
            'last_name'         => $this->input->post('last_name'),
            'name'              => sms_full_name(
                $this->input->post('first_name'),
                $this->input->post('middle_name'),
                $this->input->post('last_name')
            ),
            'birthday'          => $this->input->post('birthday'),
            'sex'               => $this->input->post('sex'),
            'address'           => $this->input->post('home'),
            'father_name'       => $this->input->post('father_name'),
            'fmobile'           => $this->input->post('fmobile'),
            'mother_name'       => $this->input->post('mother_name'),
            'mmobile'           => $this->input->post('mmobile'),
            'emergency_contact' => $this->input->post('emergency_contact'),
            'email'             => $this->input->post('email'),
            'class_id'          => $this->input->post('class_id'),
            'section_id'        => $this->input->post('section_id'),
            'standard'          => $this->get_class_name_for_student($this->input->post('class_id')),
            'medium'            => $this->input->post('medium'),
            'board'             => $this->input->post('board'),
            'is_alumni'         => $this->input->post('is_alumni') ? 1 : 0,
            'is_reregister'     => $this->input->post('is_reregister') ? 1 : 0,
            'academic_year'     => $this->input->post('academic_year') ?: null,
            'student_mobile'    => $this->student_mobile_value_for($this->input->post('class_id'), $this->input->post('student_mobile')),
        ];

        $this->db->where('student_id', $param3)->update('student', $data);

        // ================= INSERT NEW PAYMENTS ONLY =================
        $payments = $this->extractPaymentsFromPost();

        foreach ($payments as $p) {
            // Only insert if this payment is NEW (avoid duplicates)
            if (!empty($p['amount'])) {

                $this->db->insert('student_payment_history', [
                    'student_id'   => $param3,
                    'invoice_id'   => 0,
                    'title'        => 'Payment',
                    'payment_type' => $p['type'],
                    'method'       => $p['mode'],
                    'description'  => 'Payment entry',
                    'amount'       => $p['amount'],
                    'timestamp'    => $p['date'] ? strtotime($p['date']) : time()
                ]);
            }
        }

        // ================= RECALCULATE TOTAL =================
        $total_payment = $this->db->select_sum('amount')
            ->where('student_id', $param3)
            ->get('student_payment_history')
            ->row()->amount;

        $this->db->where('student_id', $param3)
                 ->update('student', ['payment_done' => $total_payment]);

        $this->handleStudentFiles($param3);

        // Send confirmation email
        try {
            $this->load->model('email_model');
            $this->email_model->student_registration_email($data['email']);
        } catch (Exception $e) {
            // Log error but don't fail the update
            log_message('error', 'Email sending failed: ' . $e->getMessage());
        }

        // Send WhatsApp update message (if enabled)
        if (!empty($data['fmobile'])) {
            try {
                $this->load->model('sms_model');
                $whatsapp_message = "Your student profile has been updated successfully in " . get_phrase('system_name') . ". Student ID: " . $param3;
                $this->sms_model->send_whatsapp($whatsapp_message, $data['fmobile']);
            } catch (Exception $e) {
                log_message('error', 'WhatsApp sending failed: ' . $e->getMessage());
            }
        }

        $this->session->set_flashdata('flash_message', 'Student updated successfully');
        redirect(base_url().'index.php?admin/student_information/'.$this->input->post('class_id'), 'refresh');
    }

    /* =========================================================
       DELETE
    ========================================================= */
    if ($param2 == 'delete') {
        $this->db->delete('student', ['student_id' => $param3]);
        $this->db->delete('student_payment_history', ['student_id' => $param3]);

        $this->session->set_flashdata('flash_message', 'Deleted successfully');
        redirect(base_url().'index.php?admin/student_information/'.$param1, 'refresh');
    }
}

    public function search_students()
    {
        if ($this->session->userdata('admin_login') != 1) {
            echo 'Unauthorized';
            return;
        }

        $filters = array();
        if ($this->input->post('first_name')) {
            $filters['first_name'] = $this->input->post('first_name');
        }
        if ($this->input->post('last_name')) {
            $filters['last_name'] = $this->input->post('last_name');
        }
        if ($this->input->post('email')) {
            $filters['email'] = $this->input->post('email');
        }

        $students = $this->crud_model->get_students($filters);

        $output = '';
        foreach ($students as $row) {
            // AGE CALCULATION
            $age = '-';
            if (!empty($row['birthday'])) {
                $dob = date_create($row['birthday']);
                if ($dob) {
                    $age = date_diff(new DateTime(), $dob)->y;
                }
            }

            // FEES CALCULATION
            $remaining = '-';
            if (isset($row['total_fees']) || isset($row['payment_done'])) {
                $remaining = floatval($row['total_fees']) - floatval($row['payment_done']);
                $remaining = number_format($remaining, 2);
            }

            $output .= '<tr>';
            $output .= '<td>' . $row['student_id'] . '</td>';
            $output .= '<td><img src="' . $this->crud_model->get_image_url('student', $row['student_id']) . '" width="30" class="img-circle"></td>';
            $output .= '<td>' . $row['first_name'] . '</td>';
            $output .= '<td>' . $row['middle_name'] . '</td>';
            $output .= '<td>' . $row['last_name'] . '</td>';
            $output .= '<td>' . $row['sex'] . '</td>';
            $output .= '<td>' . $row['fmobile'] . '</td>';
            $output .= '<td>' . $row['standard'] . '</td>';
            $output .= '<td>' . $row['medium'] . '</td>';
            $output .= '<td>' . $row['board'] . '</td>';
            $output .= '<td>' . $age . '</td>';
            $output .= '<td>' . $remaining . '</td>';
            $output .= '<td>' . $row['email'] . '</td>';
            $output .= '<td>';
            $output .= '<div class="btn-group">';
            $output .= '<button class="btn btn-blue btn-sm dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span></button>';
            $output .= '<ul class="dropdown-menu dropdown-primary pull-right">';
            $output .= '<li><a href="#" onclick="showAjaxModal(\'' . base_url() . 'index.php?modal/popup/modal_student_profile/' . $row['student_id'] . '\');">Profile</a></li>';
            $output .= '<li><a href="#" onclick="showAjaxModal(\'' . base_url() . 'index.php?modal/popup/modal_student_edit/' . $row['student_id'] . '\');">Edit</a></li>';
            $output .= '<li class="divider"></li>';
            if (!empty($row['student_photo'])) {
                $output .= '<li><a href="' . base_url('uploads/student_files/' . $row['student_photo']) . '" target="_blank" download>Download Student Photo</a></li>';
            }
            if (!empty($row['aadhar_card'])) {
                $output .= '<li><a href="' . base_url('uploads/student_files/' . $row['aadhar_card']) . '" target="_blank" download>Download Aadhar Card</a></li>';
            }
            if (!empty($row['birth_certificate'])) {
                $output .= '<li><a href="' . base_url('uploads/student_files/' . $row['birth_certificate']) . '" target="_blank" download>Download Birth Certificate</a></li>';
            }
            if (!empty($row['marksheet'])) {
                $output .= '<li><a href="' . base_url('uploads/student_files/' . $row['marksheet']) . '" target="_blank" download>Download Marksheet</a></li>';
            }
            if (!empty($row['student_photo']) || !empty($row['aadhar_card']) || !empty($row['birth_certificate']) || !empty($row['marksheet'])) {
                $output .= '<li class="divider"></li>';
            }
            $output .= '<li><a href="' . base_url() . 'index.php?admin/student/' . $row['class_id'] . '/delete/' . $row['student_id'] . '" onclick="return confirm(\'Are you sure?\')">Delete</a></li>';
            $output .= '</ul>';
            $output .= '</div>';
            $output .= '</td>';
            $output .= '</tr>';
        }

        echo $output;
    }

    public function extractPaymentsFromPost()
    {
        $payments = [];

    foreach ($_POST as $key => $value) {
        if (preg_match('/payment(\d+)_amount/', $key, $match)) {
            $i = $match[1];
            $amount = floatval($value);

            if ($amount > 0) {
                $payments[] = [
                    'index'          => $i,
                    'amount'         => $amount,
                    'date'           => $this->input->post('payment'.$i.'_date'),
                    'type'           => $this->input->post('payment'.$i.'_type'),
                    'mode'           => $this->input->post('payment'.$i.'_mode'),
                    'transaction_id' => $this->input->post('payment'.$i.'_transaction_id'),
                    'cheque_number'  => $this->input->post('payment'.$i.'_cheque_number'),
                    'cheque_bank'    => $this->input->post('payment'.$i.'_cheque_bank'),
                    'cheque_date'    => $this->input->post('payment'.$i.'_cheque_date'),
                ];
            }
        }
    }

    return $payments;
}

public function handleStudentFiles($student_id)
{
    $upload_path = FCPATH . 'uploads/student_files/';
    $doc_path    = FCPATH . 'uploads/student_documents/';

    // Create folders if not exist
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0777, true);
    }

    if (!is_dir($doc_path)) {
        mkdir($doc_path, 0777, true);
    }

    // Get old data (for replace)
    $student = $this->db->get_where('student', ['student_id' => $student_id])->row();

    /* ================= STUDENT PHOTO ================= */
    if (isset($_FILES['student_photo']) && $_FILES['student_photo']['error'] == 0) {

        $ext = strtolower(pathinfo($_FILES['student_photo']['name'], PATHINFO_EXTENSION));
        $file_name = $student_id . '_photo_' . time() . '.' . $ext;

        // Delete old file
        if (!empty($student->student_photo) && file_exists($upload_path . $student->student_photo)) {
            unlink($upload_path . $student->student_photo);
        }

        move_uploaded_file($_FILES['student_photo']['tmp_name'], $upload_path . $file_name);

        $this->db->where('student_id', $student_id);
        $this->db->update('student', ['student_photo' => $file_name]);
    }

    /* ================= AADHAR CARD ================= */
    if (isset($_FILES['government_identity']) && $_FILES['government_identity']['error'] == 0) {

        $ext = strtolower(pathinfo($_FILES['government_identity']['name'], PATHINFO_EXTENSION));
        $file_name = $student_id . '_aadhar_' . time() . '.' . $ext;

        // Delete old file
        if (!empty($student->aadhar_card) && file_exists($doc_path . $student->aadhar_card)) {
            unlink($doc_path . $student->aadhar_card);
        }

        move_uploaded_file($_FILES['government_identity']['tmp_name'], $doc_path . $file_name);

        $this->db->where('student_id', $student_id);
        $this->db->update('student', ['aadhar_card' => $file_name]);
    }

    /* ================= MARKSHEET ================= */
    if (isset($_FILES['mark_sheet']) && $_FILES['mark_sheet']['error'] == 0) {

        $ext = strtolower(pathinfo($_FILES['mark_sheet']['name'], PATHINFO_EXTENSION));
        $file_name = $student_id . '_marksheet_' . time() . '.' . $ext;

        // Delete old file
        if (!empty($student->marksheet) && file_exists($doc_path . $student->marksheet)) {
            unlink($doc_path . $student->marksheet);
        }

        move_uploaded_file($_FILES['mark_sheet']['tmp_name'], $doc_path . $file_name);

        $this->db->where('student_id', $student_id);
        $this->db->update('student', ['marksheet' => $file_name]);
    }
}
    
     /****MANAGE PARENTS CLASSWISE*****/
    function parent($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect('login', 'refresh');
        if ($param1 == 'create') {
            $data['name']        			= $this->input->post('name');
            $data['email']       			= $this->input->post('email');
            $data['password']    			= $this->input->post('password');
            $data['phone']       			= $this->input->post('phone');
            $data['address']     			= $this->input->post('address');
            $data['profession']  			= $this->input->post('profession');
            $this->db->insert('parent', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            $this->email_model->account_opening_email('parent', $data['email']); //SEND EMAIL ACCOUNT OPENING EMAIL
            redirect(base_url() . 'index.php?admin/parent/', 'refresh');
        }
        if ($param1 == 'edit') {
            $data['name']                   = $this->input->post('name');
            $data['email']                  = $this->input->post('email');
            $data['phone']                  = $this->input->post('phone');
            $data['address']                = $this->input->post('address');
            $data['profession']             = $this->input->post('profession');
            $this->db->where('parent_id' , $param2);
            $this->db->update('parent' , $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/parent/', 'refresh');
        }
        if ($param1 == 'delete') {
            $this->db->where('parent_id' , $param2);
            $this->db->delete('parent');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/parent/', 'refresh');
        }
        $page_data['page_title'] 	= get_phrase('all_parents');
        $page_data['page_name']  = 'parent';
        $this->load->view('backend/index', $page_data);
    }
	
    
    /****MANAGE TEACHERS*****/
    function teacher($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_teacher_salary_columns();

        if ($param1 == 'create') {
            $data = $this->collect_teacher_post();
            $data['password']    = $this->input->post('password');
            $this->db->insert('teacher', $data);
            $teacher_id = $this->db->insert_id();
            $this->handle_user_photo('teacher', 'teacher_id', $teacher_id, 'teacher_photo', 'teacher_photo');
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            $this->email_model->account_opening_email('teacher', $data['email']); //SEND EMAIL ACCOUNT OPENING EMAIL
            redirect(base_url() . 'index.php?admin/teacher/', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data = $this->collect_teacher_post();

            $this->db->where('teacher_id', $param2);
            $this->db->update('teacher', $data);
            $this->handle_user_photo('teacher', 'teacher_id', $param2, 'teacher_photo', 'teacher_photo');
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/teacher/', 'refresh');
        } else if ($param1 == 'personal_profile') {
            $page_data['personal_profile']   = true;
            $page_data['current_teacher_id'] = $param2;
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('teacher', array(
                'teacher_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('teacher_id', $param2);
            $this->db->delete('teacher');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/teacher/', 'refresh');
        }
        $page_data['teachers']   = $this->db->get('teacher')->result_array();
        $page_data['page_name']  = 'teacher';
        $page_data['page_title'] = get_phrase('manage_teacher');
        $this->load->view('backend/index', $page_data);
    }
	
	
	
	 /****MANAGE ALUMNI*****/
    function alumni($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
       
        $page_data['alumni']   = $this->crud_model->get_alumni_students();
        $page_data['page_name']  = 'alumni';
        $page_data['page_title'] = get_phrase('manage_alumni');
        $this->load->view('backend/index', $page_data);
    }
	
	
	/****MANAGE TEACHERS*****/
    function teacher_id_card($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_teacher_salary_columns();

        $page_data['teachers']   = $this->db->get('teacher')->result_array();
        $page_data['page_name']  = 'teacher_id_card';
        $page_data['page_title'] = get_phrase('manage_teacher_idcard');
        $this->load->view('backend/index', $page_data);
    }

    /**
     * Salary slip for a teacher. On GET shows the input form (month + days worked,
     * which pre-fills the working days of that month). On POST renders a print/PDF view.
     *
     * URL:
     *   admin/teacher_salary_slip/<teacher_id>              -> form
     *   admin/teacher_salary_slip/<teacher_id>/generate     -> renders printable slip
     */
    function teacher_salary_slip($teacher_id = '', $mode = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_teacher_salary_columns();

        $teacher_id = (int)$teacher_id;
        $teacher = $this->db->get_where('teacher', array('teacher_id' => $teacher_id))->row_array();
        if (!$teacher) {
            show_error('Teacher not found', 404);
            return;
        }

        // school header for slip — same fields the student invoice receipt uses
        $school = array();
        $name_row    = $this->db->get_where('settings', array('type' => 'system_name'))->row();
        $school['name']    = $name_row ? $name_row->description : 'School';
        $address_row = $this->db->get_where('settings', array('type' => 'address'))->row();
        $school['address'] = $address_row ? $address_row->description : '';
        $school['phone']   = '9987676008';
        $school['email']   = 'shreecochingclasses@gmail.com';
        $school['website'] = 'shreecochingclasses.com';

        if ($mode == 'generate' && $this->input->post('month')) {
            $month_str   = $this->input->post('month'); // YYYY-MM
            $days_worked = (float)$this->input->post('days_worked');
            $remarks     = $this->input->post('remarks');

            $ts = strtotime($month_str . '-01');
            $month_name  = date('F Y', $ts);
            $month_days  = (int)date('t', $ts);
            if ($days_worked <= 0) $days_worked = $month_days;
            if ($days_worked > $month_days) $days_worked = $month_days;

            $ratio = $month_days > 0 ? ($days_worked / $month_days) : 0;

            $earnings = array(
                'Basic Salary'      => (float)$teacher['basic_salary'] * $ratio,
                'HRA'               => (float)$teacher['hra'] * $ratio,
                'DA'                => (float)$teacher['da'] * $ratio,
                'Conveyance'        => (float)$teacher['conveyance'] * $ratio,
                'Medical Allowance' => (float)$teacher['medical_allowance'] * $ratio,
                'Other Allowance'   => (float)$teacher['other_allowance'] * $ratio,
            );
            $deductions = array(
                'PF'              => (float)$teacher['pf_deduction'] * $ratio,
                'Tax'             => (float)$teacher['tax_deduction'] * $ratio,
                'Other Deduction' => (float)$teacher['other_deduction'] * $ratio,
            );
            $gross = array_sum($earnings);
            $total_deduction = array_sum($deductions);
            $net   = max(0, $gross - $total_deduction);

            $view_data = array(
                'school'         => $school,
                'teacher'        => $teacher,
                'month_name'     => $month_name,
                'month_days'     => $month_days,
                'days_worked'    => $days_worked,
                'remarks'        => $remarks,
                'earnings'       => $earnings,
                'deductions'     => $deductions,
                'gross'          => $gross,
                'total_deduction'=> $total_deduction,
                'net'            => $net,
                'pdf'            => $this->input->post('output') === 'pdf',
            );
            $this->load->view('backend/admin/teacher_salary_slip_print', $view_data);
            return;
        }

        // Default month = current; seed days_worked from attendance
        $default_month = date('Y-m');
        $page_data['teacher']            = $teacher;
        $page_data['school']             = $school;
        $page_data['default_month']      = $default_month;
        $page_data['default_present']    = $this->teacher_present_days($teacher_id, $default_month);
        $page_data['page_name']          = 'teacher_salary_slip';
        $page_data['page_title']         = 'Generate Salary Slip';
        $this->load->view('backend/index', $page_data);
    }

    function student_id_card($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_student_alumni_column();

        $this->db->where('is_active', 1);
        if ($this->db->field_exists('is_alumni', 'student')) {
            $this->db->group_start()
                     ->where('is_alumni', 0)
                     ->or_where('is_alumni IS NULL', null, false)
                 ->group_end();
        }
        $page_data['students']   = $this->db->get('student')->result_array();
        $page_data['page_name']  = 'student_id_card';
        $page_data['page_title'] = 'Manage Student ID Card';
        $this->load->view('backend/index', $page_data);
    }

    function student_export_invoice($student_id = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $student = $this->db->get_where('student', array('student_id' => $student_id))->row_array();
        if (!$student) {
            show_error('Student not found', 404);
            return;
        }

        $payments = array();
        if ($this->db->table_exists('student_payment_history')) {
            $payments = $this->db->order_by('timestamp', 'asc')
                ->get_where('student_payment_history', array('student_id' => $student_id))->result_array();
        }

        $paid = 0;
        foreach ($payments as $p) $paid += floatval($p['amount']);

        $total_fees = floatval($student['total_fees'] ?? 0);

        $synthetic_invoice = array(
            'invoice_id'  => $student_id,
            'title'       => 'Tuition Fees',
            'description' => trim(($student['standard'] ?? '') . ' ' . ($student['medium'] ?? '') . ' ' . ($student['board'] ?? '')),
            'amount'      => $total_fees,
        );

        $school = array();
        $school_name_row   = $this->db->get_where('settings', array('type' => 'system_name'))->row();
        $school['name']    = $school_name_row ? $school_name_row->description : '';
        $address_row       = $this->db->get_where('settings', array('type' => 'address'))->row();
        $school['address'] = $address_row ? $address_row->description : '';
        $school['phone']   = '9987676008';
        $school['email']   = 'shreecochingclasses@gmail.com';
        $school['website'] = 'shreecochingclasses.com';

        $view_data = array(
            'invoice'  => $synthetic_invoice,
            'student'  => $student,
            'payments' => $payments,
            'paid'     => $paid,
            'due'      => max($total_fees - $paid, 0),
            'school'   => $school,
        );
        $this->load->view('backend/admin/student_invoice_receipt', $view_data);
    }

    function student_invoice_receipt($invoice_id = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $invoice = $this->db->get_where('invoice', array('invoice_id' => $invoice_id))->row_array();
        if (!$invoice) {
            show_error('Invoice not found', 404);
            return;
        }

        $student = $this->db->get_where('student', array('student_id' => $invoice['student_id']))->row_array();

        $payments = array();
        if ($this->db->table_exists('payment')) {
            $payments = $this->db->order_by('timestamp', 'asc')
                ->get_where('payment', array('invoice_id' => $invoice_id))->result_array();
        }
        if ($this->db->table_exists('student_payment_history')) {
            $hist = $this->db->order_by('timestamp', 'asc')
                ->get_where('student_payment_history', array('invoice_id' => $invoice_id))->result_array();
            $payments = array_merge($payments, $hist);
        }

        $paid = 0;
        foreach ($payments as $p) $paid += floatval($p['amount']);

        $school = array();
        $school['name']    = $this->db->get_where('settings', array('type' => 'system_name'))->row()->description ?? '';
        $address_row       = $this->db->get_where('settings', array('type' => 'address'))->row();
        $school['address'] = $address_row ? $address_row->description : '';
        $school['phone']   = '9987676008';
        $school['email']   = 'shreecochingclasses@gmail.com';
        $school['website'] = 'shreecochingclasses.com';

        $view_data = array(
            'invoice'  => $invoice,
            'student'  => $student,
            'payments' => $payments,
            'paid'     => $paid,
            'due'      => floatval($invoice['amount']) - $paid,
            'school'   => $school,
        );
        $this->load->view('backend/admin/student_invoice_receipt', $view_data);
    }
	
	
	
	
	/****MANAGE TEACHERS generateidcard*****/
    function generateidcard($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            $data['name']        = $this->input->post('name');
            $data['birthday']    = $this->input->post('birthday');
            $data['sex']         = $this->input->post('sex');
            $data['address']     = $this->input->post('address');
            $data['phone']       = $this->input->post('phone');
            $data['email']       = $this->input->post('email');
            $data['password']    = $this->input->post('password');
            $this->db->insert('teacher', $data);
            $teacher_id = $this->db->insert_id();
            $this->handle_user_photo('teacher', 'teacher_id', $teacher_id, 'teacher_photo', 'teacher_photo');
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            $this->email_model->account_opening_email('teacher', $data['email']); //SEND EMAIL ACCOUNT OPENING EMAIL
            redirect(base_url() . 'index.php?admin/generateidcard/', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']        = $this->input->post('name');
            $data['birthday']    = $this->input->post('birthday');
            $data['sex']         = $this->input->post('sex');
            $data['address']     = $this->input->post('address');
            $data['phone']       = $this->input->post('phone');
            $data['email']       = $this->input->post('email');
            
            $this->db->where('teacher_id', $param2);
            $this->db->update('teacher', $data);
            $this->handle_user_photo('teacher', 'teacher_id', $param2, 'teacher_photo', 'teacher_photo');
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/generateidcard/', 'refresh');
        } else if ($param1 == 'personal_profile') {
            $page_data['personal_profile']   = true;
            $page_data['current_teacher_id'] = $param2;
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('teacher', array(
                'teacher_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('teacher_id', $param2);
            $this->db->delete('teacher');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/generateidcard/', 'refresh');
        }
        $page_data['teachers']   = $this->db->get('teacher')->result_array();
        $page_data['page_name']  = 'teacher_idcard';
        $page_data['page_title'] = get_phrase('teacher_idcard');
        $this->load->view('backend/index', $page_data);
    }
	
	
	 
	/****MANAGE LIBRARIANS*****/
    function librarian($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            $data['name']        = $this->input->post('name');
            $data['birthday']    = $this->input->post('birthday');
            $data['sex']         = $this->input->post('sex');
            $data['address']     = $this->input->post('address');
            $data['phone']       = $this->input->post('phone');
            $data['email']       = $this->input->post('email');
            $data['password']    = $this->input->post('password');
            $this->db->insert('librarian', $data);
            $librarian_id = $this->db->insert_id();
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/librarian_image/' . $librarian_id . '.jpg');
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            $this->email_model->account_opening_email('librarian', $data['email']); //SEND EMAIL ACCOUNT OPENING EMAIL
            redirect(base_url() . 'index.php?admin/librarian/', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']        = $this->input->post('name');
            $data['birthday']    = $this->input->post('birthday');
            $data['sex']         = $this->input->post('sex');
            $data['address']     = $this->input->post('address');
            $data['phone']       = $this->input->post('phone');
            $data['email']       = $this->input->post('email');
            
            $this->db->where('librarian_id', $param2);
            $this->db->update('librarian', $data);
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/librarian_image/' . $param2 . '.jpg');
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/librarian/', 'refresh');
        } else if ($param1 == 'personal_profile') {
            $page_data['personal_profile']   = true;
            $page_data['current_librarian_id'] = $param2;
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('librarian', array(
                'librarian_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('librarian_id', $param2);
            $this->db->delete('librarian');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/librarian/', 'refresh');
        }
        $page_data['librarians']   = $this->db->get('librarian')->result_array();
        $page_data['page_name']  = 'librarian';
        $page_data['page_title'] = get_phrase('manage_librarian');
        $this->load->view('backend/index', $page_data);
    }
	
	
	/****MANAGE LIBRARIANS ID CARDS*****/
    function librarian_id_card($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        
        $page_data['librarians']   = $this->db->get('librarian')->result_array();
        $page_data['page_name']  = 'librarian_id_card';
        $page_data['page_title'] = get_phrase('manage_librarian_ID_card');
        $this->load->view('backend/index', $page_data);
    }
	
	
	
	
	/****MANAGE BANNER *****/
    function banner($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            $data['b_namea']        = $this->input->post('b_namea');
            $data['b_nameb']    = $this->input->post('b_nameb');
			
            $this->db->insert('banner', $data);
            $banner_id = $this->db->insert_id();
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/banner_image/' . $banner_id . '.jpg');
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/banner', 'refresh');
        }
        if ($param1 == 'do_update') {
             $data['b_namea']        = $this->input->post('b_namea');
            $data['b_nameb']    = $this->input->post('b_nameb');
            
            $this->db->where('banner_id', $param2);
            $this->db->update('banner', $data);
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/banner_image/' . $param2 . '.jpg');
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/banner', 'refresh');
        } else if ($param1 == 'personal_profile') {
            $page_data['personal_profile']   = true;
            $page_data['current_banner_id'] = $param2;
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('banner', array(
                'banner_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('banner_id', $param2);
            $this->db->delete('banner');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/banner', 'refresh');
        }
        $page_data['banners']   = $this->db->get('banner')->result_array();
        $page_data['page_name']  = 'banner';
        $page_data['page_title'] = get_phrase('manage_banner');
        $this->load->view('backend/index', $page_data);
    }
	
	
	  // BOARD
    function academic_syllabus($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        if ($param1 == 'create') {
            $name = trim($this->input->post('name'));
            if ($name !== '' && $this->db->get_where('board', array('name' => $name))->num_rows() == 0) {
                $max_order = $this->db->select_max('sort_order')->get('board')->row()->sort_order;
                $this->db->insert('board', array(
                    'name'       => $name,
                    'sort_order' => ((int) $max_order) + 1
                ));
                $this->session->set_flashdata('flash_message', get_phrase('data_added_successfully'));
            }
            redirect(base_url() . 'index.php?admin/academic_syllabus', 'refresh');
        }

        if ($param1 == 'delete') {
            $this->db->where('board_id', $param2);
            $this->db->delete('board');
            $this->session->set_flashdata('flash_message', get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/academic_syllabus', 'refresh');
        }

        $page_data['page_name']  = 'academic_syllabus';
        $page_data['page_title'] = 'Board';
        $page_data['boards']     = $this->db->order_by('sort_order', 'asc')->order_by('name', 'asc')->get('board')->result_array();
        $this->load->view('backend/index', $page_data);
    }

    

	
	/****MANAGE ACCOUNTANT*****/
    function accountant($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            $data['name']        = $this->input->post('name');
            $data['birthday']    = $this->input->post('birthday');
            $data['sex']         = $this->input->post('sex');
            $data['address']     = $this->input->post('address');
            $data['phone']       = $this->input->post('phone');
            $data['email']       = $this->input->post('email');
            $data['password']    = $this->input->post('password');
            $this->db->insert('accountant', $data);
            $accountant_id = $this->db->insert_id();
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/accountant_image/' . $accountant_id . '.jpg');
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            $this->email_model->account_opening_email('accountant', $data['email']); //SEND EMAIL ACCOUNT OPENING EMAIL
            redirect(base_url() . 'index.php?admin/accountant/', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']        = $this->input->post('name');
            $data['birthday']    = $this->input->post('birthday');
            $data['sex']         = $this->input->post('sex');
            $data['address']     = $this->input->post('address');
            $data['phone']       = $this->input->post('phone');
            $data['email']       = $this->input->post('email');
            
            $this->db->where('accountant_id', $param2);
            $this->db->update('accountant', $data);
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/accountant_image/' . $param2 . '.jpg');
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/accountant/', 'refresh');
        } else if ($param1 == 'personal_profile') {
            $page_data['personal_profile']   = true;
            $page_data['current_accountant_id'] = $param2;
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('accountant', array(
                'accountant_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('accountant_id', $param2);
            $this->db->delete('accountant');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/accountant/', 'refresh');
        }
        $page_data['accountants']   = $this->db->get('accountant')->result_array();
        $page_data['page_name']  = 'accountant';
        $page_data['page_title'] = get_phrase('manage_accountant');
        $this->load->view('backend/index', $page_data);
    }
	
	
	/****MANAGE ACCOUNTANT*****/
    function accountant_id_card($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        
        $page_data['accountants']   = $this->db->get('accountant')->result_array();
        $page_data['page_name']  = 'accountant_id_card';
        $page_data['page_title'] = get_phrase('manage_accountant');
        $this->load->view('backend/index', $page_data);
    }
	
	
	/****MANAGE HOSTEL*****/
    function hostel($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            $data['name']        = $this->input->post('name');
            $data['birthday']    = $this->input->post('birthday');
            $data['sex']         = $this->input->post('sex');
            $data['address']     = $this->input->post('address');
            $data['phone']       = $this->input->post('phone');
            $data['email']       = $this->input->post('email');
            $data['password']    = $this->input->post('password');
            $this->db->insert('hostel', $data);
            $hostel_id = $this->db->insert_id();
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/hostel_image/' . $hostel_id . '.jpg');
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            $this->email_model->account_opening_email('hostel', $data['email']); //SEND EMAIL ACCOUNT OPENING EMAIL
            redirect(base_url() . 'index.php?admin/hostel/', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']        = $this->input->post('name');
            $data['birthday']    = $this->input->post('birthday');
            $data['sex']         = $this->input->post('sex');
            $data['address']     = $this->input->post('address');
            $data['phone']       = $this->input->post('phone');
            $data['email']       = $this->input->post('email');
            
            $this->db->where('hostel_id', $param2);
            $this->db->update('hostel', $data);
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/hostel_image/' . $param2 . '.jpg');
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/hostel/', 'refresh');
        } else if ($param1 == 'personal_profile') {
            $page_data['personal_profile']   = true;
            $page_data['current_hostel_id'] = $param2;
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('hostel', array(
                'hostel_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('hostel_id', $param2);
            $this->db->delete('hostel');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/hostel/', 'refresh');
        }
        $page_data['hostels']   = $this->db->get('hostel')->result_array();
        $page_data['page_name']  = 'hostel';
        $page_data['page_title'] = get_phrase('manage_hostel');
        $this->load->view('backend/index', $page_data);
    }
	
	
	
	/****MANAGE HOSTE ID CARD*****/
    function hostel_id_card($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
       
        $page_data['hostels']   = $this->db->get('hostel')->result_array();
        $page_data['page_name']  = 'hostel_id_card';
        $page_data['page_title'] = get_phrase('manage_hostel_id_card');
        $this->load->view('backend/index', $page_data);
    }
	
	
	
	// STUDENT PROMOTION
    function student_promotion($param1 = '' , $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect('login', 'refresh');

       
        $page_data['page_title']    = get_phrase('student_promotion');
        $page_data['page_name']  = 'student_promotion';
        $this->load->view('backend/index', $page_data);
    }

   
	
	
	/****MANAGE HOSTE ID CARD*****/
    function generate_hostel_id_card($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            $data['name']        = $this->input->post('name');
            $data['birthday']    = $this->input->post('birthday');
            $data['sex']         = $this->input->post('sex');
            $data['address']     = $this->input->post('address');
            $data['phone']       = $this->input->post('phone');
            $data['email']       = $this->input->post('email');
            $data['password']    = $this->input->post('password');
            $this->db->insert('hostel', $data);
            $hostel_id = $this->db->insert_id();
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/hostel_image/' . $hostel_id . '.jpg');
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            $this->email_model->account_opening_email('hostel', $data['email']); //SEND EMAIL ACCOUNT OPENING EMAIL
            redirect(base_url() . 'index.php?admin/generate_hostel_id_card/', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']        = $this->input->post('name');
            $data['birthday']    = $this->input->post('birthday');
            $data['sex']         = $this->input->post('sex');
            $data['address']     = $this->input->post('address');
            $data['phone']       = $this->input->post('phone');
            $data['email']       = $this->input->post('email');
            
            $this->db->where('hostel_id', $param2);
            $this->db->update('hostel', $data);
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/hostel_image/' . $param2 . '.jpg');
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/generate_hostel_id_card/', 'refresh');
        } else if ($param1 == 'personal_profile') {
            $page_data['personal_profile']   = true;
            $page_data['generate_hostel_id_card'] = $param2;
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('hostel', array(
                'hostel_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('hostel_id', $param2);
            $this->db->delete('hostel');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/generate_hostel_id_card/', 'refresh');
        }
        $page_data['hostels']   = $this->db->get('hostel')->result_array();
        $page_data['page_name']  = 'hostel_id_card';
        $page_data['page_title'] = get_phrase('generate_hostel_id_card');
        $this->load->view('backend/index', $page_data);
    }
	
	


    
    /****MANAGE SUBJECTS*****/
    function subject($param1 = '', $param2 = '' , $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            $data['name']       = $this->input->post('name');
            $data['class_id']   = $this->input->post('class_id');
            $data['teacher_id'] = $this->input->post('teacher_id');
            $this->db->insert('subject', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/subject/'.$data['class_id'], 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']       = $this->input->post('name');
            $data['class_id']   = $this->input->post('class_id');
            $data['teacher_id'] = $this->input->post('teacher_id');
            
            $this->db->where('subject_id', $param2);
            $this->db->update('subject', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/subject/'.$data['class_id'], 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('subject', array(
                'subject_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('subject_id', $param2);
            $this->db->delete('subject');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/subject/'.$param3, 'refresh');
        }
		 $page_data['class_id']   = $param1;
        $page_data['subjects']   = $this->db->get_where('subject' , array('class_id' => $param1))->result_array();
        $page_data['page_name']  = 'subject';
        $page_data['page_title'] = get_phrase('manage_subject');
        $this->load->view('backend/index', $page_data);
    }
    
    /****MANAGE CLASSES*****/
    function classes($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        if ($param1 == 'create') {
            $data['name']         = $this->input->post('name');
            $data['name_numeric'] = $this->input->post('name_numeric');
            $data['teacher_id']   = $this->input->post('teacher_id');

            $this->db->insert('class', $data);
            $this->session->set_flashdata('flash_message', get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/classes', 'refresh');
        }

        if ($param1 == 'do_update') {
            $data['name']         = $this->input->post('name');
            $data['name_numeric'] = $this->input->post('name_numeric');
            $data['teacher_id']   = $this->input->post('teacher_id');

            $this->db->where('class_id', $param2);
            $this->db->update('class', $data);
            $this->session->set_flashdata('flash_message', get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/classes', 'refresh');
        }

        if ($param1 == 'delete') {
            $this->db->where('class_id', $param2);
            $this->db->delete('class');
            $this->session->set_flashdata('flash_message', get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/classes', 'refresh');
        }
        
        $page_data['classes']    = $this->db->get('class')->result_array();
        $page_data['page_name']  = 'class';
        $page_data['page_title'] = 'Manage Standard';
        $this->load->view('backend/index', $page_data);
    }
	
	
	
	/****MANAGE SESSION HERE *****/
    function session($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            $data['name']         = $this->input->post('name');
            $this->db->insert('session', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/session', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']         = $this->input->post('name');
            
            $this->db->where('session_id', $param2);
            $this->db->update('session', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/session', 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('session', array(
                'session_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('session_id', $param2);
            $this->db->delete('session');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/session', 'refresh');
        }
        $page_data['sessions']    = $this->db->get('session')->result_array();
        $page_data['page_name']  = 'session';
        $page_data['page_title'] = 'Manage Academic Year';
        $this->load->view('backend/index', $page_data);
    }
	
	
	
	/****MANAGE HELPFUL LINK*****/
    function help_link($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            
			$data['title']         = $this->input->post('title');
            $data['link'] = $this->input->post('link');
            
            $this->db->insert('help_link', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/help_link', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['title']         = $this->input->post('title');
            $data['link'] = $this->input->post('link');
            
            $this->db->where('helplink_id', $param2);
            $this->db->update('help_link', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/help_link', 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('help_link', array(
                'helplink_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('helplink_id', $param2);
            $this->db->delete('help_link');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/help_link', 'refresh');
        }
        $page_data['help_links']    = $this->db->get('help_link')->result_array();
        $page_data['page_name']  = 'help_link';
        $page_data['page_title'] = get_phrase('manage_help_link');
        $this->load->view('backend/index', $page_data);
    }
	
	
	
	/****MANAGE CLUB*****/
    function club($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            
			$data['club_name']         = $this->input->post('club_name');
            $data['desc'] = $this->input->post('desc');
            
            $this->db->insert('club', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/club', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['club_name']         = $this->input->post('club_name');
            $data['desc'] = $this->input->post('desc');
            
            $this->db->where('club_id', $param2);
            $this->db->update('club', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/club', 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('club', array(
                'club_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('club_id', $param2);
            $this->db->delete('club');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/club', 'refresh');
        }
        $page_data['club']    = $this->db->get('club')->result_array();
        $page_data['page_name']  = 'club';
        $page_data['page_title'] = get_phrase('manage_club');
        $this->load->view('backend/index', $page_data);
    }
	
	
	
	/****MANAGE HELP DESK*****/
    function help_desk($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            
			$data['name']         = $this->input->post('name');
            $data['purpose'] = $this->input->post('purpose');
            $data['content'] = $this->input->post('content');
            
            $this->db->insert('help_desk', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/help_desk', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']         = $this->input->post('name');
            $data['purpose'] = $this->input->post('purpose');
            $data['content'] = $this->input->post('content');
            
            $this->db->where('helpdesk_id', $param2);
            $this->db->update('help_desk', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/help_desk', 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('help_desk', array(
                'helpdesk_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('helpdesk_id', $param2);
            $this->db->delete('help_desk');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/help_desk', 'refresh');
        }
        $page_data['help_desk']    = $this->db->get('help_desk')->result_array();
        $page_data['page_name']  = 'help_desk';
        $page_data['page_title'] = get_phrase('manage_help_desk');
        $this->load->view('backend/index', $page_data);
    }
	
	
	/****MANAGE HOLIDAY*****/
    function holiday($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            
			$data['title']         = $this->input->post('title');
            $data['holiday'] = $this->input->post('holiday');
            $data['date'] = $this->input->post('date');
            
            $this->db->insert('holiday', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/holiday', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['title']         = $this->input->post('title');
            $data['holiday'] = $this->input->post('holiday');
            $data['date'] = $this->input->post('date');
            
            $this->db->where('holiday_id', $param2);
            $this->db->update('holiday', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/holiday', 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('holiday', array(
                'holiday_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('holiday_id', $param2);
            $this->db->delete('holiday');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/holiday', 'refresh');
        }
        $page_data['holiday']    = $this->db->get('holiday')->result_array();
        $page_data['page_name']  = 'holiday';
        $page_data['page_title'] = get_phrase('manage_holiday');
        $this->load->view('backend/index', $page_data);
    }
	
	
	
	
	/****MANAGE circular*****/
    function circular($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            
			$data['subject']        = $this->input->post('subject');
            $data['ref'] 			= $this->input->post('ref');
            $data['content']	 	= $this->input->post('content');
            $data['date'] 			= $this->input->post('date');
            
            $this->db->insert('circular', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/circular', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['subject']        = $this->input->post('subject');
            $data['ref'] 			= $this->input->post('ref');
            $data['content']	 	= $this->input->post('content');
            $data['date'] 			= $this->input->post('date');
            
            $this->db->where('circular_id', $param2);
            $this->db->update('circular', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/circular', 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('circular', array(
                'circular_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('circular_id', $param2);
            $this->db->delete('circular');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/circular', 'refresh');
        }
        $page_data['circular']    = $this->db->get('circular')->result_array();
        $page_data['page_name']  = 'circular';
        $page_data['page_title'] = get_phrase('manage_circular');
        $this->load->view('backend/index', $page_data);
    }
	
	
	/****MANAGE TASK MANAGER*****/
    function task_manager($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
       
        $page_data['task_managers']    = $this->db->get('task_manager')->result_array();
        $page_data['page_name']  = 'task_manager';
        $page_data['page_title'] = get_phrase('manage_task_manager');
        $this->load->view('backend/index', $page_data);
    }
	
	
	
	
	/****MANAGE TODAY'S THOUGHT*****/
    function todays_thought($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            
			$data['thought']         = $this->input->post('thought');
           
            
            $this->db->insert('todays_thought', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/todays_thought', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['thought'] = $this->input->post('thought');
            
            $this->db->where('tthought_id', $param2);
            $this->db->update('todays_thought', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/todays_thought', 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('tthought_id', array(
                'tthought_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('tthought_id', $param2);
            $this->db->delete('todays_thought');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/todays_thought', 'refresh');
        }
        $page_data['todays_thought']    = $this->db->get('todays_thought')->result_array();
        $page_data['page_name']  = 'todays_thought';
        $page_data['page_title'] = get_phrase('manage_todays_thought');
        $this->load->view('backend/index', $page_data);
    }
	
	
	
	 /****MANAGE ENQUIRY SETTINGS*****/
    function enquiry_setting($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            $data['category']         = $this->input->post('category');
            $data['purpose'] = $this->input->post('purpose');
            $data['whom']   = $this->input->post('whom');
            $this->db->insert('enquiry_category', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/enquiry_setting/', 'refresh');
        }
		
		if ($param1 == 'do_update') {
           $data['category']         = $this->input->post('category');
            $data['purpose'] = $this->input->post('purpose');
            $data['whom']   = $this->input->post('whom');
            
            $this->db->where('enquirycat_id', $param2);
            $this->db->update('enquiry_category', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/enquiry_setting/', 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('enquiry_category', array(
                'class_id' => $param2
            ))->result_array();
        }
		
		
        if ($param1 == 'delete') {
            $this->db->where('enquirycat_id', $param2);
            $this->db->delete('enquiry_category');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/enquiry_setting/', 'refresh');
        }
        $page_data['enquiry_setting']    = $this->db->get('enquiry_category')->result_array();
        $page_data['page_name']  = 'enquiry_setting';
        $page_data['page_title'] = get_phrase('manage_enquiry_category');
        $this->load->view('backend/index', $page_data);
    }
	
	
	
	
	
	
	
		 /****MANAGE ALL ADMISSION ENQUIRIES*****/
    function enquiry($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_enquiry_columns();

        // Person who is logged in — used to stamp created_by / activities.
        $created_by = $this->session->userdata('name');
        if (empty($created_by)) $created_by = 'Admin';

        if ($param1 == 'create') {
            $data['session_name']   = $this->input->post('session_name');
            $data['enquiry_no']     = $this->input->post('enquiry_no');
            $data['enquiry_date']   = $this->input->post('enquiry_date');
            $data['enquiry_for']    = $this->input->post('enquiry_for');
            $data['name']           = $this->input->post('name');
            $data['course']         = $this->input->post('course');
            $data['source']         = $this->input->post('source');
            $data['source_student'] = $this->input->post('source_student');
            $data['gender']         = $this->input->post('gender');
            $data['address']        = $this->input->post('address');
            $data['mobile']         = $this->input->post('mobile');
            $data['assign_to']      = $this->input->post('assign_to') ?: null;
            $data['handled_by']     = $this->input->post('handled_by') ?: null;
            $data['status']         = 'in_progress';
            $data['created_by']     = $created_by;
            $this->db->insert('enquiry', $data);
            $enquiry_id = $this->db->insert_id();

            $this->db->insert('enquiry_activity', array(
                'enquiry_id' => $enquiry_id,
                'status'     => 'in_progress',
                'note'       => 'Enquiry created',
                'created_by' => $created_by,
                'created_at' => date('Y-m-d H:i:s'),
            ));

            $this->session->set_flashdata('flash_message', get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/enquiry/', 'refresh');
        }

        if ($param1 == 'do_update') {
            $data['session_name']   = $this->input->post('session_name');
            $data['enquiry_no']     = $this->input->post('enquiry_no');
            $data['enquiry_date']   = $this->input->post('enquiry_date');
            $data['enquiry_for']    = $this->input->post('enquiry_for');
            $data['name']           = $this->input->post('name');
            $data['course']         = $this->input->post('course');
            $data['source']         = $this->input->post('source');
            $data['source_student'] = $this->input->post('source_student');
            $data['gender']         = $this->input->post('gender');
            $data['address']        = $this->input->post('address');
            $data['mobile']         = $this->input->post('mobile');
            $data['assign_to']      = $this->input->post('assign_to') ?: null;
            $data['handled_by']     = $this->input->post('handled_by') ?: null;

            $this->db->where('enquiry_id', $param2);
            $this->db->update('enquiry', $data);
            $this->session->set_flashdata('flash_message', get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/enquiry/', 'refresh');
        }

        // Save follow-up status + remark and log it as an activity.
        if ($param1 == 'save_status') {
            $status = $this->input->post('status');
            $remark = $this->input->post('remark');
            $this->db->where('enquiry_id', $param2);
            $this->db->update('enquiry', array(
                'status'     => $status,
                'remark'     => $remark,
                'assign_to'  => $this->input->post('assign_to') ?: null,
                'handled_by' => $this->input->post('handled_by') ?: null,
            ));
            $this->db->insert('enquiry_activity', array(
                'enquiry_id' => $param2,
                'status'     => $status,
                'note'       => $remark ?: ('Status changed to ' . $status),
                'created_by' => $created_by,
                'created_at' => date('Y-m-d H:i:s'),
            ));
            $this->session->set_flashdata('flash_message', get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/enquiry_follow/' . $param2, 'refresh');
        }

        // Add a follow-up activity note without changing the status.
        if ($param1 == 'add_activity') {
            $this->db->insert('enquiry_activity', array(
                'enquiry_id' => $param2,
                'status'     => $this->input->post('status'),
                'note'       => $this->input->post('note'),
                'created_by' => $created_by,
                'created_at' => date('Y-m-d H:i:s'),
            ));
            $this->session->set_flashdata('flash_message', get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/enquiry_follow/' . $param2, 'refresh');
        }

        // Nominate / convert a joined enquiry into a student admission.
        if ($param1 == 'nominate') {
            $this->db->where('enquiry_id', $param2);
            $this->db->update('enquiry', array('status' => 'joined'));
            $this->db->insert('enquiry_activity', array(
                'enquiry_id' => $param2,
                'status'     => 'joined',
                'note'       => 'Nominated for admission',
                'created_by' => $created_by,
                'created_at' => date('Y-m-d H:i:s'),
            ));
            redirect(base_url() . 'index.php?admin/student_add', 'refresh');
        }

        if ($param1 == 'delete') {
            $this->db->where('enquiry_id', $param2);
            $this->db->delete('enquiry');
            $this->db->where('enquiry_id', $param2);
            $this->db->delete('enquiry_activity');
            $this->session->set_flashdata('flash_message', get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/enquiry/', 'refresh');
        }

        $this->db->order_by('enquiry_id', 'desc');
        $page_data['enquiries']  = $this->db->get('enquiry')->result_array();
        $page_data['page_name']  = 'enquiry';
        $page_data['page_title'] = get_phrase('manage_enquiries');
        $this->load->view('backend/index', $page_data);
    }

    /****ADD / EDIT AN ENQUIRY (full-page form)*****/
    function enquiry_add($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_enquiry_columns();

        if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('enquiry', array('enquiry_id' => $param2))->row_array();
        }

        // Suggest the next enquiry number (ENQ-0001 style).
        $last = $this->db->select('enquiry_id')->order_by('enquiry_id', 'desc')->limit(1)->get('enquiry')->row();
        $page_data['next_no']    = sms_next_enquiry_no($last ? $last->enquiry_id : 0);
        $page_data['sessions']   = $this->db->get('session')->result_array();
        $page_data['courses']    = $this->db->table_exists('course') ? $this->db->get('course')->result_array() : array();
        $page_data['staff']      = $this->db->get('teacher')->result_array();
        $page_data['page_name']  = 'enquiry_add';
        $page_data['page_title'] = get_phrase('add_enquiry');
        $this->load->view('backend/index', $page_data);
    }

    /****ENQUIRY FOLLOW-UP / STATUS TRACKING + ACTIVITY LOG*****/
    function enquiry_follow($enquiry_id = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_enquiry_columns();

        $page_data['enquiry'] = $this->db->get_where('enquiry', array('enquiry_id' => $enquiry_id))->row_array();
        if (empty($page_data['enquiry'])) {
            redirect(base_url() . 'index.php?admin/enquiry/', 'refresh');
        }
        $this->db->order_by('activity_id', 'desc');
        $page_data['activities'] = $this->db->get_where('enquiry_activity', array('enquiry_id' => $enquiry_id))->result_array();
        $page_data['staff']      = $this->db->get('teacher')->result_array();
        $page_data['page_name']  = 'enquiry_follow';
        $page_data['page_title'] = get_phrase('enquiry_follow_up');
        $this->load->view('backend/index', $page_data);
    }

    /****BULK ENQUIRY IMPORT (xls/csv)*****/
    function enquiry_bulk_add($param1 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_enquiry_columns();

        if ($param1 == 'template') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="enquiry_bulk_template.csv"');
            $out = fopen('php://output', 'w');
            fputcsv($out, array('enquiry_no', 'enquiry_date', 'enquiry_for', 'name', 'course', 'source', 'gender', 'mobile', 'address'));
            fputcsv($out, array('ENQ-0001', date('Y-m-d'), 'Class 5', 'John Doe', 'Regular', 'Google search', 'Male', '9876543210', 'Sample address'));
            fclose($out);
            return;
        }

        if ($param1 == 'import_excel') {
            if (empty($_FILES['userfile']['tmp_name'])) {
                $this->session->set_flashdata('error', 'Please choose a file.');
                redirect(base_url() . 'index.php?admin/enquiry_bulk_add', 'refresh');
            }

            $orig_name = strtolower($_FILES['userfile']['name']);
            $is_csv    = (substr($orig_name, -4) === '.csv') ||
                         (isset($_FILES['userfile']['type']) && stripos($_FILES['userfile']['type'], 'csv') !== false);
            $ext    = $is_csv ? 'csv' : 'xlsx';
            $target = 'uploads/enquiry_import.' . $ext;
            move_uploaded_file($_FILES['userfile']['tmp_name'], $target);

            $rows = array();
            if ($is_csv) {
                if (($h = fopen($target, 'r')) !== false) {
                    while (($line = fgetcsv($h)) !== false) $rows[] = $line;
                    fclose($h);
                }
            } else {
                include_once 'simplexlsx.class.php';
                $xlsx = new SimpleXLSX($target);
                $rows = $xlsx->rows();
            }

            if (empty($rows)) {
                $this->session->set_flashdata('error', 'No rows found in the uploaded file.');
                redirect(base_url() . 'index.php?admin/enquiry_bulk_add', 'refresh');
            }

            // First non-empty row is the header.
            $header_row = null; $first_data_index = 0;
            foreach ($rows as $idx => $r) {
                foreach ($r as $cell) { if (trim((string)$cell) !== '') { $header_row = $r; $first_data_index = $idx + 1; break 2; } }
            }
            if (!$header_row) {
                $this->session->set_flashdata('error', 'Could not find a header row.');
                redirect(base_url() . 'index.php?admin/enquiry_bulk_add', 'refresh');
            }

            $header_map = sms_build_header_map($header_row);

            $created_by = $this->session->userdata('name') ?: 'Admin';
            $session_name = $this->input->post('session_name');
            $imported = 0;

            for ($idx = $first_data_index; $idx < count($rows); $idx++) {
                $r = $rows[$idx];
                $row_has_value = false;
                foreach ($r as $cell) { if (trim((string)$cell) !== '') { $row_has_value = true; break; } }
                if (!$row_has_value) continue;

                $cell = function ($keys) use ($r, $header_map) {
                    return sms_cell_value($r, $header_map, $keys);
                };

                $this->db->insert('enquiry', array(
                    'session_name' => $session_name,
                    'enquiry_no'   => $cell('enquiry_no'),
                    'enquiry_date' => $cell('enquiry_date') ?: date('Y-m-d'),
                    'enquiry_for'  => $cell('enquiry_for'),
                    'name'         => $cell('name'),
                    'course'       => $cell('course'),
                    'source'       => $cell('source'),
                    'gender'       => $cell('gender'),
                    'mobile'       => $cell(array('mobile', 'contact', 'contact_no', 'phone')),
                    'address'      => $cell('address'),
                    'status'       => 'in_progress',
                    'created_by'   => $created_by,
                ));
                $imported++;
            }

            $this->session->set_flashdata('flash_message', $imported . ' enquiries imported successfully.');
            redirect(base_url() . 'index.php?admin/enquiry/', 'refresh');
        }

        $page_data['sessions']   = $this->db->get('session')->result_array();
        $page_data['page_name']  = 'enquiry_bulk_add';
        $page_data['page_title'] = get_phrase('bulk_enquiry_import');
        $this->load->view('backend/index', $page_data);
    }

    /****MANAGE COURSES*****/
    function course($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_course_tables();
        $created_by = $this->session->userdata('name') ?: 'Admin';

        if ($param1 == 'create' || $param1 == 'do_update') {
            $class_id   = $this->input->post('class_id') ?: null;
            $class_name = '';
            if ($class_id) {
                $class_row  = $this->db->get_where('class', array('class_id' => $class_id))->row();
                $class_name = $class_row ? $class_row->name : '';
            }
            $data = array(
                'name'          => $this->input->post('name'),
                'session_name'  => $this->input->post('session_name'),
                'class_id'      => $class_id,
                'standard_name' => $this->input->post('standard_name') ?: $class_name,
                'total_fees'    => $this->input->post('total_fees') ?: 0,
                'installments'  => $this->input->post('installments') ?: 1,
                'description'   => $this->input->post('description'),
            );

            if ($param1 == 'create') {
                $data['created_by'] = $created_by;
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('course', $data);
                $course_id = $this->db->insert_id();
                $this->generate_course_installments($course_id, $data['total_fees'], $data['installments']);
                $this->session->set_flashdata('flash_message', get_phrase('data_added_successfully'));
                redirect(base_url() . 'index.php?admin/course_view/' . $course_id, 'refresh');
            } else {
                $this->db->where('course_id', $param2);
                $this->db->update('course', $data);
                // Rebuild installments to match the new fee/installment count.
                $this->db->where('course_id', $param2)->delete('course_installment');
                $this->generate_course_installments($param2, $data['total_fees'], $data['installments']);
                $this->session->set_flashdata('flash_message', get_phrase('data_updated'));
                redirect(base_url() . 'index.php?admin/course_view/' . $param2, 'refresh');
            }
        }

        if ($param1 == 'delete') {
            $this->db->where('course_id', $param2)->delete('course');
            $this->db->where('course_id', $param2)->delete('course_subject');
            $this->db->where('course_id', $param2)->delete('course_installment');
            $this->session->set_flashdata('flash_message', get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/course/', 'refresh');
        }

        $this->db->order_by('course_id', 'desc');
        $page_data['courses']    = $this->db->get('course')->result_array();
        $page_data['page_name']  = 'course';
        $page_data['page_title'] = get_phrase('manage_courses');
        $this->load->view('backend/index', $page_data);
    }

    /**
     * Splits a course's total fee into N equal monthly installments.
     */
    private function generate_course_installments($course_id, $total_fees, $count)
    {
        $amounts = sms_split_installments($total_fees, $count);
        foreach ($amounts as $idx => $amount) {
            $this->db->insert('course_installment', array(
                'course_id' => $course_id,
                'title'     => 'Installment ' . ($idx + 1),
                'amount'    => $amount,
                'due_date'  => null,
            ));
        }
    }

    /****ADD / EDIT COURSE (full-page form)*****/
    function course_add($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_course_tables();

        if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('course', array('course_id' => $param2))->row_array();
        }
        $page_data['sessions']   = $this->db->get('session')->result_array();
        $page_data['classes']    = $this->db->get('class')->result_array();
        $page_data['page_name']  = 'course_add';
        $page_data['page_title'] = get_phrase('add_course');
        $this->load->view('backend/index', $page_data);
    }

    /****VIEW COURSE: fees, installments, subjects*****/
    function course_view($course_id = '', $param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_course_tables();

        if ($param1 == 'add_subject') {
            $this->db->insert('course_subject', array(
                'course_id'    => $course_id,
                'subject_name' => $this->input->post('subject_name'),
                'subject_code' => $this->input->post('subject_code'),
            ));
            $this->session->set_flashdata('flash_message', get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/course_view/' . $course_id, 'refresh');
        }

        if ($param1 == 'delete_subject') {
            $this->db->where('csubject_id', $param2)->delete('course_subject');
            $this->session->set_flashdata('flash_message', get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/course_view/' . $course_id, 'refresh');
        }

        if ($param1 == 'save_installment') {
            $this->db->where('installment_id', $param2)->update('course_installment', array(
                'title'    => $this->input->post('title'),
                'amount'   => $this->input->post('amount') ?: 0,
                'due_date' => $this->input->post('due_date') ?: null,
            ));
            $this->session->set_flashdata('flash_message', get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/course_view/' . $course_id, 'refresh');
        }

        $page_data['course'] = $this->db->get_where('course', array('course_id' => $course_id))->row_array();
        if (empty($page_data['course'])) {
            redirect(base_url() . 'index.php?admin/course/', 'refresh');
        }
        $page_data['subjects']     = $this->db->get_where('course_subject', array('course_id' => $course_id))->result_array();
        $this->db->order_by('installment_id', 'asc');
        $page_data['installments'] = $this->db->get_where('course_installment', array('course_id' => $course_id))->result_array();
        $page_data['page_name']  = 'course_view';
        $page_data['page_title'] = get_phrase('course_details');
        $this->load->view('backend/index', $page_data);
    }


    /****MANAGE SECTIONS*****/
    function section($class_id = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_teacher_timetable_columns();

        $page_data['page_name']  = 'section';
        $page_data['page_title'] = 'Manage Teachers Time Table';
        $page_data['class_id']   = $class_id;
        $this->load->view('backend/index', $page_data);    
    }

    function sections($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_teacher_timetable_columns();

        $days = $this->input->post('days');
        if (!is_array($days)) {
            $days = array();
        }

        $data = array(
            'name'       => $this->input->post('name'),
            'nick_name'  => $this->input->post('nick_name'),
            'class_id'   => $this->input->post('class_id'),
            'teacher_id' => $this->input->post('teacher_id'),
            'days'       => implode(',', $days),
            'start_time' => $this->input->post('start_time'),
            'end_time'   => $this->input->post('end_time'),
            'session_name'     => $this->input->post('session_name'),
            'revision_section' => $this->input->post('revision_section'),
            'lecture_subject'  => $this->input->post('lecture_subject')
        );

        if ($param1 == 'create' || $param1 == 'edit') {
            $this->db->where('class_id', $data['class_id']);
            $this->db->where('teacher_id', $data['teacher_id']);
            if ($param1 == 'edit') {
                $this->db->where('section_id !=', $param2);
            }
            $duplicate = $this->db->get('section')->num_rows();

            if ($duplicate > 0) {
                $this->session->set_flashdata('error_message', 'This teacher is already assigned to the selected class.');
                redirect(base_url() . 'index.php?admin/section', 'refresh');
            }
        }

        if ($param1 == 'create') {
            $this->db->insert('section', $data);
            $this->session->set_flashdata('flash_message', get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/section', 'refresh');
        }

        if ($param1 == 'edit') {
            $this->db->where('section_id', $param2);
            $this->db->update('section', $data);
            $this->session->set_flashdata('flash_message', get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/section', 'refresh');
        }

        if ($param1 == 'delete') {
            $this->db->where('section_id', $param2);
            $this->db->delete('section');
            $this->session->set_flashdata('flash_message', get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/section', 'refresh');
        }

        redirect(base_url() . 'index.php?admin/section', 'refresh');
    }

    /**
     * Sends today's lecture reminder to every teacher with a section scheduled
     * for the current day. One digest email per teacher (lists all of today's
     * lectures), BCC'd to the configured system_email.
     */
    function send_timetable_reminders()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_teacher_timetable_columns();
        $this->load->model('email_model');

        $today_day = date('l'); // Monday..Sunday

        // Pull all sections that include today, joined with class + teacher
        $this->db->select('s.section_id, s.name section_name, s.nick_name, s.days, s.start_time, s.end_time, '
                        . 'c.name class_name, t.teacher_id, t.name teacher_name, t.email teacher_email');
        $this->db->from('section s');
        $this->db->join('class c',   'c.class_id   = s.class_id',   'left');
        $this->db->join('teacher t', 't.teacher_id = s.teacher_id', 'left');
        $this->db->where("FIND_IN_SET('" . $today_day . "', s.days) >", 0, false);
        $this->db->order_by('t.teacher_id, s.start_time');
        $rows = $this->db->get()->result_array();

        // Group by teacher
        $by_teacher = array();
        foreach ($rows as $r) {
            if (empty($r['teacher_id']) || empty($r['teacher_email'])) continue;
            $tid = (int)$r['teacher_id'];
            if (!isset($by_teacher[$tid])) {
                $by_teacher[$tid] = array(
                    'teacher' => array('name' => $r['teacher_name'], 'email' => $r['teacher_email']),
                    'items'   => array(),
                );
            }
            $section_label = $r['section_name'];
            if (!empty($r['nick_name'])) $section_label .= ' (' . $r['nick_name'] . ')';
            $by_teacher[$tid]['items'][] = array(
                'class_name'   => $r['class_name']   ?: '-',
                'section_name' => $section_label,
                'start_time'   => $r['start_time'],
                'end_time'     => $r['end_time'],
            );
        }

        // School details + BCC target
        $school = array();
        $name_row    = $this->db->get_where('settings', array('type' => 'system_name'))->row();
        $school['name']    = $name_row ? $name_row->description : 'School';
        $address_row = $this->db->get_where('settings', array('type' => 'address'))->row();
        $school['address'] = $address_row ? $address_row->description : '';
        $bcc_row     = $this->db->get_where('settings', array('type' => 'system_email'))->row();
        $bcc         = $bcc_row ? trim($bcc_row->description) : '';

        $sent = 0; $failed = 0; $skipped = 0;
        foreach ($by_teacher as $bucket) {
            if (empty($bucket['teacher']['email']) || !filter_var($bucket['teacher']['email'], FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                continue;
            }
            $ok = $this->email_model->timetable_reminder_email(
                $bucket['teacher'],
                $bucket['items'],
                $school,
                $bcc ?: null
            );
            $ok ? $sent++ : $failed++;
        }

        $msg = "Today is {$today_day}. Reminders sent: {$sent}";
        if ($failed)  $msg .= ", failed: {$failed}";
        if ($skipped) $msg .= ", skipped (missing/invalid email): {$skipped}";
        if (empty($by_teacher)) $msg = "No teachers have lectures scheduled for today ({$today_day}).";

        $this->session->set_flashdata(
            $sent > 0 || empty($by_teacher) ? 'flash_message' : 'error_message',
            $msg
        );
        redirect(base_url() . 'index.php?admin/section', 'refresh');
    }

 /*********MANAGE STUDY MATERIAL************/
    function study_material($task = "", $document_id = "")
    {
        if ($this->session->userdata('admin_login') != 1)
        {
            $this->session->set_userdata('last_page' , current_url());
            redirect(base_url(), 'refresh');
        }
                
       
        $data['study_material_info']    = $this->crud_model->select_study_material_info();
        $data['page_name']              = 'study_material';
        $data['page_title']             = get_phrase('study_material');
        $this->load->view('backend/index', $data);
    }
	

    /****MANAGE EXAMS*****/
    /****MANAGE WRITTEN (CLASSIC) EXAMS*****/
    function exam($param1 = '', $param2 = '', $param3 = '')
    {
        $this->cbt_guard();
        $form = function () {
            $classes = array_values(array_filter(array_map('intval', (array)$this->input->post('class_ids'))));
            return array(
                'name'         => trim((string)$this->input->post('name')),
                'exam_date'    => sms_parse_exam_date($this->input->post('exam_date')),
                'total_marks'  => (int)$this->input->post('total_marks'),
                'pass_percent' => min(100, max(0, (int)$this->input->post('pass_percent'))),
                'class_ids'    => implode(',', $classes),
                'comment'      => trim((string)$this->input->post('comment')),
            );
        };
        $invalid = function ($d) {
            if ($d['name'] === '')       return get_phrase('exam_name_is_required');
            if ($d['exam_date'] === '')  return get_phrase('exam_date_is_required');
            if ($d['total_marks'] < 1)   return get_phrase('total_marks_must_be_more_than_0');
            if ($d['class_ids'] === '')  return get_phrase('select_at_least_one_class');
            return null;
        };
        if ($param1 == 'create' || ($param1 == 'edit' && $param2 == 'do_update')) {
            $d = $form();
            if ($e = $invalid($d)) $this->cbt_back('exam', $e, true);
            $d['date'] = date('m/d/Y', strtotime($d['exam_date']));   // legacy text column used by older screens
            if ($param1 == 'create') {
                $this->db->insert('exam', $d);
                $id = $this->db->insert_id();
                $msg = get_phrase('data_added_successfully') . '.';
                if ($this->input->post('notify')) {
                    $this->load->model('email_model');
                    $msg .= $this->email_summary($this->email_model->notify_classic_scheduled($id, explode(',', $d['class_ids'])));
                }
                $this->cbt_back('exam', $msg);
            }
            $this->db->where('exam_id', (int)$param3)->update('exam', $d);
            $this->cbt_back('exam', get_phrase('data_updated'));
        }
        if ($param1 == 'notify') {
            $exam = $this->exam_model->classic_exam($param2);
            if (!$exam) $this->cbt_back('exam', get_phrase('exam_not_found'), true);
            $this->load->model('email_model');
            $counts = $this->email_model->notify_classic_scheduled($param2, array_filter(explode(',', $exam['class_ids'])));
            $this->cbt_back('exam', get_phrase('exam_schedule_emailed') . '.' . $this->email_summary($counts));
        }
        if ($param1 == 'delete') {
            if ($this->db->where('exam_id', (int)$param2)->where('mark_obtained IS NOT NULL', null, false)->count_all_results('mark') > 0)
                $this->cbt_back('exam', get_phrase('exam_has_marks_and_cannot_be_deleted'), true);
            $this->db->where('exam_id', (int)$param2)->delete('mark');
            $this->db->where('exam_id', (int)$param2)->delete('exam');
            $this->cbt_back('exam', get_phrase('data_deleted'));
        }
        $page_data['edit']       = $param1 == 'edit' ? $this->exam_model->classic_exam($param2) : null;
        $page_data['exams']      = $this->exam_model->classic_exams();
        $page_data['classes']    = $this->db->order_by('name', 'ASC')->get('class')->result_array();
        $page_data['page_name']  = 'exam';
        $page_data['page_title'] = get_phrase('written_exams');
        $this->load->view('backend/index', $page_data);
    }

	
	/****MANAGE NEWS*****/
    function news($param1 = '', $param2 = '' , $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            $data['news_title']    = $this->input->post('news_title');
            $data['date']    = $this->input->post('date');
            $data['news_content'] = $this->input->post('news_content');
            $this->db->insert('news', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/news/', 'refresh');
        }
        if ($param1 == 'edit' && $param2 == 'do_update') {
            $data['news_title']    = $this->input->post('news_title');
            $data['date']    = $this->input->post('date');
            $data['news_content'] = $this->input->post('news_content');
            
            $this->db->where('news_id', $param3);
            $this->db->update('news', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/news/', 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('news', array(
                'news_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('news_id', $param2);
            $this->db->delete('news');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/news/', 'refresh');
        }
        $page_data['news']      = $this->db->get('news')->result_array();
        $page_data['page_name']  = 'news';
        $page_data['page_title'] = get_phrase('manage_news');
        $this->load->view('backend/index', $page_data);
    }


 /**********MANAGE AASIGNMENTS *******************/
    function assignment($param1 = '', $param2 = '' , $param3 = '')
    {
       if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
       
        $page_data['page_name']  = 'assignment';
        $page_data['page_title'] = get_phrase('manage_assignment');
        $page_data['assignments']  = $this->db->get('assignment')->result_array();
        $this->load->view('backend/index', $page_data);
    }
	
	
	
	/**********MANAGE AASIGNMENTS *******************/
    function examquestion($param1 = '', $param2 = '' , $param3 = '')
    {
       if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
       
        $page_data['page_name']  = 'examquestion';
        $page_data['page_title'] = get_phrase('manage_exam_questions');
        $page_data['examquestions']  = $this->db->get('examquestion')->result_array();
        $this->load->view('backend/index', $page_data);
    }
	
	
	
	/**********MANAGE LOAN *******************/
    function loan_applicant($param1 = '', $param2 = '' , $param3 = '')
    {
       if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
		
		    $data['staff_name']     	= $this->input->post('staff_name');
            $data['amount']        	 	= $this->input->post('amount');
            $data['purpose']    	  	= $this->input->post('purpose');
            $data['l_duration']       	= $this->input->post('l_duration');
			
            $data['mop']       			= $this->input->post('mop');
			
			$data['g_name']     		= $this->input->post('g_name');
            $data['g_relationship']     = $this->input->post('g_relationship');
            $data['g_number']     		= $this->input->post('g_number');
			
			$data['g_address']     		= $this->input->post('g_address');
            $data['g_country']         	= $this->input->post('g_country');
            $data['c_name']     		= $this->input->post('c_name');
			
			$data['c_type']     		= $this->input->post('c_type');
            $data['model']         		= $this->input->post('model');
            $data['make']     			= $this->input->post('make');
			
			$data['serial_number']     	= $this->input->post('serial_number');
            $data['value']   			= $this->input->post('value');
            $data['condition']     		= $this->input->post('condition');
			$data['date']         		= $this->input->post('date');
            $data['status']     		= $this->input->post('status');
			
            $this->db->insert('loan', $data);
            $assignment_id = $this->db->insert_id();
			
            move_uploaded_file($_FILES["file_name"]["tmp_name"], "uploads/loan_applicant/" . $_FILES["file_name"]["name"]);
			$this->session->set_flashdata('flash_message' , get_phrase('loan_application_submitted_successfully'));
            redirect(base_url() . 'index.php?admin/loan_applicant' , 'refresh');
        }
		if ($param1 == 'do_update') {
             $data['staff_name']     	= $this->input->post('staff_name');
            $data['amount']        	 	= $this->input->post('amount');
            $data['purpose']    	  	= $this->input->post('purpose');
            $data['l_duration']       	= $this->input->post('l_duration');
			
            $data['mop']       			= $this->input->post('mop');
			
			$data['g_name']     		= $this->input->post('g_name');
            $data['g_relationship']     = $this->input->post('g_relationship');
            $data['g_number']     		= $this->input->post('g_number');
			
			$data['g_address']     		= $this->input->post('g_address');
            $data['g_country']         	= $this->input->post('g_country');
            $data['c_name']     		= $this->input->post('c_name');
			
			$data['c_type']     		= $this->input->post('c_type');
            $data['model']         		= $this->input->post('model');
            $data['make']     			= $this->input->post('make');
			
			$data['serial_number']     	= $this->input->post('serial_number');
            $data['value']   			= $this->input->post('value');
            $data['condition']     		= $this->input->post('condition');
			$data['date']         		= $this->input->post('date');
            $data['status']     		= $this->input->post('status');
            
            $this->db->where('loan_id', $param2);
            $this->db->update('loan', $data);
			 $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/loan_applicant/'.$data['assignment_id'], 'refresh');
			}
			
       if ($param1 == 'delete') {
            $this->db->where('loan_id' , $param2);
            $this->db->delete('loan');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/loan_applicant' , 'refresh');
        }
		
        $page_data['page_name']  = 'loan_applicant';
        $page_data['page_title'] = get_phrase('manage_loan_applicants');
        $page_data['loan_applicants']  = $this->db->get('loan')->result_array();
        $this->load->view('backend/index', $page_data);
    }
	
	
	
	/**********MANAGE LOAN *******************/
    function loan_approval($param1 = '', $param2 = '' , $param3 = '')
    {
       if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        
        $page_data['page_name']  = 'loan_approval';
        $page_data['page_title'] = get_phrase('manage_loan_approval');
        $page_data['loan_approvals']  = $this->db->get('loan')->result_array();
        $this->load->view('backend/index', $page_data);
    }
	
	
	
	 /**********MANAGING MEDIA HERE*******************/
    function media($param1 = '', $param2 = '' , $param3 = '')
    {
       if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        if ($param1 == 'create') {
            $youtube_url = trim($this->input->post('youtube_url'));
            $iframe = $this->build_youtube_iframe($youtube_url);
            if ($iframe === '' && $youtube_url !== '') {
                $this->session->set_flashdata('error', 'Could not extract a YouTube video ID from the URL provided.');
                redirect(base_url() . 'index.php?admin/media', 'refresh');
            }
            $data = array(
                'title'       => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'mlink'       => $iframe,
                'file_name'   => '',
                'file_type'   => 'youtube',
                'class_id'    => $this->input->post('class_id'),
                'teacher_id'  => '',
                'timestamp'   => date('D, d F Y'),
            );
            $this->db->insert('media', $data);
            $this->session->set_flashdata('flash_message', get_phrase('media_added_successfully'));
            redirect(base_url() . 'index.php?admin/media', 'refresh');
        }

        if ($param1 == 'delete') {
            $this->db->where('media_id', $param2)->delete('media');
            $this->session->set_flashdata('flash_message', get_phrase('media_deleted'));
            redirect(base_url() . 'index.php?admin/media', 'refresh');
        }

        $page_data['page_name']  = 'media';
        $page_data['page_title'] = get_phrase('manage_media');
        $page_data['medias']   = $this->db->get('media')->result_array();
        $page_data['classes']  = $this->db->get('class')->result_array();
        $this->load->view('backend/index', $page_data);
    }

    private function build_youtube_iframe($url)
    {
        if ($url === '') return '';
        $video_id = '';
        $patterns = array(
            '~(?:youtu\.be/|youtube\.com/(?:embed/|v/|watch\?v=|watch\?.+&v=|shorts/))([\w-]{11})~i',
            '~^([\w-]{11})$~',
        );
        foreach ($patterns as $p) {
            if (preg_match($p, $url, $m)) { $video_id = $m[1]; break; }
        }
        if ($video_id === '') return '';
        return '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $video_id . '" frameborder="0" allowfullscreen></iframe>';
    }
	
    
	
	    /*****FRONT_END *********/
    function front_end($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'index.php?login', 'refresh');
        
        if ($param1 == 'do_update') {
			 
            $data['description'] = $this->input->post('about_us');
            $this->db->where('type' , 'about_us');
            $this->db->update('front_end' , $data);

            $data['description'] = $this->input->post('vision');
            $this->db->where('type' , 'vision');
            $this->db->update('front_end' , $data);

            $data['description'] = $this->input->post('mission');
            $this->db->where('type' , 'mission');
            $this->db->update('front_end' , $data);

            $data['description'] = $this->input->post('goal');
            $this->db->where('type' , 'goal');
            $this->db->update('front_end' , $data);

            $data['description'] = $this->input->post('services');
            $this->db->where('type' , 'services');
            $this->db->update('front_end' , $data);

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated')); 
            redirect(base_url() . 'index.php?admin/front_end/', 'refresh');
        }
      
       
        $page_data['page_name']  = 'front_end';
        $page_data['page_title'] = get_phrase('front_ends');
        $page_data['settings']   = $this->db->get('front_end')->result_array();
        $this->load->view('backend/index', $page_data);
    }
	

    /****** SEND EXAM MARKS VIA SMS ********/
    function exam_marks_sms($param1 = '' , $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

       
        $page_data['page_name']  = 'exam_marks_sms';
        $page_data['page_title'] = get_phrase('send_marks_by_sms');
        $this->load->view('backend/index', $page_data);
    }

    /****MANAGE EXAM MARKS*****/
    /****MANAGE EXAM MARKS (written exams)*****/
    function marks($exam_id = '', $class_id = '', $subject_id = '')
    {
        $this->cbt_guard();
        if ($this->input->post('operation') == 'selection') {
            $e = (int)$this->input->post('exam_id'); $c = (int)$this->input->post('class_id'); $s = (int)$this->input->post('subject_id');
            if (!$e || !$c || !$s) $this->cbt_back('marks', get_phrase('choose_exam_class_and_subject'), true);
            redirect(base_url() . 'index.php?admin/marks/' . $e . '/' . $c . '/' . $s, 'refresh');
        }
        $here = 'marks/' . (int)$exam_id . '/' . (int)$class_id . '/' . (int)$subject_id;
        if ($this->input->post('operation') == 'update') {
            $obtained = (array)$this->input->post('mark_obtained');
            $totals   = (array)$this->input->post('mark_total');
            $comments = (array)$this->input->post('comment');
            $errors = array(); $saved = 0;
            foreach ($obtained as $mark_id => $value) {
                $m = $this->db->get_where('mark', array('mark_id' => (int)$mark_id, 'exam_id' => (int)$exam_id, 'subject_id' => (int)$subject_id))->row();
                if (!$m) continue;
                $value = trim((string)$value);
                $total = isset($totals[$mark_id]) ? (int)$totals[$mark_id] : (int)$m->mark_total;
                // Validation rules live in sms_exam_helper (unit tested).
                if ($err = sms_mark_error($value, $total)) {
                    $st = $this->db->get_where('student', array('student_id' => $m->student_id))->row();
                    $errors[] = ($st ? $st->name : '#' . $m->student_id) . ': ' . get_phrase($err);
                    continue;
                }
                $this->db->where('mark_id', (int)$mark_id)->update('mark', array(
                    'mark_obtained' => $value === '' ? null : $value, 'mark_total' => $total,
                    'comment' => trim((string)($comments[$mark_id] ?? '')),
                ));
                $saved++;
            }
            if ($errors) {
                $this->session->set_flashdata('mark_errors', $errors);
                $this->cbt_back($here, $saved . ' ' . get_phrase('saved') . ', ' . count($errors) . ' ' . get_phrase('need_correction'), true);
            }
            $this->cbt_back($here, get_phrase('marks_saved') . ' (' . $saved . ')');
        }
        if ($exam_id && $class_id && $subject_id) {
            $page_data['exam']     = $this->exam_model->classic_exam($exam_id);
            $page_data['subject']  = $this->db->get_where('subject', array('subject_id' => (int)$subject_id, 'class_id' => (int)$class_id))->row_array();
            if (!$page_data['exam'] || !$page_data['subject']) $this->cbt_back('marks', get_phrase('choose_exam_class_and_subject'), true);
            $page_data['students'] = $this->exam_model->subject_marks($exam_id, $class_id, $subject_id);
            $page_data['errors']   = (array)$this->session->flashdata('mark_errors');
        }
        $page_data['exam_id']    = (int)$exam_id;
        $page_data['class_id']   = (int)$class_id;
        $page_data['subject_id'] = (int)$subject_id;
        $page_data['exams']      = $this->exam_model->classic_exams();
        $page_data['classes']    = $this->db->order_by('name', 'ASC')->get('class')->result_array();
        $page_data['subjects']   = $this->db->order_by('name', 'ASC')->get('subject')->result_array();
        $page_data['page_name']  = 'marks';
        $page_data['page_title'] = get_phrase('manage_exam_marks');
        $this->load->view('backend/index', $page_data);
    }


    // TABULATION SHEET
    // TABULATION SHEET (written exams): class result grid, rank, publish & email results
    function tabulation_sheet($exam_id = '', $class_id = '', $action = '')
    {
        $this->cbt_guard();
        if ($this->input->post('operation') == 'selection') {
            $e = (int)$this->input->post('exam_id'); $c = (int)$this->input->post('class_id');
            if (!$e || !$c) $this->cbt_back('tabulation_sheet', get_phrase('choose_exam_and_class'), true);
            redirect(base_url() . 'index.php?admin/tabulation_sheet/' . $e . '/' . $c, 'refresh');
        }
        if ($exam_id && $class_id) {
            $tab = $this->exam_model->tabulation($exam_id, $class_id);
            if (!$tab['exam']) $this->cbt_back('tabulation_sheet', get_phrase('exam_not_found'), true);
            $here = 'tabulation_sheet/' . (int)$exam_id . '/' . (int)$class_id;
            if ($action == 'publish') {
                $entered = array_filter($tab['rows'], function ($r) { return $r['percent'] !== null; });
                if (!$entered) $this->cbt_back($here, get_phrase('no_marks_entered_yet'), true);
                $this->db->where('exam_id', (int)$exam_id)->update('exam', array('results_published' => 1, 'results_published_at' => time()));
                $this->load->model('email_model');
                $counts = $this->email_model->notify_classic_results($exam_id, $class_id);
                $this->cbt_back($here, get_phrase('results_published') . '.' . $this->email_summary($counts));
            }
            $page_data['tab']   = $tab;
            $page_data['class'] = $this->db->get_where('class', array('class_id' => (int)$class_id))->row_array();
        }
        $page_data['exam_id']    = (int)$exam_id;
        $page_data['class_id']   = (int)$class_id;
        $page_data['exams']      = $this->exam_model->classic_exams();
        $page_data['classes']    = $this->db->order_by('name', 'ASC')->get('class')->result_array();
        $page_data['page_name']  = 'tabulation_sheet';
        $page_data['page_title'] = get_phrase('tabulation_sheet');
        $this->load->view('backend/index', $page_data);
    }


    
    
    /****MANAGE GRADES*****/
    /****MANAGE GRADES (percentage bands)*****/
    function grade($param1 = '', $param2 = '')
    {
        $this->cbt_guard();
        $existing = $this->db->get('grade')->result_array();
        if ($param1 == 'create' || $param1 == 'do_update') {
            $data = array(
                'name'        => trim((string)$this->input->post('name')),
                'grade_point' => trim((string)$this->input->post('grade_point')),
                'mark_from'   => $this->input->post('mark_from'),
                'mark_upto'   => $this->input->post('mark_upto'),
                'comment'     => trim((string)$this->input->post('comment')),
            );
            // Range / overlap rules live in sms_exam_helper (unit tested).
            $error = sms_grade_error($data['name'], $data['mark_from'], $data['mark_upto'], $existing, $param1 == 'do_update' ? (int)$param2 : 0);
            if ($error) $this->cbt_back('grade' . ($param1 == 'do_update' ? '/edit/' . (int)$param2 : ''), get_phrase($error), true);
            if ($param1 == 'create') $this->db->insert('grade', $data);
            else $this->db->where('grade_id', (int)$param2)->update('grade', $data);
            $this->cbt_back('grade', get_phrase($param1 == 'create' ? 'data_added_successfully' : 'data_updated'));
        }
        if ($param1 == 'load_defaults') {
            if ($existing) $this->cbt_back('grade', get_phrase('delete_existing_grades_first'), true);
            foreach (sms_default_grades() as $g) $this->db->insert('grade', $g);
            $this->cbt_back('grade', get_phrase('default_grades_added'));
        }
        if ($param1 == 'delete') {
            $this->db->where('grade_id', (int)$param2)->delete('grade');
            $this->cbt_back('grade', get_phrase('data_deleted'));
        }
        usort($existing, function ($a, $b) { return (int)$b['mark_from'] - (int)$a['mark_from']; });
        $page_data['edit']       = $param1 == 'edit' ? $this->db->get_where('grade', array('grade_id' => (int)$param2))->row_array() : null;
        $page_data['grades']     = $existing;
        $page_data['page_name']  = 'grade';
        $page_data['page_title'] = get_phrase('manage_grade');
        $this->load->view('backend/index', $page_data);
    }

    
    /**********MANAGING CLASS ROUTINE******************/
    function class_routine($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        
        $page_data['page_name']  = 'class_routine';
        $page_data['page_title'] = get_phrase('manage_class_routine');
        $this->load->view('backend/index', $page_data);
    }
	
	/****** DAILY ATTENDANCE *****************/
	function manage_attendance($param1 = '', $param2 = '', $param3 = '', $param4 = '')
	{
		if($this->session->userdata('admin_login') != 1)
            redirect(base_url() , 'refresh');

        $this->ensure_teacher_attendance_table();

        // ==== EXPORT BRANCHES ====
        // admin/manage_attendance/export_student/<yyyy>/<mm>/<class_id>
        if ($param1 == 'export_student') {
            $this->export_attendance_csv('student', (int)$param2, (int)$param3, (int)$param4);
            return;
        }
        // admin/manage_attendance/export_teacher/<yyyy>/<mm>
        if ($param1 == 'export_teacher') {
            $this->export_attendance_csv('teacher', (int)$param2, (int)$param3, 0);
            return;
        }

        // ==== TEACHER ATTENDANCE BRANCH ====
        // URL forms:
        //   admin/manage_attendance/teacher/<dd>/<mm>/<yyyy>   -> show teacher tab for date
        //   admin/manage_attendance/save_teacher                -> POST save
        //   admin/manage_attendance/search_teacher              -> AJAX name search

        if ($param1 == 'save_teacher' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $att_date = $this->input->post('attendance_date');
            $statuses = $this->input->post('teacher_status');

            if (!empty($att_date) && is_array($statuses)) {
                foreach ($statuses as $teacher_id => $status) {
                    $teacher_id = (int)$teacher_id;
                    $status     = (int)$status;
                    $existing = $this->db->get_where('teacher_attendance', array(
                        'teacher_id' => $teacher_id,
                        'date'       => $att_date,
                    ))->row();

                    if ($existing) {
                        $this->db->where('attendance_id', $existing->attendance_id)
                                 ->update('teacher_attendance', array('status' => $status));
                    } else {
                        $this->db->insert('teacher_attendance', array(
                            'teacher_id' => $teacher_id,
                            'date'       => $att_date,
                            'status'     => $status,
                        ));
                    }
                }
                $this->session->set_flashdata('flash_message', 'Teacher attendance saved successfully');
            }

            $d = date('d', strtotime($att_date));
            $m = date('m', strtotime($att_date));
            $y = date('Y', strtotime($att_date));
            redirect(base_url() . 'index.php?admin/manage_attendance/teacher/' . $d . '/' . $m . '/' . $y, 'refresh');
        }

        if ($param1 == 'search_teacher' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $first_name = trim($this->input->post('first_name'));
            $matched_ids = array();
            $this->db->select('teacher_id');
            if ($first_name !== '') {
                $this->db->like('name', $first_name);
            }
            $rows = $this->db->get('teacher')->result_array();
            foreach ($rows as $r) {
                $matched_ids[] = (int)$r['teacher_id'];
            }
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success'     => true,
                    'matched_ids' => $matched_ids,
                    'count'       => count($matched_ids),
                )));
            return;
        }

        if ($param1 == 'teacher') {
            $day   = $param2 !== '' ? $param2 : date('d');
            $month = $param3 !== '' ? $param3 : date('m');
            $year  = $param4 !== '' ? $param4 : date('Y');
            $selected_date = sprintf('%04d-%02d-%02d', (int)$year, (int)$month, (int)$day);

            $teachers = $this->db->order_by('name', 'asc')->get('teacher')->result_array();
            $existing_teacher_attendance = array();
            if (!empty($teachers)) {
                $tids = array_map(function($t) { return $t['teacher_id']; }, $teachers);
                $this->db->where_in('teacher_id', $tids);
                $this->db->where('date', $selected_date);
                $rows = $this->db->get('teacher_attendance')->result_array();
                foreach ($rows as $r) {
                    $existing_teacher_attendance[$r['teacher_id']] = (int)$r['status'];
                }
            }

            $page_data['classes']                     = $this->db->get('class')->result_array();
            $page_data['selected_date']               = $selected_date;
            $page_data['selected_class_id']           = '';
            $page_data['students']                    = array();
            $page_data['existing_attendance']         = array();
            $page_data['teachers']                    = $teachers;
            $page_data['existing_teacher_attendance'] = $existing_teacher_attendance;
            $page_data['active_tab']                  = 'teacher';
            $page_data['page_name']                   = 'manage_attendance';
            $page_data['page_title']                  = get_phrase('manage_daily_attendance');
            $this->load->view('backend/index', $page_data);
            return;
        }

        // ==== STUDENT ATTENDANCE BRANCH (existing) ====

        // AJAX search by first name within a class: admin/manage_attendance/search
        if ($param1 == 'search' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $class_id   = $this->input->post('class_id');
            $first_name = trim($this->input->post('first_name'));

            $matched_ids = array();
            if ($class_id !== '' && $class_id !== null) {
                $this->db->select('student_id');
                $this->db->where('class_id', $class_id);
                $this->db->where('is_active', 1);
                if ($first_name !== '') {
                    $this->db->like('first_name', $first_name);
                }
                $rows = $this->db->get('student')->result_array();
                foreach ($rows as $r) {
                    $matched_ids[] = (int) $r['student_id'];
                }
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success'     => true,
                    'matched_ids' => $matched_ids,
                    'count'       => count($matched_ids),
                )));
            return;
        }

        // Handle SAVE POST: admin/manage_attendance/save
        if ($param1 == 'save' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $att_date  = $this->input->post('attendance_date');
            $class_id  = $this->input->post('class_id');
            $statuses  = $this->input->post('status');

            if (!empty($att_date) && is_array($statuses)) {
                foreach ($statuses as $student_id => $status) {
                    $student_id = (int) $student_id;
                    $status = (int) $status;
                    $existing = $this->db->get_where('attendance', array(
                        'student_id' => $student_id,
                        'date'       => $att_date,
                    ))->row();

                    if ($existing) {
                        $this->db->where('attendance_id', $existing->attendance_id)
                                 ->update('attendance', array('status' => $status));
                    } else {
                        $this->db->insert('attendance', array(
                            'student_id' => $student_id,
                            'date'       => $att_date,
                            'status'     => $status,
                        ));
                    }
                }
                $this->session->set_flashdata('flash_message', 'Attendance saved successfully');
            }

            // Redirect back to the same date+class view
            $d = date('d', strtotime($att_date));
            $m = date('m', strtotime($att_date));
            $y = date('Y', strtotime($att_date));
            redirect(base_url() . 'index.php?admin/manage_attendance/' . $d . '/' . $m . '/' . $y . '/' . $class_id, 'refresh');
        }

        // Default view: parse date params (day/month/year) + class_id
        $day      = $param1 !== '' ? $param1 : date('d');
        $month    = $param2 !== '' ? $param2 : date('m');
        $year     = $param3 !== '' ? $param3 : date('Y');
        $class_id = $param4;

        $selected_date = sprintf('%04d-%02d-%02d', (int)$year, (int)$month, (int)$day);

        $page_data['classes']            = $this->db->get('class')->result_array();
        $page_data['selected_date']      = $selected_date;
        $page_data['selected_class_id']  = $class_id;
        $page_data['students']           = array();
        $page_data['existing_attendance']= array();

        if ($class_id !== '' && $class_id !== null) {
            $this->db->where('class_id', $class_id);
            $this->db->where('is_active', 1);
            $page_data['students'] = $this->db->get('student')->result_array();

            if (!empty($page_data['students'])) {
                $student_ids = array_map(function($s) { return $s['student_id']; }, $page_data['students']);
                $this->db->where_in('student_id', $student_ids);
                $this->db->where('date', $selected_date);
                $rows = $this->db->get('attendance')->result_array();
                foreach ($rows as $r) {
                    $page_data['existing_attendance'][$r['student_id']] = (int) $r['status'];
                }
            }
        }

        $page_data['teachers']                    = array();
        $page_data['existing_teacher_attendance'] = array();
        $page_data['active_tab']                  = 'student';
        $page_data['page_name']                   = 'manage_attendance';
        $page_data['page_title']                  = get_phrase('manage_daily_attendance');
        $this->load->view('backend/index', $page_data);
	}

    /**
     * CSV export of attendance for a given month.
     *
     * @param string $kind     'student' | 'teacher'
     * @param int    $year     YYYY
     * @param int    $month    1-12
     * @param int    $class_id (students only — 0 means all classes)
     */
    private function export_attendance_csv($kind, $year, $month, $class_id = 0)
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $this->ensure_teacher_attendance_table();

        $year  = $year  ?: (int)date('Y');
        $month = $month ?: (int)date('n');
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end   = date('Y-m-t', strtotime($start));
        $days  = (int)date('t', strtotime($start));
        $month_name = date('F Y', strtotime($start));

        // School name for header row
        $school_row = $this->db->get_where('settings', array('type' => 'system_name'))->row();
        $school_name = $school_row ? $school_row->description : 'School';

        if ($kind === 'teacher') {
            $teachers = $this->db->order_by('name', 'asc')->get('teacher')->result_array();
            $entity_ids = array_map(function ($t) { return (int)$t['teacher_id']; }, $teachers);

            $attendance_map = array(); // [teacher_id][YYYY-MM-DD] = status
            if (!empty($entity_ids)) {
                $this->db->where_in('teacher_id', $entity_ids);
                $this->db->where('date >=', $start);
                $this->db->where('date <=', $end);
                $rows = $this->db->get('teacher_attendance')->result_array();
                foreach ($rows as $r) {
                    $attendance_map[(int)$r['teacher_id']][$r['date']] = (int)$r['status'];
                }
            }

            $filename = 'teacher_attendance_' . $year . '_' . sprintf('%02d', $month) . '.csv';
            $this->_send_csv_headers($filename);
            $out = fopen('php://output', 'w');

            fputcsv($out, array($school_name . ' — Teacher Attendance — ' . $month_name));
            fputcsv($out, array()); // blank separator

            $header = array('#', 'Teacher ID', 'Name', 'Designation');
            for ($d = 1; $d <= $days; $d++) $header[] = $d;
            $header[] = 'Present';
            $header[] = 'Absent';
            $header[] = 'Not Marked';
            fputcsv($out, $header);

            $i = 1;
            foreach ($teachers as $t) {
                $row = array(
                    $i++,
                    'TCH-' . str_pad($t['teacher_id'], 4, '0', STR_PAD_LEFT),
                    $t['name'],
                    isset($t['designation']) ? $t['designation'] : '',
                );
                $p = 0; $a = 0; $u = 0;
                for ($d = 1; $d <= $days; $d++) {
                    $date_key = sprintf('%04d-%02d-%02d', $year, $month, $d);
                    if (isset($attendance_map[(int)$t['teacher_id']][$date_key])) {
                        $st = $attendance_map[(int)$t['teacher_id']][$date_key];
                        if ($st === 1)       { $row[] = 'P'; $p++; }
                        else if ($st === 0)  { $row[] = 'A'; $a++; }
                        else                 { $row[] = '-'; $u++; }
                    } else {
                        $row[] = '';
                        $u++;
                    }
                }
                $row[] = $p;
                $row[] = $a;
                $row[] = $u;
                fputcsv($out, $row);
            }

            fclose($out);
            return;
        }

        // ===== STUDENTS =====
        $this->db->where('is_active', 1);
        if ($class_id > 0) $this->db->where('class_id', $class_id);
        $this->db->order_by('class_id, name', 'asc');
        $students = $this->db->get('student')->result_array();
        $entity_ids = array_map(function ($s) { return (int)$s['student_id']; }, $students);

        $attendance_map = array();
        if (!empty($entity_ids)) {
            $this->db->where_in('student_id', $entity_ids);
            $this->db->where('date >=', $start);
            $this->db->where('date <=', $end);
            $rows = $this->db->get('attendance')->result_array();
            foreach ($rows as $r) {
                $attendance_map[(int)$r['student_id']][$r['date']] = (int)$r['status'];
            }
        }

        // class name lookup
        $classes = $this->db->get('class')->result_array();
        $class_name_by_id = array();
        foreach ($classes as $c) $class_name_by_id[(int)$c['class_id']] = $c['name'];

        $cls_label = $class_id > 0
            ? (isset($class_name_by_id[$class_id]) ? $class_name_by_id[$class_id] : 'Class ' . $class_id)
            : 'All Classes';
        $filename = 'student_attendance_' . $year . '_' . sprintf('%02d', $month)
                  . ($class_id > 0 ? '_class_' . $class_id : '_all') . '.csv';

        $this->_send_csv_headers($filename);
        $out = fopen('php://output', 'w');

        fputcsv($out, array($school_name . ' — Student Attendance — ' . $month_name . ' — ' . $cls_label));
        fputcsv($out, array());

        $header = array('#', 'Student ID', 'Name', 'Class');
        for ($d = 1; $d <= $days; $d++) $header[] = $d;
        $header[] = 'Present';
        $header[] = 'Absent';
        $header[] = 'Not Marked';
        fputcsv($out, $header);

        $i = 1;
        foreach ($students as $s) {
            $row = array(
                $i++,
                'STU-' . str_pad($s['student_id'], 5, '0', STR_PAD_LEFT),
                $s['name'],
                isset($class_name_by_id[(int)$s['class_id']]) ? $class_name_by_id[(int)$s['class_id']] : '',
            );
            $p = 0; $a = 0; $u = 0;
            for ($d = 1; $d <= $days; $d++) {
                $date_key = sprintf('%04d-%02d-%02d', $year, $month, $d);
                if (isset($attendance_map[(int)$s['student_id']][$date_key])) {
                    $st = $attendance_map[(int)$s['student_id']][$date_key];
                    if ($st === 1)       { $row[] = 'P'; $p++; }
                    else if ($st === 0)  { $row[] = 'A'; $a++; }
                    else                 { $row[] = '-'; $u++; }
                } else {
                    $row[] = '';
                    $u++;
                }
            }
            $row[] = $p;
            $row[] = $a;
            $row[] = $u;
            fputcsv($out, $row);
        }

        fclose($out);
    }

    private function _send_csv_headers($filename)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    /**
     * Count of 'present' days for a teacher in a given YYYY-MM string. Used by
     * the salary slip form to pre-fill 'Days Worked' from attendance.
     */
    public function teacher_present_days($teacher_id, $year_month)
    {
        $this->ensure_teacher_attendance_table();
        $ts = strtotime($year_month . '-01');
        if (!$ts) return 0;
        $start = date('Y-m-01', $ts);
        $end   = date('Y-m-t',  $ts);
        $this->db->where('teacher_id', (int)$teacher_id);
        $this->db->where('status', 1);
        $this->db->where('date >=', $start);
        $this->db->where('date <=', $end);
        return (int)$this->db->count_all_results('teacher_attendance');
    }

    /**
     * JSON: GET admin/teacher_present_days_json/<teacher_id>?month=YYYY-MM
     */
    public function teacher_present_days_json($teacher_id = 0)
    {
        if ($this->session->userdata('admin_login') != 1) {
            $this->output->set_status_header(401);
            return;
        }
        $month = $this->input->get('month') ?: date('Y-m');
        $count = $this->teacher_present_days((int)$teacher_id, $month);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(array(
                'teacher_id' => (int)$teacher_id,
                'month'      => $month,
                'present'    => $count,
            )));
    }

    /******MANAGE BILLING / INVOICES WITH STATUS*****/
    function invoice($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
			
        if ($param1 == 'create_mass_invoice') {
            if (!($this->input->post('student_id'))) {
                foreach ($this->input->post('student_id') as $id) {

                    $data['student_id']         = $id;
                    $data['title']              = $this->input->post('title');
                    $data['description']        = $this->input->post('description');
                    $data['amount']             = $this->input->post('amount');
                    $data['amount_paid']        = $this->input->post('amount_paid');
                    $data['due']                = $data['amount'] - $data['amount_paid'];
                    $data['status']             = $this->input->post('status');
                    $data['creation_timestamp'] = strtotime($this->input->post('date'));
                    
                    $this->db->insert('invoice', $data);
                    $invoice_id = $this->db->insert_id();

                    $data2['invoice_id']        =   $invoice_id;
                    $data2['student_id']        =   $id;
                    $data2['title']             =   $this->input->post('title');
                    $data2['description']       =   $this->input->post('description');
                    $data2['payment_type']      =  'income';
                    $data2['method']            =   $this->input->post('method');
                    $data2['amount']            =   $this->input->post('amount_paid');
                    $data2['timestamp']         =   strtotime($this->input->post('date'));

                    $this->db->insert('payment' , $data2);

                }
            }
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/student_payment', 'refresh');
        }

        

        $page_data['page_name']  = 'invoice';
        $page_data['page_title'] = get_phrase('manage_invoice/payment');
        $this->db->order_by('creation_timestamp', 'desc');
        $page_data['invoices'] = $this->db->get('invoice')->result_array();
        $this->load->view('backend/index', $page_data);
    }

    /**********ACCOUNTING********************/
    function income($param1 = '' , $param2 = '')
    {
       if ($this->session->userdata('admin_login') != 1)
            redirect('login', 'refresh');
        $page_data['page_name']  = 'income';
        $page_data['page_title'] = get_phrase('student_payments');
        $this->db->order_by('creation_timestamp', 'desc');
        $page_data['invoices'] = $this->db->get('invoice')->result_array();
        $this->load->view('backend/index', $page_data); 
    }

    function get_class_section($class_id) {
        $sections = $this->db->get_where('section', array('class_id' => $class_id))->result_array();
        $options = '<option value="">' . get_phrase('select_section') . '</option>';
        foreach ($sections as $section) {
            $options .= '<option value="' . $section['section_id'] . '">' . $section['name'] . '</option>';
        }
        echo $options;
    }



    function expense($param1 = '' , $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect('login', 'refresh');
     
        $page_data['page_name']  = 'expense';
        $page_data['page_title'] = get_phrase('expenses');
        $this->load->view('backend/index', $page_data); 
    }

    function export_expenses_by_year($year = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $year = $year !== '' ? (int) $year : (int) date('Y');

        $this->db->where('year', $year);
        $this->db->order_by('amount', 'desc');
        $rows = $this->db->get('expense_category')->result_array();

        $school = array();
        $school_name_row   = $this->db->get_where('settings', array('type' => 'system_name'))->row();
        $school['name']    = $school_name_row ? $school_name_row->description : '';
        $address_row       = $this->db->get_where('settings', array('type' => 'address'))->row();
        $school['address'] = $address_row ? $address_row->description : '';
        $school['phone']   = '9987676008';
        $school['email']   = 'shreecochingclasses@gmail.com';
        $school['website'] = 'shreecochingclasses.com';

        $total = 0;
        foreach ($rows as $r) $total += floatval($r['amount']);

        $view_data = array(
            'year'    => $year,
            'rows'    => $rows,
            'total'   => $total,
            'school'  => $school,
        );
        $this->load->view('backend/admin/expense_category_export', $view_data);
    }

    function expense_category($param1 = '' , $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect('login', 'refresh');
        if ($param1 == 'create') {
            $data['name']   = $this->input->post('name');
            $data['amount'] = $this->input->post('amount') ?: 0;
            $data['year']   = $this->input->post('year') ?: null;
            $this->db->insert('expense_category' , $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/expense_category');
        }
        if ($param1 == 'edit') {
            $data['name']   = $this->input->post('name');
            $data['amount'] = $this->input->post('amount') ?: 0;
            $data['year']   = $this->input->post('year') ?: null;
            $this->db->where('expense_category_id' , $param2);
            $this->db->update('expense_category' , $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/expense_category');
        }
        if ($param1 == 'delete') {
            $this->db->where('expense_category_id' , $param2);
            $this->db->delete('expense_category');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/expense_category');
        }

        $page_data['page_name']  = 'expense_category';
        $page_data['page_title'] = get_phrase('expense_category');
        $this->load->view('backend/index', $page_data);
    }

    /**********MANAGE LIBRARY / BOOKS********************/
    function book($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect('login', 'refresh');
        
        
        $page_data['books']      = $this->db->get('book')->result_array();
        $page_data['page_name']  = 'book';
        $page_data['page_title'] = get_phrase('manage_library_books');
        $this->load->view('backend/index', $page_data);
        
    }
	
    /**********MANAGE TRANSPORT / VEHICLES / ROUTES********************/
    function transport($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect('login', 'refresh');

        if ($param1 == 'create') {
            $data['route_name']        = $this->input->post('route_name');
            $data['number_of_vehicle'] = $this->input->post('number_of_vehicle');
            $data['picnic_date']       = $this->input->post('picnic_date') ?: null;
            $data['location']          = $this->input->post('location');
            $data['description']       = $this->input->post('description');
            $data['route_fare']        = $this->input->post('route_fare');
            $data['expenses']          = $this->input->post('expenses') ?: 0;
            $data['bill_file']         = $this->upload_picnic_bill();
            $this->db->insert('transport', $data);
            $this->session->set_flashdata('flash_message', 'Picnic added successfully');
            redirect(base_url() . 'index.php?admin/transport', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['route_name']        = $this->input->post('route_name');
            $data['number_of_vehicle'] = $this->input->post('number_of_vehicle');
            $data['picnic_date']       = $this->input->post('picnic_date') ?: null;
            $data['location']          = $this->input->post('location');
            $data['description']       = $this->input->post('description');
            $data['route_fare']        = $this->input->post('route_fare');
            $data['expenses']          = $this->input->post('expenses') ?: 0;

            $new_bill = $this->upload_picnic_bill();
            if ($new_bill !== '') $data['bill_file'] = $new_bill;

            $this->db->where('transport_id', $param2);
            $this->db->update('transport', $data);
            $this->session->set_flashdata('flash_message', 'Picnic updated');
            redirect(base_url() . 'index.php?admin/transport', 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('transport', array(
                'transport_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('transport_id', $param2);
            $this->db->delete('transport');
            $this->session->set_flashdata('flash_message', 'Picnic deleted');
            redirect(base_url() . 'index.php?admin/transport', 'refresh');
        }
        $page_data['transports'] = $this->db->get('transport')->result_array();
        $page_data['page_name']  = 'transport';
        $page_data['page_title'] = 'Manage Picnic';
        $this->load->view('backend/index', $page_data);
    }

    private function upload_picnic_bill()
    {
        if (empty($_FILES['bill_file']['name']) || !is_uploaded_file($_FILES['bill_file']['tmp_name'])) {
            return '';
        }
        $upload_dir = FCPATH . 'uploads/picnic_bills/';
        if (!is_dir($upload_dir)) {
            @mkdir($upload_dir, 0755, true);
        }
        $ext = pathinfo($_FILES['bill_file']['name'], PATHINFO_EXTENSION);
        $allowed = array('pdf', 'jpg', 'jpeg', 'png', 'gif', 'webp');
        if (!in_array(strtolower($ext), $allowed)) {
            return '';
        }
        $safe_name = 'bill_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        if (move_uploaded_file($_FILES['bill_file']['tmp_name'], $upload_dir . $safe_name)) {
            return $safe_name;
        }
        return '';
    }
    /**********MANAGE DORMITORY / HOSTELS / ROOMS ********************/
    function dormitory($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect('login', 'refresh');
        if ($param1 == 'create') {
            $data['name']           = $this->input->post('name');
            $data['number_of_room'] = $this->input->post('number_of_room');
            $data['description']    = $this->input->post('description');
            $this->db->insert('dormitory', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/dormitory', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']           = $this->input->post('name');
            $data['number_of_room'] = $this->input->post('number_of_room');
            $data['description']    = $this->input->post('description');
            
            $this->db->where('dormitory_id', $param2);
            $this->db->update('dormitory', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/dormitory', 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('dormitory', array(
                'dormitory_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('dormitory_id', $param2);
            $this->db->delete('dormitory');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/dormitory', 'refresh');
        }
        $page_data['dormitories'] = $this->db->get('dormitory')->result_array();
        $page_data['page_name']   = 'dormitory';
        $page_data['page_title']  = get_phrase('manage_dormitory');
        $this->load->view('backend/index', $page_data);
        
    }
    
    /***MANAGE EVENT / NOTICEBOARD, WILL BE SEEN BY ALL ACCOUNTS DASHBOARD**/
    function noticeboard($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        
        if ($param1 == 'create') {
            $data['notice_title']     = $this->input->post('notice_title');
            $data['notice']           = $this->input->post('notice');
            $data['create_timestamp'] = strtotime($this->input->post('create_timestamp'));
            $this->db->insert('noticeboard', $data);

            $check_sms_send = $this->input->post('check_sms');

            if ($check_sms_send == 1) {
                // sms sending configurations

                $parents  = $this->db->get('parent')->result_array();
                $students = $this->db->get('student')->result_array();
                $teachers = $this->db->get('teacher')->result_array();
                $date     = $this->input->post('create_timestamp');
                $message  = $data['notice_title'] . ' ';
                $message .= get_phrase('on') . ' ' . $date;
                foreach($parents as $row) {
                    $reciever_phone = $row['phone'];
                    $this->sms_model->send_sms($message , $reciever_phone);
                }
                foreach($students as $row) {
                    $reciever_phone = $row['phone'];
                    $this->sms_model->send_sms($message , $reciever_phone);
                }
                foreach($teachers as $row) {
                    $reciever_phone = $row['phone'];
                    $this->sms_model->send_sms($message , $reciever_phone);
                }
            }

            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/noticeboard/', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['notice_title']     = $this->input->post('notice_title');
            $data['notice']           = $this->input->post('notice');
            $data['create_timestamp'] = strtotime($this->input->post('create_timestamp'));
            $this->db->where('notice_id', $param2);
            $this->db->update('noticeboard', $data);

            $check_sms_send = $this->input->post('check_sms');

            if ($check_sms_send == 1) {
                // sms sending configurations

                $parents  = $this->db->get('parent')->result_array();
                $students = $this->db->get('student')->result_array();
                $teachers = $this->db->get('teacher')->result_array();
                $date     = $this->input->post('create_timestamp');
                $message  = $data['notice_title'] . ' ';
                $message .= get_phrase('on') . ' ' . $date;
                foreach($parents as $row) {
                    $reciever_phone = $row['phone'];
                    $this->sms_model->send_sms($message , $reciever_phone);
                }
                foreach($students as $row) {
                    $reciever_phone = $row['phone'];
                    $this->sms_model->send_sms($message , $reciever_phone);
                }
                foreach($teachers as $row) {
                    $reciever_phone = $row['phone'];
                    $this->sms_model->send_sms($message , $reciever_phone);
                }
            }

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/noticeboard/', 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('noticeboard', array(
                'notice_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('notice_id', $param2);
            $this->db->delete('noticeboard');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/noticeboard/', 'refresh');
        }
        $page_data['page_name']  = 'noticeboard';
        $page_data['page_title'] = get_phrase('manage_noticeboard');
        $page_data['notices']    = $this->db->get('noticeboard')->result_array();
        $this->load->view('backend/index', $page_data);
    }
    
    /* private messaging */

    function message($param1 = 'message_home', $param2 = '', $param3 = '') {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $page_data['message_inner_page_name']   = $param1;
        $page_data['page_name']                 = 'message';
        $page_data['page_title']                = get_phrase('private_messaging');
        $this->load->view('backend/index', $page_data);
    }
    
    /*****SITE/SYSTEM SETTINGS*********/
    function system_settings($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'index.php?login', 'refresh');
        
        if ($param1 == 'do_update') {
			 
            $data['description'] = $this->input->post('system_name');
            $this->db->where('type' , 'system_name');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('system_title');
            $this->db->where('type' , 'system_title');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('address');
            $this->db->where('type' , 'address');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('phone');
            $this->db->where('type' , 'phone');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('paypal_email');
            $this->db->where('type' , 'paypal_email');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('currency');
            $this->db->where('type' , 'currency');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('system_email');
            $this->db->where('type' , 'system_email');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('system_name');
            $this->db->where('type' , 'system_name');
            $this->db->update('settings' , $data);

            if (sms_is_language($this->input->post('language'), $this->db->list_fields('language'))) {
                $data['description'] = $this->input->post('language');
                $this->db->where('type' , 'language');
                $this->db->update('settings' , $data);
            }

            $data['description'] = $this->input->post('text_align');
            $this->db->where('type' , 'text_align');
            $this->db->update('settings' , $data);
			
			$data['description'] = $this->input->post('running_session');
            $this->db->where('type' , 'session');
            $this->db->update('settings' , $data);
			
			$data['description'] = $this->input->post('system_footer');
            $this->db->where('type' , 'footer');
            $this->db->update('settings' , $data);
			
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated')); 
            redirect(base_url() . 'index.php?admin/system_settings', 'refresh');
        }
        if ($param1 == 'upload_logo') {
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/logo.png');
            $this->session->set_flashdata('flash_message', get_phrase('settings_updated'));
            redirect(base_url() . 'index.php?admin/system_settings', 'refresh');
        }
        if ($param1 == 'change_skin') {
            $data['description'] = $param2;
            $this->db->where('type' , 'skin_colour');
            $this->db->update('settings' , $data);
            $this->session->set_flashdata('flash_message' , get_phrase('theme_selected')); 
            redirect(base_url() . 'index.php?admin/system_settings', 'refresh'); 
        }
        $page_data['page_name']  = 'system_settings';
        $page_data['page_title'] = get_phrase('system_settings');
        $page_data['settings']   = $this->db->get('settings')->result_array();
        $this->load->view('backend/index', $page_data);
    }
	
	/***** UPDATE PRODUCT *****/
	// Disabled: vendor updater not used (would overwrite custom code / run uploaded PHP).
	/*
	function update( $task = '', $purchase_code = '' ) {
        
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
            
        // Create update directory.
        $dir    = 'update';
        if ( !is_dir($dir) )
            mkdir($dir, 0777, true);
        
        $zipped_file_name   = $_FILES["file_name"]["name"];
        $path               = 'update/' . $zipped_file_name;
        
        move_uploaded_file($_FILES["file_name"]["tmp_name"], $path);
        
        // Unzip uploaded update file and remove zip file.
        $zip = new ZipArchive;
        $res = $zip->open($path);
        if ($res === TRUE) {
            $zip->extractTo('update');
            $zip->close();
            unlink($path);
        }
        
        $unzipped_file_name = substr($zipped_file_name, 0, -4);
        $str                = file_get_contents('./update/' . $unzipped_file_name . '/update_config.json');
        $json               = json_decode($str, true);
        

			
		// Run php modifications
		require './update/' . $unzipped_file_name . '/update_script.php';
        
        // Create new directories.
        if(!empty($json['directory'])) {
            foreach($json['directory'] as $directory) {
                if ( !is_dir( $directory['name']) )
                    mkdir( $directory['name'], 0777, true );
            }
        }
        
        // Create/Replace new files.
        if(!empty($json['files'])) {
            foreach($json['files'] as $file)
                copy($file['root_directory'], $file['update_directory']);
        }
        
        $this->session->set_flashdata('flash_message' , get_phrase('product_updated_successfully'));
        redirect(base_url() . 'index.php?admin/system_settings');
    }
	*/

    /*****SMS SETTINGS*********/
    function sms_settings($param1 = '' , $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'index.php?login', 'refresh');
        if ($param1 == 'clickatell') {

            $data['description'] = $this->input->post('clickatell_user');
            $this->db->where('type' , 'clickatell_user');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('clickatell_password');
            $this->db->where('type' , 'clickatell_password');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('clickatell_api_id');
            $this->db->where('type' , 'clickatell_api_id');
            $this->db->update('settings' , $data);

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/sms_settings/', 'refresh');
        }

        if ($param1 == 'twilio') {

            $data['description'] = $this->input->post('twilio_account_sid');
            $this->db->where('type' , 'twilio_account_sid');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('twilio_auth_token');
            $this->db->where('type' , 'twilio_auth_token');
            $this->db->update('settings' , $data);

            $data['description'] = $this->input->post('twilio_sender_phone_number');
            $this->db->where('type' , 'twilio_sender_phone_number');
            $this->db->update('settings' , $data);

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/sms_settings/', 'refresh');
        }

        if ($param1 == 'active_service') {

            $data['description'] = $this->input->post('active_sms_service');
            $this->db->where('type' , 'active_sms_service');
            $this->db->update('settings' , $data);

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/sms_settings/', 'refresh');
        }

        if ($param1 == 'whatsapp') {

            $this->save_setting('active_whatsapp',         $this->input->post('active_whatsapp'));
            $this->save_setting('twilio_whatsapp_number',  $this->input->post('twilio_whatsapp_number'));
            $this->save_setting('whatsapp_welcome_message',$this->input->post('whatsapp_welcome_message'));

            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/sms_settings/', 'refresh');
        }

        $page_data['page_name']  = 'sms_settings';
        $page_data['page_title'] = get_phrase('sms_settings');
        $page_data['settings']   = $this->db->get('settings')->result_array();
        $this->load->view('backend/index', $page_data);
    }

    private function save_setting($type, $description)
    {
        $existing = $this->db->get_where('settings', array('type' => $type));
        if ($existing->num_rows() > 0) {
            $this->db->where('type', $type)->update('settings', array('description' => $description));
        } else {
            $this->db->insert('settings', array('type' => $type, 'description' => $description));
        }
    }
    
    /*****LANGUAGE SETTINGS*********/
    function manage_language($param1 = '', $param2 = '', $param3 = '', $param4 = '', $param5 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
			redirect(base_url() . 'index.php?login', 'refresh');

		$base      = base_url() . 'index.php?admin/manage_language/';
		$fields    = $this->db->list_fields('language');
		$languages = sms_language_columns($fields);
		$setting   = $this->db->get_where('settings', array('type' => 'language'))->row();
		$current   = $setting ? $setting->description : 'english';
		// Language / delete / add rules live in sms_core_helper (unit tested).
		$fail = function ($phrase, $to = '') use ($base) {
			$this->session->set_flashdata('error_message', get_phrase($phrase));
			redirect($base . $to, 'refresh');
		};

		// Phrase editor URL: edit_phrase/<lang>/<all|missing>/<page>/<hex search>
		$editor_url = function ($lang, $filter, $page, $search) use ($base) {
			return $base . 'edit_phrase/' . $lang . '/' . $filter . '/' . (int)$page . ($search !== '' ? '/' . bin2hex($search) : '');
		};

		if ($param1 == 'search_phrase') {
			if (!sms_is_language($param2, $fields)) $fail('language_not_found');
			$filter = $this->input->post('filter') === 'missing' ? 'missing' : 'all';
			redirect($editor_url($param2, $filter, 1, trim((string)$this->input->post('q'))), 'refresh');
		}
		if ($param1 == 'update_phrase') {
			if (!sms_is_language($param2, $fields)) $fail('language_not_found');
			// Only the phrases shown on the submitted page are posted (one page, not all rows).
			$updated = 0;
			foreach ((array)$this->input->post('phrase') as $id => $text) {
				$this->db->where('phrase_id', (int)$id)->update('language', array($param2 => trim((string)$text)));
				$updated += $this->db->affected_rows();
			}
			$this->session->set_flashdata('flash_message', get_phrase('phrases_updated') . ': ' . $updated);
			redirect($editor_url($param2, $this->input->post('filter') === 'missing' ? 'missing' : 'all',
				$this->input->post('page'), (string)$this->input->post('q')), 'refresh');
		}
		if ($param1 == 'add_phrase') {
			$phrase = strtolower(preg_replace('/\s+/', '_', trim((string)$this->input->post('phrase'))));
			if ($phrase === '') $fail('phrase_is_required');
			$this->db->query('INSERT IGNORE INTO `language` (`phrase`, `english`) VALUES (' .
				$this->db->escape($phrase) . ', ' . $this->db->escape(sms_humanize_phrase($phrase)) . ')');
			$this->session->set_flashdata('flash_message', get_phrase($this->db->affected_rows() ? 'phrase_added' : 'phrase_already_exists'));
			redirect($base, 'refresh');
		}
		if ($param1 == 'add_language') {
			$error = sms_language_add_error($this->input->post('language'), $fields);
			if ($error !== null) $fail($error);
			$language = strtolower(trim($this->input->post('language')));
			$this->db->query("ALTER TABLE `language` ADD COLUMN `$language` LONGTEXT NOT NULL DEFAULT ''");
			$this->session->set_flashdata('flash_message', get_phrase('language_added'));
			redirect($base, 'refresh');
		}
		if ($param1 == 'delete_language') {
			if ($this->input->method() !== 'post') redirect($base, 'refresh');
			$error = sms_language_delete_error($param2, $current, $fields);
			if ($error !== null) $fail($error);
			$this->load->dbforge();
			$this->dbforge->drop_column('language', $param2);
			$this->session->set_flashdata('flash_message', get_phrase('language_deleted'));
			redirect($base, 'refresh');
		}

		if ($param1 == 'edit_phrase') {
			if (!sms_is_language($param2, $fields)) $fail('language_not_found');
			$filter = $param3 === 'missing' ? 'missing' : 'all';
			$search = (ctype_xdigit((string)$param5) && strlen($param5) % 2 === 0) ? (string)hex2bin($param5) : '';

			$apply = function () use ($param2, $filter, $search) {
				if ($filter === 'missing') $this->db->where($param2, '');
				if ($search !== '') {
					$this->db->group_start()
						->like('phrase', $search)->or_like('english', $search)->or_like($param2, $search)
						->group_end();
				}
			};
			$apply();
			$total = $this->db->count_all_results('language');
			$per_page = 50;
			list($page, $offset, $pages) = sms_page_bounds($param4 === '' ? 1 : $param4, $per_page, $total);
			$apply();
			$page_data['phrases'] = $this->db->select('phrase_id, phrase, english, ' . $this->db->protect_identifiers($param2) . ' AS translation', FALSE)
				->order_by('english', 'ASC')->limit($per_page, $offset)->get('language')->result_array();

			$page_data['edit_language'] = $param2;
			$page_data['filter']        = $filter;
			$page_data['search']        = $search;
			$page_data['page']          = $page;
			$page_data['pages']         = $pages;
			$page_data['total']         = $total;
			$page_data['editor_url']    = $editor_url;
		}

		// Per-language progress for the list
		$counts = array();
		$total_phrases = $this->db->count_all('language');
		if ($languages) {
			$sums = array();
			foreach ($languages as $l) $sums[] = 'SUM(' . $this->db->protect_identifiers($l) . " <> '') AS " . $this->db->protect_identifiers($l);
			$counts = $this->db->query('SELECT ' . implode(', ', $sums) . ' FROM `language`')->row_array();
		}
		$page_data['languages']        = $languages;
		$page_data['current_language'] = $current;
		$page_data['translated']       = $counts;
		$page_data['total_phrases']    = $total_phrases;
		$page_data['page_name']        = 'manage_language';
		$page_data['page_title']       = get_phrase('manage_language');
		$this->load->view('backend/index', $page_data);
    }
    
    /*****BACKUP / RESTORE / DELETE DATA PAGE**********/
    function backup_restore($operation = '', $type = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        
        if ($operation == 'create') {
            $this->crud_model->create_backup($type);
        }
        if ($operation == 'restore') {
            $this->crud_model->restore_backup();
            $this->session->set_flashdata('backup_message', 'Backup Restored');
            redirect(base_url() . 'index.php?admin/backup_restore/', 'refresh');
        }
        if ($operation == 'delete') {
            $this->crud_model->truncate($type);
            $this->session->set_flashdata('backup_message', 'Data removed');
            redirect(base_url() . 'index.php?admin/backup_restore/', 'refresh');
        }
        
        $page_data['page_info']  = 'Create backup / restore from backup';
        $page_data['page_name']  = 'backup_restore';
        $page_data['page_title'] = get_phrase('manage_backup_restore');
        $this->load->view('backend/index', $page_data);
    }
	
	
	
    
    /******MANAGE OWN PROFILE AND CHANGE PASSWORD***/
    function manage_profile($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'index.php?login', 'refresh');
        $admin_id = $this->session->userdata('admin_id');
        if ($param1 == 'update_profile_info') {
            $data['name']  = trim((string)$this->input->post('name'));
            $data['email'] = trim((string)$this->input->post('email'));
            $photo         = isset($_FILES['userfile']) ? $_FILES['userfile'] : null;

            $email_taken = $this->db->where('email', $data['email'])
                                    ->where('admin_id !=', $admin_id)
                                    ->count_all_results('admin') > 0;
            // Validation rules live in sms_core_helper (unit tested).
            $error = sms_profile_error($data['name'], $data['email'], $email_taken);
            if ($error === null) $error = sms_upload_image_error($photo);
            if ($error !== null) {
                $this->session->set_flashdata('error_message', get_phrase($error));
                redirect(base_url() . 'index.php?admin/manage_profile/', 'refresh');
            }

            $this->db->where('admin_id', $admin_id);
            $this->db->update('admin', $data);
            $this->session->set_userdata('name', $data['name']);
            if ($photo && $photo['error'] === UPLOAD_ERR_OK) {
                move_uploaded_file($photo['tmp_name'], 'uploads/admin_image/' . $admin_id . '.jpg');
            }
            $this->session->set_flashdata('flash_message', get_phrase('account_updated'));
            redirect(base_url() . 'index.php?admin/manage_profile/', 'refresh');
        }
        if ($param1 == 'change_password') {
            $current_password = $this->db->get_where('admin', array(
                'admin_id' => $admin_id
            ))->row()->password;
            // Validation rules live in sms_core_helper (unit tested).
            $error = sms_password_change_error($current_password, $this->input->post('password'),
                $this->input->post('new_password'), $this->input->post('confirm_new_password'));
            if ($error === null) {
                $this->db->where('admin_id', $admin_id);
                $this->db->update('admin', array(
                    'password' => $this->input->post('new_password')
                ));
                $this->session->set_flashdata('flash_message', get_phrase('password_updated'));
            } else {
                $this->session->set_flashdata('error_message', get_phrase($error));
            }
            redirect(base_url() . 'index.php?admin/manage_profile/', 'refresh');
        }
        $page_data['page_name']  = 'manage_profile';
        $page_data['page_title'] = get_phrase('manage_profile');
        $page_data['edit_data']  = $this->db->get_where('admin', array(
            'admin_id' => $this->session->userdata('admin_id')
        ))->result_array();
        $this->load->view('backend/index', $page_data);
    }
	
	
// CBT CUSTOMISATION STARTS FROM HERE
// Exams are rows of `cbt_exam`; questions, assignments and answers link to it by exam_id.
// Rules (marking, timing, ranking, publish checks) live in sms_exam_helper (unit tested).

    private function cbt_guard()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'index.php?login', 'refresh');
        $this->load->model('exam_model');
        $this->exam_model->ensure_schema();
    }

    private function cbt_back($url, $message, $error = false)
    {
        $this->session->set_flashdata($error ? 'error_message' : 'flash_message', $message);
        redirect(base_url() . 'index.php?admin/' . $url, 'refresh');
    }

    /** "Emails: 3 sent, 1 failed" style summary of notification counts. */
    private function email_summary($counts)
    {
        if (empty($counts)) return '';
        $parts = array();
        foreach (array('sent' => 'sent', 'failed' => 'failed', 'not_configured' => 'not sent (email not configured)', 'no_email' => 'students without email') as $k => $label)
            if (!empty($counts[$k])) $parts[] = $counts[$k] . ' ' . $label;
        return $parts ? ' Emails: ' . implode(', ', $parts) . '.' : '';
    }

    private function cbt_exam_or_back($exam_id, $back = 'exam_list')
    {
        $exam = $this->exam_model->cbt_exam($exam_id);
        if (!$exam) $this->cbt_back($back, get_phrase('exam_not_found'), true);
        return $exam;
    }

    private function cbt_header_from_post()
    {
        $date = sms_parse_exam_date($this->input->post('exam_date'));
        $start = trim((string)$this->input->post('start_time'));
        $end = trim((string)$this->input->post('end_time'));
        return array(
            'title'        => trim((string)$this->input->post('title')),
            'class_id'     => (int)$this->input->post('class_id'),
            'subject_id'   => (int)$this->input->post('subject_id'),
            'session'      => trim((string)$this->input->post('session')),
            'exam_date'    => $date,
            'start_time'   => preg_match('/^\d{1,2}:\d{2}$/', $start) ? $start . ':00' : '',
            'end_time'     => preg_match('/^\d{1,2}:\d{2}$/', $end) ? $end . ':00' : null,
            'duration'     => max(0, (int)$this->input->post('duration')),
            'pass_percent' => min(100, max(0, (int)$this->input->post('pass_percent'))),
            'instructions' => trim((string)$this->input->post('instructions')),
        );
    }

    private function cbt_header_error($h)
    {
        if ($h['title'] === '') return get_phrase('exam_title_is_required');
        if (!$h['class_id'] || !$h['subject_id']) return get_phrase('select_class_and_subject');
        $sub = $this->db->get_where('subject', array('subject_id' => $h['subject_id']))->row();
        if (!$sub || (int)$sub->class_id !== $h['class_id']) return get_phrase('subject_does_not_belong_to_class');
        if ($h['exam_date'] === '' || $h['start_time'] === '') return get_phrase('exam_date_and_start_time_are_required');
        if ($h['duration'] < 1) return get_phrase('duration_must_be_at_least_1_minute');
        list($opens, $closes) = sms_cbt_window($h);
        if ($h['end_time'] && sms_cbt_ts($h['exam_date'], $h['end_time']) <= $opens) return get_phrase('end_time_must_be_after_start_time');
        return null;
    }

    /** CBT exam list. */
    function exam_list($mode = '', $exam_id = '')
    {
        $this->cbt_guard();
        if ($mode == 'delete') {
            $exam = $this->cbt_exam_or_back($exam_id);
            if ((int)$exam['submitted_count'] > 0)
                $this->cbt_back('exam_list', get_phrase('exam_has_submitted_attempts_and_cannot_be_deleted'), true);
            $this->exam_model->delete_cbt_exam($exam_id);
            $this->cbt_back('exam_list', get_phrase('data_deleted'));
        }
        $now = time();
        $exams = $this->exam_model->cbt_exams();
        foreach ($exams as &$e) $e['state'] = sms_cbt_state($e, $now);
        $page_data['exams']      = $exams;
        $page_data['page_name']  = 'exam_list';
        $page_data['page_title'] = get_phrase('cbt_exams');
        $this->load->view('backend/index', $page_data);
    }

    /** Create a CBT exam header + blank questions, then continue to the question editor. */
    function exam_add($param1 = '')
    {
        $this->cbt_guard();
        if ($param1 == 'create') {
            $h = $this->cbt_header_from_post();
            $error = $this->cbt_header_error($h);
            $count = (int)$this->input->post('question_count');
            if ($error === null && ($count < 1 || $count > 200)) $error = get_phrase('question_count_must_be_1_to_200');
            if ($error !== null) {
                $this->session->set_flashdata('error_message', $error);
                $this->session->set_flashdata('exam_form', $this->input->post());
                redirect(base_url() . 'index.php?admin/exam_add', 'refresh');
            }
            $exam_id = $this->exam_model->create_cbt_exam($h, $count, (int)$this->input->post('options_per_question'));
            $this->cbt_back('exam_view/' . $exam_id, get_phrase('exam_created_now_enter_the_questions'));
        }
        $page_data['form']       = (array)$this->session->flashdata('exam_form');
        $page_data['classes']    = $this->db->order_by('name', 'ASC')->get('class')->result_array();
        $page_data['subjects']   = $this->db->order_by('name', 'ASC')->get('subject')->result_array();
        $page_data['session']    = $this->exam_model->setting('session');
        $page_data['page_name']  = 'exam_add';
        $page_data['page_title'] = get_phrase('add_cbt_exam');
        $this->load->view('backend/index', $page_data);
    }

    /** Question editor + exam settings + publish for one CBT exam. */
    function exam_view($exam_id = '', $action = '', $question_id = '')
    {
        $this->cbt_guard();
        $exam = $this->cbt_exam_or_back($exam_id);
        $started = $this->db->where('exam_id', (int)$exam_id)->where_in('status', array('in_progress', 'submitted', 'checked'))->count_all_results('exam_assignment');
        $locked = $started > 0;   // questions are frozen once anyone has started
        $here = 'exam_view/' . (int)$exam_id;

        if ($action == 'save_settings') {
            $h = $this->cbt_header_from_post();
            if ($locked) { $h['class_id'] = $exam['class_id']; $h['subject_id'] = $exam['subject_id']; }
            $error = $this->cbt_header_error($h);
            if ($error !== null) $this->cbt_back($here, $error, true);
            $this->exam_model->update_cbt_exam($exam_id, $h);
            $this->cbt_back($here, get_phrase('exam_settings_saved'));
        }
        if (in_array($action, array('save_question', 'add_question', 'delete_question'), true) && $locked)
            $this->cbt_back($here, get_phrase('questions_are_locked_because_students_have_started'), true);
        if ($action == 'save_question') {
            $q = $this->db->get_where('question', array('question_id' => (int)$question_id, 'exam_id' => (int)$exam_id))->row();
            if (!$q) $this->cbt_back($here, get_phrase('question_not_found'), true);
            $options = (array)$this->input->post('options');
            $correct = strtoupper(trim((string)$this->input->post('correct_answers')));
            if (trim((string)$this->input->post('question')) === '') $this->cbt_back($here, get_phrase('question_text_is_required'), true);
            if (!isset($options[$correct]) || trim((string)$options[$correct]) === '')
                $this->cbt_back($here, get_phrase('correct_answer_must_be_a_filled_option'), true);
            $this->exam_model->save_question($question_id, $this->input->post('question'), $options, $correct, $this->input->post('marks'));
            $this->cbt_back($here, get_phrase('question_saved'));
        }
        if ($action == 'add_question') {
            $raw = $this->db->get_where('cbt_exam', array('exam_id' => (int)$exam_id))->row_array();
            $this->exam_model->add_question($raw);
            $this->cbt_back($here, get_phrase('question_added'));
        }
        if ($action == 'delete_question') {
            $this->exam_model->delete_question($question_id);
            $this->cbt_back($here, get_phrase('question_deleted'));
        }
        if ($action == 'publish') {
            $problems = sms_cbt_publish_problems($exam, $this->exam_model->cbt_questions($exam_id));
            if ($problems) {
                $this->session->set_flashdata('publish_problems', $problems);
                $this->cbt_back($here, get_phrase('exam_cannot_be_published_yet'), true);
            }
            $this->exam_model->update_cbt_exam($exam_id, array('status' => 'published', 'published_at' => time()));
            // Students assigned while it was a draft get their "exam scheduled" email now.
            $pending = array_column(array_filter($this->exam_model->assignments($exam_id), function ($a) { return empty($a['notified_at']); }), 'student_id');
            $this->load->model('email_model');
            $counts = $this->email_model->notify_cbt_scheduled($exam_id, $pending);
            $this->cbt_back($here, get_phrase('exam_published') . '. ' . get_phrase('you_can_now_assign_it_to_students') . '.' . $this->email_summary($counts));
        }
        if ($action == 'unpublish') {
            if ($locked) $this->cbt_back($here, get_phrase('exam_cannot_be_unpublished_after_students_started'), true);
            $this->exam_model->update_cbt_exam($exam_id, array('status' => 'draft'));
            $this->cbt_back($here, get_phrase('exam_moved_back_to_draft'));
        }

        $page_data['exam']       = $exam;
        $page_data['state']      = sms_cbt_state($exam, time());
        $page_data['questions']  = $this->exam_model->cbt_questions($exam_id);
        $page_data['locked']     = $locked;
        $page_data['problems']   = (array)$this->session->flashdata('publish_problems');
        $page_data['classes']    = $this->db->order_by('name', 'ASC')->get('class')->result_array();
        $page_data['subjects']   = $this->db->order_by('name', 'ASC')->get('subject')->result_array();
        $page_data['page_name']  = 'exam_view';
        $page_data['page_title'] = get_phrase('cbt_exam') . ': ' . $exam['title'];
        $this->load->view('backend/index', $page_data);
    }

    /** Assign a published CBT exam to students of its class (emails each newly assigned student). */
    function exam_assign($exam_id = '', $action = '', $student_id = '')
    {
        $this->cbt_guard();
        if ($exam_id !== '') {
            $exam = $this->cbt_exam_or_back($exam_id, 'exam_assign');
            $here = 'exam_assign/' . (int)$exam_id;
            if ($action == 'save') {
                if ($exam['status'] !== 'published') $this->cbt_back($here, get_phrase('publish_the_exam_before_assigning_it'), true);
                if (sms_cbt_state($exam, time()) === 'closed') $this->cbt_back($here, get_phrase('this_exam_has_already_closed'), true);
                $ids = (array)$this->input->post('student_ids');
                if (!$ids) $this->cbt_back($here, get_phrase('select_at_least_one_student'), true);
                $new = $this->exam_model->assign_students($exam_id, $ids);
                $this->load->model('email_model');
                $counts = $this->email_model->notify_cbt_scheduled($exam_id, $new);
                $this->cbt_back($here, count($new) . ' ' . get_phrase('students_assigned') . '.' . $this->email_summary($counts));
            }
            if ($action == 'remove') {
                $ok = $this->exam_model->unassign_student($exam_id, $student_id);
                $this->cbt_back($here, get_phrase($ok ? 'student_removed_from_exam' : 'cannot_remove_a_student_who_has_started'), !$ok);
            }
            $assigned = array();
            foreach ($this->exam_model->assignments($exam_id) as $a) $assigned[$a['student_id']] = $a;
            $page_data['exam']     = $exam;
            $page_data['state']    = sms_cbt_state($exam, time());
            $page_data['students'] = $this->exam_model->active_students_of_class($exam['class_id']);
            $page_data['assigned'] = $assigned;
        }
        $now = time();
        $exams = $this->exam_model->cbt_exams();
        foreach ($exams as &$e) $e['state'] = sms_cbt_state($e, $now);
        $page_data['exams']      = $exams;
        $page_data['page_name']  = 'exam_assign';
        $page_data['page_title'] = get_phrase('assign_exam_to_students');
        $this->load->view('backend/index', $page_data);
    }

    /** Review submitted attempts; MCQs are auto-marked, the teacher can adjust marks per question. */
    function exam_paper_check($exam_id = '', $student_id = '', $action = '')
    {
        $this->cbt_guard();
        if ($exam_id !== '') {
            $exam = $this->cbt_exam_or_back($exam_id, 'exam_paper_check');
            $this->exam_model->auto_submit_expired($exam, time());
            if ($student_id !== '') {
                $a = $this->exam_model->assignment($exam_id, $student_id);
                if (!$a || !in_array($a['status'], array('submitted', 'checked'), true))
                    $this->cbt_back('exam_paper_check/' . (int)$exam_id, get_phrase('this_student_has_not_submitted_yet'), true);
                if ($action == 'save') {
                    $score = $this->exam_model->override_marks($exam_id, $student_id, (array)$this->input->post('awarded'));
                    $note = $exam['results_published'] ? ' ' . get_phrase('results_already_published_student_sees_new_marks') : '';
                    $this->cbt_back('exam_paper_check/' . (int)$exam_id, get_phrase('marks_saved') . ': ' . $score . '.' . $note);
                }
                $page_data['student']   = $this->exam_model->student($student_id);
                $page_data['attempt']   = $a;
                $page_data['questions'] = $this->exam_model->cbt_questions($exam_id);
                $page_data['answers']   = $this->exam_model->student_answers($exam_id, $student_id);
            }
            $page_data['exam']        = $this->exam_model->cbt_exam($exam_id);
            $page_data['assignments'] = $this->exam_model->assignments($exam_id);
        }
        $page_data['exams']      = $this->exam_model->cbt_exams(array('e.status' => 'published'));
        $page_data['page_name']  = 'exam_paper_check';
        $page_data['page_title'] = get_phrase('paper_checking');
        $this->load->view('backend/index', $page_data);
    }

    /** Results per exam: score, %, pass/fail, rank; publish emails each student (and parent). */
    function exam_result_list($exam_id = '', $action = '')
    {
        $this->cbt_guard();
        if ($exam_id !== '') {
            $exam = $this->cbt_exam_or_back($exam_id, 'exam_result_list');
            $here = 'exam_result_list/' . (int)$exam_id;
            $this->exam_model->auto_submit_expired($exam, time());
            if ($action == 'publish') {
                $results = $this->exam_model->cbt_results($exam_id);
                $finished = array_filter($results, function ($r) { return $r['percent'] !== null; });
                if (!$finished) $this->cbt_back($here, get_phrase('no_submitted_attempts_to_publish'), true);
                $this->db->where('exam_id', (int)$exam_id)->update('cbt_exam', array('results_published' => 1, 'results_published_at' => time()));
                $this->load->model('email_model');
                $counts = $this->email_model->notify_cbt_results($exam_id);
                $this->cbt_back($here, get_phrase('results_published') . '.' . $this->email_summary($counts));
            }
            if ($action == 'unpublish') {
                $this->db->where('exam_id', (int)$exam_id)->update('cbt_exam', array('results_published' => 0));
                $this->cbt_back($here, get_phrase('results_hidden_from_students'));
            }
            $page_data['exam']    = $this->exam_model->cbt_exam($exam_id);
            $page_data['results'] = $this->exam_model->cbt_results($exam_id);
        }
        $page_data['exams']      = $this->exam_model->cbt_exams(array('e.status' => 'published'));
        $page_data['page_name']  = 'exam_result_list';
        $page_data['page_title'] = get_phrase('cbt_results');
        $this->load->view('backend/index', $page_data);
    }

    function exam_result_detail($exam_id = '', $student_id = '')
    {
        $this->cbt_guard();
        $exam = $this->cbt_exam_or_back($exam_id, 'exam_result_list');
        $a = $this->exam_model->assignment($exam_id, $student_id);
        if (!$a) $this->cbt_back('exam_result_list/' . (int)$exam_id, get_phrase('student_not_assigned_to_this_exam'), true);
        $page_data['exam']       = $exam;
        $page_data['attempt']    = $a;
        $page_data['student']    = $this->exam_model->student($student_id);
        $page_data['questions']  = $this->exam_model->cbt_questions($exam_id);
        $page_data['answers']    = $this->exam_model->student_answers($exam_id, $student_id);
        $page_data['page_name']  = 'exam_result_detail';
        $page_data['page_title'] = get_phrase('exam_result');
        $this->load->view('backend/index', $page_data);
    }

    /* ---------------- Email preview (eye button) ---------------- */

    /** Show the exact email a notification will send (before sending), or a logged email. URL: email_preview/<type>/<ref_id>/<extra> */
    function email_preview($type = '', $ref_id = 0, $extra = 0)
    {
        $this->cbt_guard();
        $this->load->model('email_model');
        $p = $this->email_model->preview($type, (int)$ref_id, (int)$extra);
        if ($p === null) show_404();
        $this->load->view('backend/admin/email_preview', array('p' => $p));
    }


    /* ---------------- Menu permissions (teacher / parent / student portals) ---------------- */

    function menu_permissions($action = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'index.php?login', 'refresh');
        $this->load->model('portal_model');
        if ($action == 'save') {
            // Matrix rules (locked menus always on) live in sms_portal_helper (unit tested).
            $this->portal_model->save_permissions(sms_menu_permissions_from_post((array)$this->input->post('perm')));
            $this->session->set_flashdata('flash_message', get_phrase('menu_permissions_saved'));
            redirect(base_url() . 'index.php?admin/menu_permissions', 'refresh');
        }
        if ($action == 'reset') {
            $this->portal_model->save_permissions(array());
            $this->session->set_flashdata('flash_message', get_phrase('menu_permissions_reset_to_default'));
            redirect(base_url() . 'index.php?admin/menu_permissions', 'refresh');
        }
        $page_data['menus']      = sms_portal_menus();
        $page_data['perms']      = $this->portal_model->permissions();
        $page_data['page_name']  = 'menu_permissions';
        $page_data['page_title'] = get_phrase('menu_permissions');
        $this->load->view('backend/index', $page_data);
    }

    /* ---------------- Theme & colours ---------------- */

    function theme_settings($action = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url() . 'index.php?login', 'refresh');
        $set = function ($type, $value) {
            if ($this->db->get_where('settings', array('type' => $type))->row())
                $this->db->where('type', $type)->update('settings', array('description' => $value));
            else
                $this->db->insert('settings', array('type' => $type, 'description' => $value));
        };
        if ($action == 'save') {
            $preset = (string)$this->input->post('ui_theme');
            if (!isset(sms_theme_presets()[$preset])) $preset = 'sunshine';
            $use_custom = (bool)$this->input->post('use_custom');
            $primary = (string)$this->input->post('ui_primary');
            $accent  = (string)$this->input->post('ui_accent');
            $font    = in_array($this->input->post('ui_font'), array('nunito', 'baloo', 'system'), true) ? $this->input->post('ui_font') : 'nunito';
            $set('ui_theme', $preset);
            $set('ui_primary', $use_custom && sms_valid_hex_color($primary) ? strtolower($primary) : '');
            $set('ui_accent', $use_custom && sms_valid_hex_color($accent) ? strtolower($accent) : '');
            $set('ui_font', $font);
            $this->session->set_flashdata('flash_message', get_phrase('theme_saved'));
            redirect(base_url() . 'index.php?admin/theme_settings', 'refresh');
        }
        $current = array('ui_theme' => 'sunshine', 'ui_primary' => '', 'ui_accent' => '', 'ui_font' => 'nunito');
        foreach ($this->db->where_in('type', array_keys($current))->get('settings')->result_array() as $r)
            if ($r['description'] !== '') $current[$r['type']] = $r['description'];
        $page_data['current']    = $current;
        $page_data['presets']    = sms_theme_presets();
        $page_data['page_name']  = 'theme_settings';
        $page_data['page_title'] = get_phrase('theme_and_colours');
        $this->load->view('backend/index', $page_data);
    }

    /* ---------------- Email settings & log ---------------- */

    function email_settings($action = '', $id = '')
    {
        $this->cbt_guard();
        $this->load->model('email_model');
        if ($action == 'save') {
            $fields = array('smtp_host', 'smtp_port', 'smtp_crypto', 'smtp_user');
            foreach ($fields as $f) $this->db->where('type', $f)->update('settings', array('description' => trim((string)$this->input->post($f))));
            // Gmail shows app passwords in groups of 4 ("abcd efgh ..."); spaces are not part of it.
            $pass = str_replace(' ', '', (string)$this->input->post('smtp_pass'));
            if ($pass !== '') $this->db->where('type', 'smtp_pass')->update('settings', array('description' => $pass));
            $this->db->where('type', 'email_enabled')->update('settings', array('description' => $this->input->post('email_enabled') ? '1' : '0'));
            $this->db->where('type', 'email_copy_parent')->update('settings', array('description' => $this->input->post('email_copy_parent') ? '1' : '0'));
            $this->cbt_back('email_settings', get_phrase('settings_updated'));
        }
        if ($action == 'test') {
            $to = trim((string)$this->input->post('test_email'));
            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) $this->cbt_back('email_settings', get_phrase('invalid_email_address'), true);
            $school = $this->exam_model->school();
            $status = $this->email_model->send_logged($to, 'Test email from ' . $school['name'],
                sms_email_wrap($school, 'Email is working', '<p>This test email confirms that ' . htmlspecialchars($school['name']) . ' can send emails to students and parents.</p>'),
                array('event' => 'test'));
            if ($status === 'sent') $this->cbt_back('email_settings', get_phrase('test_email_sent_to') . ' ' . $to);
            $this->cbt_back('email_settings', get_phrase('test_email_failed') . ': ' . mb_substr($this->email_model->last_error, 0, 300), true);
        }
        if ($action == 'resend') {
            $status = $this->email_model->resend_log($id);
            $this->cbt_back('email_settings', $status === 'sent' ? get_phrase('email_sent') : get_phrase('email_failed') . ': ' . mb_substr($this->email_model->last_error, 0, 300), $status !== 'sent');
        }
        $page_data['settings'] = array();
        foreach (array('smtp_host', 'smtp_port', 'smtp_crypto', 'smtp_user', 'smtp_pass', 'email_enabled', 'email_copy_parent', 'cron_key') as $f)
            $page_data['settings'][$f] = $this->exam_model->setting($f);
        $page_data['configured'] = $this->email_model->is_configured();
        $page_data['logs']       = $this->db->order_by('log_id', 'DESC')->limit(100)->get('email_log')->result_array();
        $page_data['page_name']  = 'email_settings';
        $page_data['page_title'] = get_phrase('email_settings');
        $this->load->view('backend/index', $page_data);
    }

    /****TEST WHATSAPP FUNCTIONALITY*****/
    public function sendmessagetest()
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect('login', 'refresh');

        if ($this->input->post('submit')) {
            $phone = $this->input->post('phone');
            $message = $this->input->post('message');

            if (empty($phone)) {
                $this->session->set_flashdata('error', 'Phone number is required');
                redirect(base_url() . 'index.php?admin/sendmessagetest', 'refresh');
            }

            $result = null;
            try {
                $this->load->model('sms_model');
                $result = $this->sms_model->send_whatsapp($message, $phone);
                $this->session->set_flashdata('whatsapp_response', $result);

                if (is_array($result) && !empty($result['success'])) {
                    $this->session->set_flashdata('success', 'Twilio accepted the request. SID: ' . $result['sid'] . ' | Status: ' . $result['status']);
                } else {
                    $err = is_array($result) && !empty($result['error']) ? $result['error'] : 'Unknown error (model returned ' . var_export($result, true) . ')';
                    $this->session->set_flashdata('error', 'Error sending message: ' . $err);
                }
            } catch (Exception $e) {
                $this->session->set_flashdata('error', 'Exception: ' . $e->getMessage());
                $result = array('success' => false, 'error_message' => $e->getMessage());
                $this->session->set_flashdata('whatsapp_response', $result);
            }

            $this->db->insert('whatsapp_log', array(
                'student_id'    => null,
                'event_type'    => 'manual_test',
                'phone_to'      => isset($result['to']) ? $result['to'] : ('whatsapp:' . $phone),
                'phone_from'    => isset($result['from']) ? $result['from'] : null,
                'message_body'  => $message,
                'success'       => !empty($result['success']) ? 1 : 0,
                'http_code'     => isset($result['http_code']) ? $result['http_code'] : null,
                'twilio_sid'    => isset($result['sid']) ? $result['sid'] : null,
                'twilio_status' => isset($result['status']) ? $result['status'] : null,
                'error_code'    => isset($result['error_code']) ? $result['error_code'] : null,
                'error_message' => isset($result['error_message']) ? $result['error_message'] : null,
                'raw_response'  => isset($result['raw_response']) ? $result['raw_response'] : null,
                'response_payload' => is_array($result) ? json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : null,
                'created_at'    => date('Y-m-d H:i:s'),
            ));

            redirect(base_url() . 'index.php?admin/sendmessagetest', 'refresh');
        }

        $page_data['page_name'] = 'sendmessagetest';
        $page_data['page_title'] = 'Test WhatsApp';
        $this->load->view('backend/index', $page_data);
    }

}
