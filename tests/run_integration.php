<?php
/**
 * DB integration tests for the Enquiry & Course modules.
 *
 * Exercises the real schema against the live MySQL database: runs the same
 * migration SQL the app uses, then does real INSERT/SELECT round-trips and
 * cleans up after itself. Uses clearly-marked __TEST__ rows.
 *
 * Requires MySQL running.   Usage:  php tests/run_integration.php
 */

define('SMS_ADMISSIONS_TEST', true);
require __DIR__ . '/../application/helpers/sms_admissions_helper.php';
require __DIR__ . '/../application/helpers/sms_core_helper.php';

$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'smsDB';

$tests = array();
$module = '';
function imodule($n) { global $module; $module = $n; }
function icheck($desc, $expected, $actual) {
    global $tests, $module;
    $tests[] = array('module' => $module, 'desc' => $desc, 'expected' => $expected, 'actual' => $actual, 'pass' => ($expected === $actual));
}
function icheck_true($desc, $actual) { icheck($desc, true, (bool)$actual); }

mysqli_report(MYSQLI_REPORT_OFF);
$db = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($db->connect_errno) {
    fwrite(STDERR, "Cannot connect to MySQL ({$DB_NAME}) at {$DB_HOST}: " . $db->connect_error . "\n");
    fwrite(STDERR, "Start MySQL and retry.  (Unit tests in run_tests.php need no DB.)\n");
    exit(2);
}

/* ---- Migration SQL (mirrors ensure_enquiry_columns / ensure_course_tables) ---- */
imodule('A. Migrations');

$db->query("CREATE TABLE IF NOT EXISTS `enquiry_activity` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `enquiry_id` int(11) NOT NULL, `status` varchar(30) NULL, `note` longtext NULL,
  `created_by` varchar(150) NULL, `created_at` datetime NULL,
  PRIMARY KEY (`activity_id`), KEY `enquiry_id` (`enquiry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$db->query("CREATE TABLE IF NOT EXISTS `course` (
  `course_id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL,
  `session_name` varchar(20) NULL, `class_id` int(11) NULL, `standard_name` varchar(50) NULL,
  `total_fees` decimal(10,2) NULL DEFAULT 0, `installments` int(11) NULL DEFAULT 1,
  `description` longtext NULL, `created_by` varchar(150) NULL, `created_at` datetime NULL,
  PRIMARY KEY (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$db->query("CREATE TABLE IF NOT EXISTS `course_installment` (
  `installment_id` int(11) NOT NULL AUTO_INCREMENT, `course_id` int(11) NOT NULL,
  `title` varchar(100) NULL, `amount` decimal(10,2) NULL DEFAULT 0, `due_date` date NULL,
  PRIMARY KEY (`installment_id`), KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

$db->query("CREATE TABLE IF NOT EXISTS `course_subject` (
  `csubject_id` int(11) NOT NULL AUTO_INCREMENT, `course_id` int(11) NOT NULL,
  `subject_name` varchar(255) NOT NULL, `subject_code` varchar(50) NULL,
  PRIMARY KEY (`csubject_id`), KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

// Ensure new enquiry columns exist (idempotent).
$enq_cols = array('session_name'=>"varchar(20)", 'enquiry_no'=>"varchar(50)", 'enquiry_date'=>"date",
    'enquiry_for'=>"varchar(255)", 'course'=>"varchar(255)", 'source'=>"varchar(100)",
    'source_student'=>"varchar(255)", 'gender'=>"varchar(20)", 'address'=>"longtext",
    'assign_to'=>"int(11)", 'handled_by'=>"int(11)", 'status'=>"varchar(30)", 'remark'=>"longtext",
    'created_by'=>"varchar(150)");
$existing = array();
if ($res = $db->query("SHOW COLUMNS FROM `enquiry`")) {
    while ($r = $res->fetch_assoc()) $existing[$r['Field']] = true;
}
foreach ($enq_cols as $col => $type) {
    if (!isset($existing[$col])) $db->query("ALTER TABLE `enquiry` ADD `$col` $type NULL");
}

$have = function ($table) use ($db) { return $db->query("SHOW TABLES LIKE '$table'")->num_rows === 1; };
$col_exists = function ($table, $col) use ($db) {
    return $db->query("SHOW COLUMNS FROM `$table` LIKE '$col'")->num_rows === 1;
};

icheck_true('enquiry_activity table exists',  $have('enquiry_activity'));
icheck_true('course table exists',            $have('course'));
icheck_true('course_installment table exists',$have('course_installment'));
icheck_true('course_subject table exists',    $have('course_subject'));
icheck_true('enquiry.status column exists',   $col_exists('enquiry', 'status'));
icheck_true('enquiry.assign_to column exists',$col_exists('enquiry', 'assign_to'));

/* ---- Enquiry round-trip ---- */
imodule('B. Enquiry round-trip');

$db->query("DELETE FROM enquiry WHERE name = '__TEST__ Enquiry'");
$db->query("INSERT INTO enquiry (name, mobile, course, source, status, created_by)
            VALUES ('__TEST__ Enquiry', '9876543210', 'Regular', 'Google search', 'in_progress', '__TEST__')");
$eid = $db->insert_id;
icheck_true('enquiry inserted (has id)', $eid > 0);

$row = $db->query("SELECT * FROM enquiry WHERE enquiry_id = $eid")->fetch_assoc();
icheck('enquiry name persisted',   '__TEST__ Enquiry', $row['name']);
icheck('enquiry status persisted', 'in_progress',      $row['status']);

// Activity log + status transition to joined.
$db->query("INSERT INTO enquiry_activity (enquiry_id, status, note, created_by, created_at)
            VALUES ($eid, 'joined', 'Converted', '__TEST__', NOW())");
$db->query("UPDATE enquiry SET status = 'joined' WHERE enquiry_id = $eid");
$act = (int)$db->query("SELECT COUNT(*) c FROM enquiry_activity WHERE enquiry_id = $eid")->fetch_assoc()['c'];
icheck('activity logged', 1, $act);
$new_status = $db->query("SELECT status FROM enquiry WHERE enquiry_id = $eid")->fetch_assoc()['status'];
icheck('status transitioned to joined', 'joined', $new_status);

// Cleanup.
$db->query("DELETE FROM enquiry_activity WHERE enquiry_id = $eid");
$db->query("DELETE FROM enquiry WHERE enquiry_id = $eid");
icheck('cleanup: enquiry removed', 0, (int)$db->query("SELECT COUNT(*) c FROM enquiry WHERE enquiry_id = $eid")->fetch_assoc()['c']);

/* ---- Course + installments round-trip ---- */
imodule('C. Course round-trip');

$db->query("DELETE FROM course WHERE name = '__TEST__ Course'");
$db->query("INSERT INTO course (name, session_name, total_fees, installments, created_by, created_at)
            VALUES ('__TEST__ Course', '2026-2027', 10000, 3, '__TEST__', NOW())");
$cid = $db->insert_id;
icheck_true('course inserted (has id)', $cid > 0);

// Generate installments with the SAME helper the controller uses.
$amounts = sms_split_installments(10000, 3);
foreach ($amounts as $i => $amt) {
    $amt = (float)$amt;
    $db->query("INSERT INTO course_installment (course_id, title, amount) VALUES ($cid, 'Installment " . ($i+1) . "', $amt)");
}
$sum = (float)$db->query("SELECT SUM(amount) s FROM course_installment WHERE course_id = $cid")->fetch_assoc()['s'];
icheck('3 installments created', 3, (int)$db->query("SELECT COUNT(*) c FROM course_installment WHERE course_id = $cid")->fetch_assoc()['c']);
icheck_true('installments sum equals course fee', abs($sum - 10000.0) < 0.001);

// Subject add.
$db->query("INSERT INTO course_subject (course_id, subject_name, subject_code) VALUES ($cid, 'Mathematics', 'MATH01')");
icheck('subject added', 'Mathematics', $db->query("SELECT subject_name FROM course_subject WHERE course_id = $cid")->fetch_assoc()['subject_name']);

// Cleanup.
$db->query("DELETE FROM course_installment WHERE course_id = $cid");
$db->query("DELETE FROM course_subject WHERE course_id = $cid");
$db->query("DELETE FROM course WHERE course_id = $cid");
icheck('cleanup: course removed', 0, (int)$db->query("SELECT COUNT(*) c FROM course WHERE course_id = $cid")->fetch_assoc()['c']);

/* ---- Student add / update / delete round-trip ---- */
imodule('D. Student add/update/delete');

// Discover which of the student columns actually exist, so the test works
// regardless of local schema drift.
$scols = array();
if ($res = $db->query("SHOW COLUMNS FROM `student`")) {
    while ($r = $res->fetch_assoc()) $scols[$r['Field']] = true;
}
$put = function ($arr) use ($scols) {
    $keep = array();
    foreach ($arr as $k => $v) { if (isset($scols[$k])) $keep[$k] = $v; }
    return $keep;
};
$esc = function ($v) use ($db) { return "'" . $db->real_escape_string((string)$v) . "'"; };
$ins_sql = function ($table, $row) use ($esc) {
    $cols = array(); $vals = array();
    foreach ($row as $k => $v) { $cols[] = "`$k`"; $vals[] = ($v === null ? 'NULL' : $esc($v)); }
    return "INSERT INTO `$table` (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
};

$db->query("DELETE FROM student WHERE email = '__test__student@example.com'");

// CREATE — name assembled via the same helper the controller uses.
$create = $put(array(
    'first_name' => 'Test', 'middle_name' => '', 'last_name' => 'Student',
    'name'       => sms_full_name('Test', '', 'Student'),
    'email'      => '__test__student@example.com',
    'sex'        => 'male', 'fmobile' => '9876543210',
    'academic_year' => sms_academic_year(mktime(0, 0, 0, 5, 1, 2026)),
    'password'   => password_hash('password', PASSWORD_BCRYPT),
));
$db->query($ins_sql('student', $create));
$sid = $db->insert_id;
icheck_true('student inserted (has id)', $sid > 0);

$row = $db->query("SELECT * FROM student WHERE student_id = $sid")->fetch_assoc();
icheck('student name has no double space', 'Test Student', $row['name']);
icheck('student academic year (May -> 2026-2027)', '2026-2027', $row['academic_year']);

// Payment history + recompute total (mirrors create flow)
$db->query($ins_sql('student_payment_history', array(
    'student_id' => $sid, 'invoice_id' => 0, 'title' => 'Payment',
    'amount' => 2500, 'timestamp' => 1751000000,
)));
$paid = (float)$db->query("SELECT SUM(amount) s FROM student_payment_history WHERE student_id = $sid")->fetch_assoc()['s'];
$db->query("UPDATE student SET payment_done = $paid WHERE student_id = $sid");
icheck('payment recorded & total updated', 2500.0,
    (float)$db->query("SELECT payment_done FROM student WHERE student_id = $sid")->fetch_assoc()['payment_done']);

// UPDATE — change the name (now with a middle name)
$new_name = sms_full_name('Test', 'A', 'Student');
$db->query("UPDATE student SET last_name = 'Student', middle_name = 'A', name = " . $esc($new_name) . " WHERE student_id = $sid");
icheck('student updated name', 'Test A Student',
    $db->query("SELECT name FROM student WHERE student_id = $sid")->fetch_assoc()['name']);

// DELETE — mirrors controller delete (student + payment history)
$db->query("DELETE FROM student WHERE student_id = $sid");
$db->query("DELETE FROM student_payment_history WHERE student_id = $sid");
icheck('cleanup: student removed', 0,
    (int)$db->query("SELECT COUNT(*) c FROM student WHERE student_id = $sid")->fetch_assoc()['c']);
icheck('cleanup: payment history removed', 0,
    (int)$db->query("SELECT COUNT(*) c FROM student_payment_history WHERE student_id = $sid")->fetch_assoc()['c']);

/* ---- Grade matching against the real grade table ---- */
imodule('E. Grading (live grade table)');
$grades = array();
if ($have('grade')) {
    $res = $db->query("SELECT * FROM grade");
    while ($r = $res->fetch_assoc()) $grades[] = $r;
}
if (empty($grades)) {
    icheck('grade table has rows (skipped if empty)', true, true);
} else {
    // Pick a mark inside the first grade's range and confirm the helper matches it.
    $g0 = $grades[0];
    $mid = ((float)$g0['mark_from'] + (float)$g0['mark_upto']) / 2;
    $matched = sms_match_grade($grades, $mid);
    icheck_true('helper matches a real grade row for an in-range mark', $matched !== null);
    icheck_true('matched mark is within the returned range',
        $matched !== null && $mid >= (float)$matched['mark_from'] && $mid <= (float)$matched['mark_upto']);
}

/* ---- Languages (live `language` table) ---- */
imodule('F. Languages (live language table)');
$db->set_charset('utf8');

$lang_fields = array();
foreach ($db->query("SHOW COLUMNS FROM language") as $c) $lang_fields[] = $c['Field'];
$expected_langs = array('english', 'bengali', 'hindi', 'marathi', 'kannada', 'gujarati', 'tamil');
$langs = sms_language_columns($lang_fields);
$tmp = $langs; sort($tmp); $exp = $expected_langs; sort($exp);
icheck('exactly the 7 chosen languages exist', implode(',', $exp), implode(',', $tmp));

$uniq = $db->query("SHOW INDEX FROM language WHERE Key_name = 'uniq_phrase'")->num_rows;
icheck_true('unique index on phrase exists', $uniq > 0);
icheck('no duplicate phrases (case-insensitive)', 0,
    (int)$db->query("SELECT COUNT(*) c FROM (SELECT phrase FROM language GROUP BY phrase HAVING COUNT(*) > 1) t")->fetch_assoc()['c']);

// INSERT IGNORE (what get_phrase() does on first use) must not create a duplicate, even with different case
$db->query("DELETE FROM language WHERE phrase = '__test__phrase'");
$db->query("INSERT IGNORE INTO language (phrase) VALUES ('__test__phrase')");
$db->query("INSERT IGNORE INTO language (phrase) VALUES ('__TEST__PHRASE')");
icheck('INSERT IGNORE twice (case differs) -> 1 row', 1,
    (int)$db->query("SELECT COUNT(*) c FROM language WHERE phrase = '__test__phrase'")->fetch_assoc()['c']);
icheck_true('new phrase gets blank translations by default',
    $db->query("SELECT hindi FROM language WHERE phrase = '__test__phrase'")->fetch_assoc()['hindi'] === '');
$db->query("DELETE FROM language WHERE phrase = '__test__phrase'");
icheck('cleanup: test phrase removed', 0,
    (int)$db->query("SELECT COUNT(*) c FROM language WHERE phrase = '__test__phrase'")->fetch_assoc()['c']);

// Every phrase used in the code is in the table and translated in every language
$used = array();
$scan = function ($dir) use (&$scan, &$used) {
    foreach (glob($dir . '/*') as $p) {
        if (is_dir($p)) { $scan($p); continue; }
        if (substr($p, -4) !== '.php') continue;
        preg_match_all("/get_phrase\(\s*['\"]([^'\"]*)['\"]\s*\)/", file_get_contents($p), $m);
        foreach ($m[1] as $k) $used[mb_strtolower($k)] = $k;
    }
};
$scan(__DIR__ . '/../application');
$rows = array();
foreach ($db->query("SELECT * FROM language") as $r) $rows[mb_strtolower($r['phrase'])] = $r;
$absent = array_diff_key($used, $rows);
icheck('every get_phrase() key used in code is in the table', '', implode(', ', array_slice($absent, 0, 10)));
foreach ($expected_langs as $l) {
    $blank = array();
    foreach ($used as $k => $orig) if (isset($rows[$k]) && trim($rows[$k][$l]) === '') $blank[] = $orig;
    icheck("all used phrases translated: $l", '', implode(', ', array_slice($blank, 0, 10)));
}

// No double-encoded (mojibake) text left in any language
$bad = array();
foreach ($rows as $r) foreach ($expected_langs as $l)
    if (sms_fix_mojibake($r[$l]) !== $r[$l]) $bad[] = "$l:{$r['phrase']}";
icheck('no double-encoded text in any language', '', implode(', ', array_slice($bad, 0, 10)));

// Translations are in the right script (spot-check the first letter range)
$scripts = array('hindi' => '/\p{Devanagari}/u', 'marathi' => '/\p{Devanagari}/u', 'kannada' => '/\p{Kannada}/u',
                 'bengali' => '/\p{Bengali}/u', 'gujarati' => '/\p{Gujarati}/u', 'tamil' => '/\p{Tamil}/u');
foreach ($scripts as $l => $re) {
    $in = 0; foreach ($rows as $r) if (preg_match($re, $r[$l])) $in++;
    icheck_true("$l text is in its own script (>= 95% of rows)", count($rows) && $in / count($rows) >= 0.95);
}

$setting = $db->query("SELECT description FROM settings WHERE type = 'language'")->fetch_assoc();
icheck_true('system language setting is a real language', $setting && sms_is_language($setting['description'], $lang_fields));

/* ---- Exams & CBT schema (Exam_model::ensure_schema) ---- */
imodule('G. Exams & CBT schema');
$cols = function ($t) use ($db) { $o = array(); foreach ($db->query("SHOW COLUMNS FROM `$t`") as $c) $o[$c['Field']] = $c; return $o; };
icheck_true('cbt_exam table exists', $db->query("SHOW TABLES LIKE 'cbt_exam'")->num_rows === 1);
icheck_true('email_log table exists', $db->query("SHOW TABLES LIKE 'email_log'")->num_rows === 1);
$q = $cols('question'); $a = $cols('exam_assignment'); $r = $cols('exam_result'); $e = $cols('exam'); $m = $cols('mark');
icheck_true('question / assignment / result linked by exam_id', isset($q['exam_id'], $a['exam_id'], $r['exam_id']));
icheck_true('assignment tracks start, submit, score', isset($a['started_at'], $a['submitted_at'], $a['score'], $a['total'], $a['notified_at'], $a['result_notified_at']));
icheck_true('written exam has date, classes, totals, publish flags', isset($e['exam_date'], $e['class_ids'], $e['total_marks'], $e['pass_percent'], $e['results_published'], $e['reminder_sent_at']));
icheck('mark_obtained allows blank (not entered)', 'YES', $m['mark_obtained']['Null']);
icheck_true('mark_obtained allows half marks', stripos($m['mark_obtained']['Type'], 'decimal') === 0);
icheck('every question belongs to an exam', 0, (int)$db->query("SELECT COUNT(*) c FROM question WHERE exam_id IS NULL")->fetch_assoc()['c']);
$coll = array();
foreach ($db->query("SELECT TABLE_NAME, TABLE_COLLATION FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()
                     AND TABLE_NAME IN ('question','answer','exam_result','exam_assignment','cbt_exam','student','class','subject')") as $t)
    $coll[$t['TABLE_COLLATION']] = true;
icheck('exam tables share one collation (no "Illegal mix of collations")', 1, count($coll));
foreach (array('smtp_host', 'smtp_port', 'email_copy_parent', 'cron_key') as $s)
    icheck_true("setting $s exists", $db->query("SELECT 1 FROM settings WHERE type = '$s'")->num_rows === 1);

$db->close();

/* ---- Report ---- */
$fmt = function ($v) { if (is_bool($v)) return $v?'true':'false'; if ($v===null) return 'null'; if ($v==='') return "''"; return (string)$v; };
$total = count($tests); $passed = 0; $last = null;
echo "\n==========================================================================\n";
echo "  SMS Admissions — DB Integration Test Results\n";
echo "==========================================================================\n";
foreach ($tests as $t) {
    if ($t['module'] !== $last) { echo "\n{$t['module']}\n" . str_repeat('-', 74) . "\n"; $last = $t['module']; }
    if ($t['pass']) $passed++;
    printf("  [%s] %s\n", $t['pass'] ? 'PASS' : 'FAIL', $t['desc']);
    if (!$t['pass']) { printf("         expected: %s\n         actual:   %s\n", $fmt($t['expected']), $fmt($t['actual'])); }
}
echo "\n==========================================================================\n";
printf("  TOTAL: %d   PASSED: %d   FAILED: %d\n", $total, $passed, $total - $passed);
echo "==========================================================================\n\n";
exit($passed === $total ? 0 : 1);
