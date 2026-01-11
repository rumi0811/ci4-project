<?php 
if (isset($currentPage)) {
	if (isset($currentPage['menu_name'])) {
		if (!isset($currentPage['icon_file']) || $currentPage['icon_file'] == "") {
			$currentPage['icon_file'] = 'fa-list';
		}
		echo '
<div class="subheader">
	<h1 class="subheader-title">
		<i class="subheader-icon fal '.$currentPage["icon_file"].'"></i> '.$currentPage["menu_name"].'
	</h1>
</div>';

	}	
}
?>