<?php

namespace App\Models;

use App\Models\MyMongoModel;

class DGroupMenu extends MyMongoModel
{

    public $fieldStructure = array(
        'group_menu_id' => 'int',
        'group_id' => 'int',
        'menu_id' => 'int',
        'is_view' => 'boolean',
        'is_edit' => 'boolean',
        'is_delete' => 'boolean',
        'is_approve' => 'boolean',
    );

    public function __construct()
    {
        parent::__construct("d_group_menu", "group_menu_id");
    }
}
