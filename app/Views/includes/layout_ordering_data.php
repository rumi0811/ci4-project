
<!-- Modal Add Panel-->
<div class="modal fade" id="modal_panel_ordering" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					<i class="fal fa-sort"></i> Ordering Data: <span class="fw-300"><i><?php echo ucwords(str_replace('_', ' ', $controllerName)); ?></i></span>
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true"><i class="fal fa-times"></i></span>
				</button>
			</div>
			<div class="modal-body no-padding">
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
	</div>
</div>

<script src="<?php echo base_url(); ?>assets/4.5.1/js/plugin/jquery-nestable/jquery.nestable.min.js"></script>

<script type="text/javascript">
	$(document).ready(function() {	
		var isDataNestableListChanged = false;

		$('#nestable').nestable({
			group: 'categories', 
			maxDepth: 1
		}).on('change', function(e) {
			isDataNestableListChanged = true;
			saveChangedNestableList(e); 
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

	
	var orderingDataLoad = function(url)
	{
		$("#modalPanelOrderingLoadingInfo").css("display", "block");
		$("#modal_panel_ordering").modal(); 
		var jqxhr = $.get( url, function(data) {
			var obj = $.parseJSON( data );
			if (typeof(obj.error_message) != 'undefined')
			{
				toastr['error'](obj.error_message);
				$("#modal_panel_ordering").modal('hide'); 
			}
			else
			{
				var nestableList = $("#nestableList");
				nestableList.html('');
				for(var i in obj) {
					strNode = '<div class="dd3-content">' + obj[i].text;
					if (typeof(obj[i].note) != 'undefined') {
						strNode += '<span class="pull-right"><small>' + obj[i].note + '</small></span>';
					}
					strNode += '</div>';
					$('<li/>')
						.prop('class', "dd-item dd3-item")
						.attr('data-id', obj[i].id)
						.append('<div class="dd-handle dd3-handle">&nbsp;</div>')
						.append(strNode)
						.appendTo(nestableList);
				}
			}
		})
		.fail(function() {
			
		})
		.always(function() {
			$("#modalPanelOrderingLoadingInfo").css("display", "none");
		});
	};


	
	var saveChangedNestableList = function(e) {
		var list = e.length ? e : $(e.target);
		if (window.JSON) {
			$.ajax({
				type: 'POST',
				url: '<?php echo base_url(); ?><?php echo $controllerName; ?>/save_ordering',
				data: 
				{
					data: JSON.stringify(list.nestable('serialize')), 
				},
				success: function(json){
					var data = $.parseJSON(json);
					if (typeof (data.error_message) != 'undefined') {
						toastr['error'](data.error_message);
					}
					else {
						toastr['success'](data.message);
					}
				}
			});
		}
	};

</script>