@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
        trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
        $crud->entity_name_plural => url($crud->route),
        trans('backpack::crud.add') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
    if (isset($billSundry)) {
        $url = url($crud->route.'/'.$billSundry->id);
    } else {
        $url = url($crud->route);
    }
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
        fieldset{
            width: 100%;
        }
        legend{
            font-size: medium;
            font-weight: bold;
        }
        .left{
            float: left;
        }
        .right{
            position: absolute;
            right: 0px;
        }
        .width-60{
            width: 60% !important;
        }
</style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ $url }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($billSundry))
                    @method('PUT')
                    @include('accounts.partial.sup_org_field', ['data' => $billSundry])
                @else
                    @include('accounts.partial.sup_org_field')
                @endif

                <div class="row">
                    <div class="col-md-5 form-inline">
                        <div class="form-group col mb-3">
                            <label class="left" for="name">Name &nbsp;<p class="text-danger">*</p> </label>
                            <input type="text" name="name" id="name" class="@error('name') is-invalid @enderror form-control right width-60" value="{{ isset($billSundry->name) ? $billSundry->name : old('name') }}">
                            @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="form-group col mb-3">
                            <label class="left" for="alias">Alias</label>
                            <input type="text" name="alias" id="alias" class="form-control right width-60"  value="{{ isset($billSundry->alias) ? $billSundry->alias : old('alias') }}">
                        </div>

                        <div class="form-group col mb-3">
                            <label class="left" for="print_name">Print Name</label>
                            <input type="text" name="print_name" id="print_name" class="form-control right width-60" value="{{ isset($billSundry->print_name) ? $billSundry->print_name : old('print_name') }}">
                        </div>

                        <div class="form-group col mb-3">
                            <label class="left" for="bill_sundry_type">Bill Sundry Type</label>
                            <select name="bill_sundry_type" id="bill_sundry_type" class="form-control right width-60">
                                <option value="0" disabled>-</option>
                                <option value="1" {{ isset($billSundry->sundry_type) ? ($billSundry->sundry_type == '1' ? "selected" : "") : (old('bill_sundry_type') == "1" ? "selected" : "") }}>Additive</option>
                                <option value="2" {{ isset($billSundry->sundry_type) ? ($billSundry->sundry_type == '2' ? "selected" : "") : (old('bill_sundry_type') == "2" ? "selected" : "") }}>Subtractive</option>
                            </select>
                        </div>

                        <div class="form-group col mb-3">
                            <label class="left" for="bill_sundry_nature">Bill Sundry Nature</label>
                            <select name="bill_sundry_nature" id="bill_sundry_nature" class="form-control right width-60">
                                <option value="">-</option>
                            </select>
                        </div>

                        <div class="form-group col mb-3">
                            <label class="left" for="default_value">Default Value</label>
                            <input type="number" name="default_value" id="default_value" class="form-control right width-60" min="0" value="{{ isset($billSundry->default_value) ? $billSundry->default_value : old('default_value') }}">
                        </div>

                        <div class="form-group col mb-3">
                            <label class="left" for="sub_total_heading">Sub Total Heading</label>
                            <input type="text" name="sub_total_heading" id="sub_total_heading" class="form-control right width-60" value="{{ isset($billSundry->sub_total_heading) ? $billSundry->sub_total_heading : old('sub_total_heading') }}" >
                        </div>

                        <div class="form-group col mb-3">
                            <label class="left" for="accounting_in_sale">Accounting in Sale</label>
                            <select name="accounting_in_sale" id="accounting_in_sale" class="form-control right">
                                <option value="0"  {{  isset($billSundry->account_sale) ? ($billSundry->account_sale == '0' ? "selected" : "") : (old('accounting_in_sale') == "0" ? "selected" : "") }}>No</option>
                                <option value="1"  {{  isset($billSundry->account_sale) ? ($billSundry->account_sale == '1' ? "selected" : "") : (old('accounting_in_sale') == "1" ? "selected" : "") }}>Yes</option>
                            </select>
                        </div>

                        <div class="form-group col mb-3">
                            <label class="left" for="accounting_in_purchase">Accounting in Purchase</label>
                            <select name="accounting_in_purchase" id="accounting_in_purchase" class="form-control right">
                                <option value="0"  {{  isset($billSundry->account_purchase) ? ($billSundry->account_purchase == '0' ? "selected" : "") : (old('accounting_in_purchase') == "0" ? "selected" : "") }}>No</option>
                                <option value="1"  {{  isset($billSundry->account_purchase) ? ($billSundry->account_purchase == '1' ? "selected" : "") : (old('accounting_in_purchase') == "1" ? "selected" : "") }}>Yes</option>
                            </select>
                        </div>

                        <div class="form-group col mb-3">
                            <label class="left" for="affects_the_cost_of_goods_in_sale">Affects the Cost of Goods in Sale</label>
                            <select name="affects_the_cost_of_goods_in_sale" id="affects_the_cost_of_goods_in_sale" class="form-control right">
                                <option value="0"  {{  isset($billSundry->affects_good_sales) ? ($billSundry->affects_good_sales == '0' ? "selected" : "") : "" }}>No</option>
                                <option value="1"  {{  isset($billSundry->affects_good_sales) ? ($billSundry->affects_good_sales == '1' ? "selected" : "") : "" }}>Yes</option>
                            </select>
                        </div>

                        <div class="form-group col mb-3">
                            <label class="left" for="affects_the_cost_of_goods_in_purchase">Affects the Cost of Goods in Purchase</label>
                            <select name="affects_the_cost_of_goods_in_purchase" id="affects_the_cost_of_goods_in_purchase" class="form-control right">
                                <option value="0"  {{  isset($billSundry->affects_good_purchase) ? ($billSundry->affects_good_purchase == '0' ? "selected" : "") : "" }}>No</option>
                                <option value="1"  {{  isset($billSundry->affects_good_purchase) ? ($billSundry->affects_good_purchase == '1' ? "selected" : "") : "" }}>Yes</option>
                            </select>
                        </div>

                        <div class="form-group col mb-3">
                            <label class="left" for="affects_the_cost_of_goods_in_material_issue">Affects the Cost of Goods in Material Issue</label>
                            <select name="affects_the_cost_of_goods_in_material_issue" id="affects_the_cost_of_goods_in_material_issue" class="form-control right">
                                <option value="0"  {{  isset($billSundry->affects_good_material_issue) ? ($billSundry->affects_good_material_issue == '0' ? "selected" : "") : "" }}>No</option>
                                <option value="1"  {{  isset($billSundry->affects_good_material_issue) ? ($billSundry->affects_good_material_issue == '1' ? "selected" : "") : "" }}>Yes</option>
                            </select>
                        </div>

                        <div class="form-group col mb-3">
                            <label class="left" for="affects_the_cost_of_goods_in_material_receipt">Affects the Cost of Goods in Material Receipt</label>
                            <select name="affects_the_cost_of_goods_in_material_receipt" id="affects_the_cost_of_goods_in_material_receipt" class="form-control right">
                                <option value="0"  {{  isset($billSundry->affects_good_material_receipt) ? ($billSundry->affects_good_material_receipt == '0' ? "selected" : "") : "" }}>No</option>
                                <option value="1"  {{  isset($billSundry->affects_good_material_receipt) ? ($billSundry->affects_good_material_receipt == '1' ? "selected" : "") : "" }}>Yes</option>
                            </select>
                        </div>

                        <div class="form-group col mb-3">
                            <label class="left" for="affects_the_cost_of_goods_in_stock_transfer">Affects the Cost of Goods in Stock Transfer</label>
                            <select name="affects_the_cost_of_goods_in_stock_transfer" id="affects_the_cost_of_goods_in_stock_transfer" class="form-control right">
                                <option value="0"  {{  isset($billSundry->affects_good_stock_transfer) ? ($billSundry->affects_good_stock_transfer == '0' ? "selected" : "") : "" }}>No</option>
                                <option value="1"  {{  isset($billSundry->affects_good_stock_transfer) ? ($billSundry->affects_good_stock_transfer == '1' ? "selected" : "") : "" }}>Yes</option>
                            </select>
                        </div>

                        <fieldset class="border p-2 mb-2">
                            <legend>Accounting in Sale</legend>

                            <div class="form-group col mb-3">
                                <label class="left" for="sales_affects_acconting">Affects Accounting</label>
                                <select name="sales_affects_acconting" id="sales_affects_acconting" class="form-control right">
                                    <option value="0"  {{  isset($billSundry->affects_accounting_sale) ? ($billSundry->affects_accounting_sale == '0' ? "selected" : "") : "" }}>No</option>
                                <option value="1"  {{  isset($billSundry->affects_accounting_sale) ? ($billSundry->affects_accounting_sale == '1' ? "selected" : "") : "" }}>Yes</option>
                                </select>
                            </div>

                            <div class="form-group col mb-3">
                                <label class="left" for="sales_adjust_in_purchase_amount">Adjust in Purchase Amount</label>
                                <select name="sales_adjust_in_purchase_amount" id="sales_adjust_in_purchase_amount" class="form-control right">
                                    <option value="0"  {{  isset($billSundry->adjust_amount_sale) ? ($billSundry->adjust_amount_sale == '0' ? "selected" : "") : "" }}>No</option>
                                    <option value="1"  {{  isset($billSundry->adjust_amount_sale) ? ($billSundry->adjust_amount_sale == '1' ? "selected" : "") : "" }}>Yes</option>
                                </select>
                            </div>

                            <div class="form-group col mb-3">
                                <label class="left" for="sales_purchase_amount_account_htp">Account Head to Post</label>
                                <input type="text" name="sales_purhcase_amount_account_htp" id="sales_purhcase_amount_account_htp" class="form-control right width-60" value="{{ isset($billSundry->account_head_sale) ? $billSundry->account_head_sale : old('account_head_sale') }}">
                            </div>

                            <div class="form-group col mb-3">
                                <label class="left" for="sales_adjust_in_party_account">Adjust in Party Account</label>
                                <select name="sales_adjust_in_party_account" id="sales_adjust_in_party_account" class="form-control right">
                                    <option value="0"  {{  isset($billSundry->adjust_party_amount_sale) ? ($billSundry->adjust_party_amount_sale == '0' ? "selected" : "") : "" }}>No</option>
                                    <option value="1"  {{  isset($billSundry->adjust_party_amount_sale) ? ($billSundry->adjust_party_amount_sale == '1' ? "selected" : "") : "" }}>Yes</option>
                                </select>
                            </div>

                            <div class="form-group col mb-3">
                                <label class="left" for="sales_party_account_htp">Account Head to Post</label>
                                <input type="text" name="sales_party_account_htp" id="sales_party_account_htp" class="form-control right width-60" value="{{ isset($billSundry->account_head_party_sale) ? $billSundry->account_head_party_sale : old('account_head_party_sale') }}">
                            </div>

                            <div class="form-group col mb-3">
                                <label class="left" for="sales_post_over_and_above">Post Over and Above</label>
                                <select name="sales_post_over_and_above" id="sales_post_over_and_above" class="form-control right">
                                    <option value="0"  {{  isset($billSundry->post_over_sale) ? ($billSundry->post_over_sale == '0' ? "selected" : "") : "" }}>No</option>
                                    <option value="1"  {{  isset($billSundry->post_over_sale) ? ($billSundry->post_over_sale == '1' ? "selected" : "") : "" }}>Yes</option>
                                </select>
                            </div>

                            <div class="form-group col mb-3">
                                <label class="left" for="sales_impact_zero_tax_items">Impact Zero Tax Items</label>
                                <select name="sales_impact_zero_tax_items" id="sales_impact_zero_tax_items" class="form-control right">
                                    <option value="0"  {{  isset($billSundry->impact_zero_tax_sale) ? ($billSundry->impact_zero_tax_sale == '0' ? "selected" : "") : "" }}>No</option>
                                    <option value="1"  {{  isset($billSundry->impact_zero_tax_sale) ? ($billSundry->impact_zero_tax_sale == '1' ? "selected" : "") : "" }}>Yes</option>
                                </select>
                            </div>
                        </fieldset>

                        <fieldset class="border p-2 mb-2">
                            <legend>Accounting in Purchase</legend>

                            <div class="form-group col mb-3">
                                <label class="left" for="purchase_affects_accounting">Affects Accounting</label>
                                <select name="purchase_affects_accounting" id="purchase_affects_accounting" class="form-control right">
                                    <option value="0"  {{ isset($billSundry->affects_accounting_purchase) ? ($billSundry->affects_accounting_purchase == '0' ? "selected" : "") : "" }}>No</option>
                                    <option value="1"  {{ isset($billSundry->affects_accounting_purchase) ? ($billSundry->affects_accounting_purchase == '1' ? "selected" : "") : "" }}>Yes</option>
                                </select>
                            </div>

                            <div class="form-group col mb-3">
                                <label class="left" for="purchase_adjust_in_purchase_amount">Adjust in Purchase Amount</label>
                                <select name="purchase_adjust_in_purchase_amount" id="purchase_adjust_in_purchase_amount" class="form-control right">
                                    <option value="0"  {{  isset($billSundry->adjust_amount_purchase) ? ($billSundry->adjust_amount_purchase == '0' ? "selected" : "") : "" }}>No</option>
                                    <option value="1"  {{  isset($billSundry->adjust_amount_purchase) ? ($billSundry->adjust_amount_purchase == '1' ? "selected" : "") : "" }}>Yes</option>
                                </select>
                            </div>

                            <div class="form-group col mb-3">
                                <label class="left" for="purchase_amount_account_htp">Account Head to Post</label>
                                <input type="text" name="purchase_amount_account_htp" id="purchase_amount_account_htp" class="form-control right width-60" value="{{ isset($billSundry->account_head_purchase) ? $billSundry->account_head_purchase : old('account_head_purchase') }}">
                            </div>

                            <div class="form-group col mb-3">
                                <label class="left" for="purchase_adjust_in_party_account">Adjust in Party Account</label>
                                <select name="purchase_adjust_in_party_account" id="purchase_adjust_in_party_account" class="form-control right">
                                    <option value="0" {{  isset($billSundry->adjust_party_amount_purchase) ? ($billSundry->adjust_party_amount_purchase == '0' ? "selected" : "") : "" }}>No</option>
                                    <option value="1" {{  isset($billSundry->adjust_party_amount_purchase) ? ($billSundry->adjust_party_amount_purchase == '1' ? "selected" : "") : "" }}>Yes</option>
                                </select>
                            </div>

                            <div class="form-group col mb-3">
                                <label class="left" for="purchase_party_account_htp">Account Head to Post</label>
                                <input type="text" name="purchase_party_account_htp" id="purchase_party_account_htp" class="form-control right width-60" value="{{ isset($billSundry->account_head_party_purchase) ? $billSundry->account_head_party_purchase : old('account_head_party_purchase') }}">
                            </div>

                            <div class="form-group col mb-3">
                                <label class="left" for="purchase_post_over_and_above">Post Over and Above</label>
                                <select name="purchase_post_over_and_above" id="purchase_post_over_and_above" class="form-control right">
                                    <option value="0"  {{  isset($billSundry->post_over_purchase) ? ($billSundry->post_over_purchase == '0' ? "selected" : "") : "" }}>No</option>
                                    <option value="1"  {{  isset($billSundry->post_over_purchase) ? ($billSundry->post_over_purchase == '1' ? "selected" : "") : "" }}>Yes</option>
                                </select>
                            </div>

                            <div class="form-group col mb-3">
                                <label class="left" for="purchase_impact_zero_tax_items">Impact Zero Tax Items</label>
                                <select name="purchase_impact_zero_tax_items" id="purchase_impact_zero_tax_items" class="form-control right">
                                    <option value="0" {{  isset($billSundry->impact_zero_tax_purchase) ? ($billSundry->impact_zero_tax_purchase == '0' ? "selected" : "") : (old('purchase_impact_zero_tax_items') == "0" ? "selected" : "")}}>No</option>
                                    <option value="1" {{  isset($billSundry->impact_zero_tax_purchase) ? ($billSundry->impact_zero_tax_purchase == '1' ? "selected" : "") : (old('purchase_impact_zero_tax_items') == "1" ? "selected" : "")}}>Yes</option>
                                </select>
                            </div>
                        </fieldset>
                    </div>

                    <div class="col-md-7 form-group">
                        <fieldset class="border p-2 mb-2">
                            <legend>Accounting in Material Issue/ Receipt/ Stock Transfer</legend>
                            <div class="row p-3">
                                @foreach ($accounting_material as $key => $value)
                                <div class="col form-check">
                                    <input  class="form-check-input" value="{{ $key }}" type="radio" name="accounting_in" id="material_issue-{{ $key }}"  {{  isset($billSundry->accounting_material) ? ($billSundry->accounting_material == $key ? "checked" : "") : ((old('accounting_in') == $key) ? "checked" : "" ) }}>
                                    <label  class="form-check-label" for="material_issue-{{ $key }}">{{ $value }}</label>
                                </div>
                                @endforeach
                            </div>
                            <fieldset class="border p-2 mb-2">
                                <div>Affects Accounting</div>
                                <div>Other Side</div>
                                <div>Account Head to Post</div>
                                <div>Adjust in Party Account</div>
                                <div>Post Over and Above</div>
                            </fieldset>
                        </fieldset>

                        <fieldset class="border p-2 mb-2">
                            <legend>Amount of Bill Sundry to be Fed as</legend>

                            <div class="row p-3">
                                @foreach ($amount_bill_sundry_fed as $key => $value)
                                    <div class="col-md-4 form-check">
                                        <input  class="form-check-input" value="{{ $key }}" type="radio" name="amount_of_bill_sundry_fed" id="amount_of_bill_sundry_fed-{{ $key }}"  {{  isset($billSundry->bill_sundry_fed) ? ($billSundry->bill_sundry_fed == $key ? "checked" : "") : ((old('accounting_in') == $key) ? "checked" : "" )  }}>
                                        <label  class="form-check-label" for="amount_of_bill_sundry_fed-{{ $key }}">{{ $value }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <fieldset class="border pl-4 pb-2 pr-2 pt-0 mb-2">
                                <legend>Of</legend>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="col-md-12">
                                            100.00 % of
                                        </div>
                                        <div class="row">
                                            @foreach ($bill_sundry_percentage_of as $key => $value)
                                                <div class="col-md-12 form-check">
                                                    <input  class="form-check-input" value="{{ $key }}" type="radio" name="fed_as_of" id="net_bill_amount-{{ $key }}"  {{ isset($billSundry->percentage_of) ? ($billSundry->percentage_of == $key ? "checked" : "") : ((old('fed_as_of') == $key) ? "checked" : "" ) }}>
                                                    <label  class="form-check-label" for="net_bill_amount-{{ $key }}">{{ $value }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="row form-inline">
                                            <div class="col-8">
                                                <div class="form-group mb-2">
                                                    <label class="left" for="selective_calculation_select">Selective Calculation</label>
                                                    <select name="selective_calculation" id="selective_calculation_select" class="form-control ml-2">
                                                        <option value="0"  {{  isset($billSundry->selective_calc) ? ($billSundry->selective_calc == '0' ? "selected" : "") : "" }}>No</option>
                                                        <option value="1"  {{  isset($billSundry->selective_calc) ? ($billSundry->selective_calc == '1' ? "selected" : "") : "" }}>Yes</option>
                                                    </select>
                                                </div>
                                            </div>
                                            {{-- <div class="col-4 right">
                                                <a href="void:;" class="btn btn-sm btn-secondary">Select Option</a>
                                            </div> --}}

                                        </div>

                                        <fieldset class="border p-2 mb-2 form-inline">
                                            <legend>Previous Bill Sundry(s) Details</legend>

                                            <div class="form-group col mb-3">
                                                <label class="left" for="no_of_bill_sundry">No. of Bill Sundry(s)</label>
                                                <input type="number" name="no_of_bill_sundry" id="no_of_bill_sundry" min="0" max="9" class="form-control right"  value="{{ isset($billSundry->no_bill_sundry) ? $billSundry->no_bill_sundry : old('no_of_bill_sundry') }}">
                                            </div>

                                            <div class="form-group col mb-3">
                                                <label class="left" for="consolidate_bill_sundries_amount">Consolidate Bill Sundries Amount</label>
                                                <select name="consolidate_bill_sundries_amount" id="consolidate_bill_sundries_amount" class="form-control right">
                                                    <option value="0"  {{  isset($billSundry->consolidate_amount) ? ($billSundry->consolidate_amount == '0' ? "selected" : "") : "" }}>No</option>
                                                    <option value="1"  {{  isset($billSundry->consolidate_amount) ? ($billSundry->consolidate_amount == '1' ? "selected" : "") : "" }}>Yes</option>
                                                </select>
                                            </div>
                                        </fieldset>

                                        <fieldset class="border p-2 mb-2">
                                            <legend>Bill Sundry to be Calculated On</legend>

                                            <div class="row pl-3">
                                                @foreach ($bill_sundry_calculated_on as $key => $value)
                                                    <div class="col form-check">
                                                        <input  class="form-check-input" value="{{ $key }}" type="radio" name="bill_sundry_to_be_calculated_on" id="bill_sundry_amount-{{ $key }}">
                                                        <label  class="form-check-label" for="bill_sundry_amount-{{ $key }}">{{ $value }}</label>
                                                    </div>
                                                @endforeach
                                                {{-- <div class="col form-check">
                                                    <input  class="form-check-input" value="0" type="radio" name="bill_sundry_to_be_calculated_on" id="bill_sundry_amount">
                                                    <label  class="form-check-label" for="bill_sundry_amount">Bill Sundry Amount</label>
                                                </div>

                                                <div class="col form-check">
                                                    <input  class="form-check-input" value="1" type="radio" name="bill_sundry_to_be_calculated_on" id="bill_sundry_applied_on">
                                                    <label  class="form-check-label" for="bill_sundry_applied_on">Bill Sundry Applied On</label>
                                                </div> --}}
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </fieldset>
                        </fieldset>

                        <fieldset class="border p-2 mb-2">
                            <legend>Bill Sundry Amount Round Off</legend>

                            <div class="form-inline">
                                <div class="form-group col mb-3">
                                    <label class="left" for="round_off_bill_sundry_amount">Round Off Bill Sundry Amount</label>
                                    <select name="round_off_bill_sundry_amount" id="round_off_bill_sundry_amount" class="form-control right">
                                        <option value="0"  {{  isset($billSundry->round_off) ? ($billSundry->round_off == '0' ? "selected" : "") : "" }}>No</option>
                                        <option value="1"  {{  isset($billSundry->round_off) ? ($billSundry->round_off == '1' ? "selected" : "") : "" }}>Yes</option>
                                    </select>
                                </div>

                                <div class="form-group col mb-2">
                                    <label class="left" for="rounding_off_nearest_to">Rounding Off Nearest to</label>
                                    <div class="input-group-prepend right">
                                        <span class="input-group-text">Rs.</span>
                                        <input type="number" name="rounding_off_nearest_to" id="rounding_off_nearest_to" class="form-control" min="0" readonly  value="{{ isset($billSundry->round_off_nearest) ? $billSundry->round_off_nearest : old('round_off_nearest') }}">
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                @include('crud::inc.form_save_buttons')
            </form>
        </div>
    </div>
@endsection

@section('after_scripts')
    @include('accounts.bill_sundry.partials.scripts')
@endsection
