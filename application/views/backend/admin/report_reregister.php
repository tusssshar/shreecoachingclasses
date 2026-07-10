<hr>
<div class="panel panel-gradient">
    <div class="panel-heading clearfix">
        <div class="panel-title pull-left">Re-register History</div>
        <div class="pull-right">
            <button type="button" class="btn btn-success btn-sm"
                    onclick="SMS.go('admin/report_reregister/excel', { q: document.getElementById('rep_rr_q').value });">
                <i class="entypo-download"></i> Excel (CSV)
            </button>
            <button type="button" class="btn btn-default btn-sm"
                    onclick="SMS.openPrintView('admin/report_reregister/print', { q: document.getElementById('rep_rr_q').value });">
                <i class="entypo-print"></i> Print View
            </button>
        </div>
    </div>
    <div class="panel-body">

        <p class="text-muted">Every student row whose <code>previous_student_id</code> points back to an earlier registration — i.e. one re-registration event per row.</p>

        <div class="form-inline" style="margin-bottom:14px;">
            <div class="form-group">
                <input type="text" id="rep_rr_q" class="form-control" style="min-width:320px;"
                       value="<?php echo htmlspecialchars($q); ?>"
                       placeholder="Search by current or previous name / mobile..."
                       onkeydown="if(event.key==='Enter'){event.preventDefault();SMS.go('admin/report_reregister',{q:this.value});}">
            </div>
            <button type="button" class="btn btn-primary"
                    onclick="SMS.go('admin/report_reregister', { q: document.getElementById('rep_rr_q').value });">
                <i class="entypo-search"></i> Search
            </button>
            <?php if ($q !== ''): ?>
                <button type="button" class="btn btn-default" onclick="SMS.go('admin/report_reregister', {});">Clear</button>
            <?php endif; ?>
            <span class="text-muted" style="margin-left:14px;">Showing <strong><?php echo count($rows); ?></strong> re-registration(s).</span>
        </div>

        <?php if (empty($rows)): ?>
            <div class="alert alert-info">No re-registrations on file yet.</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" style="font-size:12px;">
                <thead style="background:#f4f6fa;">
                    <tr>
                        <th>#</th>
                        <th colspan="4" style="background:#fff7ed; text-align:center;">PREVIOUS RECORD</th>
                        <th colspan="4" style="background:#ecfdf5; text-align:center;">NEW RECORD</th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>Student ID</th><th>Name</th><th>Standard</th><th>Academic Year</th>
                        <th>Student ID</th><th>Name</th><th>Standard</th><th>Academic Year</th>
                    </tr>
                </thead>
                <tbody>
                <?php $i = 1; foreach ($rows as $r): ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo $r['prev_id'] ? 'STU-' . str_pad((int)$r['prev_id'], 5, '0', STR_PAD_LEFT) : '-'; ?></td>
                        <td><?php echo htmlspecialchars($r['prev_name'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($r['prev_standard'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($r['prev_ay'] ?? '-'); ?></td>
                        <td><strong>STU-<?php echo str_pad((int)$r['new_id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                        <td><strong><?php echo htmlspecialchars($r['new_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($r['new_standard']); ?></td>
                        <td><?php echo htmlspecialchars($r['new_ay']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

    </div>
</div>
