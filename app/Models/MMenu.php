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
        // Load MongoDB library
        $mongoDb = new \App\Libraries\Mongo("default");

        ini_set('mongo.long_as_object', 1);
        $pipeline = [
            [
                '$group' => [
                    '_id' => null,
                    'menu_level' => ['$max' => '$menu_level']
                ],
            ]
        ];

        if ($cursor = $mongoDb->aggregateCursor('m_menu', $pipeline, 100)) {
            $cursor->timeout(400000);
            foreach ($cursor as $row) {
                return $row['menu_level'];
            }
        }
        return 0;
    }

    public function GetMaxSequence()
    {
        $mongoDb = new \App\Libraries\Mongo("default");

        ini_set('mongo.long_as_object', 1);
        $pipeline = [
            [
                '$group' => [
                    '_id' => null,
                    'sequence_no' => ['$max' => '$sequence_no']
                ],
            ]
        ];

        if ($cursor = $mongoDb->aggregateCursor('m_menu', $pipeline, 100)) {
            $cursor->timeout(400000);
            foreach ($cursor as $row) {
                return $row['sequence_no'];
            }
        }
        return 0;
    }

    public function GetMaxSequenceByMenuId($id)
    {
        $id = intval($id);
        $mongoDb = new \App\Libraries\Mongo("default");

        ini_set('mongo.long_as_object', 1);
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

        if ($cursor = $mongoDb->aggregateCursor('m_menu', $pipeline, 100)) {
            $cursor->timeout(400000);
            foreach ($cursor as $row) {
                return $row['sequence_no'];
            }
        }
        return [];
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
