<?php

namespace App\Controllers;

use App\Controllers\MasterDataMongoController;

class Cashier extends MasterDataMongoController
{
    public function __construct()
    {
        parent::__construct('cashier', 'm_cashier');
        $this->set_unique_fields(['cashier_code' => 'Cashier Code']);

        // TEMPORARY: Force privileges untuk testing
        $this->privilegeIndex = 1;
        $this->privilegeUpdate = 1;
        $this->privilegeDelete = 1;
        $this->privilegeApprove = 1;

        // Set current page info
        $this->currentPage = [
            'menu_name' => 'Cashier User',
            'page_name' => 'Cashier User Management',
            'parent_menu_name' => 'Master Data',
            'parent_menu_file_name' => ''
        ];
    }

    protected function field_lookup_definition_for_column_properties()
    {
        $arrOverideCol = array();
        $arrOverideCol['profile_picture'] = array('colProperties' => "width: '40px', class: 'text-center'");
        $arrOverideCol['is_active'] = array('colProperties' => "width: '60px', class: 'text-center'");
        $arrOverideCol['cashier_code'] = array('colProperties' => "width: '120px'");
        $arrOverideCol['cashier_type'] = array('colProperties' => "width: '120px', class: 'text-center'");
        $arrOverideCol['outlet_name'] = array('colProperties' => "width: '200px'");
        return $arrOverideCol;
    }

    public function index()
    {
        // CI4: Load outlet model for lookup
        $mOutlet = new \App\Models\MOutlet();
        $cursor = $mOutlet->findAll(['company_id' => $this->company_id], null, null, null, 'outlet_id');

        // Convert MongoDB cursor to array to avoid rewind error
        $dataOutlets = [];
        if ($cursor) {
            foreach ($cursor as $outlet) {
                $dataOutlets[$outlet['outlet_id']] = $outlet;
            }
        }
        $this->LookupData = $dataOutlets;

        $this->hiddenGridField = array(
            'company_id',
            'client_id',
            'cashier_password',
            'cashier_type_id',
            'outlet_id',
            'partner_id',
            'token',
            'timestamp',
            'email',
            'mobile',
            'address'
        );

        return $this->datatable();
    }

    protected function onBeforeSave($record)
    {
        $record['company_id'] = $this->company_id;

        // CI4: Only hash password if it's not empty (for edit scenario)
        if (!empty($record['cashier_password'])) {
            $record['cashier_password'] = md5($record['cashier_password']);
        }

        // Set cashier type based on cashier_type_id
        if ($record['cashier_type_id'] == 2) {
            $record['cashier_type'] = 'Supervisor';
        } else {
            $record['cashier_type'] = 'Normal Cashier';
        }

        // Get outlet name from outlet_id
        if ($record['outlet_id'] > 0) {
            $mOutlet = new \App\Models\MOutlet();
            $dataOutlet = $mOutlet->find(['outlet_id' => intval($record['outlet_id'])]);
            if ($dataOutlet) {
                $record['outlet_name'] = $dataOutlet['outlet_name'];
            }
        }

        return $record;
    }

    protected function onSuccessSave($oldRecord, $newRecord)
    {
        // No additional actions needed after save
    }

    protected function createFormEdit()
    {
        // Setup field structure
        $this->fieldStructure = [];
        $this->fieldStructure[$this->pk_id] = 'int';
        $this->fieldStructure['profile_picture'] = 'file';
        $this->fieldStructure['cashier_code'] = 'string';
        $this->fieldStructure['cashier_password'] = 'string';
        $this->fieldStructure['name'] = 'string';
        $this->fieldStructure['cashier_type_id'] = 'int';
        $this->fieldStructure['outlet_id'] = 'int';
        $this->fieldStructure['email'] = 'string';
        $this->fieldStructure['mobile'] = 'string';
        $this->fieldStructure['address'] = 'string';
        $this->fieldStructure['is_active'] = 'int';

        // CI4: Load Form library
        $form = new \App\Libraries\Form([
            'action' => $this->controllerName . '/save_data',
            'id' => $this->formName
        ]);

        $form->isFormOnly = true;
        $form->caption = 'Add/Edit Cashier';
        $form->addHidden($this->pk_id, '0');

        // Load outlet model for dropdown
        $mOutlet = new \App\Models\MOutlet();

        // Photo upload field
        $form->addFile(
            'Photo',
            'profile_picture',
            '',
            array(),
            "string",
            false,
            true,
            '<a class="delete_file_link" style="display: none; position: absolute; right: 16px; background-color: black; color: white; opacity: 0.8; padding: 4px 8px">X</a>
            <div id="imageLogoContent_profile_picture"></div>',
            "",
            true,
            12,
            ''
        );

        $form->addInput('Cashier Login', 'cashier_code', '', array(), "string", true, true, "", "", true, 6, '');
        $form->addInput('Cashier Password', 'cashier_password', '', array(), "password", true, true, "", "", true, 6, '');
        $form->addInput('Cashier Name', 'name', '', array(), "string", true, true, "", "", true, 12, '');

        $form->addInput("Email", 'email', '', array(), 'string', false, true, '', '<i class="fal fa-envelope"></i>', true, 6, '');
        $form->addInput("Mobile", 'mobile', '', array(), 'string', false, true, '', '<i class="fal fa-phone"></i>', true, 6, '');
        $form->addTextarea('Address', 'address', '', array('rows' => 3), "string", false, true, "", "", true, 12, '');

        $form->addRadio('Cashier Type', 'cashier_type_id', array(1 => 'Normal Cashier', 2 => 'Supervisor'), 0, array(), true, true);

        // Generate outlets dropdown
        $arrOutlets = $mOutlet->generateListCI(
            ['company_id' => $this->company_id],
            "outlet_name",
            null,
            "outlet_id",
            "outlet_name",
            true
        );
        $form->addSelect("Select Outlets", 'outlet_id', $arrOutlets, '', array(), true, true, "", "", true, 12, '');

        $form->addCheckBoxToggle('Is Active', 'is_active', array(1 => 'Yes'), false, array(), false, true, "", "", true, 3);

        $form->addButton('btnSave', 'Save ', array(), true, "", "", "");
        $form->addReset('btnCancel', 'Cancel', array('data-dismiss' => "modal"), true, "", "", "");

        return $form->render();
    }

    public function save_data()
    {
        $result = ['success' => 0];

        if ($this->request->getMethod() == 'post') {
            // Get model
            $model = new \App\Models\MCashier();

            // Collect data from POST
            $record = [];
            foreach ($model->fieldStructure as $key => $val) {
                if ($val != 'file' && $this->request->getPost($key) !== null) {
                    $record[$key] = $this->request->getPost($key);
                    if ($val == 'int' || $val == 'boolean') {
                        $record[$key] = intval($record[$key]);
                    }
                }
            }

            // Call onBeforeSave
            $record = $this->onBeforeSave($record);
            $record["updatedAt"] = time();

            if (!empty($record[$this->pk_id]) && $record[$this->pk_id] > 0) {
                // UPDATE
                $record["modified"] = date("Y-m-d H:i:s");
                $record["modified_by"] = session()->get('user_id');

                $pkValue = intval($record[$this->pk_id]);
                $oldRecord = $model->find([$this->pk_id => $pkValue]);

                if ($model->update([$this->pk_id => $pkValue], $record)) {
                    $result['success'] = 1;
                    $result['message'] = 'Data updated successfully';
                    $this->onSuccessSave($oldRecord, $record);
                } else {
                    $result['error_message'] = 'Failed to update data';
                }
            } else {
                // INSERT
                $record["created"] = date("Y-m-d H:i:s");
                $record["created_by"] = session()->get('user_id');
                unset($record[$this->pk_id]);

                if ($newId = $model->insert($record)) {
                    $record[$this->pk_id] = $newId;
                    $result['success'] = 1;
                    $result['message'] = 'Data saved successfully';
                    $this->onSuccessSave([], $record);
                } else {
                    $result['error_message'] = 'Failed to save data';
                }
            }
        } else {
            $result['error_message'] = 'Invalid request method';
        }

        return $this->response->setJSON($result);
    }
}
