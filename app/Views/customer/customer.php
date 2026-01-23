<?= $form ?>
<?= $datagrid ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('body').removeClass("hidden-menu");
        $("#header").css("margin-top", "0");
        $('#main').css("opacity", "1");
        $("#content").css("margin-top", "10px");

        $("#image_formEntryOutlet").on('change', function() {
            var imgPath = $(this)[0].value;
            var extn = imgPath.substring(imgPath.lastIndexOf('.') + 1).toLowerCase();
            if (extn == "gif" || extn == "png" || extn == "jpg" || extn == "jpeg") {
                if (typeof(FileReader) != "undefined") {
                    var image_holder = $("#image-holder");
                    image_holder.empty();
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $("<img />", {
                            "src": e.target.result,
                            "class": "thumb-image"
                        }).appendTo(image_holder);
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                } else {
                    alert("This browser does not support FileReader.");
                }
            } else {
                alert("Pls select only images");
            }
        });

        // COPIED FROM CI3: Button Save handler
        $("#btnSave_form1").click(function(e) {
            SaveConfirmationFormAndSubmit(e, $('#form1'), 'Save', "Do you want to save this data?", null, true);
        });
    });

    // COPIED FROM CI3: Main function to handle form submit with AJAX
    async function SaveConfirmationFormAndSubmit(e, form, _title = 'Save', _text = "Do you want to save this data?", prepareDataFunction = null, isAjaxPost = false) {
        if (_title == null || _title == '') _title = 'Save';
        if (_text == null || _text == '') _text = 'Do you want to save this data?';
        if (form && form.length) {
            form.addClass('was-validated');
            if (form[0].checkValidity() === false) {
                e.preventDefault();
                e.stopPropagation();
                return;
            }

            Swal.fire({
                title: _title,
                text: _text,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes"
            }).then(function(result) {
                if (result.value) {
                    if (prepareDataFunction != null) {
                        prepareDataFunction();
                    }
                    if (isAjaxPost) {
                        var form_data = new FormData();
                        var isHasFile = false;
                        $('input[type="file"]').each(function() {
                            console.log($(this));
                            isHasFile = true;
                            form_data.append($(this).attr('name'), $(this).prop('files')[0]);
                        });
                        if (isHasFile) {
                            var arrParams = form.serializeArray();
                            $.map(arrParams, function(n) {
                                form_data.append(n['name'], n['value']);
                            });
                            $('#' + form.attr('id') + ' input[type="checkbox"]').each(function() {
                                var checkbox_this = $(this);
                                if (checkbox_this.is(":checked") == true) {} else {
                                    form_data.append(checkbox_this.attr('name'), '0');
                                }
                            });

                            $.ajax({
                                    url: form.attr('action'),
                                    data: form_data,
                                    cache: false,
                                    contentType: false,
                                    processData: false,
                                    type: 'post',
                                    dataType: 'json'
                                }) // Added dataType
                                .done(function(data) {
                                    // FIXED: No need to parse, jQuery already parsed it with dataType: 'json'
                                    if (typeof(data.error_message) != 'undefined') {
                                        toastr['error'](data.error_message);
                                    } else {
                                        toastr['success'](data.message);
                                        // CI4: Close modal
                                        var modalPanelActive = $('.modal.show');
                                        if (modalPanelActive.length) {
                                            $(modalPanelActive[0]).modal('hide');
                                        }

                                        // CI4: Reload datatable (adjusted from top.parent.reloadGrid)
                                        if (typeof(reloadGrid) == 'function') {
                                            reloadGrid();
                                        }
                                    }
                                })
                                .fail(function(xhr, status, error) {
                                    console.error('AJAX Error:', status, error);
                                    toastr['error']('Failed to save data: ' + error);
                                })
                                .always(function() {});
                        } else {
                            var params = form.serialize();
                            $('#' + form.attr('id') + ' input[type="checkbox"]').each(function() {
                                var checkbox_this = $(this);
                                if (checkbox_this.is(":checked") == true) {} else {
                                    params += "&" + checkbox_this.attr('name') + "=0";
                                }
                            });
                            $.ajax({
                                    url: form.attr('action'),
                                    type: 'POST',
                                    data: params,
                                    dataType: 'json' // Added dataType
                                })
                                .done(function(data) {
                                    // FIXED: No need to parse, jQuery already parsed it with dataType: 'json'
                                    if (typeof(data.error_message) != 'undefined') {
                                        toastr['error'](data.error_message);
                                    } else {
                                        toastr['success'](data.message);
                                        // CI4: Close modal
                                        var modalPanelActive = $('.modal.show');
                                        if (modalPanelActive.length) {
                                            $(modalPanelActive[0]).modal('hide');
                                        }

                                        // CI4: Reload datatable (adjusted from top.parent.reloadGrid)
                                        if (typeof(reloadGrid) == 'function') {
                                            reloadGrid();
                                        }
                                    }
                                })
                                .fail(function(xhr, status, error) {
                                    console.error('AJAX Error:', status, error);
                                    toastr['error']('Failed to save data: ' + error);
                                })
                                .always(function() {});
                        }
                    } else {
                        form.submit();
                    }

                }
            });

        }
        e.preventDefault();
    };
</script>