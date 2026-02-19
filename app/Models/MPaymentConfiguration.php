<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MPaymentConfiguration extends MyMongoModel
{
    public $fieldStructure = [
        'payment_configuration_id' => 'int', //PK
        'company_id' => 'int', //FK
        'payment_configuration' => 'string',

        'is_active' => 'boolean',
        'is_all_outlet' => 'boolean',
        'is_default' => 'boolean',
        'outlets' => 'array', //array of outlet_id
        'payment_types' => 'array', //array of payment_type_id, is_enable
        'taxes' => 'array', //array of tax_id, is_enable
        'sequence_no' => 'int',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',
    ];

    public function __construct()
    {
        parent::__construct("m_payment_configuration", "payment_configuration_id");
    }
}
