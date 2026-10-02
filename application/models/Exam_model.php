<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Exams & CBT data layer: schema migration, CBT exams / attempts / results,
 * classic (written) exam marks and tabulation. Pure rules (marking, timing,
 * ranking, validation) live in sms_exam_helper and are unit tested.
 */
class Exam_model extends CI_Model
{
    const SCHEMA_VERSION = '2';

    function __construct()
    {
        parent::__construct();
    }

    /* =====================================================================
     * Schema
     * ================================================================== */

    /** Idempotent migration; cheap no-op once the stored version matches. */
    function ensure_schema()
    {
        static $done = false;
        if ($done) return;
        $done = true;

        $row = $this->db->get_where('settings', array('type' => 'exam_schema_version'))->row();
        if ($row && $row->description === self::SCHEMA_VERSION) return;

        $db = $this->db;
        $db->query("CREATE TABLE IF NOT EXISTS `cbt_exam` (
            `exam_id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NOT NULL DEFAULT '',
            `class_id` int(11) NOT NULL,
            `subject_id` int(11) NOT NULL,
            `session` varchar(50) NOT NULL DEFAULT '',
            `exam_date` date NOT NULL,
            `start_time` time NOT NULL DEFAULT '10:00:00',
            `end_time` time NULL DEFAULT NULL,
            `duration` int(11) NOT NULL DEFAULT 30,
            `pass_percent` int(11) NOT NULL DEFAULT 35,
            `instructions` text NULL,
            `status` varchar(20) NOT NULL DEFAULT 'draft',
            `results_published` tinyint(1) NOT NULL DEFAULT 0,
            `published_at` int(11) NULL,
            `results_published_at` int(11) NULL,
            `reminder_sent_at` int(11) NULL,
            `created_at` int(11) NULL,
            PRIMARY KEY (`exam_id`),
            KEY `class_date` (`class_id`, `exam_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        $db->query("CREATE TABLE IF NOT EXISTS `email_log` (
            `log_id` int(11) NOT NULL AUTO_INCREMENT,
            `created_at` datetime NOT NULL,
            `to_email` varchar(255) NOT NULL,
            `subject` varchar(255) NOT NULL,
            `body` mediumtext NULL,
            `status` varchar(20) NOT NULL,
            `error` text NULL,
            `event` varchar(50) NOT NULL DEFAULT '',
            `ref_type` varchar(30) NOT NULL DEFAULT '',
            `ref_id` int(11) NOT NULL DEFAULT 0,
            `student_id` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`log_id`),
            KEY `ref` (`ref_type`, `ref_id`),
            KEY `created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        $add = function ($table, $field, $ddl) use ($db) {
            if ($db->table_exists($table) && !$db->field_exists($field, $table))
                $db->query("ALTER TABLE `$table` ADD $ddl");
        };
        // question / answer / exam_result predate this module
        $add('question', 'marks', "`marks` INT NOT NULL DEFAULT 1");
        $add('question', 'exam_id', "`exam_id` INT NULL, ADD KEY `exam_id` (`exam_id`)");
        $add('exam_result', 'marks_awarded', "`marks_awarded` DECIMAL(10,2) NULL");
        $add('exam_result', 'status', "`status` VARCHAR(20) DEFAULT 'submitted'");
        $add('exam_result', 'submitted_at', "`submitted_at` INT NULL");
        $add('exam_result', 'exam_id', "`exam_id` INT NULL, ADD KEY `exam_student` (`exam_id`, `student_id`)");

        $db->query("CREATE TABLE IF NOT EXISTS `exam_assignment` (
            `assignment_id` int(11) NOT NULL AUTO_INCREMENT,
            `class_id` int(11) NOT NULL DEFAULT 0,
            `subject_id` int(11) NOT NULL DEFAULT 0,
            `date` date NULL,
            `duration` int(11) NOT NULL DEFAULT 0,
            `session` varchar(255) NOT NULL DEFAULT '',
            `student_id` int(11) NOT NULL,
            `status` varchar(20) NOT NULL DEFAULT 'assigned',
            `assigned_at` int(11) DEFAULT NULL,
            `completed_at` int(11) DEFAULT NULL,
            PRIMARY KEY (`assignment_id`),
            KEY `student_id` (`student_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
        $add('exam_assignment', 'exam_id', "`exam_id` INT NULL, ADD KEY `exam_id` (`exam_id`)");
        $add('exam_assignment', 'started_at', "`started_at` INT NULL");
        $add('exam_assignment', 'submitted_at', "`submitted_at` INT NULL");
        $add('exam_assignment', 'score', "`score` DECIMAL(10,2) NULL");
        $add('exam_assignment', 'total', "`total` DECIMAL(10,2) NULL");
        $add('exam_assignment', 'notified_at', "`notified_at` INT NULL");
        $add('exam_assignment', 'result_notified_at', "`result_notified_at` INT NULL");

        // classic (written) exams
        $add('exam', 'exam_date', "`exam_date` DATE NULL");
        $add('exam', 'class_ids', "`class_ids` VARCHAR(255) NOT NULL DEFAULT ''");
        $add('exam', 'total_marks', "`total_marks` INT NOT NULL DEFAULT 100");
        $add('exam', 'pass_percent', "`pass_percent` INT NOT NULL DEFAULT 35");
        $add('exam', 'results_published', "`results_published` TINYINT(1) NOT NULL DEFAULT 0");
        $add('exam', 'results_published_at', "`results_published_at` INT NULL");
        $add('exam', 'notified_at', "`notified_at` INT NULL");
        $add('exam', 'reminder_sent_at', "`reminder_sent_at` INT NULL");
        $add('mark', 'result_notified_at', "`result_notified_at` INT NULL");
        // Blank must mean "not entered" (not 0), and half marks must be possible.
        $col = $db->query("SELECT DATA_TYPE, IS_NULLABLE FROM information_schema.COLUMNS
                           WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'mark' AND COLUMN_NAME = 'mark_obtained'")->row();
        if ($col && ($col->DATA_TYPE !== 'decimal' || $col->IS_NULLABLE !== 'YES'))
            $db->query("ALTER TABLE `mark` MODIFY `mark_obtained` DECIMAL(6,2) NULL DEFAULT NULL, MODIFY `mark_total` INT NOT NULL DEFAULT 100");

        // Legacy CBT exams were identified only by (class, subject, date, duration, session): give each a cbt_exam row.
        $groups = $db->query("SELECT q.class_id, q.subject_id, q.date, q.duration, q.session, s.name subject_name
                              FROM question q LEFT JOIN subject s ON s.subject_id = q.subject_id
                              WHERE q.exam_id IS NULL GROUP BY q.class_id, q.subject_id, q.date, q.duration, q.session")->result_array();
        foreach ($groups as $g) {
            $db->insert('cbt_exam', array(
                'title' => trim(($g['subject_name'] ?: 'CBT') . ' Test'), 'class_id' => $g['class_id'], 'subject_id' => $g['subject_id'],
                'session' => (string)$g['session'], 'exam_date' => $g['date'] ?: date('Y-m-d'), 'duration' => max(1, (int)$g['duration']),
                'status' => 'draft', 'created_at' => time(),
            ));
            $id = $db->insert_id();
            $where = array('class_id' => $g['class_id'], 'subject_id' => $g['subject_id'], 'date' => $g['date'], 'duration' => $g['duration'], 'session' => $g['session']);
            $db->where($where)->where('exam_id IS NULL', null, false)->update('question', array('exam_id' => $id));
            $db->where($where)->where('exam_id IS NULL', null, false)->update('exam_assignment', array('exam_id' => $id));
        }
        $db->query("UPDATE exam_result r JOIN question q ON q.question_id = r.question_id SET r.exam_id = q.exam_id WHERE r.exam_id IS NULL");
        foreach ($db->query("SELECT exam_id, date FROM exam WHERE exam_date IS NULL")->result_array() as $e) {
            $d = sms_parse_exam_date($e['date']);
            if ($d !== '') $db->where('exam_id', $e['exam_id'])->update('exam', array('exam_date' => $d));
        }

        // email settings (Gmail defaults; credentials entered on the Email Settings page)
        $defaults = array('smtp_host' => 'smtp.gmail.com', 'smtp_port' => '587', 'smtp_crypto' => 'tls', 'smtp_user' => '',
                          'smtp_pass' => '', 'email_enabled' => '1', 'email_copy_parent' => '1',
                          'cron_key' => bin2hex(random_bytes(16)));
        foreach ($defaults as $type => $value) {
            if (!$db->get_where('settings', array('type' => $type))->row())
                $db->insert('settings', array('type' => $type, 'description' => $value));
        }

        if ($row) $db->where('type', 'exam_schema_version')->update('settings', array('description' => self::SCHEMA_VERSION));
        else      $db->insert('settings', array('type' => 'exam_schema_version', 'description' => self::SCHEMA_VERSION));
    }

    function setting($type, $default = '')
    {
        $row = $this->db->get_where('settings', array('type' => $type))->row();
        return $row ? $row->description : $default;
    }

    function school()
    {
        return array('name' => $this->setting('system_name'), 'phone' => $this->setting('phone'), 'address' => $this->setting('address'));
    }

    /* =====================================================================
     * CBT exams
     * ================================================================== */

    /** CBT exams with class/subject names, question & marks totals and attempt counts. */
    function cbt_exams($where = array())
    {
        $this->ensure_schema();
        $this->db->select("e.*, c.name class_name, s.name subject_name,
            (SELECT COUNT(*) FROM question q WHERE q.exam_id = e.exam_id) question_count,
            (SELECT COALESCE(SUM(q.marks),0) FROM question q WHERE q.exam_id = e.exam_id) total_marks,
            (SELECT COUNT(*) FROM exam_assignment a WHERE a.exam_id = e.exam_id) assigned_count,
            (SELECT COUNT(*) FROM exam_assignment a WHERE a.exam_id = e.exam_id AND a.status IN ('submitted','checked')) submitted_count", false)
            ->from('cbt_exam e')
            ->join('class c', 'c.class_id = e.class_id', 'left')
            ->join('subject s', 's.subject_id = e.subject_id', 'left');
        foreach ($where as $k => $v) $this->db->where($k, $v);
        return $this->db->order_by('e.exam_date', 'DESC')->order_by('e.start_time', 'DESC')->get()->result_array();
    }

    function cbt_exam($exam_id)
    {
        $rows = $this->cbt_exams(array('e.exam_id' => (int)$exam_id));
        return $rows ? $rows[0] : null;
    }

    /** Questions of an exam, each with options [label => content]. */
    function cbt_questions($exam_id)
    {
        $questions = $this->db->order_by('question_id', 'ASC')->get_where('question', array('exam_id' => (int)$exam_id))->result_array();
        if (!$questions) return array();
        $ids = array_column($questions, 'question_id');
        $opts = array();
        foreach ($this->db->where_in('question_id', $ids)->order_by('label', 'ASC')->get('answer')->result_array() as $a)
            $opts[$a['question_id']][$a['label']] = $a['content'];
        foreach ($questions as &$q) $q['options'] = isset($opts[$q['question_id']]) ? $opts[$q['question_id']] : array();
        return $questions;
    }

    /** Legacy question columns mirror the exam header (other screens still read them). */
    private function legacy_cols($exam)
    {
        return array('class_id' => $exam['class_id'], 'subject_id' => $exam['subject_id'], 'date' => $exam['exam_date'],
                     'duration' => $exam['duration'], 'session' => (string)$exam['session']);
    }

    function create_cbt_exam($data, $question_count, $options_per_question = 4)
    {
        $this->ensure_schema();
        $data['status'] = 'draft';
        $data['created_at'] = time();
        $this->db->insert('cbt_exam', $data);
        $exam_id = $this->db->insert_id();
        $exam = $this->db->get_where('cbt_exam', array('exam_id' => $exam_id))->row_array();
        $labels = array_slice(array('A', 'B', 'C', 'D', 'E', 'F'), 0, max(2, min(6, (int)$options_per_question)));
        for ($i = 1; $i <= max(1, (int)$question_count); $i++) $this->add_question($exam, '', $labels);
        return $exam_id;
    }

    function add_question($exam, $text = '', $labels = array('A', 'B', 'C', 'D'))
    {
        $this->db->insert('question', $this->legacy_cols($exam) + array(
            'exam_id' => $exam['exam_id'], 'question_count' => 0, 'question' => $text, 'correct_answers' => 'A', 'marks' => 1,
        ));
        $qid = $this->db->insert_id();
        foreach ($labels as $l) $this->db->insert('answer', array('question_id' => $qid, 'label' => $l, 'content' => ''));
        $this->sync_question_count($exam['exam_id']);
        return $qid;
    }

    function sync_question_count($exam_id)
    {
        $n = $this->db->where('exam_id', (int)$exam_id)->count_all_results('question');
        $this->db->where('exam_id', (int)$exam_id)->update('question', array('question_count' => $n));
    }

    function update_cbt_exam($exam_id, $data)
    {
        $this->db->where('exam_id', (int)$exam_id)->update('cbt_exam', $data);
        $exam = $this->db->get_where('cbt_exam', array('exam_id' => (int)$exam_id))->row_array();
        $this->db->where('exam_id', (int)$exam_id)->update('question', $this->legacy_cols($exam));
        $this->db->where('exam_id', (int)$exam_id)->update('exam_assignment', $this->legacy_cols($exam));
    }

    function save_question($question_id, $text, $options, $correct, $marks)
    {
        $this->db->where('question_id', (int)$question_id)->update('question', array(
            'question' => trim((string)$text), 'correct_answers' => strtoupper(trim((string)$correct)), 'marks' => max(0, (int)$marks),
        ));
        foreach ((array)$options as $label => $content) {
            $label = strtoupper(substr((string)$label, 0, 1));
            $exists = $this->db->get_where('answer', array('question_id' => (int)$question_id, 'label' => $label))->row();
            if ($exists) $this->db->where('answer_id', $exists->answer_id)->update('answer', array('content' => trim((string)$content)));
            else         $this->db->insert('answer', array('question_id' => (int)$question_id, 'label' => $label, 'content' => trim((string)$content)));
        }
    }

    function delete_question($question_id)
    {
        $q = $this->db->get_where('question', array('question_id' => (int)$question_id))->row();
        if (!$q) return;
        $this->db->where('question_id', (int)$question_id)->delete('answer');
        $this->db->where('question_id', (int)$question_id)->delete('exam_result');
        $this->db->where('question_id', (int)$question_id)->delete('question');
        if ($q->exam_id) $this->sync_question_count($q->exam_id);
    }

    function delete_cbt_exam($exam_id)
    {
        $ids = array_column($this->db->select('question_id')->get_where('question', array('exam_id' => (int)$exam_id))->result_array(), 'question_id');
        if ($ids) {
            $this->db->where_in('question_id', $ids)->delete('answer');
            $this->db->where_in('question_id', $ids)->delete('exam_result');
            $this->db->where_in('question_id', $ids)->delete('question');
        }
        $this->db->where('exam_id', (int)$exam_id)->delete('exam_result');
        $this->db->where('exam_id', (int)$exam_id)->delete('exam_assignment');
        $this->db->where('exam_id', (int)$exam_id)->delete('cbt_exam');
    }

    /* ---- Students & assignments ---- */

    function active_students_of_class($class_id)
    {
        return $this->db->select('s.student_id, s.name, s.email, s.roll, s.parent_id, p.email parent_email', false)
            ->from('student s')->join('parent p', 'p.parent_id = s.parent_id', 'left')
            ->where('s.class_id', (string)(int)$class_id)->where('s.is_active', 1)
            ->order_by('s.name', 'ASC')->get()->result_array();
    }

    function student($student_id)
    {
        return $this->db->select('s.*, p.email parent_email, c.name class_name', false)->from('student s')
            ->join('parent p', 'p.parent_id = s.parent_id', 'left')->join('class c', 'c.class_id = s.class_id', 'left')
            ->where('s.student_id', (int)$student_id)->get()->row_array();
    }

    function assignments($exam_id)
    {
        return $this->db->select('a.*, s.name student_name, s.email, s.roll', false)->from('exam_assignment a')
            ->join('student s', 's.student_id = a.student_id', 'left')
            ->where('a.exam_id', (int)$exam_id)->order_by('s.name', 'ASC')->get()->result_array();
    }

    function assignment($exam_id, $student_id)
    {
        return $this->db->get_where('exam_assignment', array('exam_id' => (int)$exam_id, 'student_id' => (int)$student_id))->row_array();
    }

    /** Assign students (only those in the exam's class). Returns the ids that were newly assigned. */
    function assign_students($exam_id, $student_ids)
    {
        $exam = $this->db->get_where('cbt_exam', array('exam_id' => (int)$exam_id))->row_array();
        if (!$exam) return array();
        $allowed = array_column($this->active_students_of_class($exam['class_id']), 'student_id');
        $new = array();
        foreach (array_unique(array_map('intval', (array)$student_ids)) as $sid) {
            if (!in_array((string)$sid, array_map('strval', $allowed), true) || $this->assignment($exam_id, $sid)) continue;
            $this->db->insert('exam_assignment', $this->legacy_cols($exam) + array(
                'exam_id' => $exam['exam_id'], 'student_id' => $sid, 'status' => 'assigned', 'assigned_at' => time(),
            ));
            $new[] = $sid;
        }
        return $new;
    }

    /** Remove an assignment that has not been started yet. */
    function unassign_student($exam_id, $student_id)
    {
        $a = $this->assignment($exam_id, $student_id);
        if (!$a || $a['status'] !== 'assigned') return false;
        $this->db->where('assignment_id', $a['assignment_id'])->delete('exam_assignment');
        return true;
    }

    /* ---- Attempts ---- */

    function student_answers($exam_id, $student_id)
    {
        $out = array();
        foreach ($this->db->get_where('exam_result', array('exam_id' => (int)$exam_id, 'student_id' => (int)$student_id))->result_array() as $r)
            $out[(int)$r['question_id']] = $r;
        return $out;
    }

    function start_attempt($exam_id, $student_id, $now)
    {
        $a = $this->assignment($exam_id, $student_id);
        if ($a && $a['status'] === 'assigned') {
            $this->db->where('assignment_id', $a['assignment_id'])->update('exam_assignment', array('status' => 'in_progress', 'started_at' => $now));
            $a['status'] = 'in_progress';
            $a['started_at'] = $now;
        }
        return $a;
    }

    /** Store one chosen option while the attempt is in progress. */
    function save_answer($exam_id, $student_id, $question_id, $label)
    {
        $q = $this->db->get_where('question', array('question_id' => (int)$question_id, 'exam_id' => (int)$exam_id))->row();
        if (!$q) return false;
        $label = strtoupper(substr(trim((string)$label), 0, 1));
        $row = array('answer' => $label, 'status' => 'answered', 'submitted_at' => time());
        $exists = $this->db->get_where('exam_result', array('exam_id' => (int)$exam_id, 'student_id' => (int)$student_id, 'question_id' => (int)$question_id))->row();
        if ($exists) $this->db->where('result_id', $exists->result_id)->update('exam_result', $row);
        else         $this->db->insert('exam_result', $row + array('exam_id' => (int)$exam_id, 'student_id' => (int)$student_id, 'question_id' => (int)$question_id));
        return true;
    }

    /** Finish an attempt: auto-mark every question and store the score. */
    function submit_attempt($exam_id, $student_id, $now)
    {
        $questions = $this->cbt_questions($exam_id);
        $saved = $this->student_answers($exam_id, $student_id);
        $answers = array();
        foreach ($saved as $qid => $r) $answers[$qid] = $r['answer'];
        $score = sms_cbt_score($questions, $answers);
        foreach ($questions as $q) {
            $qid = (int)$q['question_id'];
            $row = array('marks_awarded' => $score['awarded'][$qid], 'status' => 'checked', 'submitted_at' => $now);
            if (isset($saved[$qid])) $this->db->where('result_id', $saved[$qid]['result_id'])->update('exam_result', $row);
            else $this->db->insert('exam_result', $row + array('exam_id' => (int)$exam_id, 'student_id' => (int)$student_id, 'question_id' => $qid, 'answer' => ''));
        }
        $this->db->where(array('exam_id' => (int)$exam_id, 'student_id' => (int)$student_id))->update('exam_assignment', array(
            'status' => 'submitted', 'submitted_at' => $now, 'completed_at' => $now, 'score' => $score['score'], 'total' => $score['total'],
        ));
        return $score;
    }

    /** Admin override of awarded marks after review (paper checking). */
    function override_marks($exam_id, $student_id, $awarded)
    {
        $questions = array();
        foreach ($this->cbt_questions($exam_id) as $q) $questions[(int)$q['question_id']] = $q;
        $score = 0.0; $total = 0.0;
        foreach ($questions as $qid => $q) {
            $max = (float)$q['marks'];
            $total += $max;
            $existing = $this->db->get_where('exam_result', array('exam_id' => (int)$exam_id, 'student_id' => (int)$student_id, 'question_id' => $qid))->row();
            $given = isset($awarded[$qid]) && is_numeric($awarded[$qid]) ? max(0, min($max, (float)$awarded[$qid])) : ($existing ? (float)$existing->marks_awarded : 0.0);
            $score += $given;
            $row = array('marks_awarded' => $given, 'status' => 'checked');
            if ($existing) $this->db->where('result_id', $existing->result_id)->update('exam_result', $row);
            else $this->db->insert('exam_result', $row + array('exam_id' => (int)$exam_id, 'student_id' => (int)$student_id, 'question_id' => $qid, 'answer' => '', 'submitted_at' => time()));
        }
        $this->db->where(array('exam_id' => (int)$exam_id, 'student_id' => (int)$student_id))
            ->update('exam_assignment', array('status' => 'checked', 'score' => $score, 'total' => $total));
        return $score;
    }

    /** Close attempts whose time ran out without a submit (browser closed etc.). */
    function auto_submit_expired($exam, $now)
    {
        $n = 0;
        foreach ($this->db->get_where('exam_assignment', array('exam_id' => (int)$exam['exam_id'], 'status' => 'in_progress'))->result_array() as $a) {
            if ($now >= sms_cbt_deadline($exam, $a['started_at'])) {
                $this->submit_attempt($exam['exam_id'], $a['student_id'], sms_cbt_deadline($exam, $a['started_at']));
                $n++;
            }
        }
        return $n;
    }

    /** Results of one CBT exam with percent, pass/fail and rank (finished attempts only). */
    function cbt_results($exam_id)
    {
        $exam = $this->cbt_exam($exam_id);
        if (!$exam) return array();
        $rows = array();
        foreach ($this->assignments($exam_id) as $a) {
            $finished = in_array($a['status'], array('submitted', 'checked'), true);
            $a['percent'] = $finished ? sms_percent($a['score'], $a['total']) : null;
            $a['pass'] = $finished ? sms_is_pass($a['percent'], $exam['pass_percent']) : null;
            $a['rank'] = null;
            $rows[] = $a;
        }
        $finished_rows = array_filter($rows, function ($r) { return $r['percent'] !== null; });
        foreach (sms_rank($finished_rows, 'score') as $i => $rank) $rows[$i]['rank'] = $rank;
        return $rows;
    }

    /* =====================================================================
     * Classic (written) exams
     * ================================================================== */

    function classic_exam($exam_id)
    {
        $this->ensure_schema();
        return $this->db->get_where('exam', array('exam_id' => (int)$exam_id))->row_array();
    }

    function classic_exams()
    {
        $this->ensure_schema();
        return $this->db->order_by('exam_date', 'DESC')->order_by('exam_id', 'DESC')->get('exam')->result_array();
    }

    /** Mark rows for every active student of a class in one subject (creating missing rows). */
    function subject_marks($exam_id, $class_id, $subject_id)
    {
        $exam = $this->classic_exam($exam_id);
        $students = $this->active_students_of_class($class_id);
        foreach ($students as &$s) {
            $m = $this->db->get_where('mark', array('exam_id' => (int)$exam_id, 'class_id' => (int)$class_id,
                                                    'subject_id' => (int)$subject_id, 'student_id' => (int)$s['student_id']))->row_array();
            if (!$m) {
                $this->db->insert('mark', array('exam_id' => (int)$exam_id, 'class_id' => (int)$class_id, 'subject_id' => (int)$subject_id,
                    'student_id' => (int)$s['student_id'], 'mark_obtained' => null, 'mark_total' => (int)($exam['total_marks'] ?? 100), 'comment' => ''));
                $m = $this->db->get_where('mark', array('mark_id' => $this->db->insert_id()))->row_array();
            }
            $s['mark'] = $m;
        }
        return $students;
    }

    /**
     * Tabulation for one exam + class: per student per subject obtained/total, totals,
     * percent, grade, pass and rank. Students with no marks entered at all are listed without a rank.
     */
    function tabulation($exam_id, $class_id)
    {
        $exam = $this->classic_exam($exam_id);
        $subjects = $this->db->order_by('name', 'ASC')->get_where('subject', array('class_id' => (int)$class_id))->result_array();
        $grades = $this->db->get('grade')->result_array();
        $marks = array();
        foreach ($this->db->get_where('mark', array('exam_id' => (int)$exam_id, 'class_id' => (int)$class_id))->result_array() as $m)
            $marks[$m['student_id']][$m['subject_id']] = $m;
        $rows = array();
        foreach ($this->active_students_of_class($class_id) as $s) {
            $obt = 0.0; $tot = 0.0; $entered = 0; $cells = array();
            foreach ($subjects as $sub) {
                $m = $marks[$s['student_id']][$sub['subject_id']] ?? null;
                $has = $m && $m['mark_obtained'] !== null && $m['mark_obtained'] !== '';
                $cells[$sub['subject_id']] = $has ? array('obtained' => (float)$m['mark_obtained'], 'total' => (float)$m['mark_total']) : null;
                if ($has) { $obt += (float)$m['mark_obtained']; $tot += (float)$m['mark_total']; $entered++; }
            }
            $pct = $entered ? sms_percent($obt, $tot) : null;
            $g = $pct !== null ? sms_grade_for_percent($grades, $pct) : null;
            $rows[] = array('student' => $s, 'cells' => $cells, 'obtained' => $obt, 'total' => $tot, 'entered' => $entered,
                            'percent' => $pct, 'grade' => $g ? $g['name'] : '', 'pass' => $pct !== null ? sms_is_pass($pct, $exam['pass_percent'] ?? 35) : null,
                            'rank' => null);
        }
        $ranked = array_filter($rows, function ($r) { return $r['percent'] !== null; });
        foreach (sms_rank($ranked, 'percent') as $i => $rank) $rows[$i]['rank'] = $rank;
        return array('exam' => $exam, 'subjects' => $subjects, 'rows' => $rows, 'grades_configured' => count($grades) > 0);
    }

    /** Classes that have at least one mark entered for this exam. */
    function classes_with_marks($exam_id)
    {
        return array_column($this->db->query("SELECT DISTINCT class_id FROM mark WHERE exam_id = ? AND mark_obtained IS NOT NULL", array((int)$exam_id))->result_array(), 'class_id');
    }
}
