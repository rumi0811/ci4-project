<div class="row">

  <div class="col-sm-12">

      <div class="row">

        <div class="col-sm-12 col-md-12 col-lg-12">
          <div class="well well-sm well-light well-profile">

            <div class="row">

              <div class="col-sm-12">

                <div class="row">
                  <div class="col-md-12 profile-pic">
                    <?php
                    if (file_exists(APPPATH."../images/user/".$dataPost['profile_pic']) && $dataPost['profile_pic'] != "") {
                      ?>
                      <img class="animated" src='../images/user/<?php echo $dataPost['profile_pic'] ?>' alt='<?php echo $dataPost['name']; ?>'>
                    <?php
                    }else{
                    ?>
                   <img class="animated" src="../assets/images/avatars/male.png">
                  <?php } ?>
                  </div>
                </div>

                <div class="row">
                  <div class="col-sm-12 text-center">
                    <h1><span class="semi-bold"><?php echo $dataPost['name']; ?></span>
                        <br>
                        <small style="text-transform:lowercase"><?php echo $dataPost['login_name']; ?></small></h1>

                    <?php /*                    <ul class="list-unstyled">
                        <li>
                            <p class="text-muted">
                                <i class="fa fa-phone"></i>&nbsp;&nbsp;(<span class="txt-color-darken">313</span>) <span class="txt-color-darken">464</span> - <span class="txt-color-darken">6473</span>
                            </p>
                        </li>
                        <li>
                            <p class="text-muted">
                                <i class="fa fa-envelope"></i>&nbsp;&nbsp;<a href="mailto:simmons@smartadmin">ceo@smartadmin.com</a>
                            </p>
                        </li>
                    </ul>
                    <br>*/ ?>

                    <form method="post" id="formProfile" enctype="multipart/form-data" class="well smart-form form-profile padding-bottom-10">

                      <section style="display:none">
      									<label class="label">Profile Picture</label>
      									<label class="input">
      										<input type="file" id="profile_pic" name="profile_pic" accept="image/*" />
      									</label>
      								</section>

                      <section>
                        <label class="label">Password Lama</label>
                        <label class="input">
                          <input type="password" rows="2" class="form-control" id="old_pass" name="old_pass" />
                        </label>
                      </section>

                      <section>
                        <label class="label">Password Baru</label>
                        <label class="input">
                          <input type="password" rows="2" class="form-control" id="new_pass" name="new_pass" />
                        </label>
                      </section>

                      <section>
                        <label class="label">Konfirmasi Password Baru</label>
                        <label class="input">
                          <input type="password" rows="2" class="form-control" id="con_new_pass" name="con_new_pass" />
                        </label>
                      </section>


                      <div class="margin-top-10">
                        <button type="button" id="btnSave" disabled="true" class="btn btn-sm btn-primary pull-right">
                                Simpan
                            </button>
                        <br/>
                        <br/>
                      </div>
                    </form>
                    <br>

                  </div>

                </div>

              </div>

            </div>


          </div>

        </div>

      </div>

  </div>

</div>

<!-- end row -->

</section>
<!-- end widget grid -->

<script type="text/javascript">
  $(document).ready(function() {
    $('#old_pass').on("change", function() {
      if ($(this).val() != '') {
        var oldPass = $(this).val();
        $.ajax({
          url: '<?php echo base_url(); ?>user/checkPassValidity',
          type: 'POST',
          data: {
            'password': oldPass
          },
          dataType: 'json',
          success: function(respond) {
            if (respond == 0) {
              alertify.error('Password lama salah');
              $('#btnSave').prop('disabled', true);
            } else {
              $('#btnSave').prop('disabled', false);
            }
          }
        });
      }
    }).trigger('change');

    $("#btnSave").on("click", function() {
      var newPass = $("#new_pass").val();
      var confPass = $("#con_new_pass").val();

      if (newPass == confPass) {
        $.ajax({
          url: '<?php echo base_url(); ?>user/saveNewPassword',
          type: 'POST',
          data: {
            'password': newPass
          },
          dataType: 'json',
          success: function(data) {
            if (typeof(data.error_message) != 'undefined') {
              $.smallBox({
                title: 'Ganti Password',
                sound: false,
                content: "<i class='fa fa-times'></i> <i>" + data.error_message + "</i>",
                color: '#9d0000',
                timeout: 5000,
                iconSmall: 'fa fa-times fa-2x fadeInRight animated',
              });
            } else {
              $.smallBox({
                title: 'Ganti Password',
                sound: false,
                content: "<i class='fa fa-check'></i> <i>" + data.message + "</i>",
                color: '#009d4e',
                timeout: 5000,
                iconSmall: 'fa fa-times fa-2x fadeInRight animated',
              });
            }
          }
        });
      } else {
        //alert('Konfirmasi password tidak cocok');
        alertify.error('Konfirmasi password tidak cocok');
      }
    });


    // Avatar
    $(".profile-pic img").click(function(){
      $('#profile_pic').trigger('click');
    });

    function readURL(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {

          var profilePicTL = new TimelineMax();

          profilePicTL
            .set(".profile-pic", {
              transformOrigin: "50% 50%",
            })
            .to(".profile-pic", 2, {
              transform: "rotate(-15deg)",
              ease: Elastic.easeOut
            })
            .to(".profile-pic", .8, {
              transform: "translateY(650px) rotate(20deg)",
              ease: Back.easeIn,
              onComplete: function() {
                $('.profile-pic img').attr('src', e.target.result );
                $('.profile-pic img').css('opacity', '0');
              }
            }, "-=1.2")
            .set(".profile-pic", {
              transform: "translateY(-200px) rotate(0)",
              transformOrigin: "50% 50%",
              onComplete: function() {
                $('.profile-pic img').css('opacity', '1');
              }
            })
            .to(".profile-pic", .2, {
              transform: "translateY(0)",
              opacity: 1,
              ease: Power2.easeIn
            })
            .set(".profile-pic", {
              transformOrigin: "50% 100%"
            })
            .to(".profile-pic", .1, {
              transform: "scaleX(1.6) scaleY(.3)",
              ease: Power4.easeOut
            })
            .to(".profile-pic", .8, {
              transform: "scaleX(1) scaleY(1)",
              opacity: 1,
              ease: Elastic.easeOut
            });


            var wellTL = new TimelineMax();

            wellTL
            .set(".well-profile", {
              transformOrigin: "100% 100%"
            })
            .to(".well-profile", .7, {
              transform: "rotate(15deg) skew(0)",
              ease: Back.easeOut
            })
            .to(".well-profile", .2, {
              transform: "rotate(-5deg) skewY(-10deg)",
              ease: Back.easeIn
            })
            .to(".well-profile", 1, {
              transform: "rotate(0) skew(0)",
              ease: Elastic.easeOut
            })
            .set(".well-profile", {
              transformOrigin: "50% 100%"
            })
            .to(".well-profile", .2, {
              transform: "scaleX(1.1) scaleY(.9)",
              delay: .9,
              ease: Power4.easeIn
            }, "-=.7")
            .to(".well-profile", .8, {
              transform: "scale(1)",
              ease: Elastic.easeOut
            });


            var loginInfoTL = new TimelineMax();

            loginInfoTL
            .to(".login-info img", .3, {
              delay: .7,
              transform: "translateX(-40px) scaleX(0)",
              ease: Back.easeIn,
              onComplete: function() {
                $('.login-info img').attr('src', e.target.result );
            }})
            .to(".login-info img", .3, {
              delay: 1.2,
              transform: "translateX(0) scaleX(1)",
              ease: Back.easeOut,
              onComplete: function() {
                //
              }
            });
        }
        reader.readAsDataURL(input.files[0]);
      }
    }



    $("#profile_pic").change(function() {
      var path = $("#profile_pic").val();
      var fileName = path.replace(/^.*\\/, "");
      var pos = fileName.lastIndexOf(".");
      var type = fileName.substring(pos+1);
      if(type == 'jpg' || type == 'gif' || type == 'png' || type == 'JPG' || type == 'GIF' || type == 'PNG') {
        readURL(this);
        var file = this.files[0];
        var formData = new FormData();
        formData.append('file', file);
        $.ajax({
          url: '<?php echo base_url(); ?>user/saveImage',
          type: 'POST',
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
//      data: $('#formProfile').serialize(),
          dataType: 'text',
          success: function (data) {
            if (typeof(data.error_message) != 'undefined') {
              setTimeout(function () {
                $.smallBox({
                  title: 'Ganti Foto',
                  sound: false,
                  content: "<i class='fa fa-times'></i> <i>" + "Foto Profil gagal disimpan" + "</i>",
                  color: '#9d0000',
                  timeout: 5000,
                  iconSmall: 'fa fa-times fa-2x fadeInRight animated'
                });
                $(".profile-pic img").attr("src", "../images/user/<?php echo $dataPost['profile_pic'] ?>");
              }, 2500);
            } else {
              setTimeout(function () {
                $.smallBox({
                  title: 'Ganti Foto',
                  sound: false,
                  content: "<i class='fa fa-check'></i> <i>" + "Foto Profil berhasil disimpan" + "</i>",
                  color: '#009d4e',
                  timeout: 5000,
                  iconSmall: 'fa fa-times fa-2x fadeInRight animated'
                });
              }, 2500);
            }
          }
        });
      }else{
        setTimeout(function () {
          $.smallBox({
            title: 'Ganti Foto',
            sound: false,
            content: "<i class='fa fa-times'></i> <i>" + "Foto Profil gagal disimpan, type file foto harus jpg atau png atau gif" + "</i>",
            color: '#9d0000',
            timeout: 5000,
            iconSmall: 'fa fa-times fa-2x fadeInRight animated'
          });
//          $(".profile-pic img").attr("src", "../images/user/<?php //echo $dataPost['profile_pic'] ?>//");
        }, 500);
      }
    });

    $('#formProfile').on('submit', uploadFiles);


    function uploadFiles(event){
     //alert("tes");
     var path = $("#profile_pic").val();
     var fileName = path.replace(/^.*\\/, "");
     $.ajax({
       url: '<?php echo base_url(); ?>user/saveImage',
       type: 'POST',
       data: $('#formProfile').serialize(),
       dataType: 'json',
       success: function(data) {
         if (typeof(data.error_message) != 'undefined') {
           $.smallBox({
             title: 'Change Password',
             sound: false,
             content: "<i class='fa fa-times'></i> <i>" + data.error_message + "</i>",
             color: '#9d0000',
             timeout: 5000,
             iconSmall: 'fa fa-times fa-2x fadeInRight animated',
           });
         } else {
           $.smallBox({
             title: 'Change Password',
             sound: false,
             content: "<i class='fa fa-check'></i> <i>" + data.message + "</i>",
             color: '#009d4e',
             timeout: 5000,
             iconSmall: 'fa fa-times fa-2x fadeInRight animated',
           });
         }
       }
     });
   }

  });
</script>
