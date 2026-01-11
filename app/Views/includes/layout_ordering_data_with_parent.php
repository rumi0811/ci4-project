<!-- Modal Add Panel-->
<div class="modal fade" id="modal_panel_ordering" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document" id="modalPanelOrderingContent">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					<i class="fal fa-sort"></i> Ordering Data
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true"><i class="fal fa-times"></i></span>
				</button>
			</div>
			<div class="modal-body">
				<!-- START ROW -->
				<div class="row">
					<!-- NEW COL START -->
					<div class="col-sm-12 col-md-12 col-lg-12">
						<p>Drag handle to order the data:</p>
						<div class="dd" id="nestable">
							<ol class="dd-list" id="nestableList">
							</ol>
						</div>
					</div>
					<!-- END COL -->
				</div>
				<!-- END ROW -->
				<div id="modalPanelOrderingLoadingInfo" style="position: absolute; top: 0; left: 0; display: none; width: 100%; height: 100%; background: #fff; opacity: 0.6; z-index: 9999; text-align: center">
					<img src="<?php echo base_url(); ?>assets/img/ajax-loader.gif" style="position: relative; top: 50%; margin-top: -50px" />
				</div>
			</div>			
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<script src="<?php echo base_url(); ?>assets/4.5.1/js/plugin/jquery-nestable/jquery.nestable.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {	
		var isDataNestableListChanged = false;


		$('#nestable').nestable({
				group: 'categories'
			}).on('change', function(e) {
				isDataNestableListChanged = true;
				updateNestableListOutput(e); 
			});

		$("#modal_panel_ordering").on('hidden.bs.modal', function () {
			if (typeof(reloadGrid) == 'function') {
				if (isDataNestableListChanged) {
					isDataNestableListChanged = false;
					reloadGrid();
				}
			}
		});

	});

	var recurseDataOrdering = function(arrData, parentId, arrResult)
	{
		var arrResult = [];
		for(var id in arrData) {
			var row = arrData[id];
			if (row.parent_id == parentId || (row.parent_id == null && parentId == 0))
			{
				var tempChildren = recurseDataOrdering(arrData, row.id);
				if (tempChildren.length > 0) {
					if (row.children.length == 0)
					{
						row.children = tempChildren;
					}
					else {
						row.children = row.children.concat(tempChildren);
					}
				}
				arrResult.push(row);
			}
		}
		return arrResult;		
	}

	var renderListFromOrderingData = function(arrResult)
	{
		var strResult = "";
		if (arrResult.length > 0) {
			strResult += '<ol class="dd-list">';
			for(var i in arrResult) {
				var row = arrResult[i];
				strResult += '<li class="dd-item dd3-item" data-id="' + row.id + '">';
				strResult += '<div class="dd-handle dd3-handle">&nbsp;</div>';
				strResult += '<div class="dd3-content">' + row.text + '</div>';
				if (row.children.length > 0) {
					strResult += renderListFromOrderingData(row.children);
				}
				strResult += '</li>';
			}
			strResult += '</ol>';
		}
		else {
			strResult = "<h3>There are no data yet</h3>";
		}
		return strResult;
	}

	var orderingDataLoad = function(url)
	{
		$("#modalPanelOrderingLoadingInfo").css("display", "block");
		$("#modal_panel_ordering").modal(); 
		var jqxhr = $.get( url, function(data) {
			var obj = $.parseJSON( data );
			if (typeof(obj.error_message) != 'undefined')
			{
				toastr['error'](obj.error_message);
			}
			else
			{
				var nestable = $("#nestable");
				nestable.html('');
				var arrData = [];
				for(var i in obj) {
					obj[i].has_child = 0;
					obj[i].children = [];
					arrData.push(obj[i]);
				}
				for(var i in obj) {
					if (obj[i].parent_id > 0) {
						for(var j in arrData) {
							if (arrData[j].id == obj[i].parent_id) {
								arrData[j].has_child = 1;
								break;
							}
						}
					}
				}
				arrResult = recurseDataOrdering(arrData, 0);
				var html = renderListFromOrderingData(arrResult);
				nestable.html(html);
			}
		})
		.fail(function() {
			
		})
		.always(function() {
			$("#modalPanelOrderingLoadingInfo").css("display", "none");
		});

	}

</script>