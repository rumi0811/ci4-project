<!DOCTYPE html>
<html lang="en-us" id="extr-page">
	<head>
		<meta charset="utf-8">
		<title><?php echo $title; ?></title>
<?php $this->load->view("includes/header_script"); ?>
	</head>
	<body>
    <?php $this->load->view("includes/after_body_script"); ?>
		
	<div class="page-wrapper auth">
            <div class="page-inner bg-trans-gradient">
                <div class="page-content-wrapper bg-transparent m-0">
                    <div class="height-10 w-100 shadow-lg px-4 bg-trans-gradient">
                        <div class="d-flex align-items-center container p-0">
                            <div class="page-logo width-mobile-auto m-0 align-items-center justify-content-center p-0 bg-transparent bg-img-none shadow-0 height-9 border-0">
                                <a href="javascript:void(0)" class="page-logo-link press-scale-down d-flex align-items-center">
                                    <img src="<?php echo base_url(); ?>assets/img/logo.png" alt="Logo" aria-roledescription="logo" style="height: 50px!important; width: auto!important">
                                    <h1 class="text-white">IKON POS</h1>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="flex-1" style="background: url(<?php echo base_url(); ?>assets/4.5.1/img/svg/pattern-1.svg) no-repeat center bottom fixed; background-size: cover;">
                        <div class="container py-4 py-lg-5 my-lg-5 px-4 px-sm-0">
                            <div class="row">
                                <div class="col col-md-6 col-lg-7 hidden-sm-down">
                                    <h2 class="fs-xxl fw-500 mt-4 text-white">
                                        Point of Sales - Back Office
                                        <small class="h3 fw-300 mt-3 text-white opacity-60">
                                            Ikon Media Indonesia - POS Back office
                                        </small>
                                    </h2>
                                    <img src="<?php echo base_url(); ?>assets/img/product.jpg" style="height: 280px" />

                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-5 col-xl-4 ml-auto">
                                    <h1 class="text-white fw-300 mb-3 d-sm-block d-md-none">
                                        Secure login
                                    </h1>
                                    <div class="card p-4 rounded-plus bg-faded">
										<form method="post" id="js-login" name="js-login" novalidate="">
							                <input type="hidden" name="returnUrl" value="<?php echo $returnUrl; ?>" />
                                            <div class="form-group">
                                                <label class="form-label" for="username">Username</label>
                                                <input type="text" name="username" class="form-control form-control-lg" placeholder="Enter your username" value="<?php echo $username; ?>" required>
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
                                            <?php /*<div class="row no-gutters">
												<div class="col-lg-12 pr-lg-1 my-2">
													<a href="<?php echo base_url(); ?>login/forgot_password_request">Lupa password?</a>
													<br />
													<a class="text-info" href="<?php echo base_url(); ?>login/resend_activation">Kirim ulang kode aktivasi</a>
												</div>
                                            </div>*/ ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="position-absolute pos-bottom pos-left pos-right p-3 text-center text-white">
                                <?php echo date("Y"); ?> © IKON MEDIA INDONESIA
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php $this->load->view("includes/color_profile_script"); ?>

		<script src="<?php echo base_url(); ?>assets/4.5.1/js/vendors.bundle.js"></script>
        <script src="<?php echo base_url(); ?>assets/4.5.1/js/app.bundle.js"></script>
        <script>
            $("#btnLogin").click(function(event)
            {
                // Fetch form to apply custom Bootstrap validation
                var form = $("#js-login");

                if (form[0].checkValidity() === false)
                {
					form.addClass('was-validated');
                    event.preventDefault();
                    event.stopPropagation();
                }
				else {
					form.addClass('was-validated');
					form.submit();
				}
            });


<?php 
  if($message != "") { 
?>
    bootbox.alert('<?php echo $message; ?>', function() {
    });
<?php
  }
?>
        </script>
    
      



<?php //$this->load->view("includes/firebase_script"); ?>

	</body>
</html>