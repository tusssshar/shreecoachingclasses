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
            'payment_mode' => array('Cash', 'Online', 'Cheque'),
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
                  PRIMARY KEY (`id`),
                  KEY `student_id` (`student_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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

			$header_map = array();
			foreach ($header_row as $i => $col) {
				$normalized = trim(strtolower(preg_replace('/[^a-z0-9]+/', '_', (string)$col)));
				if ($normalized !== '') $header_map[$normalized] = $i;
			}

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
					foreach ((array)$keys as $key) {
						$normalized = trim(strtolower(preg_replace('/[^a-z0-9]+/', '_', $key)));
						if (isset($header_map[$normalized]) && isset($r[$header_map[$normalized]])) {
							return trim((string)$r[$header_map[$normalized]]);
						}
					}
					return '';
				};

				$first_name  = $cell(array('first_name'));
				$middle_name = $cell(array('middle_name'));
				$last_name   = $cell(array('last_name'));
				$full_name   = $cell(array('name', 'full_name'));
				if ($first_name === '' && $full_name !== '') {
					$parts = preg_split('/\s+/', $full_name);
					$first_name = array_shift($parts);
					if (count($parts) === 1) $last_name = array_pop($parts);
					elseif (count($parts) > 1) { $last_name = array_pop($parts); $middle_name = implode(' ', $parts); }
				}

				$data = array(
					'first_name'        => $first_name,
					'middle_name'       => $middle_name,
					'last_name'         => $last_name,
					'name'              => trim($first_name . ' ' . $middle_name . ' ' . $last_name),
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
		
		$page_data['page_name']  	= 'student_information';
		$page_data['page_title'] 	= get_phrase('student_information');
		$page_data['class_id'] 	= $class_id;
		$page_data['students'] = $this->crud_model->get_students($filters);
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

            case 'banar':
                return [
                    'title'   => get_phrase('bannar_information_page'),
                    'filename'=> 'banners_' . date('Ymd'),
                    'columns' => [
                        ['label' => 'Banner ID', 'key' => 'banar_id'],
                        ['label' => get_phrase('b_text_one'), 'key' => 'b_namea'],
                        ['label' => get_phrase('b_text_two'), 'key' => 'b_nameb']
                    ],
                    'rows'    => $this->db->get('banar')->result_array()
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
            'name'              => trim(
                $this->input->post('first_name').' '.
                $this->input->post('middle_name').' '.
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
            'password'          => password_hash('password', PASSWORD_BCRYPT)
        ];

        $this->db->insert('student', $data);
        $student_id = $this->db->insert_id();

        // ================= INSERT PAYMENTS (HISTORY) =================
        $payments = $this->extractPaymentsFromPost();
        $total_payment = 0;

        foreach ($payments as $p) {
            $total_payment += $p['amount'];

            $this->db->insert('student_payment_history', [
                'student_id'   => $student_id,
                'invoice_id'   => 0,
                'title'        => 'Payment',
                'payment_type' => $p['type'],
                'method'       => $p['mode'],
                'description'  => 'Payment entry',
                'amount'       => $p['amount'],
                'timestamp'    => $p['date'] ? strtotime($p['date']) : time()
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

                $whatsapp_message = strtr($template, array(
                    '{{studentname}}' => $data['name'],
                    '{{studentid}}'   => $student_id,
                    '{{schoolname}}'  => $school_name,
                    '{{class}}'       => $data['standard'],
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
            'name'              => trim(
                $this->input->post('first_name').' '.
                $this->input->post('middle_name').' '.
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
            'is_alumni'         => $this->input->post('is_alumni') ? 1 : 0
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
                    'index' => $i,
                    'amount' => $amount,
                    'date' => $this->input->post('payment'.$i.'_date'),
                    'type' => $this->input->post('payment'.$i.'_type'),
                    'mode' => $this->input->post('payment'.$i.'_mode')
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

        // school header for slip
        $school = array();
        $name_row    = $this->db->get_where('settings', array('type' => 'system_name'))->row();
        $school['name']    = $name_row ? $name_row->description : 'School';
        $address_row = $this->db->get_where('settings', array('type' => 'address'))->row();
        $school['address'] = $address_row ? $address_row->description : '';

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
            'title'       => 'School Fees',
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
    function banar($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            $data['b_namea']        = $this->input->post('b_namea');
            $data['b_nameb']    = $this->input->post('b_nameb');
			
            $this->db->insert('banar', $data);
            $banar_id = $this->db->insert_id();
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/banner_image/' . $banar_id . '.jpg');
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/banar', 'refresh');
        }
        if ($param1 == 'do_update') {
             $data['b_namea']        = $this->input->post('b_namea');
            $data['b_nameb']    = $this->input->post('b_nameb');
            
            $this->db->where('banar_id', $param2);
            $this->db->update('banar', $data);
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/banner_image/' . $param2 . '.jpg');
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/banar', 'refresh');
        } else if ($param1 == 'personal_profile') {
            $page_data['personal_profile']   = true;
            $page_data['current_banar_id'] = $param2;
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('banar', array(
                'banar_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('banar_id', $param2);
            $this->db->delete('banar');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/banar', 'refresh');
        }
        $page_data['banars']   = $this->db->get('banar')->result_array();
        $page_data['page_name']  = 'banar';
        $page_data['page_title'] = get_phrase('manage_banar');
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
        $page_data['page_title'] = get_phrase('manage_session');
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
	
	
	
	
	
	
	
		 /****MANAGE AAL ENQUIRY SETTINGS*****/
    function enquiry($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            $data['category']       = $this->input->post('category');
            $data['mobile']		  	= $this->input->post('mobile');
            $data['purpose']		= $this->input->post('purpose');
            $data['name']		  	= $this->input->post('name');
            $data['whom']   		= $this->input->post('whom');
            $this->db->insert('enquiry', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/enquiry/', 'refresh');
        }
		
		if ($param1 == 'do_update') {
          	$data['category']       = $this->input->post('category');
            $data['mobile']		  	= $this->input->post('mobile');
            $data['purpose']		= $this->input->post('purpose');
            $data['name']		  	= $this->input->post('name');
            $data['whom']   		= $this->input->post('whom');
            
            $this->db->where('enquiry_id', $param2);
            $this->db->update('enquiry', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/enquiry/', 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('enquiry', array(
                'enquiry_id' => $param2
            ))->result_array();
        }
		
        if ($param1 == 'delete') {
            $this->db->where('enquiry_id', $param2);
            $this->db->delete('enquiry');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/enquiry/', 'refresh');
        }
        $page_data['enquiry_setting']    = $this->db->get('enquiry')->result_array();
        $page_data['page_name']  = 'enquiry';
        $page_data['page_title'] = get_phrase('manage_enquiries');
        $this->load->view('backend/index', $page_data);
    }
	

    /****MANAGE SECTIONS*****/
    function section($class_id = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

        $page_data['page_name']  = 'section';
        $page_data['page_title'] = 'Manage Teachers Time Table';
        $page_data['class_id']   = $class_id;
        $this->load->view('backend/index', $page_data);    
    }

    function sections($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');

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
            'end_time'   => $this->input->post('end_time')
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
    function exam($param1 = '', $param2 = '' , $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            $data['name']    = $this->input->post('name');
            $data['date']    = $this->input->post('date');
            $data['comment'] = $this->input->post('comment');
            $this->db->insert('exam', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/exam/', 'refresh');
        }
        if ($param1 == 'edit' && $param2 == 'do_update') {
            $data['name']    = $this->input->post('name');
            $data['date']    = $this->input->post('date');
            $data['comment'] = $this->input->post('comment');
            
            $this->db->where('exam_id', $param3);
            $this->db->update('exam', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/exam/', 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('exam', array(
                'exam_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('exam_id', $param2);
            $this->db->delete('exam');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/exam/', 'refresh');
        }
        $page_data['exams']      = $this->db->get('exam')->result_array();
        $page_data['page_name']  = 'exam';
        $page_data['page_title'] = get_phrase('manage_exam');
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

            $data['description'] = $this->input->post('vission');
            $this->db->where('type' , 'vission');
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
    function marks($exam_id = '', $class_id = '', $subject_id = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        
        if ($this->input->post('operation') == 'selection') {
            $page_data['exam_id']    = $this->input->post('exam_id');
            $page_data['class_id']   = $this->input->post('class_id');
            $page_data['subject_id'] = $this->input->post('subject_id');
            
            if ($page_data['exam_id'] > 0 && $page_data['class_id'] > 0 && $page_data['subject_id'] > 0) {
                redirect(base_url() . 'index.php?admin/marks/' . $page_data['exam_id'] . '/' . $page_data['class_id'] . '/' . $page_data['subject_id'], 'refresh');
            } else {
                $this->session->set_flashdata('mark_message', 'Choose exam, class and subject');
                redirect(base_url() . 'index.php?admin/marks/', 'refresh');
            }
        }
        if ($this->input->post('operation') == 'update') {
            $students = $this->db->get_where('student' , array('class_id' => $class_id))->result_array();
            foreach($students as $row) {
                $data['mark_obtained'] = $this->input->post('mark_obtained_' . $row['student_id']);
                $data['comment']       = $this->input->post('comment_' . $row['student_id']);
                
                $this->db->where('mark_id', $this->input->post('mark_id_' . $row['student_id']));
                $this->db->update('mark', array('mark_obtained' => $data['mark_obtained'] , 'comment' => $data['comment']));
            }
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/marks/' . $this->input->post('exam_id') . '/' . $this->input->post('class_id') . '/' . $this->input->post('subject_id'), 'refresh');
        }
        $page_data['exam_id']    = $exam_id;
        $page_data['class_id']   = $class_id;
        $page_data['subject_id'] = $subject_id;
        
        $page_data['page_info'] = 'Exam marks';
        
        $page_data['page_name']  = 'marks';
        $page_data['page_title'] = get_phrase('manage_exam_marks');
        $this->load->view('backend/index', $page_data);
    }

    // TABULATION SHEET
    function tabulation_sheet($class_id = '' , $exam_id = '') {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
       
       
        $page_data['page_name']  = 'tabulation_sheet';
        $page_data['page_title'] = get_phrase('tabulation_sheet');
        $this->load->view('backend/index', $page_data);
    
    }

    
    
    /****MANAGE GRADES*****/
    function grade($param1 = '', $param2 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
            redirect(base_url(), 'refresh');
        if ($param1 == 'create') {
            $data['name']        = $this->input->post('name');
            $data['grade_point'] = $this->input->post('grade_point');
            $data['mark_from']   = $this->input->post('mark_from');
            $data['mark_upto']   = $this->input->post('mark_upto');
            $data['comment']     = $this->input->post('comment');
            $this->db->insert('grade', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_added_successfully'));
            redirect(base_url() . 'index.php?admin/grade/', 'refresh');
        }
        if ($param1 == 'do_update') {
            $data['name']        = $this->input->post('name');
            $data['grade_point'] = $this->input->post('grade_point');
            $data['mark_from']   = $this->input->post('mark_from');
            $data['mark_upto']   = $this->input->post('mark_upto');
            $data['comment']     = $this->input->post('comment');
            
            $this->db->where('grade_id', $param2);
            $this->db->update('grade', $data);
            $this->session->set_flashdata('flash_message' , get_phrase('data_updated'));
            redirect(base_url() . 'index.php?admin/grade/', 'refresh');
        } else if ($param1 == 'edit') {
            $page_data['edit_data'] = $this->db->get_where('grade', array(
                'grade_id' => $param2
            ))->result_array();
        }
        if ($param1 == 'delete') {
            $this->db->where('grade_id', $param2);
            $this->db->delete('grade');
            $this->session->set_flashdata('flash_message' , get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/grade/', 'refresh');
        }
        $page_data['grades']     = $this->db->get('grade')->result_array();
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

            $data['description'] = $this->input->post('language');
            $this->db->where('type' , 'language');
            $this->db->update('settings' , $data);

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
    function manage_language($param1 = '', $param2 = '', $param3 = '')
    {
        if ($this->session->userdata('admin_login') != 1)
			redirect(base_url() . 'index.php?login', 'refresh');
		
		if ($param1 == 'edit_phrase') {
			$page_data['edit_profile'] 	= $param2;	
		}
		if ($param1 == 'update_phrase') {
			$language	=	$param2;
			$total_phrase	=	$this->input->post('total_phrase');
			for($i = 1 ; $i < $total_phrase ; $i++)
			{
				//$data[$language]	=	$this->input->post('phrase').$i;
				$this->db->where('phrase_id' , $i);
				$this->db->update('language' , array($language => $this->input->post('phrase'.$i)));
			}
			redirect(base_url() . 'index.php?admin/manage_language/edit_phrase/'.$language, 'refresh');
		}
		if ($param1 == 'do_update') {
			$language        = $this->input->post('language');
			$data[$language] = $this->input->post('phrase');
			$this->db->where('phrase_id', $param2);
			$this->db->update('language', $data);
			$this->session->set_flashdata('flash_message', get_phrase('settings_updated'));
			redirect(base_url() . 'index.php?admin/manage_language/', 'refresh');
		}
		if ($param1 == 'add_phrase') {
			$data['phrase'] = $this->input->post('phrase');
			$this->db->insert('language', $data);
			$this->session->set_flashdata('flash_message', get_phrase('settings_updated'));
			redirect(base_url() . 'index.php?admin/manage_language/', 'refresh');
		}
		if ($param1 == 'add_language') {
			$language = $this->input->post('language');
			$this->load->dbforge();
			$fields = array(
				$language => array(
					'type' => 'LONGTEXT'
				)
			);
			$this->dbforge->add_column('language', $fields);
			
			$this->session->set_flashdata('flash_message', get_phrase('settings_updated'));
			redirect(base_url() . 'index.php?admin/manage_language/', 'refresh');
		}
		if ($param1 == 'delete_language') {
			$language = $param2;
			$this->load->dbforge();
			$this->dbforge->drop_column('language', $language);
			$this->session->set_flashdata('flash_message', get_phrase('settings_updated'));
			
			redirect(base_url() . 'index.php?admin/manage_language/', 'refresh');
		}
		$page_data['page_name']        = 'manage_language';
		$page_data['page_title']       = get_phrase('manage_language');
		//$page_data['language_phrases'] = $this->db->get('language')->result_array();
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
        if ($param1 == 'update_profile_info') {
            $data['name']  = $this->input->post('name');
            $data['email'] = $this->input->post('email');
            
            $this->db->where('admin_id', $this->session->userdata('admin_id'));
            $this->db->update('admin', $data);
            move_uploaded_file($_FILES['userfile']['tmp_name'], 'uploads/admin_image/' . $this->session->userdata('admin_id') . '.jpg');
            $this->session->set_flashdata('flash_message', get_phrase('account_updated'));
            redirect(base_url() . 'index.php?admin/manage_profile/', 'refresh');
        }
        if ($param1 == 'change_password') {
            $data['password']             = $this->input->post('password');
            $data['new_password']         = $this->input->post('new_password');
            $data['confirm_new_password'] = $this->input->post('confirm_new_password');
            
            $current_password = $this->db->get_where('admin', array(
                'admin_id' => $this->session->userdata('admin_id')
            ))->row()->password;
            if ($current_password == $data['password'] && $data['new_password'] == $data['confirm_new_password']) {
                $this->db->where('admin_id', $this->session->userdata('admin_id'));
                $this->db->update('admin', array(
                    'password' => $data['new_password']
                ));
                $this->session->set_flashdata('flash_message', get_phrase('password_updated'));
            } else {
                $this->session->set_flashdata('flash_message', get_phrase('password_mismatch'));
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

    /**
     * Ensure CBT schema additions exist.
     *  - question.marks                (per-question marks)
     *  - exam_result.marks_awarded     (admin awarded marks during paper checking)
     *  - exam_result.status            (pending / checked)
     *  - exam_result.submitted_at      (timestamp)
     *  - exam_assignment               (new table linking student <-> exam)
     */
    private function ensure_cbt_schema()
    {
        if ($this->db->table_exists('question') && !$this->db->field_exists('marks', 'question')) {
            $this->db->query("ALTER TABLE `question` ADD `marks` INT NOT NULL DEFAULT 1");
        }
        if ($this->db->table_exists('exam_result')) {
            if (!$this->db->field_exists('marks_awarded', 'exam_result')) {
                $this->db->query("ALTER TABLE `exam_result` ADD `marks_awarded` DECIMAL(10,2) NULL");
            }
            if (!$this->db->field_exists('status', 'exam_result')) {
                $this->db->query("ALTER TABLE `exam_result` ADD `status` VARCHAR(20) DEFAULT 'submitted'");
            }
            if (!$this->db->field_exists('submitted_at', 'exam_result')) {
                $this->db->query("ALTER TABLE `exam_result` ADD `submitted_at` INT NULL");
            }
        }
        if (!$this->db->table_exists('exam_assignment')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `exam_assignment` (
                    `assignment_id` int(11) NOT NULL AUTO_INCREMENT,
                    `class_id` int(11) NOT NULL,
                    `subject_id` int(11) NOT NULL,
                    `date` date NOT NULL,
                    `duration` int(11) NOT NULL,
                    `session` varchar(255) NOT NULL DEFAULT '',
                    `student_id` int(11) NOT NULL,
                    `status` varchar(20) NOT NULL DEFAULT 'assigned',
                    `assigned_at` int(11) DEFAULT NULL,
                    `completed_at` int(11) DEFAULT NULL,
                    PRIMARY KEY (`assignment_id`),
                    KEY `exam_key` (`class_id`,`subject_id`,`date`,`duration`,`session`),
                    KEY `student_id` (`student_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
            ");
        }
    }

    /**
     * List of all CBT exams (grouped by class+subject+date+duration+session).
     * Also supports inline delete via mode='delete'.
     */
    function exam_list($mode = '', $class_id = '', $subject_id = '', $duration = '', $date = '', $session = '') {
        if ($this->session->userdata('admin_login') != 1)
            redirect('login', 'refresh');

        $this->ensure_cbt_schema();

        if ($mode == 'delete' && $class_id !== '' && $subject_id !== '' && $duration !== '' && $date !== '') {
            if ($session == '%null') $session = '';

            $class_id = (int)$class_id;
            $subject_id = (int)$subject_id;
            $duration = (int)$duration;

            $qids = $this->db->select('question_id')->from('question')
                ->where(array('class_id' => $class_id, 'subject_id' => $subject_id, 'duration' => $duration, 'date' => $date, 'session' => $session))
                ->get()->result_array();

            if (!empty($qids)) {
                $ids = array_map(function ($r) { return (int)$r['question_id']; }, $qids);
                $this->db->where_in('question_id', $ids)->delete('answer');
                $this->db->where_in('question_id', $ids)->delete('exam_result');
                $this->db->where_in('question_id', $ids)->delete('question');
            }

            $this->db->where(array('class_id' => $class_id, 'subject_id' => $subject_id, 'duration' => $duration, 'date' => $date, 'session' => $session))
                ->delete('exam_assignment');

            $this->session->set_flashdata('flash_message', get_phrase('data_deleted'));
            redirect(base_url() . 'index.php?admin/exam_list', 'refresh');
        }

        $query = "select a.class_id, a.subject_id, a.date, a.duration, a.session, a.question_count, "
               . "b.name class_name, c.name subject_name, count(*) actual_questions, sum(a.marks) total_marks "
               . "from question a "
               . "inner join class b on a.class_id=b.class_id "
               . "inner join subject c on a.subject_id=c.subject_id "
               . "group by a.class_id, a.subject_id, a.date, a.duration, a.session "
               . "order by a.date desc, a.class_id, a.subject_id";
        $page_data['exam_groups'] = $this->db->query($query)->result_array();
        $page_data['page_name']  = 'exam_list';
        $page_data['page_title'] = get_phrase('exam_list');
        $this->load->view('backend/index', $page_data);
    }

function exam_view($class_id, $subject_id, $duration, $date, $session = '', $mode = '', $question_id = '') {
    if ($this->session->userdata('admin_login') != 1)
        redirect('login', 'refresh');

    $mode1 = $this->input->post('mode1');

    if ($session == '%null') {
        $session = '';
    }
    if ($mode == 'save') {
        $this->ensure_cbt_schema();
        $data = array();
        $data['question'] = $this->input->post('question');
        $data["correct_answers"] = $this->input->post('correct_answers');
        $marks = $this->input->post('marks');
        if ($marks !== null && $marks !== '') {
            $data['marks'] = (int)$marks;
        }
        $this->db->where('question_id', $question_id);
        $this->db->update('question', $data);

        $answers = $this->input->post('answers');
        for ($i = 0; $i < sizeof($answers); $i++) {
            $data = array();
            $this->db->where('question_id', $question_id);
            $ascii_A = ord('A');
            $this->db->where('label', chr($ascii_A + $i));
            $data["content"] = $answers[$i];
            $this->db->update('answer', $data);
        }
    } else if ($mode == 'delete') {
        $this->db->where('question_id', $question_id);
        $this->db->delete('question');
    } else if ($mode1 == 'save_exam') {
        $class_id = $this->input->post('class_id');
        $subject_id = $this->input->post('subject_id');
        $duration = $this->input->post('duration');
        $date = date("Y-m-d", strtotime($this->input->post('date')));
        $session = $this->input->post('session');
        $question_count = $this->input->post('question_count');

        $usersession = $this->session->userdata('exam_data');

        $this->db->where('class_id', $usersession['class_id']);
        $this->db->where('subject_id', $usersession['subject_id']);
        $this->db->where('duration', $usersession['duration']);
        $this->db->where('date', $usersession['date']);
        $this->db->where('session', $usersession['session']);
        $this->db->update('question', array('class_id' => $class_id, 'subject_id' => $subject_id, 'duration' => $duration, 'date' => $date, 'session' => $session, 'question_count' => $question_count));
    }

    if ($session == '%null')
        $session = '';
    $sql = "select max(b.label) as max_label from question a "
            . "inner join answer b on a.question_id=b.question_id "
            . "where a.class_id=" . $class_id . " and a.subject_id=" . $subject_id . " and a.session='" . $session . "' and a.duration='" . $duration . "' and a.date='" . $date . "'";
    $result = $this->db->query($sql)->result_array();
    $page_data['max_label'] = $result[0]['max_label'];

    $sql = "select * from question "
            . "where class_id=" . $class_id . " and subject_id=" . $subject_id . " and session='" . $session . "' and duration='" . $duration . "' and date='" . $date . "'";
    $exam_list = $this->db->query($sql)->result_array();
    $exam_data = array();
    $question_count = 0;
    foreach ($exam_list as $row) {
        $exam = array();
        $exam['question_id'] = $row['question_id'];
        $exam['class_id'] = $row['class_id'];
        $exam['subject_id'] = $row['subject_id'];
        $exam['date'] = $row['date'];
        $exam['session'] = $row['session'];
        $exam['duration'] = $row['duration'];
        $exam['question'] = $row['question'];
        $exam['correct_answers'] = $row['correct_answers'];
        $question_count = $row['question_count'];

        $sql = "select * from answer where question_id=" . $row['question_id'] . " order by label";
        $result = $this->db->query($sql)->result_array();
        foreach ($result as $row1) {
            $exam[$row1['label']] = $row1['content'];
        }
        array_push($exam_data, $exam);
    }
    $page_data['class_id'] = $class_id;
    $page_data['subject_id'] = $subject_id;
    $page_data['duration'] = $duration;

    $dates = explode('-', $date);
    $y = $dates[0];
    $m = $dates[1];
    $d = $dates[2];
    $page_data['date'] = $m . '/' . $d . '/' . $y;

    $page_data['session'] = $session;
    $page_data['question_count'] = $question_count;
    $page_data['classes'] = $this->db->get('class')->result_array();
    $page_data['subjects'] = $this->db->get_where('subject', array('class_id' => $class_id))->result_array();
    $page_data['exam_data'] = $exam_data;

    $session_data = $page_data;
    $session_data['date'] = $date;

    $page_data['page_name'] = 'exam_view';
    $page_data['page_title'] = get_phrase('view_exam');
    $this->session->set_userdata('exam_data', $session_data);
    $this->load->view('backend/index', $page_data);
}

/**
 * Add a new CBT exam header. On POST, scaffolds `question_count` blank question rows
 * with 4 blank answers each, then redirects into exam_view for editing.
 */
function exam_add($param1 = '') {
    if ($this->session->userdata('admin_login') != 1)
        redirect('login', 'refresh');

    $this->ensure_cbt_schema();

    if ($param1 == 'create' || $this->input->post('class_id')) {
        $class_id       = (int)$this->input->post('class_id');
        $subject_id     = (int)$this->input->post('subject_id');
        $duration       = (int)$this->input->post('duration');
        $session        = trim((string)$this->input->post('session'));
        $question_count = max(1, (int)$this->input->post('question_count'));
        $date_raw       = $this->input->post('date');
        $date           = date('Y-m-d', strtotime($date_raw));

        if (!$class_id || !$subject_id || !$duration || !$date_raw) {
            redirect(base_url() . 'index.php?admin/exam_add/error', 'refresh');
        }

        $existing = $this->db->get_where('question', array(
            'class_id' => $class_id, 'subject_id' => $subject_id,
            'duration' => $duration, 'date' => $date, 'session' => $session
        ))->num_rows();

        if ($existing == 0) {
            for ($i = 0; $i < $question_count; $i++) {
                $this->db->insert('question', array(
                    'class_id'        => $class_id,
                    'subject_id'      => $subject_id,
                    'date'            => $date,
                    'session'         => $session,
                    'question_count'  => $question_count,
                    'duration'        => $duration,
                    'question'        => 'Question ' . ($i + 1),
                    'correct_answers' => 'A',
                    'marks'           => 1,
                ));
                $qid = $this->db->insert_id();
                foreach (array('A', 'B', 'C', 'D') as $label) {
                    $this->db->insert('answer', array(
                        'question_id' => $qid,
                        'label'       => $label,
                        'content'     => '',
                    ));
                }
            }
        }

        $session_url = $session === '' ? '%null' : $session;
        redirect(base_url() . 'index.php?admin/exam_view/' . $class_id . '/' . $subject_id . '/' . $duration . '/' . $date . '/' . $session_url, 'refresh');
    }

    $page_data['error']     = ($param1 == 'error') ? 1 : 0;
    $page_data['page_name'] = 'exam_add';
    $page_data['page_title'] = get_phrase('add_exam');
    $page_data['classes']  = $this->db->get('class')->result_array();
    $page_data['subjects'] = $this->db->get('subject')->result_array();
    $this->load->view('backend/index', $page_data);
}

/**
 * Assign an exam to one or more students of a class. GET lists exams + students.
 * POST receives selected student ids and persists assignments.
 */
function exam_assign($mode = '') {
    if ($this->session->userdata('admin_login') != 1)
        redirect('login', 'refresh');

    $this->ensure_cbt_schema();

    if ($mode == 'save' && $this->input->post('class_id')) {
        $class_id   = (int)$this->input->post('class_id');
        $subject_id = (int)$this->input->post('subject_id');
        $duration   = (int)$this->input->post('duration');
        $date       = $this->input->post('date');
        $session    = (string)$this->input->post('session');
        $students   = $this->input->post('student_ids');

        if (is_array($students)) {
            foreach ($students as $sid) {
                $exists = $this->db->get_where('exam_assignment', array(
                    'class_id' => $class_id, 'subject_id' => $subject_id,
                    'date' => $date, 'duration' => $duration, 'session' => $session,
                    'student_id' => (int)$sid
                ))->num_rows();
                if ($exists == 0) {
                    $this->db->insert('exam_assignment', array(
                        'class_id'   => $class_id,
                        'subject_id' => $subject_id,
                        'date'       => $date,
                        'duration'   => $duration,
                        'session'    => $session,
                        'student_id' => (int)$sid,
                        'status'     => 'assigned',
                        'assigned_at'=> time(),
                    ));
                }
            }
        }
        $this->session->set_flashdata('flash_message', 'Exam assigned successfully.');
        redirect(base_url() . 'index.php?admin/exam_assign', 'refresh');
    }

    $query = "select a.class_id, a.subject_id, a.date, a.duration, a.session, "
           . "b.name class_name, c.name subject_name, count(*) actual_questions, "
           . "(select count(*) from exam_assignment x where x.class_id=a.class_id and x.subject_id=a.subject_id "
           . " and x.date=a.date and x.duration=a.duration and x.session=a.session) assigned_count "
           . "from question a "
           . "inner join class b on a.class_id=b.class_id "
           . "inner join subject c on a.subject_id=c.subject_id "
           . "group by a.class_id, a.subject_id, a.date, a.duration, a.session "
           . "order by a.date desc";
    $page_data['exam_groups'] = $this->db->query($query)->result_array();
    $page_data['page_name']   = 'exam_assign';
    $page_data['page_title']  = 'Assign Exam to Student';
    $this->load->view('backend/index', $page_data);
}

/**
 * Returns JSON list of students for a given class — used by exam_assign modal.
 */
function exam_assign_students($class_id) {
    if ($this->session->userdata('admin_login') != 1) redirect('login', 'refresh');
    $class_id = (int)$class_id;
    $this->db->where('is_active', 1);
    $students = $this->db->get_where('student', array('class_id' => $class_id))->result_array();
    header('Content-Type: application/json');
    echo json_encode($students);
}

/**
 * Online paper checking page. Admin selects an exam + a student, then sees
 * each question with the student's submitted answer and the correct answer,
 * and inputs awarded marks per question.
 */
function exam_paper_check($mode = '') {
    if ($this->session->userdata('admin_login') != 1)
        redirect('login', 'refresh');

    $this->ensure_cbt_schema();

    if ($mode == 'save' && $this->input->post('student_id')) {
        $student_id = (int)$this->input->post('student_id');
        $awarded    = $this->input->post('awarded');           // [question_id => marks]
        $answers    = $this->input->post('answer');            // [question_id => 'A'|'B'...]
        $now        = time();

        if (is_array($awarded)) {
            foreach ($awarded as $qid => $marks) {
                $qid = (int)$qid;
                $answer = isset($answers[$qid]) ? $answers[$qid] : '';

                $exists = $this->db->get_where('exam_result', array('student_id' => $student_id, 'question_id' => $qid))->row();
                $payload = array(
                    'answer'        => $answer,
                    'marks_awarded' => is_numeric($marks) ? $marks : 0,
                    'status'        => 'checked',
                    'submitted_at'  => $now,
                );
                if ($exists) {
                    $this->db->where('result_id', $exists->result_id)->update('exam_result', $payload);
                } else {
                    $payload['student_id']  = $student_id;
                    $payload['question_id'] = $qid;
                    $this->db->insert('exam_result', $payload);
                }
            }
        }

        // mark assignment completed
        $this->db->where(array(
            'class_id'   => (int)$this->input->post('class_id'),
            'subject_id' => (int)$this->input->post('subject_id'),
            'date'       => $this->input->post('date'),
            'duration'   => (int)$this->input->post('duration'),
            'session'    => (string)$this->input->post('session'),
            'student_id' => $student_id,
        ))->update('exam_assignment', array('status' => 'completed', 'completed_at' => $now));

        $this->session->set_flashdata('flash_message', 'Paper checked and marks saved.');
        redirect(base_url() . 'index.php?admin/exam_paper_check', 'refresh');
    }

    $class_id   = $this->input->get('class_id');
    $subject_id = $this->input->get('subject_id');
    $duration   = $this->input->get('duration');
    $date       = $this->input->get('date');
    $session    = $this->input->get('session');
    $student_id = (int)$this->input->get('student_id');

    if ($class_id !== null && $student_id) {
        $class_id   = (int)$class_id;
        $subject_id = (int)$subject_id;
        $duration   = (int)$duration;
        $session    = $session === null ? '' : (string)$session;

        $questions = $this->db->get_where('question', array(
            'class_id' => $class_id, 'subject_id' => $subject_id,
            'duration' => $duration, 'date' => $date, 'session' => $session
        ))->result_array();

        foreach ($questions as &$q) {
            $q['options'] = $this->db->order_by('label', 'asc')
                ->get_where('answer', array('question_id' => $q['question_id']))->result_array();
            $q['existing'] = $this->db->get_where('exam_result', array(
                'student_id' => $student_id, 'question_id' => $q['question_id']
            ))->row_array();
        }
        unset($q);

        $cls_row = $this->db->get_where('class',   array('class_id'   => $class_id))->row();
        $sub_row = $this->db->get_where('subject', array('subject_id' => $subject_id))->row();
        $page_data['questions'] = $questions;
        $page_data['student']   = $this->db->get_where('student', array('student_id' => $student_id))->row_array();
        $page_data['exam']      = array(
            'class_id'   => $class_id,
            'subject_id' => $subject_id,
            'duration'   => $duration,
            'date'       => $date,
            'session'    => $session,
            'class_name'   => $cls_row ? $cls_row->name : '',
            'subject_name' => $sub_row ? $sub_row->name : '',
        );
    }

    // exam list with assigned students for selection
    $sql = "select a.class_id, a.subject_id, a.date, a.duration, a.session, "
         . "b.name class_name, c.name subject_name, d.student_id, e.name student_name, "
         . "(select status from exam_assignment x where x.class_id=a.class_id and x.subject_id=a.subject_id "
         . " and x.date=a.date and x.duration=a.duration and x.session=a.session and x.student_id=d.student_id limit 1) status "
         . "from question a "
         . "inner join class b on a.class_id=b.class_id "
         . "inner join subject c on a.subject_id=c.subject_id "
         . "inner join exam_assignment d on d.class_id=a.class_id and d.subject_id=a.subject_id "
         . " and d.date=a.date and d.duration=a.duration and d.session=a.session "
         . "inner join student e on e.student_id=d.student_id "
         . "group by a.class_id, a.subject_id, a.date, a.duration, a.session, d.student_id "
         . "order by a.date desc, e.name";
    $page_data['assignments'] = $this->db->query($sql)->result_array();
    $page_data['page_name']   = 'exam_paper_check';
    $page_data['page_title']  = 'Online Paper Checking';
    $this->load->view('backend/index', $page_data);
}

/**
 * Result listing: per-student totals across all attempted exams.
 */
function exam_result_list() {
    if ($this->session->userdata('admin_login') != 1)
        redirect('login', 'refresh');

    $this->ensure_cbt_schema();

    $sql = "select b.class_id, b.subject_id, b.date, b.duration, b.session, "
         . "a.student_id, st.name student_name, cls.name class_name, sub.name subject_name, "
         . "sum(coalesce(a.marks_awarded, 0)) total_marks, "
         . "(select sum(marks) from question q where q.class_id=b.class_id and q.subject_id=b.subject_id "
         . " and q.date=b.date and q.duration=b.duration and q.session=b.session) total_possible, "
         . "max(a.status) status "
         . "from exam_result a "
         . "inner join question b on a.question_id=b.question_id "
         . "inner join student st on st.student_id=a.student_id "
         . "inner join class cls on cls.class_id=b.class_id "
         . "inner join subject sub on sub.subject_id=b.subject_id "
         . "group by a.student_id, b.class_id, b.subject_id, b.date, b.duration, b.session "
         . "order by b.date desc, st.name";
    $page_data['results']    = $this->db->query($sql)->result_array();
    $page_data['page_name']  = 'exam_result_list';
    $page_data['page_title'] = get_phrase('exam_result');
    $this->load->view('backend/index', $page_data);
}

function exam_result_detail() {
    if ($this->session->userdata('admin_login') != 1)
        redirect('login', 'refresh');

    $this->ensure_cbt_schema();

    $class_id   = $this->input->post('class_id')   ?: $this->input->get('class_id');
    $subject_id = $this->input->post('subject_id') ?: $this->input->get('subject_id');
    $student_id = $this->input->post('student_id') ?: $this->input->get('student_id');
    $duration   = $this->input->post('duration')   ?: $this->input->get('duration');
    $session    = $this->input->post('session');
    if ($session === null || $session === false) $session = $this->input->get('session');
    $date       = $this->input->post('date')       ?: $this->input->get('date');

    if (!$class_id || !$subject_id || !$student_id || !$date) {
        redirect(base_url() . 'index.php?admin/exam_result_list', 'refresh');
    }

    $class_id   = (int)$class_id;
    $subject_id = (int)$subject_id;
    $student_id = (int)$student_id;
    $duration   = (int)$duration;
    $session    = $session === null ? '' : (string)$session;

    $this->db->select('q.*, r.answer student_answer, r.marks_awarded, r.status, r.submitted_at');
    $this->db->from('question q');
    $this->db->join('exam_result r', 'r.question_id = q.question_id and r.student_id = ' . $student_id, 'left');
    $this->db->where(array(
        'q.class_id' => $class_id, 'q.subject_id' => $subject_id,
        'q.date' => $date, 'q.duration' => $duration, 'q.session' => $session
    ));
    $this->db->order_by('q.question_id', 'asc');
    $questions = $this->db->get()->result_array();

    foreach ($questions as &$q) {
        $q['options'] = $this->db->order_by('label', 'asc')
            ->get_where('answer', array('question_id' => $q['question_id']))->result_array();
    }
    unset($q);

    $cls_row = $this->db->get_where('class',   array('class_id'   => $class_id))->row();
    $sub_row = $this->db->get_where('subject', array('subject_id' => $subject_id))->row();
    $page_data['questions']    = $questions;
    $page_data['student']      = $this->db->get_where('student', array('student_id' => $student_id))->row_array();
    $page_data['class_name']   = $cls_row ? $cls_row->name : '';
    $page_data['subject_name'] = $sub_row ? $sub_row->name : '';
    $page_data['exam']       = array(
        'class_id' => $class_id, 'subject_id' => $subject_id,
        'date' => $date, 'duration' => $duration, 'session' => $session,
    );
    $page_data['page_name']  = 'exam_result_detail';
    $page_data['page_title'] = get_phrase('exam_result');
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
