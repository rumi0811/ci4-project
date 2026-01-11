<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Test route TANPA filter (bisa dihapus nanti kalau sudah production)
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

// Auth routes (tanpa filter)
$routes->get('/', 'Auth::index');
$routes->get('login', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('auth/logout', 'Auth::logout');
$routes->get('logout', 'Auth::logout');

// Protected routes (dengan filter) - GANTI JADI HOME!
$routes->get('dashboard', 'Home::index', ['filter' => 'auth']);
$routes->post('home/datatable', 'Home::datatable');

// Product Item routes (AFTER line "post('home/datatable'...)")
$routes->group('product_item', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ProductItem::index');
    $routes->post('save_data', 'ProductItem::saveData');
    $routes->post('delete_data', 'ProductItem::deleteData');
    $routes->get('load_data', 'ProductItem::loadData');
    $routes->get('get_datagrid_data', 'ProductItem::getDatagridData');
    $routes->get('form_ingredient', 'ProductItem::formIngredient');
    $routes->get('form_addon', 'ProductItem::formAddon');
});
