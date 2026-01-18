<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('test-models', function () {
    try {
        $mProduct = new \App\Models\MProduct();
        $mOutlet = new \App\Models\MOutlet();
        $mUser = new \App\Models\MUser();

        echo "✅ All models loaded successfully!<br><br>";

        // Test 1: Get ALL products (no filter)
        echo "<b>Test 1: Get ALL products (no filter)</b><br>";
        $allProducts = $mProduct->findAll();
        $arrProducts = [];
        if ($allProducts) {
            foreach ($allProducts as $p) {
                $arrProducts[] = $p;
            }
        }
        echo "Found " . count($arrProducts) . " total products<br><br>";

        // Test 2: Get ALL outlets
        echo "<b>Test 2: Get ALL outlets</b><br>";
        $allOutlets = $mOutlet->findAll();
        $arrOutlets = [];
        if ($allOutlets) {
            foreach ($allOutlets as $o) {
                $arrOutlets[] = $o;
            }
        }
        echo "Found " . count($arrOutlets) . " total outlets<br><br>";

        // Test 3: Show first product if exists
        if (count($arrProducts) > 0) {
            echo "<b>First Product:</b><br>";
            echo "<pre>";
            print_r($arrProducts[0]);
            echo "</pre>";
        }

        // Test 4: Show first outlet if exists
        if (count($arrOutlets) > 0) {
            echo "<b>First Outlet:</b><br>";
            echo "<pre>";
            print_r($arrOutlets[0]);
            echo "</pre>";
        }
    } catch (\Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . "<br>";
        echo "Line: " . $e->getLine();
    }
});

// Authentication Routes (explicit for clean URLs)
$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');
$routes->get('logout', 'Auth::logout');

// Dashboard (explicit route with auth filter)
$routes->get('dashboard', 'Home::index', ['filter' => 'auth']);
$routes->post('home/datatable', 'Home::datatable');

// Product Item routes (RESTORE!)
$routes->group('product_item', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ProductItem::index');
    $routes->post('/', 'ProductItem::index');  // ← TAMBAHKAN INI!
    $routes->post('save_data', 'ProductItem::saveData');
    $routes->post('delete_data', 'ProductItem::deleteData');
    $routes->get('(:any)', 'ProductItem::$1');
    $routes->post('(:any)', 'ProductItem::$1');
});

// Tax routes (RESTORE!)
$routes->group('tax', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Tax::index');
    $routes->post('save_data', 'Tax::save_data');
    $routes->post('delete_data', 'Tax::delete_data');
    $routes->get('(:any)', 'Tax::$1');
    $routes->post('(:any)', 'Tax::$1');
});

// Uom routes (ADD!)
$routes->group('uom', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Uom::index');
    $routes->post('save_data', 'Uom::save_data');
    $routes->post('delete_data', 'Uom::delete_data');
    $routes->get('(:any)', 'Uom::$1');
    $routes->post('(:any)', 'Uom::$1');
});

// Product Category routes
$routes->group('product_category', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ProductCategory::index');
    $routes->post('save_data', 'ProductCategory::save_data');
    $routes->post('delete_data', 'ProductCategory::delete_data');
    $routes->get('(:any)', 'ProductCategory::$1');
    $routes->post('(:any)', 'ProductCategory::$1');
});

// Outlets routes
$routes->group('outlets', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Outlets::index');
    $routes->post('save_data', 'Outlets::save_data');
    $routes->post('delete_data', 'Outlets::delete_data');
    $routes->get('(:any)', 'Outlets::$1');
    $routes->post('(:any)', 'Outlets::$1');
});

// Customers routes
$routes->group('customers', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Customers::index');
    $routes->post('/', 'Customers::index');  // ← TAMBAHKAN INI!
    $routes->post('save_data', 'Customers::save_data');
    $routes->post('delete_data', 'Customers::delete_data');
    $routes->get('(:any)', 'Customers::$1');
    $routes->post('(:any)', 'Customers::$1');
});

// Supplier routes
$routes->group('supplier', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Supplier::index');
    $routes->get('edit/(:num)', 'Supplier::edit/$1');
    $routes->post('save', 'Supplier::save');
    $routes->get('deleteSupplier', 'Supplier::deleteSupplier');
    $routes->post('deleteSupplier', 'Supplier::deleteSupplier');
    $routes->get('(:any)', 'Supplier::$1');
    $routes->post('(:any)', 'Supplier::$1');
});
