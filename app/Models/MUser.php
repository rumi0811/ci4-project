<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MUser extends MyMongoModel
{
    public $fieldStructure = [
        'user_id' => 'int', //PK
        'company_id' => 'int', //FK
        'username' => 'string',
        'profile_picture' => 'file',
        'pwd' => 'string',
        'name' => 'string',
        'email' => 'string',
        'mobile' => 'string',
        'address' => 'string',
        'user_type_id' => 'int',

        'token' => 'string',
        'timestamp' => 'int',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',
    ];

    public function __construct()
    {
        parent::__construct("m_user", "user_id");
    }

    public function authentication($username, $pass)
    {
        if ($dataUser = $this->findByUsernameAndPwd($username, md5($pass))) {
            $mCompany = new \App\Models\MCompany();
            if ($dataCompany = $mCompany->findByCompanyId($dataUser['company_id'])) {
                $dataUser['company_name'] = $dataCompany['company_name'];
                $dataUser['company_address'] = isset($dataCompany['address']) ? $dataCompany['address'] : '';
                $dataUser['company_image_logo'] = isset($dataCompany['image_logo']) ? $dataCompany['image_logo'] : '';
            }
        }
        return $dataUser;
    }

    public function authenticationUid($username, $google_id)
    {
        if ($dataUser = $this->findByUsernameAndGoogleId($username, $google_id)) {
            $mCompany = new \App\Models\MCompany();
            if ($dataCompany = $mCompany->findByCompanyId($dataUser['company_id'])) {
                $dataUser['company_name'] = $dataCompany['company_name'];
                $dataUser['company_address'] = isset($dataCompany['address']) ? $dataCompany['address'] : '';
                $dataUser['company_image_logo'] = isset($dataCompany['image_logo']) ? $dataCompany['image_logo'] : '';
            }
        }
        return $dataUser;
    }

    public function createTokenLogin($userId, $username, $password)
    {
        $today = strtotime(date("Y-m") . '-01');
        $arrUpdate = [
            'token' => md5($userId . "|" . $username . "|" . $today),
            'timestamp' => $today
        ];
        if ($this->update(["user_id" => intval($userId)], $arrUpdate)) {
            return $arrUpdate;
        }
        return null;
    }

    public function createTokenLoginWithDeviceId($userId, $username, $password, $loginFrom, $device_id)
    {
        $today = strtotime(date("Y-m-d H:i:s"));
        $arrUpdate = [
            'token' => md5($userId . "|" . $username . "|" . $today),
            'timestamp' => $today,
            //'login_from'=> $loginFrom,
            //"device_id" => $device_id
        ];
        $this->update(["user_id" => intval($userId)], $arrUpdate);
    }

    public function clearTokenLogin($userId)
    {
        $arrUpdate = [
            'token' => '',
            'timestamp' => ''
        ];
        $this->update(["user_id" => intval($userId)], $arrUpdate);
    }

    public function getCurrentToken($userId)
    {
        return $this->findByUserId(intval($userId), "token, timestamp");
    }

    public function findByUserId($userId, $fields = null)
    {
        $criteria = ['user_id' => intval($userId)];

        $results = $this->findAll($criteria, $fields, null, 1);

        if (!empty($results)) {
            foreach ($results as $row) {
                // Convert BSONDocument to array
                if ($row instanceof \MongoDB\Model\BSONDocument) {
                    $row = $row->getArrayCopy();  // ← CONVERT!
                } elseif (is_object($row)) {
                    $row = (array)$row;  // Fallback
                }

                // Add default values for ALL possibly missing fields
                $defaults = [
                    'pp_code' => '',
                    'mobile' => '',
                    'address' => '',
                    'device_id' => '',
                    'referral_user_id' => 0,
                    'email_verified' => 0,
                    'is_send_mail' => 0,
                    'is_account_verified' => 0,
                    'user_note' => '',
                    'telegram_id' => '',
                    'balance' => 0,
                    'price_id' => 0,
                    'email' => '',
                    'profile_picture' => '',
                    'id_card_image' => '',
                    'is_active' => 1,
                    'user_type_id' => PP_USER
                ];

                // Merge with defaults
                $row = array_merge($defaults, $row);

                return $row;
            }
        }

        return null;
    }

    public function getUniquePPCode()
    {
        $ppCodeHeader = date("ym") . "-";

        // Use existing findAll method with regex
        $criteria = [
            'pp_code' => ['$regex' => '^' . preg_quote($ppCodeHeader), '$options' => 'i']
        ];

        $results = $this->findAll($criteria, 'pp_code', 'pp_code DESC', 1);

        $num = 1;
        if (!empty($results)) {
            foreach ($results as $row) {
                if (isset($row['pp_code'])) {
                    $ppCode = (string)$row['pp_code'];
                    $num = str_replace($ppCodeHeader, "", $ppCode);
                    $num = intval($num) + 1;
                }
                break;
            }
        }

        // Same logic as CI3
        if ($num <= 9999) {
            return $ppCodeHeader . str_pad($num, 4, "0", STR_PAD_LEFT);
        } else {
            return $ppCodeHeader . $num;
        }
    }

    public function getUniqueReferralCode()
    {
        $total = 1;
        $trial = 0;
        $length = 8;
        while ($total > 0) {
            $trial++;
            $refCode = strtoupper(substr(md5(time()), 0, $length));
            $this->db->select("COUNT(*) AS total");
            $this->db->from($this->strTableName);
            $this->db->where("referral_code", $refCode);

            if ($query = $this->db->get()) {
                if ($row = $query->getRowArray()) {
                    $total = $row['total'];
                    if ($total == 0) {
                        return $refCode;
                    } else if ($trial > 5) {
                        $length++;
                        $trial = 0;
                    }
                }
            }
        }
        return "";
    }
}
