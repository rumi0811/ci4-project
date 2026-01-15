<?php

namespace App\Controllers;

use App\Controllers\MYController;

class Supplier extends MYController
{
    var $userID;
    var $userGroup;

    public function __construct()
    {
        parent::__construct();
        helper('html_escape');
        $this->datagrid = new \App\Libraries\DatagridMongo();
    }

    public function index()
    {
        if ($this->loadPrivileges()) {
            $data = session()->get();

            $data["menu_generate"] = $this->dataMenuAccess;
            $data['currentPage'] = $this->currentPage;

            helper('general');
            $moduleId = session()->get('sessionModuleID');
            $data["title"] = "Supplier";

            $strKriteria = "";

            $this->datagrid->caption = 'Daftar Supplier';
            $this->datagrid->addColumnNumbering();
            if ($this->privilegeUpdate) {
                $this->datagrid->addRowButtonEdit('id_pos_supplier', 'javascript', 'editData(data.id_pos_supplier);');
            }
            if ($this->privilegeDelete) {
                $this->datagrid->addRowButtonDelete('id_pos_supplier', 'pos_supplier');
            }
            $this->datagrid->addActionColumn('Tindakan', 'id_pos_supplier', '', array('colProperties' => "sClass: 'text-center', sWidth: '120px'"));

            $this->datagrid->addColumn(array("title" => "Nama", 'dataIndex' => 'supplier_name', 'type' => 'string', 'colProperties' => "width:'300px'"));
            $this->datagrid->addColumn(array("title" => "Alamat", 'dataIndex' => 'address_line1', 'type' => 'string', 'colProperties' => "sClass: 'text-left'"));
            $this->datagrid->addColumn(array("title" => "No Telpon", 'dataIndex' => 'phone_number', 'type' => 'string', 'colProperties' => "sClass: 'text-left', width: '150px'"));
            $this->datagrid->addColumn(array("title" => "Email", 'dataIndex' => 'email', 'type' => 'string', 'colProperties' => "sClass: 'text-center', width: '100px'"));
            $this->datagrid->defaultOrder = array('dataIndex' => 'supplier_name', 'dir' => 'asc');

            $this->datagrid->addRenderer(array(
                'title' => 'Aktif',
                'colRenderer' => "if (data == 1) return '<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>'; else return '<span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span>';"
            ));
            $strTambahData = (lang('Tambah Data Baru') == '') ? 'Tambah Data Baru' : lang('Tambah Data Baru');

            $this->datagrid->addButton("btnAddNew", "btnAddNew", "button", "<i class='fal fa-plus'></i> $strTambahData", "javascript:editData(0)", '', "btn btn-primary");

            if ($this->request->getPost("dataName") != "")
                $strKriteria .= "AND pi.item_name = '" . $this->request->getPost("dataName") . "'";
            if ($this->request->getPost("id_pos_item_category") != "")
                $strKriteria .= "AND pi.id_pos_item_category = " . $this->request->getPost("id_pos_item_category") . "";

            $this->datagrid->bindTable('pos_supplier', 'is_deleted = 0');
            $this->datagrid->generate();

            $data["datagrid"] = $this->datagrid->generate();
            $data["form"] = $this->get_form();

            //$template_data["contents"] = view('supplier/supplier', $data);

            //return view('layout', $template_data);
        }
    }

    function getData($strDataID)
    {
        $strDataID = intval($strDataID);
        if ($strDataID == 0) {
            $data = array(
                'id_pos_supplier' => 0,
                'supplier_code' => '',
                'supplier_name' => '',
                'address_line1' => '',
                'address_line2' => '',
                'phone_number' => '',
                'fax_number' => '',
                'email' => '',
                'note' => '',
                'city' => '',
                'province' => '',
                'country' => '',
                'post_code' => '',
                'is_deleted' => 0,
                'created_by' => '',
                'created' => '',
                'modified_by' => '',
                'modified' => ''
            );
        } else {
            $db = \Config\Database::connect();
            $data = $db->query("SELECT * FROM pos_supplier WHERE id_pos_supplier = $strDataID")->getRowArray();
        }
        return $data;
    }

    private function get_form()
    {
        $form = new \App\Libraries\Form(array('action' => '', 'id' => 'formSupplier'));
        $form->caption = 'Entry Supplier';
        $arrData = $this->getData(0);

        $form->addHidden('id_pos_supplier', $arrData['id_pos_supplier']);
        $form->addInput('Kode Supplier ', 'supplier_code', $arrData['supplier_code'], array('align' => 'left'), "string", true, true, "", "", true, 4, '');
        $form->addInput('Nama Supplier', 'supplier_name', $arrData['supplier_name'], array('align' => 'left'), "string", true, true, "", "", true, 8, '');
        $form->addTextArea('Alamat Line 1', 'address_line1', $arrData['address_line1'], array('rows' => 1), "string", true, true);
        $form->addTextArea('Alamat Line 2', 'address_line2', $arrData['address_line2'], array('rows' => 3), "string", false, true);

        $form->addInput('No Fax', 'fax_number', $arrData['fax_number'], array('align' => 'left'), "number", false, true, "", "", true, 4, '');
        $form->addInput('No Telpon', 'phone_number', $arrData['phone_number'], array('align' => 'left'), "number", false, true, "", "", true, 4, '');
        $form->addInput('Email', 'email', $arrData['email'], array(), "string", false, true, "", "", true, 4, '');
        $form->addInput('Kota', 'city', $arrData['city'], array(), "string", false, true, "", "", true, 4, '');
        $form->addInput('Provinsi', 'province', $arrData['province'], array(), "string", false, true, "", "", true, 4, '');
        $form->addInput('Kode Pos', 'post_code', $arrData['post_code'], array(), "number", false, true, "", "", true, 4, '');
        $form->addInput('Negara', 'country', $arrData['country'], array(), "string", false, true, "", "", true, 4, '');

        $form->addButton('btnSave', '<i class="fal fa-save"></i> Simpan', array('class' => 'btn btn-success'), true, "", "", "");
        $form->addButton('btnClose', '<i class="fal fa-times"></i> Close', array('class' => 'btn btn-danger', 'data-dismiss' => 'modal', 'aria-hidden' => 'true'), true, "", "", "");
        return $form->render();
    }

    public function edit($id = 0)
    {
        if ($this->loadPrivileges(false, false)) {
            $this->pos_supplier = new \App\Models\PosSupplier();
            if ($data = $this->pos_supplier->findByIdPosSupplier($id)) {
                echo json_encode($data);
                die();
            } else {
                echo json_encode(['error_message' => 'Data supplier tidak ditemukan']);
            }
        }
        echo json_encode(['error_message' => 'Anda tidak memiliki akses di halaman supplier']);
    }

    public function save()
    {
        if ($this->loadPrivileges(false, false)) {
            $strDataID = intval($this->request->getPost('id_pos_supplier'));
            $data = $this->request->getPost();

            $isSuccess = false;

            $db = \Config\Database::connect();
            $arrFieldSp = array();
            $fieldSp = $db->getFieldNames('pos_supplier');
            foreach ($fieldSp as $field) {
                if (isset($data[$field])) {
                    $arrFieldSp[$field] = $data[$field];
                }
            }
            if ($strDataID == 0) {
                unset($arrFieldSp['id_pos_supplier']);
                $arrFieldSp['id_adm_company'] = $this->company_id;
                $arrFieldSp['is_deleted'] = 0;
                $arrFieldSp['created'] = date('Y-m-d H:i:s');
                $arrFieldSp['created_by'] = session()->get('sessionUserID');
                $isSuccess = $db->table('pos_supplier')->insert($arrFieldSp);
                $strDataID = $db->insertID();
            } else {
                $arrFieldSp['modified'] = date('Y-m-d H:i:s');
                $arrFieldSp['modified_by'] = session()->get('sessionUserID');
                $db->table('pos_supplier')->update($arrFieldSp, array('id_pos_supplier' => $strDataID));
                if ($db->affectedRows() > 0)
                    $isSuccess = true;
            }

            if (!$isSuccess)
                $dataResponse['error_message'] = 'Gagal menyimpan data supplier';
            else
                $dataResponse['message'] = 'Data supplier berhasil disimpan';
        } else {
            $dataResponse['error_message'] = 'Anda tidak memiliki hak untuk edit data supplier';
        }
        echo json_encode($dataResponse);
    }

    function deleteSupplier()
    {
        $dataID = $this->request->getGet('id_pos_supplier') ?: $this->request->getPost('id_pos_supplier');
        $data['is_deleted'] = 1;

        $db = \Config\Database::connect();
        $success = $db->table('pos_supplier')->update($data, "id_pos_supplier = $dataID");

        if ($success)
            session()->setFlashdata('form1_success_message', 'data has been deleted');
        else
            session()->setFlashdata('form1_error_message', 'failed to delete data');

        echo "<script>window.opener.location.reload();close();</script>";
    }
}
