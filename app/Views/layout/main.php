<!DOCTYPE html>
<html lang="en-us">

<head>
    <title><?= $title ?? 'IKON POS' ?></title>
    <?= view('layout/includes/header_script') ?>
    <?php if (isset($header_script) && $header_script != ''): ?>
        <?= $header_script ?>
    <?php endif; ?>
</head>

<body class="mod-bg-1 mod-nav-link mod-skin-light">
    <?= view('layout/includes/after_body_script') ?>

    <!-- BEGIN Page Wrapper -->
    <div class="page-wrapper">
        <div class="page-inner">
            <!-- BEGIN Left Aside -->
            <aside class="page-sidebar">
                <div class="page-logo">
                    <a href="#" class="page-logo-link press-scale-down d-flex align-items-center position-relative" data-toggle="modal" data-target="#modal-shortcut">
                        <img src="<?= session()->get('company_image_logo') ?? base_url('assets/img/logo.png') ?>" alt="Company Logo" aria-roledescription="logo" style="height: 40px!important; width: auto!important">
                        <span class="position-absolute text-white opacity-50 small pos-top pos-right mr-2 mt-n2"></span>
                        <i class="fal fa-angle-down d-inline-block ml-1 fs-lg color-primary-300"></i>
                    </a>
                </div>

                <!-- BEGIN PRIMARY NAVIGATION -->
                <nav id="js-primary-nav" class="primary-nav" role="navigation">
                    <div class="nav-filter">
                        <div class="position-relative">
                            <input type="text" id="nav_filter_input" placeholder="Filter menu" class="form-control" tabindex="0">
                            <a href="#" onclick="return false;" class="btn-primary btn-search-close js-waves-off" data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar">
                                <i class="fal fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="info-card">
                        <img src="<?= session()->get('profile_picture') ?? base_url('assets/img/user/default.png') ?>" alt="Profile Picture" class="profile-image rounded-circle" />
                        <div class="info-card-text">
                            <a href="#" class="d-flex align-items-center text-white">
                                <span class="text-truncate text-truncate-sm d-inline-block">
                                    <?= session()->get('name') ?? 'User' ?>
                                </span>
                            </a>
                            <span class="d-inline-block text-truncate text-truncate-sm"><?= session()->get('username') ?? 'username' ?></span>
                        </div>
                        <img src="<?= base_url('assets/4.5.1/img/card-backgrounds/cover-2-lg.png') ?>" class="cover" alt="cover">
                        <a href="#" onclick="return false;" class="pull-trigger-btn" data-action="toggle" data-class="list-filter-active" data-target=".page-sidebar" data-focus="nav_filter_input">
                            <i class="fal fa-angle-down"></i>
                        </a>
                    </div>

                    <?php
                    if (isset($menu_generate) && is_string($menu_generate)) {
                        echo $menu_generate;
                    } else {
                        // Empty menu for now
                        echo '<ul id="js-nav-menu" class="nav-menu"></ul>';
                    }
                    ?>

                    <div class="filter-message js-filter-message bg-success-600"></div>
                </nav>
                <!-- END PRIMARY NAVIGATION -->
                <!-- NAV FOOTER -->
                <div class="nav-footer shadow-top">
                    <a href="#" onclick="return false;" data-action="toggle" data-class="nav-function-minify" class="hidden-md-down">
                        <i class="ni ni-chevron-right"></i>
                        <i class="ni ni-chevron-right"></i>
                    </a>
                </div> <!-- END NAV FOOTER -->
            </aside>
            <!-- END Left Aside -->

            <div class="page-content-wrapper">
                <!-- BEGIN Page Header -->
                <header class="page-header" role="banner">
                    <!-- we need this logo when user switches to nav-function-top -->
                    <div class="page-logo">
                        <a href="#" class="page-logo-link press-scale-down d-flex align-items-center position-relative" data-toggle="modal" data-target="#modal-shortcut">
                            <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" aria-roledescription="logo">
                            <span class="page-logo-text mr-1">IKON POS</span>
                            <span class="position-absolute text-white opacity-50 small pos-top pos-right mr-2 mt-n2"></span>
                            <i class="fal fa-angle-down d-inline-block ml-1 fs-lg color-primary-300"></i>
                        </a>
                    </div>
                    <!-- DOC: nav menu layout change shortcut -->
                    <div class="hidden-md-down dropdown-icon-menu position-relative">
                        <a href="#" class="header-btn btn js-waves-off" data-action="toggle" data-class="nav-function-hidden" title="Hide Navigation">
                            <i class="ni ni-menu"></i>
                        </a>
                        <ul>
                            <li>
                                <a href="#" class="btn js-waves-off" data-action="toggle" data-class="nav-function-minify" title="Minify Navigation">
                                    <i class="ni ni-minify-nav"></i>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn js-waves-off" data-action="toggle" data-class="nav-function-fixed" title="Lock Navigation">
                                    <i class="ni ni-lock-nav"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- DOC: mobile button appears during mobile width -->
                    <div class="hidden-lg-up">
                        <a href="#" class="header-btn btn press-scale-down" data-action="toggle" data-class="mobile-nav-on">
                            <i class="ni ni-menu"></i>
                        </a>
                    </div>
                    <div class="ml-auto d-flex">

                        <!-- app user menu -->
                        <div>
                            <a href="#" data-toggle="dropdown" title="<?= session()->get('username') ?? 'user' ?>" class="header-icon d-flex align-items-center justify-content-center ml-2">
                                <img src="<?= session()->get('profile_picture') ?? base_url('assets/img/user/default.png') ?>" class="profile-image rounded-circle" style="width: 2rem!important; height: 2rem!important;">
                            </a>
                            <div class="dropdown-menu dropdown-menu-animated dropdown-lg">
                                <div class="dropdown-header bg-trans-gradient d-flex flex-row py-4 rounded-top">
                                    <div class="d-flex flex-row align-items-center mt-1 mb-1 color-white">
                                        <span class="mr-2">
                                            <img src="<?= session()->get('profile_picture') ?? base_url('assets/img/user/default.png') ?>" class="rounded-circle profile-image" style="width: 3.125rem!important; height: 3.125rem!important;">
                                        </span>
                                        <div class="info-card-text">
                                            <div class="fs-lg text-truncate text-truncate-lg"><?= session()->get('username') ?? 'username' ?></div>
                                            <span class="text-truncate text-truncate-md opacity-80"><?= session()->get('name') ?? 'User Name' ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="dropdown-divider m-0"></div>
                                <a href="<?= base_url('user/info') ?>" class="dropdown-item">
                                    <span>Edit Profile</span>
                                </a>
                                <a href="<?= base_url('user/change_password') ?>" class="dropdown-item">
                                    <span>Change Password</span>
                                </a>
                                <div class="dropdown-divider m-0"></div>
                                <a href="#" class="dropdown-item" data-toggle="modal" data-target=".js-modal-settings">
                                    <span data-i18n="drpdwn.settings">Settings</span>
                                </a>
                                <div class="dropdown-divider m-0"></div>
                                <a href="#" class="dropdown-item" data-action="app-fullscreen">
                                    <span data-i18n="drpdwn.fullscreen">Fullscreen</span>
                                    <i class="float-right text-muted fw-n">F11</i>
                                </a>
                                <a href="#" class="dropdown-item" data-action="app-print">
                                    <span data-i18n="drpdwn.print">Print</span>
                                    <i class="float-right text-muted fw-n">Ctrl + P</i>
                                </a>
                                <div class="dropdown-divider m-0"></div>
                                <a class="dropdown-item fw-500 pt-3 pb-3" href="<?= base_url('logout') ?>">
                                    <span data-i18n="drpdwn.page-logout">Logout</span>
                                    <span class="float-right fw-n"><?= session()->get('username') ?? 'username' ?></span>
                                </a>
                            </div>
                        </div>
                    </div>

                </header>
                <!-- END Page Header -->

                <!-- the #js-page-content id is needed for some plugins to initialize -->
                <main id="js-page-content" role="main" class="page-content">
                    <ol class="breadcrumb page-breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('dashboard') ?>">Home</a>
                        </li>
                        <?php if (isset($currentPage)): ?>
                            <?php if (isset($currentPage["parent_menu_name"]) && $currentPage["parent_menu_name"] != ""): ?>
                                <?php if ($currentPage["parent_menu_file_name"] != ""): ?>
                                    <li class="breadcrumb-item"><a href="<?= base_url($currentPage["parent_menu_file_name"]) ?>"><?= $currentPage["parent_menu_name"] ?></a></li>
                                <?php else: ?>
                                    <li class="breadcrumb-item"><?= $currentPage["parent_menu_name"] ?></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if (isset($currentPage["menu_name"]) && $currentPage["menu_name"] != "Home"): ?>
                                <li class="breadcrumb-item active">
                                    <?= $currentPage["menu_name"] ?>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        <li class="position-absolute pos-top pos-right d-none d-sm-block"><span class="js-get-date"></span></li>
                    </ol>

                    <?= $this->renderSection('content') ?>
                </main>

                <!-- this overlay is activated only when mobile menu is triggered -->
                <div class="page-content-overlay" data-action="toggle" data-class="mobile-nav-on"></div> <!-- END Page Content -->


                <?= view('layout/includes/footer') ?>
                <?= view('layout/includes/shortcut') ?>
                <?= view('layout/includes/color_profile') ?>

            </div>
        </div>
    </div>
    <!-- END Page Wrapper -->

    <?= view('layout/includes/page_setting') ?>

    <?= view('layout/includes/footer_script') ?>

    <?php if (isset($scripts)): ?>
        <?= $scripts ?>
    <?php endif; ?>


</body>

</html>