<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_model extends CI_Model {
	
	function __construct()
    {
        parent::__construct();
    }

	function account_opening_email($account_type = '' , $email = '')
	{
		$system_name	=	$this->db->get_where('settings' , array('type' => 'system_name'))->row()->description;
		
		$email_msg		=	"Welcome to ".$system_name."<br />";
		$email_msg		.=	"Your account type : ".$account_type."<br />";
		$email_msg		.=	"Your login password : ".$this->db->get_where($account_type , array('email' => $email))->row()->password."<br />";
		$email_msg		.=	"Login Here : ".base_url()."<br />";
		
		$email_sub		=	"Account opening email";
		$email_to		=	$email;
		
		$this->do_email($email_msg , $email_sub , $email_to);
	}
	
	function password_reset_email($new_password = '' , $account_type = '' , $email = '')
	{
		$query			=	$this->db->get_where($account_type , array('email' => $email));
		if($query->num_rows() > 0)
		{
			
			$email_msg	=	"Your account type is : ".$account_type."<br />";
			$email_msg	.=	"Your password is : ".$new_password."<br />";
			
			$email_sub	=	"Password reset request";
			$email_to	=	$email;
			$this->do_email($email_msg , $email_sub , $email_to);
			return true;
		}
		else
		{	
			return false;
		}
	}
	
	function student_registration_email($email = '')
	{
		$system_name	=	$this->db->get_where('settings' , array('type' => 'system_name'))->row()->description;
		
		$email_msg		=	"Successfully registered in Shree Academy Educations<br />";
		$email_msg		.=	"Welcome to ".$system_name."<br />";
		$email_msg		.=	"Your account has been created successfully.<br />";
		
		$email_sub		=	"Registration Confirmation";
		$email_to		=	$email;
		
		$this->do_email($email_msg , $email_sub , $email_to);
	}
	
	/**
	 * Sends a single digest reminder email to one teacher listing all their lectures today.
	 *
	 * @param array $teacher  ['name','email']
	 * @param array $items    list of ['class_name','section_name','start_time','end_time']
	 * @param array $school   ['name','address']
	 * @param string|null $bcc  optional BCC address (admin)
	 * @return bool
	 */
	function timetable_reminder_email($teacher, $items, $school, $bcc = null)
	{
		if (empty($teacher['email']) || empty($items)) return false;

		$today_date = date('d M Y');
		$today_day  = date('l');
		$first_class = $items[0]['class_name'];
		$first_start = !empty($items[0]['start_time']) ? date('h:i A', strtotime($items[0]['start_time'])) : '';

		$subject = "Today's Class Reminder — " . $first_class . ($first_start ? ' · ' . $first_start : '');

		$rows_html = '';
		foreach ($items as $it) {
			$st = !empty($it['start_time']) ? date('h:i A', strtotime($it['start_time'])) : '-';
			$et = !empty($it['end_time'])   ? date('h:i A', strtotime($it['end_time']))   : '-';
			$rows_html .= '<tr>'
				. '<td style="padding:8px 12px;border:1px solid #ddd;">' . htmlspecialchars($it['class_name']) . '</td>'
				. '<td style="padding:8px 12px;border:1px solid #ddd;">' . htmlspecialchars($it['section_name']) . '</td>'
				. '<td style="padding:8px 12px;border:1px solid #ddd;">' . $st . '</td>'
				. '<td style="padding:8px 12px;border:1px solid #ddd;">' . $et . '</td>'
				. '</tr>';
		}

		$msg = ''
			. '<div style="font-family:Arial,sans-serif;color:#222;max-width:640px;">'
			. '<p>Dear ' . htmlspecialchars($teacher['name']) . ',</p>'
			. '<p>This is a friendly reminder of your scheduled '
			.   (count($items) > 1 ? 'lectures' : 'lecture') . ' today.</p>'
			. '<p><strong>' . $today_date . ' (' . $today_day . ')</strong></p>'
			. '<table style="border-collapse:collapse;width:100%;font-size:13px;">'
			.   '<thead><tr style="background:#1f3a68;color:#fff;">'
			.     '<th style="padding:8px 12px;border:1px solid #1f3a68;text-align:left;">Class</th>'
			.     '<th style="padding:8px 12px;border:1px solid #1f3a68;text-align:left;">Section</th>'
			.     '<th style="padding:8px 12px;border:1px solid #1f3a68;text-align:left;">Start</th>'
			.     '<th style="padding:8px 12px;border:1px solid #1f3a68;text-align:left;">End</th>'
			.   '</tr></thead>'
			.   '<tbody>' . $rows_html . '</tbody>'
			. '</table>'
			. '<p style="margin-top:14px;">Please be available 5 minutes before the scheduled start.</p>'
			. '<p>Wishing you a productive class.</p>'
			. '<p style="margin-top:18px;color:#555;">Regards,<br>'
			.   '<strong>' . htmlspecialchars($school['name']) . '</strong>'
			.   (!empty($school['address']) ? '<br>' . htmlspecialchars($school['address']) : '')
			. '</p>'
			. '</div>';

		return $this->do_email($msg, $subject, $teacher['email'], null, $bcc);
	}

	/* =====================================================================
	 * Sending (SMTP settings from Settings > Email Settings; every send is logged)
	 * ================================================================== */

	private $smtp_ready = null;
	public  $last_error = '';

	private function setting($type, $default = '')
	{
		$row = $this->db->get_where('settings', array('type' => $type))->row();
		return $row ? $row->description : $default;
	}

	/** True when SMTP credentials are entered and email is enabled. */
	function is_configured()
	{
		return $this->setting('email_enabled', '1') === '1' && $this->setting('smtp_user') !== '' && $this->setting('smtp_pass') !== '';
	}

	private function init_mailer($keepalive = false)
	{
		$this->load->library('email');
		$config = array(
			'useragent'      => 'CodeIgniter',
			'protocol'       => 'smtp',
			'smtp_host'      => $this->setting('smtp_host', 'smtp.gmail.com'),
			'smtp_port'      => (int)$this->setting('smtp_port', '587'),
			'smtp_user'      => $this->setting('smtp_user'),
			'smtp_pass'      => $this->setting('smtp_pass'),
			'smtp_crypto'    => $this->setting('smtp_crypto', 'tls'),
			'smtp_timeout'   => 20,
			'smtp_keepalive' => $keepalive,
			'mailtype'       => 'html',
			'charset'        => 'utf-8',
			'crlf'           => "\r\n",
			'newline'        => "\r\n",
			'wordwrap'       => TRUE,
		);
		$this->email->initialize($config);
		$this->smtp_ready = true;
	}

	/**
	 * Send one email and record it in email_log.
	 * @param array $meta event, ref_type, ref_id, student_id
	 * @return string sent | failed | not_configured
	 */
	function send_logged($to, $subject, $html, $meta = array(), $bcc = null)
	{
		$status = 'not_configured';
		$error  = 'SMTP is not configured (Settings > Email Settings).';
		if ($this->is_configured()) {
			if ($this->smtp_ready === null) $this->init_mailer(!empty($meta['keepalive']));
			$from = $this->setting('smtp_user');
			$this->email->clear(TRUE);
			$this->email->from($from, $this->setting('system_name'));
			$this->email->reply_to($this->setting('system_email', $from));
			$this->email->to($to);
			if (!empty($bcc)) $this->email->bcc($bcc);
			$this->email->subject($subject);
			$this->email->message($html);
			if ($this->email->send(FALSE)) {
				$status = 'sent'; $error = '';
			} else {
				$status = 'failed';
				$error  = trim(strip_tags($this->email->print_debugger(array('headers'))));
				log_message('error', 'Email send failed to ' . $to . ': ' . $error);
			}
		}
		$this->last_error = $error;
		if ($this->db->table_exists('email_log')) {
			$this->db->insert('email_log', array(
				'created_at' => date('Y-m-d H:i:s'), 'to_email' => (string)$to, 'subject' => mb_substr((string)$subject, 0, 255),
				'body' => $html, 'status' => $status, 'error' => mb_substr($error, 0, 2000),
				'event' => $meta['event'] ?? '', 'ref_type' => $meta['ref_type'] ?? '', 'ref_id' => (int)($meta['ref_id'] ?? 0),
				'student_id' => (int)($meta['student_id'] ?? 0),
			));
		}
		return $status;
	}

	/** Legacy entry point used by account / password / timetable emails. Returns true when sent. */
	function do_email($msg=NULL, $sub=NULL, $to=NULL, $from=NULL, $bcc=NULL)
	{
		return $this->send_logged($to, $sub, $msg, array('event' => 'general'), $bcc) === 'sent';
	}

	/**
	 * Email a student (and the parent, when "copy parent" is on).
	 * @param array $student row with student_id, email, parent_email
	 * @return array counts by status
	 */
	function send_to_student($student, $subject, $html, $event, $ref_type, $ref_id)
	{
		$counts = array('sent' => 0, 'failed' => 0, 'not_configured' => 0, 'no_email' => 0);
		$to = sms_email_recipients($student['email'] ?? '', $student['parent_email'] ?? '', $this->setting('email_copy_parent', '1') === '1');
		if (!$to) { $counts['no_email']++; return $counts; }
		foreach ($to as $addr) {
			$s = $this->send_logged($addr, $subject, $html, array('event' => $event, 'ref_type' => $ref_type, 'ref_id' => $ref_id,
			                                                   'student_id' => $student['student_id'] ?? 0, 'keepalive' => true));
			$counts[$s]++;
		}
		return $counts;
	}

	/** Re-send one logged email (e.g. after fixing SMTP settings). */
	function resend_log($log_id)
	{
		$log = $this->db->get_where('email_log', array('log_id' => (int)$log_id))->row_array();
		if (!$log) return null;
		$status = $this->send_logged($log['to_email'], $log['subject'], $log['body'], array(
			'event' => $log['event'], 'ref_type' => $log['ref_type'], 'ref_id' => $log['ref_id'], 'student_id' => $log['student_id']));
		if ($status === 'sent') $this->db->where('log_id', (int)$log_id)->update('email_log', array('status' => 'resent'));
		return $status;
	}

	/* =====================================================================
	 * Exam notifications
	 * ================================================================== */

	private function merge_counts(&$total, $c)
	{
		foreach ($c as $k => $v) $total[$k] = ($total[$k] ?? 0) + $v;
	}

	private function cbt_vars($exam, $student)
	{
		list($opens, $closes) = sms_cbt_window($exam);
		return array(
			'student' => $student['name'], 'title' => $exam['title'], 'subject' => $exam['subject_name'], 'class' => $exam['class_name'],
			'date' => $exam['exam_date'], 'start' => $opens ? date('h:i A', $opens) : '', 'end' => $closes ? date('h:i A', $closes) : '',
			'duration' => $exam['duration'], 'total_marks' => rtrim(rtrim(number_format((float)$exam['total_marks'], 2, '.', ''), '0'), '.'),
			'mode' => 'online', 'instructions' => $exam['instructions'], 'portal_url' => base_url() . 'index.php?student/exams',
		);
	}

	private function classic_vars($exam, $student, $class_name)
	{
		return array(
			'student' => $student['name'], 'title' => $exam['name'], 'subject' => '', 'class' => $class_name,
			'date' => $exam['exam_date'], 'start' => '', 'end' => '', 'duration' => '', 'total_marks' => '',
			'mode' => 'written', 'instructions' => $exam['comment'], 'portal_url' => '',
		);
	}

	/** "Exam scheduled" for newly assigned students of a published CBT exam. */
	function notify_cbt_scheduled($exam_id, $student_ids)
	{
		$this->load->model('exam_model');
		$exam = $this->exam_model->cbt_exam($exam_id);
		$total = array();
		if (!$exam || $exam['status'] !== 'published') return $total;
		$school = $this->exam_model->school();
		foreach ((array)$student_ids as $sid) {
			$st = $this->exam_model->student($sid);
			if (!$st) continue;
			list($subject, $html) = sms_email_exam_scheduled($school, $this->cbt_vars($exam, $st));
			$this->merge_counts($total, $this->send_to_student($st, $subject, $html, 'exam_scheduled', 'cbt_exam', $exam_id));
			$this->db->where(array('exam_id' => (int)$exam_id, 'student_id' => (int)$sid))->update('exam_assignment', array('notified_at' => time()));
		}
		return $total;
	}

	/** "Result published" for every finished attempt of a CBT exam not yet notified. */
	function notify_cbt_results($exam_id)
	{
		$this->load->model('exam_model');
		$exam = $this->exam_model->cbt_exam($exam_id);
		$total = array();
		if (!$exam) return $total;
		$school = $this->exam_model->school();
		foreach ($this->exam_model->cbt_results($exam_id) as $r) {
			if ($r['percent'] === null || !empty($r['result_notified_at'])) continue;
			$st = $this->exam_model->student($r['student_id']);
			if (!$st) continue;
			list($subject, $html) = sms_email_result($school, array(
				'student' => $st['name'], 'title' => $exam['title'] . ' (' . $exam['subject_name'] . ')', 'rows' => array(),
				'score' => $r['score'], 'total' => $r['total'], 'percent' => $r['percent'], 'grade' => '',
				'pass' => $r['pass'], 'rank' => $r['rank'], 'portal_url' => base_url() . 'index.php?student/exam_result/' . $exam_id,
			));
			$this->merge_counts($total, $this->send_to_student($st, $subject, $html, 'result_published', 'cbt_exam', $exam_id));
			$this->db->where('assignment_id', $r['assignment_id'])->update('exam_assignment', array('result_notified_at' => time()));
		}
		return $total;
	}

	/** "Exam scheduled" for a written exam, to all active students of the given classes. */
	function notify_classic_scheduled($exam_id, $class_ids)
	{
		$this->load->model('exam_model');
		$exam = $this->exam_model->classic_exam($exam_id);
		$total = array();
		if (!$exam) return $total;
		$school = $this->exam_model->school();
		foreach ((array)$class_ids as $cid) {
			$class = $this->db->get_where('class', array('class_id' => (int)$cid))->row();
			foreach ($this->exam_model->active_students_of_class($cid) as $st) {
				list($subject, $html) = sms_email_exam_scheduled($school, $this->classic_vars($exam, $st, $class ? $class->name : ''));
				$this->merge_counts($total, $this->send_to_student($st, $subject, $html, 'exam_scheduled', 'exam', $exam_id));
			}
		}
		$this->db->where('exam_id', (int)$exam_id)->update('exam', array('notified_at' => time()));
		return $total;
	}

	/** "Result published" for a written exam: each student's subject marks, total, grade and rank. */
	function notify_classic_results($exam_id, $class_id)
	{
		$this->load->model('exam_model');
		$tab = $this->exam_model->tabulation($exam_id, $class_id);
		$total = array();
		if (!$tab['exam']) return $total;
		$school = $this->exam_model->school();
		$class = $this->db->get_where('class', array('class_id' => (int)$class_id))->row();
		foreach ($tab['rows'] as $row) {
			if ($row['percent'] === null) continue;                    // nothing entered for this student
			$lines = array();
			foreach ($tab['subjects'] as $sub) {
				$c = $row['cells'][$sub['subject_id']];
				if ($c) $lines[] = array($sub['name'], rtrim(rtrim(number_format($c['obtained'], 2, '.', ''), '0'), '.'), $c['total']);
			}
			$st = $this->exam_model->student($row['student']['student_id']);
			list($subject, $html) = sms_email_result($school, array(
				'student' => $st['name'], 'title' => $tab['exam']['name'] . ($class ? ' - ' . $class->name : ''), 'rows' => $lines,
				'score' => $row['obtained'], 'total' => $row['total'], 'percent' => $row['percent'], 'grade' => $row['grade'],
				'pass' => $row['pass'], 'rank' => $row['rank'], 'portal_url' => base_url() . 'index.php?student/marks',
			));
			$this->merge_counts($total, $this->send_to_student($st, $subject, $html, 'result_published', 'exam', $exam_id));
			$this->db->where(array('exam_id' => (int)$exam_id, 'student_id' => (int)$st['student_id']))->update('mark', array('result_notified_at' => time()));
		}
		return $total;
	}

	/**
	 * Build (without sending) the email a notification would send, for previewing.
	 * $type: cbt_scheduled | cbt_reminder | cbt_result | classic_scheduled | classic_result | log
	 * Returns subject, html, sample (whose copy is shown), recipients (all addresses that would receive it), or null.
	 */
	function preview($type, $ref_id, $extra = 0)
	{
		$this->load->model('exam_model');
		$this->exam_model->ensure_schema();
		$school = $this->exam_model->school();
		$copy = $this->setting('email_copy_parent', '1') === '1';
		$out = null; $students = array();

		if ($type === 'log') {
			$log = $this->db->get_where('email_log', array('log_id' => (int)$ref_id))->row_array();
			if (!$log) return null;
			return array('subject' => $log['subject'], 'html' => $log['body'], 'sample' => $log['to_email'],
			             'recipients' => array($log['to_email']), 'status' => $log['status'], 'sent_at' => $log['created_at']);
		}
		if (in_array($type, array('cbt_scheduled', 'cbt_reminder', 'cbt_result'), true)) {
			$exam = $this->exam_model->cbt_exam($ref_id);
			if (!$exam) return null;
			if ($type === 'cbt_result') {
				foreach ($this->exam_model->cbt_results($ref_id) as $r) if ($r['percent'] !== null) $students[] = $r;
			} else {
				$assigned = $this->exam_model->assignments($ref_id);
				// Before anyone is assigned, preview with the class list (who could be assigned).
				$students = $assigned ?: $this->exam_model->active_students_of_class($exam['class_id']);
			}
			if (!$students) return array('subject' => '', 'html' => '', 'sample' => '', 'recipients' => array(), 'empty' => true);
			$first = $this->exam_model->student($students[0]['student_id']);
			if ($type === 'cbt_result') {
				$r = $students[0];
				list($subject, $html) = sms_email_result($school, array(
					'student' => $first['name'], 'title' => $exam['title'] . ' (' . $exam['subject_name'] . ')', 'rows' => array(),
					'score' => $r['score'], 'total' => $r['total'], 'percent' => $r['percent'], 'grade' => '',
					'pass' => $r['pass'], 'rank' => $r['rank'], 'portal_url' => base_url() . 'index.php?student/exam_result/' . $ref_id));
			} else {
				list($subject, $html) = sms_email_exam_scheduled($school, $this->cbt_vars($exam, $first), $type === 'cbt_reminder');
			}
			$out = compact('subject', 'html') + array('sample' => $first['name']);
		}
		if ($type === 'classic_scheduled') {
			$exam = $this->exam_model->classic_exam($ref_id);
			if (!$exam) return null;
			$class_ids = array_filter(explode(',', (string)$exam['class_ids']));
			foreach ($class_ids as $cid) foreach ($this->exam_model->active_students_of_class($cid) as $s) $students[] = $s + array('class_id' => $cid);
			if (!$students) return array('subject' => '', 'html' => '', 'sample' => '', 'recipients' => array(), 'empty' => true);
			$class = $this->db->get_where('class', array('class_id' => (int)$students[0]['class_id']))->row();
			list($subject, $html) = sms_email_exam_scheduled($school, $this->classic_vars($exam, $students[0], $class ? $class->name : ''));
			$out = compact('subject', 'html') + array('sample' => $students[0]['name']);
		}
		if ($type === 'classic_result') {
			$tab = $this->exam_model->tabulation($ref_id, $extra);
			if (!$tab['exam']) return null;
			$rows = array_values(array_filter($tab['rows'], function ($r) { return $r['percent'] !== null; }));
			foreach ($rows as $r) $students[] = $r['student'];
			if (!$rows) return array('subject' => '', 'html' => '', 'sample' => '', 'recipients' => array(), 'empty' => true);
			$class = $this->db->get_where('class', array('class_id' => (int)$extra))->row();
			$lines = array();
			foreach ($tab['subjects'] as $sub) {
				$c = $rows[0]['cells'][$sub['subject_id']];
				if ($c) $lines[] = array($sub['name'], sms_num($c['obtained']), $c['total']);
			}
			list($subject, $html) = sms_email_result($school, array(
				'student' => $rows[0]['student']['name'], 'title' => $tab['exam']['name'] . ($class ? ' - ' . $class->name : ''), 'rows' => $lines,
				'score' => $rows[0]['obtained'], 'total' => $rows[0]['total'], 'percent' => $rows[0]['percent'], 'grade' => $rows[0]['grade'],
				'pass' => $rows[0]['pass'], 'rank' => $rows[0]['rank'], 'portal_url' => base_url() . 'index.php?student/marks'));
			$out = compact('subject', 'html') + array('sample' => $rows[0]['student']['name']);
		}
		if ($out === null) return null;
		$all = array();
		foreach ($students as $s) {
			$st = isset($s['parent_email']) ? $s : $this->exam_model->student($s['student_id']);
			foreach (sms_email_recipients($st['email'] ?? '', $st['parent_email'] ?? '', $copy) as $e) $all[] = $e;
		}
		$out['recipients'] = array_values(array_unique($all));
		$out['student_count'] = count($students);
		return $out;
	}

	/** Day-before reminders for published CBT exams and written exams dated tomorrow (run daily). */
	function send_due_reminders($today)
	{
		$this->load->model('exam_model');
		$this->exam_model->ensure_schema();
		$school = $this->exam_model->school();
		$tomorrow = date('Y-m-d', strtotime($today . ' +1 day'));
		$total = array('exams' => 0);

		foreach ($this->exam_model->cbt_exams(array('e.status' => 'published', 'e.exam_date' => $tomorrow)) as $exam) {
			if (!empty($exam['reminder_sent_at'])) continue;
			foreach ($this->exam_model->assignments($exam['exam_id']) as $a) {
				if ($a['status'] !== 'assigned') continue;
				$st = $this->exam_model->student($a['student_id']);
				if (!$st) continue;
				list($subject, $html) = sms_email_exam_scheduled($school, $this->cbt_vars($exam, $st), true);
				$this->merge_counts($total, $this->send_to_student($st, $subject, $html, 'exam_reminder', 'cbt_exam', $exam['exam_id']));
			}
			$this->db->where('exam_id', $exam['exam_id'])->update('cbt_exam', array('reminder_sent_at' => time()));
			$total['exams']++;
		}

		// Written exams were announced to specific classes; remind the same classes.
		foreach ($this->db->get_where('exam', array('exam_date' => $tomorrow))->result_array() as $exam) {
			if (!empty($exam['reminder_sent_at'])) continue;
			$class_ids = array_filter(array_map('intval', explode(',', (string)($exam['class_ids'] ?? ''))));
			foreach ($class_ids as $cid) {
				$class = $this->db->get_where('class', array('class_id' => $cid))->row();
				foreach ($this->exam_model->active_students_of_class($cid) as $st) {
					list($subject, $html) = sms_email_exam_scheduled($school, $this->classic_vars($exam, $st, $class ? $class->name : ''), true);
					$this->merge_counts($total, $this->send_to_student($st, $subject, $html, 'exam_reminder', 'exam', $exam['exam_id']));
				}
			}
			$this->db->where('exam_id', $exam['exam_id'])->update('exam', array('reminder_sent_at' => time()));
			$total['exams']++;
		}
		return $total;
	}
}
