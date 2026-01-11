<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MSaleType extends MyMongoModel
{
    public $fieldStructure = [
        'sale_type_id' => 'int',
        //'company_id' => 'int', //tidak ada company_id karena ini master data utk global company
        'sale_type' => 'string', //=> input type file, type data yg disimpan tetap string
        'is_active' => 'boolean',
        'sequence_no' => 'int',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',
    ];

    public function __construct()
    {
        parent::__construct("m_sale_type", "sale_type_id");
    }
}
