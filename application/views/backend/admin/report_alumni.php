<hr>
<div class="panel panel-gradient">
    <div class="panel-heading clearfix">
        <div class="panel-title pull-left">Alumni Report</div>
        <div class="pull-right">
            <button type="button" class="btn btn-success btn-sm"
                    onclick="SMS.go('admin/report_alumni/excel', { q: document.getElementById('rep_al_q').value });">
                <i class="entypo-download"></i> Excel (CSV)
            </button>
            <button type="button" class="btn btn-default btn-sm"
                    onclick="SMS.openPrintView('admin/report_alumni/print', { q: document.getElementById('rep_al_q').value });">
                <i class="entypo-print"></i> Print View
            </button>
        </div>
    </div>
    <div class="panel-body">

        <div class="form-inline" style="margin-bottom:14px;">
            <div class="form-group">
                <input type="text" id="rep_al_q" class="form-control" style="min-width:320px;"
                       value="<?php echo htmlspecialchars($q); ?>"
                       placeholder="Search alumni by name / email / mobile..."
                       onkeydown="if(event.key==='Enter'){event.preventDefault();SMS.go('admin/report_alumni',{q:this.value});}">
            </div>
            <button type="button" class="btn btn-primary"
                    onclick="SMS.go('admin/report_alumni', { q: document.getElementById('rep_al_q').value });">
                <i class="entypo-search"></i> Search
            </button>
            <?php if ($q !== ''): ?>
                <button type="button" class="btn btn-default" onclick="SMS.go('admin/report_alumni', {});">Clear</button>
            <?php endif; ?>
            <span class="text-muted" style="margin-left:14px;">Showing <strong><?php echo count($rows); ?></strong> alumni record(s).</span>
        </div>

        <?php if (empty($rows)): ?>
            <div class="alert alert-info">No alumni records yet. Students get marked alumni when they are re-registered or when toggled on the student edit form.</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr><th>#</th><th>Student ID</th><th>Name</th><th>Standard</th><th>Academic Year</th>
                        <th>Sex</th><th>Father Mobile</th><th>Email</th>
                        <th class="text-right">Total Fees</th><th class="text-right">Paid</th></tr>
                </thead>
                <tbody>
                <?php $i = 1; foreach ($rows as $r): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td>STU-<?php echo str_pad((int)$r['student_id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td><strong><?php echo htmlspecialchars($r['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($r['standard']); ?></td>
                        <td><?php echo htmlspecialchars($r['academic_year'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($r['sex']); ?></td>
                        <td><?php echo htmlspecialchars($r['fmobile']); ?></td>
                        <td><?php echo htmlspecialchars($r['email']); ?></td>
                        <td class="text-right">&#8377; <?php echo number_format((float)$r['total_fees'], 2); ?></td>
                        <td class="text-right">&#8377; <?php echo number_format((float)$r['payment_done'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </div>
</div>
