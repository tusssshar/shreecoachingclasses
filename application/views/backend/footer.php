<!-- Footer -->
<?php
    $school_row = $this->db->get_where('settings', array('type' => 'system_name'))->row();
    $school_name = $school_row ? $school_row->description : '';
?>
<footer class="main" align="center" style="color:#FF0000">
    &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($school_name); ?>.
</footer>
	
