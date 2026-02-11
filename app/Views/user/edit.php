<div class="row">
  <div class="col-xs-12">
    <h1 class="page-title txt-color-blueDark">
      <i class="fa fa-fw <?php echo $currentPage["icon_file"] ?? 'fa-user'; ?>"></i>
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

<section id="widget-grid">
  <div class="row">
    <!-- NEW COL START -->
    <article class="col-sm-12 col-md-12 col-lg-12">
      <!-- Widget ID (each widget will need unique ID)-->
      <div class="jarviswidget jarviswidget-color-blueDark"
        id="jarvis-form-input" data-widget-editbutton="true">
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
            <form id="form-input" class="smart-form" novalidate="novalidate" method="post" action="<?php echo base_url('user/edit' . (isset($record['user_id']) && $record['user_id'] > 0 ? '/' . $record['user_id'] : '')); ?>">
              <input type="hidden" name="user_id" id="user_id" value="<?php echo $record['user_id'] ?? 0; ?>" />
              <input type="hidden" name="http_referer" value="<?php echo $http_referer; ?>" />
              <fieldset>
                <div class="row">
                  <section class="col col-8">

                    <div class="row">
                      <section class="col col-4">
                        <label class="label">Username (Email)</label> <label
                          class="input"> <input type="email" name="username"
                            id="username" value="<?php echo $record['username']; ?>">
                        </label>

                      </section>
                      <section class="col col-4">
                        <label class="label">Password</label>
                        <label class="input">
                          <input type="password" name="pwd" id="pwd" value="<?php echo $record['pwd']; ?>">
                        </label>

                      </section>

                      <section class="col col-4">
                        <label class="label">Full name</label> <label class="input"> <input
                            type="text" name="name" id="name"
                            value="<?php echo $record['name']; ?>">
                        </label>

                      </section>
                      <div class="clearfix"></div>

                      <section class="col col-4">
                        <label class="label">User Code</label>
                        <h3 class="form-control-static">
                          <?php echo isset($record['pp_code']) ? $record['pp_code'] : ''; ?>
                        </h3>
                        <?php if ($record['user_id'] == 0) { ?>
                          <small>Note: Only for user type Payment Point. This value may
                            changed during saving</small>
                        <?php } ?>
                        <?php
                        if (isset($record['referral_user_id']) && $record['referral_user_id'] != '' && $record['referral_user_id'] != 0) {
                          echo '<a href="' . base_url() . 'pp/edit/' . $record['referral_user_id'] . '">Lihat Upline</a>';
                        } ?>
                      </section>
                      <section class="col col-4">
                        <label class="label">Mobile</label> <label class="input"> <input
                            type="text" name="mobile" id="mobile"
                            value="<?php echo $record['mobile']; ?>">
                        </label>
                      </section>
                      <section class="col col-4">
                        <label class="label">Address</label> <label
                          class="textarea textarea-resizable"> <textarea rows="2"
                            name="address" id="address"><?php echo $record['address']; ?></textarea>
                        </label>
                      </section>

                      <div class="clearfix"></div>


                      <section class="col col-4">
                        <label class="label">Status</label> <label
                          class="select"> <select name="is_active" id="is_active">
                            <?php
                            $arrOptionStatus = array(
                              -99 => "Blokir Deposit &  Topup",
                              -9 => "Blokir Topup",
                              -2 => "Di non-aktifkan sementara",
                              -1 => "Banned/Blokir",
                              0 => "Inactive",
                              1 => "Active"
                            );
                            foreach ($arrOptionStatus as $key => $val) {
                              if ($key == $record['is_active'])
                                echo "<option value=\"" . $key . "\" selected=\"selected\">" . $val . "</option>";
                              else
                                echo "<option value=\"" . $key . "\">" . $val . "</option>";
                            }
                            ?>
                          </select>
                        </label>
                      </section>

                      <section class="col col-4">
                        <label class="label">Device ID Hash</label> <label class="input">
                          <input type="text" name="device_id" id="device_id" value="<?php echo $record['device_id']; ?>">
                        </label>

                      </section>
                      <section class="col col-4">
                        <label class="label">User ID Referer</label> <label class="input">
                          <input type="text" name="referral_user_id" id="referral_user_id" value="<?php echo $record['referral_user_id']; ?>">
                        </label>

                      </section>
                    </div>
                    <div class="row">
                      <section class="col col-12">
                        <label class="label">Blokir Note</label>
                        <label class="textarea textarea-resizable">
                          <textarea rows="2" name="user_note" id="user_note"><?php echo $record['user_note']; ?></textarea>
                        </label>
                      </section>
                    </div>
                    <div class="row">
                      <section class="col col-4">
                        <label class="label">User Type</label>
                        <label class="select">
                          <select name="user_type_id" id="user_type_id">
                            <?php
                            foreach ($user_types as $val) {
                              if ($val['value'] == ($record['user_type_id'] ?? 0))
                                echo "<option value=\"" . $val['value'] . "\" selected=\"selected\">" . $val['text'] . "</option>";
                              else
                                echo "<option value=\"" . $val['value'] . "\">" . $val['text'] . "</option>";
                            }
                            ?>
                          </select>
                        </label>

                      </section>


                      <section class="col col-4">
                        <label class="toggle pull-left">
                          <?php if ($record['email_verified'] == 1)
                            echo '<input type="checkbox" name="email_verified" id="email_verified" value="1" checked="checked" />';
                          else
                            echo '<input type="checkbox" name="email_verified" id="email_verified" value="1" />';
                          ?>

                          <i data-swchon-text="YES" data-swchoff-text="NO"></i> Email Verified
                        </label>
                        <label class="toggle pull-left">
                          <?php if ($record['is_send_mail'] == 1)
                            echo '<input type="checkbox" name="is_send_mail" id="is_send_mail" value="1" checked="checked" />';
                          else
                            echo '<input type="checkbox" name="is_send_mail" id="is_send_mail" value="1" />';
                          ?>

                          <i data-swchon-text="YES" data-swchoff-text="NO"></i> Subscribe Newsletter
                        </label>
                        <label class="toggle pull-left">
                          <?php if ($record['is_account_verified'] == 1)
                            echo '<input type="checkbox" name="is_account_verified" id="is_account_verified" value="1" checked="checked" />';
                          else
                            echo '<input type="checkbox" name="is_account_verified" id="is_account_verified" value="1" />';
                          ?>

                          <i data-swchon-text="YES" data-swchoff-text="NO"></i> Account Verified
                        </label>
                      </section>
                    </div>

                  </section>
                  <section class="col col-4">
                    <div class="row">
                      <section class="col col-4">
                        <label class="label">Photo Profile</label>
                        <div id="div_profile_picture">
                          <a href="<?php echo $record['profile_picture']; ?>" target="_blank"><img src="<?php echo $record['profile_picture']; ?>" class="image-responsive" width="100" height="133" /></a>
                        </div>
                      </section>
                      <section class="col col-4">
                        <label class="label">KTP</label>
                        <div id="div_id_card_image_file" style="border: 1px dashed #999; text-align: center">
                          <?php
                          if (strpos($record['id_card_image_file'], "no_image.png") !== false) {
                            echo '<div><a href="' . $record['id_card_image_file'] . '" target="_blank"><img src="' . $record['id_card_image_file'] . '" class="image-responsive" width="100" height="100" /></a></div>';
                          } else {
                            echo '<a href="javascript:deletePhoto(' . $record['user_id'] . ', \'id_card_image_file\', \'' . $record['id_card_image_file'] . '\', \'' . md5('id_card_image_file|||' . $record['id_card_image_file']) . '\')" style="position: absolute; right: 16px; background-color: black; color: white; opacity: 0.8; padding: 4px 8px">X</a>';
                            echo '<div><a href="' . $record['id_card_image_file'] . '" target="_blank"><img src="' . $record['id_card_image_file'] . '" class="image-responsive" width="100" height="133" /></a></div>';
                          }
                          ?>
                        </div>
                      </section>
                      <section class="col col-4">
                        <label class="label">Pas Photo</label>
                        <div id="div_photo_image_file" style="border: 1px dashed #999; text-align: center">
                          <?php
                          if (strpos($record['photo_image_file'], "no_image.png") !== false) {
                            echo '<div><a href="' . $record['photo_image_file'] . '" target="_blank"><img src="' . $record['photo_image_file'] . '" class="image-responsive" width="100" height="100" /></a></div>';
                          } else {
                            echo '<a href="javascript:deletePhoto(' . $record['user_id'] . ', \'photo_image_file\', \'' . $record['photo_image_file'] . '\', \'' . md5('photo_image_file|||' . $record['photo_image_file']) . '\')" style="position: absolute; right: 16px; background-color: black; color: white; opacity: 0.8; padding: 4px 8px">X</a>';
                            echo '<div><a href="' . $record['photo_image_file'] . '" target="_blank"><img src="' . $record['photo_image_file'] . '" class="image-responsive" width="100" height="133" /></a></div>';
                          }
                          ?>
                        </div>
                      </section>
                    </div>
                    <?php
                    if ($record['user_id'] > 0) {
                    ?>
                      <div class="row">
                        <section class="col col-12">
                          <label class="label">Photo Profile</label>
                          <div class="input input-file">
                            <span class="button"><input type="file" id="profile_picture" name="profile_picture" onchange="uploadFile(this, <?php echo $record['user_id']; ?>);">Browse</span><input type="text" placeholder="image file" readonly="">
                          </div>
                        </section>
                      </div>
                      <div class="row">
                        <section class="col col-12">
                          <label class="label">KTP</label>
                          <div class="input input-file">
                            <span class="button"><input type="file" id="id_card_image_file" name="id_card_image_file" onchange="uploadFile(this, <?php echo $record['user_id']; ?>);">Browse</span><input type="text" placeholder="image file" readonly="">
                          </div>
                        </section>
                      </div>
                      <div class="row">
                        <section class="col col-12">
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


              <!-- <input type="hidden" name="btnSave" id="btnSave" value="" /> -->
              <footer>
                <button type="submit" name="btnSave" value="1" class="btn btn-success pull-left">
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



<?php echo view('includes/layout_progress_dialog'); ?>

<script
  src="<?php echo base_url(); ?>assets/js/plugin/jquery-form/jquery-form.min.js"></script>

<script type="text/javascript">
  $(document).ready(function() {

    //pageSetUp();

    var form_input = $("#form-input").validate({

      // Rules for form validation
      rules: {
        username: {
          required: true
        },
        <?php if ($record['user_id'] == 0) { ?>
          pwd: {
            required: true
          },
        <?php } ?>
        name: {
          required: true
        },
        address: {
          required: true
        }
      },

      // Messages for form validation
      messages: {
        username: {
          required: 'Please enter username'
        },
        <?php if ($record['user_id'] == 0) { ?>
          pwd: {
            required: 'Please enter password'
          },
        <?php } ?>
        name: {
          required: 'Please enter full name'
        },
        address: {
          required: 'Please enter address'
        }
      }

    });

    $("#btnCancelDummy").click(function(e) {
      $("#btnCancel").val("1");
      $("#form-cancel").submit();
      e.preventDefault();
    });

    $("#btnSaveDummy").click(function(e) {
      if ($("#form-input").valid()) {
        $.SmartMessageBox({
          title: "<i class='fa fa-save' style='color:green'></i> Save",
          content: "Save this data?",
          buttons: '[No][Yes]'
        }, function(ButtonPressed) {
          if (ButtonPressed === "Yes") {
            $("#btnSave").val("1");
            $("#form-input").submit();
          }
          if (ButtonPressed === "No") {
            $.smallBox({
              title: "Save",
              content: "<i class='fa fa-times'></i> <i>Canceled...</i>",
              color: "#6c6f72",
              iconSmall: "fa fa-times fa-2x fadeInRight animated",
              timeout: 4000
            });
          }

        });
      }
      e.preventDefault();

    });

  });

  var deletePhoto = function(userId, fieldType, fileName, hash) {
    $.SmartMessageBox({
      title: "<i class='fa fa-save' style='color:green'></i> Delete Photo",
      content: "Delete this photo?",
      buttons: '[No][Yes]'
    }, function(ButtonPressed) {
      if (ButtonPressed === "Yes") {

        $("#pleaseWaitDialog").modal();
        url = "<?php echo base_url('pp/delete_photo_doc'); ?>";
        var jqxhr = $.post(url, "user_id=" + userId + "&doc_type=" + fieldType + "&file_name=" + fileName + "&hash=" + hash, function(obj) {
            var data = $.parseJSON(obj);

            if (typeof(data.error_message) != 'undefined') {
              $.smallBox({
                title: "Delete Photo",
                content: "<i class='fa fa-times'></i> <i>" + data.error_message + "</i>",
                color: "#9d0000",
                iconSmall: "fa fa-times fa-2x fadeInRight animated",
                timeout: 4000
              });
            } else {
              $.smallBox({
                title: "Delete Photo",
                content: "<i class='fa fa-times'></i> <i>" + data.message + "</i>",
                color: "#009d4e",
                iconSmall: "fa fa-times fa-2x fadeInRight animated",
                timeout: 4000
              });

              $("#div_" + fieldType).html('<div><img src="<?php echo base_url('assets/img/no_image.png'); ?>" class="image-responsive" width="100" height="100" /></a></div>');
            }
          })
          .done(function() {})
          .fail(function() {})
          .always(function() {
            $("#pleaseWaitDialog").modal('hide');
          });

      }
      if (ButtonPressed === "No") {
        $.smallBox({
          title: "Save",
          content: "<i class='fa fa-times'></i> <i>Canceled...</i>",
          color: "#6c6f72",
          iconSmall: "fa fa-times fa-2x fadeInRight animated",
          timeout: 4000
        });
      }

    });

  }

  var uploadFile = function(obj, user_id) {
    var docName = obj.name;
    var fileObj = $(obj);
    var size = fileObj[0].files[0].size;
    var imgname = obj.value;
    obj.parentNode.nextSibling.value = obj.value;

    data = new FormData();
    data.append('photo', fileObj[0].files[0]);
    data.append('doc_type', docName);
    data.append('user_id', user_id);
    data.append('hash', '<?php echo md5("photos|||" . $record['user_id']); ?>');

    var ext = imgname.substr((imgname.lastIndexOf('.') + 1));
    if (ext == 'jpg' || ext == 'jpeg' || ext == 'png' || ext == 'gif' || ext == 'PNG' || ext == 'JPG' || ext == 'JPEG') {
      if (size <= 2000000) {
        $.ajax({
            url: "<?php echo base_url('pp/upload_doc'); ?>",
            type: "POST",
            data: data,
            enctype: 'multipart/form-data',
            processData: false, // tell jQuery not to process the data
            contentType: false // tell jQuery not to set contentType
          })
          .done(function(data) {
            if (data.indexOf("SUKSES") >= 0) {
              var arrData = data.split("|||");
              var fileName = arrData[1];
              $("#div_" + docName).html('<a href="' + fileName + '" target="_blank"><img src="' + fileName + '" class="image-responsive" width="100" height="133" /></a>');
            } else {
              alert(data);
            }

          });
        return false;
      } //end size
      else {
        alert('Sorry File size exceeding from 2 Mb');
      }
    } //end FILETYPE
    else {
      alert('Sorry you can upload JPEG|JPG|PNG|GIF file type only.');
    }

  };
</script>