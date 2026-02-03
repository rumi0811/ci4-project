<?php echo view('includes/layout_breadcrumb'); ?>
<div class="row">
	<div class="col-xl-12">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>
					<i class="subheader-icon fal fa-edit"></i> Form Input: <span class="fw-300"><i>Group</i></span>
				</h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<?php echo view('includes/layout_message'); ?>

					<form id="form-input" class="needs-validation" novalidate="novalidate" method="post">
						<input type="hidden" name="group_id" id="group_id" value="<?php echo $record['group_id']; ?>" />
						<input type="hidden" name="http_referer" value="<?php echo $http_referer; ?>" />
						<input type="hidden" name="btnSave" id="btnSave" value="1" />

						<div class="panel-content">
							<div class="form-row">
								<div class="col-md-3 mb-3">
									<?php
									echo smart_form_input('group_code', (isset($record['group_code'])) ? $record['group_code'] : '', null, 'Group Code');
									?>
								</div>
								<div class="col-md-3 mb-3">
									<?php
									echo smart_form_input('group_name', (isset($record['group_name'])) ? $record['group_name'] : '', null, 'Group Name');
									?>
								</div>
								<div class="col-md-3 mb-3">
									<?php
									$groupTypes = array("0" => "Internal", "1" => "External");
									echo smart_form_dropdown('group_type', $groupTypes, (isset($record['group_type'])) ? $record['group_type'] : '', '', 'Group Type', '', '<i></i>');
									?>
								</div>
								<div class="col-md-3 mb-3">
									<?php
									echo smart_form_dropdown('company_id', $companies, (isset($record['company_id'])) ? $record['company_id'] : '', '', 'Company', '', '<i></i>');
									?>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-3 mb-3">
									<?php
									echo smart_form_switch('is_active', (isset($record['is_active'])) ? $record['is_active'] : '', '', 'Active');
									?>
								</div>
							</div>

							<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row">
								<div>
									<button type="button" id="btnSaveDummy" class="btn btn-primary">
										<i class="fal fa-save"></i> Save
									</button>
									<button type="button" id="btnCancelDummy" class="btn btn-secondary">
										<i class="fal fa-times"></i> Cancel
									</button>
								</div>
							</div>
					</form>
				</div>
			</div>
		</div>
		</article>
	</div>
	</section>

	<form id="form-cancel" method="post">
		<input type="hidden" name="http_referer" value="<?php echo $http_referer; ?>" />
		<input type="hidden" name="btnCancel" value="1" />
	</form>

	<script type="text/javascript">
		$(document).ready(function() {

			$("#btnCancelDummy").click(function(e) {
				$("#form-cancel").submit();
				e.preventDefault();
			});

			$("#btnSaveDummy").click(function(e) {
				SaveConfirmationAndSubmit(e);
			});

		});
	</script>