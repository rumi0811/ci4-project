<?php

namespace App\Controllers;

use App\Controllers\MasterDataMongoController;

class Uom extends MasterDataMongoController
{
    public function __construct()
    {
        parent::__construct('uom', 'm_uom');
        $this->set_unique_fields(['uom_code' => 'Unit code']);
    }

    protected function field_lookup_definition_for_column_properties()
    {
        $arrOverideCol = array();
        $arrOverideCol['uom_code'] = array('colProperties' => "width: '120px', class: 'text-center'");
        return $arrOverideCol;
    }

    public function index()
    {
        $this->hiddenGridField = array('company_id');
        return $this->datatable();
    }

    protected function onBeforeSave($record)
    {
        $record['company_id'] = $this->company_id;
        return $record;
    }

    protected function onSuccessSave($oldRecord, $newRecord)
    {
        if ($newRecord['uom_id'] > 0) {
            // Update - need to update relation table if uom_code changed
            if ($oldRecord['uom_code'] != $newRecord['uom_code']) {
                // CI4: Load model and update related products
                $mProduct = new \App\Models\MProduct();
                $mProduct->update_all(
                    ['uom_id' => $newRecord['uom_id']],
                    [
                        'uom_code' => $newRecord['uom_code'],
                    ]
                );
            }
        }
    }

    protected function createFormEdit()
    {
        // Setup field structure
        $this->fieldStructure = [];
        $this->fieldStructure[$this->pk_id] = 'int';
        $this->fieldStructure['uom_code'] = 'string';
        $this->fieldStructure['description'] = 'string';

        // CI4: Load Form library
        $form = new \App\Libraries\Form([
            'action' => $this->controllerName . '/save_data',
            'id' => $this->formName
        ]);

        $form->isFormOnly = true;
        $form->caption = 'Add/Edit Unit of Measure';
        $form->addHidden($this->pk_id, '0');

        $form->addInput('Unit of Measure (UoM) Code', 'uom_code', '', array(), "string", true, true, "", "", true, 12, '');
        $form->addTextarea('Description', 'description', '', array('rows' => 4), "string", false, true, "", "", true, 12, '');

        $form->addButton('btnSave', 'Save ', array(), true, "", "", "");

        $form->addReset('btnCancel', 'Cancel', array('data-dismiss' => "modal"), true, "", "", "");

        return $form->render();
    }
}
