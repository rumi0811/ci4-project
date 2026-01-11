<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MPaymentTypeGroup extends MyMongoModel
{
    public $fieldStructure = [
        'payment_type_group_id' => 'int',
        'payment_type_group' => 'string', //=> input type file, type data yg disimpan tetap string
        'sale_type_id' => 'int', //FK
        'sale_type' => 'string',
        'is_active' => 'boolean',
        'sequence_no' => 'int',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',
    ];

    public function __construct()
    {
        parent::__construct("m_payment_type_group", "payment_type_group_id");
    }
}
