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
                            <form method="post">
                                <button type="submit" name="btnAdd" value="1" class="btn btn-success">
                                    <i class="fal fa-plus"></i> Add User / Register PP
                                </button>
                            </form>
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
        window.location.href = '<?php echo base_url(); ?>user/edit/' + id;
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
</script>