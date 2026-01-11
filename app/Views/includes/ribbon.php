<!-- RIBBON -->
<div id="ribbon">

		<span class="ribbon-button-alignment">
			<span id="refresh" class="btn btn-ribbon" data-action="resetWidgets" data-title="refresh"  rel="tooltip" data-placement="right" data-original-title="<i class='text-warning fa fa-warning'></i> Warning! This will reset all your widget settings." data-html="true" data-reset-msg="Would you like to RESET all your saved widgets and clear LocalStorage?"><i class="fa fa-refresh"></i></span>
		</span>

    <!-- breadcrumb -->
    <ol class="breadcrumb">
        <!-- This is auto generated -->
        <?php if (isset($twigBreadCrumb))
          {
            foreach($twigBreadCrumb as $menu)
            {
              echo '<li class="active">'.$menu['menu'].'</li>';
            }
          }
          ?>
    </ol>


</div>
<!-- END RIBBON -->
