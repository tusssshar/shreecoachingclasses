<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">

    <title>Student Information</title>

    <!-- ===================== -->
    <!-- CSS CORE (THEME) -->
    <!-- ===================== -->

    <link rel="stylesheet" href="assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css">
    <link rel="stylesheet" href="assets/css/font-icons/entypo/css/entypo.css">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/neon-core.css">
    <link rel="stylesheet" href="assets/css/neon-theme.css">
    <link rel="stylesheet" href="assets/css/neon-forms.css">

    <?php
        $skin_colour = $this->db->get_where('settings', ['type' => 'skin_colour'])->row()->description;
        if ($skin_colour != ''):
    ?>
        <link rel="stylesheet" href="assets/css/skins/<?php echo $skin_colour;?>.css">
    <?php endif; ?>

    <?php if ($text_align == 'right-to-left') : ?>
        <link rel="stylesheet" href="assets/css/neon-rtl.css">
    <?php endif; ?>

    <link rel="shortcut icon" href="assets/images/favicon.png">

    <link rel="stylesheet" href="assets/css/font-icons/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/js/vertical-timeline/css/component.css">
    <link rel="stylesheet" href="assets/js/datatables/responsive/css/datatables.responsive.css">

    <!-- ===================== -->
    <!-- JS CORE (IMPORTANT ORDER) -->
    <!-- ===================== -->

    <!-- 1. jQuery -->
    <script src="assets/js/jquery-1.11.0.min.js"></script>

    <!--[if lt IE 9]>
        <script src="assets/js/ie8-responsive-file-warning.js"></script>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- ===================== -->
    <!-- AMCHARTS (OLD SYSTEM) -->
    <!-- ===================== -->

    <script src="<?php echo base_url();?>assets/js/amcharts/amcharts.js"></script>
    <script src="<?php echo base_url();?>assets/js/amcharts/pie.js"></script>
    <script src="<?php echo base_url();?>assets/js/amcharts/serial.js"></script>
    <script src="<?php echo base_url();?>assets/js/amcharts/gauge.js"></script>
    <script src="<?php echo base_url();?>assets/js/amcharts/funnel.js"></script>
    <script src="<?php echo base_url();?>assets/js/amcharts/radar.js"></script>

    <script src="<?php echo base_url();?>assets/js/amcharts/exporting/amexport.js"></script>
    <script src="<?php echo base_url();?>assets/js/amcharts/exporting/rgbcolor.js"></script>
    <script src="<?php echo base_url();?>assets/js/amcharts/exporting/canvg.js"></script>
    <script src="<?php echo base_url();?>assets/js/amcharts/exporting/jspdf.js"></script>

    <!-- FIXED FILE SAVER -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

    <script src="<?php echo base_url();?>assets/js/amcharts/exporting/jspdf.plugin.addimage.js"></script>

    <!-- ===================== -->
    <!-- DATA TABLES (FIXED ORDER) -->
    <!-- ===================== -->

    <!-- 2. DATA TABLE CORE (IMPORTANT - WAS MISSING) -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <!-- Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    <!-- Buttons Core -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>

    <!-- Export plugins -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <!-- Dependencies -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <!-- ===================== -->
    <!-- CUSTOM SCRIPT -->
    <!-- ===================== -->

    <script>
        function checkDelete() {
            return confirm("Are You Sure To Delete This!");
        }
    </script>

</head>
<body>