<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MCurrency extends MyMongoModel
{
    public $fieldStructure = [
        'currency_id' => 'int', //PK
        'currency_code' => 'string',
        'currency_name' => 'string',
        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',
    ];

    public function __construct()
    {
        parent::__construct("m_currency", "currency_id");
    }
}
