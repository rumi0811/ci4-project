<?php echo $datagrid; ?>

<div class="modal fade" id="modal_panel_supplier" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">
          <i class="fal fa-edit"></i> Form Edit: <span class="fw-300"><i>Supplier</i></span>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fal fa-times"></i></span>
        </button>
      </div>
      <div class="modal-body no-padding">
        <!-- START ROW -->
        <div class="row">
          <!-- NEW COL START -->
          <article class="col-sm-12 col-md-12 col-lg-12">
            <?php echo $form; ?>
          </article>
          <!-- END COL -->
        </div>
        <!-- END ROW -->
        <div id="modalPanelSupplierLoadingInfo" style="position: absolute; top: 0; left: 0; display: none; width: 100%; height: 100%; background: #fff; opacity: 0.6; z-index: 9999; text-align: center">
          <img src="<?php echo base_url(); ?>assets/img/ajax-loader.gif" style="position: relative; top: 50%; margin-top: -50px" />
        </div>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


<div class="modal fade" id="pleaseWaitDialog" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h1>Loading...</h1>
			</div>
			<div class="modal-body text-center">
				<img src="<?php echo base_url(); ?>assets/img/ajax-loader.gif" />
			</div>
		</div>
	</div>
</div>

<form id="form1" method="post" action="<?php echo base_url(); ?>supplier/edit">
  <input type="hidden" name="id_pos_supplier" id="idSupplier" />
</form>

<script type="text/javascript">
  $(document).ready(function() {	
		
		$("#btnSave_formSupplier").click(function(e)
		{
			var _title = 'Save';
			var _text = 'Do you want to save this data?';
			var form = $('#formSupplier');
			if  (form && form.length) {
				form.addClass('was-validated');
				if (form[0].checkValidity() === false)
				{
					e.preventDefault();
					e.stopPropagation();
					return;
				}
				
				Swal.fire(
				{
					title: _title,
					text: _text,
					icon: "question",
					showCancelButton: true,
					confirmButtonText: "Yes"
				}).then(function(result)
				{
					if (result.value)
					{
            $("#pleaseWaitDialog").css("display", "block");
            var url = '<?php echo base_url(); ?>supplier/save';
						var jqxhr = $.post( url, $("#formSupplier").serialize(), function(data) {
							var obj = $.parseJSON( data );
							if (typeof(obj.error_message) != 'undefined')
							{
								toastr['error'](obj.error_message);
							}
							else
							{
								toastr['success'](obj.message);
                jQuery('#DataGrid1').DataTable().ajax.reload();
                $("#modal_panel_supplier").modal('hide');
							}
						})
						.fail(function() {
							
						})
						.always(function() {
							$("#pleaseWaitDialog").css("display", "none");
						});
					}
				});
					
			}
			e.preventDefault();
		});
	});  

  var editData = function(idSupplier) {
    if (idSupplier == 0) {
      $("#id_pos_supplier_formSupplier").val("0");
      $("#supplier_code_formSupplier").val("");
      $("#supplier_name_formSupplier").val("");
      $("#address_line1_formSupplier").val("");
      $("#address_line2_formSupplier").val("");
      $("#fax_number_formSupplier").val("");
      $("#phone_number_formSupplier").val("");
      $("#email_formSupplier").val("");
      $("#city_formSupplier").val("");
      $("#province_formSupplier").val("");
      $("#post_code_formSupplier").val("");
      $("#country_formSupplier").val("");
      $("#modal_panel_supplier").modal(); 
    } else {
      $("#modalPanelSupplierLoadingInfo").css("display", "block");
      $("#modal_panel_supplier").modal();
      var url = '<?php echo base_url(); ?>supplier/edit/' + idSupplier;
      var jqxhr = $.get(url, function(data) {
          var obj = $.parseJSON(data);
          if (typeof(obj.error_message) != 'undefined') {
            toastr['error'](obj.error_message);
          } else {
            $("#id_pos_supplier_formSupplier").val(obj.id_pos_supplier);
            $("#supplier_code_formSupplier").val(obj.supplier_code);
            $("#supplier_name_formSupplier").val(obj.supplier_name);
            $("#address_line1_formSupplier").val(obj.address_line1);
            $("#address_line2_formSupplier").val(obj.address_line2);
            $("#fax_number_formSupplier").val(obj.fax_number);
            $("#phone_number_formSupplier").val(obj.phone_number);
            $("#email_formSupplier").val(obj.email);
            $("#city_formSupplier").val(obj.city);
            $("#province_formSupplier").val(obj.province);
            $("#post_code_formSupplier").val(obj.post_code);
            $("#country_formSupplier").val(obj.country);
          }
        })
        .fail(function() {

        })
        .always(function() {
          $("#modalPanelSupplierLoadingInfo").css("display", "none");
        });

    }
  };
</script>