<?php
/**
 * Zero-dependency unit test runner for the Enquiry & Course modules.
 *
 * Runs the REAL application logic in application/helpers/sms_admissions_helper.php
 * (the same functions the controller and views call).
 *
 * Usage:  php tests/run_tests.php
 */

define('SMS_ADMISSIONS_TEST', true);
require __DIR__ . '/../application/helpers/sms_admissions_helper.php';
require __DIR__ . '/../application/helpers/sms_core_helper.php';
require __DIR__ . '/../application/helpers/sms_exam_helper.php';
require __DIR__ . '/../application/helpers/sms_portal_helper.php';

$GLOBALS['__tests']  = array();
$GLOBALS['__module'] = '';

function module($name) { $GLOBALS['__module'] = $name; }

/** Record one test case with its expected/actual values. */
function check($desc, $expected, $actual) {
    $pass = ($expected === $actual);
    $GLOBALS['__tests'][] = array(
        'module'   => $GLOBALS['__module'],
        'desc'     => $desc,
        'expected' => $expected,
        'actual'   => $actual,
        'pass'     => $pass,
    );
}

/** Assert a boolean condition, showing expected/actual as true/false. */
function check_true($desc, $actual) { check($desc, true, (bool)$actual); }

/* ===================================================================
 * MODULE 1 — Enquiry entry form
 * =================================================================== */
module('1. Enquiry entry');

check('next no when table empty (0)',        'ENQ-0001', sms_next_enquiry_no(0));
check('next no when table empty (null)',     'ENQ-0001', sms_next_enquiry_no(null));
check('next no pads to 4 digits',            'ENQ-0010', sms_next_enquiry_no(9));
check('next no for large id',                'ENQ-1234', sms_next_enquiry_no(1233));
check('next no beyond 4 digits keeps all',   'ENQ-10000', sms_next_enquiry_no(9999));

check_true('source "Existing student" reveals name field',  sms_is_existing_student_source('Existing student'));
check_true('source match is case/space tolerant',           sms_is_existing_student_source('  existing student '));
check('source "Google search" hides name field',   false, sms_is_existing_student_source('Google search'));
check('empty source hides name field',              false, sms_is_existing_student_source(''));

/* ===================================================================
 * MODULE 2 — Follow-up / status tracking
 * =================================================================== */
module('2. Follow-up status');

check_true('in_progress -> warning label',  strpos(sms_enquiry_status_label('in_progress'), 'label-warning') !== false);
check_true('in_progress text',              strpos(sms_enquiry_status_label('in_progress'), 'In Progress') !== false);
check_true('joined -> success label',       strpos(sms_enquiry_status_label('joined'), 'label-success') !== false);
check_true('not_joined -> danger label',    strpos(sms_enquiry_status_label('not_joined'), 'label-danger') !== false);
check_true('unknown status -> default label',strpos(sms_enquiry_status_label('archived'), 'label-default') !== false);
check_true('unknown status is escaped (XSS safe)', strpos(sms_enquiry_status_label('<script>'), '&lt;script&gt;') !== false);

/* ===================================================================
 * MODULE 3 — Bulk .xls / .csv import
 * =================================================================== */
module('3. Bulk import');

check('header "Contact No" -> contact_no',   'contact_no', sms_normalize_header('Contact No'));
check('header trims symbols & spaces',       'enquiry_no', sms_normalize_header('  Enquiry-No! '));
check('header lowercased',                   'mobile',     sms_normalize_header('MOBILE'));
check('blank header -> empty key',           '',           sms_normalize_header('   '));

$header = array('Name', 'Contact No', 'Name');            // duplicate "Name"
$map    = sms_build_header_map($header);
check('header map: name index',              0, isset($map['name']) ? $map['name'] : null);
check('header map: contact_no index',        1, isset($map['contact_no']) ? $map['contact_no'] : null);
check_true('header map: duplicate keeps first', $map['name'] === 0);

$row = array('John Doe', '9876543210');
check('cell by single key',                  '9876543210', sms_cell_value($row, $map, 'contact_no'));
check('cell by fallback keys (first present)', '9876543210', sms_cell_value($row, $map, array('mobile', 'contact_no', 'phone')));
check('cell missing key -> empty string',    '', sms_cell_value($row, $map, 'address'));
check('cell value is trimmed',               'Jane', sms_cell_value(array('  Jane  '), array('name' => 0), 'name'));

/* ===================================================================
 * MODULE 4 — Course fees / installments
 * =================================================================== */
module('4. Course fees');

$even = sms_split_installments(12000, 3);
check('even split 12000/3',                  array(4000.0, 4000.0, 4000.0), $even);
check_true('even split sums to total',       sms_installments_match(12000, array_sum($even)));

$odd = sms_split_installments(10000, 3);
check('uneven split 10000/3 (last absorbs)', array(3333.33, 3333.33, 3333.34), $odd);
check_true('uneven split still sums to total', sms_installments_match(10000, array_sum($odd)));

check('single installment = full fee',       array(1000.0), sms_split_installments(1000, 1));
check('installment count < 1 treated as 1',  array(1000.0), sms_split_installments(1000, 0));
check('zero fee splits to zeros',            array(0.0, 0.0, 0.0, 0.0), sms_split_installments(0, 4));

check('mismatch detected',            false, sms_installments_match(10000, 9999.99));
check('exact match detected',         true,  sms_installments_match(5000, 5000));

/* ===================================================================
 * MODULE 5 — Grading (Crud_model::get_grade logic)
 * =================================================================== */
module('5. Grading');

$grades = array(
    array('grade' => 'A+', 'mark_from' => 80, 'mark_upto' => 100),
    array('grade' => 'A',  'mark_from' => 70, 'mark_upto' => 79),
    array('grade' => 'B',  'mark_from' => 60, 'mark_upto' => 69),
    array('grade' => 'F',  'mark_from' => 0,  'mark_upto' => 59),
);
check('mark 95 -> A+',              'A+', sms_match_grade($grades, 95)['grade']);
check('mark 70 lower boundary -> A', 'A', sms_match_grade($grades, 70)['grade']);
check('mark 69 upper boundary -> B', 'B', sms_match_grade($grades, 69)['grade']);
check('mark 0 -> F',                 'F', sms_match_grade($grades, 0)['grade']);
check('mark 45 -> F',                'F', sms_match_grade($grades, 45)['grade']);
check('mark 59.5 in config gap -> null', null, sms_match_grade($grades, 59.5));
check('mark above range -> null',   null, sms_match_grade($grades, 150));
check('no grades configured -> null', null, sms_match_grade(array(), 50));

/* ===================================================================
 * MODULE 6 — Student fees (Modal::getStudentFeeSummary logic)
 * =================================================================== */
module('6. Student fees');

check('total paid = highest of 3 sources', 5000.0, sms_total_paid(5000, 3000, 4500));
check('total paid respects cached value',  7000.0, sms_total_paid(0, 0, 7000));
check('remaining = total - paid',          2000.0, sms_fee_remaining(10000, 8000));
check('remaining never negative',          0.0,    sms_fee_remaining(10000, 12000));
check('remaining full when nothing paid',  10000.0, sms_fee_remaining(10000, 0));

/* ===================================================================
 * MODULE 7 — Teacher salary (teacher_salary_slip logic)
 * =================================================================== */
module('7. Teacher salary');

$teacher = array(
    'basic_salary' => 20000, 'hra' => 10000, 'da' => 4000,
    'conveyance' => 2000, 'medical_allowance' => 2000, 'other_allowance' => 1000,
    'pf_deduction' => 2400, 'tax_deduction' => 1000, 'other_deduction' => 600,
);
check('CTC = sum of allowances',      39000.0, sms_salary_ctc($teacher));
check('net = CTC - deductions',       35000.0, sms_salary_net($teacher));
check('net never negative',           0.0,     sms_salary_net(array('basic_salary' => 1000, 'pf_deduction' => 5000)));
check('empty teacher -> 0 CTC',       0.0,     sms_salary_ctc(array()));

/* ===================================================================
 * MODULE 8 — Age & validation (student/enquiry forms)
 * =================================================================== */
module('8. Age & validation');

check('age exact birthday',        25, sms_age_from_dob('2000-07-10', '2025-07-10'));
check('age day before birthday',   24, sms_age_from_dob('2000-07-11', '2025-07-10'));
check('age day after birthday',    25, sms_age_from_dob('2000-07-09', '2025-07-10'));
check('age blank dob -> empty',    '', sms_age_from_dob('', '2025-07-10'));
check('age invalid dob -> empty',  '', sms_age_from_dob('not-a-date', '2025-07-10'));

check_true('valid 10-digit mobile', sms_valid_mobile('9876543210'));
check('9-digit mobile rejected',  false, sms_valid_mobile('987654321'));
check('mobile with letters rejected', false, sms_valid_mobile('98765abcd0'));
check_true('valid email', sms_valid_email('user@example.com'));
check('invalid email rejected', false, sms_valid_email('user@@example'));
check('money formats to 2dp', '1234.50', sms_money(1234.5));

/* ===================================================================
 * MODULE 9 — Student add / update / import
 * =================================================================== */
module('9. Student add/update/import');

// Full-name assembly (student create & update)
check('full name with middle',        'John Michael Doe', sms_full_name('John', 'Michael', 'Doe'));
check('full name no middle (no double space)', 'John Doe', sms_full_name('John', '', 'Doe'));
check('full name trims parts',         'John Doe',         sms_full_name('  John ', '', ' Doe '));
check('full name first only',          'Madonna',          sms_full_name('Madonna', '', ''));

// Full-name splitting (bulk import when only "name" column present)
$s2 = sms_split_full_name('John Doe');
check('split 2 words: first',  'John', $s2['first']);
check('split 2 words: last',   'Doe',  $s2['last']);
check('split 2 words: middle', '',     $s2['middle']);
$s3 = sms_split_full_name('John Adam Michael Doe');
check('split 4 words: first',  'John',       $s3['first']);
check('split 4 words: middle', 'Adam Michael', $s3['middle']);
check('split 4 words: last',   'Doe',        $s3['last']);
$s1 = sms_split_full_name('Madonna');
check('split 1 word: first',   'Madonna', $s1['first']);
check('split 1 word: last',    '',        $s1['last']);
check('split empty -> blanks', '',        sms_split_full_name('')['first']);

// Academic year (April–March)
check('academic year in May 2026',  '2026-2027', sms_academic_year(mktime(0, 0, 0, 5, 12, 2026)));
check('academic year in Feb 2026',  '2025-2026', sms_academic_year(mktime(0, 0, 0, 2, 5, 2026)));
check('academic year on 1 April',   '2026-2027', sms_academic_year(mktime(0, 0, 0, 4, 1, 2026)));
check('academic year on 31 March',  '2025-2026', sms_academic_year(mktime(0, 0, 0, 3, 31, 2026)));

// Student-own-mobile rule (kept only for class 10+)
check('mobile kept for class 10',   '9876543210', sms_mobile_for_class_number(10, '9876543210'));
check('mobile kept for class 12',   '9876543210', sms_mobile_for_class_number(12, '9876543210'));
check('mobile dropped for class 5', null,         sms_mobile_for_class_number(5, '9876543210'));
check('blank mobile -> null',       null,         sms_mobile_for_class_number(10, '   '));

// Payment extraction helpers
check('valid payment amount key -> index', '3', sms_is_payment_amount_key('payment3_amount'));
check('non-payment key -> null',    null, sms_is_payment_amount_key('first_name'));
check('payment date key -> null',   null, sms_is_payment_amount_key('payment3_date'));
check('payments total sums amounts', 4500.0, sms_payments_total(array(
    array('amount' => 1000), array('amount' => 3500.0), array('amount' => 0))));
check('payments total empty -> 0',  0.0, sms_payments_total(array()));

// WhatsApp/message template rendering
check('template replaces placeholders',
    'Hi Ravi, ID 42 at Shree School',
    sms_render_template('Hi {{studentname}}, ID {{studentid}} at {{schoolname}}',
        array('studentname' => 'Ravi', 'studentid' => 42, 'schoolname' => 'Shree School')));
check('template leaves unknown tokens intact',
    'Hi {{name}}', sms_render_template('Hi {{name}}', array('other' => 'x')));

// Shared import cell lookup works with RAW header keys (student import passes these)
$imp_header = sms_build_header_map(array('First Name', 'Date of Birth', 'Father Mobile'));
$imp_row    = array('Asha', '2010-06-01', '9998887776');
check('import cell by raw key "Date of Birth"', '2010-06-01', sms_cell_value($imp_row, $imp_header, 'Date of Birth'));
check('import cell by fallback raw keys', '9998887776', sms_cell_value($imp_row, $imp_header, array('phone', 'Father Mobile')));

/* ===================================================================
 * MODULE 10 — Manage profile / change password (Admin::manage_profile)
 * =================================================================== */
module('10. Manage profile');

check('valid profile -> no error',          null, sms_profile_error('Admin', 'admin@admin.com'));
check('blank name rejected',                'name_is_required', sms_profile_error('   ', 'admin@admin.com'));
check('blank email rejected',               'email_is_required', sms_profile_error('Admin', ''));
check('malformed email rejected',           'invalid_email_address', sms_profile_error('Admin', 'admin@@x'));
check('email padded with spaces accepted',  null, sms_profile_error('Admin', '  admin@admin.com  '));
check('email used by another admin rejected', 'email_already_in_use', sms_profile_error('Admin', 'a@b.com', true));

check('correct password change -> no error', null, sms_password_change_error('admin', 'admin', 'new1', 'new1'));
check('wrong current password rejected',   'current_password_is_incorrect', sms_password_change_error('admin', 'wrong', 'new1', 'new1'));
check('blank current password rejected',   'current_password_is_incorrect', sms_password_change_error('admin', '', 'new1', 'new1'));
check('empty new password rejected',       'new_password_is_required', sms_password_change_error('admin', 'admin', '', ''));
check('whitespace new password rejected',  'new_password_is_required', sms_password_change_error('admin', 'admin', '   ', '   '));
check('confirm mismatch rejected',         'new_passwords_do_not_match', sms_password_change_error('admin', 'admin', 'new1', 'new2'));
check('password check is case sensitive',  'current_password_is_incorrect', sms_password_change_error('admin', 'ADMIN', 'n', 'n'));
$bc = password_hash('password', PASSWORD_BCRYPT);
check_true('bcrypt-stored password matches',     sms_password_matches('password', $bc));
check('wrong password vs bcrypt rejected',       false, sms_password_matches('Password', $bc));
check('typing the hash itself is rejected',      false, sms_password_matches($bc, $bc));
check_true('legacy plain-text password matches', sms_password_matches('admin', 'admin'));
check('blank password never matches',            false, sms_password_matches('', ''));
check('change password works for hashed accounts', null, sms_password_change_error($bc, 'password', 'new1', 'new1'));

$tmp_dir = sys_get_temp_dir();
$png = $tmp_dir . '/sms_test_photo.png';
file_put_contents($png, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='));
$txt = $tmp_dir . '/sms_test_photo.jpg';
file_put_contents($txt, '<?php echo "not an image"; ?>');
check('no photo chosen -> no error',       null, sms_upload_image_error(array('error' => UPLOAD_ERR_NO_FILE, 'tmp_name' => '')));
check('missing $_FILES entry -> no error', null, sms_upload_image_error(null));
check('real PNG accepted',                 null, sms_upload_image_error(array('error' => UPLOAD_ERR_OK, 'tmp_name' => $png)));
check('non-image renamed .jpg rejected',   'photo_must_be_an_image', sms_upload_image_error(array('error' => UPLOAD_ERR_OK, 'tmp_name' => $txt)));
check('upload over size limit rejected',   'photo_upload_failed', sms_upload_image_error(array('error' => UPLOAD_ERR_INI_SIZE, 'tmp_name' => '')));
@unlink($png); @unlink($txt);

check('photo URL gets version from mtime', 'http://x/uploads/admin_image/3.jpg?v=1700000000',
    sms_cache_busted_url('http://x/uploads/admin_image/3.jpg', 1700000000));
check('photo URL with query uses &',       'http://x/a.jpg?s=1&v=5', sms_cache_busted_url('http://x/a.jpg?s=1', 5));
check('no mtime -> URL unchanged',         'http://x/a.jpg', sms_cache_busted_url('http://x/a.jpg', false));

/* ===================================================================
 * MODULE 11 — Languages (get_phrase / manage_language / select_language)
 * =================================================================== */
module('11. Languages');

// Encoding repair (UTF-8 text that was re-saved as latin1 once or twice)
$dbl = function ($s) { return mb_convert_encoding(mb_convert_encoding($s, 'UTF-8', 'Windows-1252'), 'UTF-8', 'Windows-1252'); };
check('double-encoded French repaired',     'étudiant', sms_fix_mojibake('ÃƒÂ©tudiant'));
check('single-encoded French repaired',     'étudiant', sms_fix_mojibake('Ã©tudiant'));
check('double-encoded Hindi repaired',      'छात्र', sms_fix_mojibake($dbl('छात्र')));
check('double-encoded Bengali repaired',    'লগইন', sms_fix_mojibake($dbl('লগইন')));
check('correct Hindi left unchanged',       'प्रशासन', sms_fix_mojibake('प्रशासन'));
check('correct accented text unchanged',    'año café', sms_fix_mojibake('año café'));
check('plain ASCII unchanged',              'Manage Student', sms_fix_mojibake('Manage Student'));
check('empty string unchanged',             '', sms_fix_mojibake(''));

$lang_fields = array('phrase_id', 'phrase', 'english', 'hindi', 'marathi');
check('language columns drop key fields',   array('english', 'hindi', 'marathi'), sms_language_columns($lang_fields));
check_true('hindi is a language',           sms_is_language('hindi', $lang_fields));
check('"phrase" column is not a language',  false, sms_is_language('phrase', $lang_fields));
check('SQL in language name rejected',      false, sms_is_language("english' OR 1=1 --", $lang_fields));
check('unknown language rejected',          false, sms_is_language('klingon', $lang_fields));

check('add new language ok',                null, sms_language_add_error('Tamil', $lang_fields));
check('add blank language rejected',        'language_name_is_required', sms_language_add_error('  ', $lang_fields));
check('add language with symbols rejected', 'language_name_letters_only', sms_language_add_error('tamil`; DROP', $lang_fields));
check('add language with digits rejected',  'language_name_letters_only', sms_language_add_error('lang2', $lang_fields));
check('add existing language rejected',     'language_already_exists', sms_language_add_error('Hindi', $lang_fields));
check('add reserved "phrase" rejected',     'language_already_exists', sms_language_add_error('phrase', $lang_fields));

check('delete other language ok',           null, sms_language_delete_error('marathi', 'hindi', $lang_fields));
check('delete english blocked',             'english_cannot_be_deleted', sms_language_delete_error('english', 'hindi', $lang_fields));
check('delete current language blocked',    'current_language_cannot_be_deleted', sms_language_delete_error('hindi', 'hindi', $lang_fields));
check('delete "phrase" column blocked',     'language_not_found', sms_language_delete_error('phrase', 'hindi', $lang_fields));
check('delete unknown language blocked',    'language_not_found', sms_language_delete_error('klingon', 'hindi', $lang_fields));

check('humanize phrase key',                'Manage Student', sms_humanize_phrase('manage_student'));
check('language label native + English',    'हिन्दी (Hindi)', sms_language_label('hindi'));
check('language label for English',         'English', sms_language_label('english'));
check('language label for unknown',         'Telugu', sms_language_label('telugu'));

// Duplicate phrase rows: keep the translated one, fill gaps from the rest
$dupes = array(
    array('phrase_id' => 384,  'phrase' => 'Accountants', 'english' => '',            'hindi' => ''),
    array('phrase_id' => 900,  'phrase' => 'accountants', 'english' => 'Accountants', 'hindi' => 'लेखाकार'),
    array('phrase_id' => 1200, 'phrase' => 'Accountants', 'english' => '',            'hindi' => ''),
);
$plan = sms_pick_phrase_keeper($dupes, array('english', 'hindi'));
check('dedupe keeps the translated row',    900, $plan['keep_id']);
check('dedupe deletes the others',          array(384, 1200), $plan['delete_ids']);
$plan = sms_pick_phrase_keeper(array(
    array('phrase_id' => 5, 'phrase' => 'student', 'english' => 'Student', 'hindi' => ''),
    array('phrase_id' => 9, 'phrase' => 'Student', 'english' => '',        'hindi' => 'छात्र'),
), array('english', 'hindi'));
check('dedupe tie keeps lowest id',         5, $plan['keep_id']);
check('dedupe fills blanks from duplicates', array('hindi' => 'छात्र'), $plan['fill']);

check('page bounds: first page',            array(1, 0, 19),   sms_page_bounds(1, 50, 911));
check('page bounds: last page',             array(19, 900, 19), sms_page_bounds(19, 50, 911));
check('page bounds: page past end clamps',  array(19, 900, 19), sms_page_bounds(99, 50, 911));
check('page bounds: junk page -> 1',        array(1, 0, 19),   sms_page_bounds('abc', 50, 911));
check('page bounds: no rows -> 1 page',     array(1, 0, 1),    sms_page_bounds(3, 50, 0));

/* ===================================================================
 * MODULE 12 — CBT timing, marking & publishing (sms_exam_helper)
 * =================================================================== */
module('12. CBT exams');

$cbt = array('status' => 'published', 'exam_date' => '2026-10-10', 'start_time' => '10:00:00', 'end_time' => null, 'duration' => 30);
$t0 = strtotime('2026-10-10 10:00:00');
check('window = start .. start + duration',     array($t0, $t0 + 1800), sms_cbt_window($cbt));
check('window uses last-entry time when later', array($t0, $t0 + 7200), sms_cbt_window(array('end_time' => '12:00:00') + $cbt));
check('end before start falls back to duration', array($t0, $t0 + 1800), sms_cbt_window(array('end_time' => '09:00:00') + $cbt));
check('bad date -> no window',                  array(null, null), sms_cbt_window(array('exam_date' => '10/10/2026') + $cbt));
check('state before start = upcoming',          'upcoming', sms_cbt_state($cbt, $t0 - 1));
check('state at start = open',                  'open', sms_cbt_state($cbt, $t0));
check('state at close = closed',                'closed', sms_cbt_state($cbt, $t0 + 1800));
check('unpublished exam = draft',               'draft', sms_cbt_state(array('status' => 'draft') + $cbt, $t0));
check('deadline = started + duration',          $t0 + 600 + 1800, sms_cbt_deadline(array('end_time' => '12:00:00') + $cbt, $t0 + 600));
check('late start ends at window close',        $t0 + 1800, sms_cbt_deadline($cbt, $t0 + 600));

check_true('answer match is case/space tolerant', sms_cbt_is_correct(' b ', 'B'));
check('blank answer is never correct',          false, sms_cbt_is_correct('', ''));
$qs = array(array('question_id' => 1, 'correct_answers' => 'A', 'marks' => 1),
            array('question_id' => 2, 'correct_answers' => 'C', 'marks' => 2),
            array('question_id' => 3, 'correct_answers' => 'B', 'marks' => 1));
$sc = sms_cbt_score($qs, array(1 => 'A', 2 => 'D'));
check('score: 1 right (1), 1 wrong, 1 blank',   array(1.0, 4.0, 1, 1, 1), array($sc['score'], $sc['total'], $sc['correct'], $sc['wrong'], $sc['unanswered']));
check('per-question awarded marks',            array(1 => 1.0, 2 => 0.0, 3 => 0.0), $sc['awarded']);
check('all correct = full marks',               4.0, sms_cbt_score($qs, array(1 => 'a', 2 => 'c', 3 => 'b'))['score']);

$ready = array(array('question' => '2+2?', 'correct_answers' => 'B', 'marks' => 1, 'options' => array('A' => '3', 'B' => '4', 'C' => '', 'D' => '')));
$hdr = array('title' => 'Unit 1', 'exam_date' => '2026-10-10', 'start_time' => '10:00', 'duration' => 30);
check('complete exam has no publish problems',  array(), sms_cbt_publish_problems($hdr, $ready));
check('no questions blocks publishing',         array('The exam has no questions'), sms_cbt_publish_problems($hdr, array()));
$bad = array(array('question' => 'Question 1', 'correct_answers' => 'C', 'marks' => 0, 'options' => array('A' => 'x', 'B' => '', 'C' => '', 'D' => '')));
check('placeholder text / 1 option / empty correct / 0 marks all reported', 4, count(sms_cbt_publish_problems($hdr, $bad)));
check('missing title reported',                 'Exam title is missing', sms_cbt_publish_problems(array('title' => ' ') + $hdr, $ready)[0]);

/* ===================================================================
 * MODULE 13 — Results, grades & marks (sms_exam_helper)
 * =================================================================== */
module('13. Results & grades');

check('percent 3 of 4',                 75.0, sms_percent(3, 4));
check('percent with zero total',        0.0, sms_percent(5, 0));
check_true('35% passes at 35',          sms_is_pass(35, 35));
check('34.99% fails at 35',             false, sms_is_pass(34.99, 35));
check('competition ranking 1,2,2,4',    array(0 => 1, 1 => 2, 2 => 2, 3 => 4),
      sms_rank(array(array('score' => 90), array('score' => 80), array('score' => 80), array('score' => 70))));
check('rank ignores input order',       array(1 => 1, 0 => 2), sms_rank(array(array('score' => 10), array('score' => 20))));

$gr = array(array('grade_id' => 1, 'mark_from' => 81, 'mark_upto' => 90), array('grade_id' => 2, 'mark_from' => 91, 'mark_upto' => 100));
check('new non-overlapping grade ok',   null, sms_grade_error('B1', 71, 80, $gr));
check('overlapping grade rejected',     'grade_range_overlaps_another_grade', sms_grade_error('X', 85, 95, $gr));
check('editing a grade ignores itself', null, sms_grade_error('A2', 80, 90, $gr, 1));
check('from > upto rejected',           'mark_from_must_not_exceed_mark_upto', sms_grade_error('X', 60, 50, $gr));
check('above 100 rejected',             'grade_range_must_be_0_to_100', sms_grade_error('X', 95, 110, array()));
check('blank grade name rejected',      'grade_name_is_required', sms_grade_error(' ', 1, 2, array()));
$def = sms_default_grades();
check('standard grades: 8 bands, A1 at top', array(8, 'A1', 'E'), array(count($def), $def[0]['name'], $def[7]['name']));
$covered = true; for ($p = 0; $p <= 100; $p++) if (!sms_grade_for_percent($def, $p)) $covered = false;
check_true('standard grades cover every whole % from 0 to 100', $covered);
check('fractional % in band gap -> lower band (90.5 -> A2)', 'A2', sms_grade_for_percent($def, 90.5)['name']);
check('85% -> A2',                      'A2', sms_grade_for_percent($def, 85)['name']);
check('32.4% -> E',                     'E', sms_grade_for_percent($def, 32.4)['name']);

check('blank mark = not entered (ok)',  null, sms_mark_error('', 100));
check('mark above total rejected',      'marks_must_be_between_0_and_total', sms_mark_error('120', 100));
check('negative mark rejected',         'marks_must_be_between_0_and_total', sms_mark_error('-1', 100));
check('text mark rejected',             'marks_must_be_numbers', sms_mark_error('abc', 100));
check('half mark ok',                   null, sms_mark_error('37.5', 100));

check('legacy m/d/Y date parsed',       '2017-07-05', sms_parse_exam_date('07/05/2017'));
check('d-m-Y date parsed',              '2026-10-25', sms_parse_exam_date('25-10-2026'));
check('ISO date kept',                  '2026-10-25', sms_parse_exam_date('2026-10-25'));
check('impossible date rejected',       '', sms_parse_exam_date('02/30/2026'));
check('reminder due the day before',    true, sms_reminder_due('2026-10-11', '2026-10-10'));
check('no reminder on the exam day',    false, sms_reminder_due('2026-10-10', '2026-10-10'));
check('number trimmed: 5.00 -> 5, 2.50 -> 2.5', array('5', '2.5'), array(sms_num(5), sms_num(2.5)));

/* ===================================================================
 * MODULE 14 — Email recipients & templates (sms_exam_helper)
 * =================================================================== */
module('14. Exam emails');

check('student + parent when copying',  array('s@x.com', 'p@x.com'), sms_email_recipients('S@x.com ', 'p@x.com', true));
check('parent skipped when not copying', array('s@x.com'), sms_email_recipients('s@x.com', 'p@x.com', false));
check('same address only once',         array('a@x.com'), sms_email_recipients('a@x.com', 'A@x.com', true));
check('invalid / blank addresses dropped', array('p@x.com'), sms_email_recipients('not-an-email', 'p@x.com', true));
check('no addresses -> empty list',     array(), sms_email_recipients('', '', true));

$school = array('name' => 'Shree <Classes>', 'phone' => '123', 'address' => 'Thane');
$v = array('student' => 'Asha <b>', 'title' => 'Unit 1', 'subject' => 'Maths', 'class' => '5th', 'date' => '2026-10-10',
           'start' => '10:00 AM', 'end' => '10:30 AM', 'duration' => 30, 'total_marks' => 20, 'mode' => 'online',
           'instructions' => "No calculators", 'portal_url' => 'http://x/index.php?student/exams');
list($subj, $html) = sms_email_exam_scheduled($school, $v);
check('scheduled subject has title and date', 'Exam scheduled: Unit 1 on 10 Oct 2026', $subj);
check_true('scheduled email escapes names (XSS safe)', strpos($html, 'Asha &lt;b&gt;') !== false && strpos($html, 'Shree &lt;Classes&gt;') !== false);
check_true('scheduled email has portal link & instructions', strpos($html, 'student/exams') !== false && strpos($html, 'No calculators') !== false);
list($subj) = sms_email_exam_scheduled($school, $v, true);
check('reminder subject',               'Reminder: Unit 1 on 10 Oct 2026', $subj);
list($subj, $html) = sms_email_exam_scheduled($school, array('mode' => 'written', 'portal_url' => '') + $v);
check('written exam email has no portal link', false, strpos($html, 'student/exams'));
list($subj, $html) = sms_email_result($school, array('student' => 'Asha', 'title' => 'Term 1', 'rows' => array(array('Maths', '45', 50)),
    'score' => 45, 'total' => 50, 'percent' => 90, 'grade' => 'A2', 'pass' => true, 'rank' => 1, 'portal_url' => ''));
check('result subject',                 'Result published: Term 1', $subj);
check_true('result email has marks, %, grade, pass, rank', strpos($html, '45 / 50') !== false && strpos($html, '90.00%') !== false
    && strpos($html, 'A2') !== false && strpos($html, 'Pass') !== false && strpos($html, '>1<') !== false);
check_true('no vendor footer in emails', strpos($html, 'optimumlinkup') === false);

/* ===================================================================
 * MODULE 15 — Theme & colours (sms_core_helper)
 * =================================================================== */
module('15. Theme');

check('5 presets incl. classic',        array('sunshine', 'ocean', 'bubblegum', 'jungle', 'classic'), array_keys(sms_theme_presets()));
check_true('valid hex #abc / #a1b2c3',  sms_valid_hex_color('#abc') && sms_valid_hex_color('#A1B2C3'));
check('invalid colour rejected',        false, sms_valid_hex_color('red; } body { display:none'));
check('mix with white 100%',            '#ffffff', sms_mix_color('#123456', 1));
check('mix with black 100%',            '#000000', sms_mix_color('#123456', -1));
check('mix 0% unchanged (3-digit expanded)', '#aabbcc', sms_mix_color('#abc', 0));
$tv = sms_theme_vars('ocean');
check('ocean primary used',             '#2d8cf0', $tv['--c-primary']);
check('classic -> no theme (original look)', null, sms_theme_vars('classic'));
check('unknown preset -> sunshine',     '#ff8a3d', sms_theme_vars('nope')['--c-primary']);
$tv = sms_theme_vars('ocean', '#7B2FF7', '#ff6ec7');
check('custom colours override preset', array('#7b2ff7', '#ff6ec7'), array($tv['--c-primary'], $tv['--c-accent']));
check('bad custom colour ignored (CSS-injection safe)', '#2d8cf0', sms_theme_vars('ocean', '#fff;}*{x:y')['--c-primary']);
check_true('font choice applied',        strpos(sms_theme_vars('sunshine', '', '', 'baloo')['--f-main'], "'Baloo 2'") === 0);

/* ===================================================================
 * MODULE 16 — Portals & menu permissions (sms_portal_helper)
 * =================================================================== */
module('16. Portals & permissions');

check('three portal roles',                    array('teacher', 'parent', 'student'), array_keys(sms_portal_menus()));
check_true('defaults: teacher marks on',       sms_menu_allowed(array(), 'teacher', 'marks'));
check('defaults: student fees off',            false, sms_menu_allowed(array(), 'student', 'fees'));
check('saved 0 switches a menu off',           false, sms_menu_allowed(array('teacher' => array('marks' => 0)), 'teacher', 'marks'));
check_true('saved 1 switches a menu on',       sms_menu_allowed(array('student' => array('fees' => 1)), 'student', 'fees'));
check_true('dashboard is always on',           sms_menu_allowed(array('teacher' => array('dashboard' => 0)), 'teacher', 'dashboard'));
check_true('profile is always on',             sms_menu_allowed(array('parent' => array('profile' => 0)), 'parent', 'profile'));
check('unknown menu is never allowed',         false, sms_menu_allowed(array(), 'teacher', 'admin_settings'));
check('unknown role is never allowed',         false, sms_menu_allowed(array(), 'hacker', 'dashboard'));
check('malformed saved JSON -> defaults',      array(), sms_parse_menu_permissions('{not json'));
$mx = sms_menu_permissions_from_post(array('teacher' => array('marks' => '1')));
check('form -> matrix: ticked on, unticked off, locked forced on', array(1, 0, 1), array($mx['teacher']['marks'], $mx['teacher']['attendance'], $mx['teacher']['dashboard']));
check_true('form -> matrix covers every role & menu', count($mx['parent']) === count(sms_portal_menus()['parent']));

check('attendance today ok',                   null, sms_attendance_date_error('2026-10-02', '2026-10-02'));
check('attendance 7 days back ok',             null, sms_attendance_date_error('2026-09-25', '2026-10-02'));
check('attendance 8 days back locked',         'attendance_older_than_7_days_is_locked', sms_attendance_date_error('2026-09-24', '2026-10-02'));
check('attendance future refused',             'attendance_cannot_be_marked_for_a_future_date', sms_attendance_date_error('2026-10-03', '2026-10-02'));
check('attendance bad date refused',           'invalid_date', sms_attendance_date_error('02/10/2026', '2026-10-02'));
check('attendance summary 3P 1A = 75%',        array('present' => 3, 'absent' => 1, 'marked' => 4, 'percent' => 75.0),
      sms_attendance_summary(array(array('status' => 1), array('status' => 1), array('status' => 2), array('status' => 1), array('status' => 0))));
check('attendance summary empty -> no %',      null, sms_attendance_summary(array())['percent']);

/* ===================================================================
 * MODULE 17 — Project-wide syntax lint (every PHP file compiles)
 * =================================================================== */
module('17. Project syntax lint');

// First-party application code only (third-party application/libraries/* is
// vendored and out of scope for our unit tests).
$scan_dirs = array(
    '/../application/controllers',
    '/../application/models',
    '/../application/views',
    '/../application/helpers',
    '/../application/config',
    '/../tests',
);
$php_files = array(realpath(__DIR__ . '/../index.php'));
$collect = function ($path) use (&$collect, &$php_files) {
    if (is_file($path)) {
        if (substr($path, -4) === '.php') $php_files[] = $path;
        return;
    }
    foreach (glob(rtrim($path, '/') . '/*') as $child) { $collect($child); }
};
foreach ($scan_dirs as $d) { $collect(__DIR__ . $d); }
$php_files = array_values(array_filter($php_files));

$lint_failures = array();
foreach ($php_files as $file) {
    $out = array(); $code = 0;
    exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file) . ' 2>&1', $out, $code);
    if ($code !== 0) {
        $lint_failures[] = basename($file) . ': ' . trim(implode(' ', $out));
    }
}
check(count($php_files) . ' project PHP files compile (0 syntax errors)', array(), $lint_failures);

/* ===================================================================
 * REPORT
 * =================================================================== */
$fmt = function ($v) {
    if (is_bool($v)) return $v ? 'true' : 'false';
    if (is_array($v)) return '[' . implode(', ', array_map(function ($x) { return is_float($x) ? rtrim(rtrim(number_format($x, 2, '.', ''), '0'), '.') : $x; }, $v)) . ']';
    if ($v === null) return 'null';
    if ($v === '') return "''";
    return (string)$v;
};

$total = count($GLOBALS['__tests']);
$passed = 0;
$last_module = null;

echo "\n";
echo "==========================================================================\n";
echo "  SMS Admissions — Unit Test Results\n";
echo "==========================================================================\n";

foreach ($GLOBALS['__tests'] as $i => $t) {
    if ($t['module'] !== $last_module) {
        echo "\n" . $t['module'] . "\n";
        echo str_repeat('-', 74) . "\n";
        $last_module = $t['module'];
    }
    $status = $t['pass'] ? 'PASS' : 'FAIL';
    if ($t['pass']) $passed++;
    printf("  [%s] %s\n", $status, $t['desc']);
    if (!$t['pass']) {
        printf("         expected: %s\n", $fmt($t['expected']));
        printf("         actual:   %s\n", $fmt($t['actual']));
    }
}

echo "\n==========================================================================\n";
printf("  TOTAL: %d   PASSED: %d   FAILED: %d\n", $total, $passed, $total - $passed);
echo "==========================================================================\n\n";

exit($passed === $total ? 0 : 1);
