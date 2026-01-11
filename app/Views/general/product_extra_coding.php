<!-- Modal Add Panel-->
<div class="modal fade" id="modal_panel_kit" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					<i class="fal fa-edit"></i> Form Input: <span class="fw-300"><i>Product Kit</i></span>
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
						<div class="panel-content" id="contentFormKit">
						</div>
					</div>
					<!-- END COL -->
				</div>
				<div id="modalLoadingInfoKit" style="position: absolute; top: 0; left: 0; display: none; width: 100%; height: 100%; background: #fff; opacity: 0.6; z-index: 9999; text-align: center">
					<img src="<?php echo base_url(); ?>assets/img/ajax-loader.gif" style="position: relative; top: 50%; margin-top: -50px" />
				</div>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Modal Add Panel-->
<div class="modal fade" id="modal_panel_ing" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					<i class="fal fa-edit"></i> Form Input: <span class="fw-300"><i>Ingredient</i></span>
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
						<div class="panel-content" id="contentFormIngredient">
						</div>
					</div>
					<!-- END COL -->
				</div>
				<div id="modalLoadingInfoIngredient" style="position: absolute; top: 0; left: 0; display: none; width: 100%; height: 100%; background: #fff; opacity: 0.6; z-index: 9999; text-align: center">
					<img src="<?php echo base_url(); ?>assets/img/ajax-loader.gif" style="position: relative; top: 50%; margin-top: -50px" />
				</div>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<script type="text/javascript">
var tableDt_DataGrid1;
var tableDt_DataGrid2;
$(document).ready(function() {
	$(document).on('change', "#form1 [name='is_all_outlet']", function() {
		if ($(this).is(':checked'))
		{
			$("#form1 [name='outlets[]']").parent().parent().css('visibility', 'hidden');
			//$("#form1 [name='outlets[]']").rules('remove', 'required');
			$("#form1 [name='outlets[]']").removeAttr('required');
		}
		else {
			$("#form1 [name='outlets[]']").parent().parent().css('visibility', 'visible');
			//$("#form1 [name='outlets[]']").rules('add', {required: true});
			$("#form1 [name='outlets[]']").attr('required', 'required');
		}
	});
	$("#form1 [name='is_all_outlet']").trigger('change');

	tableDt_DataGrid1 = $('#DataGridKit').DataTable({
		columnDefs: [
			{ "sortable": false, class: "text-center", width: "90px", "aTargets": [ 0 ] }
		],
		processing: false,
		serverSide: false,
		columns: [
			{ data: "no" },
			{ data: "product_code", width: "120px" },
			{ data: "product_name" },
			{ data: "sale_price", width: "120px" },
			{ data: "action", width: "60px", class: "text-center" }//, render: function (data, type, full) { return printBoolean(data); }  }
		],
		drawCallback: function(settings) {
			if (typeof tableDt_DataGrid1 != "undefined") {
				table_rows = this.fnGetNodes();
				$.each(table_rows, function(index){
					//console.log(index);
					$("td:first", this).html(index+1);
				});
			}
		},
	});

	tableDt_DataGrid2 = $('#DataGridIng').DataTable({
		columnDefs: [
			{ "sortable": false, class: "text-center", width: "90px", "aTargets": [ 0 ] }
		],
		processing: false,
		serverSide: false,
		columns: [
			{ data: "no" },
			{ data: "product_code", width: "120px" },
			{ data: "product_name" },
			{ data: "sale_price", width: "120px" },
			{ data: "qty", width: "120px", class: 'text-right' },
			{ data: "uom_code", width: "80px" },
			{ data: "action", width: "60px", class: "text-center" }//, render: function (data, type, full) { return printBoolean(data); }  }
		],
		drawCallback: function(settings) {
			if (typeof tableDt_DataGrid2 != "undefined") {
				table_rows = this.fnGetNodes();
				$.each(table_rows, function(index){
					//console.log(index);
					$("td:first", this).html(index+1);
				});
			}
		},
	});

	$('#DataGridKit tbody').on( 'click', '.btn-delete', function () {
		tableDt_DataGrid1
			.row( $(this).parents('tr') )
			.remove()
			.draw();
	} );


	$('#DataGridIng tbody').on( 'click', '.btn-delete', function () {
		tableDt_DataGrid2
			.row( $(this).parents('tr') )
			.remove()
			.draw();
	} );

	$('.btn-add-kit').click(function () {
		$('#modal_panel_kit').modal();
		if ($('#contentFormKit').html().length < 30)
		{
			$("#modalLoadingInfoKit").css("display", "block");
			url = "<?php echo base_url(); ?>product_item/form_addon";
			var jqxhr = $.get( url, function(data) {
				$('#contentFormKit').html(data);
				$('#product_id_formKit').select2();
				$('#product_id_formKit').val('');
				$('#product_id_formKit').trigger('change');
			})
			.fail(function() {
				
			})
			.always(function() {
				$("#modalLoadingInfoKit").css("display", "none");
			});
		}
		else {
			$('#product_id_formKit').val('');
			$('#product_id_formKit').trigger('change');
		}
		// tableDt_DataGrid1.row.add([1, "Tiger Nixon", 123,  1, 'PCS', '']).draw(false);
	} );
				
	
	$('.btn-add-ing').click(function () {
		$('#modal_panel_ing').modal();
		if ($('#contentFormIngredient').html().length < 30)
		{
			$("#modalLoadingInfoIngredient").css("display", "block");
			url = "<?php echo base_url(); ?>product_item/form_ingredient";
			var jqxhr = $.get( url, function(data) {
				$('#contentFormIngredient').html(data);
				$('#product_id_formIng').select2();
				$('#product_id_formIng').val('');
				$('#product_id_formIng').trigger('change');
				$('#qty_formIng').val('');
			})
			.fail(function() {
				
			})
			.always(function() {
				$("#modalLoadingInfoIngredient").css("display", "none");
			});
		}
		else {
			$('#product_id_formIng').val('');
			$('#product_id_formIng').trigger('change');
			$('#qty_formIng').val('');
		}
	} );


	$(document).on('click', "#btnAddKit_formKit", function(e)
	{
		var form = $('#formKit');
        if  (form && form.length) {
			//console.log(form);
            form.addClass('was-validated');
            if (form[0].checkValidity() === false)
            {
                e.preventDefault();
                e.stopPropagation();
                return;
            }
			
			var id = $('#product_id_formKit').val();
			var url = '<?php echo base_url(); ?>product_item/edit/' + id;
			var jqxhr = $.post(url, form.serialize(), function (obj) {
				var data = $.parseJSON(obj);

				if (typeof (data.error_message) != 'undefined') {
					toastr['error'](data.error_message);
				}
				else {
					//toastr['success'](data.message);
					$('#modal_panel_kit').modal('hide');
					tableDt_DataGrid1.row.add( {
						"no" : tableDt_DataGrid1.rows().count() + 1,
						"product_id": data.product_id,
						"product_code": data.product_code,
						"product_name": data.product_name,
						"sale_price": data.sale_price,
						"action" : '<button class="btn btn-xs btn-danger btn-delete"><i class="fal fa-trash-alt"></i> Delete</button>',
					} ).draw(false);

				}
			})
			.done(function () {
			})
			.fail(function () {
			})
			.always(function () {
			});
        }
        e.preventDefault();
	});


	$(document).on('click', "#btnAddIng_formIng", function(e)
	{
		var form = $('#formIng');
        if  (form && form.length) {
			//console.log(form);
            form.addClass('was-validated');
            if (form[0].checkValidity() === false)
            {
                e.preventDefault();
                e.stopPropagation();
                return;
            }
			
			var id = $('#product_id_formIng').val();
			var url = '<?php echo base_url(); ?>product_item/edit/' + id;
			var jqxhr = $.post(url, form.serialize(), function (obj) {
				var data = $.parseJSON(obj);

				if (typeof (data.error_message) != 'undefined') {
					toastr['error'](data.error_message);
				}
				else {
					//toastr['success'](data.message);
					$('#modal_panel_ing').modal('hide');
					tableDt_DataGrid2.row.add( {
						"no" : tableDt_DataGrid2.rows().count() + 1,
						"product_id": data.product_id,
						"product_code": data.product_code,
						"product_name": data.product_name,
						"sale_price": data.sale_price,
						"qty": $('#qty_formIng').val(),
						"uom_code": data.uom_code,
						"action" : '<button class="btn btn-xs btn-danger btn-delete"><i class="fal fa-trash-alt"></i> Delete</button>',
					} ).draw(false);

				}
			})
			.done(function () {
			})
			.fail(function () {
			})
			.always(function () {
			});
        }
        e.preventDefault();
	});


	
	$("#btnSaveProduct_form1").click(function(e)
	{
		var dataGrid1 = tableDt_DataGrid1.rows().data();
		var dataGrid2 = tableDt_DataGrid2.rows().data();
		var data1 = [];
		var data2 = [];
		for(var i = 0; i < dataGrid1.length; i++) {
			delete dataGrid1[i].action;
			data1.push(dataGrid1[i]);
		}
		for(var i = 0; i < dataGrid2.length; i++) {
			delete dataGrid2[i].action;
			data2.push(dataGrid2[i]);
		}
		//console.log(data1);
		// console.log(data2);
		// console.log(JSON.stringify(data1));
		// console.log(JSON.stringify(data2));
		$('#data_kit_json_form1').val(JSON.stringify(data1));
		$('#data_ingredient_json_form1').val(JSON.stringify(data2));
		
		SaveConfirmationFormAndSubmit(e, $('#form1'), 'Save', "Do you want to save this data?", null, true);
	});
});



var editFormProduct = function(id)
{
	$("#modal_panel_edit_popup").modal(); 
	$("#myTab1 li:first a").click();
	if (id == 0) {
<?php 
$isHasFile = false;
foreach($fieldStructure as $key => $val) {
	if ($val == 'boolean') {
		// echo '			$("#'.$key.'_'.$formName.'_1").prop("checked", false);';
		echo '			$("#'.$formName.' [name=\''.$key.'\']").prop("checked", defaultValues.'.$key.').trigger("change");';

	}
	else if ($val == 'file') {
		$isHasFile = true;
		echo '			renderInputTypeFile($("#'.$formName.' [name=\''.$key.'\']"), "");';
	}
	else {
		echo '			$("#'.$formName.' [name=\''.$key.'\']").val("");';
	}
	echo "\n";
}				
echo '			$("#'.$formName.' .select2").trigger("change");';
echo "\n";
?>
		$('#form1').removeClass('was-validated');
	}
	else {
		$("#modalLoadingInfoEditPopup").css("display", "block");
		url = "<?php echo base_url().$controllerName; ?>/edit/" + id;
		var jqxhr = $.get( url, function(data) {
			var obj = $.parseJSON( data );
			if (typeof(obj.error_message) != 'undefined')
			{
				toastr['error'](obj.error_message);
				$("#modal_panel_edit_popup").modal('hide'); 
			}
			else
			{
<?php 
foreach($fieldStructure as $key => $val) {
	if ($val == 'boolean') {
		echo '					$("#'.$formName.' [name=\''.$key.'\']").prop("checked", (obj.'.$key.' == 1)).trigger("change");';
	}
	else if ($val == 'file') {
		echo '			renderInputTypeFile($("#'.$formName.' [name=\''.$key.'\']"), obj.'.$key.');';
	}
	else if (stripos($key, 'sale_price_') !== false) {
		$salePriceId = str_replace('sale_price_', '', $key);
		echo '					
		var isFound = false;
		for(var i in obj.sale_prices)
		{
			var sp = obj.sale_prices[i];
			if (sp[\'sale_type_id\'] == '.$salePriceId.') {
				$("#'.$formName.' [name=\''.$key.'\']").val(sp[\'sale_price\']);
				isFound = true;
			}
		}
		if (!isFound)
		{
			$("#'.$formName.' [name=\''.$key.'\']").val(\'\');
		}
		';
	}
	else if ($key == 'outlets') {
		echo '				
		var arrOutlets = new Array();	
		for(var i in obj.outlets)
		{
			var o = obj.outlets[i];
			arrOutlets.push(o[\'outlet_id\']);
		}
		//console.log(arrOutlets);
		$("#outlets_form1").val(arrOutlets);
		';
	}
	else {
		echo '					$("#'.$formName.' [name=\''.$key.'\']").val(obj.'.$key.');';
	}
	echo "\n";
}				
echo '					$("#'.$formName.' .select2").trigger("change");';
echo "\n";
?>
				//$('#DataGridKit tbody').html('');
				tableDt_DataGrid1.clear().draw();
				for(var i in obj.addons) {
					var d = obj.addons[i];
					tableDt_DataGrid1.row.add( {
						"no" : tableDt_DataGrid1.rows().count() + 1,
						"product_id": d.product_id,
						"product_code": d.product_code,
						"product_name": d.product_name,
						"sale_price": d.sale_price,
						"action" : '<button class="btn btn-xs btn-danger btn-delete"><i class="fal fa-trash-alt"></i> Delete</button>',
					} ).draw(false);
				}
				tableDt_DataGrid2.clear().draw();
				for(var i in obj.ingredients) {
					var d = obj.ingredients[i];
					tableDt_DataGrid2.row.add( {
						"no" : tableDt_DataGrid2.rows().count() + 1,
						"product_id": d.product_id,
						"product_code": d.product_code,
						"product_name": d.product_name,
						"sale_price": d.sale_price,
						"qty": d.qty,
						"uom_code": d.uom_code,
						"action" : '<button class="btn btn-xs btn-danger btn-delete"><i class="fal fa-trash-alt"></i> Delete</button>',
					} ).draw(false);
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
};

	

</script>