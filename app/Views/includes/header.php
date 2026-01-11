<!-- HEADER -->
<header id="header">

  <!-- collapse menu button -->
  <div id="hide-menu" class="btn-header">
      <!-- <span> <a href="#" title="Collapse Menu" data-action="toggleMenu"><i class="fa fa-reorder"></i></a> </span> -->
      <button class="hamburger hamburger--arrowturn is-active" type="button" data-action="toggleMenu">
        <span class="hamburger-box">
          <span class="hamburger-inner"></span>
        </span>
      </button>
  </div>
  <!-- end collapse menu -->

    <div id="logo-group">
        <!-- PLACE YOUR LOGO HERE -->
        <span id="logo"> <img src="<?php echo base_url(); ?>assets/images/logo.png" alt="IKON POS"> </span>
        <!-- END LOGO PLACEHOLDER -->
    </div>

    <!-- projects dropdown -->
<?php
if (count($this->session->sessionModuleList) > 1) {  ?>
			<div class="project-context hidden-xs">

				<span class="label">Module:</span>
				<span class="project-selector dropdown-toggle" id="show-shortcut" data-action="toggleShortcut">
          <?php echo $this->session->sessionModuleName; ?> <i class="fa fa-angle-down"></i>
        </span>


			</div>
			<!-- end projects dropdown -->
<?php
}
  ?>


    <!-- pulled right: nav area -->
    <div class="pull-right">

        <!-- #MOBILE -->
        <!-- Top menu profile link : this shows only when top menu is active -->
        <ul id="mobile-profile-img" class="header-dropdown-list hidden-xs padding-5">
            <li class="">
                <a href="#" class="dropdown-toggle no-margin userdropdown" data-toggle="dropdown">
                    <img src="<?php echo base_url(); ?>assets/images/avatars/sunny.png" alt="Avatar" class="online" />
                </a>
                <ul class="dropdown-menu pull-right">
                    <li>
                        <a href="#" class="padding-10 padding-top-0 padding-bottom-0"><i class="fa fa-cog"></i> Setting</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="#ajax/profile.php" class="padding-10 padding-top-0 padding-bottom-0"> <i class="fa fa-user"></i> <u>P</u>rofile</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="#" class="padding-10 padding-top-0 padding-bottom-0" data-action="toggleShortcut"><i class="fa fa-arrow-down"></i> <u>S</u>hortcut</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="#" class="padding-10 padding-top-0 padding-bottom-0" data-action="launchFullscreen"><i class="fa fa-arrows-alt"></i> Full <u>S</u>creen</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="login.php" class="padding-10 padding-top-5 padding-bottom-5" data-action="userLogout"><i class="fa fa-sign-out fa-lg"></i> <strong><u>L</u>ogout</strong></a>
                    </li>
                </ul>
            </li>
        </ul>


        <!-- logout button -->
        <div id="logout" class="btn-header transparent pull-right">
            <span> <a href="<?php echo base_url(); ?>logout" title="Logout" data-action="userLogout" data-logout-msg="Keluar dari aplikasi ini?"><i class="fa fa-power-off"></i></a> </span>
        </div>
        <!-- end logout button -->

        <!-- fullscreen button -->
        <div id="fullscreen" class="btn-header transparent pull-right">
            <span> <a href="#" title="Full Screen" data-action="launchFullscreen"><i class="fa fa-expand"></i></a> </span>
        </div>
        <!-- end fullscreen button -->

        <div class="btn-header transparent pull-right hidden-lg">
            <span> <a><i data-action="toggleShortcut" class="fa fa-cubes"></i></a> </span>
        </div>

        <!-- multiple lang dropdown : find all flags in the flags page -->

        <ul class="header-dropdown-list hidden-xs">
            <li>
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <?php
                    if ($sessionLanguage == 'id')
                    {
                      $Flag = 'flag-id';
                      $LangName = 'Indonesia';
                    }
                    else {
                      $Flag = "flag-us";
                      $LangName = 'English';
                    }
                    ?>
                    <img src="<?php echo base_url(); ?>assets/images/blank.gif" class="flag <?php echo $Flag; ?>" alt="English"> <span> <?php echo $LangName; ?> </span> <i class="fa fa-angle-down"></i> </a>
                <ul class="dropdown-menu pull-right">
                    <li class="<?php if ($sessionLanguage == 'en') echo 'active'; ?>">
                        <a href="#" onclick="javascript:changeActiveLanguage('en');"><img src="<?php echo base_url(); ?>assets/images/blank.gif" class="flag flag-us" alt="English"> English (US)</a>
                    </li>
                    <li class="<?php if ($sessionLanguage == 'id') echo 'active'; ?>">
                        <a href="#" onclick="javascript:changeActiveLanguage('id');"><img src="<?php echo base_url(); ?>assets/images/blank.gif" class="flag flag-id" alt="Indonesia"> Indonesia</a>
                    </li>
                    <li>
                </ul>
            </li>
        </ul>

        <!-- end multiple lang -->

    </div>
    <!-- end pulled right: nav area -->

</header>
<!-- END HEADER -->
