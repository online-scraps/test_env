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
        .right-px{
            position: absolute;
            right: 15px;
        }
        .width-50{
            width: 50% !important;
        }
        .width-70{
            width: 70% !important;
        }
        .mb-4rem{
            margin-bottom: 4rem;
        }
        .pl-35{
            padding-left: 35px;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @if(isset($account_settings))
            <form action="{{ url($crud->route).'/'.$account_settings->id }}" method="POST" id="accountSetting">
                @method('PUT')
                @csrf
                @include('accounts.partial.sup_org', [$data = $account_settings])
            @else 
            <form action="{{ url($crud->route) }}" method="POST" id="accountSetting">
                @csrf
                @include('accounts.partial.sup_org')
            @endif
                <fieldset class="border p-2 pb-3 mb-2">
                    <legend class="font-weight-bold">Account Setting</legend>
                    
                    <div class="row from-inline">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="bill_by_bill" id="bill_by_bill" {{ isset($account_settings) ? ($account_settings->bill_by_bill == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="bill_by_bill">Bill by Bill Details</label>
                            </div>  
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="credit_limits" id="credit_limits" {{ isset($account_settings) ? ($account_settings->credit_limits == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="credit_limits">Credit Limits</label>
                            </div>  
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="targets" id="targets" {{ isset($account_settings) ? ($account_settings->targets == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="targets">Targets</label>
                            </div>  
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="cost_centers" id="cost_centers" {{ isset($account_settings) ? ($account_settings->cost_centers == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="cost_centers">Cost Centers</label>
                            </div>  
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="ac_wise_intrest_rate" id="ac_wise_intrest_rate" {{ isset($account_settings) ? ($account_settings->ac_wise_intrest_rate == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="ac_wise_intrest_rate">Account Wise Intrest Rate</label>
                            </div>  
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="ledger_reconciliation" id="ledger_reconciliation" {{ isset($account_settings) ? ($account_settings->ledger_reconciliation == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="ledger_reconciliation">Ledger Reconciliation</label>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="show_ac_current_balance" id="show_ac_current_balance" {{ isset($account_settings) ? ($account_settings->show_ac_current_balance == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="show_ac_current_balance">Show Accounts Current Balance During Voucher Entry</label>
                            </div>
                            <div class="form-group mb-4rem">
                                <label class="left" for="balance_sheet_stock_updation">Balance Sheet Stock Updation</label>
                                <select name="balance_sheet_stock_updation" id="balance_sheet_stock_updation" class="form-control right-px width-50">
                                    <option class="form-control" value="">-</option>
                                </select>
                            </div>  
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="single_entry" id="single_entry" {{ isset($account_settings) ? ($account_settings->single_entry == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="single_entry">Single Entry System for Payment & Receipt Vouchers</label>
                            </div>  
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="posting_in_ac" id="posting_in_ac" {{ isset($account_settings) ? ($account_settings->posting_in_ac == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="posting_in_ac">Posting in Accounts Through Sales Return & Purchase Return</label>
                            </div>   
                        </div>

                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="salesman_broker_reporting" id="salesman_broker_reporting" {{ isset($account_settings) ? ($account_settings->salesman_broker_reporting == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="salesman_broker_reporting">Salesman/Broker-wise Reporting</label>
                            </div> 
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="budgets" id="budgets" {{ isset($account_settings) ? ($account_settings->budgets == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="budgets">Budgets</label>
                            </div> 
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="royalty_calculation" id="royalty_calculation" {{ isset($account_settings) ? ($account_settings->royalty_calculation == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="royalty_calculation">Royalty Calculation</label>
                            </div> 
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="company_act_depreciation" id="company_act_depreciation" {{ isset($account_settings) ? ($account_settings->company_act_depreciation == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="company_act_depreciation">Company's Act Depreciation</label>
                            </div> 
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="maintain_sub_ledgers" id="maintain_sub_ledgers" {{ isset($account_settings) ? ($account_settings->maintain_sub_ledgers == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="maintain_sub_ledgers">Maintain Sub Ledgers</label>
                            </div> 
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="maintain_multiple_ac" id="maintain_multiple_ac" {{ isset($account_settings) ? ($account_settings->maintain_multiple_ac == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="maintain_multiple_ac">Maintain Multiple Account Aliases</label>
                            </div> 
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="multiple_currency" id="multiple_currency" {{ isset($account_settings) ? ($account_settings->multiple_currency == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="multiple_currency">Multi Currency</label>
                            </div> 
                            <div class="form-group mb-4rem">
                                <label class="left" for="decimal_place">Currency Con Decimal Places</label>
                                <input type="number" class="form-control right-px width-50" name="decimal_place" min="0" id="decimal_place" value="{{ isset($account_settings->decimal_place) ? $account_settings->decimal_place : old('decimal_place') }}">
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="maintain_image_note" id="maintain_image_note" {{ isset($account_settings) ? ($account_settings->maintain_image_note == true ? 'checked' : '') : '' }}>
                                <label class="form-check-label" for="maintain_image_note">Maintain Images/Notes with Masters/Vouchers</label>
                                
                                <!-- image note configure button -->
                                <div class="btn btn-sm btn-secondary d-none" id="maintain_image_note_configure" data-toggle="modal" data-target="#imageNoteConfigureModal" data-toggle="tooltip"  title="Configure">
                                    <i class="la la-cogs"></i>
                                </div>
                            </div> 
                        </div>

                        <div class="col-md-12">
                            <div class="row">
                                <div class="form-check mb-3 col-md-6 pl-35">
                                    <input type="checkbox" class="form-check-input" name="party_dashboard" id="party_dashboard" {{ isset($account_settings) ? ($account_settings->party_dashboard == true ? 'checked' : '') : '' }}>
                                    <label class="form-check-label" for="party_dashboard">Enable Party Dashboard</label>
                                </div> 
                                <div class="form-inline form-group mb-3 col-md-6 dashboard_after_selecting_party_div">
                                    <label class="left" for="dashboard_after_selecting_party">Show Party Dash Board After Selecting Party In Vouchers</label>
                                    <select class="form-control right" name="dashboard_after_selecting_party" id="dashboard_after_selecting_party">
                                        <option class="form-control" value="0" {{ isset($account_settings) ? ($account_settings->dashboard_after_selecting_party == 0 ? 'selected' : '') : '' }}>N</option>
                                        <option class="form-control" value="1" {{ isset($account_settings) ? ($account_settings->dashboard_after_selecting_party == 1 ? 'selected' : '') : '' }}>Y</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="row">
                                <div class="form-check mb-3 col-md-6 pl-35">
                                    <input type="checkbox" class="form-check-input" name="maintain_ac_category" id="maintain_ac_category" {{ isset($account_settings) ? ($account_settings->maintain_ac_category == true ? 'checked' : '') : '' }}>
                                    <label class="form-check-label" for="maintain_ac_category">Maintain Account Category</label>
                                </div>
                                <div class="form-group col-6 mb-3 ac_category_caption_div">
                                    <label class="left" for="ac_category_caption">Caption</label>
                                    <input type="text" class="form-control right width-70" name="ac_category_caption" id="ac_category_caption" value="{{ isset($account_settings->ac_category_caption) ? $account_settings->ac_category_caption : old('ac_category_caption') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="border p-2 pb-3 mb-2">
                    <legend class="font-weight-bold">Banking Options</legend>

                    <div class="row">
                        <div class="form-check col-6 mb-3 pl-35">
                            <input type="checkbox" class="form-check-input" name="bank_reconciliation" id="bank_reconciliation" {{ isset($account_settings) ? ($account_settings->bank_reconciliation == true ? 'checked' : '') : '' }}>
                            <label class="form-check-label" for="bank_reconciliation">Bank Reconciliation</label>
                        </div> 
                        <div class="form-check col-6 mb-3">
                            <input type="checkbox" class="form-check-input" name="bank_instrument_detail" id="bank_instrument_detail" {{ isset($account_settings) ? ($account_settings->bank_instrument_detail == true ? 'checked' : '') : '' }}>
                            <label class="form-check-label" for="bank_instrument_detail">Maintain Bank Instrument Details</label>
                        </div> 
                        <div class="form-check col-6 mb-3 pl-35">
                            <input type="checkbox" class="form-check-input" name="post_dated_cheque" id="post_dated_cheque" {{ isset($account_settings) ? ($account_settings->post_dated_cheque == true ? 'checked' : '') : '' }}>
                            <label class="form-check-label" for="post_dated_cheque">Post Dated Cheques in Payment/Receipt Vouchers</label>
                        </div> 
                        <div class="form-check col-6 mb-3">
                            <input type="checkbox" class="form-check-input" name="cheque_printing" id="cheque_printing" {{ isset($account_settings) ? ($account_settings->cheque_printing == true ? 'checked' : '') : '' }}>
                            <label class="form-check-label" for="cheque_printing">Cheque Printing</label>
                        </div> 
                    </div>
                </fieldset>
                <div class="float-right">
                    <button type="submit" class="btn btn-md btn-success"><i class="la la-save"></i>&nbsp;Save</button>
                    <a href="{{ url($crud->route) }}" class="btn btn-md btn-secondary text-dark"><i class="la la-ban"></i>&nbsp;Cancel</a>
                </div>
            </form>
        </div>
    </div>

    @include('accounts.account_setting.partials.configuration_modals')

@endsection

@section('after_scripts')
    @include('accounts.account_setting.partials.scripts')
@endsection