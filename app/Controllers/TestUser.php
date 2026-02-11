<?php

namespace App\Controllers;

class TestUser extends BaseController
{
    public function index()
    {
        return "TestUser works!";
    }

    public function datatable()
    {
        return $this->response->setJSON([
            'test' => 'datatable works!',
            'sEcho' => 1,
            'iTotalRecords' => 0,
            'aaData' => []
        ]);
    }
}
