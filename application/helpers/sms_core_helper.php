<?php
if (!defined('BASEPATH')) {
    // Allow direct include by the CLI test runner without full CodeIgniter bootstrap.
    if (!defined('SMS_ADMISSIONS_TEST')) {
        exit('No direct script access allowed');
    }
}

/**
 * Pure, side-effect-free logic used across the SMS project (grading, fees,
 * salary, age, validation). Extracted from the controllers/models/views so the
 * behaviour can be unit tested in isolation while the real code calls the same
 * functions. No $this->db / session / superglobals here.
 */

/* -------------------------------------------------------------------------
 * Grading  (from Crud_model::get_grade)
 * ---------------------------------------------------------------------- */
if (!function_exists('sms_match_grade')) {
    /**
     * Find the grade row whose [mark_from, mark_upto] range contains $mark.
     * @param array $grades rows each with 'mark_from' and 'mark_upto'
     * @return array|null   the matching grade row, or null if none
     */
    function sms_match_grade($grades, $mark) {
        $mark = (float)$mark;
        foreach ((array)$grades as $row) {
            if ($mark >= (float)$row['mark_from'] && $mark <= (float)$row['mark_upto']) {
                return $row;
            }
        }
        return null;
    }
}

/* -------------------------------------------------------------------------
 * Student fees  (from Modal::getStudentFeeSummary)
 * ---------------------------------------------------------------------- */
if (!function_exists('sms_total_paid')) {
    /** Most reliable "paid" figure across history / legacy / cached sources. */
    function sms_total_paid($history_paid, $legacy_paid, $cached_paid) {
        return max((float)$history_paid, (float)$legacy_paid, (float)$cached_paid);
    }
}

if (!function_exists('sms_fee_remaining')) {
    /** Outstanding fee, never negative. */
    function sms_fee_remaining($total_fees, $paid) {
        return (float)max((float)$total_fees - (float)$paid, 0);
    }
}

/* -------------------------------------------------------------------------
 * Teacher salary  (from teacher_salary_slip view)
 * ---------------------------------------------------------------------- */
if (!function_exists('sms_salary_ctc')) {
    /** Monthly CTC = sum of all allowance components. */
    function sms_salary_ctc($t) {
        return (float)($t['basic_salary'] ?? 0)
             + (float)($t['hra'] ?? 0)
             + (float)($t['da'] ?? 0)
             + (float)($t['conveyance'] ?? 0)
             + (float)($t['medical_allowance'] ?? 0)
             + (float)($t['other_allowance'] ?? 0);
    }
}

if (!function_exists('sms_salary_net')) {
    /** Net take-home = CTC minus deductions, never below zero. */
    function sms_salary_net($t) {
        $deductions = (float)($t['pf_deduction'] ?? 0)
                    + (float)($t['tax_deduction'] ?? 0)
                    + (float)($t['other_deduction'] ?? 0);
        return (float)max(0, sms_salary_ctc($t) - $deductions);
    }
}

/* -------------------------------------------------------------------------
 * Age  (PHP port of calculateAgeFromDob in student_add view)
 * ---------------------------------------------------------------------- */
if (!function_exists('sms_age_from_dob')) {
    /**
     * Whole-year age from a date-of-birth string.
     * @param string $dob   e.g. '2000-05-20'
     * @param string $today reference date (defaults to today) — pass a fixed
     *                      value for deterministic tests
     * @return int|string   age in years, or '' when the DOB is unparseable
     */
    function sms_age_from_dob($dob, $today = null) {
        if (empty($dob)) return '';
        $b = date_create($dob);
        if (!$b) return '';
        $n = $today ? date_create($today) : date_create('today');
        if (!$n) return '';
        $age = (int)$n->format('Y') - (int)$b->format('Y');
        $m   = (int)$n->format('n') - (int)$b->format('n');
        if ($m < 0 || ($m === 0 && (int)$n->format('j') < (int)$b->format('j'))) {
            $age--;
        }
        return $age;
    }
}

/* -------------------------------------------------------------------------
 * Validation  (mobile pattern ^[0-9]{10}$ used in student/enquiry forms)
 * ---------------------------------------------------------------------- */
if (!function_exists('sms_valid_mobile')) {
    /** True for exactly 10 digits. */
    function sms_valid_mobile($mobile) {
        return (bool)preg_match('/^[0-9]{10}$/', (string)$mobile);
    }
}

if (!function_exists('sms_valid_email')) {
    /** True for a syntactically valid email address. */
    function sms_valid_email($email) {
        return filter_var((string)$email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('sms_money')) {
    /** Format a number as 2-decimal money, matching number_format usage. */
    function sms_money($n) {
        return number_format((float)$n, 2, '.', '');
    }
}

/* -------------------------------------------------------------------------
 * Student add / update / import
 * ---------------------------------------------------------------------- */
if (!function_exists('sms_full_name')) {
    /** Join first/middle/last into a single clean name (no double spaces). */
    function sms_full_name($first, $middle, $last) {
        $parts = array_filter(array(trim((string)$first), trim((string)$middle), trim((string)$last)), function ($p) {
            return $p !== '';
        });
        return implode(' ', $parts);
    }
}

if (!function_exists('sms_split_full_name')) {
    /**
     * Split a full name into first / middle / last, matching the bulk-import
     * rule: first word = first name, last word = last name, the rest = middle.
     * @return array{first:string,middle:string,last:string}
     */
    function sms_split_full_name($full) {
        $full = trim((string)$full);
        if ($full === '') return array('first' => '', 'middle' => '', 'last' => '');
        $parts = preg_split('/\s+/', $full);
        $first = array_shift($parts);
        $middle = '';
        $last = '';
        if (count($parts) === 1) {
            $last = array_pop($parts);
        } elseif (count($parts) > 1) {
            $last = array_pop($parts);
            $middle = implode(' ', $parts);
        }
        return array('first' => $first, 'middle' => $middle, 'last' => $last);
    }
}

if (!function_exists('sms_academic_year')) {
    /**
     * Indian academic year string (April–March) for a timestamp.
     * e.g. 12 May 2026 -> "2026-2027";  5 Feb 2026 -> "2025-2026".
     */
    function sms_academic_year($ts = null) {
        $ts = $ts ?: time();
        $y = (int)date('Y', $ts);
        $m = (int)date('n', $ts);
        return ($m >= 4) ? ($y . '-' . ($y + 1)) : (($y - 1) . '-' . $y);
    }
}

if (!function_exists('sms_mobile_for_class_number')) {
    /** Student's own mobile is kept only for class 10+ (else null); blank -> null. */
    function sms_mobile_for_class_number($class_number, $mobile) {
        $mobile = trim((string)$mobile);
        if ($mobile === '') return null;
        return ((int)$class_number >= 10) ? $mobile : null;
    }
}

if (!function_exists('sms_render_template')) {
    /** Replace {{placeholder}} tokens in a message template. */
    function sms_render_template($template, $vars) {
        $map = array();
        foreach ((array)$vars as $k => $v) {
            $map['{{' . $k . '}}'] = (string)$v;
        }
        return strtr((string)$template, $map);
    }
}

if (!function_exists('sms_is_payment_amount_key')) {
    /** Return the payment row index for a "paymentN_amount" POST key, else null. */
    function sms_is_payment_amount_key($key) {
        if (preg_match('/^payment(\d+)_amount$/', (string)$key, $m)) {
            return $m[1];
        }
        return null;
    }
}

if (!function_exists('sms_payments_total')) {
    /** Sum the 'amount' field across extracted payment rows. */
    function sms_payments_total($payments) {
        $total = 0.0;
        foreach ((array)$payments as $p) {
            $total += (float)($p['amount'] ?? 0);
        }
        return $total;
    }
}
