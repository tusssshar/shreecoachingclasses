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

/* -------------------------------------------------------------------------
 * Manage profile / change password  (Admin::manage_profile)
 * ---------------------------------------------------------------------- */
if (!function_exists('sms_profile_error')) {
    /**
     * Validate the profile form. Returns a phrase key for the first problem,
     * or null when valid. $email_taken = another account already uses $email.
     */
    function sms_profile_error($name, $email, $email_taken = false) {
        if (trim((string)$name) === '')   return 'name_is_required';
        if (trim((string)$email) === '')  return 'email_is_required';
        if (!sms_valid_email(trim((string)$email))) return 'invalid_email_address';
        if ($email_taken)                 return 'email_already_in_use';
        return null;
    }
}

if (!function_exists('sms_password_change_error')) {
    /**
     * Validate a password change against the stored password. Returns a phrase
     * key for the first problem, or null when the change may be applied.
     */
    function sms_password_change_error($stored, $current, $new, $confirm) {
        if (!sms_password_matches($current, $stored)) return 'current_password_is_incorrect';
        if (trim((string)$new) === '')            return 'new_password_is_required';
        if ((string)$new !== (string)$confirm)    return 'new_passwords_do_not_match';
        return null;
    }
}

if (!function_exists('sms_upload_image_error')) {
    /**
     * Validate one $_FILES entry for a photo upload. Returns null when nothing
     * was chosen or the file is a real JPEG/PNG/GIF/WEBP image, else a phrase key.
     */
    function sms_upload_image_error($file) {
        if (empty($file) || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) return null;
        if ($file['error'] !== UPLOAD_ERR_OK)                     return 'photo_upload_failed';
        $info = @getimagesize($file['tmp_name']);
        $allowed = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP);
        if ($info === false || !in_array($info[2], $allowed, true)) return 'photo_must_be_an_image';
        return null;
    }
}

if (!function_exists('sms_cache_busted_url')) {
    /** Append ?v=<mtime> so a replaced file at the same path isn't served from browser cache. */
    function sms_cache_busted_url($url, $mtime) {
        if (!$mtime) return $url;
        return $url . (strpos($url, '?') === false ? '?' : '&') . 'v=' . (int)$mtime;
    }
}

/* -------------------------------------------------------------------------
 * Languages  (multi_language_helper / Admin::manage_language / Multilanguage)
 * ---------------------------------------------------------------------- */
if (!function_exists('sms_mojibake_reverse_once')) {
    /**
     * Undo one layer of "UTF-8 bytes re-read as latin1/cp1252" corruption
     * (e.g. "Ã©" -> "é"). Returns null when the text isn't corrupted this way.
     */
    function sms_mojibake_reverse_once($s) {
        $s = (string)$s;
        if (!preg_match('/[^\x00-\x7F]/', $s) || !mb_check_encoding($s, 'UTF-8')) return null;
        static $cp1252 = array(
            0x20AC => 0x80, 0x201A => 0x82, 0x0192 => 0x83, 0x201E => 0x84, 0x2026 => 0x85,
            0x2020 => 0x86, 0x2021 => 0x87, 0x02C6 => 0x88, 0x2030 => 0x89, 0x0160 => 0x8A,
            0x2039 => 0x8B, 0x0152 => 0x8C, 0x017D => 0x8E, 0x2018 => 0x91, 0x2019 => 0x92,
            0x201C => 0x93, 0x201D => 0x94, 0x2022 => 0x95, 0x2013 => 0x96, 0x2014 => 0x97,
            0x02DC => 0x98, 0x2122 => 0x99, 0x0161 => 0x9A, 0x203A => 0x9B, 0x0153 => 0x9C,
            0x017E => 0x9E, 0x0178 => 0x9F,
        );
        $out = '';
        foreach (preg_split('//u', $s, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
            $cp = mb_ord($ch, 'UTF-8');
            if ($cp <= 0xFF)              $out .= chr($cp);
            elseif (isset($cp1252[$cp]))  $out .= chr($cp1252[$cp]);
            else return null;             // a real non-latin character: not mojibake
        }
        return ($out !== $s && mb_check_encoding($out, 'UTF-8')) ? $out : null;
    }
}

if (!function_exists('sms_fix_mojibake')) {
    /** Repair text that was UTF-8 double/triple encoded; correct text is returned unchanged. */
    function sms_fix_mojibake($s) {
        for ($i = 0; $i < 3; $i++) {
            $r = sms_mojibake_reverse_once($s);
            if ($r === null) break;
            $s = $r;
        }
        return (string)$s;
    }
}

if (!function_exists('sms_language_columns')) {
    /** Language names from the `language` table's column list (drops the key columns). */
    function sms_language_columns($fields) {
        return array_values(array_diff((array)$fields, array('phrase_id', 'phrase')));
    }
}

if (!function_exists('sms_is_language')) {
    /** True when $lang is one of the existing language columns. */
    function sms_is_language($lang, $fields) {
        return is_string($lang) && $lang !== '' && in_array($lang, sms_language_columns($fields), true);
    }
}

if (!function_exists('sms_language_add_error')) {
    /** Validate a new language name (becomes a column). Phrase key on error, else null. */
    function sms_language_add_error($name, $fields) {
        $name = strtolower(trim((string)$name));
        if ($name === '')                                        return 'language_name_is_required';
        if (!preg_match('/^[a-z]{2,30}$/', $name))               return 'language_name_letters_only';
        if (in_array($name, (array)$fields, true))               return 'language_already_exists';
        return null;
    }
}

if (!function_exists('sms_language_delete_error')) {
    /** Validate deleting a language column. Phrase key on error, else null. */
    function sms_language_delete_error($lang, $current, $fields) {
        if (!sms_is_language($lang, $fields))  return 'language_not_found';
        if ($lang === 'english')               return 'english_cannot_be_deleted';
        if ($lang === $current)                return 'current_language_cannot_be_deleted';
        return null;
    }
}

if (!function_exists('sms_humanize_phrase')) {
    /** Fallback label for an untranslated phrase key ("manage_student" -> "Manage Student"). */
    function sms_humanize_phrase($phrase) {
        return ucwords(str_replace('_', ' ', (string)$phrase));
    }
}

if (!function_exists('sms_pick_phrase_keeper')) {
    /**
     * Collapse duplicate rows of one phrase. Keeps the row with the most
     * translations (lowest id on ties) and fills its blanks from the others.
     * @return array ['keep_id' => int, 'fill' => [lang => text], 'delete_ids' => int[]]
     */
    function sms_pick_phrase_keeper($rows, $langs) {
        $score = function ($r) use ($langs) {
            $n = 0;
            foreach ($langs as $l) if (isset($r[$l]) && trim((string)$r[$l]) !== '') $n++;
            return $n;
        };
        usort($rows, function ($a, $b) use ($score) {
            $d = $score($b) - $score($a);
            return $d !== 0 ? $d : ((int)$a['phrase_id'] - (int)$b['phrase_id']);
        });
        $keep = array_shift($rows);
        $fill = array();
        foreach ($langs as $l) {
            if (isset($keep[$l]) && trim((string)$keep[$l]) !== '') continue;
            foreach ($rows as $r) {
                if (isset($r[$l]) && trim((string)$r[$l]) !== '') { $fill[$l] = $r[$l]; break; }
            }
        }
        return array(
            'keep_id'    => (int)$keep['phrase_id'],
            'fill'       => $fill,
            'delete_ids' => array_map(function ($r) { return (int)$r['phrase_id']; }, $rows),
        );
    }
}

if (!function_exists('sms_page_bounds')) {
    /** Clamp a 1-based page number. @return array [page, offset, total_pages] */
    function sms_page_bounds($page, $per_page, $total) {
        $per_page = max(1, (int)$per_page);
        $pages    = max(1, (int)ceil(max(0, (int)$total) / $per_page));
        $page     = min(max(1, (int)$page), $pages);
        return array($page, ($page - 1) * $per_page, $pages);
    }
}

if (!function_exists('sms_language_label')) {
    /** Display name for a language column: native script plus English, e.g. "हिन्दी (Hindi)". */
    function sms_language_label($lang) {
        static $native = array(
            'english' => 'English', 'hindi' => 'हिन्दी', 'marathi' => 'मराठी', 'kannada' => 'ಕನ್ನಡ',
            'bengali' => 'বাংলা', 'gujarati' => 'ગુજરાતી', 'tamil' => 'தமிழ்',
        );
        $english = ucwords((string)$lang);
        if (!isset($native[$lang]) || $native[$lang] === $english) return $english;
        return $native[$lang] . ' (' . $english . ')';
    }
}

/* -------------------------------------------------------------------------
 * UI theme (Settings > Theme & Colours)
 * ---------------------------------------------------------------------- */
if (!function_exists('sms_theme_presets')) {
    /** Built-in colour presets: primary, accent, soft background, sidebar start/end. 'classic' = original look. */
    function sms_theme_presets() {
        return array(
            'sunshine'  => array('label' => 'Sunshine',  'primary' => '#ff8a3d', 'accent' => '#ffc93c', 'sidebar_from' => '#ff7a59', 'sidebar_to' => '#ffb347'),
            'ocean'     => array('label' => 'Ocean',     'primary' => '#2d8cf0', 'accent' => '#22c1c3', 'sidebar_from' => '#3a7bd5', 'sidebar_to' => '#00b4db'),
            'bubblegum' => array('label' => 'Bubblegum', 'primary' => '#e85d9c', 'accent' => '#9b6dff', 'sidebar_from' => '#f472b6', 'sidebar_to' => '#a78bfa'),
            'jungle'    => array('label' => 'Jungle',    'primary' => '#2fb36d', 'accent' => '#a3d93c', 'sidebar_from' => '#20a464', 'sidebar_to' => '#7cc94a'),
            'classic'   => array('label' => 'Classic',   'primary' => '#303641', 'accent' => '#21a9e1', 'sidebar_from' => '#303641', 'sidebar_to' => '#303641'),
        );
    }
}

if (!function_exists('sms_valid_hex_color')) {
    /** True for #rgb or #rrggbb. */
    function sms_valid_hex_color($c) {
        return (bool)preg_match('/^#([0-9a-f]{3}|[0-9a-f]{6})$/i', (string)$c);
    }
}

if (!function_exists('sms_mix_color')) {
    /** Mix a hex colour with white (amount > 0) or black (amount < 0); amount in -1..1. Returns #rrggbb. */
    function sms_mix_color($hex, $amount) {
        $hex = ltrim((string)$hex, '#');
        if (strlen($hex) === 3) $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        $amount = max(-1, min(1, (float)$amount));
        $target = $amount >= 0 ? 255 : 0;
        $out = '#';
        foreach (str_split($hex, 2) as $part) {
            $v = hexdec($part);
            $out .= str_pad(dechex((int)round($v + ($target - $v) * abs($amount))), 2, '0', STR_PAD_LEFT);
        }
        return $out;
    }
}

if (!function_exists('sms_theme_vars')) {
    /**
     * Resolve the theme from settings into CSS custom properties.
     * Custom colours override the preset when they are valid hex colours.
     * @return array|null CSS var => value, or null for the classic (original) look
     */
    function sms_theme_vars($preset, $primary = '', $accent = '', $font = 'nunito') {
        $presets = sms_theme_presets();
        if (!isset($presets[$preset])) $preset = 'sunshine';
        if ($preset === 'classic') return null;
        $p = $presets[$preset];
        $primary = sms_valid_hex_color($primary) ? strtolower($primary) : $p['primary'];
        $accent  = sms_valid_hex_color($accent) ? strtolower($accent) : $p['accent'];
        $custom  = sms_valid_hex_color($primary) && $primary !== $p['primary'];
        $fonts = array(
            'nunito' => "'Nunito', 'Noto Sans', 'Segoe UI', sans-serif",
            'baloo'  => "'Baloo 2', 'Nunito', 'Noto Sans', 'Segoe UI', sans-serif",
            'system' => "'Segoe UI', 'Noto Sans', Arial, sans-serif",
        );
        return array(
            '--c-primary'      => $primary,
            '--c-primary-dark' => sms_mix_color($primary, -0.18),
            '--c-primary-soft' => sms_mix_color($primary, 0.86),
            '--c-accent'       => $accent,
            '--c-accent-soft'  => sms_mix_color($accent, 0.8),
            '--c-bg'           => sms_mix_color($primary, 0.94),
            '--c-sidebar-from' => $custom ? sms_mix_color($primary, -0.05) : $p['sidebar_from'],
            '--c-sidebar-to'   => $custom ? sms_mix_color($accent, -0.05) : $p['sidebar_to'],
            '--f-main'         => $fonts[$font] ?? $fonts['nunito'],
        );
    }
}

if (!function_exists('sms_password_matches')) {
    /**
     * Check a typed password against the stored one. Accounts created by the student
     * screens store a bcrypt hash; older accounts store plain text. Both are accepted.
     */
    function sms_password_matches($input, $stored) {
        $input = (string)$input; $stored = (string)$stored;
        if ($input === '' || $stored === '') return false;
        if (preg_match('/^\$2[aby]\$/', $stored)) return password_verify($input, $stored);
        return hash_equals($stored, $input);
    }
}
