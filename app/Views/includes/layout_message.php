
<?php 
if (isset($message) && $message != "") {
	echo '
<div id="messageAlert" class="alert alert-success alert-dismissible fade show" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true"><i class="fal fa-times"></i></span>
	</button>
	<strong>Success</strong> '.$message.'
</div>
<script type="text/javascript">
	$(document).ready(function() {	
		toastr["success"]("'.$message.'");
	});
</script>
';
}

if (isset($error_message) && $error_message != "") {
    echo '
<div id="errorAlert" class="alert alert-danger alert-dismissable fade show" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true"><i class="fal fa-times"></i></span>
	</button>
	<strong>Error</strong> '.$error_message.'
</div>
<script type="text/javascript">
	$(document).ready(function() {	
		toastr["error"]("'.$error_message.'");
	});
</script>
';
}
