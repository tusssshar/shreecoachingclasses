<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modal extends CI_Controller {

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

	
	function __construct()
    {
        parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->ensure_board_table();
		/*cache control*/
		$this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
    }
	
	/***default functin, redirects to login page if no admin logged in yet***/
	public function index()
	{
		
	}
	
	
	/*
	*	$page_name		=	The name of page
	*/
	function popup($page_name = '' , $param2 = '' , $param3 = '')
	{
		$account_type = $this->session->userdata('login_type');
		if (empty($account_type)) {
			$account_type = 'admin';
		}

		$page_data['param2'] = $param2;
		$page_data['param3'] = $param3;

		// ✅ IMPORTANT: load admin model/controller function
		//$this->load->model('Admin_model'); // or your model name

		// ✅ Pass fee summary ONLY for student edit modal
		if ($page_name == 'modal_student_edit' && !empty($param2)) {
			$page_data['fee_summary'] = $this->getStudentFeeSummary($param2);
		} else {
			$page_data['fee_summary'] = [
				'total_fees' => 0,
				'paid' => 0,
				'remaining' => 0
			];
		}

		$this->load->view('backend/'.$account_type.'/'.$page_name.'.php', $page_data);

		echo '<script src="assets/js/neon-custom-ajax.js"></script>';
	}

	public function getStudentFeeSummary($student_id)
{
    $student = $this->db->get_where('student', array(
        'student_id' => $student_id
    ))->row_array();

    if (!$student) {
        return array('total_fees' => 0, 'paid' => 0, 'remaining' => 0);
    }

    $total_fees = floatval(!empty($student['total_fees']) ? $student['total_fees'] : 0);

    // Sum from student_payment_history (newer canonical source for individual entries)
    $hist_row = $this->db->select_sum('amount')
        ->where('student_id', $student_id)
        ->get('student_payment_history')
        ->row();
    $hist_paid = !empty($hist_row->amount) ? floatval($hist_row->amount) : 0;

    // Sum from legacy `payment` table (older entries before history table existed)
    $legacy_paid = 0;
    if ($this->db->table_exists('payment')) {
        $pay_row = $this->db->select_sum('amount')
            ->where('student_id', $student_id)
            ->get('payment')
            ->row();
        $legacy_paid = !empty($pay_row->amount) ? floatval($pay_row->amount) : 0;
    }

    // Aggregate cached on the student row (kept in sync by create/update flow)
    $student_paid = floatval(!empty($student['payment_done']) ? $student['payment_done'] : 0);

    // Prefer the highest figure — if history is the most up-to-date use it,
    // but never undershoot a manually-entered payment_done value.
    $total_paid = max($hist_paid, $legacy_paid, $student_paid);

    return array(
        'total_fees' => $total_fees,
        'paid'       => $total_paid,
        'remaining'  => max($total_fees - $total_paid, 0)
    );
}
}
