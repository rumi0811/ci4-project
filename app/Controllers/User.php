<?php

namespace App\Controllers;

use App\Controllers\MYController;
use App\Models\MUser;
use App\Models\MUserType;
use App\Models\DUserGroup;
use App\Models\MCompany;
use App\Models\MGroup;
//use Config\Constants;
//use Config\CustomConstants;

class User extends MYController
{
    protected $mUser;
    protected $mUserType;
    protected $dUserGroup;
    protected $mCompany;
    protected $mGroup;

    public function __construct()
    {
        parent::__construct();

        // Load models
        $this->mUser = new MUser();
        $this->mUserType = new MUserType();
        $this->dUserGroup = new DUserGroup();
        $this->mCompany = new MCompany();
        $this->mGroup = new MGroup();

        // TEMPORARY: Force privileges untuk testing
        $this->privilegeIndex = 1;
        $this->privilegeUpdate = 1;
        $this->privilegeDelete = 1;
        $this->privilegeApprove = 1;
    }

    public function index()
    {
        if ($this->loadPrivileges(true)) {
            $request = service('request');

            $data["menu_generate"] = $this->getTemporaryMenu();
            $data["currentPage"] = $this->currentPage;

            $data["message"] = session()->getFlashdata('message');
            $data["error_message"] = session()->getFlashdata('error_message');

            if ($request->getPost("btnAdd")) {
                log_message('debug', 'btnAdd clicked! Redirecting...');
                return redirect()->to(base_url('user/edit'));
            } else if ($request->getPost('user_id_delete')) {
                //delete
                if ($this->privilegeDelete == 0) {
                    $data["error_message"] = "You don't have delete permission";
                } else {
                    $userIdDelete = $request->getPost("user_id_delete");
                    if ($this->mUser->delete(array("user_id" => intval($userIdDelete)))) {
                        $data["message"] = "Data deleted";
                    } else {
                        $data["error_message"] = "Failed to delete data";
                    }
                }
            }

            $data["user_types"] = $this->mUserType->generateList(null, "user_type_id", null, "user_type_id", "user_type", true, array("value" => "0", "text" => "All"));

            $template_data["contents"] = view('user/index', $data, ['saveData' => false]);
            return view('layout', $template_data);
        }
    }

    public function datatable()
    {
        $this->loadPrivileges();
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

        $isDormantAkun = false;
        $searchFilter = $request->getPost("searchFilter");
        $arrWhere = [];

        if ($searchFilter) {
            $arrFilter = explode("|||", $searchFilter);
            $arrSearchFields = array(
                "username",
                "name",
                "address",
                "mobile",
                "email_verified",
                "m_user.user_id",
                "pp_code",
                "device_id",
                "is_active",
                "user_note"
            );

            foreach ($arrFilter as $idx => $filter) {
                if (isset($arrSearchFields[$idx])) {
                    $fieldName = $arrSearchFields[$idx];

                    if ($fieldName == "email_verified") {
                        if ($filter != "-1") {
                            $arrWhere['email_verified'] = intval($filter);
                        }
                    } else if ($fieldName == "is_active") {
                        if ($filter != "9") {
                            if ($filter == -2) {
                                $isDormantAkun = true;
                            } else {
                                $arrWhere["is_active"] = intval($filter);
                            }
                        }
                    } else if (trim($filter) != '' && $filter != '0') {
                        if ($fieldName == "mobile") {
                            $filter = str_replace("+62", "0", $filter);
                            $filter = str_replace("-", "", $filter);
                            $filter = str_replace(" ", "", $filter);
                        }

                        if ($fieldName == "m_user.user_id") {
                            $arrWhere['user_id'] = intval($filter);
                        } else {
                            // For MongoDB regex search
                            $arrWhere[$fieldName] = ['$regex' => $filter, '$options' => 'i'];
                        }
                    }
                }
            }
        }

        // Build action buttons based on privileges
        if ($this->privilegeUpdate == 0) {
            $actionTemplate = '
                <a class="btn btn-xs btn-success" onClick="javascript:uploadForm($1);"><i class="fal fa-edit"></i> Upload Foto</a>';
        } else {
            $actionTemplate = '
                <a class="btn btn-xs btn-success" onClick="javascript:editForm($1);"><i class="fal fa-edit"></i></a>&nbsp;
                <button id="smart_mod_delete$1" class="btn btn-xs btn-danger" onClick="deleteConfirm(event, $1)"><i class="fal fa-trash-alt"></i></button>';
        }

        // Get data from MongoDB
        $dataset = $this->mUser->findAll($arrWhere, null, "created DESC");

        $arrResult = [];
        foreach ($dataset as $row) {
            // Convert ObjectId to string
            if (isset($row['_id'])) {
                $row['_id'] = (string)$row['_id'];
            }

            // Ensure all required fields exist
            $row['user_id'] = isset($row['user_id']) ? intval($row['user_id']) : 0;
            $row['username'] = isset($row['username']) ? $row['username'] : '';
            $row['name'] = isset($row['name']) ? $row['name'] : '';
            $row['pp_code'] = isset($row['pp_code']) ? $row['pp_code'] : '';
            $row['mobile'] = isset($row['mobile']) ? $row['mobile'] : '';
            $row['address'] = isset($row['address']) ? $row['address'] : '';
            $row['email_verified'] = isset($row['email_verified']) ? intval($row['email_verified']) : 0;
            $row['is_active'] = isset($row['is_active']) ? intval($row['is_active']) : 0;
            $row['device_id'] = isset($row['device_id']) ? $row['device_id'] : '';
            $row['user_note'] = isset($row['user_note']) ? $row['user_note'] : '';

            // Build action buttons
            $row['action'] = str_replace('$1', $row['user_id'], $actionTemplate);

            // Build photo column
            $pathGroup = ceil($row['user_id'] / 10000);
            $profilePic = base_url('assets/img/no_image.png');
            if (isset($row['profile_picture']) && $row['profile_picture'] != '') {
                $profilePic = base_url("assets/img/user/{$pathGroup}/{$row['user_id']}/{$row['profile_picture']}");
            }
            $row['photo'] = '<img src="' . $profilePic . '" style="width:50px;height:50px;">';

            // Format username with email verification status
            $verifiedIcon = ($row['email_verified'] == 1) ? '<i class="fal fa-check-circle text-success"></i>' : '<i class="fal fa-times-circle text-danger"></i>';
            $row['username_display'] = $row['username'] . ' ' . $verifiedIcon;

            // Format mobile with device ID
            $row['mobile_display'] = $row['mobile'] . '<br/><small>Device: ' . $row['device_id'] . '</small>';

            // Format active status
            $row['is_active_display'] = ($row['is_active'] == 1) ? 'Yes' : 'No';

            // Format created date
            $row['created_display'] = isset($row['created']) ? date('Y-m-d H:i', strtotime($row['created'])) : '';

            $arrResult[] = array(
                'action' => $row['action'],
                'photo' => $row['photo'],
                'username' => $row['username_display'],
                'pp_code' => $row['pp_code'],
                'name' => $row['name'],
                'mobile' => $row['mobile_display'],
                'address' => $row['address'],
                'created' => $row['created_display'],
                'is_active' => $row['is_active_display'],
                'user_note' => $row['user_note']
            );
        }

        $sEcho = $request->getPost("sEcho");
        if (!$sEcho) $sEcho = 0;

        $draw = $request->getPost("draw") ?? $request->getPost("sEcho") ?? 0;

        return $this->response->setJSON([
            "draw" => intval($draw),
            "recordsTotal" => count($arrResult),
            "recordsFiltered" => count($arrResult),
            "data" => $arrResult
        ]);
    }

    public function edit($id = 0)
    {
        if ($this->loadPrivileges()) {
            $request = service('request');

            // Check if Ajax request (for modal)
            if ($request->isAJAX() || $request->getGet('ajax')) {
                // Return JSON for modal
                if ($id > 0) {
                    $record = $this->mUser->findByUserId($id);

                    if ($record) {
                        // Clear password for security
                        $record["pwd"] = "";

                        return $this->response->setJSON($record);
                    } else {
                        return $this->response->setJSON([
                            'error_message' => 'User not found'
                        ]);
                    }
                } else {
                    // New user - return empty data
                    return $this->response->setJSON([
                        "user_id" => 0,
                        "username" => "",
                        "pwd" => "",
                        "pp_code" => $this->mUser->getUniquePPCode(),
                        "name" => "",
                        "address" => "",
                        "email" => "",
                        "mobile" => "",
                        "is_active" => 1,
                        "user_type_id" => PP_USER,
                        "referral_user_id" => 0,
                        "device_id" => "",
                        "email_verified" => 0,
                        "is_send_mail" => 0,
                        "is_account_verified" => 0,
                        "user_note" => ""
                    ]);
                }
            }

            // Original full-page view (keep for backward compatibility)
            $data["menu_generate"] = $this->getTemporaryMenu();
            $data["currentPage"] = $this->currentPage;

            if (!$request->getPost("http_referer")) {
                if (isset($_SERVER['HTTP_REFERER']))
                    $data["http_referer"] = $_SERVER['HTTP_REFERER'];
                else
                    $data["http_referer"] = base_url('user');
            } else {
                $data["http_referer"] = $request->getPost("http_referer");
            }

            $data["message"] = session()->getFlashdata('message');
            $data["error_message"] = session()->getFlashdata('error_message');

            $data["user_types"] = $this->mUserType->generateList(null, "user_type_id", null, "user_type_id", "user_type");

            if ($id == 0) {
                $data['title'] = "Add User / Register";
                $record = array(
                    "user_id" => 0,
                    "username" => "",
                    "pwd" => "",
                    "pp_code" => $this->mUser->getUniquePPCode(),
                    "name" => "",
                    "address" => "",
                    "email" => "",
                    "mobile" => "",
                    "is_active" => 1,
                    "user_type_id" => PP_USER,
                    "balance" => 0,
                    "price_id" => 0,
                    "referral_user_id" => 0,
                    "device_id" => "",
                    "email_verified" => 0,
                    "is_send_mail" => 0,
                    "is_account_verified" => 0,
                    "user_note" => "",
                    "telegram_id" => "",
                    "company_id" => 0
                );
                $record['profile_picture'] = base_url('assets/img/no_image.png');
                $record['id_card_image_file'] = base_url('assets/img/no_image.png');
                $record['photo_image_file'] = base_url('assets/img/no_image.png');
            } else {
                $data['title'] = "Edit User";
                if ($record = $this->mUser->findByUserId($id)) {
                    if (isset($record['profile_picture']) && $record['profile_picture'] != '') {
                        $pathGroup = ceil($record['user_id'] / 10000);
                        $record['profile_picture'] = base_url("assets/img/user/{$pathGroup}/{$record['user_id']}/{$record['profile_picture']}");
                    } else {
                        $record['profile_picture'] = base_url('assets/img/no_image.png');
                    }

                    if (isset($record["balance_hash"]) && isset($record["balance"]) && isset($record["user_id"])) {
                        if ($record["balance_hash"] != md5($record["balance"] . "|" . $record["user_id"])) {
                            $record["balance"] = 0;
                        }
                    }

                    $record["pwd"] = "";
                    $record['id_card_image_file'] = base_url('assets/img/no_image.png');
                    $record['photo_image_file'] = base_url('assets/img/no_image.png');
                }
            }

            $data["record"] = $record;

            $template_data["contents"] = view('user/edit', $data, ['saveData' => false]);
            return view('layout', $template_data);
        }
    }

    public function save_data()
    {
        if ($this->loadPrivileges()) {
            $request = service('request');

            try {
                $record = $request->getPost();

                // Handle checkboxes
                if (!isset($record['email_verified'])) $record['email_verified'] = 0;
                if (!isset($record['is_send_mail'])) $record['is_send_mail'] = 0;
                if (!isset($record['is_account_verified'])) $record['is_account_verified'] = 0;

                // Remove unwanted fields
                unset($record['http_referer']);

                if ($record['user_id'] > 0) {
                    // UPDATE
                    if ($record['pwd'] == '') {
                        unset($record['pwd']);
                    } else {
                        $record['pwd'] = md5($record['pwd']);
                    }

                    $record["modified"] = date("Y-m-d H:i:s");
                    $record["modified_by"] = session()->get('user_id');

                    if ($this->mUser->update(array("user_id" => intval($record["user_id"])), $record)) {
                        return $this->response->setJSON([
                            'message' => 'Data updated successfully'
                        ]);
                    } else {
                        return $this->response->setJSON([
                            'error_message' => 'Failed to update data'
                        ]);
                    }
                } else {
                    // INSERT
                    $record["created"] = date("Y-m-d H:i:s");
                    $record["created_by"] = session()->get('user_id');
                    $record['pp_code'] = $this->mUser->getUniquePPCode();
                    $record['pwd'] = md5($record['pwd']);
                    $record['company_id'] = session()->get('company_id');

                    unset($record["user_id"]);

                    if ($newUserId = $this->mUser->insert($record)) {
                        // Assign to default group
                        $this->dUserGroup->delete(array("user_id" => $newUserId));
                        $this->dUserGroup->insert(array("user_id" => $newUserId, "group_id" => GROUP_USER_PP_LOKET));

                        return $this->response->setJSON([
                            'message' => 'New user created successfully',
                            'user_id' => $newUserId
                        ]);
                    } else {
                        return $this->response->setJSON([
                            'error_message' => 'Failed to save new user'
                        ]);
                    }
                }
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'error_message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }

        return $this->response->setJSON([
            'error_message' => 'Access denied'
        ]);
    }

    public function info()
    {
        if ($this->loadPrivileges(true)) {
            $request = service('request');

            $data["menu_generate"] = $this->getTemporaryMenu();
            $data["currentPage"] = $this->currentPage;

            $id = session()->get('user_id');

            if (!$request->getPost("http_referer")) {
                if (isset($_SERVER['HTTP_REFERER']))
                    $data["http_referer"] = $_SERVER['HTTP_REFERER'];
                else
                    $data["http_referer"] = base_url('user');
            } else {
                $data["http_referer"] = $request->getPost("http_referer");
            }

            $data["message"] = session()->getFlashdata('message');
            $data["error_message"] = session()->getFlashdata('error_message');

            if ($request->getPost("btnCancel")) {
                return redirect()->to($data["http_referer"]);
            } else if ($request->getPost("btnSave") && $request->getPost("btnSave") == "1") {
                //save
                if ($id > 0) {
                    //update
                    $record = array();
                    $record["modified"] = date("Y-m-d H:i:s");
                    $record["modified_by"] = $id;
                    $record["name"] = $request->getPost("name");

                    // Validation for restricted names
                    if (session()->get('user_type_id') != Constants::USER_INTERNAL && stripos($record["name"], "Ikon Media") !== false) {
                        $data["error_message"] = 'Nama akun yang anda masukkan mengandung kata yang berisi nama perusahaan Ikon Media Indonesia. Penggunaan nama tersebut tidak diperbolehkan.';
                    } else {
                        $record["address"] = $request->getPost("address");
                        $record["mobile"] = $request->getPost("mobile");

                        if ($this->mUser->update(array("user_id" => $id), $record)) {
                            $data["message"] = "Data berhasil disimpan";
                        } else {
                            $data["error_message"] = "Gagal untuk menyimpan data";
                        }
                    }
                }
            }

            $data['title'] = "My Information";
            helper('smart_form');
            $record = $this->mUser->findByUserId($id);
            $record["pwd"] = "";

            if (!isset($record['profile_picture'])) $record['profile_picture'] = '';

            if ($record['profile_picture'] != '') {
                $pathGroup = ceil($record['user_id'] / 10000);
                $record['profile_picture'] = base_url("assets/img/user/{$pathGroup}/{$record['user_id']}/{$record['profile_picture']}");
            } else {
                $record['profile_picture'] = base_url('assets/img/no_image.png');
            }

            $data["record"] = $record;

            $template_data["contents"] = view('user/info', $data, ['saveData' => false]);
            return view('layout', $template_data);
        }
    }

    public function change_password()
    {
        if ($this->loadPrivileges(true)) {
            $request = service('request');

            $data["menu_generate"] = $this->getTemporaryMenu();
            $data["currentPage"] = $this->currentPage;

            $id = intval(session()->get('user_id'));

            if (!$request->getPost("http_referer")) {
                if (isset($_SERVER['HTTP_REFERER']))
                    $data["http_referer"] = $_SERVER['HTTP_REFERER'];
                else
                    $data["http_referer"] = base_url('dashboard');
            } else {
                $data["http_referer"] = $request->getPost("http_referer");
            }

            $data["message"] = session()->getFlashdata('message');
            $data["error_message"] = session()->getFlashdata('error_message');

            if ($request->getPost("btnCancel")) {
                return redirect()->to($data["http_referer"]);
            } else if ($request->getPost("btnSave") && $request->getPost("btnSave") == "1") {
                //save
                $bolSuccess = false;
                if ($record = $this->mUser->findByUserId($id)) {
                    if ($request->getPost("old_password") && $record['pwd'] == md5($request->getPost("old_password"))) {
                        if ($request->getPost("new_password1")) {
                            if ($request->getPost("new_password1") == $request->getPost("old_password")) {
                                $data["error_message"] = "Password/sandi baru sama dengan sandi lama anda. Perubahan tidak dilakukan.";
                            } else {
                                $bolSuccess = $this->mUser->update(
                                    array("user_id" => $record["user_id"]),
                                    array("pwd" => md5($request->getPost("new_password1")))
                                );
                            }
                        }
                        if ($bolSuccess) {
                            //TODO: email to user that password has been changed
                            $data["message"] = "Password/sandi baru telah tersimpan";
                        } else if (!isset($data["error_message"])) {
                            $data["error_message"] = "Gagal untuk menyimpan sandi baru";
                        }
                    } else {
                        $data["error_message"] = "Password/sandi lama anda tidak sesuai";
                    }
                } else {
                    $data["error_message"] = "Pengguna tidak ditemukan";
                }
            }
            helper('smart_form');

            $template_data["contents"] = view('user/change_password', $data, ['saveData' => false]);

            return view('layout', $template_data);
        }
    }

    public function change_email()
    {
        if ($this->loadPrivileges(true)) {
            $request = service('request');

            $data["menu_generate"] = $this->getTemporaryMenu();
            $data["currentPage"] = $this->currentPage;

            $data['email'] = session()->get('username');
            $id = session()->get('user_id');

            if (!$request->getPost("http_referer")) {
                if (isset($_SERVER['HTTP_REFERER']))
                    $data["http_referer"] = $_SERVER['HTTP_REFERER'];
                else
                    $data["http_referer"] = base_url('dashboard');
            } else {
                $data["http_referer"] = $request->getPost("http_referer");
            }

            $data["message"] = session()->getFlashdata('message');
            $data["error_message"] = session()->getFlashdata('error_message');

            if ($request->getPost("btnCancel")) {
                return redirect()->to($data["http_referer"]);
            } else if ($request->getPost("btnSave") && $request->getPost("btnSave") == "1") {
                //save
                $bolSuccess = false;
                if ($record = $this->mUser->findByUserId($id)) {
                    if ($record['pwd'] == md5($request->getPost("password"))) {
                        if ($request->getPost("new_email")) {
                            if ($request->getPost("new_email") == $record["username"]) {
                                $data["error_message"] = "Email baru sama dengan email lama anda.";
                            } else {
                                $newUser = $this->mUser->findByUsername($request->getPost("new_email"));
                                if ($newUser) {
                                    $data["error_message"] = "User dengan email " . $request->getPost("new_email") . " sudah ada. Silakan ganti alamat email dengan yang lain.";
                                } else {
                                    $bolSuccess = $this->mUser->update(
                                        array("user_id" => $record["user_id"]),
                                        array("username" => $request->getPost("new_email"))
                                    );
                                    if ($bolSuccess) {
                                        $data["message"] = "Email baru akun anda telah tersimpan. Silakan logout lalu masuk kembali.";
                                    } else {
                                        $data["error_message"] = "Gagal untuk menyimpan data";
                                    }
                                }
                            }
                        }
                    } else {
                        $data["error_message"] = "Perubahan email batal karena password/sandi yang anda masukkan salah.";
                    }
                } else {
                    $data["error_message"] = "Pengguna tidak ditemukan";
                }
            }

            $template_data["contents"] = view('user/change_email', $data, ['saveData' => false]);
            return view('layout', $template_data);
        }
    }

    public function getdata_user()
    {
        $this->loadPrivilegesFromUri("user", "index");
        $request = service('request');

        $arrData = array();
        if ($this->privilegeIndex == 0) {
            return $this->response->setJSON($arrData);
        }

        if (session()->get('user_type_id') == Constants::PP_USER) {
            $arrData[] = array(
                "id" => session()->get('user_id'),
                "text" => session()->get('name') . " (" . session()->get('username') . ")"
            );
        } else {
            $q = "";
            if ($request->getGet("q")) $q = $request->getGet("q");

            $criteria = [];
            if ($q != "") {
                $criteria['$or'] = [
                    ['name' => ['$regex' => $q, '$options' => 'i']],
                    ['username' => ['$regex' => $q, '$options' => 'i']]
                ];
            }

            $arrResult = $this->mUser->findAll($criteria, "user_id, name, username", "name");
            foreach ($arrResult as $row) {
                $arrData[] = array(
                    "id" => $row["user_id"],
                    "text" => $row["name"] . " (" . $row["username"] . ")"
                );
            }
        }

        return $this->response->setJSON($arrData);
    }

    public function getdata_user_all()
    {
        $this->loadPrivilegesFromUri("admin_user", "index", false);
        $request = service('request');

        $arrData = array();
        if ($this->privilegeIndex == 0) {
            return $this->response->setJSON($arrData);
        }

        if (session()->get('user_type_id') != Constants::USER_INTERNAL) {
            $arrData[] = array(
                "id" => session()->get('user_id'),
                "text" => session()->get('name') . " (" . session()->get('username') . ")"
            );
        } else {
            $q = "";
            if ($request->getGet("q")) $q = $request->getGet("q");

            $criteria = [];
            if ($q != "") {
                $criteria['$or'] = [
                    ['name' => ['$regex' => $q, '$options' => 'i']],
                    ['username' => ['$regex' => $q, '$options' => 'i']]
                ];
            }

            $arrResult = $this->mUser->findAll($criteria, "user_id, name, username", "name");
            foreach ($arrResult as $row) {
                $arrData[] = array(
                    "id" => $row["user_id"],
                    "text" => $row["name"] . " (" . $row["username"] . ")"
                );
            }
        }

        return $this->response->setJSON($arrData);
    }

    public function getdata_user_mobile()
    {
        $this->loadPrivileges(false, false);
        $request = service('request');

        $arrData = array();
        if ($this->privilegeIndex == 0) {
            return $this->response->setJSON($arrData);
        }

        if (session()->get('user_type_id') == Constants::PP_USER) {
            $arrData[] = array(
                "id" => session()->get('user_id'),
                "text" => session()->get('name') . " (" . session()->get('username') . ")"
            );
        } else {
            $q = "";
            if ($request->getGet("q")) $q = $request->getGet("q");

            $criteria = ['user_type_id' => Constants::PP_USER];
            if ($q != "") {
                $criteria['$or'] = [
                    ['name' => ['$regex' => $q, '$options' => 'i']],
                    ['username' => ['$regex' => $q, '$options' => 'i']],
                    ['mobile' => ['$regex' => $q, '$options' => 'i']]
                ];
            }

            $arrResult = $this->mUser->findAll($criteria, "user_id, name, username, mobile", "name");
            foreach ($arrResult as $row) {
                $mobile = isset($row["mobile"]) ? $row["mobile"] : '';
                $arrData[] = array(
                    "id" => $row["user_id"],
                    "text" => $row["name"] . " - " . $row["username"] . " (" . $mobile . ")"
                );
            }
        }

        return $this->response->setJSON($arrData);
    }

    public function getdata_group()
    {
        $this->loadPrivileges(false, false);
        $request = service('request');

        $arrData = array();
        if ($this->privilegeIndex == 0) {
            return $this->response->setJSON($arrData);
        }

        $q = "";
        if ($request->getGet("q")) $q = $request->getGet("q");

        $criteria = ['is_active' => 1];
        if ($q != "") {
            $criteria['$or'] = [
                ['group_name' => ['$regex' => $q, '$options' => 'i']],
                ['group_code' => ['$regex' => $q, '$options' => 'i']]
            ];
        }

        $arrResult = $this->mGroup->findAll($criteria, "group_id, group_code, group_name", "group_name");
        foreach ($arrResult as $row) {
            $arrData[] = array(
                "id" => $row["group_id"],
                "text" => $row["group_name"] . " (" . $row["group_code"] . ")"
            );
        }

        return $this->response->setJSON($arrData);
    }

    public function upload_doc()
    {
        $this->loadPrivilegesFromUri("user", "edit", false);
        $request = service('request');

        if ($this->privilegeUpdate == 0) {
            $this->loadPrivilegesFromUri("user", "info", false);
            if ($this->privilegeUpdate == 0) {
                $this->loadPrivilegesFromUri("user", "index", false);
                if ($this->privilegeIndex == 0) {
                    echo "You don't have privilege to access this page";
                    return;
                }
            }
        }

        $user_id = intval($request->getPost("user_id"));
        $doc_type = $request->getPost("doc_type");
        $hash = $request->getPost("hash");

        if (md5("photos|||" . $user_id) != $hash) {
            die("Invalid security hash");
        }

        $file = $request->getFile('photo');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $strFileName = md5(time() . $file->getName()) . ".jpg";
            $fileResult = $this->uploadPhoto($user_id, "", $strFileName, $file);
            $pathGroup = ceil($user_id / 10000);

            if ($fileResult != "") {
                if ($doc_type == 'profile_picture') {
                    if ($oldData = $this->mUser->findByUserId($user_id)) {
                        $this->mUser->update(
                            array("user_id" => $user_id),
                            array($doc_type => $fileResult)
                        );

                        if (session()->get('user_id') == $user_id) {
                            session()->set('profile_picture', base_url("assets/img/user/{$pathGroup}/{$user_id}/{$fileResult}"));
                        }
                    } else {
                        echo "User not found";
                        return;
                    }
                }
                $fileResult = base_url("assets/img/user/{$pathGroup}/{$user_id}/{$fileResult}");
                echo "SUKSES|||" . $fileResult;
                return;
            }
        }
        echo "Gagal untuk mengupload file";
    }

    private function uploadPhoto($userId, $strOldFile, $strNewFile, $file)
    {
        $folder = FCPATH . 'assets/img/user';
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $pathGroup = ceil($userId / 10000);
        $folder = FCPATH . 'assets/img/user/' . $pathGroup . '/';
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $folder = FCPATH . 'assets/img/user/' . $pathGroup . '/' . $userId . '/';
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        if ($strOldFile != "") {
            @unlink($folder . $strOldFile);
        }

        $fileResult = "";
        if ($strNewFile != '') {
            $file->move($folder, $strNewFile);

            // Security check: ensure no PHP code in uploaded file
            $checkData = file_get_contents($folder . $strNewFile);
            if (stripos($checkData, "<?php") !== false) {
                @unlink($folder . $strNewFile);
                return "";
            }
            $fileResult = $strNewFile;
        }

        return $fileResult;
    }
}
