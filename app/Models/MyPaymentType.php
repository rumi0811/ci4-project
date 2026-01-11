<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MPaymentType extends MyMongoModel
{
    public $fieldStructure = [
        'payment_type_id' => 'int', //PK
        //'company_id' => 'int', //tidak ada company_id karena ini master data utk global company
        'logo' => 'file', //FK
        'payment_type_group_id' => 'int', //FK
        'payment_type_group' => 'string',
        'payment_type' => 'string',
        'is_active' => 'boolean',
        'sequence_no' => 'int',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',
    ];

    public $descriptionField = 'payment_type';

    public function __construct()
    {
        parent::__construct("m_payment_type", "payment_type_id");
    }
}
