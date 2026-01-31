<?php

namespace App\Controllers;

use App\Controllers\MasterDataMongoController;

class Customers extends MasterDataMongoController
{

    public function __construct()
    {
        parent::__construct('customers', 'm_customer');
        $this->title = 'Customer';
        $this->set_unique_fields(['email_address' => 'Email address']);
    }

    protected function field_lookup_definition_for_column_properties()
    {
        $arrOverideCol = array();
        $arrOverideCol['photo'] = array('title' => 'Photo', 'colProperties' => "width: '40px', class: 'text-center'");
        $arrOverideCol['is_active'] = array('colProperties' => "width: '60px', class: 'text-center'");
        $arrOverideCol['mobile_phone'] = array('colProperties' => "width: '120px', class: 'text-center'");
        $arrOverideCol['register_date'] = array('colProperties' => "width: '120px', class: 'text-center'");
        $arrOverideCol['email_address'] = array('colProperties' => "width: '150px'");
        return $arrOverideCol;
    }


    public function index()
    {
        $this->hiddenGridField = array(
            'company_id',
            'photo',
            'outlet_id',
            'outlet_name',
            'address',
            'note',
            'note',
            'client_id',
            'server_id',
            'updatedAt',
            '_deleted'
        );

        $this->extraScript = '<script>
// Set reloadGrid function
var reloadGridFunc = function() {
    jQuery("#DataGrid1").DataTable().ajax.reload(null, false);
};

window.reloadGrid = reloadGridFunc;

if (typeof(top) !== "undefined" && top !== window) {
    top.reloadGrid = reloadGridFunc;
}

if (typeof(top) !== "undefined" && typeof(top.parent) !== "undefined" && top.parent !== null) {
    try {
        top.parent.reloadGrid = reloadGridFunc;
    } catch(e) {}
}

if (typeof(parent) !== "undefined" && parent !== window) {
    try {
        parent.reloadGrid = reloadGridFunc;
    } catch(e) {}
}

// ========== TAMBAH INI: FIX EDIT FORM ==========
// Override editForm to load data for edit
var originalEditForm = window.editForm || function() {};

window.editForm = function(id) {
    $("#modal_panel_edit_popup").modal();
    
    if (id == 0) {
        // Add new - reset form
        $("#form1 [name=\'customer_id\']").val("");
        $("#form1 [name=\'company_id\']").val("");
        $("#form1 [name=\'customer_name\']").val("");
        $("#form1 [name=\'email_address\']").val("");
        $("#form1 [name=\'mobile_phone\']").val("");
        $("#form1 [name=\'address\']").val("");
        $("#form1 [name=\'register_date\']").val("");
        $("#form1 [name=\'expiry_date\']").val("");
        $("#form1 [name=\'note\']").val("");
        $("#form1 [name=\'is_active\']").prop("checked", false);
        $("#form1 .select2").trigger("change");
        if ($("#form1 .summernote").length > 0) {
            $("#form1 .summernote").summernote("code", "");
        }
        
        $("#modalLoadingInfoEditPopup").css("display", "none");
    } else {
        // Edit - load data
        $("#modalLoadingInfoEditPopup").css("display", "block");
        
        var url = "' . base_url('customers/edit') . '/" + id;
        
        $.get(url, function(data) {
            if (typeof(data) === "string") {
                data = JSON.parse(data);
            }
            
            if (data.error_message) {
                toastr["error"](data.error_message);
                $("#modal_panel_edit_popup").modal("hide");
            } else {
                // Populate form
                $("#form1 [name=\'customer_id\']").val(data.customer_id || "");
                $("#form1 [name=\'company_id\']").val(data.company_id || "");
                $("#form1 [name=\'customer_name\']").val(data.customer_name || "");
                $("#form1 [name=\'email_address\']").val(data.email_address || "");
                $("#form1 [name=\'mobile_phone\']").val(data.mobile_phone || "");
                $("#form1 [name=\'address\']").val(data.address || "");
                $("#form1 [name=\'register_date\']").val(data.register_date || "");
                $("#form1 [name=\'expiry_date\']").val(data.expiry_date || "");
                $("#form1 [name=\'note\']").val(data.note || "");
                
                if (data.is_active == 1) {
                    $("#form1 [name=\'is_active\']").prop("checked", true);
                } else {
                    $("#form1 [name=\'is_active\']").prop("checked", false);
                }
                
                $("#form1 .select2").trigger("change");
                
                if ($("#form1 .summernote").length > 0 && data.note) {
                    $("#form1 .summernote").summernote("code", data.note);
                }
            }
        })
        .fail(function() {
            toastr["error"]("Failed to load data");
            $("#modal_panel_edit_popup").modal("hide");
        })
        .always(function() {
            $("#modalLoadingInfoEditPopup").css("display", "none");
        });
    }
};
// ========== END FIX ==========

console.log("editForm override applied");
</script>';
        // ================================

        return $this->datatable();
    }

    protected function onBeforeSave($record)
    {
        $record['company_id'] = $this->company_id;
        $record['updatedAt'] = time();
        return $record;
    }

    protected function onSuccessSave($oldRecord, $newRecord) {}

    protected function createFormEdit()
    {
        $mCustomer = new \App\Models\MCustomer();
        $this->fieldStructure = $mCustomer->fieldStructure ?? [];
        unset($this->fieldStructure['created']);
        unset($this->fieldStructure['created_by']);
        unset($this->fieldStructure['modified']);
        unset($this->fieldStructure['modified_by']);
        unset($this->fieldStructure['client_id']);
        unset($this->fieldStructure['server_id']);
        unset($this->fieldStructure['updatedAt']);
        unset($this->fieldStructure['_deleted']);

        $form = new \App\Libraries\Form(array('action' => $this->controllerName . '/save_data', 'id' => $this->formName));
        $form->isFormOnly = true;
        $form->caption = 'Add/Edit Customer';
        $form->addHidden($this->pk_id, '0');
        $form->addHidden('modal_dialog_class', 'modal-xl modal-dialog-scrollable');

        $form->addFile(
            'Profile Photo',
            'photo',
            '',
            array(),
            "string",
            false,
            true,
            '<a class="delete_file_link" style="display: none; position: absolute; right: 16px; background-color: black; color: white; opacity: 0.8; padding: 4px 8px">X</a>
		<div id="imageLogoContent_photo"></div>
		',
            "",
            true,
            12,
            ''
        );
        $form->addInput('Customer Name', 'customer_name', '', array(), "string", true, true, "", "", true, 12, '');
        $form->addInput('Register Date', 'register_date', '', array(), "date", false, true, "", "", true, 6, '');
        $form->addInput('Expiry Date', 'expiry_date', '', array(), "date", false, true, "", "", true, 6, '');
        $form->addInput('Email Address', 'email_address', '', array(), "string", false, true, "", "", true, 6, '');
        $form->addInput('Mobile Phone', 'mobile_phone', '', array(), "string", false, true, "", "", true, 6, '');
        $form->addTextarea('Address', 'address', '', array('rows' => 3), "string", false, true, "", "", true, 12, '');

        $form->addTextarea('Note', 'note', '', array('rows' => 4), "string", false, true, "", "", true, 12, '');
        $form->addCheckBoxToggle('Is Active', 'is_active', array(1 => 'Yes'), false, array(), false, true, "", "", true, 12);

        $form->addButton('btnSave', 'Save ', array(), true, "", "", "");

        $form->addReset('btnCancel', 'Cancel', array('data-dismiss' => "modal"), true, "", "", "");

        return $form->render();
    }
}
