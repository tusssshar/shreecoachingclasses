<hr>
<div class="panel panel-gradient">
    <div class="panel-heading">
        <div class="panel-title">CBT Exam Results</div>
    </div>
    <div class="panel-body">

        <?php if (empty($results)): ?>
            <div class="alert alert-info">No exam results yet. Check papers via <a href="<?php echo base_url(); ?>index.php?admin/exam_paper_check">Online Paper Checking</a>.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered datatable" id="table_export">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Date</th>
                            <th>Marks</th>
                            <th>%</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i = 1; foreach ($results as $r):
                        $total = (float)$r['total_marks'];
                        $possible = (float)$r['total_possible'];
                        $pct = $possible > 0 ? round(($total / $possible) * 100, 2) : 0;
                        $url = base_url() . 'index.php?admin/exam_result_detail'
                             . '&class_id='   . (int)$r['class_id']
                             . '&subject_id=' . (int)$r['subject_id']
                             . '&date='       . urlencode($r['date'])
                             . '&duration='   . (int)$r['duration']
                             . '&session='    . urlencode($r['session'])
                             . '&student_id=' . (int)$r['student_id'];
                    ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($r['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($r['class_name']); ?></td>
                            <td><?php echo htmlspecialchars($r['subject_name']); ?></td>
                            <td><?php echo htmlspecialchars($r['date']); ?></td>
                            <td><?php echo $total; ?> / <?php echo $possible; ?></td>
                            <td><?php echo $pct; ?>%</td>
                            <td><span class="label label-<?php echo $r['status']=='checked' ? 'success' : 'warning'; ?>"><?php echo htmlspecialchars($r['status']); ?></span></td>
                            <td><a href="<?php echo $url; ?>" class="btn btn-info btn-sm">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function ($) {
    if ($("#table_export").length) { $("#table_export").dataTable(); }
});
</script>
