@extends('layouts.user.app')

@section('content')
    <style>
        @media (min-width: 1200px) {
            .pricing-header {
                max-width: 100% !important;
            }
        }

        .pricing-features li {
            text-align: center
        }
    </style>
    <div class="main-content right-chat-active">
        <div class="middle-sidebar-bottom">
            <div class="middle-sidebar-left pe-0">
                <div class="container py-5">
                    <div class="card p-4 shadow-sm">
                        <h4 class="fw-700 mb-3">{{ __('messages.Import Members CSV') }}</h4>

                        <div class="alert alert-info">
                            <i class="feather-info"></i>
                            {{ __('messages.Only CSV files are accepted. Please download the sample template, fill in your data, and upload.') }}
                        </div>

                        <div class="mb-3">
                            <a href="{{ route('import.sample.csv') }}" class="btn btn-success">
                                <i class="feather-download"></i> {{ __('messages.Download Sample CSV Template') }}
                            </a>
                        </div>

                        <form action="{{ route('members.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                            @csrf
                            <div class="mb-3">
                                <label for="family_id" class="form-label fw-600">{{ __('messages.Select Family Tree') }}</label>
                                <select name="family_id" id="family_id" class="form-control" required>
                                    <option value="">-- {{ __('messages.Choose Family Tree') }} --</option>
                                    @foreach($families as $family)
                                        <option value="{{ $family->id }}">{{ $family->familyid }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">{{ __('messages.Members will be imported into the selected family tree.') }}</small>
                            </div>
                            <div class="mb-3">
                                <label for="excel_file" class="form-label fw-600">{{ __('messages.Select CSV File') }}</label>
                                <input type="file" name="excel_file" id="excel_file" class="form-control" required accept=".csv">
                                <small class="text-muted">{{ __('messages.Accepted format: .csv only') }}</small>
                            </div>
                            <button type="submit" class="btn btn-primary" id="importBtn">
                                <i class="feather-upload"></i> {{ __('messages.Import') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.getElementById('importForm').addEventListener('submit', function(e) {
        var familySelect = document.getElementById('family_id');
        if (!familySelect.value) {
            e.preventDefault();
            toastr.error('{{ __("messages.Please select a family tree.") }}');
            return;
        }

        var fileInput = document.getElementById('excel_file');
        var file = fileInput.files[0];

        if (!file) {
            e.preventDefault();
            toastr.error('{{ __("messages.Please select a file.") }}');
            return;
        }

        var extension = file.name.split('.').pop().toLowerCase();
        if (extension !== 'csv') {
            e.preventDefault();
            toastr.error('{{ __("messages.Only CSV files are allowed.") }}');
            fileInput.value = '';
            return;
        }

        document.getElementById('importBtn').disabled = true;
        document.getElementById('importBtn').innerHTML = '<span class="spinner-border spinner-border-sm"></span> {{ __("messages.Importing...") }}';
    });
</script>
@endsection
