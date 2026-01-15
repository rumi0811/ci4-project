<?php

namespace App\Controllers;

use App\Controllers\MasterDataMongoController;

class Customers extends MasterDataMongoController
{

    public function __construct()
    {
        parent::__construct('customers', 'm_customer');
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
            'note'
        );

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
