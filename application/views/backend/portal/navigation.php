<?php
/* Sidebar for the teacher / parent / student portals, built from sms_portal_menus() and Settings > Menu Permissions. */
$__role = $this->session->userdata('login_type');
$__base = $__role === 'parent' ? 'parents' : $__role;
$__perms = isset($this->portal_model) ? $this->portal_model->permissions() : array();
$__menus = sms_portal_menus();
$__menus = isset($__menus[$__role]) ? $__menus[$__role] : array();
// pages that belong to a menu besides its own page
$__extra = array('online_exams' => array('take_exam', 'exam_result', '../student/exam_result'), 'results' => array('../student/exam_result'),
                 'notices' => array('../portal/notices'), 'profile' => array('../portal/profile'), 'attendance' => array('../portal/attendance'),
                 'fees' => array('../portal/fees'));
?>
<div class="sidebar-menu">
    <header class="logo-env">
        <div class="logo">
            <a href="<?php echo base_url(); ?>index.php?<?php echo $__base; ?>/dashboard"><img src="uploads/logo.png" style="max-height:60px;" alt="" /></a>
        </div>
        <div class="sidebar-collapse"><a href="#" class="sidebar-collapse-icon with-animation"><i class="entypo-menu"></i></a></div>
        <div class="sidebar-mobile-menu visible-xs"><a href="#" class="with-animation"><i class="entypo-menu"></i></a></div>
    </header>
    <ul id="main-menu" class="">
        <?php foreach ($__menus as $__key => $__m):
            if (!sms_menu_allowed($__perms, $__role, $__key)) continue;
            $__active = $page_name === $__m[2] || in_array($page_name, $__extra[$__key] ?? array(), true); ?>
            <li class="<?php if ($__active) echo 'active'; ?>">
                <a href="<?php echo base_url(); ?>index.php?<?php echo $__base; ?>/<?php echo $__m[2]; ?>">
                    <i class="<?php echo $__m[1]; ?>"></i>
                    <span><?php echo get_phrase($__m[0]); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
