<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MCashier extends MyMongoModel
{
    public $fieldStructure = [
        'cashier_id' => 'int', //PK
        'client_id' => 'string', //ID dari RxDb client
        'company_id' => 'int', //FK
        'profile_picture' => 'file',
        'cashier_code' => 'string',
        'cashier_password' => 'string',
        'name' => 'string',
        'email' => 'string',
        'mobile' => 'string',
        'address' => 'string',
        'cashier_type_id' => 'int',
        'cashier_type' => 'int',
        'outlet_id' => 'int',
        'outlet_name' => 'string',

        'partner_id' => 'string',
        'token' => 'string',
        'timestamp' => 'int',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',
    ];

    public function __construct()
    {
        parent::__construct("m_cashier", "cashier_id");
    }

    public function createTokenLogin($cashierId)
    {
        $today = strtotime(date("Y-m") . '-01');
        $arrUpdate = [
            'partner_id' => md5($cashierId . "|IKONPOS"),
            'token' => md5($cashierId . "|" . $today),
            'timestamp' => $today
        ];
        if ($this->update(["cashier_id" => intval($cashierId)], $arrUpdate)) {
            return $arrUpdate;
        }
        return null;
    }
}
