<?php echo view('includes/layout_breadcrumb'); ?>
<div class="row">
	<div class="col-xl-12">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>
					<i class="subheader-icon fal fa-table"></i> Datatable: <span class="fw-300"><i><?php echo $currentPage['menu_name']; ?></i></span>
				</h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<?php echo view('includes/layout_message'); ?>

					<table id="DataGrid1" class="table table-striped table-bordered table-hover smart-form" width="100%">
						<thead>
							<tr>
								<th>Action</th>
								<th>Name</th>
								<th>Breadcrumb</th>
								<th>Order No</th>
								<th>Menu Level</th>
								<th>Function</th>
								<th>Visible?</th>
							</tr>
						</thead>
					</table>
					<div class="dt-toolbar-footer">
						<div class="col-sm-12">
							<button onClick="addData();" class="btn btn-success"><i class="fal fa-plus"></i> Add Menu</button>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>


<?php echo view('menu/edit_popup'); ?>

<form id="form_delete" method="post">
	<input type="hidden" name="menu_id_delete" id="menu_id_delete" value="0" />
</form>



<script type="text/javascript">
	$(document).ready(function() {
		var oTable = $('#DataGrid1').dataTable({
			columnDefs: [{
					sortable: false,
					class: "text-center",
					width: "100px",
					targets: 0
				},
				{
					searchable: false,
					targets: [0, 3, 4, 6]
				},
			],
			columns: [{
					data: "action"
				},
				{
					data: "menu_name",
					class: "nowrap",
					width: "150px"
				},
				{
					data: "note"
				},
				{
					data: "sequence_no",
					width: "60px",
					class: "text-center"
				},
				{
					data: "menu_level",
					width: "70px",
					class: "text-center"
				},
				{
					data: "file_name",
					class: "nowrap",
					width: "150px"
				},
				{
					data: "is_visible",
					class: "text-center",
					width: "90px",
					render: function(data, type, full) {
						return printBoolean(data);
					}
				}
			],
			serverSide: true,
			ajaxSource: '<?php echo base_url(); ?>menu/datatable',
			paginate: false,
			fnServerData: function(sSource, aoData, fnCallback) {
				aoData.push({
					"name": "iDisplayLength",
					"value": -1
				});
				$.ajax({
					'dataType': 'json',
					'type': 'POST',
					'url': sSource,
					'data': aoData,
					'success': fnCallback
				});
			},
		});

		$("#icon_file").select2({
			allowHtml: true,
			width: "100%",
			templateResult: formatIconFile,
			templateSelection: formatIconFile,
			escapeMarkup: function(m) {
				return m;
			}
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
	}

	var deleteConfirm = function(e, id) {
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
			if (result.value === true || result.isConfirmed === true) {
				$("#menu_id_delete").val(id);
				$("#form_delete").submit();
			}
		});
		e.preventDefault();
	};

	var addData = function() {
		$("#menu_id").val("0");
		$("#menu_name").val("");
		$("#page_name").val("");
		$("#note").val("");
		$("#is_visible").prop("checked", true);
		$("#is_new_flag").prop("checked", false);
		$("#file_name").val("");
		$("#parent_menu_id").val(0);
		$("#icon_file").val("");

		$("#modal_panel").modal();
	}

	function printBoolean(data, type, full) {
		if (data == 1) return "Yes";
		else if (data == 0) return "No";
		else return "";
	}
</script>