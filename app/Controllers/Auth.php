<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        helper(['form', 'url']);
    }

    public function index()
    {

        if (session()->get('logged_in')) {
            return redirect()->to('dashboard');
        }


        $returnUrl = $this->request->getGet('returnUrl') ?? '';


        $message = session()->getFlashdata('message') ?? '';


        $data = [
            'title' => 'Login - IKON POS',
            'returnUrl' => $returnUrl,
            'username' => '',
            'message' => $message
        ];

        return view('auth/login', $data);
    }


    public function login()
    {

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $returnUrl = $this->request->getPost('returnUrl') ?? '';

        // Validate input
        if (empty($username) || empty($password)) {
            session()->setFlashdata('message', 'Username dan password harus diisi');
            return redirect()->to('login');
        }


        $userModel = new UserModel();


        $user = $userModel->getUserByUsername($username);


        if (empty($user)) {
            session()->setFlashdata('message', 'Username tidak ditemukan atau tidak aktif');
            return redirect()->to('login');
        }


        if (md5($password) !== $user['pwd']) {
            session()->setFlashdata('message', 'Password salah');
            return redirect()->to('login');
        }


        $sessionData = [
            'logged_in' => true,
            'username' => $user['username'],
            'name' => $user['name'],
            'user_type_id' => $user['user_type_id'],
            'user_id' => (string)$user['user_id'],
            'company_id' => $user['company_id']
        ];

        session()->set($sessionData);


        if (!empty($returnUrl)) {
            return redirect()->to($returnUrl);
        } else {
            return redirect()->to('dashboard');
        }
    }

    public function logout()
    {

        session()->destroy();


        return redirect()->to('login');
    }
}
