<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MProduct extends MyMongoModel
{
    public $fieldStructure = [
        'product_id' => 'int',
        'client_id' => 'string', //ID dari RxDb client
        'server_id' => 'string', //ID dari Mongo ID
        'company_id' => 'int', //FK
        'product_category_id' => 'int', //FK
        'uom_id' => 'int', //FK
        'picture' => 'file',
        'product_code' => 'string', //code/SKU
        'product_name' => 'string',
        'category_code' => 'string',
        'category_name' => 'string',
        'barcode' => 'string',
        'description' => 'string',
        'is_active' => 'boolean',
        'is_sale' => 'boolean',
        'is_addon' => 'boolean', //sales as add-on/kit
        'is_inventory' => 'boolean',
        'is_all_outlet' => 'boolean',
        'minimum_stock' => 'float',
        'maximum_stock' => 'float',

        'cogs_price' => 'float',
        //this is sale price of DINE-IN
        'sale_price' => 'float',

        //array of sale_type_id, sale_type, sale_price
        'sale_prices' => 'array',

        'uom_code' => 'string',
        'outlets' => 'array', //array of outlet_id
        'addons' => 'array',
        'ingredients' => 'array',

        'coa_code' => 'string',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',

        'updatedAt' => 'float', //timestamp
        '_deleted' => 'boolean',
    ];

    public function __construct()
    {
        parent::__construct("m_product", "product_id");
    }
}
