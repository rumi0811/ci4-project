<?php echo view('includes/layout_breadcrumb'); ?>

<div class="row">
    <div class="col-xl-12">
        <!-- Filter Panel -->
        <div id="panel-filter" class="panel">
            <div class="panel-hdr">
                <h2>
                    <i class="subheader-icon fal fa-filter"></i> Filter
                </h2>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <form id="form_filter_1" method="post">
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" id="username" class="form-control" />
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" />
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" id="address" class="form-control" />
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Mobile</label>
                                <input type="text" name="mobile" id="mobile" class="form-control" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Email Verified</label>
                                <select class="form-control select2" name="email_verified" id="email_verified">
                                    <option value="-1">All</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">User ID</label>
                                <input type="text" name="user_id" id="user_id" class="form-control" />
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">User Code (PP Code)</label>
                                <input type="text" name="pp_code" id="pp_code" class="form-control" />
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Device ID</label>
                                <input type="text" name="device_id" id="device_id" class="form-control" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Status User</label>
                                <select class="form-control" name="is_active" id="is_active">
                                    <option value="9">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                    <option value="-1">Banned</option>
                                    <option value="-2">Dormant</option>
                                    <option value="-9">Blokir Topup</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Blokir Note</label>
                                <input type="text" name="user_note" id="user_note" class="form-control" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12">
                                <button type="button" name="btnSearch" class="btn btn-success" onClick="javascript:search()">
                                    <i id="iSearch" class="fal fa-search"></i> Search
                                </button>
                                <button type="reset" name="btnReset" class="btn btn-secondary">
                                    <i class="fal fa-redo"></i> Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Data Grid Panel -->
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    <i class="subheader-icon fal fa-table"></i> Search Result
                </h2>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <?php echo view('includes/layout_message'); ?>

                    <table id="DataGrid1" class="table table-striped table-bordered table-hover" width="100%">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Photo</th>
                                <th>Username</th>
                                <th>User Code</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Address</th>
                                <th>Created</th>
                                <th>Active</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="10">Please enter filter criteria above and click <strong>Search</strong></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="dt-toolbar-footer">
                        <div class="col-sm-12 mt-3">
                            <button type="button" onclick="editForm(0)" class="btn btn-success">
                                <i class="fal fa-plus"></i> Add User / Register PP
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Forms -->
<form id="form_delete" method="post">
    <input type="hidden" name="user_id_delete" id="user_id_delete" value="0" />
</form>

<!-- Modal Edit User -->
<div class="modal fade" id="modal_panel_edit_popup" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fal fa-edit"></i> <span id="modal_title">Add User / Register PP</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <!-- Loading Indicator -->
                <div id="modalLoadingInfoEditPopup" style="display: none; text-align: center; padding: 40px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-3">Loading data...</p>
                </div>

                <!-- Form Content -->
                <form id="form1" action="<?php echo base_url('user/save_data'); ?>" method="post" class="needs-validation" novalidate>
                    <input type="hidden" name="user_id" id="user_id" value="0" />
                    <input type="hidden" name="http_referer" value="<?php echo base_url('user'); ?>" />

                    <div class="row">
                        <!-- Left Column - Form Fields -->
                        <div class="col-md-8">
                            <div class="row">
                                <!-- Username -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Username (Email) <span class="text-danger">*</span></label>
                                    <input type="email" name="username" id="username" class="form-control" required />
                                </div>

                                <!-- Password -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="pwd" id="pwd" class="form-control" />
                                    <small class="form-text text-muted">Leave blank to keep current password</small>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Full Name -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" required />
                                </div>

                                <!-- User Code (Display Only) -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">User Code (PP Code)</label>
                                    <input type="text" name="pp_code" id="pp_code" class="form-control" readonly />
                                    <small class="form-text text-muted">Auto-generated for new users</small>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Mobile -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mobile</label>
                                    <input type="text" name="mobile" id="mobile" class="form-control" />
                                </div>

                                <!-- Address -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" id="address" class="form-control" rows="2"></textarea>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Status -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="is_active" id="is_active" class="form-control">
                                        <option value="-99">Blokir Deposit & Topup</option>
                                        <option value="-9">Blokir Topup</option>
                                        <option value="-2">Di non-aktifkan sementara</option>
                                        <option value="-1">Banned/Blokir</option>
                                        <option value="0">Inactive</option>
                                        <option value="1" selected>Active</option>
                                    </select>
                                </div>

                                <!-- Device ID -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Device ID Hash</label>
                                    <input type="text" name="device_id" id="device_id" class="form-control" />
                                </div>
                            </div>

                            <div class="row">
                                <!-- User ID Referer -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">User ID Referer</label>
                                    <input type="text" name="referral_user_id" id="referral_user_id" class="form-control" value="0" />
                                </div>

                                <!-- User Type -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">User Type</label>
                                    <select name="user_type_id" id="user_type_id" class="form-control">
                                        <?php
                                        // We'll populate this from controller
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Blokir Note -->
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Blokir Note</label>
                                    <textarea name="user_note" id="user_note" class="form-control" rows="2"></textarea>
                                </div>
                            </div>

                            <!-- Checkboxes -->
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" name="email_verified" id="email_verified" value="1" class="form-check-input" />
                                        <label class="form-check-label" for="email_verified">Email Verified</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" name="is_send_mail" id="is_send_mail" value="1" class="form-check-input" />
                                        <label class="form-check-label" for="is_send_mail">Subscribe Newsletter</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="checkbox" name="is_account_verified" id="is_account_verified" value="1" class="form-check-input" />
                                        <label class="form-check-label" for="is_account_verified">Account Verified</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Photos (Optional for now) -->
                        <div class="col-md-4">
                            <div class="text-center">
                                <label class="form-label">Profile Photo</label>
                                <div id="profile_picture_preview" style="border: 2px dashed #ccc; padding: 20px; min-height: 150px;">
                                    <img src="<?php echo base_url('assets/img/no_image.png'); ?>" style="max-width: 100%; max-height: 150px;" id="profile_picture_img" />
                                </div>
                                <small class="form-text text-muted">Photo upload will be added later</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fal fa-times"></i> Cancel
                </button>
                <button type="button" id="btnSave_form1" class="btn btn-primary">
                    <i class="fal fa-save"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>



<form id="formEdit" target="_blank" method="post">
</form>

<!-- Loading Modal -->
<div class="modal fade" id="pleaseWaitDialog" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1>Loading...</h1>
            </div>
            <div class="modal-body text-center">
                <div class="spinner-border" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var oTable;
    var isSearch = true; // Langsung true

    $(document).ready(function() {
        $('.select2').select2();

        oTable = $('#DataGrid1').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?php echo base_url(); ?>user/datatable',
                type: 'POST',
                data: function(d) {
                    d.searchFilter = $("#username").val() + "|||" +
                        $("#name").val() + "|||" +
                        $("#address").val() + "|||" +
                        $("#mobile").val() + "|||" +
                        $("#email_verified").val() + "|||" +
                        $("#user_id").val() + "|||" +
                        $("#pp_code").val() + "|||" +
                        $("#device_id").val() + "|||" +
                        $("#is_active").val() + "|||" +
                        $("#user_note").val();
                }
                // NO dataSrc! Auto-detect "data" field!
            },
            columns: [{
                    data: 'action'
                },
                {
                    data: 'photo'
                },
                {
                    data: 'username'
                },
                {
                    data: 'pp_code'
                },
                {
                    data: 'name'
                },
                {
                    data: 'mobile'
                },
                {
                    data: 'address'
                },
                {
                    data: 'created'
                },
                {
                    data: 'is_active',
                    render: function(data) {
                        return data == 'Yes' ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Inactive</span>';
                    }
                },
                {
                    data: 'user_note'
                }
            ],
            pageLength: 25,
            order: [
                [7, 'desc']
            ]
        });
    });

    function search() {
        oTable.ajax.reload();
    }

    function editForm(id) {
        console.log('editForm called with id:', id);

        // Set modal title
        $('#modal_title').text(id > 0 ? 'Edit User' : 'Add User / Register PP');

        // Reset form
        $('#form1')[0].reset();
        $('#form1').removeClass('was-validated');
        $('#email_verified').prop('checked', false);
        $('#is_send_mail').prop('checked', false);
        $('#is_account_verified').prop('checked', false);

        if (id == 0) {
            // Add new user
            $('#user_id').val(0);
            $('#pp_code').val('Auto-generated');
            $('#is_active').val(1);
            $('#user_type_id').val(<?php echo PP_USER; ?>);
            $('#referral_user_id').val(0);
            $('#modalLoadingInfoEditPopup').hide();

            // Show modal
            $('#modal_panel_edit_popup').modal('show');

        } else {
            // Show modal first with loading
            $('#modalLoadingInfoEditPopup').show();
            $('#modal_panel_edit_popup').modal('show');

            // Wait for modal to be fully shown, then load data
            $('#modal_panel_edit_popup').one('shown.bs.modal', function() {
                console.log('Modal shown, loading data for ID:', id);

                $.ajax({
                    url: '<?php echo base_url(); ?>user/edit/' + id,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        ajax: 1
                    },
                    success: function(data) {
                        console.log('User data received:', data);

                        if (data.error_message) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.error_message
                            });
                            $('#modal_panel_edit_popup').modal('hide');
                            return;
                        }

                        // Populate all fields
                        $('#form1 #user_id').val(data.user_id || 0);
                        $('#form1 #username').val(data.username || '');
                        $('#form1 #name').val(data.name || '');
                        $('#form1 #pp_code').val(data.pp_code || '');
                        $('#form1 #mobile').val(data.mobile || '');
                        $('#form1 #address').val(data.address || '');
                        $('#form1 #is_active').val(data.is_active !== undefined ? data.is_active : 1);
                        $('#form1 #device_id').val(data.device_id || '');
                        $('#form1 #referral_user_id').val(data.referral_user_id || 0);
                        $('#form1 #user_type_id').val(data.user_type_id || <?php echo PP_USER; ?>);
                        $('#form1 #user_note').val(data.user_note || '');

                        // Checkboxes
                        $('#form1 #email_verified').prop('checked', data.email_verified == 1);
                        $('#form1 #is_send_mail').prop('checked', data.is_send_mail == 1);
                        $('#form1 #is_account_verified').prop('checked', data.is_account_verified == 1);

                        // Hide loading
                        $('#modalLoadingInfoEditPopup').hide();

                        // Verify
                        console.log('Form values after populate:', {
                            user_id: $('#form1 #user_id').val(),
                            username: $('#form1 #username').val(),
                            name: $('#form1 #name').val()
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Ajax failed:', status, error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load user data: ' + error
                        });
                        $('#modal_panel_edit_popup').modal('hide');
                    },
                    complete: function() {
                        $('#modalLoadingInfoEditPopup').hide();
                    }
                });
            });
        }
    }

    function deleteConfirm(e, id) {
        e.preventDefault();

        Swal.fire({
            title: 'Delete User?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $("#user_id_delete").val(id);
                $("#form_delete").submit();
            }
        });
    }

    function reloadGrid() {
        window.location.reload();
    }
    // Save button handler
    $("#btnSave_form1").click(function(e) {
        e.preventDefault();

        var form = $("#form1");

        // Validate form
        form.addClass('was-validated');
        if (form[0].checkValidity() === false) {
            e.stopPropagation();
            return;
        }

        // Confirm save
        Swal.fire({
            title: 'Save User?',
            text: "Do you want to save this data?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, Save!",
            cancelButtonText: "Cancel"
        }).then(function(result) {
            if (result.value) {
                // Show loading
                $("#modalLoadingInfoEditPopup").show();

                // Get form data
                var formData = form.serialize();

                // Handle checkboxes (if unchecked, send 0)
                if (!$("#email_verified").is(":checked")) {
                    formData += "&email_verified=0";
                }
                if (!$("#is_send_mail").is(":checked")) {
                    formData += "&is_send_mail=0";
                }
                if (!$("#is_account_verified").is(":checked")) {
                    formData += "&is_account_verified=0";
                }

                // Ajax save
                $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: formData,
                        dataType: 'json'
                    })
                    .done(function(data) {
                        $("#modalLoadingInfoEditPopup").hide();

                        if (data.error_message) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.error_message
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message || 'Data saved successfully',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Close modal
                            $("#modal_panel_edit_popup").modal('hide');

                            // Reload datatable
                            oTable.ajax.reload(null, false);
                        }
                    })
                    .fail(function(xhr, status, error) {
                        $("#modalLoadingInfoEditPopup").hide();
                        console.error('AJAX Error:', status, error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to save data: ' + error
                        });
                    });
            }
        });
    });
</script>