<?php
if (!defined('BASEPATH')) {
    // Allow direct include by the CLI test runner without full CodeIgniter bootstrap.
    if (!defined('SMS_ADMISSIONS_TEST')) {
        exit('No direct script access allowed');
    }
}

/**
 * Pure exam / CBT / notification logic (no $this->db, session or superglobals),
 * shared by Admin, Student, Cron and Email_model and unit tested in
 * tests/run_tests.php.
 */

/* -------------------------------------------------------------------------
 * CBT timing
 * ---------------------------------------------------------------------- */
if (!function_exists('sms_cbt_ts')) {
    /** Unix timestamp for a Y-m-d date + H:i[:s] time, or null when either is invalid. */
    function sms_cbt_ts($date, $time) {
        $date = trim((string)$date);
        $time = trim((string)$time);
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $time)) return null;
        $ts = strtotime($date . ' ' . $time);
        return $ts === false ? null : $ts;
    }
}

if (!function_exists('sms_cbt_window')) {
    /**
     * Availability window of a CBT exam: [opens, closes] timestamps.
     * Closes at end_time when set, otherwise start + duration (a fixed sitting).
     */
    function sms_cbt_window($exam) {
        $opens = sms_cbt_ts($exam['exam_date'] ?? '', $exam['start_time'] ?? '');
        if ($opens === null) return array(null, null);
        $closes = !empty($exam['end_time']) ? sms_cbt_ts($exam['exam_date'], $exam['end_time']) : null;
        if ($closes === null || $closes <= $opens) $closes = $opens + max(1, (int)($exam['duration'] ?? 0)) * 60;
        return array($opens, $closes);
    }
}

if (!function_exists('sms_cbt_state')) {
    /** draft | upcoming | open | closed, as seen at $now. */
    function sms_cbt_state($exam, $now) {
        if (($exam['status'] ?? 'draft') !== 'published') return 'draft';
        list($opens, $closes) = sms_cbt_window($exam);
        if ($opens === null) return 'draft';
        if ($now < $opens)   return 'upcoming';
        if ($now >= $closes) return 'closed';
        return 'open';
    }
}

if (!function_exists('sms_cbt_deadline')) {
    /** When a student's attempt must end: started + duration, but never after the window closes. */
    function sms_cbt_deadline($exam, $started_at) {
        list(, $closes) = sms_cbt_window($exam);
        $own = (int)$started_at + max(1, (int)($exam['duration'] ?? 0)) * 60;
        return $closes === null ? $own : min($own, $closes);
    }
}

/* -------------------------------------------------------------------------
 * CBT marking
 * ---------------------------------------------------------------------- */
if (!function_exists('sms_cbt_is_correct')) {
    /** True when the chosen option label matches the correct one (case/space tolerant). */
    function sms_cbt_is_correct($given, $correct) {
        $given = strtoupper(trim((string)$given));
        return $given !== '' && $given === strtoupper(trim((string)$correct));
    }
}

if (!function_exists('sms_cbt_score')) {
    /**
     * Auto-mark an attempt.
     * @param array $questions rows with question_id, correct_answers, marks
     * @param array $answers   [question_id => chosen label]
     * @return array score, total, correct, wrong, unanswered, awarded[question_id]
     */
    function sms_cbt_score($questions, $answers) {
        $out = array('score' => 0.0, 'total' => 0.0, 'correct' => 0, 'wrong' => 0, 'unanswered' => 0, 'awarded' => array());
        foreach ((array)$questions as $q) {
            $qid   = (int)$q['question_id'];
            $marks = max(0, (float)($q['marks'] ?? 1));
            $given = isset($answers[$qid]) ? trim((string)$answers[$qid]) : '';
            $out['total'] += $marks;
            if ($given === '') {
                $out['unanswered']++;
                $out['awarded'][$qid] = 0.0;
            } elseif (sms_cbt_is_correct($given, $q['correct_answers'] ?? '')) {
                $out['correct']++;
                $out['score'] += $marks;
                $out['awarded'][$qid] = $marks;
            } else {
                $out['wrong']++;
                $out['awarded'][$qid] = 0.0;
            }
        }
        return $out;
    }
}

if (!function_exists('sms_cbt_publish_problems')) {
    /**
     * Reasons a CBT exam can't be published yet (empty list = ready).
     * @param array $questions rows with question, correct_answers, marks, options[label=>content]
     */
    function sms_cbt_publish_problems($exam, $questions) {
        $p = array();
        if (trim((string)($exam['title'] ?? '')) === '') $p[] = 'Exam title is missing';
        if (sms_cbt_ts($exam['exam_date'] ?? '', $exam['start_time'] ?? '') === null) $p[] = 'Exam date / start time is missing';
        if ((int)($exam['duration'] ?? 0) < 1) $p[] = 'Duration must be at least 1 minute';
        if (empty($questions)) $p[] = 'The exam has no questions';
        foreach ((array)$questions as $i => $q) {
            $n = $i + 1;
            $text = trim((string)($q['question'] ?? ''));
            if ($text === '' || preg_match('/^Question \d+$/', $text)) $p[] = "Question $n: question text not entered";
            $filled = array_filter((array)($q['options'] ?? array()), function ($c) { return trim((string)$c) !== ''; });
            if (count($filled) < 2) $p[] = "Question $n: needs at least 2 options";
            $correct = strtoupper(trim((string)($q['correct_answers'] ?? '')));
            if ($correct === '' || !isset($filled[$correct])) $p[] = "Question $n: correct answer points to an empty option";
            if ((float)($q['marks'] ?? 0) <= 0) $p[] = "Question $n: marks must be more than 0";
        }
        return $p;
    }
}

/* -------------------------------------------------------------------------
 * Results
 * ---------------------------------------------------------------------- */
if (!function_exists('sms_percent')) {
    /** Percentage rounded to 2 dp; 0 when there is nothing to score. */
    function sms_percent($score, $total) {
        return (float)$total > 0 ? round((float)$score * 100 / (float)$total, 2) : 0.0;
    }
}

if (!function_exists('sms_is_pass')) {
    function sms_is_pass($percent, $pass_percent) {
        return (float)$percent >= (float)$pass_percent;
    }
}

if (!function_exists('sms_rank')) {
    /**
     * Competition ranking (1, 2, 2, 4) by $key descending. Returns [row index => rank].
     */
    function sms_rank($rows, $key = 'score') {
        $scores = array();
        foreach ((array)$rows as $i => $r) $scores[$i] = (float)($r[$key] ?? 0);
        arsort($scores);
        $ranks = array(); $pos = 0; $prev = null; $rank = 0;
        foreach ($scores as $i => $s) {
            $pos++;
            if ($prev === null || $s < $prev) $rank = $pos;
            $ranks[$i] = $rank;
            $prev = $s;
        }
        return $ranks;
    }
}

/* -------------------------------------------------------------------------
 * Grades & marks (classic exams)
 * ---------------------------------------------------------------------- */
if (!function_exists('sms_grade_error')) {
    /**
     * Validate a grade band against existing bands. Phrase key on error, else null.
     * @param array $existing rows with grade_id, mark_from, mark_upto
     */
    function sms_grade_error($name, $from, $upto, $existing, $except_id = 0) {
        if (trim((string)$name) === '') return 'grade_name_is_required';
        if (!is_numeric($from) || !is_numeric($upto)) return 'marks_must_be_numbers';
        $from = (float)$from; $upto = (float)$upto;
        if ($from < 0 || $upto > 100) return 'grade_range_must_be_0_to_100';
        if ($from > $upto) return 'mark_from_must_not_exceed_mark_upto';
        foreach ((array)$existing as $g) {
            if ((int)$g['grade_id'] === (int)$except_id) continue;
            if ($from <= (float)$g['mark_upto'] && $upto >= (float)$g['mark_from']) return 'grade_range_overlaps_another_grade';
        }
        return null;
    }
}

if (!function_exists('sms_default_grades')) {
    /** CBSE-style 9-point grading bands (percentage). */
    function sms_default_grades() {
        return array(
            array('name' => 'A1', 'grade_point' => '10', 'mark_from' => 91, 'mark_upto' => 100, 'comment' => 'Outstanding'),
            array('name' => 'A2', 'grade_point' => '9',  'mark_from' => 81, 'mark_upto' => 90,  'comment' => 'Excellent'),
            array('name' => 'B1', 'grade_point' => '8',  'mark_from' => 71, 'mark_upto' => 80,  'comment' => 'Very Good'),
            array('name' => 'B2', 'grade_point' => '7',  'mark_from' => 61, 'mark_upto' => 70,  'comment' => 'Good'),
            array('name' => 'C1', 'grade_point' => '6',  'mark_from' => 51, 'mark_upto' => 60,  'comment' => 'Above Average'),
            array('name' => 'C2', 'grade_point' => '5',  'mark_from' => 41, 'mark_upto' => 50,  'comment' => 'Average'),
            array('name' => 'D',  'grade_point' => '4',  'mark_from' => 33, 'mark_upto' => 40,  'comment' => 'Pass'),
            array('name' => 'E',  'grade_point' => '0',  'mark_from' => 0,  'mark_upto' => 32,  'comment' => 'Needs Improvement'),
        );
    }
}

if (!function_exists('sms_grade_for_percent')) {
    /** Grade row for a percentage, tolerant of the 1-mark gaps between integer bands (e.g. 90.5 -> A2). */
    function sms_grade_for_percent($grades, $percent) {
        $p = (float)$percent;
        $best = null;
        foreach ((array)$grades as $g) {
            if ($p >= (float)$g['mark_from'] && $p <= (float)$g['mark_upto']) return $g;
            // fractional percentage falling between two integer bands -> lower band
            if ($p > (float)$g['mark_upto'] && $p < (float)$g['mark_upto'] + 1) $best = $g;
        }
        return $best;
    }
}

if (!function_exists('sms_mark_error')) {
    /** Validate one entered mark. Blank = not entered (allowed). Phrase key on error, else null. */
    function sms_mark_error($obtained, $total) {
        if ($obtained === '' || $obtained === null) return null;
        if (!is_numeric($obtained)) return 'marks_must_be_numbers';
        if ((float)$total <= 0) return 'total_marks_must_be_more_than_0';
        if ((float)$obtained < 0 || (float)$obtained > (float)$total) return 'marks_must_be_between_0_and_total';
        return null;
    }
}

if (!function_exists('sms_parse_exam_date')) {
    /** Normalise the legacy exam date text (m/d/Y, d-m-Y or Y-m-d) to Y-m-d, or '' if unparseable. */
    function sms_parse_exam_date($text) {
        $t = trim((string)$text);
        if ($t === '') return '';
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $t, $m) && checkdate((int)$m[2], (int)$m[3], (int)$m[1])) return $t;
        if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $t, $m) && checkdate((int)$m[1], (int)$m[2], (int)$m[3]))
            return sprintf('%04d-%02d-%02d', $m[3], $m[1], $m[2]);           // datepicker m/d/Y
        if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $t, $m) && checkdate((int)$m[2], (int)$m[1], (int)$m[3]))
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);           // d-m-Y
        return '';
    }
}

/* -------------------------------------------------------------------------
 * Email recipients & templates
 * ---------------------------------------------------------------------- */
if (!function_exists('sms_email_recipients')) {
    /** Unique valid addresses: the student's, plus the parent's when copying parents. */
    function sms_email_recipients($student_email, $parent_email, $copy_parent) {
        $list = array();
        foreach (array($student_email, $copy_parent ? $parent_email : '') as $e) {
            $e = strtolower(trim((string)$e));
            if ($e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL) && !in_array($e, $list, true)) $list[] = $e;
        }
        return $list;
    }
}

if (!function_exists('sms_email_wrap')) {
    /** Branded HTML email shell. $inner is trusted HTML built by the template functions below. */
    function sms_email_wrap($school, $heading, $inner) {
        $h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
        return '<div style="font-family:Arial,Helvetica,sans-serif;font-size:14px;color:#222;max-width:620px;">'
             . '<div style="background:#1f3a68;color:#fff;padding:14px 18px;font-size:18px;font-weight:bold;">' . $h($school['name'] ?? '') . '</div>'
             . '<div style="padding:18px;border:1px solid #dde3ec;border-top:0;">'
             . '<h2 style="margin:0 0 12px;font-size:18px;color:#1f3a68;">' . $h($heading) . '</h2>'
             . $inner
             . '<p style="margin-top:22px;color:#555;">Regards,<br><strong>' . $h($school['name'] ?? '') . '</strong>'
             . (!empty($school['phone']) ? '<br>' . $h($school['phone']) : '')
             . (!empty($school['address']) ? '<br>' . $h($school['address']) : '') . '</p>'
             . '</div><p style="color:#999;font-size:11px;margin-top:8px;">This is an automated message. Please do not reply.</p></div>';
    }
}

if (!function_exists('sms_email_table')) {
    /** Two-column detail table for emails. */
    function sms_email_table($rows) {
        $html = '<table style="border-collapse:collapse;margin:8px 0;">';
        foreach ($rows as $label => $value) {
            $html .= '<tr><td style="padding:6px 12px 6px 0;color:#555;">' . htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8') . '</td>'
                   . '<td style="padding:6px 0;font-weight:bold;">' . htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8') . '</td></tr>';
        }
        return $html . '</table>';
    }
}

if (!function_exists('sms_email_exam_scheduled')) {
    /**
     * "Exam scheduled" email. $v: student, title, subject, class, date (Y-m-d), start, end, duration,
     * total_marks, mode ('online'|'written'), instructions, portal_url
     * @return array [subject, html]
     */
    function sms_email_exam_scheduled($school, $v, $reminder = false) {
        $h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
        $when = $v['date'] ? date('l, d M Y', strtotime($v['date'])) : '-';
        $rows = array('Exam' => $v['title']);
        if (!empty($v['subject'])) $rows['Subject'] = $v['subject'];
        if (!empty($v['class']))   $rows['Class'] = $v['class'];
        $rows['Date'] = $when;
        if (!empty($v['start']))   $rows['Time'] = $v['start'] . (!empty($v['end']) ? ' - ' . $v['end'] : '');
        if (!empty($v['duration'])) $rows['Duration'] = $v['duration'] . ' minutes';
        if (!empty($v['total_marks'])) $rows['Total marks'] = $v['total_marks'];
        $rows['Mode'] = ($v['mode'] ?? 'online') === 'online' ? 'Online (CBT)' : 'Written';

        $intro = $reminder
            ? '<p>This is a reminder that <strong>' . $h($v['student']) . '</strong> has an exam <strong>tomorrow</strong>.</p>'
            : '<p>An exam has been scheduled for <strong>' . $h($v['student']) . '</strong>.</p>';
        $inner = $intro . sms_email_table($rows);
        if (!empty($v['instructions'])) $inner .= '<p><strong>Instructions:</strong><br>' . nl2br($h($v['instructions'])) . '</p>';
        if (($v['mode'] ?? 'online') === 'online' && !empty($v['portal_url']))
            $inner .= '<p>Log in to the student portal at the exam time to take the test:<br><a href="' . $h($v['portal_url']) . '">' . $h($v['portal_url']) . '</a></p>';
        $inner .= '<p>All the best!</p>';

        $subject = ($reminder ? 'Reminder: ' : 'Exam scheduled: ') . $v['title'] . ' on ' . ($v['date'] ? date('d M Y', strtotime($v['date'])) : '');
        return array($subject, sms_email_wrap($school, $reminder ? 'Exam tomorrow' : 'Exam scheduled', $inner));
    }
}

if (!function_exists('sms_email_result')) {
    /**
     * "Result published" email. $v: student, title, class, date, rows (array of [subject, obtained, total]),
     * score, total, percent, grade, pass (bool|null), rank, portal_url
     * @return array [subject, html]
     */
    function sms_email_result($school, $v) {
        $h = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
        $inner = '<p>The result of <strong>' . $h($v['title']) . '</strong> for <strong>' . $h($v['student']) . '</strong> has been published.</p>';
        if (!empty($v['rows'])) {
            $inner .= '<table style="border-collapse:collapse;margin:8px 0;min-width:320px;">'
                    . '<tr style="background:#f1f4f9;"><th style="padding:6px 10px;border:1px solid #dde3ec;text-align:left;">Subject</th>'
                    . '<th style="padding:6px 10px;border:1px solid #dde3ec;">Marks</th></tr>';
            foreach ($v['rows'] as $r) {
                $inner .= '<tr><td style="padding:6px 10px;border:1px solid #dde3ec;">' . $h($r[0]) . '</td>'
                        . '<td style="padding:6px 10px;border:1px solid #dde3ec;text-align:center;">' . $h($r[1]) . ' / ' . $h($r[2]) . '</td></tr>';
            }
            $inner .= '</table>';
        }
        $rows = array('Total' => rtrim(rtrim(number_format((float)$v['score'], 2, '.', ''), '0'), '.') . ' / ' . rtrim(rtrim(number_format((float)$v['total'], 2, '.', ''), '0'), '.'),
                      'Percentage' => number_format((float)$v['percent'], 2) . '%');
        if (!empty($v['grade'])) $rows['Grade'] = $v['grade'];
        if (isset($v['pass']) && $v['pass'] !== null) $rows['Result'] = $v['pass'] ? 'Pass' : 'Fail';
        if (!empty($v['rank'])) $rows['Rank'] = $v['rank'];
        $inner .= sms_email_table($rows);
        if (!empty($v['portal_url'])) $inner .= '<p>View the detailed result in the student portal:<br><a href="' . $h($v['portal_url']) . '">' . $h($v['portal_url']) . '</a></p>';
        return array('Result published: ' . $v['title'], sms_email_wrap($school, 'Result published', $inner));
    }
}

if (!function_exists('sms_reminder_due')) {
    /** True when an exam on $exam_date (Y-m-d) is tomorrow relative to $today (Y-m-d). */
    function sms_reminder_due($exam_date, $today) {
        $d = sms_parse_exam_date($exam_date);
        return $d !== '' && $d === date('Y-m-d', strtotime($today . ' +1 day'));
    }
}

if (!function_exists('sms_cbt_state_badge')) {
    /** Bootstrap label for an exam state (draft / upcoming / open / closed). */
    function sms_cbt_state_badge($state) {
        $map = array('draft' => array('default', 'Draft'), 'upcoming' => array('info', 'Upcoming'),
                     'open' => array('success', 'Open now'), 'closed' => array('warning', 'Closed'));
        $m = $map[$state] ?? array('default', ucfirst((string)$state));
        return '<span class="label label-' . $m[0] . '">' . htmlspecialchars($m[1]) . '</span>';
    }
}

if (!function_exists('sms_attempt_badge')) {
    /** Bootstrap label for a student's attempt status. */
    function sms_attempt_badge($status) {
        $map = array('assigned' => array('default', 'Not started'), 'in_progress' => array('info', 'In progress'),
                     'submitted' => array('primary', 'Submitted'), 'checked' => array('success', 'Checked'),
                     'completed' => array('success', 'Checked'));
        $m = $map[$status] ?? array('default', ucfirst((string)$status));
        return '<span class="label label-' . $m[0] . '">' . htmlspecialchars($m[1]) . '</span>';
    }
}

if (!function_exists('sms_num')) {
    /** Number without trailing zeros: 5.00 -> "5", 2.50 -> "2.5". */
    function sms_num($n) {
        return rtrim(rtrim(number_format((float)$n, 2, '.', ''), '0'), '.');
    }
}

if (!function_exists('sms_export_buttons')) {
    /** Excel / PDF / Print buttons for admin/export_list (the existing Export_service). */
    function sms_export_buttons($list, $identifier = '') {
        $base = base_url() . 'index.php?admin/export_list/' . $list . '/';
        $tail = $identifier !== '' ? '/' . $identifier : '';
        return '<a href="' . $base . 'excel' . $tail . '" target="_blank" class="btn btn-info btn-sm">Excel</a> '
             . '<a href="' . $base . 'pdf' . $tail . '" target="_blank" class="btn btn-danger btn-sm">PDF</a> '
             . '<a href="' . $base . 'print' . $tail . '" target="_blank" class="btn btn-default btn-sm">Print</a>';
    }
}

if (!function_exists('sms_preview_button')) {
    /** Eye button opening the email preview (exactly what will be sent) in a new tab. */
    function sms_preview_button($type, $ref_id, $extra = 0, $label = 'Preview email', $size = 'sm') {
        $url = base_url() . 'index.php?admin/email_preview/' . $type . '/' . (int)$ref_id . '/' . (int)$extra;
        return '<a href="' . $url . '" target="_blank" class="btn btn-default btn-' . $size . '" title="' . htmlspecialchars($label) . '">'
             . '<i class="entypo-eye"></i> ' . htmlspecialchars($label) . '</a>';
    }
}
