<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title">
            Generate Salary Slip — <?php echo htmlspecialchars($teacher['name']); ?>
        </div>
    </div>
    <div class="panel-body">

        <?php
            $basic   = (float)$teacher['basic_salary'];
            $hra     = (float)$teacher['hra'];
            $da      = (float)$teacher['da'];
            $conv    = (float)$teacher['conveyance'];
            $medical = (float)$teacher['medical_allowance'];
            $otherA  = (float)$teacher['other_allowance'];
            $pf      = (float)$teacher['pf_deduction'];
            $tax     = (float)$teacher['tax_deduction'];
            $otherD  = (float)$teacher['other_deduction'];
            $ctc     = $basic + $hra + $da + $conv + $medical + $otherA;
            $net     = max(0, $ctc - ($pf + $tax + $otherD));
        ?>

        <?php
            $jd = $teacher['joining_date'] ?? '';
            $jd_display = ($jd && $jd !== '0000-00-00') ? date('d M Y', strtotime($jd)) : '-';
        ?>
        <div class="row" style="margin-bottom:15px;">
            <div class="col-sm-6">
                <strong>Designation:</strong> <?php echo htmlspecialchars($teacher['designation'] ?? '-'); ?><br>
                <strong>Joining Date:</strong> <?php echo $jd_display; ?><br>
                <strong>PAN:</strong> <?php echo htmlspecialchars($teacher['pan_number'] ?? '-'); ?><br>
                <strong>Bank A/C:</strong> <?php echo htmlspecialchars($teacher['bank_account'] ?? '-'); ?>
            </div>
            <div class="col-sm-6">
                <strong>Basic:</strong> <?php echo number_format($basic, 2); ?> &nbsp;
                <strong>HRA (50%):</strong> <?php echo number_format($hra, 2); ?><br>
                <strong>DA (20%):</strong> <?php echo number_format($da, 2); ?> &nbsp;
                <strong>Conveyance (10%):</strong> <?php echo number_format($conv, 2); ?><br>
                <strong>Medical (10%):</strong> <?php echo number_format($medical, 2); ?> &nbsp;
                <strong>Other Allow.:</strong> <?php echo number_format($otherA, 2); ?><br>
                <strong>PF (fixed):</strong> <?php echo number_format($pf, 2); ?> &nbsp;
                <strong>Tax (fixed):</strong> <?php echo number_format($tax, 2); ?><br>
                <hr style="margin:6px 0;">
                <strong style="color:#1f3a68;">CTC / month:</strong> &#8377; <?php echo number_format($ctc, 2); ?><br>
                <strong style="color:#1a8d3a;">Net Take-Home:</strong> &#8377; <?php echo number_format($net, 2); ?>
            </div>
        </div>

        <form method="post" action="<?php echo base_url(); ?>index.php?admin/teacher_salary_slip/<?php echo (int)$teacher['teacher_id']; ?>/generate" target="_blank" class="form-horizontal form-groups-bordered">
            <div class="form-group">
                <label class="col-sm-3 control-label">Month</label>
                <div class="col-sm-4">
                    <input type="month" id="slip_month" name="month" class="form-control" value="<?php echo $default_month; ?>" required>
                    <small class="text-muted">Working days are auto-filled from attendance for the selected month.</small>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Days Worked</label>
                <div class="col-sm-4">
                    <input type="number" id="slip_days_worked" name="days_worked" min="0" max="31" step="0.5" class="form-control" value="<?php echo (int)$default_present; ?>" required>
                    <small class="text-muted">
                        Auto-counted from <em>Daily Attendance &rarr; Teachers</em> for the selected month.
                        <span id="slip_days_hint" class="text-info"></span>
                    </small>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Remarks (optional)</label>
                <div class="col-sm-6">
                    <input type="text" name="remarks" class="form-control" placeholder="e.g. Leaves: 2 paid, 1 unpaid">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Output</label>
                <div class="col-sm-4">
                    <label class="radio-inline"><input type="radio" name="output" value="pdf" checked> PDF (print)</label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <button type="submit" class="btn btn-primary"><i class="entypo-doc-text"></i> Generate Salary Slip</button>
                    <a href="<?php echo base_url(); ?>index.php?admin/teacher" class="btn btn-default">Back</a>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
(function () {
    var $month = document.getElementById('slip_month');
    var $days  = document.getElementById('slip_days_worked');
    var $hint  = document.getElementById('slip_days_hint');
    var teacherId = <?php echo (int)$teacher['teacher_id']; ?>;
    if (!$month || !$days) return;

    function refresh() {
        var m = $month.value;
        if (!m) return;
        $hint.textContent = ' loading...';
        jQuery.getJSON(
            '<?php echo base_url(); ?>index.php?admin/teacher_present_days_json/' + teacherId,
            { month: m },
            function (resp) {
                if (resp && typeof resp.present !== 'undefined') {
                    $days.value = resp.present;
                    $hint.textContent = ' (' + resp.present + ' present days in ' + m + ')';
                } else {
                    $hint.textContent = '';
                }
            }
        ).fail(function () { $hint.textContent = ' (could not load)'; });
    }
    $month.addEventListener('change', refresh);
})();
</script>
