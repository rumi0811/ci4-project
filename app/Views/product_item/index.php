<?php $this->load->view('includes/layout_breadcrumb'); ?>
<?php $this->load->view('includes/layout_message'); ?>

<?php echo $grid; ?>

<?php //$this->load->view('product_item/edit_popup'); ?>
<?php //$this->load->view('includes/layout_ordering_data'); ?>

<?php //$this->load->view('product_item/upload_popup'); ?>

<form id="form_blank" method="post" target="_blank" action="<?php echo base_url(); ?>product_item/set_price">
	<input type="hidden" name="id_pos_outlet" id="id_pos_outlet_form" value="0" />
</form>

<script type="text/javascript">
	var setPriceOutlet = function(row) {
		$('#id_pos_outlet_form').val(row.id_pos_outlet);
		$('#form_blank').attr('action', "<?php echo base_url(); ?>product_item/set_price");
		$('#form_blank').submit();
	};

	
	var editForm = function(id)
	{
		$('#id_pos_outlet_form').val(id);
		$('#form_blank').attr('action', "<?php echo base_url(); ?>product_item/edit/" + id);
		$('#form_blank').submit();
	};

	
	var addData = function()
	{
		$('#id_pos_outlet_form').val(0);
		$('#form_blank').attr('action', "<?php echo base_url(); ?>product_item/edit");
		$('#form_blank').submit();		
	};
</script>

