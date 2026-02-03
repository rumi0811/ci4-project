<?php

namespace App\Controllers;

use App\Controllers\MYController;


class Dashboard extends MYController
{
    public function __construct()
    {
        helper(['url', 'form']);
    }

    public function index()
    {
        // Test 1: Cek login
        if (!session()->get('logged_in')) {
            return redirect()->to('login');
        }

        // Test 2: Log basic info
        log_message('debug', '🔥 SEKAR: Dashboard index() called');

        // Test 3: Cek apakah method getTemporaryMenu exists
        if (method_exists($this, 'getTemporaryMenu')) {
            log_message('debug', '🔥 SEKAR: getTemporaryMenu EXISTS');

            try {
                $menuHtml = $this->getTemporaryMenu();
                log_message('debug', '🔥 SEKAR: getTemporaryMenu returned: ' . gettype($menuHtml));
            } catch (\Exception $e) {
                log_message('error', '🔥 SEKAR ERROR: ' . $e->getMessage());
                $menuHtml = '<ul id="js-nav-menu" class="nav-menu"><li>ERROR</li></ul>';
            }
        } else {
            log_message('error', '🔥 SEKAR: getTemporaryMenu NOT EXISTS!');
            $menuHtml = '<ul id="js-nav-menu" class="nav-menu"><li>METHOD NOT FOUND</li></ul>';
        }

        $data = [
            'title' => 'Dashboard - IKON POS',
            'currentPage' => [
                'page_name' => 'Dashboard - IKON POS',
                'menu_name' => 'Home',
                'parent_menu_name' => '',
                'parent_menu_file_name' => ''
            ],
            'menu_generate' => $menuHtml,
        ];

        log_message('debug', '🔥 SEKAR: About to render view');
        return view('dashboard/index', $data);
    }

    /**
     * Generate menu from database
     * TODO: Implement actual menu generation from database
     */
    private function generateMenu()
    {
        $html = '<ul id="js-nav-menu" class="nav-menu">';

        // Home
        $html .= '<li class="active">';
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
        $html .= '  <a href="' . base_url('customer') . '" title="Customers">';
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

    /**
     * AJAX endpoint for dashboard datatable
     * This will be called by JavaScript to load dashboard data
     */
    public function datatable()
    {
        // TODO: Implement actual data fetching from models
        // For now, return dummy data structure

        $response = [
            'has_data' => true,
            'data' => [],
            'summary' => [
                'qty_transaction' => 0,
                'sub_total' => 0,
                'discount_total' => 0,
                'grand_total' => 0,
                'gross_profit' => 0,
                'avg_sale' => 0,
                'gross_margin' => '0%'
            ],
            'charts' => [
                'dow' => [
                    'categories' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                    'grand_total' => [0, 0, 0, 0, 0, 0, 0],
                    'qty_transaction' => [0, 0, 0, 0, 0, 0, 0]
                ],
                'hourly' => [
                    'categories' => range(0, 23),
                    'grand_total' => array_fill(0, 24, 0),
                    'qty_transaction' => array_fill(0, 24, 0)
                ]
            ]
        ];

        return $this->response->setJSON($response);
    }
}
