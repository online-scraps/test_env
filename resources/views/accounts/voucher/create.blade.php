@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
        trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
        $crud->entity_name_plural => url($crud->route),
        trans('backpack::crud.add') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <section class="main-container">
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
            <small>{!! $crud->getSubheading() ?? trans('backpack::crud.add').' '.$crud->entity_name !!}.</small>

            @if ($crud->hasAccess('list'))
            <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
            @endif
        </h2>
    </section>
@endsection

@push('after_styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/nepali.datepicker.v2.2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.fancybox.min.css') }}">
    <style>
        .table>:not(:first-child) {
            border-top: 0px !important;
        }
    </style>
@endpush

@section('content')
    <form action="{{ url($crud->route) }}" role="form" method="POST" id="journalVoucherForm" enctype="multipart/form-data">
        @csrf
        @include('accounts.partial.sup_org_field')
        <div class="row">
            <!-- <div class="col-md-4">
                <div class="input-group mb-3">
                    <span class="input-group-text">Auto No.</span>
                    <input type="text" class="form-control" id="auto_no" name="auto_no" placeholder="Auto No." required>
                </div>
            </div> -->
            <div class="col-md-4">
                <div class="input-group mb-3">
                    <span class="input-group-text">Voucher Series</span>
                    <select class="form-control" name="series_no_id" id="series_no_id">
                        <option disabled selected value="">-</option>
                        @foreach($series_numbers as $no)
                            <option value="{{ $no->id }}">{{ $no->description }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="input-group mb-3">
                    <span class="input-group-text">Voucher No.</span>
                    <input type="text" class="form-control" id="voucher_no" name="voucher_no" placeholder="Voucher No." readonly>
                </div>
            </div>

            <div class="col-md-4">
                <div class="input-group mb-3">
                    <span class="input-group-text">Voucher Date(B.S.)</span>
                    <input type="text" class="form-control" id="voucher_date_bs" name="voucher_date_bs" value="{{ $today_date }}" placeholder="Voucher Date">
                </div>
            </div>

            <div class="col-md-4">
                <div class="input-group mb-3">
                    <span class="input-group-text">Voucher Date(A.D.)</span>
                    <input type="date" class="form-control" id="voucher_date_ad" name="voucher_date_ad" placeholder="Voucher Date">
                </div>
            </div>

            <!-- <div class="col-md-4">
                <div class="input-group mb-3">
                    <span class="input-group-text">Cheque No.</span>
                    <input type="text" class="form-control" id="cheque_no" name="cheque_no" placeholder="Cheque No.">
                </div>
            </div>

            <div class="col-md-8">
                <div class="input-group mb-3">
                    <span class="input-group-text">Pay To</span>
                    <input type="text" class="form-control" id="pay_to" name="pay_to" placeholder="Pay To">
                </div>
            </div> -->
        </div>

        <div class="table-responsive">
            <table class="table" id="journal-voucher-table">
                <thead>
                    <tr class="text-white" style="background-color: #192840">
                        <th scope="col" style="white-space; nowrap;width:5%">Dr/Cr</th>
                        <th scope="col" style="white-space: nowrap;width:25%">Acounts Details</th>
                        <!-- <th scope="col" style="white-space: nowrap;width:20%">Sub Account</th> -->
                        <th scope="col" style="white-space: nowrap;width:20%">Dr. Amount</th>
                        <th scope="col" style="white-space: nowrap;width:20%">Cr. Amount</th>
                        <th scope="col" style="white-space: nowrap;width:20%">Short Narration</th>
                        <th scope="col" style="white-space: nowrap;width:10%">Action</th>
                    </tr>
                </thead>
                
                <tbody id="journal-voucher-body">
                    <tr class="item-row" id="item-row-1" tr-id="1">
                        <td>
                            <div class="input-group">
                                <select class="form-control p-1 dr_cr" id="dr_cr-1" tr-id="1" name="dr_cr[1]" disabled>
                                    <option value="1">Dr.</option>
                                    <option value="0">Cr.</option>
                                </select>
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control p-1 general_ledger" id="general_ledger-1" tr-id="1" name="general_ledger_id[1]" placeholder="Search Ledger">
                                <input type="hidden" name="general_ledger_hidden[1]" class="general_ledger_hidden">
                            </div>
                        </td>

                        <!-- <td>
                            <div class="input-group">
                                <input type="text" class="form-control p-1 sub_ledger" id="sub_ledger-1" tr-id="1" name="sub_ledger_id[1]" placeholder="Search Sub Ledger">
                                <input type="hidden" name="sub_ledger_hidden[1]" class="sub_ledger_hidden">
                            </div>
                        </td> -->

                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 dr_amount" id="dr_amount-1" tr-id="1" name="dr_amount[1]" placeholder="Dr. Amount" size="1">
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 cr_amount" id="cr_amount-1" tr-id="1" name="cr_amount[1]" placeholder="Cr. Amount" size="1">
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <textarea class="form-control p-1 remarks" id="remarks-1" name="remarks[1]" rows="1" placeholder="Remarks"></textarea>
                            </div>
                        </td>

                        <td>
                            <i class="fa fa-plus p-1 fireRepeaterClick" aria-hidden="true"></i>
                            <i class="fa fa-trash p-1 destroyRepeater d-none" tr-id="1" id="itemDestroyer-1" aria-hidden="true"></i>
                        </td>
                    </tr>

                    <!-- repeater row -->
                    <tr class="item-row d-none" id="repeater">
                        <td>
                            <div class="input-group">
                                <select class="form-control p-1 dr_cr">
                                    <option value="1">Dr.</option>
                                    <option value="0">Cr.</option>
                                </select>
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control p-1 general_ledger" placeholder="Search Ledger">
                                <input type="hidden" class="general_ledger_hidden">
                            </div>
                        </td>

                        <!-- <td>
                            <div class="input-group">
                                <input type="text" class="form-control p-1 sub_ledger" placeholder="Search Sub Ledger">
                                <input type="hidden" class="sub_ledger_hidden">
                            </div>
                        </td> -->

                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 dr_amount" placeholder="Dr. Amount" size="1">
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 cr_amount" placeholder="Cr. Amount" size="1">
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <textarea class="form-control p-1 remarks" placeholder="Remarks" rows="1" size="1"></textarea>
                            </div>
                        </td>

                        <td>
                            <i class="fa fa-plus p-1 fireRepeaterClick" aria-hidden="true"></i>
                            <i class="fa fa-trash p-1 destroyRepeater" aria-hidden="true"></i>
                        </td>
                    </tr>
                    <!-- end of repeater row -->
                </tbody>
                <tr>
                    <td colspan="2" class="text-right font-weight-bold">Total</td>
                    <td><span class="total_dr_amount font-weight-bold"></span><input type="number" class="form-control d-none" id="total_dr_amount" name="total_dr_amount" readonly></td>
                    <td><span class="total_cr_amount font-weight-bold"></span><input type="number" class="form-control d-none" id="total_cr_amount" name="total_cr_amount" readonly></td>
                </tr>
            </table>
        </div>

        <div class="main-container">
            <div class="row">
                {{--<div class="col-md-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Total Dr. Amt</span>
                        <input type="text" class="form-control" id="total_dr_amount" name="total_dr_amount" readonly>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Total Cr. Amt</span>
                        <input type="text" class="form-control" id="total_cr_amount" name="total_cr_amount" readonly>
                    </div>
                </div>--}}

                <div class="col-md-12">
                    <div class="input-group mb-3">
                        <span class="input-group-text">In Words</span>
                        <input type="text" class="form-control" id="in_word" name="in_word" readonly>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Long Narration</span>
                        <textarea class="form-control" name="narration" placeholder="Remarks" rows="3"></textarea>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="voucher_image">
            <div class="row">
                @if($account_setting)
                    @if($account_setting->maintain_image_note)
                        @php 
                            if($maintain_image_note->image_with_account_master){
                                $col = '8';
                            }else{
                                $col = '12';
                            }
                        @endphp

                        <!-- account master image  -->
                        @if($maintain_image_note->image_with_account_master)
                            <div class="col-4 from-group mb-3">
                                <label for="image_with_account_master" class="font-weight-bold">Account Image</label>
                                <br>
                                <a href="" id="image_with_account_master_fancy" data-fancybox data-caption="" class="d-none">
                                    <img src="" id="image_with_account_master_preview" height='70px' width='70px'>
                                </a>
                                <button class="btn btn-light btn-sm remove_account_master_image d-none" data-handle="remove" type="button"><i class="fa fa-trash"></i></button>
                                <input type="file" class="from-control" name="image_with_account_master" id="image_with_account_master" accept="image/*" />
                            </div>
                        @endif
                        <!-- account master note -->
                        @if($maintain_image_note->note_with_account_master)
                            <div class="col-{{$col}} form-group mb-3">
                                <label for="note_with_account_master" class="font-weight-bold">Account Note</label>
                                <textarea name="note_with_account_master" id="note_with_account_master" class="w-100 form-control" rows="3"></textarea>
                            </div>
                        @endif
                    @endif

                    @if($account_setting->maintain_image_note)
                        @php 
                            if($maintain_image_note->image_with_account_voucher){
                                $col = '8';
                            }else{
                                $col = '12';
                            }
                        @endphp

                        <!-- account voucher image -->
                        @if($maintain_image_note->image_with_account_voucher)
                            <div class="col-4 form-group mb-3">
                                <label for="image_with_account_voucher" class="font-weight-bold">Voucher Image</label>
                                <br>
                                <a href="" id="image_with_account_voucher_fancy" data-fancybox data-caption="" class="d-none">
                                    <img src="" id="image_with_account_voucher_preview" height='70px' width='70px'>
                                </a>
                                <button class="btn btn-light btn-sm remove_account_voucher_image d-none" data-handle="remove" type="button"><i class="fa fa-trash"></i></button>
                                <input type="file" class="from-control" name="image_with_account_voucher" id="image_with_account_voucher" accept="image/*" />
                            </div>
                        @endif
                        <!-- account voucher note -->
                        @if($maintain_image_note->note_with_account_voucher)
                            <div class="col-{{$col}} form-group mb-3">
                                <label for="note_with_account_voucher" class="font-weight-bold">Voucher Note</label>
                                <textarea name="note_with_account_voucher" id="note_with_account_voucher" class="w-100 form-control" rows="3"></textarea>
                            </div>
                        @endif
                    @endif
                @endif
            </div>
        </div>

        <div class="main-container mb-4">
            <div class="row">
                <div class="col d-flex justify-content-end">
                    <button disabled type="submit" id="saveBtn" class="btn btn-primary me-1"><i class="la la-save"> Save</i></button>
                    <a href="{{url($crud->route)}}"><i  class="btn btn-danger me-1"><i class="la la-close"> Cancel</i></i></a>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('after_scripts')
    @include('accounts.voucher.partials.scripts')
@endsection