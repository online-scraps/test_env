@extends(backpack_view('blank'))


@push('after_styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nepali.datepicker.v2.2.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />

    <style>
        .select2-selection__rendered {
            line-height: 31px !important;
        }

        .select2-container .select2-selection--single {
            height: 35px !important;
        }

        .select2-selection__arrow {
            height: 34px !important;
        }

        .bg-default {
            background: rgba(27, 42, 78, 0.1) !important;
        }
    </style>
@endpush

@section('header')
    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
            <small>{!! $crud->getSubheading() ?? trans('backpack::crud.add') . ' ' . $crud->entity_name !!}.</small>

            @if ($crud->hasAccess('list'))
                <small><a style="color:white" href="{{ url($crud->route) }}" class="d-print-none font-sm"><i
                            class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i>
                        {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
            @endif
        </h2>
    </section>
@endsection
@section('content')
    <div class="billing_navbar">
        <div class="billing_nav">
            <div class="heading">
                <h1>Sales Order </h1>
            </div>
            </form>
            <div class="header_icons">
                <a href="#" class='icon-btn' id="customer-div" data-toggle="tooltip" data-placement="top"
                    title="Click here to Search Existing Customer"><i class="fa-brands fa-searchengin"></i></a>

                    {{-- reload page to create sales  --}}
                <a href="{{ url($crud->route) . '/create' }}" class='icon-btn'><i class="fa fa-plus"
                        aria-hidden="true"></i></a>

                {{-- redirect home  --}}
                <a href="{{ url('/') }}" class='icon-btn'><i class="fa fa-home" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>


    <div class="modal fade bd-example-modal-xl" id="previous_bill_modal" tabindex="-1" role="dialog" aria-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="container">
                        <div class="row">
                            <h3>Previous Bill</h3>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="text" class="form-control p-1" id="getHistory" placeholder="Mobile/Bill"
                                        size="1">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="date" class="form-control p-1" id="sl_history_from"
                                        placeholder="From date" size="1">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <input type="date" class="form-control p-1" id="sl_history_to" placeholder="To date"
                                        size="1">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="input-group mb-3">
                                    <label class="input-group-text" for="bill_status">Status</label>
                                    <select class="form-select" id="ayment_type">
                                        <option value="1" selected>Created</option>
                                        <option value="2">Completed</option>
                                        <option value="2">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <button id="sl_history_search_btn" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="modal_table_content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="sl_history_fetch" class="btn btn-success">Fetch</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal for adding item to the Previous bill --}}
    <form id="salesForm" action="{{ url($crud->route) }}" method="POST">
        @csrf

        {{-- end of the modal content --}}

        <div class="billing-form main-container">
            <div class="row mt-3">
                <div class="col-xl-4 col-md-4 col-sm-6">
                    <div class="input-group mb-3" id="billDiv">
                        <input type="hidden" name="hidden_bill_type" id="hidden-bill-type">
                        <label class="input-group-text" for="bill_type">Bill Type</label>
                        <select class="form-select" id="bill_type" name="bill_type">
                            <option value="1" selected>Individual</option>
                            <option value="2">Corporate</option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Address</span>
                        <input type="text" class="form-control" id="address" required name="address"
                            placeholder="address">
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Bill Date</span>
                        <input type="date" name="bill_date_ad" readonly class="form-control" placeholder="date">
                    </div>
                    <div id="pan_vat_field" class="input-group mb-3">
                        <span class="input-group-text">Pan/Vat</span>
                        <input type="text" class="form-control" id="pan_vat" name="pan_vat"
                            placeholder="Pan/Vat No">
                    </div>
                </div>
                <div class="col-xl-4 col-md-4 col-sm-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Buyer Name</span>
                        <input type="hidden" name="customer_id" id="hidden_customer">
                        <input type="text" class="form-control" id="full_name" name="full_name"
                            placeholder="Buyer" />
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Contact No</span>
                        <input type="number" class="form-control" id="contact_number" name="contact_number"
                            placeholder="Contact">
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">Transaction Date</span>
                        <input type="date" name="transaction_date_ad"
                            value="{{ dateToString(\Carbon\Carbon::now()) }}" class="form-control" placeholder="date">
                    </div>
                    <div id="company_field" class="input-group mb-3">
                        <span class="input-group-text">Company Name</span>
                        <input type="text" class="form-control" id="company_name" name="company_name"
                            placeholder="Company Name">
                    </div>
                </div>
                <div class="col-xl-4 col-md-4 col-sm-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Sales Order No</span>
                        <input type="text" class="form-control" readonly name="bill_no" placeholder="SO No.">
                    </div>
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="salesDiscountMode">Discount Mode</label>
                        <select class="form-select" name="discount_type" id="salesDiscountMode">
                            @foreach ($discount_modes as $mode)
                                <option value="{{ $mode->id }}">{{ $mode->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <span>Item Wise Discount</span>
                        <input type="checkbox" checked id="discountCheckbox" class="mt-2 mx-2">
                    </div>

                </div>
            </div>
        </div>

        {{-- End of upper form filter design? --}}
        <div class="table-responsive">
            <table class="table" style="min-width: 1200px">
                <thead>
                    <tr class="text-white" style="background-color:#192840">
                        {{-- <th scope="col">S.No</th> --}}
                        <th scope="col">Code/Model Name</th>
                        <th scope="col">Available Qty</th>
                        <th scope="col" style="min-width: 120px;">Qty</th>
                        <th scope="col">Unit </th>
                        <th scope="col">Unit Price</th>
                        <th scope="col">Disc </th>
                        <th scope="col">Tax/Vat</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="sales-table">
                    <tr>
                        {{-- <th scope="row">1</th> --}}
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control p-1 salesItemStock" name="item_id[1]"
                                    placeholder="Search Item" id="salesItemStock-1" data-cntr="1">
                                <input type="hidden" name="itemSalesHidden[1]" class="itemSalesHidden">

                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input class="form-control p-1 salesAvailableQty" name="sales_availableQty[1]"
                                    type="text" value="" id="salesAvailableQty-1" data-cntr="1" readonly
                                    size="1" />
                        </td>
        </div>
        
        <td>
            <div class="input-group">
                <input type="text" class="form-control p-1 salesAddQty" name="total_qty[1]" id="salesAddQty-1"
                    placeholder="Qty" size="1" data-cntr="1"
                    style="border-top-left-radius: 0;border-bottom-left-radius: 0;">
            </div>
        </td>
        <td>
            <div class="input-group">
                <input type="text" class="form-control p-1 salesUnit" id="salesUnit-1" name="unit_id[1]"
                    placeholder=" Unit" readonly size="1" data-cntr="1">
            </div>
        </td>
        <td>
            <div class="input-group">
                <input type="text" class="form-control p-1 salesUnitPrice" id="salesUnit-1" name="unit_cost_price[1]"
                    id="salesUnitPrice-1" placeholder="unit Price" readonly size="1" data-cntr="1">
            </div>
        </td>
        <td>
            <div class="input-group">
                <input type="float" class="form-control p-1 salesDiscount" name="item_discount[1]"
                    id="salesDiscount-1" placeholder="Discount" size="1" data-cntr="1">
            </div>
        </td>
        <td> <input type="text" class="form-control p-1 salesTax" name="tax_vat[1]" id="salesTax-1" data-cntr="1"
                readonly size="1"></td>
        <td><input type="text" class="form-control p-1 salesAmount" name="item_total[1]" id="salesAmount-1"
                data-cntr="1" readonly size="1"></td>
        <td>
            <i class="fa fa-plus p-1 fireRepeaterClick" aria-hidden="true"></i>
            <i class="fa fa-trash p-1 destroyRepeater d-none" data-cntr="1" id="itemDestroyer-1"
                aria-hidden="true"></i>
        </td>
        </tr>
        <tr class="d-none" id="repeater">
            {{-- <th scope="row">1</th> --}}
            <td>
                <div class="input-group">
                    <input type="text" class="form-control p-1 salesItemStock" placeholder="Search Item">
                    <input type="hidden" class="itemSalesHidden">

                </div>
            </td>
            <td>
                <div class="input-group">
                    <input class="form-control p-1 salesAvailableQty" size="1" type="text" value=""
                        readonly />
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="text" class="form-control p-1 salesAddQty" placeholder="Qty" size="1" 
                    style="border-top-left-radius: 0;border-bottom-left-radius: 0;">
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="text" class="form-control p-1 salesUnit" placeholder=" Unit" readonly
                        size="1">
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="text" name="salesUnitPrice" class="form-control p-1 salesUnitPrice" readonly
                        placeholder="unit Price" size="1">
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="text" class="form-control p-1 salesDiscount" placeholder="Discount" size="1">
                </div>
            </td>
            <td>
                <div class="input-group"> <input type="text" class="form-control p-1 salesTax" readonly
                        size="1">
                </div>
            </td>
            <td>
                <div class="input-group"><input type="text" class="form-control p-1 salesAmount" readonly
                        size="1">
                </div>
            </td>
            <td>
                <i class="fa fa-plus p-1 fireRepeaterClick" aria-hidden="true"></i>
                <i class="fa fa-trash p-1 destroyRepeater" aria-hidden="true"></i>
            </td>
        </tr>
        </tbody>
        </table>
        </div>
        {{-- End of item search design --}}
        <hr>

        <div class="main-container mb-12">
            <div class="row">
                <div class="col-xl-6 col-md-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Discount</span>
                        <input type="text" class="form-control" id="flatDiscount" disabled="true" name="discount"
                            placeholder="Discount">
                    </div>
                    <div class="input-group mb-3">

                        <label class="input-group-text" for="discount_approver">Discount Approver</label>
                        <select class="form-select" name="discount_approver_id" id="discount_approver">
                            @foreach ($discount_approver as $mode)
                                <option value="{{ $mode->id }}">{{ $mode->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="input-group mb-3">
                        <span class="input-group-text">Remarks</span>
                        <input type="text" class="form-control" name="remarks" placeholder="Remarks">
                    </div>
                </div>
                
                <div class="col-xl-6 col-md-12">
                    <table class="table table-sm table-bordered">
                        <tbody>
                            <tr>
                                <th class="bg-primary text-white">Sub Total</th>
                                <td><input id="sl_gross_total" type="text" name="gross_amt" class="form-control"
                                        readonly></td>
                            </tr>
                            <tr>
                                <th class="bg-primary text-white">Total Discount</th>
                                <td> <input id="sl_discount_amount" type="text" name="discount_amt"
                                        class="form-control" readonly></td>
                            </tr>
                            <tr>
                                <th class="bg-primary text-white">Taxable Amount</th>
                                <td><input id="sl_taxable_amnt" type="text" name="taxable_amt" class="form-control"
                                        readonly></td>
                            </tr>
                            <tr>
                                <th class="bg-primary text-white">Tax Total</th>
                                <td><input id="sl_tax_amount" type="text" name="total_tax_vat" class="form-control"
                                        readonly></td>
                            </tr>
                            <tr>
                                <th class="bg-primary text-white">Net Amount</th>
                                <td> <input id="sl_net_amount" type="text" name="net_amt" class="form-control"
                                        readonly></td>
                            </tr>
                            <tr id="sl_due_amt_field">
                                <th class="bg-primary text-white">Due</th>
                                <td> <input id="sl_due_amount" readonly type="text" name="due_amt"
                                        class="form-control"></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="col">
                        <input id="status" type="hidden" name="status_id" value="">
                        <button id="save" type="submit" class="btn btn-secondary st_save">Draft</button>
                        <button id="approve" type="submit" class="btn btn-success st_approve">Approve</button>
                    </div>
                </div>

            </div>
        </div>
        {{-- End of Price table --}}


        {{-- Customer Modal --}}


    </form>

    @include('customAdmin.salesordervoucher.partials.modal')
@endsection
@push('after_scripts')
    @include('customAdmin.salesordervoucher.partials.script');
@endpush
