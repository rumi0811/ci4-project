<!--  -->
<?= view('includes/layout_breadcrumb') ?>
<?php echo $grid; ?>


<!-- Modal Add Panel-->
<div class="modal fade" id="modal_panel_edit_popup" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					<i class="fal fa-edit"></i> Form Input: <span class="fw-300"><i><?php echo ucwords(str_replace('_', ' ', $controllerName)); ?></i></span>
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true"><i class="fal fa-times"></i></span>
				</button>
			</div>
			<?php echo $form; ?>
			<div id="modalLoadingInfoEditPopup" style="position: absolute; top: 0; left: 0; display: none; width: 100%; height: 100%; background: #fff; opacity: 0.6; z-index: 9999; text-align: center">
				<img src="<?php echo base_url(); ?>assets/img/ajax-loader.gif" style="position: relative; top: 50%; margin-top: -50px" />
			</div>
		</div>
	</div>
</div>

<?= view('includes/layout_ordering_data') ?>

<?= view('general/upload_popup') ?>

<form name="formDummy" id="formDummy" target="_blank" method="get">
	<input type="hidden" name="q" id="mapQ" />
</form>

<script type="text/javascript">
	var defaultValues = {};
	$(document).ready(function() {
		//get default form value, and save to memory variable
		<?php
		$isHasFile = false;
		foreach ($fieldStructure as $key => $val) {
			if ($val == 'boolean') {
				// echo '			$("#'.$key.'_'.$formName.'_1").prop("checked", false);';
				echo '			defaultValues.' . $key . ' = $("#' . $formName . ' [name=\'' . $key . '\']").attr("checked");';
			} else if ($val == 'file') {
			} else {
				echo '			defaultValues.' . $key . ' = $("#' . $formName . ' [name=\'' . $key . '\']").val();';
			}
			echo "\n";
		}
		?>

		if ($('#modal_dialog_class_form1').length == 0) {
			$('#modal_panel_edit_popup div.modal-dialog').addClass('modal-lg');
		} else {
			$('#modal_panel_edit_popup div.modal-dialog').addClass($('#modal_dialog_class_form1').val());
		}

		$("#btnSave_form1").click(function(e) {
			SaveConfirmationFormAndSubmit(e, $('#form1'), 'Save', "Do you want to save this data?", null, true);
		});


		// Event listener untuk ProductItem button Save
		$("#btnSaveProduct_form1").on("click", function(e) {
			if (typeof tableDt_DataGrid1 !== 'undefined' && typeof tableDt_DataGrid2 !== 'undefined') {
				var dataGrid1 = tableDt_DataGrid1.rows().data();
				var dataGrid2 = tableDt_DataGrid2.rows().data();
				var data1 = [];
				var data2 = [];
				for (var i = 0; i < dataGrid1.length; i++) {
					delete dataGrid1[i].action;
					data1.push(dataGrid1[i]);
				}
				for (var i = 0; i < dataGrid2.length; i++) {
					delete dataGrid2[i].action;
					data2.push(dataGrid2[i]);
				}
				$('#data_kit_json_form1').val(JSON.stringify(data1));
				$('#data_ingredient_json_form1').val(JSON.stringify(data2));
			}
			SaveConfirmationFormAndSubmit(e, $('#form1'), 'Save', "Do you want to save this data?", null, true);
		});





		$('.delete_file_link').click(function(e) {
			elem = $(this).closest('div.input-group').next().children("input[type='file']");
			Swal.fire({
				title: "Delete",
				text: "Do you want to delete this file?",
				icon: "question",
				showCancelButton: true,
				confirmButtonText: "Yes"
			}).then(function(result) {
				if (result.value) {
					$("#modalLoadingInfoEditPopup").css("display", "block");
					var url = "<?php echo base_url() . $controllerName; ?>/delete_file";
					var params = $('#<?php echo $formName; ?>').serialize();
					params += '&deleted_file_field=' + elem.attr('name');
					var jqxhr = $.post(url, params, function(obj) {
							var data = $.parseJSON(obj);
							if (typeof(data.error_message) != 'undefined') {
								toastr['error'](data.error_message);
							} else {
								toastr['success'](data.message);
								renderInputTypeFile(elem, '');
							}
						})
						.done(function() {})
						.fail(function() {})
						.always(function() {
							$("#modalLoadingInfoEditPopup").css("display", "none");
						});
				}
			});
			e.preventDefault();
		});
	});

	var editForm = function(id) {
		$("#modal_panel_edit_popup").modal();
		if (id == 0) {
			<?php
			$isHasFile = false;
			foreach ($fieldStructure as $key => $val) {
				if ($val == 'boolean') {
					// echo '			$("#'.$key.'_'.$formName.'_1").prop("checked", false);';
					echo '			$("#' . $formName . ' [name=\'' . $key . '\']").prop("checked", defaultValues.' . $key . ');';
				} else if ($val == 'file') {
					$isHasFile = true;
					echo '			renderInputTypeFile($("#' . $formName . ' [name=\'' . $key . '\']"), "");';
					//echo '			$("#'.$key.'_'.$formName.'").val("");';
					//echo '			$("#'.$formName.' [name=\''.$key.'\']:not(input[type=\'file\'])").val("");';
				} else if ($key == 'cashier_type_id') {
					echo '					$("input[name=\'cashier_type_id\']").removeAttr(\'checked\');';
				} else {
					//echo '			$("#'.$key.'_'.$formName.'").val("");';
					//echo '			$("#'.$formName.' [name=\''.$key.'\']:not(input[type=\'file\'])").val("");';
					echo '			$("#' . $formName . ' [name=\'' . $key . '\']").val("");';
				}
				echo "\n";
			}
			echo '			$("#' . $formName . ' .select2").trigger("change");';
			echo "\n";
			?>

			//$("#form1 .summernote").summernote('code', '');

			$('#form1').removeClass('was-validated');
		} else {
			$("#modalLoadingInfoEditPopup").css("display", "block");
			url = "<?php echo base_url() . $controllerName; ?>/edit/" + id;
			var jqxhr = $.get(url, function(data) {
					//var obj = $.parseJSON(data);
					var obj = (typeof data === 'string') ? $.parseJSON(data) : data;
					if (typeof(obj.error_message) != 'undefined') {
						toastr['error'](obj.error_message);
						$("#modal_panel_edit_popup").modal('hide');
					} else {
						<?php
						foreach ($fieldStructure as $key => $val) {
							if ($val == 'boolean') {
								// echo '				$("#'.$key.'_'.$formName.'_1").prop("checked", (obj.'.$key.' == 1));';
								echo '					$("#' . $formName . ' [name=\'' . $key . '\']").prop("checked", (obj.' . $key . ' == 1));';
							} else if ($val == 'file') {
								echo '			renderInputTypeFile($("#' . $formName . ' [name=\'' . $key . '\']"), obj.' . $key . ');';
								//echo '				$("#'.$key.'_'.$formName.'").val(obj.'.$key.');';
								// echo '					$("#'.$formName.' [name=\''.$key.'\']:not(input[type=\'file\'])").val(obj.'.$key.');';
							} else if ($val == 'html') {
								echo '					$("#' . $formName . ' [name=\'' . $key . '\']").val(obj.' . $key . ');';
								echo '					$("#' . $formName . ' .summernote").summernote("code", obj.' . $key . ');';
							} else if ($key == 'cashier_type_id') {
								echo '					$("#' . $formName . ' input[name=\'' . $key . '\'][value=\'" + obj.' . $key . ' + "\']").prop(\'checked\', true);';
							} else if ($key == 'cashier_password') {
								echo '					$("#' . $formName . ' input[name=\'' . $key . '\']").val(\'\');';
							} else {
								//echo '				$("#'.$key.'_'.$formName.'").val(obj.'.$key.');';
								echo '					$("#' . $formName . ' [name=\'' . $key . '\']").val(obj.' . $key . ');';
							}
							echo "\n";
						}
						echo '					$("#' . $formName . ' .select2").trigger("change");';
						echo "\n";
						?>
						// ========================================
						// SEKAR FIX 2026-01-31: Populate sale_prices
						// ========================================
						if (obj.sale_prices && obj.sale_prices.length > 0) {
							console.log('🔥 SEKAR: Populating sale_prices', obj.sale_prices);
							for (var i = 0; i < obj.sale_prices.length; i++) {
								var sp = obj.sale_prices[i];
								var fieldId = 'sale_price_' + sp.sale_type_id + '_form1';
								console.log('🔥 SEKAR: Setting', fieldId, '=', sp.sale_price);
								$('#' + fieldId).val(sp.sale_price);
							}
						}

						$('#form1').addClass('was-validated');
					}
				})
				.fail(function() {

				})
				.always(function() {
					$("#modalLoadingInfoEditPopup").css("display", "none");
				});
		}
	}


	var renderInputTypeFile = function(elem, url) {
		// console.log(elem);
		var dataIndex = elem.attr('name');
		if (url != null && url != '') {
			$(elem).closest('div.input-group').prev().children(".delete_file_link").css('display', 'block');
			$('#imageLogoContent_' + dataIndex).css('display', 'block');
			$('#imageLogoContent_' + dataIndex).html('<a href="' + url + '" target="_blank"><img src="' + url + '" class="image-responsive" style="width: 100px" /></a>');
			elem.attr('original-required', elem.attr('required'));
			elem.removeAttr('required');
			elem.css('display', 'none');
		} else {
			$(elem).closest('div.input-group').prev().children(".delete_file_link").css('display', 'none');
			// $('.delete_file_link').css('display', 'none');
			$('#imageLogoContent_' + dataIndex).css('display', 'none');
			if (elem.attr('original-required')) {
				elem.attr('required', elem.attr('original-required'));
			}
			elem.css('display', 'block');
		}
	};

	// var deleteConfirm = function(e, id)
	// {
	// 	DeleteConfirmation(
	// 		function() {
	// 			$("#group_id_delete").val(id);
	// 			$("#form_delete").submit();
	// 		}
	// 	);
	// };

	var controllerName = '<?php echo $REPOSITORY_NAME; ?>';
	var uploadFileCSVXLS = function() {
		Dropzone.forElement('#form-upload-data-dropzone').removeAllFiles(true);
		$("#modal_panel_upload_data").modal();
	}
</script>
<?php echo $extra_coding; ?>