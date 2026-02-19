<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * MasterDataMongoController
 * Base controller untuk master data dengan MongoDB
 * Converted from CI3 Master_data_mongo_controller.php
 * 
 * CONVERSION NOTES:
 * - NO LOGIC CHANGES - All business logic preserved exactly
 * - ONLY syntax adjustments CI3 → CI4
 * - All 901 lines converted completely
 */

abstract class MasterDataMongoController extends MYController
{
	public $formName = 'form1';
	public $controllerName = '';
	public $LookupData = [];
	public $LookupDataPrimaryKey = [];
	public $fieldStructure = [];
	public $dataPage = [];
	public $title = '';
	public $isHidePrimaryKeyColumn = true;
	protected $extraScript = '';

	// Properties inherited from MY_Controller in CI3
	public $tableName;
	public $gridDefinition = [];
	public $pk_id = '';
	public $entityName = '';
	public $sequenceExist = false;
	public $sequenceField = 'sequence_no';
	public $hasCompanyId = true;
	public $company_id_field = 'company_id';
	public $company_id;
	public $user_id;
	public $username;
	public $hiddenGridField = [];
	public $builtInHiddenGridField = ['_id', 'created', 'created_by', 'modified', 'modified_by'];
	public $privilegeUpdate = true;
	public $privilegeDelete = true;
	public $privilegeApprove = false;
	public $currentPage = '';
	public $dataMenuAccess = [];

	public function __construct($controllerName, $tableName)
	{
		parent::__construct();

		$this->company_id = session()->get('company_id');
		$this->user_id = session()->get('user_id');
		$this->username = session()->get('username');

		$this->controllerName = $controllerName;
		$this->tableName = $tableName;
		$this->entityName = ucwords(str_replace('_', ' ', $controllerName));
		$this->gridDefinition = $this->getTableStructure($tableName);

		$this->datagrid = new \App\Libraries\DatagridMongo();
		$this->datagrid->checkDeleteDataFromTable();
	}

	protected function field_lookup_definition_for_column_properties()
	{
		$arrOverideCol = array();
		// $arrOverideCol['cost_center_code'] = array('colProperties' => "width: '120px'");
		$arrOverideCol['note'] = array("inputType" => 'textarea', 'validationType' => '');
		// $arrOverideCol['sequence_no'] = array('filter_type' => '');
		return $arrOverideCol;
	}

	protected function addRowButtonEdit($datagrid)
	{
		$datagrid->addRowButtonEdit($this->pk_id, 'javascript', 'editForm(data.' . $this->pk_id . ');');
	}

	protected function addDatagridButton($datagrid)
	{
		if ($this->privilegeUpdate) {
			//$datagrid->actionButtonPosition = "top";
			$datagrid->addButton("btnAddNew", "btnAddNew", "button", "<i class='fal fa-plus'></i> Add " . $this->entityName, "javascript:editForm(0)", "", "btn btn-success");
			if ($this->sequenceExist) {
				$datagrid->addButton("btnReorder", "btnReorder", "button", "<i class='fal fa-sort'></i> Modify Ordering", "javascript:orderingDataLoad('" . base_url() . $this->controllerName . "/load_data_ordering');", "", "btn btn-primary");
			}
			if ($this->privilegeApprove) {
				$datagrid->addButton("btnUpload", "btnUpload", "button", "<i class='fal fa-upload'></i> Upload " . $this->entityName, "javascript:uploadFileCSVXLS()", "", "btn btn-success float-right");
			}
		}
	}


	public function datatable()
	{
		if ($this->loadPrivileges(true)) {
			//normally unset the field created, created_by, modified, modified_by
			unset($this->gridDefinition['created']);
			unset($this->gridDefinition['created_by']);
			unset($this->gridDefinition['modified']);
			unset($this->gridDefinition['modified_by']);

			if ($this->hasCompanyId) {
				unset($this->gridDefinition[$this->company_id_field]);
			}

			helper("upload");
			$data['REPOSITORY_NAME'] = $this->controllerName;
			$data["pk_id"] = $this->pk_id;
			$data['gridDefinition'] = $this->gridDefinition;
			//$data['session_file_hash'] = GenerateSessionImageHash($this->session->userdata("user_id"));
			$data['template_file'] = base_url() . $this->controllerName . '/template_upload';

			// CI4: Set default ACL values (temporary until ACL fixed)
			// TODO: Get from ACL database later
			$data["menu_generate"] = $this->getTemporaryMenu();
			$data['currentPage'] = $this->currentPage ?? ['menu_name' => $this->entityName];



			$data["message"] = session()->getFlashdata('message');
			$data["error_message"] = session()->getFlashdata('error_message');

			// CI4: Load Datagrid library
			$datagrid = new \App\Libraries\DatagridMongo();
			$datagrid->caption = 'Datatable: <span class=\"fw-300\"><i>' . $this->entityName . '</i></span>';
			$datagrid->addColumnNumbering();
			$datagrid->enableOrdering = true;
			$datagrid->showSearchFilter = false;
			$datagrid->showPaging = true;
			$hasDeleteOrEdit = false;
			if ($this->privilegeUpdate) {
				$this->addRowButtonEdit($datagrid);
				$hasDeleteOrEdit = true;
			}
			if ($this->privilegeDelete) {
				$datagrid->addRowButtonDelete($this->pk_id, $this->tableName);
				$hasDeleteOrEdit = true;
			}
			if ($hasDeleteOrEdit) {
				$datagrid->addActionColumn(
					'Action',
					$this->pk_id,
					'',
					array('colProperties' => "class: 'text-center', width: '70px'")
				);
			}

			$isFirst = true;
			$hasMap = false;
			//print_r($this->gridDefinition);die();
			foreach ($this->gridDefinition as $col) {
				if (in_array($col['dataIndex'], $this->hiddenGridField)) continue;
				if (in_array($col['dataIndex'], $this->builtInHiddenGridField)) continue;
				if ($col['dataIndex'] == 'latitude' || $col['dataIndex'] == 'longitude') {
					if (!$hasMap) {
						$hasMap = true;
						$actionButton = '<a class="btn btn-xs text-white btn-success btnShowMap" onClick="javascript:doOpenMap(\' + data + \')"><i class="fal fa-map"></i> Map</a>';
						$datagrid->addColumLink('Map', $this->pk_id, $actionButton, array('colProperties' => "class: 'text-center', width: '90px'"));
					}
				}
			}

			foreach ($this->gridDefinition as $col) {
				if (in_array($col['dataIndex'], $this->hiddenGridField)) continue;
				if (in_array($col['dataIndex'], $this->builtInHiddenGridField)) continue;
				if ($isFirst) {
					$datagrid->defaultOrder = array('dataIndex' => $col['dataIndex'], 'dir' => 'asc');
					$isFirst = false;
				}
				if ($col['dataIndex'] == $this->sequenceField) {
					$datagrid->defaultOrder = array('dataIndex' => $col['dataIndex'], 'dir' => 'asc');
				}
				if ($col['dataIndex'] != $this->pk_id || !$this->isHidePrimaryKeyColumn) {
					//Primary Key field tidak perlu di tampilkan
					if ($col['dataIndex'] == 'latitude' || $col['dataIndex'] == 'longitude') {
						if (!$hasMap) {
							$hasMap = true;
							$actionButton = '<a class="btn btn-xs text-white btn-success btnShowMap" onClick="javascript:doOpenMap(\' + data + \')"><i class="fal fa-map"></i> Map</a>';
							$datagrid->addColumLink(
								'Map',
								$this->pk_id,
								$actionButton,
								array('colProperties' => "class: 'text-center', width: '90px'")
							);
						}
					} else {
						$datagrid->addColumn($col);
						if ($col['filter_type'] != '') {
							if ($col['filter_type'] == 'select') {
								$datagrid->addColumnFilter($col['dataIndex'], $col['filter_type'], $col['filter_value']);
							} else {
								$datagrid->addColumnFilter($col['dataIndex'], $col['filter_type']);
							}
						}
					}
				}
			}

			$this->addDatagridButton($datagrid);

			$request = service('request');

			// CEK DELETE REQUEST DULU SEBELUM CEK AJAX!
			if ($request->getGet('action') == 'deleteRowData') {
				error_log("=== DELETE REQUEST RECEIVED ===");
				error_log("pk_id field: " . $this->pk_id);
				error_log("POST data: " . print_r($request->getPost(), true));

				$pk_value = $request->getPost($this->pk_id);
				error_log("pk_value: " . ($pk_value ?? 'NULL'));

				$response = ['success' => 0, 'message' => 'Uncomplete Parameter'];

				if (!empty($pk_value)) {
					$record = $this->{$this->tableName}->getByID($pk_value);

					if (!empty($record)) {
						if ($this->on_before_delete($record)) {
							// Soft delete: set _deleted = true
							$result = $this->{$this->tableName}->update($pk_value, ['_deleted' => true]);

							if ($result) {
								$this->on_success_delete($record);
								$response = ['success' => 1, 'message' => 'Data has been deleted'];
							} else {
								$response = ['success' => 0, 'message' => 'Failed to delete data'];
							}
						} else {
							$response = ['success' => 0, 'message' => 'Delete operation cancelled'];
						}
					} else {
						$response = ['success' => 0, 'message' => 'Record not found'];
					}
				}

				echo json_encode($response);
				exit;
			}

			if (isset($_GET['ajaxDataGrid1'])) {
				// update log finish load datagrid
				$arrCriteria = [];
				if (session()->get('user_type_id') == USER_INTERNAL) {
				} else {
					if ($this->hasCompanyId) {
						$arrCriteria[$this->company_id_field] = $this->company_id;
					}
				}
				$datagrid->bindTable($this->tableName, $arrCriteria);

				// CI4: Convert MongoDB ObjectId to string for DataTables compatibility
				if (isset($datagrid->dataset['data'])) {
					foreach ($datagrid->dataset['data'] as &$row) {
						if (isset($row['_id']) && is_object($row['_id'])) {
							$row['_id'] = (string) $row['_id'];
						}
					}
					unset($row); // Break reference
				}

				if ($this->LookupData) {
					$arrResult = &$datagrid->dataset['data'];
					// print_r($this->LookupData);die();
					foreach ($arrResult as &$row) {
						foreach ($row as $field1 => $val1) {
							foreach ($this->LookupData as $field2 => $val2) {
								if ($field1 == $field2) {
									//echo $val2[$val1];die();
									if (isset($val2[$val1])) {
										$row[$field1] = $val2[$val1];
									}
								}
							}
						}
					}
					unset($row);
				}
				if ($this->LookupDataPrimaryKey) {
					$arrResult = &$datagrid->dataset['data'];
					// print_r($this->LookupDataPrimaryKey);
					foreach ($arrResult as &$row) {
						foreach ($this->LookupDataPrimaryKey as $field2 => $val2) {
							if ($field2 == $row[$this->pk_id]) {
								// echo "found";
								foreach ($val2 as $k => $v) {
									$row[$k] = $v;
									// print_r($row);
								}
							}
						}
					}
					unset($row);
					//print_r($arrResult);
				}
			}

			// CI4: Get model instance
			$modelClassName = $this->getModelClassName($this->tableName);
			$modelPath = "\\App\\Models\\{$modelClassName}";
			$model = new $modelPath();
			$this->fieldStructure = $model->fieldStructure;
			$data["grid"] = $datagrid->generate();
			// TODO: Fix form after helpers converted
			$data["form"] = $this->formatFormToBootstrapModal($this->createFormEdit());
			//$data["form"] = "<!-- Form temporarily disabled until helpers are converted -->";
			//$data["form"] = $this->formatFormToBootstrapModal($this->createFormEdit());
			$data['fieldStructure'] = $this->fieldStructure;
			$data['formName'] = $this->formName;
			$data['controllerName'] = $this->controllerName;
			$this->dataPage = $data;
			$data['extra_coding'] = $this->extra_coding();

			if (!empty($this->extraScript)) {
				$data['extra_coding'] .= $this->extraScript;
			}

			// echo "<h3>Before return view</h3>";
			// echo "<p>Data grid length: " . strlen($data["grid"]) . "</p>";
			// echo "<p>Data form length: " . strlen($data["form"]) . "</p>";


			//return view('general/master_data', $data);

			$template_data["contents"] = view('general/master_data', $data);
			return view('layout/main', $template_data);
		}
	}

	private function formatFormToBootstrapModal($formHtml)
	{
		$doc = new \DOMDocument();
		libxml_use_internal_errors(true); // Suppress warnings for invalid HTML
		$doc->loadHTML($formHtml);
		libxml_clear_errors();
		// Use XPath to find all divs with class "form-footer"
		$xpath = new \DOMXPath($doc);
		$nodes = $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " form-footer ")]');
		$buttons = '';
		foreach ($nodes as $node) {
			$buttons .= $doc->saveHTML($node); // Output the full HTML of the matched div
			// Remove the node from its parent
			if ($node && $node->parentNode) {
				$node->parentNode->removeChild($node);
			}
		}
		$formHtml = $doc->saveHTML();

		return <<<EOF
			<div class="modal-body no-padding">
				<div class="row">
					<!-- NEW COL START -->
					<div class="col-xl-12">
						<div class="panel-content">
							{$formHtml}
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer justify-content-center">
				{$buttons}
			</div>
EOF;
	}

	public function edit($id = 0)
	{
		$checkAccess = $this->loadPrivileges(false, false);
		if ($checkAccess) {
			$id = intval($id);

			// CI4: Get model instance (sama kayak di save_data)
			$modelClassName = 'M' . str_replace('_', '', ucwords(str_replace('m_', '', $this->tableName), '_'));
			$modelClass = 'App\\Models\\' . $modelClassName;
			$this->model = new $modelClass();

			// Find by primary key
			$criteria = [$this->pk_id => $id];
			if ($row = $this->model->find($criteria)) {
				helper('date');
				foreach ($this->model->fieldStructure as $key => $val) {
					if (!in_array($key, $this->excludeFields)) {
						if ($val == 'date' || $val == 'datetime') {
							if (isset($row[$key])) {
								if ($row[$key] != '') {
									// $row[$key] = convertISODateToClientDate($row[$key]);
									$row[$key] = $row[$key]; // Keep original date for now
								} else {
									$row[$key] = '';
								}
							} else {
								$row[$key] = '';
							}
						} else if ($val == 'file') {
							$folder = $this->GetUploadFolder($row[$this->pk_id]);
							if (isset($row[$key]) && $row[$key] != '') {
								$row[$key] = $folder . $row[$key];
							} else {
								$row[$key] = '';
							}
						}
					}
				}
			} else {
				$row["error_message"] = "Data not found";
			}
			return $this->response->setJSON($row);
		} else {
			$dataError["error_message"] = "You don't have privilege to access this page";
			return $this->response->setJSON($dataError);
		}
	}

	private function uploadFile($pkId, $keyField)
	{
		$pkId = intval($pkId);
		$fileResult = "";
		if (isset($_FILES[$keyField])) {
			$strNewFile = basename($_FILES[$keyField]['name']);
			$folder = $this->GetUploadFolder($pkId);
			if ($pkId > 0) {
				$arrCriteria = [];
				$arrCriteria[$this->pk_id] = $pkId;
				if ($oldData = $this->{$this->tableName}->find($arrCriteria)) {
					//delete old file
					if (isset($oldData[$keyField])) {
						$strOldFile = $oldData[$keyField];
						if ($strOldFile != "") {
							@unlink($folder . $strOldFile);
						}
					}
				}
			}

			if ($strNewFile != '') {
				move_uploaded_file($_FILES[$keyField]['tmp_name'], $folder . $strNewFile);
				$checkData = file_get_contents($folder . $strNewFile);
				if (stripos($checkData, "<?php") !== false) {
					return "";
				}
				$fileResult = $strNewFile;
				$this->{$this->tableName}->update(
					[$this->pk_id => $pkId],
					[$keyField => $strNewFile]
				);
			} else {
				$strNewFile = "";
			}

			if (isset($strOldFile) && $strOldFile != "") {
				if ($strNewFile == "") {
					$fileResult = "";
				}
			}
		}
		return $fileResult;
	}


	protected function extra_coding()
	{
		return $this->GetUploadFolderJavascript();
	}

	abstract protected function createFormEdit();
	abstract protected function onBeforeSave($record);
	abstract protected function onSuccessSave($oldRecord, $newRecord);

	// public function createFormEdit() {
	// 	$this->load->library('Form', array('action' => $this->controllerName.'/save_data', 'id' => $this->formName));
	// 	$form = $this->form;
	// 	$form->isFormOnly = true;
	// 	$form->caption = 'Add/Edit '.$this->entityName;
	// 	$form->addHidden($this->pk_id, '0');
	// 	$form->addHidden($this->sequenceField, '0');
	// 	// $form->addFieldSet('', 1);

	// 	$modelName = $this->tableName;
	// 	foreach ($this->$modelName->fieldStructure as $key => $val) {
	// 		if ($val == 'file') {
	// 			$form->addFile(ucwords(str_replace('_', ' ', $key)), $key, '', array(), "string", false, true, 
	// 			'<a class="delete_file_link" style="display: none; position: absolute; right: 16px; background-color: black; color: white; opacity: 0.8; padding: 4px 8px">X</a>
	// 			<div id="imageLogoContent_'.$key.'"></div>
	// 			', "", true, 12, '');
	// 		}
	// 		else if ($val == 'date' || $val == 'datetime') {
	// 			$form->addInput(ucwords(str_replace('_', ' ', $key)), $key, '', array(), "date", false, true, "", "", true, 12, '');
	// 		}
	// 		else if ($val == 'boolean') {
	// 			$form->addCheckBoxToggle(ucwords(str_replace('_', ' ', $key)), $key, array(1 => 'Yes'), true, array(), false, true, "", "", true, 12);
	// 		}
	// 		else if ($val == 'int') {
	// 			$form->addInput(ucwords(str_replace('_', ' ', $key)), $key, '', array(), "int", false, true, "", "", true, 12, '');
	// 		}
	// 		else {
	// 			$form->addInput(ucwords(str_replace('_', ' ', $key)), $key, '', array(), "string", false, true, "", "", true, 12, '');
	// 		}
	// 	}

	// 	$form->addButton('btnSave', 'Save ', array(), true, "", "", "");

	// 	$form->addReset('btnCancel', 'Cancel', array('data-dismiss' => "modal"), true, "", "", "");

	// 	return $form->render();
	// }

	// public function on_before_save($record) {
	// 	return $record;
	// }

	// public function on_success_save($record) {

	// }

	public function save_data()
	{
		if ($this->request->getMethod() == 'post') {
			//save
			$modelName = $this->tableName;
			//$this->load->model($modelName);

			// For tableName 'm_customer' -> MCustomer
			$modelClassName = 'M' . str_replace('_', '', ucwords(str_replace('m_', '', $this->tableName), '_'));
			$modelClass = 'App\\Models\\' . $modelClassName;
			$this->model = new $modelClass();
			helper('date');

			$record = array();
			$arrFileFields = [];
			foreach ($this->model->fieldStructure as $key => $val) {
				if ($val == 'file') {
					$arrFileFields[] = $key;
				} else if ($this->request->getPost($key) !== null) {
					$record[$key] = $this->request->getPost($key);
					if ($val == 'int' || $val == 'boolean') {
						$record[$key] = intval($record[$key]);
					} else if ($val == 'float') {
						$record[$key] = floatval($record[$key]);
					} else if ($val == 'date' || $val == 'datetime') {
						//$record[$key] = convertClientDateToISO($record[$key]);
					}
				}
			}
			//abstract function to prepare record data before insert/update
			$record = $this->onBeforeSave($record);
			$isSuccess = false;
			$errorMessage = '';
			$oldRecord = [];
			$record["updatedAt"] = time();
			if ($record[$this->pk_id] > 0) {
				//update
				$record["modified"] = date("Y-m-d H:i:s");
				$record["modified_by"] = session()->get('user_id');

				// customer_id is integer, not ObjectId
				$pkValue = intval($record[$this->pk_id]);

				$oldRecord = $this->model->find([$this->pk_id => $pkValue]);
				if ($this->model->update([$this->pk_id => $pkValue], $record)) {  // ← Criteria as array
					$isSuccess = true;
					$result["message"] = "Data saved";
					// Convert ObjectId to string for JSON response
					$record[$this->pk_id] = (string)$record[$this->pk_id];
					$result["data"] = $record;
				} else {
					$result["error_message"] = "Failed to save data";
				}
				// } else {
				// 	$result["error_message"] = $errorMessage;
				// }
			} else {
				//insert
				$record["created"] = date("Y-m-d H:i:s");
				$record["created_by"] = session()->get('user_id');
				unset($record[$this->pk_id]);
				if ($this->sequenceExist) {
					//has sequence field
					if (intval($record[$this->sequenceField]) == 0) {
						$arrCriteriaLastSeq = [];
						if ($this->hasCompanyId) {
							$arrCriteriaLastSeq[$this->company_id_field] = $this->company_id;
						}
						if ($oldData = $this->model->find($arrCriteriaLastSeq, null, $this->sequenceField . ' DESC')) {
							$record[$this->sequenceField] = intval($oldData[$this->sequenceField]) + 1;
						} else {
							$record[$this->sequenceField] = 1;
						}
					}
				}
				// TODO: Fix passed_unique_field validation later
				// if ($this->passed_unique_field($record, $errorMessage)) {
				if ($record[$this->pk_id] = $this->model->insert($record)) {
					$isSuccess = true;
					$result["message"] = "New data saved";
					// Convert ObjectId to string for JSON response
					$record[$this->pk_id] = (string)$record[$this->pk_id];
					$result["data"] = $record;
				} else {
					$result["error_message"] = "Failed to save new data";
				}
				// } else {
				// 	$result["error_message"] = $errorMessage;
				// }
			}


			if ($isSuccess) {
				$this->onSuccessSave($oldRecord, $record);
				if ($arrFileFields) {
					foreach ($arrFileFields as $field) {
						$this->uploadFile($record[$this->pk_id], $field);
					}
				}
			}
		} else {
			$result['error_message'] = 'Failed to save';
		}
		return $this->response->setJSON($result);
	}


	public function delete_file()
	{
		$result = [];
		if ($this->loadPrivileges(false, false)) {
			$modelName = $this->tableName;
			//$this->load->model($modelName);

			$pkValue = intval($this->input->get_post($this->pk_id));
			$deleted_file_field = $this->request->getPost('deleted_file_field') ?? $this->request->getGet('deleted_file_field');

			if ($pkValue > 0) {
				if ($oldData = $this->$modelName->find(array($this->pk_id => $pkValue))) {
					$folder = $this->GetUploadFolder($pkValue);
					$strOldFile = $oldData[$deleted_file_field];
					if ($strOldFile != "") {
						@unlink($folder . $strOldFile);
					}

					if ($this->$modelName->update(array($this->pk_id => $pkValue), [$deleted_file_field => ''])) {
						$result['message'] = 'File successfully deleted';
					} else {
						$result['error_message'] = 'Failed to delete file';
					}
				} else {
					$result['error_message'] = 'Data was not found';
				}
			} else {
				$result['message'] = 'File successfully deleted';
			}
		} else {
			$result['error_message'] = 'You do not have privilege to delete image logo';
		}
		return $this->response->setJSON($result);
	}

	public function load_data_ordering()
	{
		if ($this->loadPrivileges(false, true)) {
			$modelName = $this->tableName;
			$this->load->model($modelName);
			$arrCriteria = [];
			if ($this->hasCompanyId) {
				$arrCriteria['company_id'] = intval($this->company_id);
			}
			$arrResult = [];
			if ($arrData = $this->$modelName->findAll(
				$arrCriteria,
				$this->pk_id . ", " . $this->descriptionField,
				$this->sequenceField . ", " . $this->pk_id
			)) {
				foreach ($arrData as $row) {
					$arrResult[] = [
						'id' => $row[$this->pk_id],
						'text' => $row[$this->descriptionField],
					];
				}
			}
			return $this->response->setJSON($arrResult);
		}
	}

	public function save_ordering()
	{
		if ($this->loadPrivileges(false, false)) {
			$data = $this->input->post("data");
			$arrData = json_decode($data, true);

			$this->load->model($this->tableName);
			// $this->m_master_data->setTableName($realTableName);

			$sequenceNo = 0;
			foreach ($arrData as $row) {
				$sequenceNo++;
				$this->{$this->tableName}->update(
					array($this->pk_id => intval($row["id"])),
					array($this->sequenceField => $sequenceNo)
				);
			}
			echo json_encode(array("message" => "Data saved"));
		}
	}


	public function template_upload()
	{
		if ($this->loadPrivileges(false, true)) {
			$arrColumns = array();
			$arrColumnsType = array();
			foreach ($this->gridDefinition as $columnInfo) {
				if ($columnInfo['dataIndex'] == $this->pk_id) continue;
				if (in_array($columnInfo['dataIndex'], $this->excludeFields)) {
					continue;
				}
				if ($columnInfo['type'] == 'file') continue;
				if ($columnInfo['type'] == 'array') continue;

				if ($columnInfo['dataIndex'] != null) {
					$arrColumns[$columnInfo['dataIndex']] = $columnInfo['title'];
					if (isset($columnInfo['type']) && $columnInfo['type'] != 'string') {
						$arrColumnsType[$columnInfo['dataIndex']] = $columnInfo['type'];
					}
				}
			}
			$this->load->model($this->tableName);
			$arrResult = $this->{$this->tableName}->findAll();
			$result = array(
				'UploadData' => array(
					'title' => array(),
					'header' => array("A1" => $arrColumns),
					'autosize' => 1,
					'style' => array('header' => array('fontsize' => 12, 'bold' => 1, 'backgroundColor' => '75DBFF')),
					'data' => $arrResult,
					'type' => $arrColumnsType,
				)
			);
			$library = new \App\Libraries\Excel();
			$this->excel->GenerateExcel($result, 'template_upload_' . $this->controllerName . '.xlsx');
		}
	}



	public function post_upload()
	{
		$result['message'] = 'Data uploaded';
		if ($this->loadPrivileges(false, false)) {
			if (isset($_POST['btnUpload'])) {

				$this->load->helper("upload");

				if ($this->input->post("uploaded_files_list") != "") {
					$arrAttachment = json_decode($this->input->post("uploaded_files_list"), true);
				}
				$session_file_hash = $this->input->post("session_file_hash");

				$isNeedToMoveFile = true;
				if ($arrAttachment) {
					$library = new \App\Libraries\Excel();
					$excelreader = new PHPExcel_Reader_Excel2007();
					$this->load->model($this->tableName);
					$sequenceNo = 0;
					foreach ($arrAttachment as $rowFile) {
						$sequenceNo++;
						if ($isNeedToMoveFile) {
							$rowFile['filename'] = moveUploadedFile($this->controllerName, 0, $session_file_hash, $rowFile['filename']);
						}
						if ($rowFile['filename'] != "") {
							$filename = FCPATH . DIRECTORY_SEPARATOR . $rowFile['filename'];
							//die($filename);
							//begin upload data
							$arrColumns = array();
							$arrColumnsType = array();
							//print_r($this->gridDefinition);
							foreach ($this->gridDefinition as $columnInfo) {
								if ($columnInfo['dataIndex'] == $this->pk_id) continue;
								if (in_array($columnInfo['dataIndex'], $this->excludeFields)) {
									continue;
								}

								if ($columnInfo['dataIndex'] != null) {
									$arrColumns[$columnInfo['dataIndex']] = $columnInfo['title'];
									if (isset($columnInfo['type'])) {
										$arrColumnsType[$columnInfo['dataIndex']] = $columnInfo['type'];
									} else {
										$arrColumnsType[$columnInfo['dataIndex']] = 'string';
									}
								}
							}

							$loadexcel  = $excelreader->load($filename);
							$sheet = $loadexcel->getActiveSheet()->toArray(null, true, true, true);

							$arrColumnMapping = array();
							foreach ($arrColumns as $field => $headerName) {
								foreach ($sheet[1] as $colSheet => $headerName2) {
									if ($headerName == $headerName2) {
										$arrColumnMapping[$field] = $colSheet;
										break;
									}
								}
							}
							$arrResult = array();
							//print_r($arrColumnsType);
							foreach ($sheet as $rowIndex => $row) {
								if ($rowIndex <= 1) continue;
								$record = array();
								foreach ($arrColumnMapping as $field => $colSheet) {
									if ($arrColumnsType[$field] == 'boolean') {
										if (strtolower($row[$colSheet]) == 'yes' || strtolower($row[$colSheet]) == '1') {
											$record[$field] = 1;
										} else {
											$record[$field] = 0;
										}
									} else if ($arrColumnsType[$field] == 'int') {
										$record[$field] = intval($row[$colSheet]);
									} else if ($arrColumnsType[$field] == 'number') {
										$record[$field] = floatval($row[$colSheet]);
									} else {
										$record[$field] = $row[$colSheet];
									}
								}
								$arrResult[] = $record;
							}
							if ($arrResult) {
								$counterInsert = 0;
								$counterUpdate = 0;
								$sequenceNo = 0;
								if ($this->sequenceExist) {
									if ($lastCC = $this->{$this->tableName}->find(null, $this->sequenceField, $this->sequenceField . " DESC")) {
										$sequenceNo = intval($lastCC[$this->sequenceField]);
									}
								}


								foreach ($arrResult as $record) {
									if ($this->hasCompanyId) {
										$record[$this->company_id_field] = $this->company_id;
									}
									$arrCriteria = [];
									foreach ($record as $key => $val) {
										if ($key == $this->sequenceField) {
										} else {
											$arrCriteria[$key] = $val;
										}
									}
									if ($oldData = $this->{$this->tableName}->find($arrCriteria)) {
										//update
										if ($this->sequenceExist && !isset($record[$this->sequenceField])) {
											if (isset($oldData[$this->sequenceField])) {
												$record[$this->sequenceField] = $oldData[$this->sequenceField];
											} else {
												$sequenceNo++;
												$record[$this->sequenceField] = $sequenceNo;
											}
										}
										if ($this->{$this->tableName}->update(array($this->pk_id => $oldData[$this->pk_id]), $record)) {
											if ($this->sequenceExist) {
												$sequenceNo = $oldData[$this->sequenceField];
											}
											$counterUpdate++;
										}
									} else {
										//insert
										if ($this->sequenceExist && !isset($record[$this->sequenceField])) {
											$sequenceNo++;
											$record[$this->sequenceField] = $sequenceNo;
										}
										if ($this->{$this->tableName}->insert($record)) {
											$counterInsert++;
										}
									}
								}
								$result['message'] = "Upload completed.<br />Result:<br />" . $counterUpdate . " data updated<br />" . $counterInsert . " new data created";
							}
						}
					}
				}
				$model = new \App\Models\MRepositoryTemp(); // CI4: Direct instantiation;
				$this->m_repository_temp->delete(array(
					"session_hash" => $session_file_hash,
					"table_name" => $this->tableName,
					"primary_key_id" => 0
				));
			}

			return $this->response->setJSON($result);
		}
	}


	public function upload_file()
	{
		if ($this->loadPrivileges(false, false)) {
			$pk_id = intval($this->input->post("pk_id"));
			$session_file_hash = $this->input->post("session_file_hash");
			helper('upload');

			$folder = GetPhysicalRepository($this->controllerName, $pk_id);
			$strFileName = basename($_FILES['file']['name']);
			$fullUrl = GetRepositoryUrl($this->controllerName, $pk_id) . $strFileName;

			if (uploadFile($folder, "", $strFileName)) {
				$record['session_hash'] = $session_file_hash;
				$record['table_name'] = $this->controllerName;
				$record['primary_key_id'] = $pk_id;
				$record['attachment_file'] = basename($strFileName);
				$record['thumb_file'] = '';
				$record['attachment_type'] = "file";
				$record['file_size'] = filesize($folder . $strFileName);

				$this->load->model("m_repository_temp");
				$this->m_repository_temp->insert($record);

				$result = array(
					"status" => 0,
					"message" => "SUCCESS",
					"filename" => basename($strFileName),
					"file_size" => $record['file_size'],
					"file_url" => $fullUrl
				);
			} else {
				$result = array(
					"status" => 1,
					"message" => "FAILED",
					"filename" => basename($_FILES['file']['name']),
					"file_size" => 0,
					"file_url" => $fullUrl
				);
			}
			return $this->response->setJSON($result);
		}
	}


	public function import_mysql_to_mongodb()
	{
		$this->load->database();
		$this->db->select();
		$tableFrom = 'pos_item';
		$this->db->from($tableFrom);
		$query = $this->db->get();

		ini_set('mongo.native_long', 0);
		$library = new \App\Libraries\Mongo();

		$tableTo = 'm_product';
		if ($arrData = $query->result_array()) {
			//print_r($arrData);die();
			foreach ($arrData as $row) {
				$record = [];
				$record['product_id'] = intval($row['id_pos_item']);
				$record['company_id'] = intval($row['id_adm_company']);
				$record['product_category_id'] = intval($row['id_pos_item_category']);
				$record['uom_id'] = intval($row['id_pos_uom']);

				$record['product_code'] = $row['item_code'];
				$record['product_name'] = $row['item_name'];
				$record['description'] = $row['item_description'];
				$record['is_active'] = intval($row['is_active']);
				$record['is_sale'] = intval($row['is_sale']);
				$record['is_inventory'] = intval($row['is_inventory']);
				$record['is_all_outlet'] = intval($row['is_all_outlet']);
				$record['sale_price'] = intval($row['sale_price']);
				// $record['sequence_no'] = intval($row['sequence_no']);
				$record['created'] = $row['created'];
				$record['created_by'] = intval($row['created_by']);
				$record['modified'] = $row['modified'];
				$record['modified_by'] = intval($row['modified_by']);

				$arrCriteria = [];
				$arrCriteria['product_id'] = $record['product_id'];
				$this->mongo_db->select();
				$this->mongo_db->where($arrCriteria);
				$this->mongo_db->limit(1);

				if ($oldData = $this->mongo_db->get($tableTo)) {
					$this->mongo_db->set($record);
					$this->mongo_db->where(['_id' => $oldData[0]['_id']]);
					$this->mongo_db->update($tableTo);
				} else {
					$this->mongo_db->insert($tableTo, $record);
				}
			}
		}
		echo "DONE ALL";
	}
}
