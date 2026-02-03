<?php echo view('includes/layout_breadcrumb'); ?>
<?php echo $grid; ?>


<!-- Modal Add Panel-->
<div class="modal fade" id="modal_panel" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document" id="modalPanelContent">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					<i class="fal fa-edit"></i> Form Input: <span class="fw-300"><i>Group</i></span>
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true"><i class="fal fa-times"></i></span>
				</button>
			</div>
			<div class="modal-body no-padding">
				<!-- START ROW -->
				<div class="row">
					<!-- NEW COL START -->
					<div class="col-xl-12">
						<div class="panel-content">
							<?php echo $form; ?>
						</div>
					</div>
					<!-- END COL -->
				</div>
				<!-- END ROW -->
			</div>
			<?php echo view('includes/layout_modal_progress'); ?>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<form name="formDummy" id="formDummy" target="_blank" method="get">
</form>

<script type="text/javascript">
	$(document).ready(function() {


		$("#btnSave_form1").click(function(e) {
			// Set submitButton value manually
			$('input[name=submitButton_form1]').val('btnSave_form1');

			SaveConfirmationFormAndSubmit(e, $('#form1'), 'Save', "Do you want to save this data?", null, true);
		});

	});


	var printGroupType = function(data) {
		if (data == 0) return "Internal";
		else if (data == 1) return "External";
		else return data;
	};

	var printActionMembers = function(group_id, type, row) {
		return '<a class="btn btn-xs btn-success text-white" onClick="javascript:doOpenMember(' + group_id + ')"><i class="fal fa-list"></i> Set User (' + row.total_user + ')</a>';
	};


	var doOpenPrivilege = function(id) {
		var f = $('#formDummy');
		f.attr('action', '<?php echo base_url(); ?>group/privilege/' + id);
		f.submit();
	};

	var doOpenMember = function(id) {
		var f = $('#formDummy');
		f.attr('action', '<?php echo base_url(); ?>group/member/' + id);
		f.submit();
	};


	var editForm = function(id) {
		$("#modal_panel").modal();
		if (id == 0) {
			<?php
			foreach ($fieldStructure as $key => $val) {
				if ($val == 'boolean') {
					echo '				$("#' . $key . '_' . $formName . '_1").prop("checked", false);';
				} else {
					echo '				$("#' . $key . '_' . $formName . '").val("");';
				}
				echo "\n";
			}
			echo '				$("#' . $formName . ' .select2").trigger("change");';
			?>
			$('#form1').removeClass('was-validated');
		} else {
			$("#modalLoadingInfo").css("display", "block");
			url = "<?php echo base_url(); ?>group/edit/" + id;
			var jqxhr = $.get(url, function(data) {
					var obj = $.parseJSON(data);
					if (typeof(obj.error_message) != 'undefined') {
						toastr['error'](obj.error_message);
						$("#modal_panel").modal('hide');
					} else {
						<?php
						foreach ($fieldStructure as $key => $val) {
							if ($val == 'boolean') {
								echo '				$("#' . $key . '_' . $formName . '_1").prop("checked", (obj.' . $key . ' == 1));';
							} else {
								echo '				$("#' . $key . '_' . $formName . '").val(obj.' . $key . ');';
							}
							echo "\n";
						}
						echo '				$("#' . $formName . ' .select2").trigger("change");';
						?>
						$('#form1').addClass('was-validated');
					}
				})
				.fail(function() {

				})
				.always(function() {
					$("#modalLoadingInfo").css("display", "none");
				});
		}
	}

	// var deleteConfirm = function(e, id)
	// {
	// 	DeleteConfirmation(
	// 		function() {
	// 			$("#group_id_delete").val(id);
	// 			$("#form_delete").submit();
	// 		}
	// 	);
	// };

	function printBoolean(data, type, full) {
		if (data == 1) return "Yes";
		else if (data == 0) return "No";
		else return "";
	}
</script>