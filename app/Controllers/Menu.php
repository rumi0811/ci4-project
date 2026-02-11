<?php

namespace App\Controllers;

use App\Controllers\MYController;
use App\Models\MMenu;

class Menu extends MYController
{
    protected $mMenu;

    public function __construct()
    {
        parent::__construct();

        // Load model
        $this->mMenu = new MMenu();

        // Set default currentPage
        $this->currentPage = [
            'menu_name' => 'Menu',
            'page_name' => 'Menu Management',
            'parent_menu_name' => 'Master Data',
            'parent_menu_file_name' => ''
        ];

        // TEMPORARY: Force privileges untuk testing
        $this->privilegeIndex = 1;
        $this->privilegeUpdate = 1;
        $this->privilegeDelete = 1;
        $this->privilegeApprove = 1;
    }

    public function index()
    {
        if ($this->loadPrivileges()) {
            $request = service('request');

            $data["menu_generate"] = $this->getTemporaryMenu();
            $data["currentPage"] = $this->currentPage;

            $data["message"] = session()->getFlashdata('message');
            $data["error_message"] = session()->getFlashdata('error_message');

            helper('smart_form');

            if ($request->getPost('btnSave')) {
                $record = $request->getPost();
                unset($record["btnSave"]);

                if (!isset($record["is_visible"])) $record["is_visible"] = 0;
                if (!isset($record["is_new_flag"])) $record["is_new_flag"] = 0;

                $record['menu_id'] = intval($record['menu_id']);
                $record['parent_menu_id'] = intval($record['parent_menu_id']);
                $record['is_visible'] = intval($record['is_visible']);

                $maxSequence = 0;
                $menuLevel = -1;

                if ($record['parent_menu_id'] == 0) {
                    $maxSequence = $this->mMenu->GetMaxSequence();
                    $menuLevel = -1;
                } else {
                    $maxSequence = $this->mMenu->GetMaxSequenceByMenuId($record['parent_menu_id']);
                    if ($dataParent = $this->mMenu->findByMenuId($record['parent_menu_id'])) {
                        $menuLevel = intval($dataParent['menu_level']);
                    }
                }
                $record['menu_level'] = intval($menuLevel) + 1;

                if ($record["menu_id"] > 0) {
                    //update
                    $oldData = $this->mMenu->findByMenuId($record["menu_id"], "parent_menu_id, menu_level");
                    $old_parent_menu = intval($oldData['parent_menu_id']);
                    $old_menu_level = intval($oldData['menu_level']);
                    $record['sequence_no'] = intval($record['sequence_no']);

                    if ($this->mMenu->update(array("menu_id" => $record["menu_id"]), $record)) {
                        if ($old_parent_menu != intval($record['parent_menu_id'])) {
                            $this->mMenu->update(
                                array("menu_id" => $record["menu_id"]),
                                array("sequence_no" => intval($maxSequence) + 1)
                            );
                        }

                        if ($old_menu_level != intval($record['menu_level'])) {
                            $this->mMenu->update(
                                ['parent_menu_id' => $record['menu_id']],
                                ['menu_level' => $record['menu_level'] + 1]
                            );

                            if ($arrDataChildren = $this->mMenu->findAllByParentMenuId($record['menu_id'], "menu_id")) {
                                foreach ($arrDataChildren as $rowChild) {
                                    $rowChild["menu_id"] = intval($rowChild["menu_id"]);
                                    $this->mMenu->update(
                                        array("parent_menu_id" => $rowChild["menu_id"]),
                                        array("menu_level" => $record['menu_level'] + 2)
                                    );

                                    if ($arrDataChildren2 = $this->mMenu->findAllByParentMenuId($rowChild['menu_id'], "menu_id")) {
                                        foreach ($arrDataChildren2 as $rowChild2) {
                                            $rowChild2["menu_id"] = intval($rowChild2["menu_id"]);
                                            $this->mMenu->update(
                                                array("parent_menu_id" => $rowChild2["menu_id"]),
                                                array("menu_level" => $record['menu_level'] + 3)
                                            );
                                        }
                                    }
                                }
                            }
                        }

                        $data["message"] = "Data saved";
                    } else {
                        $data["error_message"] = "Failed to save data";
                    }
                } else {
                    //insert
                    $record['sequence_no'] = intval($maxSequence) + 1;
                    unset($record["menu_id"]);

                    if ($this->mMenu->insert($record)) {
                        $data["message"] = "New data saved";
                    } else {
                        $data["error_message"] = "Failed to save new data";
                    }
                }
            } else if ($request->getPost('menu_id_delete')) {
                //delete
                if ($this->privilegeDelete == 0) {
                    $data["error_message"] = "You don't have delete permission";
                } else {
                    $menuIdDelete = intval($request->getPost("menu_id_delete"));
                    if ($this->mMenu->delete(array("menu_id" => $menuIdDelete))) {
                        $data["message"] = "Data deleted";
                    } else {
                        $data["error_message"] = "Failed to delete data";
                    }
                }
            }

            $data["menu_data"] = $this->getParentMenu();
            $font_awesome = getFontAwesomeCheatSheet();
            $data["font_awesome"] = array();
            foreach ($font_awesome as $fa) {
                $data["font_awesome"][$fa] = $fa;
            }

            $template_data["contents"] = view('menu/index', $data, ['saveData' => false]);
            return view('layout', $template_data);
        }
    }

    public function datatable()
    {
        $this->loadPrivileges(false, false);
        $request = service('request');

        if ($this->privilegeIndex == 0) {
            $arr = array(
                "sEcho" => intval($request->getPost("sEcho")) + 1,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array(),
            );
            return $this->response->setJSON($arr);
        }

        $actionTemplate = '
                <button class="btn btn-xs btn-success" onClick="javascript:editForm($1);"><i class="fal fa-edit"></i></button>&nbsp;
                <button id="smart_mod_delete$1" class="btn btn-xs btn-danger" onClick="deleteConfirm(event, $1)"><i class="fal fa-trash-alt"></i></button>';

        $sortBy = "";
        if ($request->getPost("iSortCol_0")) {
            $arrFields = array("action", "menu_name", "note", "sequence_no", "menu_level", "file_name", "is_visible");
            $sortBy = "," . $arrFields[$request->getPost("iSortCol_0")] . " " . $request->getPost("sSortDir_0");
        }

        $arrCriteria = [];
        $filter = "";
        if ($request->getPost("sSearch")) {
            $filter = $request->getPost("sSearch");
        }

        // Convert MongoDB cursor to array FIRST
        $dataset = $this->mMenu->findAll(null, null, "parent_menu_id, sequence_no" . $sortBy);

        // Convert cursor to array to allow multiple iterations
        $datasetArray = [];
        foreach ($dataset as $row) {
            $datasetArray[] = $row;
        }

        $totalRecord = count($datasetArray);

        $arrResult = [];
        $this->reorderMenu($datasetArray, 0, 0, $arrResult);

        $newDataset = array();
        if (count($arrResult) > 0) {
            foreach ($arrResult as $row) {
                if (!isset($row["icon_file"])) $row["icon_file"] = '';
                $breadcrumb = trim($row["parent_menu_name"] . " > " . $row["menu_name"], " > ");

                if ($filter != "") {
                    if (stripos($breadcrumb, $filter) === false && stripos($row["file_name"], $filter) === false) continue;
                }

                $row["action"] = str_replace("$1", $row["menu_id"], $actionTemplate);
                $row["menu_name"] = str_replace("$1", $row["menu_name"], $this->printMenu());
                $row["menu_name"] = str_replace("$2", $row["icon_file"], $row["menu_name"]);
                $row["menu_name"] = str_replace("$3", $row["menu_level"], $row["menu_name"]);

                $newDataset[] = array(
                    "action" => $row["action"],
                    "menu_name" => $row["menu_name"],
                    "note" => $breadcrumb,
                    "sequence_no" => $row["sequence_no"],
                    "menu_level" => $row["menu_level"],
                    "file_name" => $row["file_name"],
                    "is_visible" => $row["is_visible"]
                );
            }
        }

        $sEcho = $request->getPost("sEcho");
        if (!$sEcho) $sEcho = 0;

        $arr = array(
            "sEcho" => $sEcho,
            "iTotalRecords" => $totalRecord,
            "iTotalDisplayRecords" => count($newDataset),
            "aaData" => $newDataset
        );

        return $this->response->setJSON($arr);
    }

    private function printMenu()
    {
        $template = "<div class='menu-level-$3'><i class=\"$2\"></i> $1</div>";
        return $template;
    }

    public function edit($id = 0)
    {
        if ($this->loadPrivileges()) {
            $id = intval($id);

            if ($row = $this->mMenu->findByMenuId($id)) {
                // Data found
            } else {
                $row["error_message"] = "Data not found";
            }

            return $this->response->setJSON($row);
        }
    }

    private function restructureMenu($arrMenu, $id_menu = 0, $menu_level, &$arrResult)
    {
        $next_menu_level = $menu_level + 1;
        foreach ($arrMenu as $key => $value) {
            $value['parent_menu_id'] = intval($value['parent_menu_id']);
            // Ensure menu_id exists and convert to int
            if (!isset($value['menu_id'])) {
                continue; // Skip if menu_id not found
            }

            $value['menu_id'] = intval($value['menu_id']);
            $value['parent_menu_id'] = intval($value['parent_menu_id']);
            $value['menu_level'] = intval($value['menu_level']);

            if ($value['menu_level'] == $menu_level && $value['parent_menu_id'] == $id_menu) {
                $dashes = "";
                for ($i = 0; $i < $value['menu_level']; $i++)
                    if ($i == 0) $dashes = "&#9492;&#9472;&nbsp;";
                    else $dashes = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $dashes;

                if ($menu_level == 0)
                    $arrResult[] = array("value" => $value['menu_id'], "text" => $value['menu_name'], "selected" => false);
                else
                    $arrResult[] = array("value" => $value['menu_id'], "text" => $dashes . $value['menu_name'], "selected" => false);

                $this->restructureMenu($arrMenu, intval($value['menu_id']), $next_menu_level, $arrResult);
            }
        }
        return $arrResult;
    }

    private function getParentMenu()
    {
        $arrResult = array();
        $arrResult[] = array("value" => "0", "text" => "[Main Menu - No Parent]");

        // Convert cursor to array FIRST
        $dataset = $this->mMenu->findAll(null, null, "menu_level, sequence_no");
        $arrMenu = [];
        foreach ($dataset as $row) {
            $arrMenu[] = $row;
        }

        $this->restructureMenu($arrMenu, 0, 0, $arrResult);

        $arrList = array();
        foreach ($arrResult as $data) {
            $arrList[$data["value"]] = $data["text"];
        }
        return $arrList;
    }

    //recursively re-order the menu
    private function reorderMenu($arrMenu, $id_menu = 0, $menu_level, &$arrResult)
    {
        $next_menu_level = $menu_level + 1;
        foreach ($arrMenu as $key => $value) {
            if ($value['menu_level'] == $menu_level && $value['parent_menu_id'] == $id_menu) {
                if ($value['parent_menu_id'] > 0) {
                    $value['parent_menu_name'] = trim($arrResult[$value['parent_menu_id']]["parent_menu_name"] . " > " . $arrResult[$value['parent_menu_id']]["menu_name"], " > ");
                } else {
                    $value['parent_menu_name'] = "";
                }
                $arrResult[$value["menu_id"]] = $value;
                $this->reorderMenu($arrMenu, $value['menu_id'], $next_menu_level, $arrResult);
            }
        }
        return $arrResult;
    }
}
