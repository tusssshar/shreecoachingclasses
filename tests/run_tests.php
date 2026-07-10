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
 * MODULE 10 — Project-wide syntax lint (every PHP file compiles)
 * =================================================================== */
module('10. Project syntax lint');

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
