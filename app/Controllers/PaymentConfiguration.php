<?php

namespace App\Controllers;

use App\Controllers\MasterDataMongoController;

class PaymentConfiguration extends MasterDataMongoController
{
    public function __construct()
    {
        parent::__construct('payment_configuration', 'm_payment_configuration');
        $this->set_unique_fields(['payment_configuration' => 'Configuration Name']);

        // TEMPORARY: Force privileges untuk testing
        $this->privilegeIndex = 1;
        $this->privilegeUpdate = 1;
        $this->privilegeDelete = 1;
        $this->privilegeApprove = 1;

        // Set current page info
        $this->currentPage = [
            'menu_name' => 'Payment Configuration',
            'page_name' => 'Payment Configuration Management',
            'parent_menu_name' => 'Master Data',
            'parent_menu_file_name' => ''
        ];
    }

    protected function field_lookup_definition_for_column_properties()
    {
        $arrOverideCol = array();
        $arrOverideCol['is_active'] = array('colProperties' => "width: '60px', class: 'text-center'");
        return $arrOverideCol;
    }

    protected function extra_coding()
    {
        $result = $this->GetUploadFolderJavascript();
        $result .= view('general/payment_configuration_extra_coding', $this->dataPage);
        return $result;
    }

    protected function addRowButtonEdit($datagrid)
    {
        $datagrid->addRowButtonEdit($this->pk_id, 'javascript', 'editFormCustom(data.' . $this->pk_id . ');');
    }

    public function index()
    {
        helper('smart_form');
        $this->hiddenGridField = array('company_id', 'client_id', 'payment_types', 'taxes', 'outlets');
        return $this->datatable();
    }

    protected function onBeforeSave($record)
    {
        $record['company_id'] = $this->company_id;

        if (isset($record['is_all_outlet']) && $record['is_all_outlet'] == 1) {
            $record['is_default'] = 1;
        } else {
            $record['is_all_outlet'] = 0;
            $record['is_default'] = 0;
        }

        if (isset($record['outlets'])) {
            $resultOutlet = [];
            foreach ($record['outlets'] as $o) {
                $o = intval($o);
                $resultOutlet[] = ['outlet_id' => $o];
            }
            $record['outlets'] = $resultOutlet;
        }

        $record['payment_types'] = [];
        $record['taxes'] = [];

        // Get all POST data
        $allPost = $this->request->getPost();

        foreach ($allPost as $key => $val) {
            if (stripos($key, "payment_type_id_") !== false) {
                $paymentTypeId = intval(str_replace("payment_type_id_", "", $key));
                $record['payment_types'][] = [
                    'payment_type_id' => $paymentTypeId,
                    'is_enable' => intval($val),
                ];
            } else if (stripos($key, "tax_id") !== false) {
                $taxId = intval(str_replace("tax_id_", "", $key));
                $record['taxes'][] = [
                    'tax_id' => $taxId,
                    'is_enable' => intval($val),
                ];
            }
        }

        $record['updatedAt'] = time();

        return $record;
    }

    protected function onSuccessSave($oldRecord, $newRecord)
    {
        // No additional actions needed after save
    }

    protected function createFormEdit()
    {
        // Setup field structure
        $model = new \App\Models\MPaymentConfiguration();
        $this->fieldStructure = $model->fieldStructure;

        // CI4: Load Form library
        $form = new \App\Libraries\Form([
            'action' => $this->controllerName . '/save_data',
            'id' => $this->formName
        ]);

        $form->isFormOnly = true;
        $form->caption = 'Add/Edit Payment Configuration';
        $form->addHidden($this->pk_id, '0');
        $form->addHidden($this->sequenceField, '0');
        $form->addHidden('modal_dialog_class', 'modal-xl modal-dialog-scrollable');

        // Load required models
        $mPaymentTypeGroup = new \App\Models\MPaymentTypeGroup();
        $mPaymentType = new \App\Models\MPaymentType();
        $mOutlet = new \App\Models\MOutlet();
        $mTax = new \App\Models\MTax();

        $form->addInput('Configuration Name', 'payment_configuration', '', array(), "string", true, true, "", "", true, 12, '');
        $form->addCheckBoxToggle('Assigned Outlets', 'is_all_outlet', array(1 => 'Available to all outlets'), true, array(), false, true, "", "", true, 6);

        $arrOutlets = $mOutlet->generateListCI(
            ['company_id' => $this->company_id],
            "outlet_name",
            null,
            "outlet_id",
            "outlet_name"
        );
        $form->addSelect("Select Outlets", 'outlets', $arrOutlets, '', array('multiple' => 'multiple'), false, true, "", "", true, 6, '');

        $form->addCheckBoxToggle('Is Active', 'is_active', array(1 => 'Yes'), false, array(), false, true, "", "", true, 12);

        $form->addTab('Payment Type', 'tab1');

        // Get payment type groups
        $arrPaymentTypeGroups = $mPaymentTypeGroup->findAllByIsActive(1, null, 'sequence_no');
        if ($arrPaymentTypeGroups) {
            $arrPaymentTypes = $mPaymentType->findAllByIsActive(1, null, 'sequence_no');

            // Convert cursor to array
            $paymentTypeGroupsArray = [];
            foreach ($arrPaymentTypeGroups as $group) {
                $paymentTypeGroupsArray[] = $group;
            }

            $paymentTypesArray = [];
            foreach ($arrPaymentTypes as $type) {
                $paymentTypesArray[] = $type;
            }

            foreach ($paymentTypeGroupsArray as $rowGroup) {
                $form->addFieldSet($rowGroup['payment_type_group'], 1);
                foreach ($paymentTypesArray as $row) {
                    if ($rowGroup['payment_type_group_id'] == $row['payment_type_group_id']) {
                        $form->addCheckBoxToggle(
                            $row['payment_type'],
                            'payment_type_id_' . $row['payment_type_id'],
                            array(1 => 'Yes'),
                            false,
                            array(),
                            false,
                            true,
                            "",
                            "",
                            true,
                            4
                        );
                    }
                }
            }
        }

        $form->addTab('Tax', 'tab2');

        $arrTaxes = $mTax->findAllByIsActive(1, null, 'sequence_no');
        if ($arrTaxes) {
            // Convert cursor to array
            $taxesArray = [];
            foreach ($arrTaxes as $tax) {
                $taxesArray[] = $tax;
            }

            foreach ($taxesArray as $row) {
                $form->addCheckBoxToggle(
                    $row['tax_code'],
                    'tax_id_' . $row['tax_id'],
                    array(1 => 'Enable'),
                    false,
                    array(),
                    false,
                    true,
                    "",
                    "",
                    true,
                    12
                );
            }
        }

        $form->addButton('btnSave', 'Save ', array(), true, "", "", "");
        $form->addReset('btnCancel', 'Cancel', array('data-dismiss' => "modal"), true, "", "", "");

        return $form->render();
    }

    public function save_data()
    {
        $result = ['success' => 0];

        if ($this->request->getMethod() == 'post') {
            // Get model
            $model = new \App\Models\MPaymentConfiguration();

            // Collect data from POST
            $record = [];
            foreach ($model->fieldStructure as $key => $val) {
                if ($val != 'file' && $val != 'array' && $this->request->getPost($key) !== null) {
                    $record[$key] = $this->request->getPost($key);
                    if ($val == 'int' || $val == 'boolean') {
                        $record[$key] = intval($record[$key]);
                    }
                }
            }

            // Get outlets array
            if ($this->request->getPost('outlets')) {
                $record['outlets'] = $this->request->getPost('outlets');
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
