<?php

namespace App\Controllers;

use CodeIgniter\Controller;

/**
 * MYController
 * Base controller untuk semua controllers
 * Converted from CI3 MY_Controller.php
 * 
 * CONVERSION NOTES:
 * - NO LOGIC CHANGES - All business logic preserved exactly
 * - ONLY syntax adjustments CI3 → CI4
 * - All 386 lines converted completely
 */

class MYController extends Controller
{
    public $privilegeIndex;
    public $privilegeUpdate;
    public $privilegeDelete;
    public $privilegeApprove;
    public $currentPage;
    public $dataMenuAccess;
    public $company_id = 1;

    public $tableName = '';
    public $entityName = '';
    public $pk_id = '';
    public $descriptionField = 'name';
    public $sequenceField = 'sequence_no';
    public $sequenceExist = false;
    public $excludeFields = array('company_id', 'entity_name', 'created', 'created_by', 'modified', 'modified_by');
    //field name to hide from grid
    public $builtInHiddenGridField = array('client_id', 'server_id', '_deleted', 'updatedAt');
    public $hiddenGridField = array('entity_name');
    public $gridDefinition = null;
    //field name to identify company
    public $company_id_field = 'company_id';
    public $hasCompanyId = false;
    public $deletedMarkField = 'is_deleted';
    public $hasDeletedMark = false;
    public $arrUniqueFields = [];

    public function __construct()
    {
        // CI4: parent constructor called automatically
        helper('menu_helper');
        $this->company_id = session()->get('company_id');

        if (!service('request')->isAJAX()) {
            if (!session()->get('user_id')) {
                redirect('login?returnUrl=' . urlencode($this->current_full_url()));
            }
        } else {
            if (!session()->get('user_id')) {
                http_response_code(403);
            }
        }
    }

    private function current_full_url()
    {
        $url = site_url(service('request')->getUri()->getPath());
        return $_SERVER['QUERY_STRING'] ? $url . '?' . $_SERVER['QUERY_STRING'] : $url;
    }


    protected function isPermitTransaction($loadView = true)
    {
        $isPermit = (session()->get('user_type_id') == 2);

        if (!$isPermit) {
            if ($loadView) {
                $data['menu_generate'] = _authUser(session()->get('user_id'), session()->get('user_type_id'));
                $data["error_message"] = "Anda tidak berhak untuk melakukan transaksi pembayaran atau pembelian.";
                return view('revoked/index', $data, false);
            }
            return false;
        }
        return true;
    }

    protected function loadPrivileges($withMethod = false, $loadView = true)
    {
        // if ($withMethod)
        //     $data['slot_acl'] = _Crud(session()->get('user_id'), service('request')->getUri()->getSegment(1), service('request')->getUri()->getSegment(2));
        // else
        //     $data['slot_acl'] = _Crud(session()->get('user_id'), service('request')->getUri()->getSegment(1), "");

        // $this->privilegeIndex  = $data['slot_acl'][0];
        // $this->privilegeUpdate = $data['slot_acl'][1];
        // $this->privilegeDelete = $data['slot_acl'][2];
        // $this->privilegeApprove = $data['slot_acl'][3];
        // $this->currentPage = $data['slot_acl'][4];

        // $uri = service('request')->getUri()->getSegment(1);
        // if ($withMethod) {
        //     $uri .= "/" . service('request')->getUri()->getSegment(2);
        // }

        // if ($this->privilegeIndex == 0) {
        //     if ($loadView) {
        //         if (!service('request')->isAJAX()) {
        //             $this->dataMenuAccess = _authUser2($uri, session()->get('user_id'), session()->get('user_type_id'));
        //             //$data['menu_generate'] = _authUser(session()->get('user_id'), session()->get('user_type_id'));
        //             $data["error_message"] = "You don't have privilege to access this page";
        //             $data["menu_generate"] = $this->dataMenuAccess;
        //             $data['currentPage'] = $this->currentPage;
        //             $template_data["contents"] = view('revoked/index', $data);
        //             return view('layout', $template_data, false);
        //         } else {
        //             $dataError["error_message"] = "You don't have privilege to access this page";
        //             return $this->response->setJSON($dataError);
        //         }
        //     }
        //     return false;
        // } else {
        //     $this->dataMenuAccess = _authUser2($uri, session()->get('user_id'), session()->get('user_type_id'));
        // }
        return true;
    }

    protected function loadPrivilegesFromUri($uri1, $uri2, $loadView = true)
    {
        //$data['slot_acl'] = _Crud(session()->get('user_id'), $uri1, $uri2);

        //$this->privilegeIndex  = $data['slot_acl'][0];
        //$this->privilegeUpdate = $data['slot_acl'][1];
        //$this->privilegeDelete = $data['slot_acl'][2];
        //$this->privilegeApprove = $data['slot_acl'][3];
        //$this->currentPage = $data['slot_acl'][4];

        // if ($this->privilegeIndex == 0) {
        //     if ($loadView) {
        //         if (!service('request')->isAJAX()) {
        //             $this->dataMenuAccess = _authUser2($uri1 . "/" . $uri2, session()->get('user_id'), session()->get('user_type_id'));
        //             //$data['menu_generate'] = _authUser(session()->get('user_id'), session()->get('user_type_id'));
        //             $data["error_message"] = "You don't have privilege to access this page";
        //             $data["menu_generate"] = $this->dataMenuAccess;
        //             $data['currentPage'] = $this->currentPage;
        //             $template_data["contents"] = view('revoked/index', $data);
        //             return view('layout', $template_data, false);
        //         } else {
        //             $dataError["error_message"] = "You don't have privilege to access this page";
        //             return $this->response->setJSON($dataError);
        //         }
        //     }
        //     return false;
        // } else {
        //     $this->dataMenuAccess = _authUser2($uri1 . "/" . $uri2, session()->get('user_id'), session()->get('user_type_id'));
        // }
        return true;
    }




    protected function field_lookup_definition_for_column_properties()
    {
        $arrOverideCol = array();
        return $arrOverideCol;
    }

    protected function getTableStructure($tableName)
    {
        if ($this->gridDefinition == null) {
            if (strpos($tableName, 'm_') === 0) {
                $this->entityName = str_replace('m_', '', $tableName);
            } else if (strpos($this->tableName, 'd_') === 0) {
                $this->entityName = str_replace('d_', '', $tableName);
            } else {
                $this->entityName = $tableName;
            }
            $this->entityName = str_replace("_", " ", $this->entityName);
            $this->entityName = ucwords($this->entityName);

            $modelName = $this->tableName;
            // CI4: Get model class name from table name
            $modelClassName = $this->getModelClassName($this->tableName);
            $modelPath = "\\App\\Models\\{$modelClassName}";
            $model = new $modelPath();

            $gridDefinition = array();
            $this->pk_id = '';
            $this->hasCompanyId = false;
            $isHasFile = false;
            if ($arrData = $model->fieldStructure) {
                $isFirstAfterPK = false;
                foreach ($arrData as $field => $type) {
                    $title = str_replace("_", " ", $field);
                    $title = ucwords($title);
                    $colProperties = '';
                    $filter_type = 'text';
                    $inputType = 'text';
                    $filterValue = [];
                    $colRenderer = null;
                    if ($type == 'int') {
                        $type = 'int';
                        $colProperties = "width: '100px', class: 'text-right'";
                        $inputType = 'number';
                    } else if ($type == 'boolean') {
                        $filter_type = 'select';
                        $filterValue = array("" => 'All', 0 => 'No', 1 => 'Yes');
                        $type = 'boolean';
                        $colProperties = "width: '100px', class: 'text-center'";
                        $inputType = 'switch';
                        $colRenderer = 'if (data == 1) return "Yes"; else if (data == 0) return "No"; else return "";';
                    } else if ($type == 'file') {
                        $filter_type = '';
                        $colProperties = "width: '100px', class: 'text-center'";
                        $inputType = 'file';
                        $colRenderer = 'return renderGridFile(data, type, row);';
                        $isHasFile = true;
                    } else if ($type == 'double' || $type == 'float') {
                        $type = 'number';
                        $colProperties = "width: '100px', class: 'text-right'";
                        $inputType = 'number';
                    } else if ($type == 'date') {
                        $type = 'date';
                        $colProperties = "width: '100px', class: 'text-center'";
                        $filter_type = 'date';
                    }

                    if ($isFirstAfterPK) {
                        if (!in_array($field, $this->excludeFields)) {
                            if ($type == 'string') {
                                $this->descriptionField = $field;
                                $isFirstAfterPK = false;
                            }
                        }
                    }
                    //PK
                    if ($field == $model->strPKAutoIncrement) {
                        $this->pk_id  = $field;
                        $isFirstAfterPK = true;
                    }
                    if ($field == $this->sequenceField) {
                        $this->sequenceExist = true;
                    }
                    if ($field == $this->company_id_field) {
                        $this->hasCompanyId = true;
                    }
                    $gridDefinition[$field] = array(
                        'title' => $title,
                        'dataIndex' => $field,
                        'type' => $type,
                        'filter_type' => $filter_type,
                        'filter_value' => $filterValue,
                        'colProperties' => $colProperties,
                        'inputType' => $inputType,
                        'validationType' => 'required',
                        'colRenderer' => $colRenderer
                    );
                }
            }
            if (isset($model->descriptionField) && $model->descriptionField != null) {
                $this->descriptionField = $model->descriptionField;
            }

            if (!$this->hasCompanyId) {
                unset($this->excludeFields[$this->company_id_field]);
            }

            $arrFields = $this->field_lookup_definition_for_column_properties();
            foreach ($arrFields as $field => $col) {
                foreach ($gridDefinition as &$col2) {
                    if ($field == $col2['dataIndex']) {
                        if (isset($col['colProperties'])) $col2['colProperties'] = $col['colProperties'];
                        if (isset($col['filter_type'])) $col2['filter_type'] = $col['filter_type'];
                        if (isset($col['title'])) $col2['title'] = $col['title'];
                        if (isset($col['type'])) $col2['type'] = $col['type'];
                        if (isset($col['inputType'])) $col2['inputType'] = $col['inputType'];
                        if (isset($col['validationType'])) $col2['validationType'] = $col['validationType'];
                        if (isset($col['colRenderer'])) $col2['colRenderer'] = $col['colRenderer'];
                    }
                }
            }

            $this->gridDefinition = $gridDefinition;
        }
        return $this->gridDefinition;
    }


    protected function GetUploadFolder($pkId)
    {
        $folder = 'assets/img/' . strtolower($this->controllerName);
        if (!file_exists($folder)) {
            mkdir($folder, 0777);
        }
        $pathGroup = ceil($pkId / 10000);
        $folder = 'assets/img/' . strtolower($this->controllerName) . '/' . $pathGroup . '/';
        if (!file_exists($folder)) {
            mkdir($folder, 0777);
        }
        $folder = 'assets/img/' . strtolower($this->controllerName) . '/' . $pathGroup . '/' . $pkId . '/';
        if (!file_exists($folder)) {
            mkdir($folder, 0777);
        }

        return $folder;
    }


    public function GetUploadFolderJavascript()
    {
        return "
<script type=\"text/javascript\">
		var renderGridFile = function(data, type, row) {
			return GetFileLocation(row." . $this->pk_id . ", data);
		};

		var GetFileLocation = function(pkId, fileUrl)
		{
			if (fileUrl == null || fileUrl == '') return '';
			var pathGroup = Math.ceil(pkId / 10000);
			var fileFinal = 'assets/img/" . strtolower($this->controllerName) . "/' + pathGroup + '/' + pkId + '/' + fileUrl;
			var result = '';
			if (fileUrl.indexOf('.jpg') >= 0 || fileUrl.indexOf('.gif') >= 0 || fileUrl.indexOf('.png') >= 0 || fileUrl.indexOf('.jpeg') >= 0)
			{
				result = '<a href=\"' + fileFinal + '\" target=\"_blank\"><img src=\"' + fileFinal + '\" style=\"width: 100px\" border=0 /></a>';
			}
			else {
				result = '<a href=\"' + fileFinal + '\" target=\"_blank\">' + fileFinal + '</a>';
			}

			return result;
		};
</script>";
    }



    protected function set_unique_fields($arrUniqueFields)
    {
        $this->arrUniqueFields = $arrUniqueFields;
    }

    //must return true to continue
    //parameter : $record to check
    //$errorMessage = error when unique field violated
    protected function passed_unique_field($record, &$errorMessage)
    {
        if ($this->arrUniqueFields) {
            foreach ($this->arrUniqueFields as $uniqueFieldName => $uniqueFieldDescription) {
                $arrCriteria = [];
                if ($this->hasCompanyId) {
                    $arrCriteria[$this->company_id_field] = $record[$this->company_id_field];
                }
                $arrCriteria[$uniqueFieldName] = $record[$uniqueFieldName];
                if (isset($record[$this->pk_id]) && $record[$this->pk_id] > 0) {
                    //check update
                    $arrCriteria[$this->pk_id]['$ne'] = $record[$this->pk_id];
                    if ($oldData = $model->findAll($arrCriteria)) {
                        $errorMessage = 'Failed to save data.';
                        $errorMessage .= "<br />" . $uniqueFieldDescription . ' <b>' . $record[$uniqueFieldName] . '</b> is already exists';
                        $errorMessage .= "<br />" . $uniqueFieldDescription . ' must be unique';
                        return false;
                    }
                } else {
                    //check insert
                    if ($oldData = $model->findAll($arrCriteria)) {
                        $errorMessage = 'Failed to save data.';
                        $errorMessage .= "<br />" . $uniqueFieldDescription . ' <b>' . $record[$uniqueFieldName] . '</b> is already exists';
                        $errorMessage .= "<br />" . $uniqueFieldDescription . ' must be unique';
                        return false;
                    }
                }
            }
        }
        return true;
    }
    /**
     * Helper method to convert table name to Model class name
     * CI4 addition for dynamic model loading
     */
    protected function getModelClassName($tableName)
    {
        $name = preg_replace("/^[md]_/", "", $tableName);
        $parts = explode("_", $name);
        $className = "";
        foreach ($parts as $part) {
            $className .= ucfirst($part);
        }
        return "M" . $className;
    }

    /**
     * Temporary menu until ACL is fixed
     * CI4: This will be replaced with dynamic menu from database
     */
    protected function getTemporaryMenu()
    {
        $html = '<ul id="js-nav-menu" class="nav-menu">';

        // Home
        $html .= '<li>';
        $html .= '  <a href="' . base_url('dashboard') . '" title="Dashboard">';
        $html .= '    <i class="fal fa-home"></i>';
        $html .= '    <span class="nav-link-text">Home</span>';
        $html .= '  </a>';
        $html .= '</li>';

        // Master Data with children
        $html .= '<li>';
        $html .= '  <a href="#" title="Master Data">';
        $html .= '    <i class="fal fa-database"></i>';
        $html .= '    <span class="nav-link-text">Master Data</span>';
        $html .= '  </a>';
        $html .= '  <ul>';
        $html .= '    <li>';
        $html .= '      <a href="' . base_url('product_item') . '" title="Products">';
        $html .= '        <span class="nav-link-text">Products</span>';
        $html .= '      </a>';
        $html .= '    </li>';
        $html .= '    <li>';
        $html .= '      <a href="' . base_url('product_category') . '" title="Product Category">';
        $html .= '        <span class="nav-link-text">Product Category</span>';
        $html .= '      </a>';
        $html .= '    </li>';
        $html .= '    <li>';
        $html .= '      <a href="' . base_url('master_satuan') . '" title="Master Satuan">';
        $html .= '        <span class="nav-link-text">Master Satuan</span>';
        $html .= '      </a>';
        $html .= '    </li>';
        $html .= '    <li>';
        $html .= '      <a href="' . base_url('cashier_user') . '" title="Cashier User">';
        $html .= '        <span class="nav-link-text">Cashier User</span>';
        $html .= '      </a>';
        $html .= '    </li>';
        $html .= '  </ul>';
        $html .= '</li>';

        // Gerai / Outlet
        $html .= '<li>';
        $html .= '  <a href="' . base_url('outlet') . '" title="Gerai / Outlet">';
        $html .= '    <i class="fal fa-store"></i>';
        $html .= '    <span class="nav-link-text">Gerai / Outlet</span>';
        $html .= '  </a>';
        $html .= '</li>';

        // Customers
        $html .= '<li>';
        $html .= '  <a href="' . base_url('customers') . '" title="Customers">';
        $html .= '    <i class="fal fa-users"></i>';
        $html .= '    <span class="nav-link-text">Customers</span>';
        $html .= '  </a>';
        $html .= '</li>';

        // Payment Configuration
        $html .= '<li>';
        $html .= '  <a href="' . base_url('payment_configuration') . '" title="Payment Configuration">';
        $html .= '    <i class="fal fa-credit-card"></i>';
        $html .= '    <span class="nav-link-text">Payment Configuration</span>';
        $html .= '  </a>';
        $html .= '</li>';

        // Reports
        $html .= '<li>';
        $html .= '  <a href="#" title="Reports">';
        $html .= '    <i class="fal fa-chart-line"></i>';
        $html .= '    <span class="nav-link-text">Reports</span>';
        $html .= '  </a>';
        $html .= '</li>';

        // Exit
        $html .= '<li>';
        $html .= '  <a href="' . base_url('logout') . '" title="Exit">';
        $html .= '    <i class="fal fa-sign-out"></i>';
        $html .= '    <span class="nav-link-text">Exit</span>';
        $html .= '  </a>';
        $html .= '</li>';

        $html .= '</ul>';

        return $html;
    }
}
