<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Scheduled jobs. Run daily (e.g. Windows Task Scheduler, 6 PM):
 *   C:\xampp\php\php.exe C:\xampp\htdocs\sms\index.php cron exam_reminders
 * or over HTTP with the key from Settings > Email Settings:
 *   index.php?cron/exam_reminders/<cron_key>
 */
class Cron extends CI_Controller
{
    public $exam_model;
    public $email_model;

    function exam_reminders($key = '')
    {
        $this->load->database();
        $this->load->model('exam_model');
        $this->exam_model->ensure_schema();
        if (!is_cli()) {
            $expected = $this->exam_model->setting('cron_key');
            if ($expected === '' || !hash_equals($expected, (string)$key)) show_404();
        }
        $this->load->model('email_model');
        $counts = $this->email_model->send_due_reminders(date('Y-m-d'));
        $line = date('Y-m-d H:i:s') . ' exam reminders: ' . json_encode($counts);
        log_message('info', $line);
        echo $line . (is_cli() ? PHP_EOL : '');
    }
}
