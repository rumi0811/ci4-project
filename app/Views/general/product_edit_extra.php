							<div class="form-row">
								<div class="col-md-12 mb-3">
									<ul id="myTab1" class="nav nav-tabs">
										<li class="nav-item">
											<a class="nav-link active" href="#s1" data-toggle="tab">Product Add-on</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" href="#s2" data-toggle="tab">Ingredients</a>
										</li>
									</ul>
									<div id="myTabContent1" class="tab-content border border-top-0 p-3">
										<div class="tab-pane fade show active" id="s1" role="tabpanel">
											<div class="row">
												<div class="col-xl-12">
													<div id="panel-DataGridKit" class="panel">
														<div class="panel-hdr">
															<h2>
																<i class="subheader-icon fal fa-table"></i> Datatable: <span class="fw-300"><i>Available add-on</i></span>
															</h2>
														</div>
														<div class="panel-container show">
															<div class="panel-content">
																<table id='DataGridKit' cellspacing=0 width='100%' class='table table-striped table-hover table-bordered dataTable'>
																	<thead>
																		<tr>
																			<th>No</th>
																			<th>Code</th>
																			<th>Addon Name</th>
																			<th>Unit Price</th>
																			<th>Action</th>
																		</tr>
																	</thead>
																	<tbody>
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
														<i class="fal fa-plus"></i> Add Add-on
													</button>
												</div>
											</div>

										</div>
										<div class="tab-pane fade" id="s2" role="tabpanel">
											<section>
												<div class="row">
													<div class="col-xl-12">
														<div id="panel-DataGridIng" class="panel">
															<div class="panel-hdr">
																<h2>
																	<i class="subheader-icon fal fa-table"></i> Datatable: <span class="fw-300"><i>Ingredient</i></span>
																</h2>
															</div>
															<div class="panel-container show">
																<div class="panel-content">
																	<table id='DataGridIng' cellspacing=0 width='100%' class='table table-striped table-hover table-bordered dataTable'>
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