<?php

namespace App\Controllers;

use App\Controllers\MYController;
use App\Models\MGroup;
use App\Models\MMenu;
use App\Models\DGroupMenu;
use App\Models\MCompany;
use App\Models\MUser;
use App\Models\DUserGroup;

class Group extends MYController
{
    protected $formName = 'form1';
    protected $PKField = 'group_id';
    protected $mGroup;
    protected $mMenu;
    protected $dGroupMenu;
    protected $mCompany;
    protected $mUser;
    protected $dUserGroup;
    protected $datagrid;

    public function __construct()
    {
        parent::__construct();

        // Load models
        $this->mGroup = new MGroup();
        $this->mMenu = new MMenu();
        $this->dGroupMenu = new DGroupMenu();
        $this->mCompany = new MCompany();
        $this->mUser = new MUser();
        $this->dUserGroup = new DUserGroup();

        // Load library
        $this->datagrid = new \App\Libraries\DatagridMongo();

        // Set default currentPage
        $this->currentPage = [
            'menu_name' => 'Group',
            'page_name' => 'Group Management',
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

            //$this->datagrid->catchRequestData(array('formOutlet', 'dataName', 'id_pos_item_category'));
            $this->datagrid->caption = 'Datatable: <span class="fw-300"><i>Group</i></span>';
            $this->datagrid->addColumnNumbering();
            $this->datagrid->enableOrdering = true;
            $this->datagrid->showSearchFilter = false;
            $this->datagrid->showPaging = true;
            $this->datagrid->defaultOrder = array('dataIndex' => 'group_code', 'dir' => 'asc');

            if ($this->privilegeUpdate) {
                $this->datagrid->addRowButtonEdit($this->PKField, 'javascript', 'editForm(data.' . $this->PKField . ');');
            }
            if ($this->privilegeDelete) {
                $this->datagrid->addRowButtonDelete($this->PKField, 'm_group');
            }

            $this->datagrid->addActionColumn(
                'Action',
                $this->PKField,
                '',
                array('colProperties' => "class: 'text-center', width: '120px'")
            );

            $actionButton = '<a class="btn btn-xs btn-warning text-white" onClick="javascript:doOpenPrivilege(\' + data + \')"><i class="fal fa-list"></i> Access</a>';
            //$actionButton .= '<a class="btn btn-xs btn-success btnShare"  onClick="javascript:doShare(\' + data + \')"><i class="fal fa-share"></i> Share</a>';
            $this->datagrid->addColumLink(
                'Privileges',
                $this->PKField,
                $actionButton,
                array('colProperties' => "class: 'text-center', width: '90px'")
            );

            $this->datagrid->addColumn(array(
                "title" => "Group Code",
                'dataIndex' => 'group_code',
                'type' => 'string',
                'colProperties' => "width: '150px'"
            ));
            $this->datagrid->addColumn(array(
                "title" => "Group Name",
                'dataIndex' => 'group_name',
                'type' => 'string',
                'colProperties' => ""
            ));
            $this->datagrid->addColumn(array(
                "title" => "Group Type",
                'dataIndex' => 'group_type',
                'type' => 'string',
                'colProperties' => "width: '180px'",
                'colRenderer' => 'return printGroupType(data);'
            ));
            $this->datagrid->addColumn(array(
                "title" => "Active?",
                'dataIndex' => 'is_active',
                'type' => 'string',
                'colProperties' => "width: '70px', class: 'text-center'",
                'colRenderer' => 'if (data == 1) return "Yes"; else if (data == 0) return "No"; else return "";'
            ));

            $actionButton = '<a class="btn btn-xs btn-success" onClick="javascript:doOpenMember(\' + data + \')"><i class="fal fa-list"></i> Set User</a>';
            //$actionButton .= '<a class="btn btn-xs btn-success btnShare"  onClick="javascript:doShare(\' + data + \')"><i class="fal fa-share"></i> Share</a>';
            $this->datagrid->addColumLink(
                'Members',
                'group_id',
                $actionButton,
                array(
                    'colProperties' => "class: 'text-center', width: '120px'",
                    'colRenderer' => 'return printActionMembers(data, type, row);'
                )
            );

            $this->datagrid->addColumnFilter('group_code', 'text');
            $this->datagrid->addColumnFilter('group_name', 'text');
            $this->datagrid->addColumnFilter('group_type', 'select', array("" => 'All', 0 => 'System Admin', 1 => 'Internal Group', 2 => 'External/Customer', 3 => 'Reseller'));
            $this->datagrid->addColumnFilter('is_active', 'select', array("" => 'All', 0 => 'No', 1 => 'Yes'));

            if ($this->privilegeUpdate) {
                //$this->datagrid->actionButtonPosition = "top";
                $this->datagrid->addButton("btnAddNew", "btnAddNew", "button", "<i class='fal fa-plus'></i> Add New Group", "javascript:editForm(0)", "", "btn btn-primary");
            }

            if (isset($_GET['ajaxDataGrid1'])) {
                // update log finish load datagrid

                // Get MongoDB connection for counting total users
                $this->datagrid->bindTable("m_group");
                $arrResult = &$this->datagrid->dataset['data'];
                foreach ($arrResult as &$row) {
                    // Temporary: skip counting users
                    $row['total_user'] = 0;
                }
                unset($row);
            }

            $data["grid"] = $this->datagrid->generate();
            $data["form"] = $this->createForm();
            $data['fieldStructure'] = $this->mGroup->fieldStructure;
            $data['formName'] = $this->formName;

            $template_data["contents"] = view('group/index', $data, ['saveData' => false]);
            return view('layout', $template_data);
        }
    }


    private function createForm()
    {
        $fieldStructure = $this->mGroup->fieldStructure;

        $arrData = array();
        foreach ($fieldStructure as $key => $val) {
            if ($val == 'int' || $val == 'boolean') {
                $arrData[$key] = 0;
            } else {
                $arrData[$key] = '';
            }
        }

        $form = new \App\Libraries\Form(array('action' => 'group/save_data', 'id' => $this->formName));
        $form->isFormOnly = true;
        $form->caption = 'Add/Edit Group';
        $form->addHidden($this->PKField, $arrData[$this->PKField]);

        // TAMBAHKAN INI - Hidden field untuk submit button
        //$form->addHidden('submitButton_' . $this->formName, '1');

        //$form->addFieldSet('', 1);

        $form->addInput('Group Code', 'group_code', $arrData['group_code'], array(), "string", true, true, "", "", true, 3, '');
        $form->addInput('Group Name', 'group_name', $arrData['group_name'], array(), "string", true, true, "", "", true, 6, '');

        $arrGroupTypes = array("0" => "Internal", "1" => "External");
        $form->addSelect("Group Type", 'group_type', $arrGroupTypes, $arrData['group_type'], array(), true, true, "", "", true, 3, '');

        $arrCompanies = $this->mCompany->generateListCI(null, "company_name", null, "company_id", "company_name", true,  array(0 => "All Companies"));

        $form->addSelect("Company", 'company_id', $arrCompanies, $arrData['company_id'], array(), true, true, "", "", true, 12, '');

        $form->addCheckBox('', 'is_active', array('1' => 'Is Active'), '', array(), false, true, "", "");

        $form->addButton('btnSave', 'Save ', array(), true, "", "", "");

        $form->addReset('btnCancel', 'Cancel', array('data-dismiss' => "modal"), true, "", "", "");

        return $form->render();
    }

    // public function datatable()
    // {
    //     $this->loadPrivileges(false, false);
    //
    //     if($this->privilegeIndex == 0) {
    //         $arr = array(
    //                 "sEcho" => intval($request->getGet("sEcho")) + 1,
    //                 "iTotalRecords" => 0,
    //                 "iTotalDisplayRecords" => 0,
    //                 "aaData" => array(),
    //         );
    //         return $this->response->setJSON($arr);
    //     }
    //     $actionTemplate = '
    //             <button class="btn btn-xs btn-warning" onClick="javascript:location.href=\''.base_url().'group/privilege/$1\';" title="Setup Permission"><i class="fal fa-list"></i></button>&nbsp;
    //             <button class="btn btn-xs btn-success" onClick="javascript:editForm($1);" title="Edit"><i class="fal fa-edit"></i></button>&nbsp;
    //             <button id="smart_mod_delete$1" class="btn btn-xs btn-danger" onClick="deleteConfirm(event, $1)" title="Delete"><i class="fal fa-trash-alt"></i></button>';
    //     $actionTemplate2 = '
    //             <button class="btn btn-xs btn-warning" onClick="javascript:location.href=\''.base_url().'group/member/$1\';" title="Set User"><i class="fal fa-user-alt"></i> Set User ($2)</button>';
    //
    //     $datatables = new \App\Libraries\Datatables();
    //
    //     $datatables->select('m_group.group_id AS group_id, group_code, group_name, 
    //     CASE WHEN group_type = 0 THEN \'Internal\' ELSE \'External\' END AS group_type, m_group.is_active, 
    //     CASE WHEN m_company.name IS NULL THEN \'All Companies\' ELSE m_company.name END AS company_name,
    //     total_user', false)
    //     ->add_column('action', $actionTemplate, 'group_id')
    //     ->add_column('action2', $actionTemplate2, 'group_id,total_user')
    //     ->from('m_group')
    //     ->join('m_company', 'm_group.company_id = m_company.company_id', 'left')
    //     ->join('(SELECT group_id, COUNT(*) AS total_user FROM d_user_group GROUP BY group_id) AS t1', 't1.group_id=m_group.group_id', 'left');
    //
    //     return $this->response->setJSON($datatables->generate());
    //
    // }


    public function edit($id = 0)
    {
        $checkAccess = $this->loadPrivileges(false, false);
        if ($checkAccess) {
            $id = intval($id);
            if ($row = $this->mGroup->find($this->PKField . " = " . $id)) {
            } else {
                $row["error_message"] = "Data not found";
            }
            return $this->response->setJSON($row);
        } else {
            $dataError["error_message"] = "You don't have privilege to access this page";
            return $this->response->setJSON($dataError);
        }
    }

    public function save_data()
    {
        $request = service('request');

        if ($request->getPost('submitButton_' . $this->formName)) {
            //save
            $record = array();
            foreach ($this->mGroup->fieldStructure as $key => $val) {
                if ($request->getPost($key) !== null) {
                    $record[$key] = $request->getPost($key);
                    if ($val == 'int' || $val == 'boolean') {
                        $record[$key] = intval($record[$key]);
                    } else if ($val == 'date' || $val == 'datetime') {
                        $record[$key] = convertClientDateToISO($record[$key]);
                    }
                }
            }

            $result = array();
            if ($record[$this->PKField] > 0) {
                //update
                $record["modified"] = date("Y-m-d H:i:s");
                $record["modified_by"] = session()->get('user_id');
                if ($this->mGroup->update(array($this->PKField => $record[$this->PKField]), $record)) {
                    $result["message"] = "Data saved";
                } else {
                    $result["error_message"] = "Failed to save data";
                }
            } else {
                //insert
                $record["created"] = date("Y-m-d H:i:s");
                $record["created_by"] = session()->get('user_id');
                unset($record[$this->PKField]);
                if ($record[$this->PKField] = $this->mGroup->insert($record)) {
                    $id = $record[$this->PKField];
                    $result["message"] = "New data saved";
                } else {
                    $result["error_message"] = "Failed to save new data";
                }
            }
        } else {
            $result['error_message'] = 'Failed to save';
        }
        return $this->response->setJSON($result);
    }

    // public function edit($id = 0)
    // {
    //     if ($this->loadPrivileges())
    //     {
    //         $request = service('request');
    //         $id = intval($id);
    //         $data["menu_generate"] = $this->getTemporaryMenu();
    //         $data["currentPage"] = $this->currentPage;
    //         helper("smart_form");
    //
    //         if (!$request->getPost("http_referer"))
    //         {
    //             if (isset($_SERVER['HTTP_REFERER']))
    //                 $data["http_referer"] = $_SERVER['HTTP_REFERER'];
    //             else
    //                 $data["http_referer"] = "group";
    //         }
    //         else
    //         {
    //             $data["http_referer"] = $request->getPost("http_referer");
    //         }
    //         $data["message"] = session()->getFlashdata('message');
    //         $data["error_message"] = session()->getFlashdata('error_message');
    //
    //
    //         if ($request->getPost("btnCancel"))
    //         {
    //             $redirectUrl = str_replace(base_url(), '', $data["http_referer"]);
    //             return redirect()->to($redirectUrl);
    //         }
    //         else if ($request->getPost("btnSave") && $request->getPost("btnSave") == "1")
    //         {
    //             //save
    //             $record = $request->getPost();
    //             unset($record['btnSave']);
    //             unset($record['btnCancel']);
    //             unset($record['http_referer']);
    //             if (!isset($record['is_active'])) $record['is_active'] = 0;
    //             $record['group_id'] = intval($record['group_id']);
    //             $record['group_type'] = intval($record['group_type']);
    //             if ($record['group_id'] > 0)
    //             {
    //                 //update
    //                 $record["modified"] = date("Y-m-d H:i:s");
    //                 $record["modified_by"] = session()->get('user_id');
    //                 if ($this->mGroup->update(array("group_id" => $record["group_id"]), $record))
    //                 {
    //                     $data["message"] = "Data saved";
    //                 }
    //                 else
    //                 {
    //                     $data["error_message"] = "Failed to save data";
    //                 }
    //             }
    //             else {
    //                 //insert
    //                 $record["created"] = date("Y-m-d H:i:s");
    //                 $record["created_by"] = session()->get('user_id');
    //                 unset($record["group_id"]);
    //                 if ($record["group_id"] = $this->mGroup->insert($record))
    //                 {
    //                     $id = $record["group_id"];
    //                     $data["message"] = "New data saved";
    //                 }
    //                 else
    //                 {
    //                     $data["error_message"] = "Failed to save new data";
    //                 }
    //             }
    //         }
    //
    //         if ($id == 0)
    //         {
    //             $data['title'] = "Add Group";
    //             $record = array ( "group_id" => 0, "group_code" => "",  "group_name" => "",
    //                     "group_type" => 0,
    //                     "is_active" => 1);
    //         }
    //         else
    //         {
    //             $data['title'] = "Edit Group";
    //             $record = $this->mGroup->findByGroupId($id);
    //         }
    //         $data["record"] = $record;
    //         $data["companies"] = $this->mCompany->generateListCI(null, "name", null, "company_id", "name", true,  array(0 => "All Companies"));
    //         $template_data["contents"] = view('group/edit', $data, ['saveData' => false]);
    //         return view('layout', $template_data);
    //     }
    // }


    public function privilege($group_id = 0)
    {
        if ($this->loadPrivileges()) {
            $data["menu_generate"] = $this->getTemporaryMenu();
            $data["currentPage"] = $this->currentPage;

            $data["message"] = session()->getFlashdata('message');
            $data["error_message"] = session()->getFlashdata('error_message');

            $data['currentPage']['menu_name'] = "Group Privileges Setting";
            $data['currentPage']['page_name'] = "Group Privileges Setting";

            $maxLevel = $this->mMenu->GetMaxLevel();
            if ($data['menu'] = $this->mGroup->getGroupMenu($group_id)) {
                $resultmenu = $this->_buildmenu($data['menu']);

                $list = '<table id="DataGrid1" class="table table-striped table-bordered smart-form" width="100%">';
                $list .= $this->_cetakHeader($maxLevel);
                $n = 1;
                $list .= "<tbody>";
                $lastId = '';
                $lastIdEdit = '';
                $lastIsdel = '';
                $lastIsApprove = '';
                foreach ($resultmenu as $value) {
                    $isviewc = ($value['is_view'] == 1) ? "checked=''" : "";
                    $iseditc = ($value['is_edit'] == 1) ? "checked=''" : "";
                    $isdeletec = ($value['is_delete'] == 1) ? "checked=''" : "";
                    if (!isset($value['is_approve'])) $value['is_approve'] = 0;
                    $isapprovec = ($value['is_approve'] == 1) ? "checked=''" : "";
                    if ($value['parent_menu_id'] == 0) {
                        $list .= '<tr><td>' . $n . '</td>';

                        $isView = 'view_' . $value['menu_id'];
                        $isEdit = 'edit_' . $value['menu_id'];
                        $isDel = 'delete_' . $value['menu_id'];
                        $isApprove = 'approve_' . $value['menu_id'];

                        $list .= '<td><input ' . $isviewc . ' type="checkbox" id="' . $isView . '" class="view_' . $value['menu_id'] . '" onclick="checkPriv(this)" name="view_' . $value['menu_id'] . '"  /></td>';
                        $list .= '<td><input ' . $iseditc . ' type="checkbox" id="' . $isEdit . '" class="edit_' . $value['menu_id'] . '" onclick="checkPriv(this)" name="edit_' . $value['menu_id'] . '" /></td>';
                        $list .= '<td><input ' . $isdeletec . ' type="checkbox" id="' . $isDel . '" class="delete_' . $value['menu_id'] . '" onclick="checkPriv(this)" name="delete_' . $value['menu_id'] . '" /></td>';
                        $list .= '<td><input ' . $isapprovec . ' type="checkbox" id="' . $isApprove . '" class="approve_' . $value['menu_id'] . '" onclick="checkPriv(this)" name="approve_' . $value['menu_id'] . '" /></td>';

                        $colspan = $maxLevel + 1;
                        $list .= '<td><i class="fal fa-fw ' . $value['icon_file'] . '"></i> <strong>' . $value['menu_name'] . '</strong></td>';

                        //echo $colspans;
                        $list .= '<td><strong>' . $value['note'] . '</strong></td>';
                        $list .= '<td><strong>' . $value['file_name'] . '</strong></td>';

                        $list .= '</tr>' . "\r\n";
                        $lastId = $isView;
                        $lastIdEdit = $isEdit;
                        $lastIsdel = $isDel;
                        $lastIsApprove = $isApprove;
                        $n++;
                    } else {
                        $isView = $lastId . '_' . $value['parent_menu_id'];
                        $isEdit = $lastIdEdit . '_' . $value['parent_menu_id'];
                        $isDel = $lastIsdel . '_' . $value['parent_menu_id'];
                        $isApprove = $lastIsApprove . '_' . $value['parent_menu_id'];

                        $list .= '<tr  ><td>&nbsp;</td>';
                        $list .= '<td><input ' . $isviewc . ' type="checkbox" onclick="checkPriv(this)" name="view_' . $value['menu_id'] . '" id="' . $isView . '" class="child_view_checked_' . $value['parent_menu_id'] . '" /></td>';
                        $list .= '<td><input ' . $iseditc . ' type="checkbox" onclick="checkPriv(this)" name="edit_' . $value['menu_id'] . '" id="' . $isEdit . '" class="child_edit_checked_' . $value['parent_menu_id'] . '"/></td>';
                        $list .= '<td><input ' . $isdeletec . ' type="checkbox" onclick="checkPriv(this)" name="delete_' . $value['menu_id'] . '" id="' . $isDel . '" class="child_del_checked_' . $value['parent_menu_id'] . '" /></td>';
                        $list .= '<td><input ' . $isapprovec . ' type="checkbox" onclick="checkPriv(this)" name="approve_' . $value['menu_id'] . '" id="' . $isApprove . '" class="child_approve_checked_' . $value['parent_menu_id'] . '" /></td>';

                        if (!isset($value['icon_file'])) $value['icon_file'] = '';

                        if ($value['menu_level'] != 0) {
                            $menuName = "";
                            for ($level = 1; $level <= $value['menu_level']; $level++) {
                                if ($level == $value['menu_level'])
                                    $menuName .= '&#9492;&#9472;';
                                else
                                    $menuName .= '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;';
                                //$list .= " <td>&#9492;&#9472;</td>";
                            }
                            $colspans = $maxLevel - $value['menu_level'] + 1;

                            if ($colspans > 1) {
                                if ($colspans == 2) {
                                    $list .= "  <td>" . $menuName . "&nbsp;<i class=\"fal fa-fw " . $value['icon_file'] . "\"></i> <span class=\"text-primary\"><em>" . $value['menu_name'] . "</em></span></td>";
                                } else {
                                    $list .= "  <td>" . $menuName . "&nbsp;<i class=\"fal fa-fw " . $value['icon_file'] . "\"></i> <span class=\"text-success\">" . $value['menu_name'] . "</span></td>";
                                }
                            } else {
                                $list .= "  <td><i class=\"fal fa-fw " . $value['icon_file'] . "\"></i> " . $value['menu_name'] . "</td>";
                            }
                        }
                        $list .= '<td>' . trim($value["parent_menu_name"] . " > " . $value["menu_name"], " > ") . '</td>';
                        $list .= '<td>' . $value['file_name'] . '</td>';

                        $list .= '</tr>' . "\r\n";
                    }
                }
                $list .= "</tbody>";
                $list .= '</table>';
                $data['list'] = $list;
                $data['group_id'] = $group_id;

                $template_data["contents"] = view('group/privilege', $data, ['saveData' => false]);
                return view('layout', $template_data);
            } else {
                return redirect()->to(base_url() . "group");
            }
        }
    }


    public function _buildmenu($mn)
    {
        $arrresult = array();
        $this->_reorderMenu($mn, 0, 0, $arrresult);
        return $arrresult;
    }

    public function _reorderMenu($mn, $idmenu = "", $menulevel, &$arrResult)
    {

        $nxmenuLevel = $menulevel + 1;
        foreach ($mn as $value) {
            if ($value['menu_level'] == $menulevel && $value['parent_menu_id'] == $idmenu) {
                if ($value['parent_menu_id'] > 0) {
                    $value['parent_menu_name'] = trim($arrResult[$value['parent_menu_id']]["parent_menu_name"] . " > " . $arrResult[$value['parent_menu_id']]["menu_name"], " > ");
                } else {
                    $value['parent_menu_name'] = "";
                }
                $arrResult[$value['menu_id']] = $value;
                $this->_reorderMenu($mn, $value['menu_id'], $nxmenuLevel, $arrResult);
            }
        }
        return $arrResult;
    }

    function _cetakHeader($max)
    {
        $table = "<thead>
                <tr>
                <th>No</th>
                <th>View</th>
                <th>Edit</th>
                <th>Delete</th>
                <th>Approve</th>
                <th>Menu Name</th>
                <th>Breadcrumb / Navigation</th>
                <th>Function Name</th>
                </tr>
                </thead>";
        return $table;
    }

    public function save_privilege()
    {
        if ($this->loadPrivileges()) {
            $request = service('request');

            $group_id = intval($request->getPost('group_id'));
            $bolTrue = '1';
            $bolFalse = '0';
            $lastIndex = 'undefined';

            $this->dGroupMenu->delete(['group_id' => $group_id]);

            $postData = $request->getPost();
            foreach ($postData as $key => $value) {
                if (
                    substr($key, 0, 4) == 'view' || substr($key, 0, 4) == 'edit' ||
                    substr($key, 0, 6) == 'delete' || substr($key, 0, 7) == 'approve'
                ) {
                    //ambil ID setiap yang di substring

                    if (substr($key, 0, 7) == 'approve') $idx = substr($key, 8);
                    else if (substr($key, 0, 6) == 'delete') $idx = substr($key, 7);
                    else $idx = substr($key, 5);

                    if ($lastIndex == $idx) continue;
                    else
                        $lastIndex = $idx;

                    (isset($postData['view_' . $idx])) ? $viewValue = $bolTrue : $viewValue = $bolFalse;
                    (isset($postData['edit_' . $idx])) ? $editValue = $bolTrue : $editValue = $bolFalse;
                    (isset($postData['delete_' . $idx])) ? $deleteValue = $bolTrue : $deleteValue = $bolFalse;
                    (isset($postData['approve_' . $idx])) ? $approveValue = $bolTrue : $approveValue = $bolFalse;

                    $arrID = explode('_', $idx);

                    if (count($arrID) > 0) {
                        $menuId = $arrID[0];

                        $arrDataToInsert = [];
                        $arrDataToInsert['group_id'] = $group_id;
                        $arrDataToInsert['menu_id'] = intval($menuId);
                        $arrDataToInsert['is_view'] = intval($viewValue);
                        $arrDataToInsert['is_edit'] = intval($editValue);
                        $arrDataToInsert['is_delete'] = intval($deleteValue);
                        $arrDataToInsert['is_approve'] = intval($approveValue);
                        $this->dGroupMenu->insert($arrDataToInsert);
                    }
                }
            }
            session()->setFlashdata('message', 'Data has been saved');
            return redirect()->to('group/privilege/' . $group_id);
        }
    }



    public function member($id = 0)
    {
        if ($this->loadPrivileges()) {
            helper('smart_form');
            $request = service('request');

            $data["menu_generate"] = $this->getTemporaryMenu();
            $data["currentPage"] = $this->currentPage;

            if (!$request->getPost("http_referer")) {
                if (isset($_SERVER['HTTP_REFERER']))
                    $data["http_referer"] = $_SERVER['HTTP_REFERER'];
                else
                    $data["http_referer"] = "group";
            } else {
                $data["http_referer"] = $request->getPost("http_referer");
            }
            $data["message"] = session()->getFlashdata('message');
            $data["error_message"] = session()->getFlashdata('error_message');


            if ($request->getPost("btnCancel")) {
                return redirect()->to($data["http_referer"]);
            } else if ($request->getPost("btnSave") && $request->getPost("btnSave") == "1") {
                //save
                $record = $request->getPost();
                unset($record['btnSave']);
                unset($record['btnCancel']);
                unset($record['http_referer']);
                if ($record['group_id'] > 0) {
                    $bolSuccess = false;
                    if ($request->getPost("user_id")) {
                        $userId = $request->getPost("user_id");
                        $this->dUserGroup->delete(array("group_id" => $record['group_id'], "user_id" => $userId));
                        $this->dUserGroup->insert(array("group_id" => $record['group_id'], "user_id" => $userId));

                        // Get database to check affected rows
                        $db = \Config\Database::connect('default', false);
                        $bolSuccess = $db->affectedRows();
                    }
                    if ($bolSuccess) {
                        $data["message"] = "Data saved";
                    } else {
                        $data["error_message"] = "Failed to save data";
                    }
                }
            } else if ($request->getPost('user_id_delete') && $request->getPost('group_id_delete')) {
                //delete
                if ($this->privilegeDelete == 0) {
                    $data["error_message"] = "You don't have delete permission";
                } else {
                    $idToDelete = $request->getPost("user_id_delete");
                    $group_id_delete = $request->getPost("group_id_delete");
                    if ($this->dUserGroup->delete(array("user_id" => $idToDelete, "group_id" => $group_id_delete))) {
                        $data["message"] = "Data deleted";
                    } else {
                        $data["error_message"] = "Failed to delete data";
                    }
                }
            }

            $data['id'] = $id;
            $data['currentPage']['menu_name'] = "Set Group Members";
            $data['currentPage']['page_name'] = "Set Group Members";
            $record = $this->mGroup->findByGroupId($id);

            $data["users"] = $this->mUser->generateList(null, "user_id", null, "user_id", array("username", "name"), false,  array("value" => "0", "text" => "All"));
            $data["user_groups"] = $this->dUserGroup->findAllByGroupId($id);


            $data["record"] = $record;

            $template_data["contents"] = view('group/member', $data, ['saveData' => false]);
            return view('layout', $template_data);
        }
    }

    public function member_list($id = 0)
    {
        $request = service('request');

        $this->loadPrivilegesFromUri("group", "index", false);

        if ($this->privilegeIndex == 0) {
            $arr = array(
                "sEcho" => intval($request->getGet("sEcho")) + 1,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => array(),
            );
            return $this->response->setJSON($arr);
        }

        $strCriteria = [];
        $arrCriteria['group_id'] = intval($id);
        $arrUserIds = [];
        $arrCriteriaUser = [];
        if ($dataUserGroups = $this->dUserGroup->findAll($arrCriteria)) {
            foreach ($dataUserGroups as $row) {
                $arrUserIds[] = intval($row['user_id']);
            }
            $arrCriteriaUser['user_id']['$in'] = $arrUserIds;
        } else {
            $arrCriteriaUser['user_id'] = 0;
        }

        $actionTemplate = '
                <button class="btn btn-xs btn-danger" onClick="deleteConfirm(event, $1)"><i class="fal fa-trash-alt"></i></a>';
        $arrResult = [];
        if ($dataUsers = $this->mUser->findAll($arrCriteriaUser)) {
            foreach ($dataUsers as $row) {
                $row['action'] = str_ireplace('$1', $row['user_id'], $actionTemplate);
                $arrResult[] = $row;
            }
        }
        $arr = array(
            "sEcho" => intval($request->getGet("sEcho")) + 1,
            "iTotalRecords" => count($arrResult),
            "iTotalDisplayRecords" => count($arrResult),
            "aaData" => $arrResult,
        );
        return $this->response->setJSON($arr);
    }
}
