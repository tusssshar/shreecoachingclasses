<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$school_row    = $this->db->get_where('settings', array('type' => 'system_name'))->row();
$school_name   = $school_row ? $school_row->description : 'School';

$address_row   = $this->db->get_where('settings', array('type' => 'address'))->row();
$school_addr   = $address_row ? $address_row->description : '';

$contact_phone   = '9987676008';
$contact_email   = 'shreecochingclasses@gmail.com';
$contact_website = 'shreecochingclasses.com';

$this->load->model('crud_model');

function compute_expiry_date($joined_ts)
{
    if (!$joined_ts) return '';
    $year  = (int) date('Y', $joined_ts);
    $month = (int) date('n', $joined_ts);
    $expiry_year = ($month <= 3) ? $year : $year + 1;
    return date('d M Y', strtotime($expiry_year . '-03-31'));
}
?>

<style>
    .id-card-pair { margin-bottom: 22px; page-break-inside: avoid; }
    .id-card-v2 {
        border: 1px solid #c9c9c9;
        border-radius: 10px;
        background: #fff;
        font-family: 'Segoe UI', Arial, sans-serif;
        width: 320px;
        display: inline-block;
        vertical-align: top;
        margin-right: 14px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.18);
        overflow: hidden;
    }

    /* Header — bold, gradient, with accent bar */
    .idc-header {
        background: linear-gradient(135deg, #1f3a68 0%, #2c5298 100%);
        color: #fff;
        text-align: center;
        padding: 14px 10px 10px;
        position: relative;
    }
    .idc-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: 1.2px;
        color: #ffffff !important;
        text-shadow: 0 1px 3px rgba(0,0,0,0.5);
    }
    .idc-header .school-address {
        font-size: 10px;
        color: #ffffff;
        opacity: 0.92;
        display: block;
        margin-top: 3px;
        line-height: 1.3;
        font-weight: 400;
    }
    .idc-header small {
        font-size: 10.5px;
        letter-spacing: 1.5px;
        color: #ffffff;
        opacity: 0.95;
        display: block;
        margin-top: 6px;
        text-transform: uppercase;
    }
    .idc-header::after {
        content: "";
        display: block;
        height: 4px;
        background: #f5b921;
        margin: 9px -10px -10px;
    }

    /* Front body — compact, photo + key data */
    .idc-body { padding: 12px 14px 10px; text-align: center; }
    .idc-photo {
        width: 100px; height: 120px;
        object-fit: cover;
        border: 3px solid #1f3a68;
        border-radius: 4px;
        background: #eef1f6;
        margin: 2px 0 10px;
        display: block;
        margin-left: auto; margin-right: auto;
    }
    .idc-id-badge {
        display: inline-block;
        background: #f5b921;
        color: #1f3a68;
        font-weight: bold;
        font-size: 12px;
        padding: 3px 10px;
        border-radius: 14px;
        margin-bottom: 8px;
        letter-spacing: 0.6px;
    }
    .idc-name {
        font-size: 15px;
        font-weight: 700;
        color: #1f3a68;
        margin: 2px 0 8px;
        line-height: 1.2;
        word-wrap: break-word;
    }

    .idc-info {
        text-align: left;
        font-size: 12px;
        line-height: 1.55;
        background: #f8f9fb;
        border: 1px solid #e3e6ec;
        border-radius: 5px;
        padding: 6px 9px;
        margin: 0;
    }
    .idc-info > div { display: flex; justify-content: space-between; padding: 1px 0; }
    .idc-info .label { font-weight: 600; color: #555; }
    .idc-info .value { color: #1f3a68; font-weight: 600; text-align: right; max-width: 60%; word-wrap: break-word; }

    /* Footer — school address strip */
    .idc-footer {
        background: #1f3a68;
        color: #fff;
        text-align: center;
        padding: 7px 10px;
        font-size: 10.5px;
        font-weight: 500;
        letter-spacing: 0.4px;
    }

    /* Back side */
    .idc-back .idc-body { text-align: left; padding: 12px 14px 10px; }
    .idc-back h4 {
        font-size: 12px;
        text-align: center;
        margin: 0 0 6px;
        color: #1f3a68;
        font-weight: 700;
        letter-spacing: 0.4px;
    }
    .idc-back ol {
        padding-left: 18px;
        margin: 0 0 8px;
        font-size: 10.5px;
        line-height: 1.45;
        color: #333;
    }
    .idc-back ol li { margin-bottom: 3px; }
    .idc-back .contact-block,
    .idc-back .dates-block {
        border-top: 1px dashed #c0c0c0;
        padding-top: 6px;
        margin-top: 6px;
        font-size: 11px;
        line-height: 1.55;
        color: #333;
    }
    .idc-back .contact-block strong,
    .idc-back .dates-block strong {
        color: #1f3a68;
        display: inline-block;
        min-width: 60px;
    }
    .idc-back .dates-block .valid-until {
        color: #c0392b;
        font-weight: bold;
    }
    .idc-back .signature-block {
        margin-top: 14px;
        text-align: right;
        font-size: 10.5px;
        padding-bottom: 2px;
    }
    .idc-back .signature-line {
        display: inline-block;
        border-top: 1px solid #333;
        min-width: 130px;
        padding-top: 2px;
        margin-top: 22px;
        color: #555;
    }

    .idc-actions { margin-top: 6px; }

    @media print {
        .hidden-print, .panel-heading, .navbar, .sidebar, .idc-actions { display: none !important; }
        body { background: #fff; }
        .id-card-v2 { box-shadow: none; border: 1px solid #1f3a68; }
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading clearfix">
                <h4 class="panel-title pull-left">Student ID Cards</h4>
                <span class="pull-right text-muted">Total: <?php echo count($students); ?></span>
            </div>
            <div class="panel-body">
                <?php if (empty($students)): ?>
                    <p class="text-muted">No active students found.</p>
                <?php else: ?>
                    <?php foreach ($students as $s):
                        $photo_url   = $this->crud_model->get_image_url('student', $s['student_id']);
                        $joined_ts   = !empty($s['created_at']) ? strtotime($s['created_at']) : null;
                        $joined_disp = $joined_ts ? date('d M Y', $joined_ts) : '-';
                        $expiry_disp = compute_expiry_date($joined_ts) ?: '-';
                        $class_disp  = $s['standard'];
                        if ($class_disp === '' || $class_disp === null) {
                            $class_disp = $this->crud_model->get_type_name_by_id('class', $s['class_id']);
                        }
                        $card_id = 'sidc-' . $s['student_id'];
                    ?>
                        <div class="id-card-pair" id="<?php echo $card_id; ?>">
                            <!-- FRONT -->
                            <div class="id-card-v2 idc-front">
                                <div class="idc-header">
                                    <h3><?php echo strtoupper(htmlspecialchars($school_name)); ?></h3>
                                    <small>Student Identity Card</small>
                                </div>
                                <div class="idc-body">
                                    <img class="idc-photo" src="<?php echo $photo_url; ?>" alt="">
                                    <div class="idc-id-badge">STU-<?php echo str_pad($s['student_id'], 5, '0', STR_PAD_LEFT); ?></div>
                                    <div class="idc-name"><?php echo htmlspecialchars($s['name']); ?></div>
                                    <div class="idc-info">
                                        <div><span class="label">Father</span><span class="value"><?php echo htmlspecialchars($s['father_name'] ?: '-'); ?></span></div>
                                        <div><span class="label">Class</span><span class="value"><?php echo htmlspecialchars($class_disp ?: '-'); ?></span></div>
                                        <div><span class="label">Emergency</span><span class="value"><?php echo htmlspecialchars($s['emergency_contact'] ?? '-' ?: '-'); ?></span></div>
                                    </div>
                                </div>
                                <div class="idc-footer">
                                    <?php echo htmlspecialchars($school_addr); ?>
                                </div>
                            </div>

                            <!-- BACK -->
                            <div class="id-card-v2 idc-back">
                                <div class="idc-header">
                                    <h3><?php echo strtoupper(htmlspecialchars($school_name)); ?></h3>
                                    <small>TERMS &amp; CONDITIONS</small>
                                </div>
                                <div class="idc-body">
                                    <h4>Terms and Conditions &mdash; <?php echo htmlspecialchars($school_name); ?></h4>
                                    <ol>
                                        <li>Fees once paid are non-refundable and non-transferable.</li>
                                        <li>Students are expected to maintain regular attendance and discipline in class.</li>
                                        <li>Parents are requested to ensure timely payment of fees and inform the institute about any absence in advance.</li>
                                        <li>Shree Coaching Classes reserves the right to make changes to class schedules or batches when required.</li>
                                    </ol>
                                    <div class="contact-block">
                                        <div><strong>Phone:</strong> <?php echo htmlspecialchars($contact_phone); ?></div>
                                        <div><strong>Email:</strong> <?php echo htmlspecialchars($contact_email); ?></div>
                                        <div><strong>Website:</strong> <?php echo htmlspecialchars($contact_website); ?></div>
                                    </div>
                                    <div class="dates-block">
                                        <div><strong>Joined:</strong> <?php echo $joined_disp; ?></div>
                                        <div><strong>Valid Until:</strong> <span class="valid-until"><?php echo $expiry_disp; ?></span></div>
                                    </div>
                                    <div class="signature-block">
                                        <div class="signature-line">Authorized Signature</div>
                                    </div>
                                </div>
                            </div>

                            <div class="idc-actions hidden-print">
                                <button type="button" class="btn btn-sm btn-primary"
                                        onclick="printIdPair('<?php echo $card_id; ?>')">
                                    <i class="entypo-print"></i> Print
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function printIdPair(elemId) {
        var content = document.getElementById(elemId).outerHTML;
        var styles = '';
        document.querySelectorAll('style').forEach(function (s) { styles += s.outerHTML; });

        var w = window.open('', 'print_id', 'height=700,width=750');
        w.document.write('<html><head><title>Student ID Card</title>' + styles + '</head><body style="padding:20px;">');
        w.document.write(content);
        w.document.write('</body></html>');
        w.document.close();
        w.focus();
        setTimeout(function () { w.print(); w.close(); }, 250);
    }
</script>
