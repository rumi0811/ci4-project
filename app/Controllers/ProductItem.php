<?php

namespace App\Controllers;

use App\Controllers\MasterDataMongoController;
use App\Models\MProduct;
use App\Models\MProductCategory;
use App\Models\MUom;
use App\Models\MOutlet;
use App\Models\MSaleType;

/**
 * ProductItem Controller
 * Product CRUD management
 * Converted from CI3 Product_item.php
 * 
 * CONVERSION NOTES:
 * - NO LOGIC CHANGES - hanya syntax CI3 → CI4
 * - Semua business logic preserved dari CI3
 * - Menggunakan auto-generate form dari MasterDataMongoController
 * - TIDAK pakai manual view seperti sebelumnya
 */
class ProductItem extends MasterDataMongoController
{
    public function __construct()
    {
        parent::__construct('product_item', 'm_product');
        $this->set_unique_fields(['product_code' => 'Product code']);
    }

    protected function fieldLookupDefinitionForColumnProperties()
    {
        $arrOverideCol = [];
        $arrOverideCol['picture'] = ['colProperties' => "width: '40px', class: 'text-center'"];
        $arrOverideCol['is_active'] = ['colProperties' => "width: '60px', class: 'text-center'"];
        $arrOverideCol['is_all_outlet'] = ['colProperties' => "width: '60px', class: 'text-center'"];
        $arrOverideCol['sale_price'] = ['title' => 'Sale Price', 'colProperties' => "width: '80px', class: 'text-right'"];
        return $arrOverideCol;
    }

    protected function extraCoding()
    {
        $result = $this->GetUploadFolderJavascript();

        // FIX: Setup field structure untuk JavaScript generation
        // Field structure harus sama seperti di createFormEdit()
        $this->fieldStructure = [];
        $this->fieldStructure[$this->pk_id] = 'int';
        $this->fieldStructure['picture'] = 'file';
        $this->fieldStructure['product_code'] = 'string';
        $this->fieldStructure['product_name'] = 'string';
        $this->fieldStructure['description'] = 'string';
        $this->fieldStructure['barcode'] = 'string';
        $this->fieldStructure['is_sale'] = 'boolean';
        $this->fieldStructure['is_active'] = 'boolean';
        $this->fieldStructure['is_addon'] = 'boolean';
        $this->fieldStructure['is_inventory'] = 'boolean';
        $this->fieldStructure['is_all_outlet'] = 'boolean';
        $this->fieldStructure['uom_id'] = 'int';
        $this->fieldStructure['product_category_id'] = 'int';

        // Load m_sale_type untuk generate dynamic sale_price fields
        $mSaleType = new \App\Models\MSaleType();
        $dataSaleType = $mSaleType->findAll();
        if ($dataSaleType) {
            foreach ($dataSaleType as $rowSaleType) {
                $this->fieldStructure['sale_price_' . $rowSaleType['sale_type_id']] = 'float';
            }
        }

        // FIX: Pass variables yang dibutuhkan ke view
        $data = $this->dataPage ?? [];
        $data['fieldStructure'] = $this->fieldStructure;
        $data['formName'] = $this->formName;
        $data['controllerName'] = $this->controllerName;

        $viewPath = APPPATH . 'Views/general/product_extra_coding.php';
        log_message('debug', 'Loading view from: ' . $viewPath);
        log_message('debug', 'View file exists: ' . (file_exists($viewPath) ? 'YES' : 'NO'));
        log_message('debug', 'fieldStructure keys: ' . implode(', ', array_keys($this->fieldStructure)));

        $result .= view('general/product_extra_coding', $data);
        return $result;
    }

    public function index()
    {
        helper('smart_form');

        $this->hiddenGridField = [
            'company_id',
            'description',
            'product_category_id',
            'category_code',
            'barcode',
            'is_sale',
            'is_addon',
            'is_inventory',
            'minimum_stock',
            'maximum_stock',
            'coa_code',
            'uom_id',
            'uom_code',
            'outlets',
            'addons',
            'ingredients',
            'sale_prices',
            'client_id',
            'server_id',
            'updatedAt',
            '_deleted'
        ];

        // Setup fieldStructure untuk editFormProduct
        $this->fieldStructure = [];
        $this->fieldStructure[$this->pk_id] = 'int';
        $this->fieldStructure['picture'] = 'file';
        $this->fieldStructure['product_code'] = 'string';
        $this->fieldStructure['product_name'] = 'string';
        $this->fieldStructure['description'] = 'string';
        $this->fieldStructure['barcode'] = 'string';
        $this->fieldStructure['is_sale'] = 'boolean';
        $this->fieldStructure['is_active'] = 'boolean';
        $this->fieldStructure['is_addon'] = 'boolean';
        $this->fieldStructure['is_inventory'] = 'boolean';
        $this->fieldStructure['is_all_outlet'] = 'boolean';
        $this->fieldStructure['uom_id'] = 'int';
        $this->fieldStructure['product_category_id'] = 'int';

        // Dynamic sale_price fields
        $mSaleType = new MSaleType();
        $dataSaleType = $mSaleType->findAll();
        if ($dataSaleType) {
            foreach ($dataSaleType as $rowSaleType) {
                $this->fieldStructure['sale_price_' . $rowSaleType['sale_type_id']] = 'float';
            }
        }

        return $this->datatable();
    }

    protected function onBeforeSave($record)
    {
        $record['company_id'] = $this->company_id;

        // Get product category details
        if (isset($record['product_category_id']) && $record['product_category_id'] > 0) {
            $mProductCategory = new MProductCategory();
            $dataProductCategory = $mProductCategory->findByProductCategoryId((int)$record['product_category_id']);

            if ($dataProductCategory) {
                $record['category_code'] = $dataProductCategory['category_code'];
                $record['category_name'] = $dataProductCategory['category_name'];
            }
        }

        // Get UOM details
        if (isset($record['uom_id']) && $record['uom_id'] > 0) {
            $mUom = new MUom();
            $dataUom = $mUom->findByUomId((int)$record['uom_id']);

            if ($dataUom) {
                $record['uom_code'] = $dataUom['uom_code'];
            }
        }

        // Process kits/addons data
        $arrKits = @json_decode($this->request->getPost('data_kit_json'), true);
        if ($this->request->getPost('data_kit_json') != null) {
            $arrKitsFinal = [];
            if ($arrKits) {
                foreach ($arrKits as $row) {
                    unset($row['no']);
                    unset($row['action']);
                    $arrKitsFinal[] = $row;
                }
            }
            $record['addons'] = $arrKitsFinal;
        }

        // Process ingredients data
        $arrIngredients = @json_decode($this->request->getPost('data_ingredient_json'), true);
        if ($this->request->getPost('data_ingredient_json') != null) {
            $arrIngredientsFinal = [];
            if ($arrIngredients) {
                foreach ($arrIngredients as $row) {
                    unset($row['no']);
                    unset($row['action']);
                    $row['qty'] = floatval($row['qty']);
                    $arrIngredientsFinal[] = $row;
                }
            }
            $record['ingredients'] = $arrIngredientsFinal;
        }

        // Process outlets
        if (isset($record['outlets']) && $record['outlets']) {
            $resultOutlet = [];
            foreach ($record['outlets'] as $key => $val) {
                $val = (int)$val;
                $resultOutlet[] = ['outlet_id' => $val];
            }
            $record['outlets'] = $resultOutlet;
        }

        // Process sale prices by sale type
        $mSaleType = new MSaleType();
        $dataSaleType = $mSaleType->findAll(null, null, 'sale_type_id');

        if ($dataSaleType) {
            // Convert cursor to array
            $arrSaleType = [];
            foreach ($dataSaleType as $row) {
                $arrSaleType[] = $row;
            }

            $record['sale_prices'] = [];
            $counter = 0;

            foreach ($arrSaleType as $rowSaleType) {
                $counter++;
                $saleTypeId = $rowSaleType['sale_type_id'];
                $fieldName = 'sale_price_' . $saleTypeId;

                if ($this->request->getPost($fieldName) != null) {
                    $record['sale_prices'][] = [
                        'sale_type_id' => $rowSaleType['sale_type_id'],
                        'sale_type' => $rowSaleType['sale_type'],
                        'sale_price' => floatval($this->request->getPost($fieldName)),
                    ];

                    // First sale price becomes main sale_price
                    if ($counter == 1) {
                        $record['sale_price'] = floatval($this->request->getPost($fieldName));
                    }
                }
            }
        } else {
            // No sale types, just use single sale_price
            if (isset($record['sale_price'])) {
                $record['sale_price'] = floatval($record['sale_price']);
            }
        }

        $record['updatedAt'] = time();
        return $record;
    }

    protected function onSuccessSave($oldRecord, $newRecord)
    {
        // Hook untuk post-save operations jika diperlukan
        // Saat ini kosong seperti di CI3
    }


    /**
     * Create Form Edit
     * EXACT conversion dari CI3 menggunakan Form library
     */
    protected function createFormEdit()
    {
        // Setup field structure - EXACT dari CI3
        $this->fieldStructure = [];
        $this->fieldStructure[$this->pk_id] = 'int';
        $this->fieldStructure['picture'] = 'file';
        $this->fieldStructure['product_code'] = 'string';
        $this->fieldStructure['product_name'] = 'string';
        $this->fieldStructure['description'] = 'string';
        $this->fieldStructure['barcode'] = 'string';
        $this->fieldStructure['is_sale'] = 'boolean';
        $this->fieldStructure['is_active'] = 'boolean';
        $this->fieldStructure['is_addon'] = 'boolean';
        $this->fieldStructure['is_inventory'] = 'boolean';
        $this->fieldStructure['is_all_outlet'] = 'boolean';
        $this->fieldStructure['uom_id'] = 'int';
        $this->fieldStructure['product_category_id'] = 'int';

        // CI4: Gunakan Form library (sama seperti Customers)
        $form = new \App\Libraries\Form([
            'action' => $this->controllerName . '/save_data',
            'id' => $this->formName
        ]);

        $form->isFormOnly = true;
        $form->caption = 'Add/Edit Product';
        $form->addHidden($this->pk_id, '0');
        $form->addHidden('modal_dialog_class', 'modal-xl modal-dialog-scrollable');
        $form->addHidden('data_kit_json', '');
        $form->addHidden('data_ingredient_json', '');

        // Load models untuk dropdown
        $mProductCategory = new \App\Models\MProductCategory();
        $mUom = new \App\Models\MUom();
        $mOutlet = new \App\Models\MOutlet();
        $mSaleType = new \App\Models\MSaleType();

        // Add fields - EXACT seperti CI3
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

        $form->addInput('Product Code/SKU', 'product_code', '', array(), "string", true, true, "", "", true, 3, '');
        $form->addInput('Product Name', 'product_name', '', array(), "string", true, true, "", "", true, 6, '');

        // Dropdown Product Category
        $arrProductCategory = $this->generateDropdownFromModel(
            $mProductCategory,
            [
                'company_id' => $this->company_id,
                'is_active' => 1,
                '_deleted' => ['$ne' => true]
            ],
            'product_category_id',
            'category_name'
        );
        $form->addSelect("Product Category", 'product_category_id', $arrProductCategory, 0, array(), true, true, "", "", true, 3, '');

        $form->addTextarea('Description', 'description', '', array('rows' => 4), "string", false, true, "", "", true, 12, '');

        $form->addInput('Kode Barcode', 'barcode', '', array(), "string", false, true, "", "<i class='fal fa-barcode'></i>", true, 6, '');

        // Dropdown UOM
        $arrUOM = $this->generateDropdownFromModel(
            $mUom,
            ['company_id' => $this->company_id],
            'uom_id',
            'uom_code'
        );
        $form->addSelect("UoM", 'uom_id', $arrUOM, 0, array(), true, true, "", "", true, 3, '');

        $this->fieldStructure['cogs_price'] = 'float';
        $form->addInput('COGS Price', 'cogs_price', '', array(), "string", true, true, "", "", true, 3, '');

        // Sale Price - check apakah ada sale types
        $dataSaleType = $mSaleType->findAll();
        if ($dataSaleType) {
            $counter = 0;
            foreach ($dataSaleType as $rowSaleType) {
                $counter++;
                $this->fieldStructure['sale_price_' . $rowSaleType['sale_type_id']] = 'float';
                $form->addInput(
                    'Unit Price (' . $rowSaleType['sale_type'] . ')',
                    'sale_price_' . $rowSaleType['sale_type_id'],
                    '',
                    array(),
                    "string",
                    true,
                    true,
                    "",
                    "",
                    true,
                    3,
                    ''
                );
            }
            if ($counter % 4 != 0) {
                $form->addLiteral('', 'lblFiller', '<div style="clear: both"></div>', false);
            }
        } else {
            $this->fieldStructure['sale_price'] = 'float';
            $form->addInput('Unit Price', 'sale_price', '', array(), "string", true, true, "", "", true, 3, '');
        }


        // Debug: Log fieldStructure
        log_message('debug', 'ProductItem fieldStructure: ' . print_r($this->fieldStructure, true));


        $form->addCheckBoxToggle(
            'Product Availability',
            'is_all_outlet',
            array(1 => 'Available to all outlets'),
            true,
            array(),
            false,
            true,
            "",
            "",
            true,
            6
        );

        // Dropdown Outlets (multiple)
        $arrOutlets = $this->generateDropdownFromModel(
            $mOutlet,
            ['company_id' => $this->company_id],
            'outlet_id',
            'outlet_name'
        );
        $this->fieldStructure['outlets'] = 'array';
        $form->addSelect(
            "Select Outlets",
            'outlets',
            $arrOutlets,
            '',
            array('multiple' => 'multiple'),
            false,
            true,
            "",
            "",
            true,
            6,
            ''
        );

        $form->addCheckBoxToggle('Is Active', 'is_active', array(1 => 'Yes'), false, array(), false, true, "", "", true, 3);
        $form->addCheckBoxToggle('Product is for Sale', 'is_sale', array(1 => 'Yes'), false, array(), false, true, "", "", true, 3);
        $form->addCheckBoxToggle('Can be Addon/Kit', 'is_addon', array(1 => 'Yes'), false, array(), false, true, "", "", true, 3);
        $form->addCheckBoxToggle('Maintain Inventory', 'is_inventory', array(1 => 'Yes'), false, array(), false, true, "", "", true, 3);

        $form->addLiteral('', 'lblTab', view('general/product_edit_extra', []), false);

        $form->addButton('btnSaveProduct', 'Save ', array(), true, "", "", "");
        $form->addReset('btnCancel', 'Cancel', array('data-dismiss' => "modal"), true, "", "", "");

        return $form->render();
    }

    /**
     * Helper untuk generate dropdown dari model
     */
    protected function generateDropdownFromModel($model, $criteria, $valueField, $labelField)
    {
        $cursor = $model->findAll($criteria);
        $options = [];
        $options[0] = '-- Select --';

        if ($cursor) {
            foreach ($cursor as $row) {
                $options[$row[$valueField]] = $row[$labelField];
            }
        }

        return $options;
    }

    /**
     * Form untuk add/edit ingredient
     * Converted dari CI3
     */
    public function formIngredient()
    {
        $this->fieldStructure = [];
        $this->fieldStructure[$this->pk_id] = 'int';
        $this->fieldStructure['picture'] = 'file';
        $this->fieldStructure['product_code'] = 'string';
        $this->fieldStructure['product_name'] = 'string';
        $this->fieldStructure['qty'] = 'float';

        $mProduct = new MProduct();

        $arrCriteria = [
            'is_active' => 1,
            'is_inventory' => 1,
            'company_id' => $this->company_id
        ];

        $arrProducts = $this->generateDropdownOptions(
            $mProduct,
            $arrCriteria,
            'product_id',
            'product_code'
        );

        $data = [
            'arrProducts' => $arrProducts
        ];

        return view('product_item/form_ingredient', $data);
    }

    /**
     * Form untuk add/edit addon
     * Converted dari CI3
     */
    public function formAddon()
    {
        $this->fieldStructure = [];
        $this->fieldStructure[$this->pk_id] = 'int';
        $this->fieldStructure['picture'] = 'file';
        $this->fieldStructure['product_code'] = 'string';
        $this->fieldStructure['product_name'] = 'string';

        $mProduct = new MProduct();

        $arrCriteria = [
            'is_active' => 1,
            'is_addon' => 1,
            'company_id' => $this->company_id
        ];

        $arrProducts = $this->generateDropdownOptions(
            $mProduct,
            $arrCriteria,
            'product_id',
            'product_code'
        );

        $data = [
            'arrProducts' => $arrProducts
        ];

        return view('product_item/form_addon', $data);
    }

    /**
     * Helper untuk generate dropdown options
     */
    protected function generateDropdownOptions($model, $criteria, $valueField, $labelField)
    {
        $cursor = $model->findAll($criteria);
        $options = [];

        if ($cursor) {
            foreach ($cursor as $row) {
                $options[$row[$valueField]] = $row[$labelField];
            }
        }

        return $options;
    }
}
