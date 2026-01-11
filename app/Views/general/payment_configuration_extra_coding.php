<script type="text/javascript">
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

});

var editFormCustom = function(id) 
{
		$("#modal_panel_edit_popup").modal(); 		
		if (id == 0) {
			$("#form1 input[type='checkbox']").prop("checked", false);
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
			//echo '			$("#'.$key.'_'.$formName.'").val("");';
			//echo '			$("#'.$formName.' [name=\''.$key.'\']:not(input[type=\'file\'])").val("");';
		}
		else {
			//echo '			$("#'.$key.'_'.$formName.'").val("");';
			//echo '			$("#'.$formName.' [name=\''.$key.'\']:not(input[type=\'file\'])").val("");';
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
					$("#form1 input[type='checkbox']").prop("checked", false);
<?php 
	foreach($fieldStructure as $key => $val) {
		if ($val == 'boolean') {
			// echo '				$("#'.$key.'_'.$formName.'_1").prop("checked", (obj.'.$key.' == 1));';
			echo '					$("#'.$formName.' [name=\''.$key.'\']").prop("checked", (obj.'.$key.' == 1)).trigger("change");';
		}
		else if ($val == 'file') {
			echo '			renderInputTypeFile($("#'.$formName.' [name=\''.$key.'\']"), obj.'.$key.');';
			//echo '				$("#'.$key.'_'.$formName.'").val(obj.'.$key.');';
			// echo '					$("#'.$formName.' [name=\''.$key.'\']:not(input[type=\'file\'])").val(obj.'.$key.');';
		}
		else {
			//echo '				$("#'.$key.'_'.$formName.'").val(obj.'.$key.');';
			if ($key == 'outlets') {
				echo '	
					var arrOutletIds = new Array();				
					for(var x in obj.'.$key.')
					{
						var o = obj.'.$key.'[x];
						arrOutletIds.push(o[\'outlet_id\']);
					}
					$("#'.$formName.' [name=\''.$key.'[]\']").val(arrOutletIds);';
			}
			else {
				echo '					$("#'.$formName.' [name=\''.$key.'\']").val(obj.'.$key.');';
			}
		}
		echo "\n";
	}				
	echo '					$("#'.$formName.' .select2").trigger("change");';
	echo "\n";
?>

					for(var i in obj.payment_types) {
						rec = obj.payment_types[i];
						key = 'payment_type_id_' + rec.payment_type_id;
						$("#form1 [name='" + key + "']").prop("checked", (rec['is_enable'] == 1)).trigger("change");
					}
					for(var i in obj.taxes) {
						rec = obj.taxes[i];
						key = 'tax_id_' + rec.tax_id;
						$("#form1 [name='" + key + "']").prop("checked", (rec['is_enable'] == 1)).trigger("change");
						
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