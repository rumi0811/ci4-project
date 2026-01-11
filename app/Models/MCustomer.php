<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MCustomer extends MyMongoModel
{
    public $fieldStructure = [
        'customer_id' => 'int', //PK
        'client_id' => 'string', //ID dari RxDb client
        'server_id' => 'string', //ID dari Mongo ID
        'company_id' => 'int', //tidak ada company_id karena ini master data utk global company
        'photo' => 'file',
        'customer_name' => 'string',
        'email_address' => 'string',
        'mobile_phone' => 'string',
        'address' => 'string',
        'register_date' => 'datetime',
        'expiry_date' => 'datetime',
        'note' => 'string',
        // 'membership_type' => 'int',

        'is_active' => 'boolean',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',

        'updatedAt' => 'float', //timestamp
        '_deleted' => 'boolean',
    ];

    public $descriptionField = 'customer_name';

    public function __construct()
    {
        parent::__construct("m_customer", "customer_id");
    }
}
