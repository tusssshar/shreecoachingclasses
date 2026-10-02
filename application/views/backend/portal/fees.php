<hr>
<?php include __DIR__ . '/child_switcher.php'; ?>
<?php if (!$fees): ?>
    <div class="alert alert-info"><?php echo get_phrase('no_fee_details'); ?></div>
<?php else: ?>
<div class="row">
    <div class="col-sm-4"><div class="tile-stats tile-white-gray"><div class="num">&#8377; <?php echo number_format($fees['total']); ?></div><h3><?php echo get_phrase('total_fees'); ?></h3></div></div>
    <div class="col-sm-4"><div class="tile-stats tile-green"><div class="num">&#8377; <?php echo number_format($fees['paid']); ?></div><h3><?php echo get_phrase('paid'); ?></h3></div></div>
    <div class="col-sm-4"><div class="tile-stats tile-<?php echo $fees['balance'] > 0 ? 'red' : 'green'; ?>"><div class="num">&#8377; <?php echo number_format($fees['balance']); ?></div><h3><?php echo get_phrase('balance_due'); ?></h3></div></div>
</div>
<div class="panel panel-gradient">
    <div class="panel-heading"><div class="panel-title"><?php echo get_phrase('payment_history'); ?></div></div>
    <div class="panel-body">
        <?php if (empty($fees['history'])): ?>
            <p class="text-muted"><?php echo get_phrase('no_payments_recorded_yet'); ?></p>
        <?php else: ?>
        <table class="table table-bordered table-condensed">
            <thead><tr><th><?php echo get_phrase('date'); ?></th><th><?php echo get_phrase('title'); ?></th><th><?php echo get_phrase('method'); ?></th><th class="text-right"><?php echo get_phrase('amount'); ?></th></tr></thead>
            <tbody>
            <?php foreach ($fees['history'] as $p): ?>
                <tr><td><?php echo $p['timestamp'] ? date('d M Y', $p['timestamp']) : '-'; ?></td>
                    <td><?php echo html_escape($p['title']); ?></td><td><?php echo html_escape(ucfirst((string)$p['method'])); ?></td>
                    <td class="text-right">&#8377; <?php echo number_format((float)$p['amount'], 2); ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        <p class="text-muted"><?php echo get_phrase('contact_the_office_for_fee_receipts'); ?></p>
    </div>
</div>
<?php endif; ?>
