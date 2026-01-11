<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MProductCategory extends MyMongoModel
{
    public $fieldStructure = [
        'product_category_id' => 'int',
        'client_id' => 'string', //ID dari RxDb client
        'server_id' => 'string', //ID dari Mongo ID
        'company_id' => 'int', //FK
        'category_code' => 'string', //=> input type file, type data yg disimpan tetap string
        'category_name' => 'string',
        'picture' => 'file',
        'note' => 'string',
        'is_active' => 'boolean',
        'sequence_no' => 'int',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',

        'updatedAt' => 'float', //timestamp
        '_deleted' => 'boolean',
    ];

    public function __construct()
    {
        parent::__construct("m_product_category", "product_category_id");
    }
}
