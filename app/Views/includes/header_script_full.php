<?php echo view("includes/header_script"); ?>

<!-- Common JS file -->
<script src="<?php echo base_url(); ?>assets/js/common.js"></script>

<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
	if (!window.jQuery) {
		document.write('<script src="<?php echo base_url(); ?>assets/1.9.0/js/libs/jquery-3.2.1.min.js"><\/script>');
	}
</script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script>
	if (!window.jQuery.ui) {
		document.write('<script src="<?php echo base_url(); ?>assets/1.9.0/js/libs/jquery-ui.min.js"><\/script>');
	}
</script>