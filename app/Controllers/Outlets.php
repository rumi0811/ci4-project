<?php

namespace App\Controllers;

use App\Controllers\MasterDataMongoController;

class Outlets extends MasterDataMongoController
{
    public function __construct()
    {
        parent::__construct('outlets', 'm_outlet');
        $this->set_unique_fields(['outlet_name' => 'Product code']);
    }

    protected function field_lookup_definition_for_column_properties()
    {
        $arrOverideCol = array();
        $arrOverideCol['outlet_logo'] = array('title' => 'Logo', 'colProperties' => "width: '40px', class: 'text-center'");
        $arrOverideCol['is_active'] = array('colProperties' => "width: '60px', class: 'text-center'");
        $arrOverideCol['phone_number'] = array('colProperties' => "width: '120px', class: 'text-center'");
        $arrOverideCol['outlet_name'] = array('colProperties' => "width: '150px'");
        return $arrOverideCol;
    }

    public function index()
    {
        $this->hiddenGridField = array(
            'company_id',
            'client_id',  // ← TAMBAHKAN INI JUGA!
            'note',
            'alt_phone_number',
            'city',
            'province',
            'country',
            'post_code'
        );
        return $this->datatable();  // ← TAMBAHKAN return!
    }

    protected function onBeforeSave($record)
    {
        $record['company_id'] = $this->company_id;
        $record['updatedAt'] = time();
        return $record;
    }

    protected function onSuccessSave($oldRecord, $newRecord)
    {
        if ($newRecord['outlet_id'] > 0) {
            // Update - need to update relation table if outlet name changed
            if ($oldRecord['outlet_name'] != $newRecord['outlet_name']) {
                // CI4: Load model and update related cashiers
                $mCashier = new \App\Models\MCashier();
                $mCashier->update_all(
                    ['outlet_id' => $newRecord['outlet_id']],
                    [
                        'outlet_name' => $newRecord['outlet_name'],
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
        $this->fieldStructure['outlet_logo'] = 'file';
        $this->fieldStructure['room_or_table_map'] = 'file';
        $this->fieldStructure['outlet_name'] = 'string';
        $this->fieldStructure['outlet_address'] = 'string';
        $this->fieldStructure['phone_number'] = 'string';
        $this->fieldStructure['alt_phone_number'] = 'string';
        $this->fieldStructure['city'] = 'string';
        $this->fieldStructure['province'] = 'string';
        $this->fieldStructure['country'] = 'string';
        $this->fieldStructure['post_code'] = 'string';
        $this->fieldStructure['note'] = 'string';
        $this->fieldStructure['is_active'] = 'boolean';

        // CI4: Load Form library
        $form = new \App\Libraries\Form([
            'action' => $this->controllerName . '/save_data',
            'id' => $this->formName
        ]);

        $form->isFormOnly = true;
        $form->caption = 'Add/Edit Outlet';
        $form->addHidden($this->pk_id, '0');
        $form->addHidden('modal_dialog_class', 'modal-xl modal-dialog-scrollable');

        // CI4: Models loaded but not used in CI3 - removed (dead code)
        // $this->load->model(['m_product_category', 'm_uom']);

        $form->addFile(
            'Outlet Logo',
            'outlet_logo',
            '',
            array(),
            "string",
            false,
            true,
            '<a class="delete_file_link" style="display: none; position: absolute; right: 16px; background-color: black; color: white; opacity: 0.8; padding: 4px 8px">X</a>
            <div id="imageLogoContent_outlet_logo"></div>',
            "",
            true,
            12,
            ''
        );

        $form->addInput('Outlet Name', 'outlet_name', '', array(), "string", true, true, "", "", true, 12, '');
        $form->addTextarea('Address', 'outlet_address', '', array('rows' => 4), "string", true, true, "", "", true, 12, '');

        $form->addInput('Phone Number', 'phone_number', '', array(), "string", false, true, "", "", true, 6, '');
        $form->addInput('Alternative Phone Number', 'alt_phone_number', '', array(), "string", false, true, "", "", true, 6, '');
        $form->addInput('City', 'city', '', array(), "string", false, true, "", "", true, 3, '');
        $form->addInput('Province', 'province', '', array(), "string", false, true, "", "", true, 3, '');
        $form->addInput('Country', 'country', '', array(), "string", false, true, "", "", true, 3, '');
        $form->addInput('Post code', 'post_code', '', array(), "string", false, true, "", "", true, 3, '');

        $form->addTextarea('Note', 'note', '', array('rows' => 4), "string", false, true, "", "", true, 12, '');

        $form->addFile(
            'Room or Table Map',
            'room_or_table_map',
            '',
            array(),
            "string",
            false,
            true,
            '<a class="delete_file_link" style="display: none; position: absolute; right: 16px; background-color: black; color: white; opacity: 0.8; padding: 4px 8px">X</a>
            <div id="imageLogoContent_room_or_table_map"></div>',
            "",
            true,
            12,
            ''
        );

        $form->addButton('btnSave', 'Save ', array(), true, "", "", "");

        $form->addReset('btnCancel', 'Cancel', array('data-dismiss' => "modal"), true, "", "", "");

        return $form->render();
    }

    public function save_data()
    {
        $result = ['success' => 0];

        if ($this->request->getMethod() == 'post') {
            // Get model
            $model = new \App\Models\MOutlet();

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
