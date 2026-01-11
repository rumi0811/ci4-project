<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MUom extends MyMongoModel
{
    public $fieldStructure = [
        'uom_id' => 'int', //PK
        'company_id' => 'int', //FK
        'uom_code' => 'string',
        'description' => 'string',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',
    ];

    public function __construct()
    {
        parent::__construct("m_uom", "uom_id");
    }
}
