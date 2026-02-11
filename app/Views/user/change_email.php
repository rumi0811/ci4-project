<div class="row">
	<div class="col-xs-12">
		<h1 class="page-title txt-color-blueDark">
			<i class="fa fa-fw <?php echo $currentPage["icon_file"]; ?>"></i>
			<?php echo $currentPage["menu_name"]; ?>
		</h1>
	</div>
</div>
<?php 
if ($message != "") {
?>
<div id="messageAlert" class="alert alert-success alert-dismissable">
	<button type="button" class="close" data-dismiss="alert"
		aria-hidden="true">&times;</button>
	<strong>Success</strong>
	<?php echo $message; ?>
</div>
<?php 
}
?>
<?php 
if ($error_message != "") {
?>
<div id="errorAlert" class="alert alert-danger alert-dismissable">
	<button type="button" class="close" data-dismiss="alert"
		aria-hidden="true">&times;</button>
	<strong>Error</strong>
	<?php echo $error_message; ?>
</div>
<?php 
}
?>

<section id="widget-grid">
	<div class="row">
		<!-- NEW COL START -->
		<article class="col-sm-12 col-md-12 col-lg-12">
			<!-- Widget ID (each widget will need unique ID)-->
			<div class="jarviswidget jarviswidget-color-blueDark" id="jarvis-form-input" data-widget-editbutton="true">
				<header>
					<span class="widget-icon"> <i class="fal fa-edit"></i>
					</span>
					<h2>Ganti Alamat Email / Username</h2>
				</header>
				<!-- widget div-->
				<div>
					<!-- widget edit box -->
					<div class="jarviswidget-editbox">
						<!-- This area used as dropdown edit box -->
					</div>
					<!-- end widget edit box -->
					<!-- widget content -->
					<div class="widget-body no-padding">
						<form id="form-input" class="smart-form" novalidate="novalidate" method="post">
							<input type="hidden" name="http_referer" value="<?php echo $http_referer; ?>" />
							<fieldset>
								
								<div class="row">
									<section class="col col-12">
										<label class="label">Email Lama</label>
										<label class="input">
											<span><?php echo $email; ?></span>
										</label>
									</section>
								</div>
								<div class="row">
									<section class="col col-12">
										<label class="label">Email Baru</label>
										<label class="input">
											<input type="email" name="new_email" id="new_email" />
										</label>
									</section>
								</div>
								<div class="row">
									<section class="col col-12">
										<label class="label">Sandi/Password</label>
										<label class="input">
											<input type="password" name="password" id="password" />
										</label>
									</section>
								</div>
								
							</fieldset>


							<input type="hidden" name="btnSave" id="btnSave" value="" />
							<footer>
								<button type="button" id="btnSaveDummy" class="btn btn-success pull-left">
									<i class="fa fa-save"></i> Submit
								</button>
								<button type="button" id="btnCancelDummy" class="btn pull-left">
									<i class="fa fa-times"></i> Batal
								</button>
							</footer>
						</form>
						<form id="form-cancel" method="post">
							<input type="hidden" name="http_referer" value="<?php echo $http_referer; ?>" />
							<input type="hidden" name="btnCancel" id="btnCancel" value="" />
						</form>
					</div>
				</div>
			</div>
		</article>
	</div>
</section>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/plugin/jquery-form/jquery-form.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
			
	pageSetUp();

	var form_input = $("#form-input").validate({
	
				// Rules for form validation
				rules : {
					new_email : {
						required : true,
						email : true
					},
					password : {
						required : true
					}
				},
	
				// Messages for form validation
				messages : {
					old_password : {
						required : 'Masukkan email baru',
						email : 'Masukkan alamat email yang valid'
					},
					password : {
						required : 'Silakan masukkan password/sandi anda'
					}
				},


				// Do not change code below
				errorPlacement : function(error, element) {
					error.insertAfter(element.parent());
				},

		        invalidHandler: function(form, validator) {
		            if (!validator.numberOfInvalids())
		                return;
		            $('html, body').animate({
		                scrollTop: $(validator.errorList[0].element).offset().top - 100
		            }, 500);
		        }
                
	});
			
	$("#btnCancelDummy").click(function(e)
	{
		$("#btnCancel").val("1");
		$("#form-cancel").submit();
		e.preventDefault();
	});
	
	$("#btnSaveDummy").click(function(e)
	{
		if ($("#form-input").valid()) {
			$.SmartMessageBox({
				title : "<i class='fa fa-save' style='color:green'></i> Ubah Email",
				content : "Ubah email anda?",
				buttons : '[No][Yes]'
			}, function(ButtonPressed) {
				if (ButtonPressed === "Yes") {
					$("#btnSave").val("1");
					$("#form-input").submit();
				}
				if (ButtonPressed === "No") {
					$.smallBox({
						title : "Ubah Email",
						content : "<i class='fa fa-times'></i> <i>Canceled...</i>",
						color : "#6c6f72",
						iconSmall : "fa fa-times fa-2x fadeInRight animated",
						timeout : 4000
					});
				}

			});	
		}
		e.preventDefault();
			
	});
	
});
        
</script>
