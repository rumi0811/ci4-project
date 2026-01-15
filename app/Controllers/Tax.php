<?php

namespace App\Controllers;

use App\Controllers\MasterDataMongoController;

class Tax extends MasterDataMongoController
{
    public function __construct()
    {
        parent::__construct('tax', 'm_tax');
        $this->set_unique_fields(['tax_code' => 'Tax code']);
    }

    protected function field_lookup_definition_for_column_properties()
    {
        $arrOverideCol = array();
        $arrOverideCol['logo'] = array('colProperties' => "width: '40px', class: 'text-center'");
        $arrOverideCol['payment_type_group'] = array('colProperties' => "width: '180px'");
        return $arrOverideCol;
    }

    public function index()
    {
        $this->hiddenGridField = array('company_id');
        $this->datatable();
    }

    protected function onBeforeSave($record)
    {
        return $record;
    }

    protected function onSuccessSave($oldRecord, $newRecord) {}

    protected function createFormEdit()
    {
        // Setup field structure
        $this->fieldStructure = [];
        $this->fieldStructure[$this->pk_id] = 'int';
        $this->fieldStructure['tax_code'] = 'string';
        $this->fieldStructure['description'] = 'string';
        $this->fieldStructure['tax_percentage'] = 'float';
        $this->fieldStructure['is_active'] = 'boolean';

        // CI4: Load Form library
        $form = new \App\Libraries\Form([
            'action' => $this->controllerName . '/save_data',
            'id' => $this->formName
        ]);

        $form->isFormOnly = true;
        $form->caption = 'Add/Edit Tax';
        $form->addHidden($this->pk_id, '0');
        $form->addHidden($this->sequenceField, '0');

        $form->addInput('Tax Code', 'tax_code', '', array(), "string", true, true, "", "", true, 12, '');
        $form->addInput('Tax Percentage', 'tax_percentage', '', array(), "number", true, true, "", "%", true, 12, '');
        $form->addTextarea('Description', 'description', '', array('rows' => 4), "string", false, true, "", "", true, 12, '');

        $form->addCheckBoxToggle('Is Active', 'is_active', array(1 => 'Yes'), true, array(), false, true, "", "", true, 3);

        $form->addButton('btnSave', 'Save ', array(), true, "", "", "");

        $form->addReset('btnCancel', 'Cancel', array('data-dismiss' => "modal"), true, "", "", "");

        return $form->render();
    }
}
