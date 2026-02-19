<?php echo view('includes/layout_breadcrumb'); ?>
<?php echo view('includes/layout_message'); ?>

<div class="row">
	<div class="col-xl-12">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>
					Change <span class="fw-300"><i>Password</i></span>
				</h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<form class="was-validated" id="form-input" novalidate="novalidate" method="post" oninput="new_password2.setCustomValidity(new_password2.value != new_password1.value ? 'Confirmation password is not match' : '');">
						<input type="hidden" name="http_referer" value="<?php echo $http_referer; ?>" />
						<input type="hidden" name="btnSave" value="1" />

						<div class="panel-content">
							<div class="form-row">
								<div class="col-md-12 mb-3">
									<?php echo smart_form_password('old_password', '', null, 'Old Password', '', '', 'required'); ?>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-12 mb-3">
									<?php echo smart_form_password('new_password1', '', null, 'New Password', '', '', 'required'); ?>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-12 mb-3">
									<?php echo smart_form_password('new_password2', '', null, 'Confirm Password', '', '', 'required', 'Confirmation password is not match'); ?>
								</div>
							</div>
						</div>
						<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row">
							<div class="mt-3">
								<button class="btn btn-success" type="button" id="btnSaveDummy"><i class="fal fa-save"></i> Save</button>
								<button type="button" id="btnCancelDummy" class="btn btn-danger">
									<i class="fal fa-times"></i> Batal
								</button>
							</div>
						</div>
					</form>
					<form id="form-cancel" method="post">
						<input type="hidden" name="http_referer" value="<?php echo $http_referer; ?>" />
						<input type="hidden" name="btnCancel" id="btnCancel" value="" />
					</form>


				</div>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
	$(document).ready(function() {

		$("#btnCancelDummy").click(function(e) {
			$("#btnCancel").val("1");
			$("#form-cancel").submit();
			e.preventDefault();
		});

		$("#btnSaveDummy").click(function(e) {
			SaveConfirmationAndSubmit(e);
		});


	});
</script>