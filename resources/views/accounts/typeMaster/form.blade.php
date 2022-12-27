@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
        trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
        $crud->entity_name_plural => url($crud->route),
        trans('backpack::crud.add') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;

    //Checked URL for sales type master and purchase type master
    $url = \Request::route()->getName();

    if (isset($typeMaster)) {
        $url = url($crud->route.'/'.$typeMaster->id);
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
            font-weight: 500;
        }
        .right{
            position: absolute;
            right: 0px;
        }
        .width-60{
            width: 60% !important;
        }
        .form-check{
            justify-content: left;
        }
        .form-check-label{
            font-weight: 500 !important;
        }
        @media (min-width: 576px){
            .form-inline .form-check {
                justify-content: left;
            }
        }
</style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{$url}}" method="POST" enctype="multipart/form-data">
                @csrf
                    @if (isset($typeMaster))
                        @method('PUT')
                        @include('accounts.partial.sup_org_field', ['data' => $typeMaster])
                    @else
                        @include('accounts.partial.sup_org_field')
                    @endif
                        {{-- @include('accounts.typeMaster.partials.message') --}}
                <div class="row">
                    <div class="col-md-6 form-inline">
                        <div class="form-group col mb-3">
                            @if (str_contains($url, 'sales-type-master'))
                                <label class="left" for="sales_type">Sales Type &nbsp;<p class="text-danger">*</p> </label>
                                <input type="text" name="sales_type" id="sales_type" class="@error('sales_type') is-invalid @enderror form-control right width-60" value="{{ isset($typeMaster->sales_type) ? $typeMaster->sales_type  : old('sales_type') }}">
                                @error('sales_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            @elseif(str_contains($url, 'purchase-type-master'))
                                <label class="left" for="purchase_type">Purchase Type &nbsp;<p class="text-danger">*</p></label>
                                <input type="text" name="purchase_type" id="purchase_type" class="@error('purchase_type') is-invalid @enderror form-control right width-60"  value="{{ isset($typeMaster->purchase_type) ? $typeMaster->purchase_type  : old('purchase_type')  }}">
                                @error('purchase_type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <fieldset class="border p-2 mb-2">
                            @if (str_contains($url, 'sales-type-master'))
                                <legend><h4 class="font-weight-bold">Sales Account Information</h4></legend>
                            @elseif(str_contains($url, 'purchase-type-master'))
                                <legend><h4 class="font-weight-bold">Purchase Account Information</h4></legend>
                            @endif
                                <div class="row  p-3">
                                    <div class="col-md-12">
                                        <div class="row">
                                            @foreach ($account_info as $key => $info)
                                                <div class="col-md-12 form-check">
                                                    @if (str_contains($url, 'sales-type-master'))
                                                        <input  class="form-check-input" value="{{ $key }}" type="radio" name="account_info" id="account_info-{{ $key }}" {{  isset($typeMaster->sales_ac_info) ? ($typeMaster->sales_ac_info == $key ? "checked" : "") : ($key == 1 ? 'checked' : "") }}>
                                                    @elseif(str_contains($url, 'purchase-type-master'))
                                                        <input  class="form-check-input" value="{{ $key }}" type="radio" name="account_info" id="account_info-{{ $key }}" {{  isset($typeMaster->purchase_ac_info) ? ($typeMaster->purchase_ac_info == $key ? "checked" : "") : ($key == 1 ? 'checked' : "") }}>
                                                    @endif
                                                    <label  class="form-check-label" for="account_info-{{ $key }}">
                                                        {{ $info }}
                                                    </label>
                                                </div>
                                            @endforeach
                                            {{-- @error('account_info')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror --}}
                                        </div>
                                    </div>
                                </div>
                                @error('account_info')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </fieldset>

                        <fieldset class="border p-2 mb-2">
                            <legend><h4 class="font-weight-bold">Taxaction Type</h4></legend>
                                <div class="row p-3">
                                    @foreach ($tax_type as $tax)
                                        <div class="col-md-6">
                                            <div class="row">
                                                @foreach ($tax as $key => $value)
                                                    <div class="col-md-12 form-check">
                                                        <input  class="form-check-input" value="{{ $key }}" type="radio" name="tax_type" id="tax_type-{{ $key }}" {{  isset($typeMaster->taxation_type) ? ($typeMaster->taxation_type == $key ? "checked" : "") : ($key == 1 ? 'checked' : "") }}>
                                                        <label  class="form-check-label" for="tax_type-{{ $key }}">{{ $value }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                        </fieldset>
                        <p>
                            (Above Information cannot be changed if any transaction exist for this sales type)
                        </p>
                        <fieldset class="border p-2 mb-2">
                            <legend><h4 class="font-weight-bold">Other Information</h4></legend>
                            <div class="row p-3">
                                <div class="col-md-12  form-inline">
                                    <div class="form-group col mb-3">
                                        <label class="left" for="tax_invoice">Tax Invoice</label>
                                        <select name="tax_invoice" id="tax_invoice" class="form-control right form">
                                            <option value="0" {{  isset($typeMaster->tax_invoice) ? ($typeMaster->tax_invoice == 0 ? "selected" : "") : "" }}>No</option>
                                            <option value="1" {{  isset($typeMaster->tax_invoice) ? ($typeMaster->tax_invoice == 1 ? "selected" : "") : "" }}>Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12  form-inline">
                                    <div class="form-group col mb-3">
                                        <label class="left" for="skip_vat">Skip in VAT Reports</label>
                                        <select name="skip_vat" id="skip_vat" class="form-control right">
                                            <option value="0" {{  isset($typeMaster->skip_vat) ? ($typeMaster->skip_vat == 0 ? "selected" : "") : "" }}>No</option>
                                            <option value="1" {{  isset($typeMaster->skip_vat) ? ($typeMaster->skip_vat == 1 ? "selected" : "") : "" }}>Yes</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <fieldset class="border p-2 mb-2">
                            <legend><h4 class="font-weight-bold">Region</h4></legend>
                                <div class="row  p-3">
                                    <div class="col-md-12">
                                        <div class="row">
                                            @foreach ($region as $key => $value)
                                                <div class="col-md-12 form-check">
                                                    <input  class="form-check-input" value="{{ $key }}" type="radio" name="region" id="region-{{ $key }}" {{ isset($typeMaster->region) ? ($typeMaster->region == $key ? "checked" : "") : ($key == 1 ? 'checked' : "") }}>
                                                    <label  class="form-check-label" for="region-{{ $key }}">{{ $value }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                        </fieldset>
                        <fieldset class="border p-2 mb-2">
                            <legend><h4 class="font-weight-bold">Form Information</h4></legend>
                            <div class="row p-3">
                                <div class="col-md-12  form-inline">
                                    <div class="form-group col mb-3">
                                        <label class="left" for="issue_st_form">Issue ST Form</label>
                                        <select name="issue_st_form" id="issue_st_form" class="form-control right">
                                            <option value="0" {{  isset($typeMaster->issue_st_form) ? ($typeMaster->issue_st_form == 0 ? "selected" : "") : "" }}>No</option>
                                            <option value="1" {{  isset($typeMaster->issue_st_form) ? ($typeMaster->issue_st_form == 1 ? "selected" : "") : "" }}>Yes</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="col-md-12  form-inline">
                                    <div class="form-group col mb-3">
                                        <label class="left" for="form_issubale">Form Issuable</label>
                                        <select name="form_issubale" id="form_issubale" class="form-control right">
                                            <option value="0" {{  isset($typeMaster->form_issubale) ? ($typeMaster->form_issubale == 0 ? "selected" : "") : "" }}>No</option>
                                            <option value="1" {{  isset($typeMaster->form_issubale) ? ($typeMaster->form_issubale == 1 ? "selected" : "") : "" }}>Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12  form-inline">
                                    <div class="form-group col mb-3">
                                        <label class="left" for="receive_st_form">Receive ST Form</label>
                                        <select name="receive_st_form" id="receive_st_form" class="form-control right">
                                            <option value="0" {{  isset($typeMaster->receive_st_form) ? ($typeMaster->receive_st_form == 0 ? "selected" : "") : "" }}>No</option>
                                            <option value="1" {{  isset($typeMaster->receive_st_form) ? ($typeMaster->receive_st_form == 1 ? "selected" : "") : "" }}>Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12  form-inline">
                                    <div class="form-group col mb-3">
                                        <label class="left" for="form_receivable">Form Receivable</label>
                                        <select name="form_receivable" id="form_receivable" class="form-control right">
                                            <option value="0" {{  isset($typeMaster->form_receivable) ? ($typeMaster->form_receivable == 0 ? "selected" : "") : "" }}>No</option>
                                            <option value="1" {{  isset($typeMaster->form_receivable) ? ($typeMaster->form_receivable == 1 ? "selected" : "") : "" }}>Yes</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="border p-2 mb-2">
                            <legend><h4 class="font-weight-bold">Tax Calculation</h4></legend>
                            <div class="row p-3">
                                @foreach ($tax_calc as $key => $value)
                                    <div class="col-md-6 form-check">
                                        <input  class="form-check-input" value="{{ $key }}" type="radio" name="tax_calc" id="tax_calc-{{ $key }}" {{  isset($typeMaster->tax_calculation) ? ($typeMaster->tax_calculation == $key ? "checked" : "") : ($key == 1 ? 'checked' : "") }}>
                                        <label  class="form-check-label" for="tax_calc-{{ $key }}">{{ $value }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row p-3">
                                <div class="col-md-12  form-inline">
                                    <div class="form-group col mb-3">
                                        <label class="left" for="tax">Tax (In %)</label>
                                        <input type="number" name="tax" id="tax" class="@error('tax') is-invalid @enderror form-control right width-60" value="{{ isset($typeMaster->tax_percent) ? $typeMaster->tax_percent : old('tax') }}">
                                    </div>
                                </div>
                                <div class="col-md-12  form-inline">
                                    <div class="form-group col mb-3">
                                        <label class="left" for="sucharge">Surcharge (In %)</label>
                                        <input type="number" name="sucharge" id="sucharge" class="@error('sucharge') is-invalid @enderror form-control right width-60" value="{{ isset($typeMaster->surcharge_percent) ? $typeMaster->surcharge_percent : old('sucharge') }}">
                                    </div>
                                </div>
                                <div class="col-md-12  form-inline">
                                    <div class="form-group col mb-3">
                                        <label class="left" for="cess">Add. (Cess In %)</label>
                                        <input type="number" name="cess" id="cess" class="@error('cess') is-invalid @enderror form-control right width-60" value="{{ isset($typeMaster->cess_percent) ? $typeMaster->cess_percent : old('cess') }}">
                                    </div>

                                </div>
                                @if (str_contains($url, 'sales-type-master'))
                                    <div class="col-md-12  form-inline">
                                        <div class="form-group col mb-3">
                                            <label class="left" for="freeze_tax_sales">Freeze Tax In Sales</label>
                                            <select name="freeze_tax_sales" id="freeze_tax_sales" class="form-control right">
                                                <option value="0" {{  isset($typeMaster->freeze_tax_sales) ? ($typeMaster->freeze_tax_sales == 0 ? "selected" : "") : "" }}>No</option>
                                                <option value="1" {{  isset($typeMaster->freeze_tax_sales) ? ($typeMaster->freeze_tax_sales == 0 ? "selected" : "") : "" }}>Yes</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-md-12  form-inline">
                                        <div class="form-group col mb-3">
                                            <label class="left" for="freeze_tax_sales_returns">Freeze Tax In Sales Return</label>
                                            <select name="freeze_tax_sales_returns" id="freeze_tax_sales_returns" class="form-control right">
                                                <option value="0" {{  isset($typeMaster->freeze_tax_sales_returns) ? ($typeMaster->freeze_tax_sales_returns == 0 ? "selected" : "") : "" }}>No</option>
                                                <option value="1" {{  isset($typeMaster->freeze_tax_sales_returns) ? ($typeMaster->freeze_tax_sales_returns == 0 ? "selected" : "") : "" }}>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                @elseif(str_contains($url, 'purchase-type-master'))
                                    <div class="col-md-12  form-inline">
                                        <div class="form-group col mb-3">
                                            <label class="left" for="freeze_tax_purchase">Freeze Tax In Purchase</label>
                                            <select name="freeze_tax_purchase" id="freeze_tax_purchase" class="form-control right">
                                                <option value="0" {{  isset($typeMaster->freeze_tax_purchase) ? ($typeMaster->freeze_tax_purchase == 0 ? "selected" : "") : "" }}>No</option>
                                                <option value="1" {{  isset($typeMaster->freeze_tax_purchase) ? ($typeMaster->freeze_tax_purchase == 0 ? "selected" : "") : "" }}>Yes</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="col-md-12  form-inline">
                                        <div class="form-group col mb-3">
                                            <label class="left" for="freeze_tax_purchase_returns">Freeze Tax In Purchase Return</label>
                                            <select name="freeze_tax_purchase_returns" id="freeze_tax_purchase_returns" class="form-control right">
                                                <option value="0" {{  isset($typeMaster->freeze_tax_purchase_returns) ? ($typeMaster->freeze_tax_purchase_returns == 0 ? "selected" : "") : "" }}>No</option>
                                                <option value="1" {{  isset($typeMaster->freeze_tax_purchase_returns) ? ($typeMaster->freeze_tax_purchase_returns == 0 ? "selected" : "") : "" }}>Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </fieldset>
                        <fieldset class="border p-2 mb-2">
                            <legend><h4 class="font-weight-bold">For printing in Documents</h4></legend>
                            <div class="row p-3">
                                <div class="col-md-12  form-inline">
                                    <div class="form-group col mb-3">
                                        <label class="left" for="inv_heading">Invoice Heading</label>
                                        <input type="text" name="inv_heading" id="inv_heading" class="@error('inv_heading') is-invalid @enderror form-control right width-60" value="{{ isset($typeMaster->inv_heading) ? $typeMaster->inv_heading : old('inv_heading') }}">
                                        @error('inv_heading')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12 form-inline">
                                    <div class="form-group col mb-3">
                                        <label class="left" for="inv_description">Invoice Description</label>
                                        <input type="textarea" name="inv_description" id="inv_description" class="@error('inv_description') is-invalid @enderror form-control right width-60" value="{{ isset($typeMaster->inv_description) ? $typeMaster->inv_description : old('inv_description') }}">
                                        @error('inv_description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        {{-- <textarea name="name" id="name" class="form-control" rows="4" cols="50"></textarea> --}}
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('.select2').select2();
    </script>
    @include('accounts.bill_sundry.partials.scripts')
@endsection
