 @extends('layouts.admin.app')

 @section('content')

  <div class="content-header">
         <div>
             <nav aria-label="breadcrumb">
                 <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                             {{ __('messages.Family Tree Builder') }}</a></li>
                     <li class="breadcrumb-item active" aria-current="page">{{ __('messages.Templates') }}</li>
                 </ol>
             </nav>
         </div>
     </div>

 <div class="content-body">
    
     <div class="component-section py-2 mt-0">
         <div class="d-flex justify-content-between align-items-center mb-4">
         <h4>
             {{ __('messages.Templates') }}
         </h4>
         <a href="{{ route('admin.template.create') }}">

             <button class="btn btn-primary">
                 {{ __('messages.Create a Template') }}
             </button>
         </a>
     </div>
         <div class="table-responsive">
             <table id="messagesTable" class="table table-bordered table-hover">
                 <thead class="table-dark">
                     <tr>
                         <th>{{ __('messages.Name') }}</th>
                         <th>{{ __('messages.image') }}</th>
                         <th>{{ __('messages.Action') }}</th>
                     </tr>
                 </thead>
                 <tbody>
                     @if (isset($templates) && count($templates) > 0)
                     @foreach ($templates as $User)
                     <tr id="User-row-{{ $User->id }}">
                         <td>
                             <strong>{{ $User->name }}</strong><br>
                         </td>
                         <td>
                            <img src="{{ asset( $User->image ) }}" alt="" width="100" height="100">
                         </td>
                         <td>
                             <a href="{{ route('admin.template.edit', $User->id) }}"
                                 class="btn btn-sm btn-primary">
                                 Edit
                             </a>
                             <a href="javascript:void(0);"
                                 class="btn btn-sm btn-danger delete-user"
                                 data-id="{{ $User->id }}"
                                 data-url="{{ route('admin.template.delete', $User->id) }}">
                                 Delete
                             </a>
                         </td>
                     </tr>
                     @endforeach
                     @else
                     <tr>
                         <td colspan="3" class="text-center">No Type Found</td>
                     </tr>
                     @endif
                 </tbody>
             </table>
         <div class="d-flex justify-content-end mt-3">
             {{ $templates->links('pagination::bootstrap-5') }}
         </div>
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
             User: {
                 searchPlaceholder: 'Search...',
                 sSearch: '',
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
<script>
    $(document).ready(function() {
        $('#messagesTable').DataTable({
            "paging": false,
            "info": false,
            "lengthChange": false,
            "order": [],
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search records"
            },
            "columnDefs": [{
                "orderable": false,
                "targets": 2
            }]
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.delete-user');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.getAttribute('data-id');
                const deleteUrl = this.getAttribute('data-url');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = deleteUrl;
                    }
                });
            });
        });
    });
</script>

 @endsection