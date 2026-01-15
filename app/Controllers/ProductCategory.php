<?php

namespace App\Controllers;

use App\Controllers\MasterDataMongoController;

class ProductCategory extends MasterDataMongoController
{
    public function __construct()
    {
        parent::__construct('product_category', 'm_product_category');
        $this->set_unique_fields(['category_code' => 'Category code']);
    }

    public function index()
    {
        // TODO: MongoDB aggregation temporarily disabled
        // Will be enabled after mongo_db initialization is fixed
        // Original CI3 logic: Count products per category and add to grid

        /*
    // CI4: MongoDB aggregation to count products per category
    ini_set('mongo.long_as_object', 1);
    
    $arrCriteria = [];
    $arrCriteria['company_id'] = $this->company_id;
    $arrCriteria['product_category_id']['$exists'] = true;
    
    $pipeline = [
        [
            '$match' => $arrCriteria,
        ],
        [
            '$group' => [
                '_id' => '$product_category_id',
                'count' => ['$sum' => 1],
            ]
        ],
    ];
    
    $dataCount = [];
    
    // CI4: Use MongoDB library for aggregation
    if ($cursor = $this->mongo_db->aggregateCursor('m_product', $pipeline, 100)) {
        $cursor->timeout(400000);
        foreach ($cursor as $row) {
            $dataCount[$row['_id']] = ['product_count' => $row['count']];
        }
    }
    
    $this->LookupDataPrimaryKey = $dataCount;
    
    // CI4: Add product_count column to grid definition
    $arrNewGridDefinition = [];
    foreach ($this->gridDefinition as $dataIndex => $gridDef) {
        if ($dataIndex == 'note') {
            $arrNewGridDefinition['product_count'] = array(
                'title' => 'Jumlah Produk',
                'dataIndex' => 'product_count',
                'type' => 'string',
                'filter_type' => 'text',
                'filter_value' => '',
                'colProperties' => "width: '80px', class: 'text-right'",
                'inputType' => 'text',
                'validationType' => 'false',
            );
        }
        $arrNewGridDefinition[$dataIndex] = $gridDef;
    }
    $this->gridDefinition = $arrNewGridDefinition;
    */

        $this->hiddenGridField = array('company_id');
        $this->datatable();
    }

    protected function onBeforeSave($record)
    {
        $record['company_id'] = $this->company_id;
        $record['updatedAt'] = time();
        return $record;
    }

    protected function onSuccessSave($oldRecord, $newRecord)
    {
        if ($newRecord['product_category_id'] > 0) {
            // Update - need to update relation table if category code or name changed
            if (
                $oldRecord['category_code'] != $newRecord['category_code'] ||
                $oldRecord['category_name'] != $newRecord['category_name']
            ) {
                // CI4: Load model and update related products
                $mProduct = new \App\Models\MProduct();
                $mProduct->update_all(
                    ['product_category_id' => $newRecord['product_category_id']],
                    [
                        'category_code' => $newRecord['category_code'],
                        'category_name' => $newRecord['category_name']
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
        $this->fieldStructure['picture'] = 'file';
        $this->fieldStructure['category_code'] = 'string';
        $this->fieldStructure['category_name'] = 'string';
        $this->fieldStructure['note'] = 'string';
        $this->fieldStructure['is_active'] = 'boolean';
        $this->fieldStructure['sequence_no'] = 'int';

        // CI4: Load Form library
        $form = new \App\Libraries\Form([
            'action' => $this->controllerName . '/save_data',
            'id' => $this->formName
        ]);

        $form->isFormOnly = true;
        $form->caption = 'Add/Edit Product Category';
        $form->addHidden($this->pk_id, '0');
        $form->addHidden($this->sequenceField, '0');

        $form->addFile(
            'Picture',
            'picture',
            '',
            array(),
            "string",
            false,
            true,
            '<a class="delete_file_link" style="display: none; position: absolute; right: 16px; background-color: black; color: white; opacity: 0.8; padding: 4px 8px">X</a>
            <div id="imageLogoContent_picture"></div>',
            "",
            true,
            12,
            ''
        );

        $form->addInput('Category Code', 'category_code', '', array(), "string", true, true, "", "", true, 3, '');
        $form->addInput('Category Name', 'category_name', '', array(), "string", true, true, "", "", true, 9, '');
        $form->addTextarea('Note', 'note', '', array('rows' => 4), "string", false, true, "", "", true, 12, '');

        $form->addCheckBoxToggle('Is Active', 'is_active', array(1 => 'Yes'), true, array(), false, true, "", "", true, 3);

        $form->addButton('btnSave', 'Save ', array(), true, "", "", "");
        $form->addReset('btnCancel', 'Cancel', array('data-dismiss' => "modal"), true, "", "", "");

        return $form->render();
    }
}
