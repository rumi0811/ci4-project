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
					echo '$("#' . $key . '_' . $formName . '_1").prop("checked", false);' . "\n";
				} else {
					echo '$("#' . $key . '_' . $formName . '").val("");' . "\n";
				}
			}
			echo '$("#' . $formName . ' .select2").trigger("change");' . "\n";
			?>
			$('#form1').removeClass('was-validated');
		} else {
			$("#modalLoadingInfo").css("display", "block");

			url = "<?php echo base_url(); ?>group/edit/" + id;

			$.ajax({
				url: url,
				type: 'GET',
				dataType: 'json',
				success: function(obj) {
					if (typeof(obj.error_message) != 'undefined') {
						toastr['error'](obj.error_message);
						$("#modal_panel").modal('hide');
					} else {
						<?php
						foreach ($fieldStructure as $key => $val) {
							if ($val == 'boolean') {
								echo '$("#' . $key . '_' . $formName . '_1").prop("checked", (obj.' . $key . ' == 1));' . "\n";
							} else {
								echo '$("#' . $key . '_' . $formName . '").val(obj.' . $key . ');' . "\n";
							}
						}
						echo '$("#' . $formName . ' .select2").trigger("change");' . "\n";
						?>
						$('#form1').addClass('was-validated');
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
		}
	}

	function deleteConfirm(fieldName, id) {
		console.log('deleteConfirm called with ID:', id);

		Swal.fire({
			title: 'Are you sure?',
			text: "You want to delete this record?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!',
			cancelButtonText: 'Cancel'
		}).then((result) => {
			console.log('SweetAlert result:', result);
			console.log('result.value:', result.value);
			console.log('result.isConfirmed:', result.isConfirmed);

			// Support both old and new SweetAlert versions
			if (result.value === true || result.isConfirmed === true) {
				console.log('Calling deleteRow with ID:', id);
				deleteRow(id);
			} else {
				console.log('Delete cancelled');
			}
		});
	}

	function deleteRow(id) {
		console.log('deleteRow called with ID:', id);

		$.ajax({
			url: '<?php echo base_url(); ?>group/delete_data',
			type: 'POST',
			dataType: 'json',
			data: {
				id: id
			},
			success: function(response) {
				console.log('Delete SUCCESS:', response);

				// ✅ GANTI: Pakai SweetAlert
				Swal.fire({
					icon: 'success',
					title: 'Deleted!',
					text: 'Data deleted successfully!',
					timer: 1500,
					showConfirmButton: false
				}).then(function() {
					window.location.href = '<?php echo base_url(); ?>group';
				});
			},
			error: function(xhr, status, error) {
				console.error('Delete ERROR:', error);
				console.log('XHR:', xhr);

				// ✅ GANTI: Pakai SweetAlert
				Swal.fire({
					icon: 'error',
					title: 'Error!',
					text: 'Error deleting data: ' + error
				});
			}
		});
	}

	function printBoolean(data, type, full) {
		if (data == 1) return "Yes";
		else if (data == 0) return "No";
		else return "";
	}

	// ✅ OVERRIDE DELETE BUTTON - LANGSUNG TANPA SWEETALERT DULU
	// ✅ OVERRIDE DELETE BUTTON - PAKAI SWEETALERT
	$(document).on('click', '.btn-delete-DataGrid1', function(e) {
		e.preventDefault();
		e.stopPropagation();

		console.log('Delete button clicked!');

		var groupId = null;
		var row = $(this).closest('tr');

		// Cari button Privileges di row yang sama
		var privilegeBtn = row.find('a[onclick*="doOpenPrivilege"]');

		if (privilegeBtn.length > 0) {
			var onclick = privilegeBtn.attr('onclick');
			console.log('Privilege button onclick:', onclick);

			var match = onclick.match(/\d+/);
			if (match) {
				groupId = parseInt(match[0]);
				console.log('ID extracted from Privilege button:', groupId);
			}
		}

		// Fallback: cari dari button Members
		if (!groupId) {
			var memberBtn = row.find('a[onclick*="doOpenMember"]');
			if (memberBtn.length > 0) {
				var onclick = memberBtn.attr('onclick');
				console.log('Member button onclick:', onclick);

				var match = onclick.match(/\d+/);
				if (match) {
					groupId = parseInt(match[0]);
					console.log('ID extracted from Member button:', groupId);
				}
			}
		}

		console.log('Final Group ID:', groupId);

		if (groupId && groupId > 0) {
			// ✅ PAKAI SWEETALERT - KONSISTEN DENGAN STYLE YANG ADA
			Swal.fire({
				title: 'Are you sure?',
				text: "You want to delete this record?",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!',
				cancelButtonText: 'Cancel'
			}).then((result) => {
				console.log('SweetAlert result:', result);

				// Support both old and new SweetAlert versions
				if (result.value === true || result.isConfirmed === true) {
					console.log('Delete confirmed! Calling deleteRow...');
					deleteRow(groupId);
				} else {
					console.log('Delete cancelled');
				}
			});
		} else {
			console.error('Cannot find valid group ID');

			// ✅ PAKAI SWEETALERT UNTUK ERROR MESSAGE
			Swal.fire({
				icon: 'error',
				title: 'Error!',
				text: 'Cannot find Group ID. Check console.'
			});
		}

		return false;
	});
</script>