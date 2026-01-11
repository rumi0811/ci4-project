<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MCompany extends MyMongoModel
{
    public $fieldStructure = [
        'company_id' => 'int',
        'currency_id' => 'int', //FK
        'owner_name' => 'string',
        'company_name' => 'string',
        'company_logo' => 'file', //=> input type file, type data yg disimpan tetap string
        'address' => 'string',
        'currency_code' => 'string',
        'business_type_id' => 'int', //FK
        'business_type' => 'string',
        'is_active' => 'boolean',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',

        'updatedAt' => 'float', //timestamp
    ];

    public function __construct()
    {
        parent::__construct("m_company", "company_id");
    }
}
