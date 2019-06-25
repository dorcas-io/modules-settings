@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection
@section('body_content_main')
    @include('layouts.blocks.tabler.alert')
<div class="row">

    @include('layouts.blocks.tabler.sub-menu')
    <div class="col-md-9" id="access-grants-profile">

        <div class="row row-cards row-deck">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap bootstrap-table"
                           data-pagination="true"
                           data-search="true"
                           data-side-pagination="server"
                           data-show-refresh="true"
                           data-id-field="id"
                           data-unique-id="id"
                           data-row-attributes="processRows"
                           data-response-handler="processRecords"
                           data-url="{{ route('settings-access-grants-search') }}?{{ http_build_query(!empty($arguments) ? $arguments : []) }}"
                           id="tbl-listing"
                           v-on:click="clicked($event)">
                        <thead>
                        <tr>
                            <th class="w-1" data-field="avatar">&nbsp;</th>
                            <th data-field="name">Name</th>
                            <th data-field="email">Email</th>
                            <th data-field="modules_count">Modules</th>
                            <th data-field="pending_modules_count">Pending Modules</th>
                            <th data-field="status">Request Status</th>
                            <th data-field="created_at">Requested On</th>
                            <th data-field="menu">Actions</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @component('layouts.components.tabler.modals.standard')
            @slot('id')
                modal-respond-to-access-request
            @endslot
            @slot('title')
                Respond to Request
            @endslot
            <form action="" method="post">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <div class="form-group">
                            <label class="form-label">Set Request Status</label>
                            <select name="status" id="status" class="form-control custom-select">
                                <option value="accepted">Accept the Request</option>
                                <option value="rejected">Reject the Request</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                        <h4>Approved Modules</h4>
                        <ul v-if="typeof account.id !== 'undefined' && account.extra_json.modules.length > 0">
                            <li v-for="(module, index) in account.extra_json.modules" :key="'approved-module-' + index">@{{ titleCase(module) }}</li>
                        </ul>
                        <p v-else>No approved modules.</p>
                    </div>
                    <div class="col-sm-12 col-md-6">
                        <h4>Pending Modules</h4>
                        <div class="form-group" v-if="typeof account.id !== 'undefined' && account.extra_json.pending_modules.length > 0">
                            <div class="form-label">&nbsp;</div>
                            <div class="custom-controls-stacked">
                                <label class="custom-control custom-checkbox"
                                       v-for="(module, index) in account.extra_json.pending_modules" :key="'module-' + index">
                                    <input type="checkbox" class="custom-control-input" name="modules[]" :value="module"
                                           checked>
                                    <span class="custom-control-label">@{{ titleCase(module) }}</span>
                                </label>
                            </div>
                        </div>
                        <p v-else>No pending modules requiring approval.</p>
                    </div>
                </div>
                <input type="hidden" name="grant_id" v-model="account.id" />
                <button type="submit" name="action" value="update_request_status" class="btn btn-primary"
                        v-if="typeof account.id !== 'undefined' && account.extra_json.pending_modules.length > 0">Save Response</button>
            </form>
        @endcomponent
    </div>
</div>
@endsection
@section('body_js')
    <script>
        app.currentUser = {!! json_encode($dorcasUser) !!};
        let vmAccessGrants = new Vue({
            el: '#access-grants-profile',
            data: {
                accounts: [],
                request_id: '{{ !empty($requestId) ? $requestId : '' }}',
                account: {}
            },
            mounted: function () {
                 this.triggerEdit();
            },
            methods: {
                titleCase: function (string) {
                    return v.titleCase(string);
                },
                clicked: function ($event) {
                    let target = $event.target;
                    if (!target.hasAttribute('data-action')) {
                        target = target.parentNode.hasAttribute('data-action') ? target.parentNode : target;
                    }
                    //console.log(target, target.getAttribute('data-action'));
                    let action = target.getAttribute('data-action');
                    let index = parseInt(target.getAttribute('data-index'), 10);
                    if (isNaN(index)) {
                        console.log('Index is not set.');
                        return;
                    }
                    if (action === 'view') {
                        return true;
                    } else if (action === 'edit') {
                        this.editItem(index);
                    } else if (action === 'delete') {
                        this.deleteItem(index);
                    } else if (action === 'restore') {
                        // do nothing
                    } else {
                        return true;
                    }
                },
                editItem: function (index) {
                    let account = typeof this.accounts[index] !== 'undefined' ? this.accounts[index] : {};
                    //console.log(account);
                    if (typeof account.id === 'undefined') {
                        return false;
                    }
                    this.account = account;
                    $('#modal-respond-to-access-request').modal('show');
                },
                restoreItem: function (index) {
                    let context = this;
                    let account = typeof this.accounts[index] !== 'undefined' ? this.accounts[index] : {};
                    //console.log(account);
                    if (typeof account.id === 'undefined') {
                        return false;
                    }
                    if (!account.is_trashed) {
                        return swal('Restore Account', 'The enrollments does not need to be restored', 'info');
                    }
                    Swal.fire({
                        animation: true,
                        title: "Restore Account",
                        text: "You are about to restore this enrollments: " + account.firstname + ' ' + account.lastname,
                        customClass: 'swal2-btns-left',
                        showCancelButton: true,
                        confirmButtonClass: 'swal2-btn swal2-btn-confirm',
                        confirmButtonText: 'Yes, continue.',
                        cancelButtonClass: 'swal2-btn swal2-btn-cancel',
                        cancelButtonText: 'Cancel',
                        showLoaderOnConfirm: true,
                        preConfirm: (restore_item) => {
                            /*console.log(restore_item);
                            if (typeof b.dismiss !== 'undefined' && b.dismiss === 'cancel') {
                                return '';
                            }*/
                            context.is_publishing = true;
                            return axios.post("/xhr/admin/people/" + account.id)
                                .then(function (response) {
                                    //console.log(response);
                                    window.location = '{{ url()->current() }}';
                                    return swal("Done!", "The user enrollments was successfully restored.", "success");
                                })
                                .catch(function (error) {
                                    let message = '';
                                    if (error.response) {
                                        // The request was made and the server responded with a status code
                                        // that falls out of the range of 2xx
                                        //let e = error.response.data.errors[0];
                                        //message = e.title;
                                                var e = error.response;
                                                message = e.data.message;
                                    } else if (error.request) {
                                        // The request was made but no response was received
                                        // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                        // http.ClientRequest in node.js
                                        message = 'The request was made but no response was received';
                                    } else {
                                        // Something happened in setting up the request that triggered an Error
                                        message = error.message;
                                    }
                                    return swal("Restore Failed", message, "warning");
                                });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    });


                },
                deleteItem: function (index) {
                    let context = this;
                    let account = typeof this.accounts[index] !== 'undefined' ? this.accounts[index] : {};
                    //console.log(account);
                    if (typeof account.id === 'undefined') {
                        return false;
                    }
                    Swal.fire({
                        animation: true,
                        title: "Delete this Module Access Request?",
                        text: "You are about to remove this module access request",
                        customClass: 'swal2-btns-left',
                        showCancelButton: true,
                        confirmButtonClass: 'swal2-btn swal2-btn-confirm',
                        cancelButtonClass: 'swal2-btn swal2-btn-cancel',
                        confirmButtonText: 'Yes, continue.',
                        cancelButtonText: 'Cancel',
                        showLoaderOnConfirm: true,
                        preConfirm: (delete_item) => {
                            /*console.log(delete_item);
                            if (typeof b.dismiss !== 'undefined' && b.dismiss === 'cancel') {
                                return '';
                            }*/
                            context.is_publishing = true;
                            return axios.delete("/mse/settings-access-grants/" + account.id)
                                .then(function (response) {
                                    //console.log(response);
                                    window.location = '{{ url()->current() }}';
                                    return swal("Done!", "The module access request was successfully removed.", "success");
                                })
                                .catch(function (error) {
                                    let message = '';
                                    if (error.response) {
                                        // The request was made and the server responded with a status code
                                        // that falls out of the range of 2xx
                                        //let e = error.response.data.errors[0];
                                        //message = e.title;
                                            var e = error.response;
                                            message = e.data.message;
                                    } else if (error.request) {
                                        // The request was made but no response was received
                                        // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                        // http.ClientRequest in node.js
                                        message = 'The request was made but no response was received';
                                    } else {
                                        // Something happened in setting up the request that triggered an Error
                                        message = error.message;
                                    }
                                    return swal("Deleting Failed", message, "warning");
                                });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    });

                },
                triggerEdit: function () {
                    if (this.request_id.length === 0 || this.accounts.length === 0) {
                        return '';
                    }
                    let indexOf = -1;
                    let totalCount = this.accounts.length;
                    for (let i = 0; i < totalCount; i++) {
                        if (this.accounts[i].id !== this.request_id) {
                            continue;
                        }
                        indexOf = i;
                        break;
                    }
                    if (indexOf === -1) {
                        return '';
                    }
                    this.editItem(indexOf);
                }
            }
        });

        function processRecords(response) {
            //console.log(response);
            vmAccessGrants.accounts = response.rows;
            vmAccessGrants.triggerEdit();
            return response;
        }

        function processRows(row, index) {
            let user = typeof row.user !== 'undefined' && row.user.data !== null ? row.user.data : null;
            if (user !== null) {
                row.avatar = '<div class="avatar d-block" style="background-image: url(' + row.photo + ')">';
                if (row.photo === null) {
                    row.avatar += row.firstname.substr(0, 1) + row.lastname.substr(0, 1);
                }
                row.avatar+= '</div>';
                row.name = user.firstname + ' ' + user.lastname;
                row.email = user.email;
            }
            row.modules_count = typeof row.extra_json.modules !== 'undefined' ? row.extra_json.modules.length : 0;
            row.pending_modules_count = typeof row.extra_json.pending_modules !== 'undefined' ? row.extra_json.pending_modules.length : 0;
            row.menu = '';
            if (app.currentUser.uuid !== row.id) {
                row.menu = '<div style="font-size: 1.2rem; margin-left: 10px;">';
                row.menu += '<a href="#" class="btn btn-icon"><i data-action="edit" data-index="' + index + '"  class="fe fe-eye"></i></a>';
                row.menu += '<a href="#" class="btn btn-icon"><i  data-action="delete" data-index="' + index + '" class="fe fe-trash-2"></i></a>';
                row.menu += '</div>';
            }
            return row;
        }


    </script>
@endsection
