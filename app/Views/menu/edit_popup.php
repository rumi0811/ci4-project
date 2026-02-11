<!-- Modal Add Panel-->
<div class="modal fade" id="modal_panel" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document" id="modalPanelContent">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					<i class="fal fa-edit"></i> Form Input: <span class="fw-300"><i>Menu/Function</i></span>
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true"><i class="fal fa-times"></i></span>
				</button>
			</div>
			<div class="modal-body no-padding">
				<!-- START ROW -->
				<div class="row">
					<!-- NEW COL START -->
					<article class="col-xl-12">
						<form class="was-validated" id="form-input" novalidate="novalidate" method="post">
							<input type="hidden" name="menu_id" id="menu_id" value="" />
							<input type="hidden" name="btnSave" value="1" />

							<div class="form-row">
								<div class="col-md-6 mb-3">
									<?php echo smart_form_input('menu_name', '', '', 'Menu Name', '', '', 'required'); ?>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo smart_form_input('page_name', '', '', 'Page Name', '', '', 'required'); ?>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-6 mb-3">
									<?php echo smart_form_input('file_name', '', '', 'File Name', '', ''); ?>
								</div>
								<div class="col-md-6 mb-3">
									<?php echo smart_form_dropdown(
										'parent_menu_id',
										$menu_data,
										(isset($record['user_type_id'])) ? $record['user_type_id'] : '',
										'',
										'Parent Menu'
									);
									?>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-6 mb-3">
									<?php echo smart_form_dropdown(
										'icon_file',
										$font_awesome,
										'',
										'',
										'Icon'
									);
									?>
								</div>
								<div class="col-md-6 mb-3">
									<?php
									echo smart_form_textarea('note', '', 'rows="3"', 'Note');
									?>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-6 mb-3">
									<?php
									echo smart_form_input_number('sequence_no', '', '', 'Order No');
									?>
								</div>
								<div class="col-md-3 mb-3">
									<div class="form-group">
										<?php
										echo smart_form_switch('is_visible', '', '', 'Visible');
										?>
									</div>
								</div>
								<div class="col-md-3 mb-3">
									<?php
									echo smart_form_switch('is_new_flag', '', '', 'Flag New');
									?>
								</div>
							</div>
							<button class="btn btn-success" type="button" id="btnSaveDummy"><i class="fal fa-save"></i> Save</button>
							<button type="button" class="btn btn-danger pull-left" data-dismiss="modal" aria-hidden="true">
								<i class="fal fa-times"></i> Close
							</button>
						</form>
					</article>
					<!-- END COL -->
				</div>
				<!-- END ROW -->
				<?php echo view('includes/layout_modal_progress'); ?>

			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script type="text/javascript">
	$(document).ready(function() {
		$("#icon_file").select2({
			allowHtml: true,
			width: "100%",
			templateResult: formatIconFile,
			templateSelection: formatIconFile,
			escapeMarkup: function(m) {
				return m;
			}
		});

		$("#btnSaveDummy").click(function(e) {
			SaveConfirmationAndSubmit(e);
		});
	});

	function formatIconFile(row) {
		return '<i class="' + row.text + '"></i>' + ' ' + row.text;
	}

	var editForm = function(id) {
		$("#modalLoadingInfo").css("display", "block");
		$("#modal_panel").modal();

		url = "<?php echo base_url(); ?>menu/edit/" + id;

		$.ajax({
			url: url,
			type: 'GET',
			dataType: 'json',
			success: function(obj) {
				if (typeof(obj.error_message) != 'undefined') {
					toastr['error'](obj.error_message);
					$("#modal_panel").modal('hide');
				} else {
					$("#menu_id").val(obj.menu_id);
					$("#menu_name").val(obj.menu_name);
					$("#page_name").val(obj.page_name);
					$("#note").val(obj.note);
					$("#is_visible").prop("checked", (obj.is_visible == 1));
					$("#is_new_flag").prop("checked", (obj.is_new_flag == 1));
					$("#file_name").val(obj.file_name);
					$("#sequence_no").val(obj.sequence_no);
					$("#parent_menu_id").val(obj.parent_menu_id);
					$('#parent_menu_id').trigger('change');
					$("#icon_file").val(obj.icon_file);
					$('#icon_file').trigger('change');
				}
			},
			error: function(xhr, status, error) {
				console.error('Edit form error:', error);
				toastr['error']('Failed to load data');
				$("#modal_panel").modal('hide');
			},
			complete: function() {
				$("#modalLoadingInfo").css("display", "none");
			}
		});
	};
</script>