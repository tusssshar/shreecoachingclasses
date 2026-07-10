<?php
if (!defined('BASEPATH')) {
    // Allow this pure-logic helper to be included directly by the test runner
    // (PHP CLI) without the full CodeIgniter bootstrap.
    if (!defined('SMS_ADMISSIONS_TEST')) {
        exit('No direct script access allowed');
    }
}

/**
 * Pure, side-effect-free logic for the Enquiry and Course modules.
 *
 * Everything here is deliberately free of $this->db / session so it can be
 * unit tested in isolation (see tests/run_tests.php) while the controller and
 * views call the very same functions.
 */

if (!function_exists('sms_next_enquiry_no')) {
    /**
     * Next suggested enquiry number, e.g. ENQ-0001.
     * @param int|null $last_id highest existing enquiry_id (0/null when table empty)
     */
    function sms_next_enquiry_no($last_id) {
        $next = ((int)$last_id) + 1;
        return 'ENQ-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('sms_enquiry_status_label')) {
    /** Coloured Bootstrap label markup for an enquiry status. */
    function sms_enquiry_status_label($status) {
        $map = array(
            'in_progress' => '<span class="label label-warning">In Progress</span>',
            'joined'      => '<span class="label label-success">Joined</span>',
            'not_joined'  => '<span class="label label-danger">Not Joined</span>',
        );
        return isset($map[$status])
            ? $map[$status]
            : '<span class="label label-default">' . htmlspecialchars((string)$status) . '</span>';
    }
}

if (!function_exists('sms_is_existing_student_source')) {
    /** Whether a source value should reveal the "existing student name" field. */
    function sms_is_existing_student_source($source) {
        return strtolower(trim((string)$source)) === 'existing student';
    }
}

if (!function_exists('sms_normalize_header')) {
    /** Normalise a spreadsheet header cell to a lookup key: "Contact No" -> "contact_no". */
    function sms_normalize_header($col) {
        return trim(strtolower(preg_replace('/[^a-z0-9]+/i', '_', (string)$col)), '_');
    }
}

if (!function_exists('sms_build_header_map')) {
    /** Build normalised-header => column-index map from a header row. */
    function sms_build_header_map($header_row) {
        $map = array();
        foreach ((array)$header_row as $i => $col) {
            $key = sms_normalize_header($col);
            if ($key !== '' && !isset($map[$key])) {
                $map[$key] = $i;
            }
        }
        return $map;
    }
}

if (!function_exists('sms_cell_value')) {
    /**
     * Read a value from a data row by one or more candidate header keys.
     * @param array        $row        indexed cell values
     * @param array        $header_map from sms_build_header_map()
     * @param string|array $keys       candidate normalised keys (first match wins)
     */
    function sms_cell_value($row, $header_map, $keys) {
        foreach ((array)$keys as $k) {
            // Accept raw ("Payment Date") or already-normalised ("payment_date") keys.
            $nk = sms_normalize_header($k);
            if (isset($header_map[$nk]) && isset($row[$header_map[$nk]])) {
                return trim((string)$row[$header_map[$nk]]);
            }
        }
        return '';
    }
}

if (!function_exists('sms_split_installments')) {
    /**
     * Split a total course fee into $count equal installments; the final
     * installment absorbs any rounding remainder so the parts sum to the total.
     * @return float[] list of installment amounts (length == max(1,$count))
     */
    function sms_split_installments($total_fees, $count) {
        $count = max(1, (int)$count);
        $total = round((float)$total_fees, 2);
        $per   = round($total / $count, 2);
        $out   = array();
        for ($i = 1; $i <= $count; $i++) {
            $out[] = ($i === $count) ? round($total - $per * ($count - 1), 2) : $per;
        }
        return $out;
    }
}

if (!function_exists('sms_installments_match')) {
    /** True when the installment amounts sum (to 2dp) to the course fee. */
    function sms_installments_match($total_fees, $installments_sum) {
        return round((float)$installments_sum, 2) === round((float)$total_fees, 2);
    }
}
