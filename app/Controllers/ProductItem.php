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
 * NOTES:
 * - NO LOGIC CHANGES - hanya syntax CI3 → CI4
 * - Semua business logic preserved dari CI3
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
        $result .= view('general/product_extra_coding', $this->dataPage);
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
            'sale_prices'
        ];

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

    protected function createFormEdit()
    {
        // Setup field structure
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

        // CI4: Prepare data for view
        $record = [];

        // Check if editing (has ID in request)
        $id = $this->request->getPost($this->pk_id) ?? $this->request->getGet($this->pk_id);
        if ($id) {
            // Load existing record
            $modelClassName = $this->getModelClassName($this->tableName);
            $modelPath = "\\App\\Models\\{$modelClassName}";
            $model = new $modelPath();
            $result = $model->find(['product_id' => (int)$id]);
            if ($result) {
                $record = $result[0] ?? [];
            }
        }

        // Set default values for new record
        if (empty($record)) {
            foreach ($this->fieldStructure as $field => $type) {
                if ($type == 'int' || $type == 'boolean') {
                    $record[$field] = 0;
                } else if ($type == 'float' || $type == 'double') {
                    $record[$field] = 0.0;
                } else {
                    $record[$field] = '';
                }
            }
        }

        // Load models for dropdown data
        $mProductCategory = new MProductCategory();
        $mUom = new MUom();
        $mOutlet = new MOutlet();
        $mSaleType = new MSaleType();

        // Get dropdown data - Product Categories
        $pos_item_categories = [];
        $pos_item_categories[0] = '-- Select Product Category --';
        $dataProductCategory = $mProductCategory->findAll([
            'company_id' => $this->company_id,
            'is_active' => 1,
            '_deleted' => ['$ne' => true]
        ]);
        if ($dataProductCategory) {
            foreach ($dataProductCategory as $row) {
                $pos_item_categories[$row['product_category_id']] = $row['category_name'];
            }
        }

        // Get dropdown data - UOM
        $pos_uoms = [];
        $pos_uoms[0] = '-- Select UOM --';
        $dataUom = $mUom->findAll(['company_id' => $this->company_id]);
        if ($dataUom) {
            foreach ($dataUom as $row) {
                $pos_uoms[$row['uom_id']] = $row['uom_code'];
            }
        }

        // Get dropdown data - Outlets
        $outlet_list = [];
        $dataOutlet = $mOutlet->findAll(['company_id' => $this->company_id]);
        if ($dataOutlet) {
            foreach ($dataOutlet as $row) {
                $outlet_list[$row['outlet_id']] = $row['outlet_name'];
            }
        }

        // Get sale types
        $sale_types = [];
        $dataSaleType = $mSaleType->findAll();
        if ($dataSaleType) {
            foreach ($dataSaleType as $row) {
                $sale_types[] = $row;
            }
        }

        // ✅ ADD FIELD ALIASES untuk backward compatibility dengan view CI3
        if (!empty($record)) {
            // Alias product_id
            $record['id_pos_item'] = $record['product_id'] ?? 0;

            // Alias field names dengan prefix 'item_'
            $record['item_barcode'] = $record['barcode'] ?? '';
            $record['item_name'] = $record['product_name'] ?? '';
            $record['item_description'] = $record['description'] ?? '';
            $record['item_sku'] = $record['product_code'] ?? '';
            $record['item_picture_cover'] = $record['picture'] ?? '';

            // Alias category & uom dengan prefix 'id_pos_'
            $record['id_pos_item_category'] = $record['product_category_id'] ?? 0;
            $record['id_pos_uom'] = $record['uom_id'] ?? 0;
        } else {
            // Default values untuk new record
            $record['id_pos_item'] = 0;
            $record['item_barcode'] = '';
            $record['item_name'] = '';
            $record['item_description'] = '';
            $record['item_sku'] = '';
            $record['item_picture_cover'] = '';
            $record['id_pos_item_category'] = 0;
            $record['id_pos_uom'] = 0;
        }

        // Prepare data for view
        $data['record'] = $record;
        $data['http_referer'] = previous_url() ?? base_url();
        $data['session_file_hash'] = md5(session()->get('user_id') . time());
        $data['controllerName'] = $this->controllerName;
        $data['fieldStructure'] = $this->fieldStructure;

        // Dropdown data untuk view - nama variable sesuai dengan yang dipakai di view
        $data['pos_item_categories'] = $pos_item_categories;
        $data['pos_uoms'] = $pos_uoms; // View pakai 'pos_uoms'
        $data['pos_outlets'] = $outlet_list;
        $data['outlet_list'] = $outlet_list;
        $data['sale_types'] = $sale_types;

        // Return view file
        return view('product_item/form_edit', $data);
    }

    /**
     * Generate product form HTML
     * Temporary solution - ideally use Form library
     */
    protected function generateProductForm()
    {
        // Load models
        $mProductCategory = new MProductCategory();
        $mUom = new MUom();
        $mOutlet = new MOutlet();
        $mSaleType = new MSaleType();

        // Get dropdown data
        $arrProductCategory = $this->generateDropdownOptions(
            $mProductCategory,
            ['company_id' => $this->company_id, 'is_active' => 1, '_deleted' => ['$ne' => true]],
            'product_category_id',
            'category_name'
        );

        $arrUOM = $this->generateDropdownOptions(
            $mUom,
            ['company_id' => $this->company_id],
            'uom_id',
            'uom_code'
        );

        $arrOutlets = $this->generateDropdownOptions(
            $mOutlet,
            ['company_id' => $this->company_id],
            'outlet_id',
            'outlet_name'
        );

        // Get sale types
        $dataSaleType = $mSaleType->findAll();
        $arrSaleType = [];
        if ($dataSaleType) {
            foreach ($dataSaleType as $row) {
                $arrSaleType[] = $row;
            }
        }

        // Build form HTML
        $data = [
            'pk_id' => $this->pk_id,
            'formName' => $this->formName,
            'controllerName' => $this->controllerName,
            'arrProductCategory' => $arrProductCategory,
            'arrUOM' => $arrUOM,
            'arrOutlets' => $arrOutlets,
            'arrSaleType' => $arrSaleType
        ];

        return view('product_item/form_edit', $data);
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

    /**
     * Form untuk add/edit ingredient
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
            'product_code' // Will need to concatenate with product_name in view
        );

        $data = [
            'arrProducts' => $arrProducts
        ];

        return view('product_item/form_ingredient', $data);
    }

    /**
     * Form untuk add/edit addon
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
}
