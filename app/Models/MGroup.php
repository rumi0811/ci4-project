<?php

namespace App\Models;

use App\Models\MyMongoModel;

class MGroup extends MyMongoModel
{
    public $fieldStructure = [
        'group_id' => 'int',
        'company_id' => 'int',
        'group_code' => 'string',
        'group_name' => 'string',
        'group_type' => 'int',
        'is_active' => 'boolean',

        'created' => 'datetime',
        'created_by' => 'int',
        'modified' => 'datetime',
        'modified_by' => 'int',
    ];

    public function __construct()
    {
        parent::__construct("m_group", "group_id");
    }

    public function getGroupMenu($groupId)
    {
        $groupId = intval($groupId);
        if ($row = $this->findByGroupId($groupId)) {
            $mMenu = new \App\Models\MMenu();
            $dGroupMenu = new \App\Models\DGroupMenu();

            $arrGroupMenus = $dGroupMenu->findAllByGroupId($groupId);
            if ($arrGroupMenus) {
                foreach ($arrGroupMenus as $row) {
                    if (!isset($dataGroupMenu[$row['menu_id']])) {
                        $dataGroupMenu[$row['menu_id']] = $row;
                    } else {
                        if ($row['is_view'] == 1) $dataGroupMenu[$row['menu_id']]['is_view'] = $row['is_view'];
                        if ($row['is_edit'] == 1) $dataGroupMenu[$row['menu_id']]['is_edit'] = $row['is_edit'];
                        if ($row['is_delete'] == 1) $dataGroupMenu[$row['menu_id']]['is_delete'] = $row['is_delete'];
                        if ($row['is_approve'] == 1) $dataGroupMenu[$row['menu_id']]['is_approve'] = $row['is_approve'];
                    }
                }
            }
            $arrCriteria = [];
            $arrCriteria['is_visible'] = 1;
            if ($dataMenu = $mMenu->findAll($arrCriteria, null, 'sequence_no, parent_menu_id')) {
                foreach ($dataMenu as &$row) {
                    if (isset($dataGroupMenu[$row['menu_id']])) {
                        $row['is_view'] = $dataGroupMenu[$row['menu_id']]['is_view'];
                        $row['is_edit'] = $dataGroupMenu[$row['menu_id']]['is_edit'];
                        $row['is_delete'] = $dataGroupMenu[$row['menu_id']]['is_delete'];
                        if (!isset($dataGroupMenu[$row['menu_id']]['is_approve'])) $dataGroupMenu[$row['menu_id']]['is_approve'] = 0;
                        $row['is_approve'] = $dataGroupMenu[$row['menu_id']]['is_approve'];
                        $row['group_id'] = $dataGroupMenu[$row['menu_id']]['group_id'];
                    } else {
                        $row['is_view'] = 0;
                        $row['is_edit'] = 0;
                        $row['is_delete'] = 0;
                        $row['is_approve'] = 0;
                        $row['group_id'] = 0;
                    }
                }
                unset($row);
                return $dataMenu;
            }
        }
        return false;
    }

    public function getMaxMenuLevel()
    {
        $this->db->select("MAX(menu_level) AS maxlevel");
        $this->db->from("menus");
        $query = $this->db->get();

        return $query->getResultArray();
    }

    public function delete($id)
    {
        $dUserGroup = new \App\Models\DUserGroup();
        $dGroupMenu = new \App\Models\DGroupMenu();

        $dUserGroup->delete(['group_id' => $id]);
        $dGroupMenu->delete(['group_id' => $id]);

        return parent::delete(["group_id" => $id]);
    }
}
