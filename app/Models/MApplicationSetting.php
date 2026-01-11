<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MApplicationSetting extends MyMongoModel
{
    public $fieldStructure = [
        'application_setting_id' => 'int', //PK
        'date_format' => 'string',
        'cash_payment_type_group_id' => 'int', //link to payment_type_group_id of cash payment
        'cash_payment_type_id' => 'int', //link to payment_type_id of cash payment
    ];

    public function __construct()
    {
        parent::__construct("m_application_setting", "application_setting_id");
    }
}
