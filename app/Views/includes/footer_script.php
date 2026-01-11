<script src="<?php echo base_url(); ?>assets/4.5.1/js/vendors.bundle.js"></script> 
<script src="<?php echo base_url(); ?>assets/4.5.1/js/app.bundle.js"></script>
<script src="<?php echo base_url(); ?>assets/4.5.1/js/dependency/moment/moment.js"></script>
<script src="<?php echo base_url(); ?>assets/4.5.1/js/formplugins/select2/select2.bundle.js"></script>
<script src="<?php echo base_url(); ?>assets/4.5.1/js/datagrid/datatables/datatables.bundle.js"></script>
<script src="<?php echo base_url(); ?>assets/4.5.1/js/datagrid/datatables/datatables.export.js"></script>
<script src="<?php echo base_url(); ?>assets/4.5.1/js/notifications/sweetalert2/sweetalert2.bundle.js"></script>
<script src="<?php echo base_url(); ?>assets/4.5.1/js/notifications/toastr/toastr.js"></script>
<script src="<?php echo base_url(); ?>assets/4.5.1/js/formplugins/dropzone/dropzone.js"></script>
<script src="<?php echo base_url(); ?>assets/4.5.1/js/miscellaneous/lightgallery/lightgallery.bundle.js"></script>
<script src="<?php echo base_url(); ?>assets/4.5.1/js/plugin/jquery-nestable/jquery.nestable.min.js"></script>
<script src="<?php echo base_url(); ?>assets/4.5.1/js/formplugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/4.5.1/js/formplugins/bootstrap-daterangepicker/bootstrap-daterangepicker.js"></script>
<script type="text/javascript">
    /* Activate smart panels */
    Dropzone.autoDiscover = false;
    
    $('#js-page-content').smartPanel({
            onRefresh: function() {
                if (typeof(top.parent.reloadGrid) == 'function') {
                    top.parent.reloadGrid();
                }
            }
        }
    );

	$('.select2').select2();

    $.extend( true, $.fn.dataTable.defaults, {
        fixedColumns:
        {
            leftColumns: 1
        },
        responsive: true,
        fixedHeader: true,
        lengthMenu: [[10, 25, 50, 100, '-1'], [10, 25, 50, 100, 'All']],
        displayLength : 25,
        dom:
                /*	--- Layout Structure 
					--- Options
					l	-	length changing input control
					f	-	filtering input
					t	-	The table!
					i	-	Table information summary
					p	-	pagination control
					r	-	processing display element
					B	-	buttons
					R	-	ColReorder
					S	-	Select

					--- Markup
					< and >				- div element
					<"class" and >		- div with a class
					<"#id" and >		- div with an ID
					<"#id.class" and >	- div with an ID and a class

					--- Further reading
					https://datatables.net/reference/option/dom
					--------------------------------------
					*/
					"<'row mb-3'<'col-sm-6 col-md-4 d-flex align-items-center justify-content-start'f><'col-sm-6 col-md-2 d-flex align-items-center justify-content-start'l><'col-sm-12 col-md-6 d-flex align-items-center justify-content-end'B>>" +
					"<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        buttons: [
                        {
                        	extend:    'colvis',
                        	text:      'Column Visibility',
                        	titleAttr: 'Col visibility',
                            className: 'btn-outline-default'
                        },
                        {
                            extend: 'pdfHtml5',
                            text: 'PDF',
                            titleAttr: 'Generate PDF',
                            className: 'btn-outline-danger btn-sm mr-1'
                        },
                        {
                            extend: 'excelHtml5',
                            text: 'Excel',
                            titleAttr: 'Generate Excel',
                            className: 'btn-outline-success btn-sm mr-1',
                            exportOptions: {
                                orthogonal: 'export'
                            }
                        },
                        {
                            extend: 'csvHtml5',
                            text: 'CSV',
                            titleAttr: 'Generate CSV',
                            className: 'btn-outline-primary btn-sm mr-1',
                            exportOptions: {
                                orthogonal: 'export'
                            }
                        },
                        {
                            extend: 'copyHtml5',
                            text: 'Copy',
                            titleAttr: 'Copy to clipboard',
                            className: 'btn-outline-primary btn-sm mr-1'
                        },
                        {
                            extend: 'print',
                            text: 'Print',
                            titleAttr: 'Print Table',
                            className: 'btn-outline-primary btn-sm'
                        }
                    ],
        lengthChange: true,       
        paginationType: "full_numbers",
        language: {
            lengthMenu: "Show _MENU_"
        },             
        processing: true,
        serverSide: true,
    } );

    toastr.options = {
        'closeButton': true,
        'debug': false,
        'newestOnTop': true,
        'progressBar': true,
        'positionClass': 'toast-top-right',
        'preventDuplicates': true,
        'onclick': null,
        'showDuration': 300,
        'hideDuration': 100,
        'timeOut': 5000,
        'extendedTimeOut': 1000,
        'showEasing': 'swing',
        'hideEasing': 'linear',
        'showMethod': 'fadeIn',
        'hideMethod': 'fadeOut'
    };

    
    async function SaveConfirmationFormAndSubmit(e, form, _title = 'Save', _text = "Do you want to save this data?", prepareDataFunction = null, isAjaxPost = false)
	{
        if (_title == null || _title == '') _title = 'Save';
        if (_text == null || _text == '') _text = 'Do you want to save this data?';
        if  (form && form.length) {
            form.addClass('was-validated');
            if (form[0].checkValidity() === false)
            {
                e.preventDefault();
                e.stopPropagation();
                return;
            }
            
            Swal.fire(
            {
                title: _title,
                text: _text,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes"
            }).then(function(result)
            {
                if (result.value)
                {
                    if (prepareDataFunction != null) {
                        prepareDataFunction();
                    }
                    if (isAjaxPost)
                    {
                        var form_data = new FormData();                  
                        var isHasFile = false;
                        $('input[type="file"]').each(function()
                        {
                            console.log($(this));
                            isHasFile = true;
                            form_data.append($(this).attr('name'), $(this).prop('files')[0]);
                        });
                        if (isHasFile) {
                            var arrParams = form.serializeArray();
                            $.map(arrParams, function(n){
                                form_data.append(n['name'], n['value']);
                            });
                            $('#' + form.attr('id') + ' input[type="checkbox"]').each( function () {
                                var checkbox_this = $(this);
                                if( checkbox_this.is(":checked") == true ) {
                                } else {
                                    form_data.append(checkbox_this.attr('name'), '0');
                                }
                            });

                            $.ajax({
                                url: form.attr('action'), 
                                data: form_data, 
                                cache: false,
                                contentType: false,
                                processData: false,
                                type: 'post'})
                            .done(function (obj) {
                                var data = $.parseJSON(obj);
                                    if (typeof (data.error_message) != 'undefined') {
                                        toastr['error'](data.error_message);
                                    }
                                    else {
                                        toastr['success'](data.message);
                                        //var modalPanelActive = $('#modal_panel');
                                        var modalPanelActive = $('.modal.show');
                                        if (modalPanelActive.length) {
                                            $(modalPanelActive[0]).modal('hide');
                                        }
                                        
                                        if (typeof(top.parent.reloadGrid) == 'function') {
                                            top.parent.reloadGrid();
                                        }
                                    }
                            })
                            .fail(function () {
                            })
                            .always(function () {
                            });
                        }
                        else {
                            var params = form.serialize();
                            $('#' + form.attr('id') + ' input[type="checkbox"]').each( function () {
                                var checkbox_this = $(this);
                                if( checkbox_this.is(":checked") == true ) {
                                } else {
                                    params += "&" + checkbox_this.attr('name') + "=0";
                                }
                            });
                            var jqxhr = $.post(form.attr('action'), params, function (obj) {
                                var data = $.parseJSON(obj);

                                if (typeof (data.error_message) != 'undefined') {
                                    toastr['error'](data.error_message);
                                }
                                else {
                                    toastr['success'](data.message);
                                    var modalPanelActive = $('#modal_panel');
                                    if (modalPanelActive.length) {
                                        modalPanelActive.modal('hide');
                                    }
                                    
                                    if (typeof(top.parent.reloadGrid) == 'function') {
                                        top.parent.reloadGrid();
                                    }
                                }
                            })
                            .done(function () {
                            })
                            .fail(function () {
                            })
                            .always(function () {
                            });
                        }                        
                    }
                    else {
                        form.submit();
                    }
                    
                }
            });
                
        }
        e.preventDefault();
    };
    
    

    async function SaveConfirmationAndSubmit(e, _title = 'Save', _text = "Do you want to save this data?", prepareDataFunction = null, isAjaxPost = false)
	{
        SaveConfirmationFormAndSubmit(e, $('#form-input'), _title, _text, prepareDataFunction, isAjaxPost);
    };
    
    
	async function DeleteConfirmation(e, callbackFunction)
	{
		Swal.fire(
		{
			title: "Delete",
			text: "Do you want to delete selected data?",
			icon: "question",
			showCancelButton: true,
			confirmButtonText: "Yes"
		}).then(function(result)
		{
            if (result.value)
            {
                callbackFunction();
            }
		});
        e.preventDefault();
	};

    
    async function UploadConfirmationAndSubmit(e, url)
	{
        Swal.fire(
        {
            title: 'Upload',
            text: 'Do you want to upload data?',
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes"
        }).then(function(result)
        {
            if (result.value)
            {
                var form = $("#form-upload-data");
                var jqxhr = $.post(form.attr('action'), form.serialize(), function (obj) {
                    var data = $.parseJSON(obj);

                    if (typeof (data.error_message) != 'undefined') {
                        toastr['error'](data.error_message);
                    }
                    else {
                        toastr['success'](data.message);
                        $('#modal_panel_upload_data').modal('hide');
                        
                        if (typeof(top.parent.reloadGrid) == 'function') {
                            top.parent.reloadGrid();
                        }
                    }
                })
                .done(function () {
                })
                .fail(function () {
                })
                .always(function () {
                });
            }
        });
        e.preventDefault();
    };
</script>	