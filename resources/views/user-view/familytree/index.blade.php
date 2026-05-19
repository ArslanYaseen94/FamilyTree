@extends('layouts.user.app')

@section('content')
    <style>
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        .form-avatar {
            margin-top: 1em;
        }

        .form-avatar .form-inline {
            display: flex;
            flex-wrap: wrap;
            gap: 0em;
        }

        .form-avatar .form-group {
            margin: 0;
            position: relative;
            cursor: pointer;
            width: 61px;
            /* Adjust size as needed */
        }

        .form-avatar .form-group img {
            width: 100%;
            height: auto;
            border: 2px solid transparent;
            border-radius: 50%;
            transition: border-color 0.3s ease;
        }

        .form-avatar .form-group input[type="radio"] {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .form-avatar .form-group input[type="radio"]:checked+label img {
            border-color: #007bff;
            /* Change this color to highlight the selected image */
        }

        .form-avatar .form-group label {
            display: block;
            padding: 5px;
            border-radius: 50%;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }

        .form-avatar .form-group input[type="radio"]:checked+label {
            border-color: #007bff;
            /* Change this color to match the selected state */
        }

        .form-label.tt {
            display: flex;
            align-items: center;
            text-align: center;
            position: relative;
            margin: 1em 0;
        }

        .form-label.tt::before,
        .form-label.tt::after {
            content: '';
            flex: 1;
            border-top: 1px solid #ccc;
            /* Change this color and style as needed */
            margin: 0 1em;
        }


        #family-tree {
            position: relative;
            width: 90%;
            height: 75vh;
            /* Adjust as needed */
            overflow: hidden;
            border: 5px solid #0075ff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            border-radius: 15px;
            cursor: grab;
            margin-left: 3rem
        }

        #family-tree::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('/assets/front-end/images/tree.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.3;
            z-index: 0;
            border-radius: inherit;
        }

        #family-tree>* {
            position: relative;
            z-index: 1;
        }

        .node {
            cursor: pointer;
        }

        .link {
            fill: none;
            stroke: #555;
            stroke-width: 1.5px;
        }

        .node-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .member-card,
        .partner-card {
            border: 1px solid var(--theme-color) !important;
            height: 140px;
            width: 143px;
            margin: 5px;
        }

        .partner-card {
            border: 3px solid var(--theme-color) !important;
            height: 120px;
            width: 140px;
            border-radius: 70px;
            margin: 5px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }


        .avatar {
            width: 90%;
            height: 90%;
            border-radius: 50%;
            border: 3px solid var(--theme-color) !important;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }


        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .partner-card {
            height: 134px !important;
            width: 113px !important;
            border-radius: 50% !important;
        }

        /* Responsive Adjustments */
        @media (max-width: 1200px) {

            .member-card,
            .partner-card {
                height: 10vh;
                width: 10vw;
            }
        }

        @media (max-width: 992px) {

            .member-card,
            .partner-card {
                height: 8vh;
                width: 8vw;
            }
        }

        @media (max-width: 768px) {

            .member-card,
            .partner-card {
                height: 6vh;
                width: 6vw;
            }
        }

        @media (max-width: 576px) {

            .member-card,
            .partner-card {
                height: 5vh;
                width: 5vw;
            }

            #family-tree {
                height: 60vh;
            }
        }
    </style>
    <style>
    @media (max-width: 767.98px) {
        .table-responsive {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }
    }
</style>
    <div class="main-content right-chat-active">
        <div class="middle-sidebar-bottom">
            <div class="middle-sidebar-left pe-0">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card shadow-xss w-100 d-block d-flex border-0 p-4 mb-3">
                            <div class="card-body d-flex align-items-center p-0">
                                <h2 class="fw-700 mb-0 mt-0 font-md text-grey-900"> {{ __('messages.Family Tree List') }}</h2>
                                <div class="search-form-2 ms-auto">
                                    <a href="#"
                                        class="d-none d-lg-block bg-success p-3 z-index-1 rounded-3 text-white font-xsssss text-uppercase fw-700 ls-3"
                                        style="z-index: 1;" data-toggle="modal" data-target="#addnewtree">{{ __('messages.Create a Family') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="container mt-4">
                            <!-- Table -->
                            <div class="table-responsive">
                                <table id="familyTreeTable" class="table table-bordered table-striped align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            
                                            <th> {{ __('messages.Family ID') }}</th>
                                            <th> {{ __('messages.Name') }}</th>
                                            <th> {{ __('messages.Address') }}</th>
                                            <th> {{ __('messages.Role') }}</th>
                                            <th> {{ __('messages.Created At') }}</th>
                                            <th> {{ __('messages.Status') }}</th>
                                            <th> {{ __('messages.Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($FamilyTreeInfo as $FamilyTree)
                                            <tr>
                                                <td>
                                                    <a
                                                        href="{{ route('user.addmember', Crypt::encryptString($FamilyTree->family->id)) }}">
                                                        {{ $FamilyTree->family->familyid }}
                                                    </a>
                                                </td>
                                                <td>{{ $FamilyTree->firstname }} {{ $FamilyTree->lastname }}</td>
                                                <td>{{ $FamilyTree->village }}</td>
                                                <td>{{ $FamilyTree->role == 0 ? __('messages.Parent') : '' }}</td>

                                                <td>{{ date('d-m-Y', strtotime($FamilyTree->created_at)) }}</td>
                                                <td>
                                                    @if ($FamilyTree->is_active == 1)
                                                        <div class="badge badge-danger"> {{ __('messages.Deactivate') }}</div>
                                                    @else
                                                        <div class="badge badge-success"> {{ __('messages.Activate') }} </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <button class="btn btn-sm btn-outline-primary feather-edit"
                                                            data-id="{{ $FamilyTree->id }}"
                                                            data-name="{{ $FamilyTree->familyid }}">
                                                             {{ __('messages.Edit') }}
                                                        </button>

                                                        <button class="btn btn-sm btn-outline-danger"
                                                            data-toggle="modal"
                                                            data-target="#deleteConfirmationModal"
                                                            data-id="{{ $FamilyTree->family->id }}"
                                                            data-familyid="{{ $FamilyTree->family->familyid }}"
                                                            onclick="setDeleteId(this)">
                                                            {{ __('messages.Delete') }}
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- main content -->
@endsection

<div class="modal fade" id="addnewtree" tabindex="-1" role="dialog" aria-labelledby="addnewtreeLabel"
    aria-hidden="true" style="display:none">
    <div class="modal-dialog" role="document" style="max-width: 50%;">
        <div class="modal-content" style="margin-top:7rem !important">
            <div class="modal-header">
                <h5 class="fw-700 mb-0 mt-0 font-md text-grey-900" id="addnewtreeLabel"> {{ __('messages.Create FamilyTree') }}</h5>
            </div>
            <form id="firstMemberForm" action="{{ route('user.addfirstmember.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="self_id" id="self_id" value="0">
                <div class="modal-body">
                    <div class="">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                    data-bs-target="#home" type="button" role="tab" aria-controls="home"
                                    aria-selected="true"> {{ __('messages.Personal') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#profile"
                                    type="button" role="tab" aria-controls="profile"
                                    aria-selected="false"> {{ __('messages.Contact') }}</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="bio-tab" data-bs-toggle="tab" data-bs-target="#messages"
                                    type="button" role="tab" aria-controls="messages"
                                    aria-selected="false"> {{ __('messages.Biographical') }}</button>
                            </li>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <br>
                            <div role="tabpanel" class="tab-pane active" id="home">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label mont-font fw-600 font-xsss"> {{ __('messages.First Name:') }}</label>
                                        <input type="text" class="form-control" placeholder="{{ __('messages.Enter First Name') }}"
                                            name="firstname" required />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label mont-font fw-600 font-xsss"> {{ __('messages.Last Name:') }}</label>
                                        <input type="text" class="form-control" placeholder="{{ __('messages.Enter Last Name') }}"
                                            name="lastname" required />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label mont-font fw-600 font-xsss"> {{ __('messages.Email:') }}</label>
                                        <input type="email" class="form-control" placeholder="{{ __('messages.Enter Email') }}"
                                            name="email" required />
                                    </div>

                                    <div class="col-md-4 mt-2">
                                        <label class="form-label mont-font fw-600 font-xsss"> {{ __('messages.Family Id:') }}</label>
                                        <input type="text"
                                            class="form-control"
                                            placeholder="12345-1234567-1"
                                            name="family_id"
                                            id="family_id_cnic"
                                            inputmode="numeric"
                                            maxlength="15"
                                            pattern="^\d{5}-\d{7}-\d{1}$"
                                            required />
                                    </div>
                                    
                                </div><br>
                                <div class="row">
                                    <div class="col">
                                        <input class="choice" id="cb1" value="1" name="gender"
                                            type="radio" />
                                        <label class="tgl-btn mr-2" for="cb1"> {{ __('messages.Female') }}</label>
                                        <input class="choice" id="cb2" value="2" name="gender"
                                            type="radio" />
                                        <label class="tgl-btn" for="cb2"> {{ __('messages.Male') }}</label>
                                    </div>
                                    <div class="col">
                                        <div class="form-group mb-0">
                                            <input class="tgl tgl-light" id="cbs1" value="1"
                                                name="death" type="checkbox" checked />
                                            <label class="tgl-btn mt-3" for="cbs1"></label>
                                            <label class="form-label"> {{ __('messages.This person is alive') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label"> {{ __('messages.Village:') }}</label>
                                    <input type="text" name="village" placeholder="{{ __('messages.Enter Village') }}"
                                        class="form-control datepicker-here" />
                                </div><br>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <label class="form-label"> {{ __('messages.Birth Date:') }}</label>
                                        <input type="date" name="birthdate" 
                                            class="form-control datepicker-here" data-position='top left' required />
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label"> {{ __('messages.Marriage Date:') }}</label>
                                        <input type="date" name="marriagedate" 
                                            class="form-control datepicker-here" data-position='top left' />
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-label"> {{ __('messages.Death Date:') }}</label>
                                        <input type="date" name="deathdate"
                                            class="form-control datepicker-here" data-position='top left' />
                                    </div>
                                </div><br>
                                <label class="form-label"> {{ __('messages.Link this member to a user:') }}</label>
                                <input type="text" class="form-control" name="user" />
                                
                                <div class="form-avatar">
                                   
                                    <div class="form-inline">
                                        @for ($i = 1; $i <= 18; $i++)
                                            <div class="form-group">
                                                <input type="radio" name="avatar" value="{{ $i }}"
                                                    id="sradioe{{ $i }}" class="choice image">
                                                <label for="sradioe{{ $i }}"><b><img
                                                            src="{{ asset('assets/front-end/avatar/' . $i . '.jpg') }}" /></b></label>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="profile">
                                <div class="row mt-2">
                                    <div class="col">
                                        <label class="form-label"> {{ __('messages.Facebook:') }}</label>
                                        <input type="text" class="form-control"  placeholder="{{ __('messages.Enter Facebook') }}"
                                            name="facebook" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label"> {{ __('messages.Twitter:') }}</label>
                                        <input type="text" class="form-control" placeholder="{{ __('messages.Enter Twitter') }}"
                                            name="twitter" />
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col">
                                        <label class="form-label"> {{ __('messages.Instagram:') }}</label>
                                        <input type="text" class="form-control" placeholder="{{ __('messages.Enter Instagram') }}"
                                            name="instagram" />
                                    </div>

                                </div>
                                <div class="row mt-2">
                                    <div class="col">
                                        <label class="form-label"> {{ __('messages.Home Tel:') }}</label>
                                        <input type="text" class="form-control" placeholder="{{ __('messages.Enter Home Tel') }}"
                                            name="tel" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label"> {{ __('messages.Mobile:') }}</label>
                                        <input type="text" class="form-control" placeholder="{{ __('messages.Enter Mobile') }}"
                                            name="mobile" />
                                    </div>
                                </div>
                                <label class="form-label mt-2"> {{ __('messages.Website:') }}</label>
                                <input type="text" class="form-control" placeholder="{{ __('messages.Enter Website') }}"
                                    name="site" />
                            </div>
                            <div role="tabpanel" class="tab-pane" id="messages">
                                <div class="row mt-2">
                                    <div class="col">
                                        <label class="form-label"> {{ __('messages.Birth Place:') }}</label>
                                        <input class="form-control" type="text" placeholder="{{ __('messages.Enter Birth Place') }}"
                                            name="birthplace" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label"> {{ __('messages.Death Place:') }}</label>
                                        <input class="form-control" type="text" placeholder="{{ __('messages.Enter Death Place') }}"
                                            name="deathplace" />
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col">
                                        <label class="form-label"> {{ __('messages.Home Town:') }}</label>
                                        <input class="form-control" type="text" placeholder="{{ __('messages.Enter Home Town') }}"
                                            name="home_town" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label"> {{ __('messages.School:') }}</label>
                                        <input class="form-control" type="text" placeholder="{{ __('messages.Enter School') }}"
                                            name="school" />
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col">
                                        <label class="form-label"> {{ __('messages.Background:') }}</label>
                                        <input class="form-control" type="text"
                                            placeholder="{{ __("messages.Enter Background Details") }}" name="background_details" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label"> {{ __('messages.Business Info:') }}</label>
                                        <input class="form-control" type="text" placeholder="{{ __('messages.Enter Business Info') }}"
                                            name="business_info" />
                                    </div>
                                </div>
                                <label class="form-label mt-2"> {{ __('messages.Profession:') }}</label>
                                <textarea class="form-control" name="profession" style="min-height: calc(2.5em + 0.75rem + 50px);"
                                    placeholder="{{ __("messages.Enter Profession") }}"></textarea>
                                <label class="form-label mt-2"> {{ __('messages.Company:') }}</label>
                                <textarea class="form-control" name="company" style="min-height: calc(2.5em + 0.75rem + 50px);"
                                    placeholder="{{ __("messages.Enter Company") }}"></textarea>
                                <label class="form-label mt-2">{{ __('messages.Interests:') }}</label>
                                <textarea class="form-control" name="interests" style="min-height: calc(2.5em + 0.75rem + 50px);"
                                    placeholder="{{ __("messages.Enter Interests") }}"></textarea>
                                <label class="form-label mt-2"> {{ __('messages.Bio Notes:') }}</label>
                                <textarea class="form-control" name="bio" style="min-height: calc(2.5em + 0.75rem + 50px);"
                                    placeholder="{{ __("messages.Enter Bio Notes") }}"></textarea>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="pics">
                                <input id="images" name="images[]" type="file" multiple>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" style="border: none"
                        class="bg-current text-center text-white font-xsss fw-600 p-3 w175 rounded-3 d-inline-block"> {{ __('messages.Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog"
    aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true" style="display:none">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="fw-700 mb-0 mt-0 font-md text-grey-900" id="deleteConfirmationModalLabel">Delete FamilyTree
                </h5>
            </div>
            <div class="modal-body">
                <p>
                    Are you sure you want to delete this family tree
                    <strong id="deleteFamilyLabel"></strong>?
                </p>
            </div>
            <div class="modal-footer">

                <button type="button" id="confirmDelete" class="btn btn-danger" onclick="confirmDeleteFamilyTree()">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Keep this script NEXT to the modal so it always loads (even if the layout
    // doesn't render @section('scripts') / @stack('scripts') on this page).
    window.deleteId = window.deleteId || '';

    function setDeleteId(element) {
        window.deleteId = $(element).data('id') || '';
        const familyLabel = $(element).data('familyid') || '';
        $('#deleteFamilyLabel').text(familyLabel ? `(${familyLabel})` : '');
    }

    function confirmDeleteFamilyTree() {
        if (!window.deleteId) {
            alert('Missing family id. Please close and try again.');
            return;
        }

        $('#confirmDelete').prop('disabled', true).text('Deleting...');

        $.ajax({
            url: "{{ route('user.familytreedelete') }}",
            method: 'POST',
            data: {
                id: window.deleteId,
                _token: '{{ csrf_token() }}'
            },
            success: function() {
                $('#deleteConfirmationModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                let msg = 'An error occurred while deleting the family tree. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                alert(msg);
                $('#confirmDelete').prop('disabled', false).text('Delete');
            }
        });
    }
</script>

@section('scripts')
    <script>
        // Move the setDeleteId function to the top
        // NOTE: setDeleteId/confirmDeleteFamilyTree are defined above (outside sections)
        // so they always exist. Keep the rest of this section for page-specific handlers.

        function editfamilytree(element) {
            var id = $(element).data('id');
            var name = $(element).data('name');

            $('#editid').val(id);
            $('#editfamilyid').val(name);
        }

        $(document).ready(function() {
            $('#editfamilytree').on('submit', function(e) {
                e.preventDefault();
                $('#editform-error').hide();
                $('#editSave').prop('disabled', true);
                $('#editSave').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );
                $.ajax({
                    url: "{{ route('user.familytreeupdate') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#editform-success').text(response.message).show();
                        setTimeout(function() {
                            $('#editSave').prop('disabled', false);
                            $('#editSave').html('Save');
                            $('#editform-success').fadeOut();
                            window.location.reload();
                        }, 1000);
                    },
                    error: function(xhr) {
                        $('#editSave').prop('disabled', false);
                        $('#editSave').html('Save');

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            var errorHtml = '<ul>';
                            $.each(errors, function(key, value) {
                                errorHtml += '<li>' + value +
                                    '</li>'; // Append each error as list item
                            });
                            errorHtml += '</ul>';

                            // Display errors in a specific element with id="login-error"
                            $('#editform-error').html(errorHtml).show();
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            // Display single error message
                            $('#editform-error').text(xhr.responseJSON.message).show();
                        } else {
                            $('#editform-error').text('An error occurred. Please try again.')
                                .show();
                        }
                    }
                });
            });
        });
    </script>
     <script>
    $(document).ready(function() {
        $('#familyTreeTable').DataTable({
            responsive: true,
            scrollX: true,
            paging: false,
            ordering: false,
            info: false
        });
    });
</script>
    <script>

        $('body').on('click', '.feather-edit', function() {
            let memberId = $(this).data('id');
            $.ajax({
                url: `/user/modal/edit/${memberId}`,
                method: 'GET',
                success: function(response) {
                    $('body').append(response);
                    $('#editMemberForm').attr('action', `/user/members/${memberId}`);
                    $('#editMemberModal').modal('show');
                },
                error: function() {
                    alert('An error occurred while fetching the modal.');
                }
            });
        });

        $('#firstMemberForm').on('submit', function(e) {
            if (!window.validateMemberLifeDates(this)) {
                e.preventDefault();
            }
        });

        $('#addnewtree').on('shown.bs.modal', function() {
            const form = document.getElementById('firstMemberForm');
            if (form) window.toggleMemberDeathDateField(form);
        });
    </script>
    @include('partials.member-life-date-validation')
@endsection

@push('scripts')
<script>
    // CNIC input formatter: 12345-1234567-1
    (function() {
        const el = document.getElementById('family_id_cnic');
        if (!el) return;

        function formatCNIC(value) {
            const digits = (value || '').replace(/\D/g, '').slice(0, 13);
            const p1 = digits.slice(0, 5);
            const p2 = digits.slice(5, 12);
            const p3 = digits.slice(12, 13);
            let out = p1;
            if (p2.length) out += '-' + p2;
            if (p3.length) out += '-' + p3;
            return out;
        }

        el.addEventListener('input', () => {
            const start = el.selectionStart;
            const before = el.value;
            el.value = formatCNIC(el.value);
            // Best-effort cursor preservation
            const delta = el.value.length - before.length;
            if (typeof start === 'number') el.setSelectionRange(start + delta, start + delta);
        });
    })();
</script>
@endpush
