<?php

namespace App\Controllers;

use App\Controllers\MasterDataMongoController;
use App\Models\MUserType;

class UserType extends MasterDataMongoController
{
    public function __construct()
    {
        parent::__construct('user_type', 'm_user_type');
        $this->set_unique_fields(['user_type' => 'User Type']);
    }

    public function index()
    {
        helper('smart_form');

        // $this->hiddenGridField = ['company_id', 'cashier_password', 'cashier_type_id', 'outlet_id'];
        $this->isHidePrimaryKeyColumn = false;

        return $this->datatable();
    }

    protected function onBeforeSave($record)
    {
        return $record;
    }

    protected function onSuccessSave($oldRecord, $newRecord)
    {
        // Custom logic after save (if needed)
    }

    protected function createFormEdit()
    {
        // Setup field structure - EXACT dari CI3
        $this->fieldStructure = [];
        $this->fieldStructure[$this->pk_id] = 'int';
        $this->fieldStructure['user_type'] = 'string';

        // CI4: Gunakan Form library (sama seperti ProductItem)
        $form = new \App\Libraries\Form([
            'action' => $this->controllerName . '/save_data',
            'id' => $this->formName
        ]);

        $form->isFormOnly = true;
        $form->caption = 'Add/Edit User Type';
        $form->addHidden($this->pk_id, '0');

        $form->addInput('User Type', 'user_type', '', array(), "string", true, true, "", "", true, 12, '');

        $form->addButton('btnSave', 'Save ', array(), true, "", "", "");
        $form->addReset('btnCancel', 'Cancel', array('data-dismiss' => "modal"), true, "", "", "");

        return $form->render();
    }
}
