<?php echo view('includes/layout_breadcrumb'); ?>
<?php echo view('includes/layout_message'); ?>

<div class="row">
	<div class="col-xl-12">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>
					My <span class="fw-300"><i>Profile</i></span>
				</h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<form class="was-validated" id="form-input" novalidate="novalidate" method="post">
						<input type="hidden" name="http_referer" value="<?php echo $http_referer; ?>" />
						<input type="hidden" name="btnSave" id="btnSave" value="1" />
						<div class="panel-content">
							<div class="row">
								<div class="col-md-3">
									<div class="form-row">
										<div class="col-xl-12 mb-3">
											<label class="label">Foto Profil</label>
											<div id="div_profile_picture">
												<a href="<?php echo $record['profile_picture']; ?>" target="_blank"><img src="<?php echo $record['profile_picture']; ?>" class="image-responsive" style="max-width: 250px!important" /></a>
											</div>
										</div>
										<div class="col-xl-12 mb-3">
											<div class="form-group">
												<label class="form-label">Upload Foto Baru (jpg, png, gif)</label>
												<div class="custom-file">
													<input type="file" class="custom-file-input" id="profile_picture" name="profile_picture" onchange="uploadFile(this, <?php echo $record['user_id']; ?>);" />
													<label class="custom-file-label" for="customControlValidation7">Choose file...</label>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-9">
									<div class="form-row">
										<div class="col-xl-6 mb-3">
											<?php echo smart_form_label('username', $record['username'], null, 'Username'); ?>
										</div>
									</div>
									<div class="form-row">
										<div class="col-xl-6 mb-3">
											<?php echo smart_form_input('name', $record['name'], '', 'Full name', '', '', 'required'); ?>
										</div>
										<div class="col-xl-6 mb-3">
											<?php echo smart_form_input('mobile', isset($record['mobile']) ? $record['mobile'] : '', '', 'Mobile Number', '', '', 'required'); ?>
										</div>
									</div>
									<div class="form-row">
										<div class="col-xl-6 mb-3">
											<?php echo smart_form_textarea('address', isset($record['address']) ? $record['address'] : '', 'rows="3"', 'Address', '', '', 'required'); ?>
										</div>
									</div>
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

		//pageSetUp();

		// var form_input = $("#form-input").validate({

		// 			// Rules for form validation
		// 			rules : {
		// 				name : {
		// 					required : true
		// 				},
		// 				mobile : {
		// 					required : true
		// 				},
		// 				address : {
		// 					required : true
		// 				}
		// 			},

		// 			// Messages for form validation
		// 			messages : {
		// 				name : {
		// 					required : 'Please enter full name'
		// 				},
		// 				mobile : {
		// 					required : 'Please enter mobile number'
		// 				},
		// 				address : {
		// 					required : 'Please enter address'
		// 				}
		// 			},


		// 			// Do not change code below
		// 			errorPlacement : function(error, element) {
		// 				error.insertAfter(element.parent());
		// 			},

		// 	        invalidHandler: function(form, validator) {
		// 	            if (!validator.numberOfInvalids())
		// 	                return;
		// 	            $('html, body').animate({
		// 	                scrollTop: $(validator.errorList[0].element).offset().top - 100
		// 	            }, 500);
		// 	        }

		// });

		$("#btnCancelDummy").click(function(e) {
			$("#btnCancel").val("1");
			$("#form-cancel").submit();
			e.preventDefault();
		});

		$("#btnSaveDummy").click(function(e) {
			var form = $('#form-input');
			form.addClass('was-validated');
			if (form[0].checkValidity() === false) {
				e.preventDefault();
				e.stopPropagation();
				return;
			}

			Swal.fire({
				title: "Save",
				text: 'Save this data?',
				icon: "question",
				showCancelButton: true,
				confirmButtonText: "Yes"
			}).then(function(result) {
				if (result.value) {
					$("#btnSave").val("1");
					$("#form-input").submit();
				}
			});

			e.preventDefault();

		});

	});




	var uploadFile = function(obj, user_id) {
		var docName = obj.name;
		var fileObj = $(obj);
		var size = fileObj[0].files[0].size;
		var imgname = obj.value;
		obj.parentNode.nextSibling.value = obj.value;

		data = new FormData();
		data.append('photo', fileObj[0].files[0]);
		data.append('doc_type', 'profile_picture');
		data.append('user_id', user_id);
		data.append('hash', '<?php echo md5("photos|||" . $record['user_id']); ?>');

		var ext = imgname.substr((imgname.lastIndexOf('.') + 1));
		if (ext == 'jpg' || ext == 'jpeg' || ext == 'png' || ext == 'gif' || ext == 'PNG' || ext == 'JPG' || ext == 'JPEG') {
			if (size <= 640 * 1024) {
				$.ajax({
						url: "<?php echo base_url() ?>user/upload_doc",
						type: "POST",
						data: data,
						enctype: 'multipart/form-data',
						processData: false, // tell jQuery not to process the data
						contentType: false // tell jQuery not to set contentType
					})
					.done(function(data) {
						if (data.indexOf("SUKSES") >= 0) {
							var arrData = data.split("|||");
							var fileName = arrData[1];
							obj.parentNode.nextSibling.value = '';
							fileObj.val('');
							$("#div_" + docName).html('<a href="' + fileName + '" target="_blank"><img src="' + fileName + '" class="image-responsive" style="max-width: 250px!important" /></a>');
						} else {
							alert(data);
						}

					});
				return false;
			} //end size
			else {
				alert('Ukuran file maksimum 640 KB');
			}
		} //end FILETYPE
		else {
			alert('Anda hanya dapat mengupload file dengan ekstensi JPEG|JPG|PNG|GIF.');
		}

	};
</script>