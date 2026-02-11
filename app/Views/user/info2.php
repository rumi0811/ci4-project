<div class="row">
	<div class="col-xs-12">
		<h1 class="page-title txt-color-blueDark">
			<i class="fa fa-fw <?php echo $currentPage["icon_file"]; ?>"></i>
			<?php echo $currentPage["menu_name"]; ?>
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

<section id="widget-grid">
	<div class="row">
		<!-- NEW COL START -->
		<article class="col-sm-12 col-md-12 col-lg-12">
			<!-- Widget ID (each widget will need unique ID)-->
			<div class="jarviswidget jarviswidget-color-blueDark" id="jarvis-form-input" data-widget-editbutton="true">
				<header>
					<span class="widget-icon"> <i class="fal fa-edit"></i>
					</span>
					<h2>Account Information</h2>
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
						<form id="form-input" class="smart-form" novalidate="novalidate"
							method="post">
							<input type="hidden" name="http_referer" value="<?php echo $http_referer; ?>" />
								<div class="row">
									<section class="col col-3">
                    
							<fieldset>
                    <div class="row">
                      <section class="col col-12">
                        <label class="label">Foto Profil</label>
                        <div id="div_profile_picture">
                          <a href="<?php echo $record['profile_picture'];?>" target="_blank"><img src="<?php echo $record['profile_picture'];?>" class="image-responsive" style="max-width: 250px!important" /></a>
                        </div>
                      </section>
                    </div>
                    
                    <div class="row">
                      <section class="col col-12">
                          <label class="label">Upload Foto Baru</label>
                          <div class="input input-file">
                              <span class="button"><input type="file" id="profile_picture" name="profile_picture" onchange="uploadFile(this, <?php echo $record['user_id']; ?>);">Browse</span><input type="text" placeholder="image file" readonly="">
													</div>
                      </section>
                    </div>
                    
							</fieldset>
                    
                  </section>
									<section class="col col-9">
                    
							<fieldset>
								<div class="row">
									<section class="col col-6">
										<label class="label">Username (Email)</label>
										<label class="input">
											<span><?php echo $record['username']; ?></span>
										</label>
									</section>

									<section class="col col-6">
										<label class="label">Payment Point Code</label>
										<h3 class="form-control-static">
											<?php echo $record['pp_code']; ?>
										</h3>
									</section>
								</div>
								<div class="row">
									<section class="col col-6">
										<label class="label">Full name</label>
										<label class="input">
											<input type="text" name="name" id="name" value="<?php echo $record['name']; ?>" />
										</label>

									</section>
									
									<section class="col col-6">
										<label class="label">Mobile Number</label>
										<label class="input">
											<input type="text" name="mobile" id="mobile" value="<?php echo $record['mobile']; ?>" />
										</label>
									</section>
								</div>
								<div class="row">
									<section class="col col-12">
										<label class="label">Address</label>
										<label class="textarea textarea-resizable">
											<textarea rows="2" name="address" id="address"><?php echo $record['address']; ?></textarea>
										</label>
									</section>
								</div>
								<div class="row">

									<section class="col col-12">
										<label class="label">Balance(IDR)</label>
										<h3 class="form-control-static">
											<?php echo $record['balance']; ?>
										</h3>
									</section>

								</div>

							</fieldset>
            </section>
          </div>

							<input type="hidden" name="btnSave" id="btnSave" value="" />
							<footer>
								<button type="button" id="btnSaveDummy" class="btn btn-success pull-left">
									<i class="fa fa-save"></i> Save
								</button>
								<button type="button" id="btnCancelDummy" class="btn pull-left">
									<i class="fa fa-times"></i> Cancel
								</button>
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

<script
	src="<?php echo base_url(); ?>assets/js/plugin/jquery-form/jquery-form.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
			
	pageSetUp();

	var form_input = $("#form-input").validate({
	
				// Rules for form validation
				rules : {
					name : {
						required : true
					},
					mobile : {
						required : true
					},
					address : {
						required : true
					}
				},
	
				// Messages for form validation
				messages : {
					name : {
						required : 'Please enter full name'
					},
					mobile : {
						required : 'Please enter mobile number'
					},
					address : {
						required : 'Please enter address'
					}
				},


				// Do not change code below
				errorPlacement : function(error, element) {
					error.insertAfter(element.parent());
				},

		        invalidHandler: function(form, validator) {
		            if (!validator.numberOfInvalids())
		                return;
		            $('html, body').animate({
		                scrollTop: $(validator.errorList[0].element).offset().top - 100
		            }, 500);
		        }
                
	});
			
	$("#btnCancelDummy").click(function(e)
	{
		$("#btnCancel").val("1");
		$("#form-cancel").submit();
		e.preventDefault();
	});
	
	$("#btnSaveDummy").click(function(e)
	{
		if ($("#form-input").valid()) {
			$.SmartMessageBox({
				title : "<i class='fa fa-save' style='color:green'></i> Save",
				content : "Save this data?",
				buttons : '[No][Yes]'
			}, function(ButtonPressed) {
				if (ButtonPressed === "Yes") {
					$("#btnSave").val("1");
					$("#form-input").submit();
				}
				if (ButtonPressed === "No") {
					$.smallBox({
						title : "Save",
						content : "<i class='fa fa-times'></i> <i>Canceled...</i>",
						color : "#6c6f72",
						iconSmall : "fa fa-times fa-2x fadeInRight animated",
						timeout : 4000
					});
				}

			});	
		}
		e.preventDefault();
			
	});
	
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
  data.append('doc_type', 'profile_picture');
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
             $("#div_" + docName).html('<a href="' + fileName + '" target="_blank"><img src="' + fileName + '" class="image-responsive" style="max-width: 250px!important" /></a>');
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
