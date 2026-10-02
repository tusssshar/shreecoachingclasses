<?php
/* Friendly theme: font + stylesheet + the school's colours (Settings > Theme & Colours). Included by includes_top.php and login.php. */
$__ts = array();
foreach ($this->db->where_in('type', array('ui_theme', 'ui_primary', 'ui_accent', 'ui_font'))->get('settings')->result_array() as $__r)
    $__ts[$__r['type']] = $__r['description'];
$__vars = sms_theme_vars($__ts['ui_theme'] ?? 'sunshine', $__ts['ui_primary'] ?? '', $__ts['ui_accent'] ?? '', $__ts['ui_font'] ?? 'nunito');
if ($__vars !== null): ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&family=Baloo+2:wght@500;700;800&display=swap">
    <link rel="stylesheet" href="assets/css/friendly-theme.css?v=4">
    <style>:root{<?php foreach ($__vars as $__k => $__v) echo $__k . ':' . $__v . ';'; ?>}</style>
<?php endif; ?>
