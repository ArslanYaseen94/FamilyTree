 @extends('layouts.admin.app')

 @section('content')

     <div class="content-header">
         <div>
             <nav aria-label="breadcrumb">
                 <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                             {{ __('messages.Family Tree Builder') }}</a></li>
                     <li class="breadcrumb-item active" aria-current="page">{{ __('messages.order') }}</li>
                 </ol>
             </nav>
         </div>
     </div>

     <div class="content-body">
         <div class="component-section mt-0">
            <div class="d-flex justify-content-between align-items-center mb-4">
                 <h4>{{ __('messages.order') }} </h4>
             </div>
             <div class="table-responsive">
                 <table id="example1" class="table table-bordered table-striped">
                     <thead class="table-dark">
                         <tr>
                             <th class="wd-20p">{{ __('messages.User Name') }}</th>
                             <th class="wd-20p">{{ __('messages.User Email') }} </th>
                             <th class="wd-20p">{{ __('messages.Package') }}</th>
                             <th class="wd-20p">{{ __('messages.Price') }}</th>
                             <th class="wd-20p">{{ __('messages.Starting Date') }}</th>
                             <th class="wd-20p">{{ __('messages.Ending Date') }}</th>
                             <th class="wd-20p">{{ __('messages.Status') }}</th>
                         </tr>
                     </thead>
                     <tbody>
                         @if (isset($order))
                             @foreach ($order as $User)
                                 <tr id="User-row-{{ $User->id }}">
                                     <td>{{ $User->user->name }}</td>
                                     <td>{{ $User->user->email }}</td>
                                     <td>{{ $User->plan->name }}</td>
                                     <td>${{ $User->plan->monthly_price }}</td>
                                     <td>{{ \Carbon\Carbon::parse($User->package_start)->format('m/d/Y') }}</td>
<td>{{ \Carbon\Carbon::parse($User->package_end)->format('m/d/Y') }}</td>

                                     <td style="background-color: {{ $User->is_active == 1 ? '#008141' : '#DA0E0A' }};color:white !important">
                                         {{ $User->is_active == 1 ? 'Active' : 'Inactive' }}
                                     </td>

                                 </tr>
                             @endforeach
                         @endif
                     </tbody>
                 </table>
             <div class="d-flex justify-content-end mt-3">
                 {{ $order->links('pagination::bootstrap-5') }}
             </div>
             </div><!-- component-section -->
         </div><!-- content-body -->
     </div><!-- content -->
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
                         <button type="button" class="btn btn-secondary" id="cancel" data-dismiss="modal">Close</button>
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
