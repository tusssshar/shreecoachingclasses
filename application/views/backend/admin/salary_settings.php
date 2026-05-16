<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title">Salary Structure Settings</div>
    </div>
    <div class="panel-body">

        <?php if ($this->session->flashdata('flash_message')): ?>
            <div class="alert alert-success"><?php echo $this->session->flashdata('flash_message'); ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error_message')): ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error_message'); ?></div>
        <?php endif; ?>

        <p class="text-muted" style="margin-bottom:20px;">
            All earning components below are stored as a percentage of <strong>Basic Salary</strong>.
            <strong>PF</strong> and <strong>Tax</strong> are flat fixed amounts.
            The sum of earning percentages must total <strong>100%</strong>.
        </p>

        <form method="post" action="<?php echo base_url(); ?>index.php?admin/salary_settings/save" class="form-horizontal form-groups-bordered validate">
            <?php
                $pct   = $salary['percentages'];
                $fixed = $salary['fixed'];
                $earning_keys = array(
                    'hra'               => 'HRA',
                    'da'                => 'DA',
                    'conveyance'        => 'Conveyance',
                    'medical_allowance' => 'Medical',
                    'other_allowance'   => 'Other Allowance',
                );
            ?>

            <h4 style="margin-left:15px;">Earning Components (% of Basic)</h4>
            <?php foreach ($earning_keys as $key => $label): ?>
            <div class="form-group">
                <label class="col-sm-3 control-label"><?php echo $label; ?></label>
                <div class="col-sm-3">
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" max="100" class="form-control earning-pct"
                               name="<?php echo $key; ?>" value="<?php echo $pct[$key]; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="form-group" style="background:#f7f7f7;padding:8px 0;">
                <label class="col-sm-3 control-label"><strong>Total of Earnings</strong></label>
                <div class="col-sm-3">
                    <p class="form-control-static" id="total_pct" style="font-size:16px;font-weight:bold;">0 %</p>
                    <small id="total_status" class="text-danger">Must equal 100%</small>
                </div>
            </div>

            <hr>
            <h4 style="margin-left:15px;">Deductions</h4>
            <div class="form-group">
                <label class="col-sm-3 control-label">PF Deduction <span class="label label-default">fixed</span></label>
                <div class="col-sm-3">
                    <div class="input-group">
                        <span class="input-group-addon">&#8377;</span>
                        <input type="number" step="0.01" min="0" class="form-control"
                               name="pf_deduction" value="<?php echo $fixed['pf_deduction']; ?>">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Tax Deduction <span class="label label-default">fixed</span></label>
                <div class="col-sm-3">
                    <div class="input-group">
                        <span class="input-group-addon">&#8377;</span>
                        <input type="number" step="0.01" min="0" class="form-control"
                               name="tax_deduction" value="<?php echo $fixed['tax_deduction']; ?>">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Other Deduction <span class="label label-default">% of Basic</span></label>
                <div class="col-sm-3">
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" max="100" class="form-control"
                               name="other_deduction" value="<?php echo $pct['other_deduction']; ?>">
                        <span class="input-group-addon">%</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-5">
                    <button type="submit" id="saveBtn" class="btn btn-primary">Save Salary Structure</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    var inputs = document.querySelectorAll('.earning-pct');
    var totalEl = document.getElementById('total_pct');
    var statusEl = document.getElementById('total_status');
    var saveBtn = document.getElementById('saveBtn');

    function recalc() {
        var sum = 0;
        inputs.forEach(function (i) {
            sum += parseFloat(i.value) || 0;
        });
        sum = Math.round(sum * 100) / 100;
        totalEl.textContent = sum + ' %';
        if (Math.abs(sum - 100) < 0.001) {
            totalEl.style.color = '#1a8d3a';
            statusEl.className = 'text-success';
            statusEl.textContent = '✓ Earnings total 100%';
            saveBtn.disabled = false;
        } else {
            totalEl.style.color = '#c0392b';
            statusEl.className = 'text-danger';
            statusEl.textContent = 'Must equal 100% (currently ' + sum + '%)';
            saveBtn.disabled = true;
        }
    }
    inputs.forEach(function (i) {
        i.addEventListener('input', recalc);
        i.addEventListener('change', recalc);
    });
    recalc();
})();
</script>
