 @extends('layouts.admin.app')

 @section('content')
     <div class="content-header">
         <div>
             <nav aria-label="breadcrumb">
                 <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">
                             {{ __('messages.Family Tree Builder') }}</a></li>
                     <li class="breadcrumb-item active" aria-current="page">{{ __('messages.Plan') }}</li>
                 </ol>
             </nav>
         </div>
     </div>

     <div class="content-body">
         <div class="component-section mt-0">
             <div class="d-flex justify-content-between align-items-center mb-4">
                 <h4>{{ __('messages.Plans Table') }} </h4>
                 <button class="btn btn-primary" data-toggle="modal" data-target="#addPlanModal">
                     {{ __('messages.Add New Plan') }} </button>
             </div>
             <div class="table-responsive">
                 <table id="example1" class="table table-bordered table-striped">
                     <thead class="table-dark">
                         <tr>
                             <th class="wd-20p">{{ __('messages.Name') }}</th>
                             <th class="wd-25p">{{ __('messages.Monthly Price') }}</th>
                             <th class="wd-25p">{{ __('messages.Yearly Price') }} </th>
                             <th class="wd-20p">{{ __('messages.Action') }}</th>
                         </tr>
                     </thead>
                     <tbody>
                         @foreach ($PlanInfo as $plan)
                             <tr id="plan-row-{{ $plan->id }}">
                                 <td>{{ $plan->name }}</td>
                                 <td>{{ $plan->monthly_price }}</td>
                                 <td>{{ $plan->yearly_price }}</td>
                                 <td>
                                     <div class="dropdown">
                                         <button class="btn btn-dark dropdown-toggle" type="button" id="dropdownMenuButton"
                                             data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                             <i data-feather="settings"></i>
                                         </button>
                                         <div class="dropdown-menu tx-14" aria-labelledby="dropdownMenuButton">
                                             <a class="dropdown-item edit-plan-btn" data-toggle="modal"
                                                 data-target="#editPlanModal"
                                                 data-route-edit="{{ route('admin.plan.edit', ['id' => $plan->id]) }}"
                                                 data-id="{{ $plan->id }}"
                                                 href="javascript:void(0)">{{ __('messages.Update') }}</a>
                                             <a class="dropdown-item delete-plan-btn"
                                                 data-route-delete="{{ route('admin.plan.delete', ['id' => $plan->id]) }}"
                                                 data-id="{{ $plan->id }}"
                                                 href="javascript:void(0)">{{ __('messages.Delete') }} </a>
                                         </div>
                                     </div>
                                 </td>
                             </tr>
                         @endforeach
                     </tbody>
                 </table>
             </div>
             <div class="d-flex justify-content-end mt-3">
                 {{ $PlanInfo->links('pagination::bootstrap-5') }}
             </div>
         </div><!-- component-section -->
     </div><!-- content-body -->
     </div><!-- content -->
     <div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered" style="max-width: 60%;">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="addPlanModalLabel">{{ __('messages.Add Plan') }} </h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true"><i data-feather="x"></i></span>
                     </button>
                 </div>
                 <form id="addPlanForm">
                     <div class="modal-body">
                         <div id="plan-error" class="alert alert-danger light alert-dismissible fade show"
                             style="display:none;"></div>
                         <div id="plan-success" class="alert alert-success light alert-dismissible fade show"
                             style="display:none;"></div>
                         <div class="mb-3">
                             <label for="name" class="form-label">{{ __('messages.Plan name') }} </label>
                             <input type="text" class="form-control" id="name" name="name"
                                 placeholder="{{ __('messages.Enter Plan Name') }}">
                         </div>
                         <div class="mb-3">
                             <label for="name" class="form-label">{{ __('messages.Plan name in Chinese') }} </label>
                             <input type="text" class="form-control" id="chinese_name" name="chinese_name"
                                 placeholder="{{ __('messages.Enter Plan Name') }}">
                         </div>
                         <div class="mb-3">
                             <label for="name" class="form-label">{{ __('messages.Plan name in Korean') }} </label>
                             <input type="text" class="form-control" id="koeran_name" name="korean_name"
                                 placeholder="{{ __('messages.Enter Plan Name') }}">
                         </div>
                         <div class="row">
                             <div class="col-sm-6">
                                 <h4>{{ __('messages.Monthly') }}</h4>
                                 <div class="mb-3">
                                     <label for="code" class="form-label">{{ __('messages.Price') }}</label>
                                     <input type="text" class="form-control" id="monthly_price" name="monthly_price"
                                         placeholder="{{ __('messages.Price') }}">
                                 </div>
                                 <div class="mb-3">
                                     <label for="code"
                                         class="form-label">{{ __('messages.Famillies [n] / Unlimited') }}</label>
                                     <input type="text" class="form-control" id="monthly_famillies"
                                         name="monthly_famillies" placeholder="{{ __('messages.[n] / Unlimited') }}">
                                 </div>
                                 <div class="mb-3">
                                     <label for="code"
                                         class="form-label">{{ __('messages.Members per family  [n] / Unlimited') }}
                                     </label>
                                     <input type="text" class="form-control" id="monthly_members" name="monthly_members"
                                         placeholder="{{ __('messages.[n] / Unlimited') }}">
                                 </div>
                                 <div class="mb-3">
                                     <!--<label for="code" class="form-label">{{ __('messages.Private Family  [n] / Unlimited') }}</label>-->
                                     <input type="hidden" class="form-control" id="monthly_private"
                                         name="monthly_private" placeholder="{{ __('messages.[n] / Unlimited') }}">
                                 </div>
                             </div>
                             <div class="col-sm-6">
                                 <h4>{{ __('messages.Yearly') }} </h4>
                                 <div class="mb-3">
                                     <label for="code" class="form-label">{{ __('messages.Price') }}</label>
                                     <input type="text" class="form-control" id="yearly_price" name="yearly_price"
                                         placeholder="{{ __('messages.Price') }}">
                                 </div>
                                 <div class="mb-3">
                                     <label for="code"
                                         class="form-label">{{ __('messages.Famillies [n] / Unlimited') }}</label>
                                     <input type="text" class="form-control" id="yearly_famillies"
                                         name="yearly_famillies" placeholder="{{ __('messages.[n] / Unlimited') }}">
                                 </div>
                                 <div class="mb-3">
                                     <label for="code"
                                         class="form-label">{{ __('messages.Members per family  [n] / Unlimited') }}</label>
                                     <input type="text" class="form-control" id="yearly_members"
                                         name="yearly_members" placeholder="{{ __('messages.[n] / Unlimited') }}">
                                 </div>
                                 <div class="mb-3">
                                     <!--<label for="code" class="form-label">{{ __('messages.Private Family  [n] / Unlimited') }}</label>-->
                                     <input type="hidden" class="form-control" id="yearly_private"
                                         name="yearly_private" placeholder="{{ __('messages.[n] / Unlimited') }}">
                                 </div>
                             </div>
                             <div class="col-sm-8 row">
                                 <div class="col-sm-6">
                                     <div class="mb-3">
                                         <div class="custom-control custom-switch">
                                             <input type="hidden" class="custom-control-input" id="pdfexport"
                                                 name="pdfexport" value="1">
                                             <!--<label class="custom-control-label" for="pdfexport">{{ __('messages.PDF Export') }} </label>-->
                                         </div>
                                     </div>
                                     <div class="mb-3">
                                         <div class="custom-control custom-switch">
                                             <input type="hidden" class="custom-control-input" id="heritatefamilies"
                                                 name="heritatefamilies" value="1">
                                             <!--<label class="custom-control-label" for="heritatefamilies">{{ __('messages.Heritate families') }} </label>-->
                                         </div>
                                     </div>
                                     <div class="mb-3">
                                         <div class="custom-control custom-switch">
                                             <input type="hidden" class="custom-control-input" id="support"
                                                 name="support" value="1">
                                             <!--<label class="custom-control-label" for="support">{{ __('messages.Support') }}</label>-->
                                         </div>
                                     </div>
                                 </div>
                                 <div class="col-sm-6">
                                     <div class="mb-3">
                                         <div class="custom-control custom-switch">
                                             <input type="hidden" class="custom-control-input" id="showads"
                                                 name="showads" value="1">
                                             <!--<label class="custom-control-label" for="showads">{{ __('messages.show ads') }}</label>-->
                                         </div>
                                     </div>
                                     <div class="mb-3">
                                         <div class="custom-control custom-switch">
                                             <input type="hidden" class="custom-control-input" id="createalbums"
                                                 name="createalbums" value="1">
                                             <!--<label class="custom-control-label" for="createalbums">{{ __('messages.Create albums') }} </label>-->
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" id="Resetbutton"
                             data-dismiss="modal">{{ __('messages.Close') }}</button>
                         <button type="submit" class="btn btn-primary"
                             id="savePlanBtn">{{ __('messages.Save') }}</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
     <div class="modal fade" id="editPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered" style="max-width: 60%;">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="PlanModalLabel">{{ __('messages.Edit Plan') }} </h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true"><i data-feather="x"></i></span>
                     </button>
                 </div>
                 <form id="editPlanForm">
                     <div id="edit_content"></div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-secondary" id="editResetbutton"
                             data-dismiss="modal">{{ __('messages.Close') }}</button>
                         <button type="submit" class="btn btn-primary"
                             id="editPlanBtn">{{ __('messages.Save') }}</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
     <div class="modal fade" id="deletePlanModal" tabindex="-1" role="dialog" aria-labelledby="deletePlanModalLabel"
         aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="updatePlanModalLabel">{{ __('messages.Delete Plan') }}</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true"><i data-feather="x"></i></span>
                     </button>
                 </div>
                 <div class="modal-body text-center">
                     <div id="delete-plan-success" class="alert alert-success light alert-dismissible fade show"
                         style="display:none;"></div>
                     <div id="delete-plan-error" class="alert alert-danger light alert-dismissible fade show"
                         style="display:none;"></div>
                     <form id="deletePlanForm">
                         <h4>{{ __('messages.deleteModalMsg') }}</h4>
                         <p class="mb-4">{{ __('messages.deleteModalMsg2') }}</p>
                         <button type="button" class="btn btn-secondary" id="cancel"
                             data-dismiss="modal">Close</button>
                         <button type="submit" class="btn btn-primary"
                             id="confirmDeleteBtn">{{ __('messages.Yes Delete it!') }}</button>
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
         });

         $(document).ready(function() {
             $('#addPlanForm').on('submit', function(e) {
                 e.preventDefault();
                 $('#plan-error').hide();
                 $('#savePlanBtn').prop('disabled', true);
                 $('#savePlanBtn').html(
                     '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                 );
                 $('#Resetbutton').hide();
                 $.ajax({
                     url: "{{ route('admin.plan.store') }}",
                     method: 'POST',
                     data: $(this).serialize(),
                     headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     },
                     success: function(response) {

                         $('#savePlanBtn').prop('disabled', false);
                         $('#savePlanBtn').html('Save');
                         $('#Resetbutton').show();
                         $('#plan-success').text(response.message).show();
                         setTimeout(function() {
                             $('#plan-success').fadeOut();
                             window.location.reload();
                         }, 2000);
                     },
                     error: function(xhr) {
                         $('#savePlanBtn').prop('disabled', false);
                         $('#savePlanBtn').html('Save');
                         $('#Resetbutton').show();

                         if (xhr.responseJSON && xhr.responseJSON.errors) {
                             var errors = xhr.responseJSON.errors;
                             var errorHtml = '<ul>';
                             $.each(errors, function(key, value) {
                                 errorHtml += '<li>' + value +
                                     '</li>'; // Append each error as list item
                             });
                             errorHtml += '</ul>';

                             // Display errors in a specific element with id="plan-error"
                             $('#plan-error').html(errorHtml).show();
                         } else if (xhr.responseJSON && xhr.responseJSON.message) {
                             // Display single error message
                             $('#plan-error').text(xhr.responseJSON.message).show();
                         } else {
                             $('#plan-error').text('An error occurred. Please try again.')
                                 .show();
                         }
                     }
                 });
             });
         });

         $('.delete-plan-btn').click(function() {
             var planId = $(this).data('id');
             var deleteRoute = $(this).data('route-delete');
             $('#confirmDeleteBtn').attr('data-id', planId);
             $('#confirmDeleteBtn').attr('data-route', deleteRoute);
             $('#deletePlanModal').modal('show');
         });

         $('#confirmDeleteBtn').click(function(e) {
             e.preventDefault();
             var planId = $(this).data('id');
             var deleteRoute = $(this).data('route');

             $('#delete-plan-error').hide();
             $('#confirmDeleteBtn').prop('disabled', true);
             $('#confirmDeleteBtn').html(
                 '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
             );
             $('#cancel').hide();

             $.ajax({
                 url: deleteRoute,
                 type: 'POST',
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
                 success: function(response) {
                     $('#delete-plan-success').text(response.message).show();
                     setTimeout(function() {
                         $('#confirmDeleteBtn').prop('disabled', false);
                         $('#confirmDeleteBtn').html('Yes Delete it!');
                         $('#cancel').show();
                         $('#delete-plan-success').fadeOut();
                         $('#deletePlanModal').modal('hide');
                         $('#plan-row-' + planId).remove();
                     }, 1000);
                 },
                 error: function(xhr) {
                     $('#confirmDeleteBtn').prop('disabled', false);
                     $('#confirmDeleteBtn').html('Yes Delete it!');
                     $('#cancel').show();
                     $('#delete-plan-error').text(response.message).show();
                 }
             });
         });

         $('.edit-plan-btn').click(function(e) {
             e.preventDefault();
             var planId = $(this).data('id');
             var editRoute = $(this).data('route-edit');

             $.ajax({
                 url: editRoute,
                 type: 'get',
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
                 success: function(response) {
                     $('#edit_content').html(response);
                 },
                 error: function(xhr) {

                 }
             });
         });

         $(document).ready(function() {
             $('#editPlanForm').on('submit', function(e) {
                 e.preventDefault();
                 $('#editplan-error').hide();
                 $('#editPlanBtn').prop('disabled', true);
                 $('#editPlanBtn').html(
                     '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                 );
                 $('#editResetbutton').hide();
                 $.ajax({
                     url: "{{ route('admin.plan.update') }}",
                     method: 'POST',
                     data: $(this).serialize(),
                     headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                     },
                     success: function(response) {

                         $('#editplan-success').text(response.message).show();
                         setTimeout(function() {
                             $('#editPlanBtn').prop('disabled', false);
                             $('#editPlanBtn').html('Save');
                             $('#editResetbutton').show();
                             $('#editplan-success').fadeOut();
                             window.location.reload();
                         }, 1000);
                     },
                     error: function(xhr) {
                         $('#editPlanBtn').prop('disabled', false);
                         $('#editPlanBtn').html('Save');
                         $('#editResetbutton').show();

                         if (xhr.responseJSON && xhr.responseJSON.errors) {
                             var errors = xhr.responseJSON.errors;
                             var errorHtml = '<ul>';
                             $.each(errors, function(key, value) {
                                 errorHtml += '<li>' + value +
                                     '</li>'; // Append each error as list item
                             });
                             errorHtml += '</ul>';

                             // Display errors in a specific element with id="plan-error"
                             $('#editplan-error').html(errorHtml).show();
                         } else if (xhr.responseJSON && xhr.responseJSON.message) {
                             // Display single error message
                             $('#editplan-error').text(xhr.responseJSON.message).show();
                         } else {
                             $('#editplan-error').text('An error occurred. Please try again.')
                                 .show();
                         }
                     }
                 });
             });
         });
     </script>
@endsection
