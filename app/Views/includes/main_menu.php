<!-- Left panel : Navigation area -->
<aside id="left-panel">

    <!-- User info -->
    <div class="login-info">

				<span>
					<a href="<?php echo base_url(); ?>user/profile" id="show-shortcut">
            <?php
            $id = $users['id_adm_user'];
            $sql = "Select profile_pic FROM adm_user WHERE id_adm_user = $id";
            $dataset = $this->db->query($sql)->row_array();
            $profile_pic = $dataset['profile_pic'];

            if (file_exists(APPPATH."../images/user/".$profile_pic) && $profile_pic != "") {
              ?>
              <img src="<?php echo base_url(); ?>images/user/<?php echo $profile_pic."?".mt_rand();?>" alt="<?php echo $users['name']; ?>" class="online" />
              <?php
            }else{
              ?>
              <img src="<?php echo base_url(); ?>assets/images/avatars/male.png" alt="<?php echo $users['name']; ?>" class="online" />
            <?php } ?>
						<span><?php echo $users['name']; ?></span>
          </a>
				</span>
    </div>
    <!-- end user info -->

    <nav>
        <?php
          echo $menu_generate;
        ?>
    </nav>
    <span class="minifyme" data-action="minifyMenu" > <i class="fa fa-arrow-circle-left hit"></i> </span>

</aside>
