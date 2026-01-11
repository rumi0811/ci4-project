<!DOCTYPE html>
<html lang="en-us" id="extr-page">

<head>
    <meta charset="utf-8">
    <title><?= $title; ?></title>


    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#03a9f4">
    <link rel="shortcut icon" href="<?= base_url(); ?>assets/img/favicon/favicon.ico" type="image/x-icon" />
    <link rel="icon" href="<?= base_url(); ?>assets/img/favicon/favicon.ico" type="image/x-icon" />

    <link id="vendorsbundle" rel="stylesheet" media="screen, print" href="<?= base_url(); ?>assets/4.5.1/css/vendors.bundle.css">
    <link id="appbundle" rel="stylesheet" media="screen, print" href="<?= base_url(); ?>assets/4.5.1/css/app.bundle.css">
    <link id="mytheme" rel="stylesheet" media="screen, print" href="<?= base_url(); ?>assets/4.5.1/css/themes/cust-theme-4.css">
    <link id="myskin" rel="stylesheet" media="screen, print" href="<?= base_url(); ?>assets/4.5.1/css/skins/skin-master.css">

    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url(); ?>assets/img/favicon/favicon.png">
    <link rel="mask-icon" href="<?= base_url(); ?>assets/img/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="stylesheet" media="screen, print" href="<?= base_url(); ?>assets/4.5.1/css/fa-brands.css">
    <link rel="stylesheet" media="screen, print" href="<?= base_url(); ?>assets/4.5.1/css/formplugins/select2/select2.bundle.css">
    <link rel="stylesheet" media="screen, print" href="<?= base_url(); ?>assets/4.5.1/css/datagrid/datatables/datatables.bundle.css">
    <link rel="stylesheet" media="screen, print" href="<?= base_url(); ?>assets/4.5.1/css/notifications/sweetalert2/sweetalert2.bundle.css">
    <link rel="stylesheet" media="screen, print" href="<?= base_url(); ?>assets/4.5.1/css/notifications/toastr/toastr.css">
    <link rel="stylesheet" media="screen, print" href="<?= base_url(); ?>assets/4.5.1/css/formplugins/dropzone/dropzone.css">
    <link rel="stylesheet" media="screen, print" href="<?= base_url(); ?>assets/4.5.1/css/miscellaneous/lightgallery/lightgallery.bundle.css">
    <link rel="stylesheet" media="screen, print" href="<?= base_url(); ?>assets/4.5.1/css/formplugins/bootstrap-datepicker/bootstrap-datepicker.css">
    <link rel="stylesheet" media="screen, print" href="<?= base_url(); ?>assets/4.5.1/css/formplugins/bootstrap-daterangepicker/bootstrap-daterangepicker.css">
    <link rel="stylesheet" media="screen, print" href="<?= base_url(); ?>assets/css/style.css">
</head>

<body>

    <script>
        'use strict';

        var classHolder = document.getElementsByTagName("BODY")[0],
            themeSettings = (localStorage.getItem('themeSettings')) ? JSON.parse(localStorage.getItem('themeSettings')) : {},
            themeURL = themeSettings.themeURL || '',
            themeOptions = themeSettings.themeOptions || '';

        if (themeSettings.themeOptions) {
            classHolder.className = themeSettings.themeOptions;
            console.log("%c✔ Theme settings loaded", "color: #148f32");
        } else {
            console.log("%c✔ Heads up! Theme settings is empty or does not exist, loading default settings...", "color: #ed1c24");
        }

        if (themeSettings.themeURL && !document.getElementById('mytheme')) {
            var cssfile = document.createElement('link');
            cssfile.id = 'mytheme';
            cssfile.rel = 'stylesheet';
            cssfile.href = themeURL;
            document.getElementsByTagName('head')[0].appendChild(cssfile);
        } else if (themeSettings.themeURL && document.getElementById('mytheme')) {
            document.getElementById('mytheme').href = themeSettings.themeURL;
        }

        var saveSettings = function() {
            themeSettings.themeOptions = String(classHolder.className).split(/[^\w-]+/).filter(function(item) {
                return /^(nav|header|footer|mod|display)-/i.test(item);
            }).join(' ');
            if (document.getElementById('mytheme')) {
                themeSettings.themeURL = document.getElementById('mytheme').getAttribute("href");
            };
            localStorage.setItem('themeSettings', JSON.stringify(themeSettings));
        }

        var resetSettings = function() {
            localStorage.setItem("themeSettings", "");
        }
    </script>

    <div class="page-wrapper auth">
        <div class="page-inner bg-trans-gradient">
            <div class="page-content-wrapper bg-transparent m-0">
                <div class="height-10 w-100 shadow-lg px-4 bg-trans-gradient">
                    <div class="d-flex align-items-center container p-0">
                        <div class="page-logo width-mobile-auto m-0 align-items-center justify-content-center p-0 bg-transparent bg-img-none shadow-0 height-9 border-0">
                            <a href="javascript:void(0)" class="page-logo-link press-scale-down d-flex align-items-center">
                                <img src="<?= base_url(); ?>assets/img/logo.png" alt="Logo" aria-roledescription="logo" style="height: 50px!important; width: auto!important">
                                <h1 class="text-white">IKON POS</h1>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="flex-1" style="background: url(<?= base_url(); ?>assets/4.5.1/img/svg/pattern-1.svg) no-repeat center bottom fixed; background-size: cover;">
                    <div class="container py-4 py-lg-5 my-lg-5 px-4 px-sm-0">
                        <div class="row">
                            <div class="col col-md-6 col-lg-7 hidden-sm-down">
                                <h2 class="fs-xxl fw-500 mt-4 text-white">
                                    Point of Sales - Back Office
                                    <small class="h3 fw-300 mt-3 text-white opacity-60">
                                        Ikon Media Indonesia - POS Back office
                                    </small>
                                </h2>
                                <img src="<?= base_url(); ?>assets/img/product.jpg" style="height: 280px" />
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-5 col-xl-4 ml-auto">
                                <h1 class="text-white fw-300 mb-3 d-sm-block d-md-none">
                                    Secure login
                                </h1>
                                <div class="card p-4 rounded-plus bg-faded">
                                    <form method="post" action="<?= base_url('auth/login'); ?>" id="formLogin" name="formLogin" novalidate="">
                                        <input type="hidden" name="returnUrl" value="<?= esc($returnUrl); ?>" />
                                        <div class="form-group">
                                            <label class="form-label" for="username">Username</label>
                                            <input type="text" name="username" class="form-control form-control-lg" placeholder="Enter your username" value="<?= esc($username); ?>" required>
                                            <div class="invalid-feedback">Enter your registered username.</div>
                                            <div class="help-block">Your unique username to app</div>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label" for="password">Password</label>
                                            <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="password" required>
                                            <div class="invalid-feedback">Enter your password.</div>
                                            <div class="help-block">Your password</div>
                                        </div>
                                        <div class="form-group text-left">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="rememberme">
                                                <label class="custom-control-label" for="rememberme"> Remember me</label>
                                            </div>
                                        </div>
                                        <div class="row no-gutters">
                                            <div class="col-lg-6 pr-lg-1 my-2">
                                                <button name="btnLogin" id="btnLogin" type="submit" class="btn btn-info btn-block btn-lg">Secure Login</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="position-absolute pos-bottom pos-left pos-right p-3 text-center text-white">
                            <?= date("Y"); ?> © IKON MEDIA INDONESIA
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <p id="js-color-profile" class="d-none">
        <span class="color-primary-50"></span>
        <span class="color-primary-100"></span>
        <span class="color-primary-200"></span>
        <span class="color-primary-300"></span>
        <span class="color-primary-400"></span>
        <span class="color-primary-500"></span>
        <span class="color-primary-600"></span>
        <span class="color-primary-700"></span>
        <span class="color-primary-800"></span>
        <span class="color-primary-900"></span>
        <span class="color-info-50"></span>
        <span class="color-info-100"></span>
        <span class="color-info-200"></span>
        <span class="color-info-300"></span>
        <span class="color-info-400"></span>
        <span class="color-info-500"></span>
        <span class="color-info-600"></span>
        <span class="color-info-700"></span>
        <span class="color-info-800"></span>
        <span class="color-info-900"></span>
        <span class="color-danger-50"></span>
        <span class="color-danger-100"></span>
        <span class="color-danger-200"></span>
        <span class="color-danger-300"></span>
        <span class="color-danger-400"></span>
        <span class="color-danger-500"></span>
        <span class="color-danger-600"></span>
        <span class="color-danger-700"></span>
        <span class="color-danger-800"></span>
        <span class="color-danger-900"></span>
        <span class="color-warning-50"></span>
        <span class="color-warning-100"></span>
        <span class="color-warning-200"></span>
        <span class="color-warning-300"></span>
        <span class="color-warning-400"></span>
        <span class="color-warning-500"></span>
        <span class="color-warning-600"></span>
        <span class="color-warning-700"></span>
        <span class="color-warning-800"></span>
        <span class="color-warning-900"></span>
        <span class="color-success-50"></span>
        <span class="color-success-100"></span>
        <span class="color-success-200"></span>
        <span class="color-success-300"></span>
        <span class="color-success-400"></span>
        <span class="color-success-500"></span>
        <span class="color-success-600"></span>
        <span class="color-success-700"></span>
        <span class="color-success-800"></span>
        <span class="color-success-900"></span>
        <span class="color-fusion-50"></span>
        <span class="color-fusion-100"></span>
        <span class="color-fusion-200"></span>
        <span class="color-fusion-300"></span>
        <span class="color-fusion-400"></span>
        <span class="color-fusion-500"></span>
        <span class="color-fusion-600"></span>
        <span class="color-fusion-700"></span>
        <span class="color-fusion-800"></span>
        <span class="color-fusion-900"></span>
    </p>

    <script src="<?= base_url(); ?>assets/4.5.1/js/vendors.bundle.js"></script>
    <script src="<?= base_url(); ?>assets/4.5.1/js/app.bundle.js"></script>
    <script>
        $("#btnLogin").click(function(event) {

            var form = $("#formLogin");

            if (form[0].checkValidity() === false) {
                form.addClass('was-validated');
                event.preventDefault();
                event.stopPropagation();
            } else {
                form.addClass('was-validated');
                form.submit();
            }
        });

        <?php if ($message != ""): ?>
            bootbox.alert('<?= addslashes($message); ?>', function() {});
        <?php endif; ?>
    </script>

</body>

</html>