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
	<section class="container-fluid">
        <h2>
        <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
        <small>{!! $crud->getSubheading() ?? trans('backpack::crud.add').' '.$crud->entity_name !!}.</small>

        @if ($crud->hasAccess('list'))
            <small class="back-btn"><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
        @endif
        </h2>
	</section>
@endsection

@section('after_styles')
    <style>
        label{
            font-weight: bold;
        }
        legend{
            font-size: larger;
        }
        .left{
            float: left;
        }
        .right{
            position: absolute;
            right: 15px !important;
        }
        .width-50{
            width: 50% !important;
        }
        .width-55{
            width: 55% !important;
        }
        .width-60{
            width: 60% !important;
        }
        .width-64{
            width: 64% !important;
        }
        .width-70{
            width: 70% !important;
        }
        .form-group label.error{
            min-width: 100% !important;
            font-size: 0.8rem;
            color: red;
            display: block;
        }
        .form-group input.error, .form-group select.error{
            border-color: rgba(255, 0, 0, 0.26);
            box-shadow: 0 0 0 0.25rem rgba(255, 0, 0, 0.25);
        }
        .group span.select2{
            width: 65% !important;
            position: absolute;
            right: 1% !important;
        }
        .ledger_id span.select2{
            width: 70% !important;
            position: absolute;
            right: 15px !important;
        }
        span.select2 span.selection .error{
            border-color: rgba(255, 0, 0, 0.26);
            box-shadow: 0 0 0 0.25rem rgba(255, 0, 0, 0.25);
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ url($crud->route) }}" method="POST" id="chartsOfAccountForm">
                @csrf
                @include('accounts.partial.sup_org_field')

                <input type="hidden" name="group" value="0" id="groupValue">
                <div class="row">
                    <div class="col-6">
                        <fieldset class="border p-2 pb-3 mb-2">
                            <legend class="font-weight-bold">General Information</legend>

                            <div class="row form-inline">
                                <div class="form-group col-12 mb-3">
                                    <label class="left" for="coa_name">Name<span class="text-danger">&nbsp;*</span></label>
                                    <input type="text" class="form-control right width-70" name="name" id="coa_name">
                                </div>

                                <div class="form-group col-12 mb-3">
                                    <label class="left" for="alias">Alias</label>
                                    <input type="text" class="form-control right width-70" name="alias" id="alias">
                                </div>    

                                <div class="form-group col-12 mb-3">
                                    <label class="left" for="print_name">Print Name</label>
                                    <input type="text" class="form-control right width-70" name="print_name" id="print_name">
                                </div>    

                                @if(isset($account_setting))
                                    @if($account_setting->maintain_sub_ledgers)
                                        <div class="form-group col-12 mb-3">
                                            <label class="left" for="ledger_type">Ledger Type</label>
                                            <select name="ledger_type" id="ledger_type" class="form-control right width-70">
                                                <option class="form-control" value="1">General Ledger</option>
                                                <option class="form-control" value="2">Sub Ledger</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-12 mb-3 ledger_id">
                                            <label for="ledger_id" class="left">Ledger<span class="text-danger">&nbsp;*</span></label>
                                            <select name="ledger_id" id="ledger_id" class="form-control right widht-70">
                                                <option value="" selected>-</option>
                                                @if($ledgers)
                                                    @foreach($ledgers as $ledger)
                                                        <option value="{{ $ledger->id }}">{{ $ledger->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    @else 
                                        <input type="hidden" name="ledger_type" id="ledger_type" value="1">
                                    @endif
                                @endif

                                <div class="form-group col-10 mb-3 group">
                                    <label class="left" for="group_id">Group<span class="text-danger">&nbsp;*</span></label>
                                    <select name="group_id" id="group_id" class="form-control right width-64">
                                        <option class="form-control" value="">-</option>
                                        @foreach($groups as $group)
                                            <option class="form-control" value="{{ $group->id }}">{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-2 mb-3 group">
                                    <button class="btn-md btn-primary btn-sm" id="createNewGroup" data-toggle="modal" data-target="#createNewGroupModal"><i class="la la-plus"></i></button>
                                </div>

                                <div class="form-group col-8 mb-3">
                                    <label class="left" for="opening_balance">Prev. Year CB/<br>Opening Bal.</label>
                                    <input type="number" class="form-control ml-4 right width-55" name="opening_balance" id="opening_balance">
                                </div>

                                <div class="form-group col-4 mb-3">
                                    <label class="left" for="dr_cr">(Rs) Dr/Cr</label>
                                    <select name="dr_cr" id="dr_cr" class="form-control right">
                                        <option class="form-control" value="0">Dr.</option>
                                        <option class="form-control" value="1">Cr.</option>
                                    </select>
                                </div>

                                <div class="form-group col-12 mb-3">
                                    <label class="left" for="address">Address</label>
                                    <input type="text" class="form-control right width-70" name="address" id="address">
                                </div> 

                                <div class="form-group col-12 mb-3">
                                    <label class="left" for="country_id">Country</label>
                                    <select name="country_id" id="country_id" class="form-control right width-70">
                                        @foreach($countries as $country)
                                            <option class="form-control" value="{{ $country->id }}">{{ $country->code }}&nbsp;-&nbsp;{{ $country->name_en }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-12 mb-3">
                                    <label class="left" for="email">Email</label>
                                    <input type="email" class="form-control right width-70" name="email" id="email">
                                </div> 

                                <div class="form-group col-12 mb-3">
                                    <label class="left" for="pan">PAN</label>
                                    <input type="text" class="form-control right width-70" name="pan" id="pan">
                                </div> 

                                <div class="form-group col-12 mb-3">
                                    <label class="left" for="mobile_no">Mobile No.</label>
                                    <input type="text" class="form-control right width-70" name="mobile_no" id="mobile_no">
                                </div> 

                                <div class="form-group col-12 mb-3">
                                    <label class="left" for="tel_no">Tel No.</label>
                                    <input type="text" class="form-control right width-70" name="tel_no" id="tel_no">
                                </div> 

                                <div class="form-group col-12 mb-3">
                                    <label class="left" for="fax">Fax</label>
                                    <input type="text" class="form-control right width-70" name="fax" id="fax">
                                </div> 

                                <div class="form-group col-12 mb-3">
                                    <label class="left" for="contact_peron">Contact Person</label>
                                    <input type="text" class="form-control right width-70" name="contact_peron" id="contact_peron">
                                </div> 
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-6">
                        <fieldset class="border p-2 mb-2">
                            <legend class="font-weight-bold">Other Information</legend>

                            <div class="row form-inline">
                                <div class="form-group col-12 mb-3">
                                    <label class="left" for="maintain_bill_by_bill_balance">Maintain Bill By BIll Balance</label>
                                    <select class="form-control right" name="maintain_bill_by_bill_balance" id="maintain_bill_by_bill_balance">
                                        <option class="form-control" value="0">N</option>
                                        <option class="form-control" value="1">Y</option>
                                    </select>
                                </div>

                                <div class="form-group col-6 mb-3">
                                    <label class="left" for="credit_day_for_sales">Credit Day<br>for Sales</label>
                                    <input type="number" class="form-control right width-50" name="credit_day_for_sales" id="credit_day_for_sales" min="0">
                                </div>

                                <div class="form-group col-6 mb-3">
                                    <label class="left" for="credit_day_for_purchase">Credit Day<br>for Purchase</label>
                                    <input type="number" class="form-control right width-50" name="credit_day_for_purchase" id="credit_day_for_purchase" min="0">
                                </div>

                                <div class="form-group col-5 mb-3">
                                    <label class="left" for="specify_default_sales_type">Specify Default<br>Sales Type</label>
                                    <select class="form-control right" name="specify_default_sales_type" id="specify_default_sales_type">
                                        <option class="form-control" value="0">N</option>
                                        <option class="form-control" value="1">Y</option>
                                    </select>
                                </div>

                                <div class="form-group col-7 mb-3">
                                    <label class="left" for="default_sales_type">Default<br>Sales Type</label>
                                    <select name="default_sales_type" class="form-control right width-60" id="default_sales_type">
                                        <option class="form-control" value="0">-</option>
                                        <option class="form-control" value="1">Export</option>
                                        <option class="form-control" value="2">Services</option>
                                        <option class="form-control" value="3">VAT/13%</option>
                                        <option class="form-control" value="4">VAT/Exempt</option>
                                        <option class="form-control" value="5">VAT/Item-Wise</option>
                                    </select>
                                </div>

                                <div class="form-group col-5 mb-3">
                                    <label class="left" for="specify_default_purchase_type">Specify Default<br>Purc. Type</label>
                                    <select class="form-control right" name="specify_default_purchase_type" id="specify_default_purchase_type">
                                        <option class="form-control" value="0">N</option>
                                        <option class="form-control" value="1">Y</option>
                                    </select>
                                </div>

                                <div class="form-group col-7 mb-3">
                                    <label class="left" for="default_purchase_type">Default<br>Purc. Type</label>
                                    <select name="default_purchase_type" class="form-control right width-60" id="default_purchase_type">
                                        <option class="form-control" value="0">-</option>
                                        <option class="form-control" value="1">Export</option>
                                        <option class="form-control" value="2">Services</option>
                                        <option class="form-control" value="3">VAT/13%</option>
                                        <option class="form-control" value="4">VAT/13%(CP)</option>
                                        <option class="form-control" value="5">VAT/Exempt</option>
                                    </select>
                                </div>

                                <div class="form-group col-6 mb-3">
                                    <label class="left" for="freeze_sale_type">Freez Sale<br>Type</label>
                                    <select class="form-control right" name="freeze_sale_type" id="freeze_sale_type">
                                        <option class="form-control" value="0">N</option>
                                        <option class="form-control" value="1">Y</option>
                                    </select>
                                </div>

                                <div class="form-group col-6 mb-4">
                                    <label class="left" for="freeze_purchase_type">Freez Purc.<br>Type</label>
                                    <select class="form-control right" name="freeze_purchase_type" id="freeze_purchase_type">
                                        <option class="form-control" value="0">N</option>
                                        <option class="form-control" value="1">Y</option>
                                    </select>
                                </div>
                                
                                <div class="form-group col-md-12 mb-4">
                                    <label class="left" for="bank_details">Bank Details</label>
                                    <input type="number" class="form-control right width-70" name="bank_details" id="bank_details" min="0">
                                    <textarea name="bank_details" id="bank_details" class="form-control right width-70" rows="2"></textarea>
                                </div>

                                <div class="form-group col-12 mt-1 mb-3">
                                    <label class="left" for="beneficary_name">Beneficary Name</label>
                                    <input type="text" class="form-control right width-70" name="beneficary_name" id="beneficary_name">
                                </div> 

                                <div class="form-group col-12 mb-3">
                                    <label class="left" for="bank_name">Bank Name</label>
                                    <input type="text" class="form-control right width-70" name="bank_name" id="bank_name">
                                </div> 

                                <div class="form-group col-12 mb-3">
                                    <label class="left" for="bank_ac_no">Bank A/C No.</label>
                                    <input type="text" class="form-control right width-70" name="bank_ac_no" id="bank_ac_no">
                                </div> 

                                <div class="form-group col-12 mb-3">
                                    <label class="left" for="ifsc_code">IFSC Code</label>
                                    <input type="text" class="form-control right width-70" name="ifsc_code" id="ifsc_code">
                                </div> 

                                <div class="form-group col-6 mb-3">
                                    <label class="left" for="enable_email_query">Enable Email Query</label>
                                    <select class="form-control right" name="enable_email_query" id="enable_email_query">
                                        <option class="form-control" value="0">N</option>
                                        <option class="form-control" value="1">Y</option>
                                    </select>
                                </div>

                                <div class="form-group col-6 mb-4">
                                    <label class="left" for="enable_sms_query">Enable SMS Query</label>
                                    <select class="form-control right" name="enable_sms_query" id="enable_sms_query">
                                        <option class="form-control" value="0">N</option>
                                        <option class="form-control" value="1">Y</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <div class="float-right">
                    <button type="submit" class="btn btn-md btn-success"><i class="la la-save"></i>&nbsp;Save and New</button>
                    <a href="{{ url($crud->route) }}" class="btn btn-md btn-secondary text-dark"><i class="la la-ban"></i>&nbsp;Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="createNewGroupModal" tabindex="-1" role="dialog" aria-labelledby="createNewGroupModalTtile" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Account Group Master</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ url($crud->route) }}" method="POST" id="saveGroupForm">
                    <div class="modal-body">
                        @csrf
                        @include('accounts.charts_of_account.partials.org_store')
                        <input type="hidden" name="group" value="1" id="groupValue">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="group_name">Group<span class="text-danger">&nbsp;*</span></label>
                                <input type="text" class="form-control" name="name" id="group_name">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="group_alias">Alias</label>
                                <input type="text" class="form-control" name="alias" id="group_alias">
                            </div>

                            <div class="form-goup col-md-4">
                                <label for="primary_group">Primary Group?</label>
                                <select class="form-control" name="primary_group" id="primary_group">
                                    <option class="form-control" value="0">No</option>
                                    <option class="form-control" value="1">Yes</option>
                                </select>
                            </div>

                            <div class="form-goup col-md-4">
                                <label for="group_id">Under<span class="text-danger">&nbsp;*</span></label>
                                <select class="form-control" name="group_id" id="group_id">
                                    @foreach($groups as $under)
                                        <option class="form-control" value="{{ $under->id }}">{{ $under->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="la la-ban"></i>&nbsp;Close</button>
                        <button type="submit" class="btn btn-success" id="saveGroup"><i class="la la-save"></i>&nbsp;Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('after_scripts')
    @include('accounts.charts_of_account.partials.scripts')
@endsection