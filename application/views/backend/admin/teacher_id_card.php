<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$school_row  = $this->db->get_where('settings', array('type' => 'system_name'))->row();
$school_name = $school_row ? $school_row->description : 'School';

$this->load->model('crud_model');
?>

<style>
    .id-card {
        border: 2px solid #333;
        border-radius: 8px;
        padding: 0;
        margin-bottom: 20px;
        background: #fff;
        font-family: Arial, sans-serif;
        max-width: 320px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        page-break-inside: avoid;
    }
    .id-card-header {
        background: #2c3e50;
        color: #fff;
        text-align: center;
        padding: 10px;
        border-radius: 6px 6px 0 0;
    }
    .id-card-header h4 { margin: 0; font-size: 16px; letter-spacing: 0.5px; color: #ffffff !important; text-shadow: 0 1px 2px rgba(0,0,0,0.4); font-weight: bold; }
    .id-card-header small { font-size: 11px; color: #ffffff; opacity: 0.85; }
    .id-card-body { padding: 15px; text-align: center; }
    .id-card-photo {
        width: 100px; height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #2c3e50;
        margin-bottom: 10px;
    }
    .id-card-name { font-size: 17px; font-weight: bold; margin: 6px 0 2px; color: #2c3e50; }
    .id-card-role { font-size: 12px; color: #7f8c8d; margin-bottom: 10px; }
    .id-card-details {
        text-align: left;
        font-size: 12px;
        line-height: 1.7;
        border-top: 1px dashed #ccc;
        padding-top: 8px;
    }
    .id-card-details .label { font-weight: bold; color: #555; display: inline-block; min-width: 70px; }
    .id-card-footer {
        background: #2c3e50;
        color: #fff;
        text-align: center;
        font-size: 11px;
        padding: 6px;
        border-radius: 0 0 6px 6px;
        letter-spacing: 1px;
    }
    .id-card-actions { text-align: center; margin-top: 8px; }
    @media print {
        .hidden-print, .panel-heading, .navbar, .sidebar, .id-card-actions { display: none !important; }
        body { background: #fff; }
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <h4 class="panel-title pull-left">Teacher ID Cards</h4>
                <span class="pull-right text-muted">Total: <?php echo count($teachers); ?></span>
            </div>
            <div class="panel-body">
                <?php if (empty($teachers)): ?>
                    <p class="text-muted">No teachers found in the database.</p>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($teachers as $t):
                            $photo_url = $this->crud_model->get_image_url('teacher', $t['teacher_id']);
                        ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="id-card" id="card-<?php echo $t['teacher_id']; ?>">
                                    <div class="id-card-header">
                                        <h4><?php echo htmlspecialchars($school_name); ?></h4>
                                        <small>FACULTY IDENTITY CARD</small>
                                    </div>
                                    <div class="id-card-body">
                                        <img class="id-card-photo" src="<?php echo $photo_url; ?>" alt="">
                                        <div class="id-card-name"><?php echo htmlspecialchars($t['name']); ?></div>
                                        <div class="id-card-role">Teacher</div>
                                        <div class="id-card-details">
                                            <div><span class="label">ID:</span> TCH-<?php echo str_pad($t['teacher_id'], 4, '0', STR_PAD_LEFT); ?></div>
                                            <div><span class="label">Email:</span> <?php echo htmlspecialchars($t['email']); ?></div>
                                            <div><span class="label">Phone:</span> <?php echo htmlspecialchars($t['phone']); ?></div>
                                            <div><span class="label">Blood:</span> <?php echo htmlspecialchars($t['blood_group'] ?: '-'); ?></div>
                                            <div><span class="label">Address:</span> <?php echo htmlspecialchars($t['address'] ?: '-'); ?></div>
                                        </div>
                                    </div>
                                    <div class="id-card-footer">
                                        VALID <?php echo date('Y'); ?> &middot; <?php echo strtoupper(htmlspecialchars($school_name)); ?>
                                    </div>
                                </div>
                                <div class="id-card-actions hidden-print">
                                    <button type="button" class="btn btn-sm btn-primary"
                                            onclick="printIdCard('card-<?php echo $t['teacher_id']; ?>')">
                                        <i class="entypo-print"></i> Print
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function printIdCard(elemId) {
        var content = document.getElementById(elemId).outerHTML;
        var styles = '';
        var styleNodes = document.querySelectorAll('style');
        styleNodes.forEach(function (s) { styles += s.outerHTML; });

        var w = window.open('', 'print_id_card', 'height=600,width=400');
        w.document.write('<html><head><title>ID Card</title>' + styles + '</head><body style="padding:20px;">');
        w.document.write(content);
        w.document.write('</body></html>');
        w.document.close();
        w.focus();
        setTimeout(function () { w.print(); w.close(); }, 250);
    }
</script>
