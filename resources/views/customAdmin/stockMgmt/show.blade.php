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

@push('after_styles')
    <style>
        .upper-container .col-2 {
            flex: 0 0 auto;
            width: 9.666667%;
        }
        .row *{
            border: transparent !important;
        }
        .input-group-text {
            background-color: transparent !important;
            font-weight: bold;

        }
    </style>
@endpush

@section('header')
    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
            <small>{!! $crud->getSubheading() ?? trans('backpack::crud.preview').' '.$crud->entity_name !!}.</small>

            @if ($crud->hasAccess('list'))
                <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
            @endif
        </h2>
    </section>
@endsection

@php 
 $status=[
    1=>'text-warning',
    2=>'text-success',
    3=>'text-danger',
    ];
@endphp

@section('content')
    <div class="card shadow px-3 mt-4">
        <!-- store name section -->
        <div class="mt-3">
            <div class="row">
                <div class="col-lg-3 col-md-4">
                    <div class="mb-3">
                        <span class="me-1" style="font-weight: bold;"> Status: </span> <span class="{{ isset($entry->sup_status_id)? $status[$entry->sup_status_id] : ''}}" style="font-weight: bold;"> {{ucfirst($entry->supStatus->name_en)}}</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="mb-3">
                        <span class="me-1" style="font-weight: bold;">Store Name: </span> <span>{{$entry->mstStore->name_en}}</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="mb-3">
                        <span class="me-1" style="font-weight: bold;">Batch No: </span> <span>{{$entry->getBatchNo()}}</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="mb-3">
                        <span class="me-1" style="font-weight: bold;">Adj No: </span> <span>{{$entry->adjustment_no??'n/a'}}</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="mb-3">
                        <span class="me-1" style="font-weight: bold;">Date Ad: </span> <span>{{$entry->entry_date_ad?dateToString($entry->entry_date_ad):'n/a'}}</span>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="mb-3">
                        <span class="me-1" style="font-weight: bold;">Date Bs:</span> <span>{{$entry->getDateString()}}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- table for item -->
        <div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                    <tr style="background-color: rgb(241, 241, 241)">
                        <th scope="col">Item</th>
                        <th scope="col">Add Qty</th>
                        <th scope="col">Free item</th>
                        <th scope="col">Total Qty</th>
                        <th scope="col">Expiry Date</th>
                        <th scope="col">Unit cost</th>
                        <th scope="col">unit Sales</th>
                        <th scope="col">Discount</th>
                        <th scope="col">Tax/vat</th>
                        <th scope="col">Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td scope="col">{{$item->mstItem->name}}</td>
                            <td scope="col">{{$item->add_qty}}</td>
                            <td scope="col">{{$item->free_qty??'n/a'}}</td>
                            <td scope="col">{{$item->total_qty}}</td>
                            <td scope="col">{{$item->expiry_date?dateToString($item->expiry_date):'n/a'}}</td>
                            <td scope="col">{{$item->unit_cost_price}}</td>
                            <td scope="col">{{$item->unit_sales_price??'n/a'}}</td>
                            <td scope="col">{{$item->discount??'n/a'}}</td>
                            <td scope="col">{{$item->tax_vat??'n/a'}}</td>
                            <td scope="col">{{$item->item_total}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- bottom table section -->
        <div>
            <div class="row">
                <div class="col-md-6 mt-2">
                    <div class="">
                        <span style="font-weight: bold;">Remarks</span>
                        {{$entry->comments}}
                    </div>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless text-dark">
                        <tr>
                            <td class="font-weight-bold">Gross total</td>
                            <td>{{$entry->gross_total}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Total Disc</td>
                            <td>{{$entry->total_discount}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Taxable Amount</td>
                            <td>{{$entry->taxable_amount}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Tax Total</td>
                            <td>{{$entry->tax_total}}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold">Net Amount</td>
                            <td>{{$entry->net_amount}}</td>
                        </tr>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
