<!-- PAGE FOOTER -->
<div class="page-footer">
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <span class="txt-color-white">IKON POS version 0.9
            <span class="hidden-xs"> - ©<script type="text/javascript">var year = new Date();document.write(year.getFullYear());</script>
            PT. Krida Jaringan Nusantara, Tbk.</span></span>
        </div>
    </div>
</div>
<!-- END PAGE FOOTER -->



<div id="shortcut">
  <ul>

        <?php
          foreach($this->session->sessionModuleList as $module)
          {
            $strSelected = "";
            if ($module['id_adm_module'] == $this->session->sessionModuleID)
            {
              $strSelected = "selected";
            }
          ?>
          <li>
  					<a href="<?php echo base_url(); ?>home/change_module/<?php echo $module['id_adm_module']; ?>" class="jarvismetro-tile big-cubes <?php echo $strSelected; ?> bg-color-blue">
                <span class="iconbox">
                  <i class="fa <?php echo $module['module_icon_class']; ?> fa-4x"></i>
                  <span><?php echo $module['name']; ?></span>
                </span>
            </a>
  				</li>
          <?php
          }
         ?>
      </div>
	</ul>
</div>
