<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MTax extends MyMongoModel
{
    public $fieldStructure = [
        'tax_id' => 'int', //PK
        //'company_id' => 'int', //tidak ada company_id karena ini master data utk global company
        'tax_code' => 'string',
        'description' => 'string',
        'tax_percentage' => 'float',
        'is_active' => 'boolean',
        'sequence_no' => 'int',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',
    ];

    public $descriptionField = 'tax_code';

    public function __construct()
    {
        parent::__construct("m_tax", "tax_id");
    }
}
