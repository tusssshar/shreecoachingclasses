<?php
if (!defined('BASEPATH')) {
    // Allow direct include by the CLI test runner without full CodeIgniter bootstrap.
    if (!defined('SMS_ADMISSIONS_TEST')) {
        exit('No direct script access allowed');
    }
}

/**
 * Teacher / parent / student portals: menu registry, menu permissions and
 * small pure rules (attendance window, attendance summary). Unit tested.
 */

if (!function_exists('sms_portal_menus')) {
    /**
     * Menus per portal role: key => [phrase, entypo icon, page, default on?, locked?].
     * Locked menus (dashboard, profile) are always visible.
     */
    function sms_portal_menus() {
        return array(
            'teacher' => array(
                'dashboard'      => array('dashboard',          'entypo-gauge',      'dashboard',      true,  true),
                'timetable'      => array('my_timetable',       'entypo-clock',      'timetable',      true,  false),
                'students'       => array('my_students',        'entypo-users',      'students',       true,  false),
                'attendance'     => array('mark_attendance',    'entypo-check',      'attendance',     true,  false),
                'marks'          => array('enter_marks',        'entypo-pencil',     'marks',          true,  false),
                'paper_checking' => array('paper_checking',     'entypo-doc-text',   'paper_checking', true,  false),
                'results'        => array('results',            'entypo-chart-bar',  'results',        true,  false),
                'notices'        => array('noticeboard',        'entypo-megaphone',  'notices',        true,  false),
                'profile'        => array('my_profile',         'entypo-lock',       'manage_profile', true,  true),
            ),
            'parent' => array(
                'dashboard'      => array('dashboard',          'entypo-gauge',      'dashboard',      true,  true),
                'results'        => array('results',            'entypo-chart-bar',  'results',        true,  false),
                'online_exams'   => array('online_exams',       'entypo-monitor',    'exams',          true,  false),
                'attendance'     => array('attendance',         'entypo-calendar',   'attendance',     true,  false),
                'fees'           => array('fees',               'entypo-credit-card','fees',           true,  false),
                'notices'        => array('noticeboard',        'entypo-megaphone',  'notices',        true,  false),
                'profile'        => array('my_profile',         'entypo-lock',       'manage_profile', true,  true),
            ),
            'student' => array(
                'dashboard'      => array('dashboard',          'entypo-gauge',      'dashboard',      true,  true),
                'online_exams'   => array('my_online_exams',    'entypo-monitor',    'exams',          true,  false),
                'marks'          => array('my_marks',           'entypo-chart-bar',  'marks',          true,  false),
                'attendance'     => array('my_attendance',      'entypo-calendar',   'attendance',     true,  false),
                'fees'           => array('fees',               'entypo-credit-card','fees',           false, false),
                'notices'        => array('noticeboard',        'entypo-megaphone',  'notices',        true,  false),
                'profile'        => array('my_profile',         'entypo-lock',       'manage_profile', true,  true),
            ),
        );
    }
}

if (!function_exists('sms_menu_allowed')) {
    /**
     * Is a menu visible/usable for a role? $perms is the saved matrix
     * [role => [menu => 0|1]]; unsaved menus fall back to their default.
     */
    function sms_menu_allowed($perms, $role, $menu) {
        $menus = sms_portal_menus();
        if (!isset($menus[$role][$menu])) return false;
        $def = $menus[$role][$menu];
        if ($def[4]) return true;                                   // locked: always on
        if (isset($perms[$role]) && is_array($perms[$role]) && array_key_exists($menu, $perms[$role]))
            return (bool)$perms[$role][$menu];
        return $def[3];
    }
}

if (!function_exists('sms_parse_menu_permissions')) {
    /** Decode the saved JSON matrix; anything malformed becomes "use defaults". */
    function sms_parse_menu_permissions($json) {
        $p = json_decode((string)$json, true);
        return is_array($p) ? $p : array();
    }
}

if (!function_exists('sms_menu_permissions_from_post')) {
    /** Build the matrix from ticked checkboxes [role => [menu => '1']], covering every known menu. */
    function sms_menu_permissions_from_post($post) {
        $out = array();
        foreach (sms_portal_menus() as $role => $menus) {
            foreach ($menus as $key => $m) {
                $out[$role][$key] = $m[4] ? 1 : (!empty($post[$role][$key]) ? 1 : 0);
            }
        }
        return $out;
    }
}

if (!function_exists('sms_attendance_date_error')) {
    /**
     * Teachers may mark attendance for today and up to $days_back days before; never the future.
     * Returns a phrase key on error, else null.
     */
    function sms_attendance_date_error($date, $today, $days_back = 7) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$date) || strtotime($date) === false) return 'invalid_date';
        if ($date > $today) return 'attendance_cannot_be_marked_for_a_future_date';
        if ($date < date('Y-m-d', strtotime($today . ' -' . (int)$days_back . ' days'))) return 'attendance_older_than_7_days_is_locked';
        return null;
    }
}

if (!function_exists('sms_attendance_summary')) {
    /** Present / absent counts and percentage from attendance rows (status 1 present, 2 absent). */
    function sms_attendance_summary($rows) {
        $present = 0; $absent = 0;
        foreach ((array)$rows as $r) {
            if ((int)$r['status'] === 1) $present++;
            elseif ((int)$r['status'] === 2) $absent++;
        }
        $marked = $present + $absent;
        return array('present' => $present, 'absent' => $absent, 'marked' => $marked,
                     'percent' => $marked ? round($present * 100 / $marked, 1) : null);
    }
}
