@extends(backpack_view('blank'))

@section('header')

    <section class="main-container">
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>

            @if ($crud->hasAccess('list'))
                <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
            @endif
        </h2>
    </section>
@endsection
@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="{{url('css/style.css')}}">


<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="/resources/demos/style.css">
<title>Purchase Order</title>

</head>


<body>
{{-- start of Purchase order history modal --}}
    <div class="modal fade bd-example-modal-xl" id="purchase_order_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="container">
                        <div class="row">
                            <h3>Purchase History of <span id="item_name_modal"></span></h3>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="input-group">
                                    <span class="input-group-text">From</span>
                                    <input type="date" class="form-control" id="po_history_from" value="{{generate_date_with_extra_days(dateToday(),7)}}" name="po_history_from">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <span class="input-group-text">To</span>
                                    <input type="date" class="form-control" id="po_history_to" value="{{convert_ad_from_bs()}}" name="po_history_to">
                                </div>
                            </div>
                            <div class="col">
                                <button class="btn btn-success " id="po_history_fetch_btn">Fetch</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="modal_table_content"></div>
                </div>
                <div class="modal-footer">
                    <h5 class="left">Total Purchase Qty: <span id="total_qty_history"></span></h5>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- end of the modal content --}}
    <form action="{{ url($crud->route).'/'.$po->id}}" role="form" method="PUT" id="po_form">
        @method('PUT')

        <div class="po_navbar">
            <div class="billing_nav">
                <div class="heading">
                    <h1></h1>
                </div>
                <!-- <h5 class="heading">Status:</h5> -->
                <div class="bill_icons">
                    <a href="{{ url($crud->route) }}"><button class='icon-btn'><i class="fa fa-plus" aria-hidden="true"></i></button></a>
                    <button class='icon-btn'><i class="fa fa-home" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
        <div class="">
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="po_type">PO type</label>
                        <select class="form-select" id="po_type" name="purchase_order_type_id">
                            <option value=''>Select</option>
                            @foreach($po_types as $type)
                            <option value="{{$type->id}}" {{ $po->purchase_order_type_id== $type->id? 'selected' : ''}}>{{$type->name_en}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="supplier">Supplier</label>
                        <select class="form-select" id="supplier" name="supplier_id" disabled>
                            <option value="">--select supplier--</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{$supplier->id}}" {{ $po->supplier_id== $supplier->id? 'selected' : ''}}>{{$supplier->name_en}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Purchase Order No</span>
                        <input type="text" class="form-control" id="purchase_order" name="purchase_order_num" value="{{$po->purchase_order_num}}" placeholder="PO No" readonly>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Store</span>

                        <select class="form-select" id="store" name="store_id" disabled>
                            <option value="{{$po->store_id}}" selected>{{$po->StoreEntity->name_en}}</option>

                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="requested_store">Requested Store</label>
                        <select class="form-select" id="requested_store" placeholder="helloo" name="requested_store_id" disabled>
                            <option value="">--select requested store--</option>
                            @foreach( $requested_store as $store)
                            <option value="{{$store->id}}" {{ $po->requested_store_id== $store->id ? 'selected' : ''}}>{{$store->name_en}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Contact No</span>
                        <input type="text" class="form-control" value="" id="phone" name="phone" placeholder="Contact No" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Contact Email</span>
                        <input type="text" class="form-control" value="" id="email" name="email" placeholder="Email" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Expected Delivery</span>
                        <input type="date" class="form-control" id="expected_delivery" min="<?php echo dateToday(); ?>" value="{{$po->expected_delivery}}" name="expected_delivery">
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="purchase_order_num">PO Number</label>
                        <select class="form-select" id="purchase_order_num" name="purchase_order_num">
                            @foreach ($purchaseOrderNumbers as $code => $codeId)
                                <option value="{{ $codeId }}" {{ ($codeId == $stock->purchase_order_num) ? 'selected' : ''  }}>{{ $code }}</option>
                            @endforeach
                        </select>
                        <span class="input-group-text bg-primary text-white" onclick="loadModal(this, '4')">
                            +
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- End of upper form filter design --}}
        <div class="table-responsive">
            <table class="table" style="min-width: 1200px;">
                <thead>

                    <tr class="text-white" style="background-color: #192840">
                        <!-- <th scope="col">S.No</th> -->
                        <th scope="col">Model Name</th>
                        <th scope="col">Purchase Qty</th>
                        <th scope="col">Free Qty</th>
                        <th scope="col">Total Qty</th>
                        <th scope="col" style="white-space: nowrap">Disc Mode</th>
                        <th scope="col">Discount </th>
                        <th scope="col">Tax Vat </th>
                        <th scope="col">Purchase Price</th>
                        <th scope="col">Sales Price</th>
                        <th scope="col">Item Amount</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="po-table">
                    @php $count=$po->purchase_items->count(); @endphp
                    @foreach($po->purchase_items as $key => $item)
                    @php $key++; $mstItem = $item->itemEntity; @endphp

                    <tr class="item-row" id="item-row-1" tr-id="1">
                        <!-- <th scope="row">1</th> -->
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control p-1 po_item_name" id="po_item_name-{{$key}}" tr-id="{{$key}}" name="items_id[{{$key}}]" value="{{$mstItem->code.':'.$mstItem->name}}" placeholder="Search Item">
                                <input type="hidden" id="po_item_name_hidden-{{$key}}" value="{{ $item->items_id}}" name="po_item_name_hidden[{{$key}}]" class="po_item_name_hidden">
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control p-1 po_purchase_qty" min='0' id="po_purchase_qty-{{$key}}" tr-id="{{$key}}" value="{{$item->purchase_qty}}" name="purchase_qty[{{$key}}]" placeholder="Add Qty" size="1">
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control p-1 po_free_qty" min='0' id="po_free_qty-{{$key}}" tr-id="{{$key}}" value="{{$item->free_qty}}" name="free_qty[{{$key}}]" placeholder="Free item" size="1">
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control p-1 po_total_qty" id="po_total_qty-{{$key}}" tr-id="{{$key}}" value="{{$item->total_qty}}" name="total_qty[{{$key}}]" placeholder="Add Qty" size="1" readonly>
                            </div>
                        </td>

                        <td>
                            <div class="input-group mb-3">
                                <select class="form-select po_discount_mode" id="po_discount_mode-{{$key}}" tr-id="{{$key}}" name="discount_mode_id[{{$key}}]">
                                    @foreach($discount_modes as $mode)
                                    <option value="{{$mode->id}}" {{ $item->discount_mode_id== $mode->id ? 'selected' : ''}}>{{$mode->name_en}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control p-1 po_discount" min='0' id="po_discount-{{$key}}" tr-id="{{$key}}" min=0 max=100 value="{{$item->discount}}" name=" discount[{{$key}}]" placeholder="Discount" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" id="po_tax_vat-{{$key}}" class="form-control p-1 po_tax_vat" tr-id="{{$key}}" placeholder="" value="{{$item->tax_vat}}" name='tax_vat[{{$key}}]' size="1" readonly>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control p-1 po_purchase_price" min='0' id="po_purchase_price-{{$key}}" tr-id="{{$key}}" value="{{$item->purchase_price}}" name="purchase_price[{{$key}}]" placeholder="Purchase Price" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control p-1 po_sales_price" min='0' id="po_sales_price-{{$key}}" tr-id="{{$key}}" value="{{$item->sales_price}}" name="sales_price[{{$key}}]" placeholder="Sales Price" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control p-1 po_item_amount" id="po_item_amount-{{$key}}" tr-id="{{$key}}" value="{{$item->item_amount}}" name="item_amount[{{$key}}]" placeholder="Item Amount" size="1" readonly>
                            </div>
                        </td>
                        <td>
                            <i class="fa fa-plus p-1 fireRepeaterClick" id="" aria-hidden="true"></i>
                            <i class="fa fa-trash p-1 destroyRepeater {{$count>1?'':'d-none'}}" id="itemDestroyer-1" aria-hidden="true"  tr-id='1'></i>
                            <i  class="fa fa-history p-1 po_history_icon" tr-id="1" id="po_history_icon-1" data-toggle="modal" data-target="#purchase_order_modal" aria-hidden="true"></i>
                        </td>
                    </tr>
                    @endforeach

                    <!-- Repeater Row -->
                    <tr class="item-row  d-none" id="repeater">
                        <!-- <th scope="row">1</th> -->
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control p-1 po_item_name" placeholder="Search Item">
                                <input type="hidden" class="po_item_name_hidden">
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control p-1 po_purchase_qty" placeholder="Add Qty" size="1">
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control p-1 po_free_qty" placeholder="Free item" size="1">
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control p-1 po_total_qty" placeholder="Add Qty" size="1" readonly>
                            </div>
                        </td>

                        <td>
                            <div class="input-group mb-3">
                                <select class="form-select po_discount_mode">
                                    @foreach($discount_modes as $mode)
                                    <option value="{{$mode->id}}">{{$mode->name_en}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control p-1 po_discount" placeholder="Discount" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control p-1 po_tax_vat" placeholder="" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control p-1 po_purchase_price" placeholder="Purchase Price" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control p-1 po_sales_price" placeholder="Sales Price" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control p-1 po_item_amount" placeholder="Item Amount" size="1" readonly>
                            </div>
                        </td>
                        <td>
                            <i class="fa fa-plus p-1 fireRepeaterClick" id="" aria-hidden="true"></i>
                            <i class="fa fa-trash p-1  destroyRepeater" aria-hidden="true"></i>
                            <i type="button" class="fa fa-history p-1 po_history_icon" data-toggle="modal"  aria-hidden="true"></i>
                        </td>
                    </tr>
                    <!-- End of Repeater Row -->
                </tbody>
            </table>
        </div>


        {{-- End of item search design --}}
        <hr>
        <div class="">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Created By</span>
                        <input type="text" class="form-control" id="created_by" value="{{$po->createdByEntity->name}}" name="created_by" placeholder="Created By" readonly>
                    </div>
                    <!-- <div class="col">
                        <div class="input-group mb-3">
                            <span class="input-group-text">Approved By</span>
                            <select class="form-select" id="" name="approved_by">
                                <option value="1" selected>Ramesh</option>
                                <option value="2">Suresh</option>
                            </select>
                        </div>
                    </div> -->
                    <div class="input-group mb-3">
                        <span class="input-group-text">Comments</span>
                        <input type="text" class="form-control" id="comments" name="comments" value="{{$po->comments}}" placeholder="comments">
                    </div>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-bordered">
                        <tbody>
                            <tr>
                                <th class="bg-primary text-white">Gross Amount</th>
                                <td id="" class="" name=""><input id="po_gross_amount" value="{{$po->gross_amt}}" name="gross_amt" class="form-control" readonly></td>
                            </tr>
                            <tr>
                                <th class="bg-primary text-white">Discount Amount</th>
                                <td id="" name=""><input id="po_discount_amount" value="{{$po->discount_amt}}" name="discount_amt" class="form-control" readonly></td>
                            </tr>
                            <tr>
                                <th class="bg-primary text-white">Tax Amount</th>
                                <td id="" name=""><input id="po_tax_amount" value="{{$po->tax_amt}}" name="tax_amt" class="form-control" readonly></td>
                            </tr>
                            <tr>
                                <th class="bg-primary text-white">Other Charges</th>
                                <td id="" name="">
                                    <input type="number" class="form-control" id="po_other_charges" value="{{$po->other_charges}}" name="other_charges" placeholder="other charges">
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-primary text-white">Net Amount</th>
                                <td id="" name=""><input id="po_net_amount" value="{{$po->net_amt}}" name="net_amt" class="form-control" readonly></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- End of Price table --}}
        <div class="main-container mb-4">
            <div class="d-flex justify-content-end">
                @if( $po->status_id !== \App\Models\SupStatus::APPROVED)
                <input id="status" type="hidden" name="status_id" value="">
                <button id="save" type="submit" class="btn btn-primary st_save">Save</button>
                @if(backpack_user()->is_po_approver)
                <button id="approve" type="submit" class="btn btn-success st_approve">Approve</button>
                @endif
                @endif
                <button id="cancelBtn" class="btn btn-danger po_cancel_btn">Cancel</button>
            </div>
        </div>
    </form>
    @include('customAdmin.partial._inlineSequenceCreate')

    @endsection


    @section('after_scripts')
    @include('customAdmin.purchaseOrder.po_scripts');

    @endsection
