<div class="row">
	<div class="col-xs-12">
		<h1 class="page-title txt-color-blueDark">
			<i class="fa fa-fw fa-photo"></i>
			<?php echo $title; ?>
		</h1>
	</div>
</div>
<?php 
if ($message != "") {
?>
<div id="messageAlert" class="alert alert-success alert-dismissable">
	<button type="button" class="close" data-dismiss="alert"
		aria-hidden="true">&times;</button>
	<strong>Success</strong>
	<?php echo $message; ?>
</div>
<?php 
}
?>
<?php 
if ($error_message != "") {
?>
<div id="errorAlert" class="alert alert-danger alert-dismissable">
	<button type="button" class="close" data-dismiss="alert"
		aria-hidden="true">&times;</button>
	<strong>Error</strong>
	<?php echo $error_message; ?>
</div>
<?php 
}
?>
<?php 
  if ($record["user_id"] > 0) { ?>
<section id="widget-grid">
	<div class="row">
		<!-- NEW COL START -->
		<article class="col-sm-12 col-md-12 col-lg-12">
			<!-- Widget ID (each widget will need unique ID)-->
			<div class="jarviswidget jarviswidget-color-blueDark" id="jarvis-form-input" data-widget-editbutton="true">
				<header>
					<span class="widget-icon"> <i class="fal fa-edit"></i>
					</span>
					<h2>Form Input User</h2>
				</header>
				<!-- widget div-->
				<div>
					<!-- widget edit box -->
					<div class="jarviswidget-editbox">
						<!-- This area used as dropdown edit box -->
					</div>
					<!-- end widget edit box -->
					<!-- widget content -->
					<div class="widget-body no-padding">
						<form id="form-input" class="smart-form" novalidate="novalidate" method="post">
							<fieldset>
								<div class="row">
									<section class="col col-12">
                    <div class="row">
                      <section class="col col-6">
                        <label class="label">KTP</label>
                        <div id="div_id_card_image_file">
<?php 
  if (strpos($record['id_card_image_file'], "no_image.png") !== false)
  {
    echo '<a href="'.$record['id_card_image_file'].'" target="_blank"><img src="'.$record['id_card_image_file'].'" class="image-responsive" width="100" height="100" /></a>';
  }
  else
  {
    echo '<a href="'.$record['id_card_image_file'].'" target="_blank"><img src="'.$record['id_card_image_file'].'" class="image-responsive" width="100" height="133" /></a>';
  }
?>
                        </div>
                      </section>
                      <section class="col col-6">
                        <label class="label">Pas Photo</label>
                        <div id="div_photo_image_file">
<?php 
  if (strpos($record['photo_image_file'], "no_image.png") !== false)
  {
    echo '<a href="'.$record['photo_image_file'].'" target="_blank"><img src="'.$record['photo_image_file'].'" class="image-responsive" width="100" height="100" /></a>';
  }
  else
  {
    echo '<a href="'.$record['photo_image_file'].'" target="_blank"><img src="'.$record['photo_image_file'].'" class="image-responsive" width="100" height="133" /></a>';
  }
?>
                        </div>
                      </section>
                    </div>
<?php
  if ($record['user_id'] > 0) {
?>
                    <div class="row">
                      <section class="col col-6">
                          <label class="label">KTP</label>
                          <div class="input input-file">
                              <span class="button"><input type="file" id="id_card_image_file" name="id_card_image_file" onchange="uploadFile(this, <?php echo $record['user_id']; ?>);">Browse</span><input type="text" placeholder="image file" readonly="">
													</div>
                      </section>
                      <section class="col col-6">
                          <label class="label">Pas Photo</label>
                          <div class="input input-file">
                              <span class="button"><input type="file" id="photo_image_file" name="photo_image_file" onchange="uploadFile(this, <?php echo $record['user_id']; ?>);">Browse</span><input type="text" placeholder="image file" readonly="">
													</div>
                      </section>
                    </div>
<?php
  }
?>
									</section>
								</div>
								

							</fieldset>


							<input type="hidden" name="btnSave" id="btnSave" value="" />
							<footer>
                *Note: Setelah pilih file photo yang akan di upload dari tombol Browse di atas, maka photo akan tersimpan langsung ke server.
							</footer>
						</form>
						<form id="form-cancel" method="post">
							<input type="hidden" name="http_referer" value="<?php echo $http_referer; ?>" />
							<input type="hidden" name="btnCancel" id="btnCancel" value="" />
						</form>
					</div>
				</div>
			</div>
		</article>
	</div>
</section>
<?php 
}
?>

<script
	src="<?php echo base_url(); ?>assets/js/plugin/jquery-form/jquery-form.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
			
	pageSetUp();				
	
});
     
     
var uploadFile = function(obj, user_id)
{
  var docName = obj.name;
  var fileObj = $(obj);
  var size  =  fileObj[0].files[0].size;
  var imgname  =  obj.value;
  obj.parentNode.nextSibling.value = obj.value;
  
  data = new FormData();
  data.append('photo', fileObj[0].files[0]);
  data.append('doc_type', docName);
  data.append('user_id', user_id);
  data.append('hash', '<?php echo md5("photos|||".$record['user_id']); ?>');

  var ext =  imgname.substr( (imgname.lastIndexOf('.') +1) );
  if(ext=='jpg' || ext=='jpeg' || ext=='png' || ext=='gif' || ext=='PNG' || ext=='JPG' || ext=='JPEG')
  {
     if(size <= 2000000)
     {
        $.ajax({
          url: "<?php echo base_url() ?>/pp/upload_doc",
          type: "POST",
          data: data,
          enctype: 'multipart/form-data',
          processData: false,  // tell jQuery not to process the data
          contentType: false   // tell jQuery not to set contentType
        })
        .done(function(data) {
           if(data.indexOf("SUKSES") >= 0)
           {
             var arrData = data.split("|||");
             var fileName = arrData[1];
             $("#div_" + docName).html('<a href="' + fileName + '" target="_blank"><img src="' + fileName + '" class="image-responsive" width="100" height="133" /></a>');
           }
           else
           {
             alert(data);
           }

        });
      return false;
    }//end size
    else
    {
      alert('Sorry File size exceeding from 2 Mb');
    }
  }//end FILETYPE
  else
  {
    alert('Sorry you can upload JPEG|JPG|PNG|GIF file type only.');
  }
  
};
     
</script>
