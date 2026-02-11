<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MMenu extends MyMongoModel
{
    public function __construct()
    {
        parent::__construct("m_menu", "menu_id");
    }

    public function GetMaxLevel()
    {
        $mongoDb = new \App\Libraries\Mongo("default");

        $pipeline = [
            [
                '$group' => [
                    '_id' => null,
                    'menu_level' => ['$max' => '$menu_level']
                ],
            ]
        ];

        $cursor = $mongoDb->aggregate('m_menu', $pipeline);

        // Convert cursor to array
        $result = [];
        foreach ($cursor as $row) {
            $result[] = $row;
        }

        if (!empty($result)) {
            return $result[0]['menu_level'] ?? 0;
        }

        return 0;
    }

    public function GetMaxSequence()
    {
        $mongoDb = new \App\Libraries\Mongo("default");

        $pipeline = [
            [
                '$group' => [
                    '_id' => null,
                    'sequence_no' => ['$max' => '$sequence_no']
                ],
            ]
        ];

        $cursor = $mongoDb->aggregate('m_menu', $pipeline);

        // Convert cursor to array
        $result = [];
        foreach ($cursor as $row) {
            $result[] = $row;
        }

        if (!empty($result)) {
            return $result[0]['sequence_no'] ?? 0;
        }

        return 0;
    }

    public function GetMaxSequenceByMenuId($id)
    {
        $id = intval($id);
        $mongoDb = new \App\Libraries\Mongo("default");

        $pipeline = [
            [
                '$match' => ['parent_menu_id' => $id],
            ],
            [
                '$group' => [
                    '_id' => null,
                    'sequence_no' => ['$max' => '$sequence_no'],
                ],
            ]
        ];

        $cursor = $mongoDb->aggregate('m_menu', $pipeline);

        // Convert cursor to array
        $result = [];
        foreach ($cursor as $row) {
            $result[] = $row;
        }

        if (!empty($result)) {
            return $result[0]['sequence_no'] ?? 0;
        }

        return 0;
    }

    public function findCountByMenuName($keyword)
    {
        if ($keyword == "") {
            $strCriteria = "";
        } else {
            $strCriteria = " WHERE UPPER(menu_name) LIKE UPPER('%$keyword%') ";
        }
        return $this->findCount($strCriteria);
    }
}
