<?php

namespace App\Models;

use CodeIgniter\Model;
use MongoDB\Client;

class UserModel extends Model
{
    protected $DBGroup = 'default';

    protected $db;
    protected $collection;

    public function __construct()
    {
        parent::__construct();

        // Connect to MongoDB directly
        $client = new Client("mongodb://localhost:27017");

        // Select database 'ikonpos'
        $database = $client->selectDatabase('ikon_pos_db');

        // Select collection 'm_user'
        $this->collection = $database->selectCollection('m_user');
    }

    public function getUserByUsername($username)
    {
        try {
            $user = $this->collection->findOne([
                'username' => $username,
                'is_active' => 1
            ]);

            if ($user) {
                return json_decode(json_encode($user), true);
            }

            return null;
        } catch (\Exception $e) {
            log_message('error', 'UserModel::getUserByUsername - Error: ' . $e->getMessage());
            return null;
        }
    }

    public function authenticate($username, $password)
    {
        try {
            $user = $this->getUserByUsername($username);

            if (!$user) {
                return null;
            }

            if (md5($password) === $user['pwd']) {
                return $user;
            }

            return null;
        } catch (\Exception $e) {
            log_message('error', 'UserModel::authenticate - Error: ' . $e->getMessage());
            return null;
        }
    }
}
