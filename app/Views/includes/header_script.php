	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#03a9f4">
	<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon/favicon.ico" type="image/x-icon" />
	<link rel="icon" href="<?php echo base_url(); ?>assets/img/favicon/favicon.ico" type="image/x-icon" />

	<link id="vendorsbundle" rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>assets/4.5.1/css/vendors.bundle.css">
	<link id="appbundle" rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>assets/4.5.1/css/app.bundle.css">
	<link id="mytheme" rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>assets/4.5.1/css/themes/cust-theme-4.css">
	<link id="myskin" rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>assets/4.5.1/css/skins/skin-master.css">
	<!-- Place favicon.ico in the root directory -->
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo base_url(); ?>assets/img/favicon/favicon.png">
	<!-- <link rel="icon" type="image/png" sizes="32x32" href="<?php echo base_url(); ?>assets/img/favicon/favicon-32x32.png"> -->
	<link rel="mask-icon" href="<?php echo base_url(); ?>assets/img/favicon/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>assets/4.5.1/css/fa-brands.css">
	<link rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>assets/4.5.1/css/formplugins/select2/select2.bundle.css">
	<link rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>assets/4.5.1/css/datagrid/datatables/datatables.bundle.css">
	<link rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>assets/4.5.1/css/notifications/sweetalert2/sweetalert2.bundle.css">
	<link rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>assets/4.5.1/css/notifications/toastr/toastr.css">
	<link rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>assets/4.5.1/css/formplugins/dropzone/dropzone.css">
	<link rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>assets/4.5.1/css/miscellaneous/lightgallery/lightgallery.bundle.css">
	<link rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>assets/4.5.1/css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
	<link rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>assets/4.5.1/css/formplugins/bootstrap-daterangepicker/bootstrap-daterangepicker.css">
	<link rel="stylesheet" media="screen, print" href="<?php echo base_url(); ?>assets/css/style.css">
<?php

$CI =& get_instance();
$CI->load->library('carabiner');

// add a css file
$CI->carabiner->css('1.9.0/css/bootstrap.min.css', 'screen');
//$CI->carabiner->css('1.9.0/css/font-awesome.min.css', 'screen');
// $CI->carabiner->css('1.9.0/css/all.min.css', 'screen');
$CI->carabiner->css('css/font-awesome-animation.min.css', 'screen');
$CI->carabiner->css('1.9.0/css/smartadmin-production-plugins.css', 'screen');
$CI->carabiner->css('1.9.0/css/smartadmin-production.css', 'screen');
$CI->carabiner->css('1.9.0/css/smartadmin-skins.min.css', 'screen');
$CI->carabiner->css('css/style.css', 'screen');

//$CI->carabiner->display('css');

?>
