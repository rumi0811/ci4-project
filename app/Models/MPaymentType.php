<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MPaymentType extends MyMongoModel
{
    public $fieldStructure = [
        'payment_type_id' => 'int', //PK
        'company_id' => 'int', //FK
        'payment_type_group_id' => 'int', //FK
        'payment_type' => 'string',
        'is_active' => 'boolean',
        'sequence_no' => 'int',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',
    ];

    public function __construct()
    {
        parent::__construct("m_payment_type", "payment_type_id");
    }

    public function findAllByIsActive($isActive, $limit = null, $orderBy = null)
    {
        return $this->findAll(['is_active' => intval($isActive)], $limit, $orderBy);
    }
}
