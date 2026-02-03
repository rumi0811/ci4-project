<?php echo view('includes/layout_breadcrumb'); ?>
<div class="row">
	<div class="col-xl-12">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>
					<i class="subheader-icon fal fa-edit"></i> Set Member Group: <span class="fw-300"><i><?php echo $record['group_code']; ?> - <?php echo $record['group_name']; ?></i></span>
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
								<div class="col-md-12 mb-3">
									<?php
									echo smart_form_dropdown('user_id', array(), '', '', 'User', '', '', 'required');
									?>
								</div>
							</div>
						</div>
						<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row">
							<div>
								<button type="button" id="btnSaveDummy" class="btn btn-primary">
									<i class="fal fa-save"></i> Add New User
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
	</div>
</div>


<div class="row">
	<div class="col-xl-12">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>
					<i class="subheader-icon fal fa-table"></i> Datatable: <span class="fw-300"><i>Member List</i></span>
				</h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<table id="DataGrid1" class="table table-striped table-bordered table-hover" width="100%">
						<thead>
							<tr>
								<th>Action</th>
								<th>Username</th>
								<th>Nama</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td colspan="3">No user yet</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<form id="form_delete" method="post">
	<input type="hidden" name="user_id_delete" id="user_id_delete" value="0" />
	<input type="hidden" name="group_id_delete" id="group_id_delete" value="<?php echo $id; ?>" />
</form>

<form id="form-cancel" method="post">
	<input type="hidden" name="http_referer" value="<?php echo $http_referer; ?>" />
	<input type="hidden" name="btnCancel" value="1" />
</form>

<script type="text/javascript">
	var oTable;
	$(document).ready(function() {

		$('#user_id').select2({
			minimumInputLength: 3,
			ajax: {
				delay: 250,
				url: "<?php echo base_url(); ?>user/getdata_user_all",
				dataType: 'json',
				data: function(params) {
					var query = {
						q: params.term,
						page: params.page || 1
					}
					return query;
				},
				processResults: function(data) {
					return {
						results: data
					};
				}
			},
			initSelection: function(element, callback) {
				var data = {
					id: "0",
					text: "Pilih Mitra"
				};
				callback(data);
			}
		});

		$("#btnCancelDummy").click(function(e) {
			$("#form-cancel").submit();
			e.preventDefault();
		});

		$("#btnSaveDummy").click(function(e) {
			SaveConfirmationAndSubmit(e);
		});




		oTable = $('#DataGrid1').dataTable({
			"columnDefs": [{
					"searchable": false,
					"targets": 0
				},
				{
					"searchable": false,
					"targets": 1
				}
			],
			"aoColumns": [{
					"mData": "action",
					"sWidth": "35px",
					"bSortable": false,
					"sClass": "center"
				},
				//{ "mData": "user_id", "sWidth": "50px"},
				{
					"mData": "username",
					"sWidth": "200px",
					"sClass": "nowrap"
				},
				{
					"mData": "name"
				}
			],
			"aaSorting": [
				[1, "asc"]
			],
			ajaxSource: '<?php echo base_url(); ?>group/member_list/<?php echo $id; ?>',
			fnServerData: function(sSource, aoData, fnCallback) {
				//var searchFilter = "";
				//aoData.push( { "name": "searchFilter", "value": searchFilter } );
				$.ajax({
					'dataType': 'json',
					'type': 'POST',
					'url': sSource,
					'data': aoData,
					'success': fnCallback
				});
			},
		});


	});

	var deleteConfirm = function(e, id) {
		DeleteConfirmation(e, function() {
			$("#user_id_delete").val(id);
			$("#form_delete").submit();
		});
	};
</script>