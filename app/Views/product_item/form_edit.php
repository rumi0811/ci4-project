<?= view('includes/layout_breadcrumb') ?>
<div class="row">
	<div class="col-xl-12">
		<div id="panel-1" class="panel">
			<div class="panel-hdr">
				<h2>
					<i class="subheader-icon fal fa-edit"></i> Form Input: <span class="fw-300"><i><?php echo $controllerName ?? 'Form'; ?></i></span>
				</h2>
			</div>
			<div class="panel-container show">
				<div class="panel-content">
					<?= view('includes/layout_message') ?>
					<form id="form-input" class="needs-validation" novalidate="novalidate" method="post">
						<input type="hidden" name="product_id" id="product_id" value="<?php echo $record['product_id'] ?? ''; ?>" />
						<input type="hidden" name="http_referer" value="<?php echo $http_referer ?? ''; ?>" />
						<input type="hidden" name="session_file_hash" value="<?php echo $session_file_hash ?? ''; ?>" />
						<input type="hidden" name="btnSave" value="1" />
						<input type="hidden" id="data_kit_json" name="data_kit_json" value="" />
						<input type="hidden" id="data_ingredient_json" name="data_ingredient_json" value="" />

						<div class="panel-content">
							<div class="form-row">
								<div class="col-md-2 mb-3">
									<div class="form-row">
										<div class="col-xl-12 mb-3">
											<label class="label">Product Photo</label>
											<div id="div_profile_pic">
												<a href="<?php echo $record['picture'] ?? base_url('assets/img/no-image.png'); ?>" target="_blank">
													<img src="<?php echo $record['picture'] ?? base_url('assets/img/no-image.png'); ?>" class="image-responsive" style="width: 90%" />
												</a>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-10 mb-3">
									<div class="form-row">
										<div class="col-md-3 mb-3">
											<?php
											echo smart_form_input('item_barcode', (isset($record['item_barcode'])) ? $record['item_barcode'] : '', 'autocomplete="off"', 'Barcode', '', '', 'required');
											?>
										</div>
										<div class="col-md-6 mb-3">
											<?php
											echo smart_form_input('item_name', (isset($record['item_name'])) ? $record['item_name'] : '', 'autocomplete="off"', 'Product Name', '', '', 'required');
											?>
										</div>

										<div class="col-md-3 mb-3">
											<?php
											echo smart_form_dropdown(
												'id_pos_item_category',
												$pos_item_categories,
												(isset($record['id_pos_item_category'])) ? $record['id_pos_item_category'] : '',
												'style="margin-right: -36px; float: left"',
												'Product Category',
												'',
												'<i style="right: 47px!important"></i><a class="btn btn-warning" style="padding: 6px 12px; float: right" onClick="openProductCategory();"> + </a>',
												'required'
											);
											?>
										</div>
									</div>
									<div class="form-row">
										<div class="col-md-12 mb-3">
											<?php
											echo smart_form_textarea('item_description', (isset($record['item_description'])) ? $record['item_description'] : '', 'rows="3"', 'Description');
											?>
										</div>
									</div>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-2 mb-3">
									<div class="form-group">
										<label class="form-label">Upload Photo Product</label>
										<div class="custom-file">
											<input type="file" class="custom-file-input" id="item_picture_cover" name="item_picture_cover" onchange="uploadFile(this, <?php echo $record['id_pos_item']; ?>);" />
											<label class="custom-file-label" for="customControlValidation7">Choose file...</label>
										</div>
									</div>
								</div>
								<div class="col-md-3 mb-3">
									<?php
									echo smart_form_input('item_sku', (isset($record['item_sku'])) ? $record['item_sku'] : '', '', 'SKU Code');
									?>
								</div>
								<div class="col-md-3 mb-3">
									<?php
									echo smart_form_input('sale_price', (isset($record['sale_price'])) ? $record['sale_price'] : '', '', 'Unit Price', '', '', 'required');
									?>
								</div>
								<div class="col-md-2 mb-3">
									<?php
									echo smart_form_dropdown('id_pos_uom', $pos_uoms, (isset($record['id_pos_uom'])) ? $record['id_pos_uom'] : '', '', 'Unit', '', '', 'required');
									?>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-3 mb-3">
									<?php
									$is_all_outlet_option = [];
									$is_all_outlet_option[1] = 'Available to all outlet';
									$is_all_outlet_option[0] = 'For specific outlet only';
									if ($record['is_all_outlet'] == 0) {
										$cssDisplay = 'block';
									} else {
										$cssDisplay = 'none';
									}
									echo smart_form_dropdown(
										'is_all_outlet',
										$is_all_outlet_option,
										(isset($record['is_all_outlet'])) ? $record['is_all_outlet'] : '',
										'onChange="javascript:doChangeAvailability(this.value)"',
										'Product Availability',
										'',
										'',
										'required'
									);
									?>
								</div>
								<div class="col-md-9 mb-3" id="divOutlets" style="display: <?php echo $cssDisplay; ?>">
									<?php
									echo smart_form_dropdown2(
										'id_pos_outlet_list[]',
										$pos_outlets,
										(isset($record['id_pos_outlet_list'])) ? $record['id_pos_outlet_list'] : '',
										'id="id_pos_outlet_list" multiple="multiple"',
										'Select Outlet Available',
										'',
										'',
										'required'
									);
									?>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-3 mb-3">
									<?php
									echo smart_form_switch('is_active', (isset($record['is_active'])) ? $record['is_active'] : '', '', 'Active');
									?>
								</div>
								<div class="col-md-3 mb-3">
									<?php
									echo smart_form_switch('is_sale', (isset($record['is_sale'])) ? $record['is_sale'] : '', '', 'Product is for Sale');
									?>
								</div>
								<div class="col-md-3 mb-3">
									<?php
									echo smart_form_switch('is_inventory', (isset($record['is_inventory'])) ? $record['is_inventory'] : '', '', 'Maintain Inventory');
									?>
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-12 mb-3">
									<ul id="myTab1" class="nav nav-tabs">
										<li class="nav-item">
											<a class="nav-link active" href="#s1" data-toggle="tab">Product Kits</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#s2" data-toggle="tab">Ingredients</a>
										</li>
									</ul>
									<div id="myTabContent1" class="tab-content border border-top-0 p-3">
										<div class="tab-pane fade show active" id="s1" role="tabpanel">
											<?php //echo $grid_kit; 
											?>
											<div class="row">
												<div class="col-xl-12">
													<div id="panel-DataGrid1" class="panel">
														<div class="panel-hdr">
															<h2>
																<i class="subheader-icon fal fa-table"></i> Datatable: <span class="fw-300"><i>Product Kits</i></span>
															</h2>
														</div>
														<div class="panel-container show">
															<div class="panel-content">
																<table id='DataGrid1' cellspacing=0 width='100%' class='table table-striped table-hover table-bordered dataTable'>
																	<thead>
																		<tr>
																			<th>No</th>
																			<th>Code</th>
																			<th>Kit Name</th>
																			<th>Unit Price</th>
																			<th>Qty</th>
																			<th>Unit</th>
																			<th>Action</th>
																		</tr>
																	</thead>
																	<tbody>
																		<?php
																		if ($detail_kits) {
																			foreach ($detail_kits as $idx => $row) {
																				echo "
				<tr>
					<td>" . ($idx + 1) . "</td>
					<td>" . $row['item_code'] . "</td>
					<td>" . $row['item_name'] . "</td>
					<td>" . $row['sale_price'] . "</td>
					<td>" . $row['qty'] . "</td>
					<td>" . $row['uom_code'] . "</td>
					<td><button class=\"btn btn-xs btn-danger btn-delete\"><i class=\"fal fa-trash-alt\"></i> Delete</button></td>
				</tr>";
																			}
																		}
																		?>
																	</tbody>
																	<tfoot>
																	</tfoot>
																</table>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-12">
													<button type="button" class="btn btn-success btn-add-kit">
														<i class="fal fa-plus"></i> Add Kit
													</button>
												</div>
											</div>

										</div>
										<div class="tab-pane fade" id="s2" role="tabpanel">
											<section>
												<?php //echo $grid_ingredient; 
												?>
												<div class="row">
													<div class="col-xl-12">
														<div id="panel-DataGrid1" class="panel">
															<div class="panel-hdr">
																<h2>
																	<i class="subheader-icon fal fa-table"></i> Datatable: <span class="fw-300"><i>Ingredient</i></span>
																</h2>
															</div>
															<div class="panel-container show">
																<div class="panel-content">
																	<table id='DataGrid2' cellspacing=0 width='100%' class='table table-striped table-hover table-bordered dataTable'>
																		<thead>
																			<tr>
																				<th>No</th>
																				<th>Code</th>
																				<th>Kit Name</th>
																				<th>Unit Price</th>
																				<th>Qty</th>
																				<th>Unit</th>
																				<th>Action</th>
																			</tr>
																		</thead>
																		<tbody>
																			<?php
																			if ($detail_boms) {
																				foreach ($detail_boms as $idx => $row) {
																					echo "
				<tr>
					<td>" . ($idx + 1) . "</td>
					<td>" . $row['item_code'] . "</td>
					<td>" . $row['item_name'] . "</td>
					<td>" . $row['sale_price'] . "</td>
					<td>" . $row['qty'] . "</td>
					<td>" . $row['uom_code'] . "</td>
					<td><button class=\"btn btn-xs btn-danger btn-delete\"><i class=\"fal fa-trash-alt\"></i> Delete</button></td>
				</tr>";
																				}
																			}
																			?>
																		</tbody>
																		<tfoot>
																		</tfoot>
																	</table>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-sm-12">
														<button type="button" class="btn btn-success btn-add-ing">
															<i class="fal fa-plus"></i> Add Ingredient
														</button>
													</div>
												</div>

											</section>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row">
							<div>
								<button type="button" id="btnSaveDummy" class="btn btn-primary">
									<i class="fal fa-save"></i> Save
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


<form id="form-cancel" method="post">
	<input type="hidden" name="http_referer" value="<?php echo $http_referer; ?>" />
	<input type="hidden" name="btnCancel" value="1" />
</form>


<!-- Modal Add Panel-->
<div class="modal fade" id="modal_panel_category" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">
					<i class="fal fa-edit"></i> Add New: <span class="fw-300"><i>Product Category</i></span>
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true"><i class="fal fa-times"></i></span>
				</button>
			</div>
			<div class="modal-body no-padding">
				<!-- START ROW -->
				<div class="row">
					<article class="col-xl-12">
						<form class="was-validated" id="form-input-category" novalidate="novalidate">
							<input type="hidden" name="id_pos_item_category" value="" />
							<input type="hidden" name="table_name" value="item_category" />
							<input type="hidden" name="btnSave" value="1" />
							<input type="hidden" name="is_active" value="1" />

							<div class="panel-content">
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<?php echo smart_form_input('category_code', '', null, 'Category Code', '', '', 'required'); ?>
									</div>
									<div class="col-md-9 mb-3">
										<?php echo smart_form_input('category_name', '', '', 'Category Name', '', '', 'required'); ?>
									</div>
								</div>
							</div>
							<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row">
								<div class="mt-3">
									<button class="btn btn-success" type="button" id="btnSaveDummyCategory"><i class="fal fa-save"></i> Save</button>
									<button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">
										<i class="fal fa-times"></i> Close
									</button>
								</div>
							</div>
						</form>
					</article>
					<!-- END COL -->
				</div>
				<!-- END ROW -->
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->


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
					<article class="col-xl-12">
						<form class="was-validated" id="form-input-kit" novalidate="novalidate">
							<input type="hidden" name="btnSave" value="1" />

							<div class="panel-content">
								<div class="form-row">
									<div class="col-md-12 mb-3">
										<?php echo smart_form_dropdown2('kit_item_code', $arrProductKits, null, '', 'Product Kit', '', '', 'required'); ?>
									</div>
								</div>
								<div class="form-row">
									<div class="col-md-4 mb-3">
										<?php echo smart_form_input_number('kit_qty', '', '', 'Qty', '', '', 'required'); ?>
									</div>
								</div>
							</div>
							<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row">
								<div class="mt-3">
									<button class="btn btn-success" type="button" id="btnSaveDummyKit"><i class="fal fa-save"></i> Save</button>
									<button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">
										<i class="fal fa-times"></i> Close
									</button>
								</div>
							</div>
						</form>
					</article>
					<!-- END COL -->
				</div>
				<!-- END ROW -->
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
					<article class="col-xl-12">
						<form class="was-validated" id="form-input-ing" novalidate="novalidate">
							<input type="hidden" name="btnSave" value="1" />

							<div class="panel-content">
								<div class="form-row">
									<div class="col-md-12 mb-3">
										<?php echo smart_form_dropdown2('ing_item_code', $arrProductIngredients, null, '', 'Product', '', '', 'required'); ?>
									</div>
								</div>
								<div class="form-row">
									<div class="col-md-4 mb-3">
										<?php echo smart_form_input_number('ing_qty', '', '', 'Qty', '', '', 'required'); ?>
									</div>
								</div>
							</div>
							<div class="panel-content border-faded border-left-0 border-right-0 border-bottom-0 d-flex flex-row">
								<div class="mt-3">
									<button class="btn btn-success" type="button" id="btnSaveDummyIng"><i class="fal fa-save"></i> Save</button>
									<button type="button" class="btn btn-danger" data-dismiss="modal" aria-hidden="true">
										<i class="fal fa-times"></i> Close
									</button>
								</div>
							</div>
						</form>
					</article>
					<!-- END COL -->
				</div>
				<!-- END ROW -->
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script type="text/javascript">
	var form_input;
	var tableDt_DataGrid1;
	var tableDt_DataGrid2;
	$(document).ready(function() {
		tableDt_DataGrid1 = $('#DataGrid1').DataTable({
			columnDefs: [{
				"sortable": false,
				class: "text-center",
				width: "90px",
				"aTargets": [0]
			}],
			processing: false,
			serverSide: false,
			columns: [{
					data: "no"
				},
				{
					data: "item_code",
					width: "120px"
				},
				{
					data: "item_name"
				},
				{
					data: "sale_price",
					width: "120px"
				},
				{
					data: "qty",
					width: "120px",
					class: 'text-right'
				},
				{
					data: "uom_code",
					width: "80px"
				},
				{
					data: "action",
					width: "60px",
					class: "text-center"
				} //, render: function (data, type, full) { return printBoolean(data); }  }
			],
			drawCallback: function(settings) {
				if (typeof tableDt_DataGrid1 != "undefined") {
					table_rows = this.fnGetNodes();
					$.each(table_rows, function(index) {
						console.log(index);
						$("td:first", this).html(index + 1);
					});
				}
			},
		});

		tableDt_DataGrid2 = $('#DataGrid2').DataTable({
			columnDefs: [{
				"sortable": false,
				class: "text-center",
				width: "90px",
				"aTargets": [0]
			}],
			processing: false,
			serverSide: false,
			columns: [{
					data: "no"
				},
				{
					data: "item_code",
					width: "120px"
				},
				{
					data: "item_name"
				},
				{
					data: "sale_price",
					width: "120px"
				},
				{
					data: "qty",
					width: "120px",
					class: 'text-right'
				},
				{
					data: "uom_code",
					width: "80px"
				},
				{
					data: "action",
					width: "60px",
					class: "text-center"
				} //, render: function (data, type, full) { return printBoolean(data); }  }
			],
			drawCallback: function(settings) {
				if (typeof tableDt_DataGrid2 != "undefined") {
					table_rows = this.fnGetNodes();
					$.each(table_rows, function(index) {
						console.log(index);
						$("td:first", this).html(index + 1);
					});
				}
			},
		});

		$('#DataGrid1 tbody').on('click', '.btn-delete', function() {
			tableDt_DataGrid1
				.row($(this).parents('tr'))
				.remove()
				.draw();
		});


		$('#DataGrid2 tbody').on('click', '.btn-delete', function() {
			tableDt_DataGrid2
				.row($(this).parents('tr'))
				.remove()
				.draw();
		});

		$('.btn-add-kit').click(function() {
			$('#kit_item_code').val('');
			$('#kit_item_code').trigger('change');
			$('#kit_qty').val('');
			$('#modal_panel_kit').modal();
			// tableDt_DataGrid1.row.add([1, "Tiger Nixon", 123,  1, 'PCS', '']).draw(false);
		});


		$('.btn-add-ing').click(function() {
			$('#ing_item_code').val('');
			$('#ing_item_code').trigger('change');
			$('#ing_qty').val('');
			$('#modal_panel_ing').modal();
			// tableDt_DataGrid1.row.add([1, "Tiger Nixon", 123,  1, 'PCS', '']).draw(false);
		});


		$("#btnCancelDummy").click(function(e) {
			$("#form-cancel").submit();
			e.preventDefault();
		});

		$("#btnSaveDummy").click(function(e) {
			var dataGrid1 = tableDt_DataGrid1.rows().data();
			var dataGrid2 = tableDt_DataGrid2.rows().data();
			var data1 = [];
			var data2 = [];
			for (var i = 0; i < dataGrid1.length; i++) {
				data1.push(dataGrid1[i]);
			}
			for (var i = 0; i < dataGrid2.length; i++) {
				data2.push(dataGrid2[i]);
			}
			console.log(data1);
			console.log(data2);
			console.log(JSON.stringify(data1));
			console.log(JSON.stringify(data2));
			$('#data_kit_json').val(JSON.stringify(data1));
			$('#data_ingredient_json').val(JSON.stringify(data2));
			if ($('#is_all_outlet').val() == 1) {
				$('#id_pos_outlet_list').removeAttr('required');
			} else {
				$('#id_pos_outlet_list').attr('required', 'required');
			}
			SaveConfirmationAndSubmit(e, 'Save', "Do you want to save this data?");
		});

		$("#btnSaveDummyCategory").click(function(e) {
			var form = $('#form-input-category');
			if (form && form.length) {
				form.addClass('was-validated');
				if (form[0].checkValidity() === false) {
					e.preventDefault();
					e.stopPropagation();
					return;
				}
				Swal.fire({
					title: 'Save',
					text: 'Do you want to save this data?',
					icon: "question",
					showCancelButton: true,
					confirmButtonText: "Yes"
				}).then(function(result) {
					if (result.value) {
						var url = '<?php echo base_url(); ?>master_data/save_ajax';
						var jqxhr = $.post(url, form.serialize(), function(obj) {
								var data = $.parseJSON(obj);

								if (typeof(data.error_message) != 'undefined') {
									toastr['error'](data.error_message);
								} else {
									toastr['success'](data.message);
									$('#modal_panel_category').modal('hide');

									if (typeof(top.parent.reloadDataCategory) == 'function') {
										top.parent.reloadDataCategory();
									}
								}
							})
							.done(function() {})
							.fail(function() {})
							.always(function() {});
					}
				});

			}
			e.preventDefault();
		});

		$("#btnSaveDummyKit").click(function(e) {
			var form = $('#form-input-kit');
			if (form && form.length) {
				form.addClass('was-validated');
				if (form[0].checkValidity() === false) {
					e.preventDefault();
					e.stopPropagation();
					return;
				}
				var url = '<?php echo base_url(); ?>product_item/edit_kit';
				var jqxhr = $.post(url, form.serialize(), function(obj) {
						var data = $.parseJSON(obj);

						if (typeof(data.error_message) != 'undefined') {
							toastr['error'](data.error_message);
						} else {
							//toastr['success'](data.message);
							$('#modal_panel_kit').modal('hide');
							tableDt_DataGrid1.row.add({
								"no": tableDt_DataGrid1.rows().count() + 1,
								"item_code": data.item_code,
								"item_name": data.item_name,
								"sale_price": data.sale_price,
								"qty": $('#kit_qty').val(),
								"uom_code": data.uom_code,
								"action": '<button class="btn btn-xs btn-danger btn-delete"><i class="fal fa-trash-alt"></i> Delete</button>',
							}).draw(false);

						}
					})
					.done(function() {})
					.fail(function() {})
					.always(function() {});
			}
			e.preventDefault();
		});

		$("#btnSaveDummyIng").click(function(e) {
			var form = $('#form-input-ing');
			if (form && form.length) {
				form.addClass('was-validated');
				if (form[0].checkValidity() === false) {
					e.preventDefault();
					e.stopPropagation();
					return;
				}
				var url = '<?php echo base_url(); ?>product_item/edit_ingredient';
				var jqxhr = $.post(url, form.serialize(), function(obj) {
						var data = $.parseJSON(obj);

						if (typeof(data.error_message) != 'undefined') {
							toastr['error'](data.error_message);
						} else {
							//toastr['success'](data.message);
							$('#modal_panel_ing').modal('hide');
							tableDt_DataGrid2.row.add({
								"no": tableDt_DataGrid2.rows().count() + 1,
								"item_code": data.item_code,
								"item_name": data.item_name,
								"sale_price": data.sale_price,
								"qty": $('#ing_qty').val(),
								"uom_code": data.uom_code,
								"action": '<button class="btn btn-xs btn-danger btn-delete"><i class="fal fa-trash-alt"></i> Delete</button>',
							}).draw(false);

						}
					})
					.done(function() {})
					.fail(function() {})
					.always(function() {});
			}
			e.preventDefault();
		});
	});

	var doChangeAvailability = function(val) {
		console.log(val);
		if (val == 0) {
			$('#divOutlets').css('display', 'block');
		} else {
			$('#divOutlets').css('display', 'none');
		}
	};

	var reloadDataCategory = function() {
		url = "<?php echo base_url(); ?>product_item/get_product_category";
		var jqxhr = $.get(url, function(obj) {
				var data = $.parseJSON(obj);

				if (typeof(data.error_message) != 'undefined') {
					toastr['error'](data.error_message);
				} else {
					var cbo = $('#id_pos_item_category');
					var lastSelected = cbo.val();
					strHtml = '';
					for (var i in data) {
						strHtml += '<option value="' + data[i].id_pos_item_category + '">' + data[i].category_name + '</option>';
					}
					cbo.html(strHtml);
					cbo.val(lastSelected);
				}
			})
			.done(function() {})
			.fail(function() {})
			.always(function() {});
	}

	var openProductCategory = function() {
		$('#modal_panel_category').modal();
	};


	var uploadFile = function(obj, id_pos_item) {
		if (id_pos_item > 0) {
			var docName = obj.name;
			var fileObj = $(obj);
			var size = fileObj[0].files[0].size;
			var imgname = obj.value;
			obj.parentNode.nextSibling.value = obj.value;

			data = new FormData();
			data.append('photo', fileObj[0].files[0]);
			data.append('doc_type', 'item_picture_cover');
			data.append('id', id_pos_item);
			data.append('hash', '<?php echo md5("photos|||" . $record['id_pos_item']); ?>');

			var ext = imgname.substr((imgname.lastIndexOf('.') + 1));
			if (ext == 'jpg' || ext == 'jpeg' || ext == 'png' || ext == 'gif' || ext == 'PNG' || ext == 'JPG' || ext == 'JPEG') {
				if (size <= 1024 * 1024) {
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
					toastr['error']('Maximum file size is 1 MB');
				}
			} //end FILETYPE
			else {
				toastr['error']('You can only upload photo file (JPEG|JPG|PNG|GIF).');
			}
		}
	};
</script>