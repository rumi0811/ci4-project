<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MOutlet extends MyMongoModel
{
    public $fieldStructure = [
        'outlet_id' => 'int', //PK
        'client_id' => 'string', //ID dari RxDb client
        'company_id' => 'int', //FK
        'outlet_logo' => 'file',
        'outlet_name' => 'string',
        'outlet_address' => 'string',
        'phone_number' => 'string',
        'alt_phone_number' => 'string',
        'city' => 'string',
        'province' => 'string',
        'country' => 'string',
        'post_code' => 'string',
        'note' => 'string',
        'is_active' => 'string',
        'room_or_table_map' => 'file',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',

        '_deleted' => 'boolean',
    ];

    public function __construct()
    {
        parent::__construct("m_outlet", "outlet_id");
    }
}
