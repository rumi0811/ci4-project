<!DOCTYPE html>
<html lang="en-us">
	<head>
		<meta charset="utf-8">
		<title>Aktivasi Akun Pengguna</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css">	
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>assets/css/font-awesome.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>assets/css/smartadmin-production.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>assets/css/smartadmin-skins.css">	
	    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>assets/css/style.css"> 
		<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon/favicon.ico" type="image/x-icon">
		<link rel="icon" href="<?php echo base_url(); ?>assets/img/favicon/favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">
	</head>
	<body id="login" class="animated fadeInDown">
	
		<!--[if lt IE 9]>
      <div style="width: 100%; background: #009d4e; padding: 10px;">
				<img src="<?php echo base_url(); ?>assets/img/logo-white.png" alt="Logo" width="132px" border="0"/>
			</div>

		<center>
		<br />
		<h2>TagihanPulsa.com is not support your Internet browser.<br />The application is designed to work with Internet Explorer 11 or newer. <br />Please download the newer version of your Internet Explorer.</h2>
		</center>
		<br />
		
		<![endif]-->
		
		<!--[if gt IE 8]>	
	
	
		<!-- possible classes: minified, no-right-panel, fixed-ribbon, fixed-header, fixed-width-->
		<header id="header">

			<div id="logo-group">
				<span id="logo"> <img src="<?php echo base_url(); ?>assets/img/logo-white.png" alt="Logo"> </span>
			</div>

		</header>

		<div id="main" role="main">

			<!-- MAIN CONTENT -->
			<div id="content">
<?php
if ($message != "") {
?>
        <p class="alert alert-info font-md">
<?php
 echo $message;
?>
        </p>
<?php } ?>
<?php
if ($error_message != "") {
?>
        <p class="alert alert-danger font-md">
<?php
 echo $error_message;
?>
        </p>
<?php } ?>
        <a href="<?php echo base_url(); ?>login">Ke halaman login</a>
			</div>

		</div>

		<!--================================================== -->	

    <!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
		<script> if (!window.jQuery) { document.write('<script src="<?php echo base_url(); ?>assets/js/libs/jquery-2.0.2.min.js"><\/script>');} </script>

    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script> if (!window.jQuery.ui) { document.write('<script src="<?php echo base_url(); ?>assets/js/libs/jquery-ui-1.10.3.min.js"><\/script>');} </script>

		<!-- BOOTSTRAP JS -->		
		<script src="<?php echo base_url(); ?>assets/js/bootstrap/bootstrap.min.js"></script>
		
		
	<![endif]-->
	
	</body>
</html>