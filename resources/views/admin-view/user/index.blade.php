 @extends('layouts.admin.app')

 @section('content')
     <div class="content-header">
         <div>
             <nav aria-label="breadcrumb">
                 <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                             {{ __('messages.Family Tree Builder') }}</a></li>
                     <li class="breadcrumb-item active" aria-current="page">{{ __('messages.User') }}</li>
                 </ol>
             </nav>
         </div>
     </div>

     <div class="content-body">
         <div class="component-section mt-0">
            <div class="d-flex justify-content-between align-items-center mb-4">
                 <h4>{{ __('messages.User') }} </h4>
             </div>
             <div class="table-responsive">
                 <table id="example1" class="table table-bordered table-striped" style="width:100%">
                     <thead class="table-dark">
                         <tr>
                             <th class="wd-25p">{{ __('messages.membershipStatus') }} </th>
                             <th class="wd-25p">{{ __('messages.Status') }} </th>
                             <th class="wd-25p">{{ __('messages.Family Id') }} </th>
                             <th class="wd-20p">{{ __('messages.Name') }}</th>
                             <th class="wd-25p">{{ __('messages.Email') }} </th>
                             <th class="wd-25p">{{ __('messages.Phone') }}</th>
                             <th class="wd-25p">{{ __('messages.City') }}</th>
                             <th class="wd-25p">{{ __('messages.State') }}</th>
                             <th class="wd-25p">{{ __('messages.Country') }}</th>
                             <th class="wd-25p">{{ __('messages.Gender') }} </th>
                             <th class="wd-20p">{{ __('messages.Action') }}</th>
                         </tr>
                     </thead>
                     <tbody>
                         @foreach ($UserInfo as $User)
                             <tr id="User-row-{{ $User->id }}">
                                 <td>{{ $User->membership_plan }}</td>
                                 <td>
                                     @if ($User->Status == 1)
                                         <div class="badge badge-danger">Deactivate</div>
                                     @else
                                         <div class="badge badge-success">Activate </div>
                                     @endif
                                 </td>
                                 <td>{{ $User->familyId }}</td>
                                 <td>{{ $User->name }}</td>
                                 <td>{{ $User->email }}</td>
                                 <td>{{ $User->phone }}</td>
                                 <td>{{ $User->city }}</td>
                                 <td>{{ $User->state }}</td>
                                 <td>{{ $User->country }}</td>
                                 <td>{{ $User->gender }}</td>
                                 <td>
                                     <div class="dropdown">
                                         <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton"
                                             data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                             <i data-feather="settings"></i>
                                         </button>
                                         <div class="dropdown-menu tx-14" aria-labelledby="dropdownMenuButton">
                                             @if ($User->Status == 1)
                                                 <a class="dropdown-item activate-User-btn"
                                                     data-route-activate="{{ route('admin.user.activate', ['id' => $User->id]) }}"
                                                     data-id="{{ $User->id }}"
                                                     href="javascript:void(0)">{{ __('messages.Activate') }} </a>
                                             @else
                                                 <a class="dropdown-item deactivate-User-btn"
                                                     data-route-deactivate="{{ route('admin.user.deactivate', ['id' => $User->id]) }}"
                                                     data-id="{{ $User->id }}"
                                                     href="javascript:void(0)">{{ __('messages.Deactivate') }} </a>
                                             @endif
                                         </div>
                                     </div>
                                 </td>
                             </tr>
                         @endforeach
                     </tbody>
                 </table>
             </div>
             <div class="d-flex justify-content-end mt-3">
                 {{ $UserInfo->links('pagination::bootstrap-5') }}
             </div>
         </div>
     </div>
     </div>
     <div class="modal fade" id="deactivateUserModal" tabindex="-1" role="dialog"
         aria-labelledby="deactivateUserModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="updateUserModalLabel">{{ __('messages.Deactivate User') }}</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true"><i data-feather="x"></i></span>
                     </button>
                 </div>
                 <div class="modal-body text-center">
                     <div id="deactivate-User-success" class="alert alert-success light alert-dismissible fade show"
                         style="display:none;"></div>
                     <div id="deactivate-User-error" class="alert alert-danger light alert-dismissible fade show"
                         style="display:none;"></div>
                     <form id="deactivateUserForm">
                         <h4>{{ __('messages.deactivateModalMsg') }}</h4>
                         <p class="mb-4">{{ __('messages.deactivateModalMsg2') }}</p>
                         <button type="button" class="btn btn-secondary" id="cancel" data-dismiss="modal">Close</button>
                         <button type="submit" class="btn btn-primary"
                             id="confirmDeactivateBtn">{{ __('messages.Yes Deactivate it!') }}</button>
                     </form>
                 </div>
             </div>
         </div>
     </div>
     <div class="modal fade" id="activateUserModal" tabindex="-1" role="dialog" aria-labelledby="activateUserModalLabel"
         aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="updateUserModalLabel">{{ __('messages.Activate User') }}</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true"><i data-feather="x"></i></span>
                     </button>
                 </div>
                 <div class="modal-body text-center">
                     <div id="activate-User-success" class="alert alert-success light alert-dismissible fade show"
                         style="display:none;"></div>
                     <div id="activate-User-error" class="alert alert-danger light alert-dismissible fade show"
                         style="display:none;"></div>
                     <form id="activateUserForm">
                         <h4>{{ __('messages.activateModalMsg') }}</h4>
                         <p class="mb-4">{{ __('messages.activateModalMsg2') }}</p>
                         <button type="button" class="btn btn-secondary" id="cancel"
                             data-dismiss="modal">Close</button>
                         <button type="submit" class="btn btn-primary"
                             id="confirmActivateBtn">{{ __('messages.Yes Activate it!') }}</button>
                     </form>
                 </div>
             </div>
         </div>
     </div>
 @endsection
@section('scripts')
    <script>
        $(function() {
            'use strict'
            $('#example1').DataTable({
                paging: false,
                info: false,
                lengthChange: false,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records"
                }
            });
            // Select2
            $('.dataTables_length select').select2({
                minimumResultsForSearch: Infinity
            });
        });

         $('.deactivate-User-btn').click(function() {
             var UserId = $(this).data('id');
             var deactivateRoute = $(this).data('route-deactivate');
             $('#confirmDeactivateBtn').attr('data-id', UserId);
             $('#confirmDeactivateBtn').attr('data-route', deactivateRoute);
             $('#deactivateUserModal').modal('show');
         });

         $('#confirmDeactivateBtn').click(function(e) {
             e.preventDefault();
             var UserId = $(this).data('id');
             var deactivateRoute = $(this).data('route');

             $('#deactivate-User-error').hide();
             $('#confirmDeactivateBtn').prop('disabled', true);
             $('#confirmDeactivateBtn').html(
                 '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
             );
             $('#cancel').hide();

             $.ajax({
                 url: deactivateRoute,
                 type: 'POST',
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
                 success: function(response) {
                     $('#deactivate-User-success').text(response.message).show();
                     setTimeout(function() {
                         $('#confirmDeactivateBtn').prop('disabled', false);
                         $('#confirmDeactivateBtn').html('Yes Deactivate it!');
                         $('#cancel').show();
                         $('#deactivate-User-success').fadeOut();
                         $('#deactivateUserModal').modal('hide');
                         window.location.reload();
                     }, 1000);
                 },
                 error: function(xhr) {
                     $('#confirmDeactivateBtn').prop('disabled', false);
                     $('#confirmDeactivateBtn').html('Yes Deactivate it!');
                     $('#cancel').show();
                     $('#deactivate-User-error').text(response.message).show();
                 }
             });
         });

         $('.activate-User-btn').click(function() {
             var UserId = $(this).data('id');
             var activateRoute = $(this).data('route-activate');
             $('#confirmActivateBtn').attr('data-id', UserId);
             $('#confirmActivateBtn').attr('data-route', activateRoute);
             $('#activateUserModal').modal('show');
         });

         $('#confirmActivateBtn').click(function(e) {
             e.preventDefault();
             var UserId = $(this).data('id');
             var activateRoute = $(this).data('route');

             $('#activate-User-error').hide();
             $('#confirmActivateBtn').prop('disabled', true);
             $('#confirmActivateBtn').html(
                 '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
             );
             $('#cancel').hide();

             $.ajax({
                 url: activateRoute,
                 type: 'POST',
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
                 success: function(response) {
                     $('#activate-User-success').text(response.message).show();
                     setTimeout(function() {
                         $('#confirmActivateBtn').prop('disabled', false);
                         $('#confirmActivateBtn').html('Yes Activate it!');
                         $('#cancel').show();
                         $('#activate-User-success').fadeOut();
                         $('#activateUserModal').modal('hide');
                         window.location.reload();
                     }, 1000);
                 },
                 error: function(xhr) {
                     $('#confirmActivateBtn').prop('disabled', false);
                     $('#confirmActivateBtn').html('Yes Activate it!');
                     $('#cancel').show();
                     $('#activate-User-error').text(xhr.responseJSON.message).show();
                 }
             });
         });
     </script>
@endsection
