<?php
$prev = date('Y-m', strtotime($month . '-01 -1 month'));
$next = date('Y-m', strtotime($month . '-01 +1 month'));
?>
<hr>
<?php include __DIR__ . '/child_switcher.php'; ?>
<div class="panel panel-gradient">
    <div class="panel-heading"><div class="panel-title"><i class="entypo-calendar"></i> <?php echo get_phrase('attendance'); ?> &middot; <?php echo date('F Y', strtotime($month . '-01')); ?></div></div>
    <div class="panel-body">
        <div style="margin-bottom:12px;">
            <a href="<?php echo $portal_base; ?>attendance/<?php echo $prev; ?>" class="btn btn-default btn-sm">&laquo; <?php echo date('M Y', strtotime($prev . '-01')); ?></a>
            <?php if ($next <= date('Y-m')): ?>
                <a href="<?php echo $portal_base; ?>attendance/<?php echo $next; ?>" class="btn btn-default btn-sm"><?php echo date('M Y', strtotime($next . '-01')); ?> &raquo;</a>
            <?php endif; ?>
        </div>
        <div class="row">
            <div class="col-sm-4"><div class="tile-stats tile-white-gray"><div class="num"><?php echo $summary['present']; ?></div><h3><?php echo get_phrase('present'); ?></h3></div></div>
            <div class="col-sm-4"><div class="tile-stats tile-white-gray"><div class="num"><?php echo $summary['absent']; ?></div><h3><?php echo get_phrase('absent'); ?></h3></div></div>
            <div class="col-sm-4"><div class="tile-stats tile-white-gray"><div class="num"><?php echo $summary['percent'] === null ? '-' : $summary['percent'] . '%'; ?></div><h3><?php echo get_phrase('attendance'); ?></h3></div></div>
        </div>
        <?php if (empty($rows)): ?>
            <p class="text-muted"><?php echo get_phrase('no_attendance_marked_this_month'); ?></p>
        <?php else: ?>
        <table class="table table-bordered table-condensed" style="max-width:420px;">
            <thead><tr><th><?php echo get_phrase('date'); ?></th><th><?php echo get_phrase('status'); ?></th></tr></thead>
            <tbody>
            <?php foreach ($rows as $r): ?>
                <tr><td><?php echo date('D, d M Y', strtotime($r['date'])); ?></td>
                    <td><?php echo (int)$r['status'] === 1 ? '<span class="label label-success">' . get_phrase('present') . '</span>'
                        : ((int)$r['status'] === 2 ? '<span class="label label-danger">' . get_phrase('absent') . '</span>' : '-'); ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
