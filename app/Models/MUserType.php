<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MUserType extends MyMongoModel
{
    public $fieldStructure = [
        'user_type_id' => 'int', //PK
        'user_type' => 'string',
        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',
    ];

    public function __construct()
    {
        parent::__construct("m_user_type", "user_type_id");
    }
}
