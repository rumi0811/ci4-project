<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MTable extends MyMongoModel
{
    public $fieldStructure = [
        'table_id' => 'int',
        'company_id' => 'int',
        'outlet_id' => 'int',
        'table_number' => 'string',
        'map_x' => 'number',
        'map_y' => 'number',
        'map_width' => 'number',
        'map_height' => 'number',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',
    ];

    public function __construct()
    {
        parent::__construct("m_table", "table_id");
    }
}
