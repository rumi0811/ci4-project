<?php

namespace App\Models;

use App\Models\MyMongoModel;

class DUserGroup extends MyMongoModel
{
    public function __construct()
    {
        parent::__construct("d_user_group", "user_group_id");
    }
}
