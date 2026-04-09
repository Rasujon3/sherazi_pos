@extends('admin_master')
@section('content')

    <div class="content-wrapper">
        <section class="content">
            <div class="card card-info">
                <div class="card-header">
                    <h3>Edit Services for {{ $staff->name }}</h3>
                </div>

                <form action="{{ route('staffs.update.services', $staff->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body" id="service-wrapper">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="branch_id">Branch <span class="required">*</span></label>
                                    <select class="form-control select2bs4" name="branch_id" id="branch_id" required="">
                                        <option value="" selected="" disabled="">Select One</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" @if($staff->branch_id == $branch->id) selected @endif>{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="specialty_id">Specialty <span class="required">*</span></label>
                                    <select class="form-control select2bs4" name="specialty_id" id="specialty_id" required="">
                                        <option value="" selected="" disabled="">Select One</option>
                                        @foreach($specialities as $speciality)
                                            <option
                                                value="{{ $speciality->id }}"
                                                @if($staff->specialty_id == $speciality->id) selected @endif
                                            >
                                                {{ $speciality->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('specialty_id')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="experience_id">Experience <span class="required">*</span></label>
                                    <select class="form-control select2bs4" name="experience_id" id="experience_id" required="">
                                        <option value="" selected="" disabled="">Select One</option>
                                        @foreach($experiences as $experience)
                                            <option
                                                value="{{ $experience->id }}"
                                                @if($staff->experience_id == $experience->id) selected @endif
                                            >
                                                {{ $experience->year_of_exp . ' Years' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('experience_id')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="working_day_id">Working Days <span class="required">*</span></label>
                                    <select class="form-control select2bs4"
                                            name="working_day_ids[]"
                                            id="working_day_id"
                                            multiple
                                            required>
                                        @foreach($workingDays as $workingDay)
                                            <option
                                                value="{{ $workingDay->id }}"
                                                @if($staff->workingDays->pluck('id')->contains($workingDay->id))
                                                    selected
                                                @endif
                                            >
                                                {{ $workingDay->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('working_day_id')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="working_time_range_id">Time (From To) <span class="required">*</span></label>
                                    <select class="form-control select2bs4" name="working_time_range_id" id="working_time_range_id" required="">
                                        <option value="" selected="" disabled="">Select One</option>
                                        @foreach($workingTimeRanges as $workingTimeRange)
                                            <option
                                                value="{{ $workingTimeRange->id }}"
                                                @if($staff->working_time_range_id == $workingTimeRange->id) selected @endif
                                            >
                                                {{ timeFormat($workingTimeRange->from_time) . ' to ' . timeFormat($workingTimeRange->to_time) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('working_time_range_id')
                                    <span class="alert alert-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- প্রথম row (এটি সবসময় থাকবে + button সহ) -->
                        <div id="service-wrapper">
                            <div class="row mb-3">
                                <h3>Services</h3>
                                <button type="button" class="btn btn-success add-row ml-2">
                                    <i class="fas fa-plus"></i> Add Service
                                </button>
                            </div>

                            @foreach($staff->services as $index => $ss)
                                <div class="row service-row mb-3">

                                    <div class="col-md-4">
                                        <select name="service_id[]" class="form-control select2bs4" required>
                                            <option value="">Select service</option>
                                            @foreach($services as $service)
                                                <option value="{{ $service->id }}"
                                                        @if($service->id == $ss->service_id) selected @endif>
                                                    {{ $service->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <select name="duration_id[]" class="form-control select2bs4" required>
                                            <option value="">Select duration</option>
                                            @foreach($durations as $duration)
                                                <option value="{{ $duration->id }}"
                                                        @if($duration->id == $ss->duration_id) selected @endif>
                                                    {{ $duration->time_duration }} {{ $duration->time_unit }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <input type="number" step="1"
                                               name="price[]"
                                               class="form-control"
                                               value="{{ $ss->price }}"
                                               required>
                                    </div>

                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger remove-row">
                                            <i class="fas fa-minus"></i> Remove
                                        </button>
                                    </div>

                                </div>
                            @endforeach

                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Services
                        </button>
                        <a href="{{ route('staffs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>

                </form>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {

                // Initialize Select2 for first row
                initSelect2();

                // Add row
                $(document).on('click', '.add-row', function() {
                    addNewRow();
                });

                // Remove row
                $(document).on('click', '.remove-row', function() {
                    removeRow($(this));
                });

                // Check duplicate services
                $(document).on('change', 'select[name="service_id[]"]', function() {
                    checkDuplicateServices();
                });

                // Initialize Select2
                function initSelect2() {
                    $('.select2bs4').select2({
                        theme: 'bootstrap4',
                        placeholder: function() {
                            return $(this).data('placeholder') || 'Select an option';
                        },
                        allowClear: true
                    });
                }

                // Add new row
                function addNewRow() {
                    let $firstRow = $('.service-row:first');

                    // Clone করার আগে Select2 destroy
                    $firstRow.find('.select2bs4').select2('destroy');

                    // Clone করুন
                    let $newRow = $firstRow.clone();

                    // Values reset করুন
                    $newRow.find('input').val('');
                    $newRow.find('select').val('').prop('selectedIndex', 0);

                    // Select2 artifacts remove করুন
                    $newRow.find('.select2-container').remove();

                    // Button change করুন
                    $newRow.find('.col-md-2').html(`
                <button type="button" class="btn btn-danger remove-row">
                    <i class="fas fa-minus"></i> Remove
                </button>
            `);

                    // Append করুন
                    $('#service-wrapper').append($newRow);

                    // Re-initialize Select2 for all rows
                    initSelect2();

                    // Animation
                    $newRow.hide().fadeIn(200);
                }

                // Remove row
                function removeRow($button) {
                    let $rows = $('.service-row');

                    if ($rows.length <= 1) {
                        alert('At least one service must be added!');
                        return;
                    }

                    if (confirm('Are you sure you want to remove this service?')) {
                        let $row = $button.closest('.service-row');

                        // Select2 destroy
                        $row.find('.select2bs4').select2('destroy');

                        // Remove with animation
                        $row.fadeOut(200, function() {
                            $(this).remove();
                        });
                    }
                }

                // Check for duplicate services
                function checkDuplicateServices() {
                    let selectedServices = [];
                    let hasDuplicate = false;

                    $('select[name="service_id[]"]').each(function() {
                        let value = $(this).val();

                        if (value) {
                            if (selectedServices.includes(value)) {
                                alert('This service is already selected!');
                                $(this).val('').trigger('change');
                                hasDuplicate = true;
                                return false; // Break loop
                            }
                            selectedServices.push(value);
                        }
                    });
                }
            });
        </script>
    @endpush
    <style>
        .service-row {
            margin-bottom: 15px;
        }

        .service-row:last-child {
            margin-bottom: 0;
        }
    </style>

@endsection
