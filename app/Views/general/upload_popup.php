<!-- Modal Add Panel-->
<div class="modal fade" id="modal_panel_upload_data" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					<i class="fal fa-upload"></i> Upload Data: <span class="fw-300"><i><?php echo is_array($currentPage) ? $currentPage['menu_name'] : $currentPage; ?></i></span>
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true"><i class="fal fa-times"></i></span>
				</button>
			</div>
			<div class="modal-body no-padding">
				<div id="errorAlertUpload" class="alert alert-danger alert-dismissable fade show" role="alert" style="display: none">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true"><i class="fal fa-times"></i></span>
					</button>
					<strong>Error</strong> Please drop at least one file to upload
				</div>
				<!-- START ROW -->
				<div class="row">
					<!-- NEW COL START -->
					<article class="col-sm-12 col-md-12 col-lg-12">
						<form method="get" action="<?php echo $template_file; ?>">
							<button type="submit" class="btn btn-primary"><i class="fal fa-download"></i> Download Template for Upload</button>
						</form>
						<hr />
						<form id="form-upload-data-dropzone" action="<?php echo base_url() . $controllerName; ?>/upload_file" class="dropzone">
							<input type="hidden" name="pk_id" value="0" />
							<input type="hidden" name="btnUpload" value="1" />
							<input type="hidden" name="session_file_hash" value="<?php echo $session_file_hash ?? ''; ?>" />

						</form>
						<form id="form-upload-data" method="post" action="<?php echo base_url() . $controllerName; ?>/post_upload">
							<input type="hidden" name="session_file_hash" value="<?php echo $session_file_hash ?? ''; ?>" />
							<input type="hidden" name="btnUpload" value="1" />
							<input type="hidden" name="uploaded_files_list" id="uploaded_files_list" />
							<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row">
								<div class="mt-3">
									<button class="btn btn-success" type="button" id="btnUploadDummy"><i class="fal fa-upload"></i> Upload Data</button>
									<button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">
										<i class="fal fa-times"></i> Close
									</button>
								</div>
							</div>
						</form>
					</article>
					<!-- END COL -->
				</div>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->



<script type="text/javascript">
	var arrUploadedFiles = [];
	$(document).ready(function() {
		$("#btnUploadDummy").click(function(e) {
			if (arrUploadedFiles.length == 0) {
				$('#errorAlertUpload').css('display', 'block');
				toastr['error']('Please drop at least one file to upload');
				return;
			} else {
				$("#uploaded_files_list").val(JSON.stringify(arrUploadedFiles));
				$('#errorAlertUpload').css('display', 'none');
			}
			UploadConfirmationAndSubmit(e);
		});

		$("#form-upload-data-dropzone").dropzone({
			addRemoveLinks: true,
			maxFilesize: 4, //MB
			acceptedFiles: ".csv, .xls, .xlsx",
			dictDefaultMessage: '<span class="text-center"><span class="font-lg visible-xs-block visible-sm-block visible-lg-block"><span class="font-lg"><i class="fal fa-caret-right text-info"></i> Drop files (same as template above with data)<br /><span class="font-xs">to upload data</span></span><span>&nbsp&nbsp<h4 class="display-inline"><br />(Or click here to open file dialog, and select file)</h4></span>',
			dictResponseError: 'Error uploading file!',
			success: function(file, json) {
				console.log(file);
				response = $.parseJSON(json);
				if (response.status == 0) {
					isExists = false;
					for (var i in arrUploadedFiles) {
						if (arrUploadedFiles[i].filename == response.filename) {
							isExists = true;
							break;
						}
					}
					if (!isExists) {
						arrUploadedFiles.push({
							filename: response.filename,
							file_size: response.file_size,
							file_url: response.file_url,
							thumb_url: response.thumb_url
						});
					} else {
						alert('File is already exists');
						file.previewElement.remove();
					}

				}
			},
			removedfile: function(file) {
				var name = file.name;
				// alert(name);
				$.ajax({
					type: 'POST',
					url: '<?php echo base_url(); ?>repository/delete_file/<?php echo $REPOSITORY_NAME; ?>',

					data: {
						filename: name,
						pk_id: '0',
						session_file_hash: '<?php echo $session_file_hash ?? ''; ?>',
					},
					success: function(json) {
						response = $.parseJSON(json);
						if (response.status == 0) {
							for (var i in arrUploadedFiles) {
								if (arrUploadedFiles[i].filename == response.filename) {
									arrUploadedFiles.splice(i, 1);
									break;
								}
							}
						}
					}
				});
				file.previewElement.remove();
			}
		});



	});
</script>