<?php echo view('includes/layout_breadcrumb'); ?>
<div class="row">
	<div class="col-xl-12">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>
					<i class="subheader-icon fal fa-table"></i> Datatable: <span class="fw-300"><i>Privilege Function List</i></span>
				</h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<?php echo view('includes/layout_message'); ?>

					<form method="post" id="formData" name="formData" action="<?php echo base_url() ?>group/save_privilege">
						<input type="hidden" name="group_id" value="<?php echo $group_id; ?>" />
						<div class="widget-body no-padding">
							<div class="dt-toolbar mb-3">
								<div class="col-xs-12">
									<button type="submit" name="submit" class="btn btn-primary" onClick="javascript:return saveData()">
										<i class="fal fa-save"></i> Save
									</button>
									<button type="button" class="btn btn-secondary" onclick="location.href='<?php echo base_url() ?>group';">
										<i class="fal fa-arrow-left"></i> Back
									</button>
								</div>
							</div>

							<?php echo $list; ?>

							<div class="dt-toolbar-footer">
								<div class="col-sm-12">
									<button type="submit" name="submit" class="btn btn-primary" onClick="javascript:return saveData()">
										<i class="fal fa-save"></i> Save
									</button>
									<button type="button" class="btn btn-secondary" onclick="location.href='<?php echo base_url() ?>group';">
										<i class="fal fa-arrow-left"></i> Back
									</button>
								</div>
							</div>

						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>



<script type="text/javascript">
	var oTable;
	$(document).ready(function() {

		oTable = $('#DataGrid1').dataTable({
			"aoColumnDefs": [{
				"sClass": "text-center",
				"aTargets": [0, 1, 2, 3]
			}],
			processing: true,
			serverSide: false,
			"bAutoWidth": true,
			sort: false,
			paginate: false,
			displayLength: -1,
			dom: "<'dt-toolbar'<'col-xs-12'f>>rt",
		});

	});


	function checkPriv(obj) {

		var isMainParent;
		var strName;
		var arrName;
		if (obj != null) {
			arrName = obj.id.split("_"); // mengambil
			var currObj;
			var preNameTested;
			//untuk check all anak(child)nya
			for (i = 0; i < document.formData.elements.length; i++) {
				currObj = document.formData.elements[i];

				if (currObj.id.length < obj.id.length) continue;
				preNameTested = currObj.id.substring(0, obj.id.length);

				if ((currObj.type == 'checkbox') && (preNameTested == obj.id) && (currObj.id != obj.id)) {
					if (currObj.id.split("_").length > arrName.length) currObj.checked = obj.checked;
				}

			}
			//sekarang cek untuk checkbox parentnya
			if (!isMainParent) {
				var level = arrName.length - 1;
				var currName;
				var parentName = obj.id;

				for (j = level; j > 1; j--) {
					currName = parentName;
					parentName = arrName[0];
					for (i = 1; i < j; i++) parentName = parentName + '_' + arrName[i];

					if (checkParent(currName, parentName)) {
						document.formData.elements[parentName].checked = obj.checked;
					} else {
						document.formData.elements[parentName].checked = true; //false;
					}
					//bikin checkbox unstate karena salah satu anak tidak terpilih
				}
			}
		}
	}

	function checkParent(currName, parentName) {
		//e.g misal name adalah chkIDview_1_1
		//     maka parentName adalah chkIDview_1
		var arrName = currName.split("_");
		var arrTest = parentName.split("_");
		var level = arrTest.length;
		//kemudian cari friend / tetangga yang satu level
		var countFriend = 0;
		var countFriendChecked = 0;
		for (i = 0; i < document.formData.elements.length; i++) {
			currObj = document.formData.elements[i];
			if (currObj.id.length < parentName.length) continue;
			preNameTested = currObj.id.substring(0, parentName.length);
			if ((currObj.type == 'checkbox') && (parentName == preNameTested) && (currObj.id != parentName)) {
				arrTested = currObj.id.split("_");
				if ((arrTested.length > level) && (arrTested.length = arrName.length)) {
					countFriend++;
					if (currObj.checked) countFriendChecked++;
				}
			}
		}
		return ((countFriend == countFriendChecked) || (countFriendChecked == 0))
	}

	var saveData = function() {
		var nNodes = oTable.fnGetHiddenNodes();
		$('td', nNodes).each(function(index, ncolumn) {

			var objName = $("input", ncolumn).attr("name");
			if (typeof(objName) != 'undefined') {
				var chk = $("input", ncolumn).prop('checked');
				if (chk) {
					var nHidden = document.createElement('input');
					nHidden.type = 'hidden';
					nHidden.name = objName;
					nHidden.value = $("input", ncolumn).val();
					$("#formData").append(nHidden);
				}
			}
		});
		return true;
	}
</script>