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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="stylesheet" href="{{ asset('css/nepali.datepicker.v2.2.min.css') }}">




@endpush

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

@section('content')
    <form action="{{ url($crud->route).'/'.$grn->id}}" role="form" method="PUT" id="grn_form">
    @method('PUT')
   @csrf
    <div class="billing-form main-container">
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="input-group mb-3">
                    <label class="input-group-text" for="grn_type">Type</label>
                    <select class="form-select grn_type" id="grn_type" name="grn_type_id">
                        <option value="0" selected>--select--</option>
                            @foreach($grn_types as $type)
                            <option value="{{$type->id}}" {{ $grn->grn_type_id== $type->id? 'selected' : ''}}>{{$type->name_en}}</option>
                            @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group mb-3">
                    <span class="input-group-text">Store</span>
                    <select class="form-select" id="store" name="store_id" readonly>
                            <option value="{{$grn->store_id}}" selected>{{$grn->StoreEntity->name_en}}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group mb-3">
                    <label class="input-group-text" for="supplier">Supplier</label>
                    <select class="form-select" id="supplier" name="supplier_id" readonly>
                        <option value="">--select supplier--</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{$supplier->id}}" {{ $grn->supplier_id== $supplier->id? 'selected' : ''}}>{{$supplier->name_en}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group mb-3">
                        <span class="input-group-text">PO No</span>
                        <input type="text" class="form-control" id="purchase_order" value="{{$grn->purchase_order_id}}" name="purchase_order_id" placeholder="PO NO">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#grn_modal">Add</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group mb-3">
                    <span class="input-group-text">PO Date</span>
                    <input type="date" value="{{$grn->po_date}}" class="form-control" id="po_date" name="po_date">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group mb-3">
                    <span class="input-group-text">Invoice No</span>
                    <input type="text" class="form-control" value="{{$grn->invoice_no}}" id="invoice_no" name="invoice_no"placeholder="Invoice No">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group mb-3">
                    <span class="input-group-text">Invoice Date</span>
                    <input type="date" class="form-control" id="invoice_date" value="{{$grn->invoice_date}}" name="invoice_date">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group mb-3">
                    <span class="input-group-text">DC No</span>
                    <input type="text" class="form-control" id="dc_no" value="{{$grn->dc_no}}" name="dc_no"placeholder="Delivery Chalan">
                </div>
            </div>
            <div class="col-md-4">
                <div class="input-group mb-3">
                    <span class="input-group-text">DC Date</span>
                    <input type="date" class="form-control" id="dc_date"value="{{$grn->dc_date}}" name="dc_date">
                </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-sm-6">
                <div class="input-group mb-3">
                    <label class="input-group-text" for="batch_number">Batch number</label>
                    <select class="form-select" id="batch_number" name="batch_number" {{ (count($batchNumbers)<1) ? 'disabled' : '' }}>
                        @foreach ($batchNumbers as $code => $codeId)
                            <option value="{{ $codeId }}">{{ $code }}</option>
                        @endforeach
                    </select>
                    <span class="input-group-text bg-primary text-white" id="sequenceCreateBtn" onclick="loadModal(this, '1')">
                        +
                    </span>
                </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-sm-6">
                <div class="input-group mb-3">
                    <label class="input-group-text" for="grn_no">GRN Number</label>
                    <select class="form-select" id="grn_no" name="grn_no" {{ (count($grnNumbers) < 1) ? 'disabled' : '' }}>
                        @foreach ($grnNumbers as $code => $codeId)
                            <option value="{{ $codeId }}" {{ ($codeId == $grn->grn_no) ? 'selected' : ''  }}>{{ $code }}</option>
                        @endforeach
                    </select>
                    <span class="input-group-text bg-primary text-white" id="sequenceCreateBtn" onclick="loadModal(this, '2')">
                        +
                    </span>
                </div>
            </div>

        </div>
    {{-- End of upper form filter design? --}}
    </div>
    <div class="table-responsive">
        <table class="table" id="grn-table">
            <thead>
            <tr class="text-white" style="background-color: #192840">
                            <th scope="col" style="white-space: nowrap;">Code/Product Name</th>
                            <th scope="col" style="white-space: nowrap;">PO-Qty</th>
                            <th scope="col" style="white-space: nowrap;">Rec-Qty</th>
                            <th scope="col" style="white-space: nowrap;">Invoice-Qty</th>
                            <th scope="col" style="white-space: nowrap;">Free Qty</th>
                            <th scope="col" style="white-space: nowrap;">Total Qty</th>
                            <th scope="col" style="white-space: nowrap;">Exp-Date</th>
                            <th scope="col" style="white-space: nowrap;">purchase Price</th>
                            <th scope="col" style="white-space: nowrap;">Sales Price</th>
                            <th scope="col" style="white-space: nowrap;">Discount Mode</th>
                            <th scope="col">Discount </th>
                            <th scope="col">Tax/Vat </th>
                            <th scope="col" style="white-space: nowrap;">Amount</th>
                            <th scope="col" style="min-width: 100px;">Action</th>
                    </tr>
            </thead>
            <tbody id="grn-table">
                    @php $count=$grn->grn_items->count(); @endphp
                    @foreach($grn->grn_items as $key => $item)
                    @php $key++; $mstItem = $item->itemEntity; @endphp
                    <tr class="item-row" id="item-row-1" tr-id="{{$key}}">
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control p-1 grn_item_name" id="grn_item_name-{{$key}}" tr-id="{{$key}}" name="items_id[{{$key}}]" value="{{$mstItem->code.':'.$mstItem->name}}" placeholder="Search Item">
                                <input type="hidden" id="grn_item_name_hidden-{{$key}}" value="{{ $item->mst_items_id}}" name="grn_item_name_hidden[{{$key}}]" class="grn_item_name_hidden">
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 grn_po_qty" id="grn_po_qty-{{$key}}" tr-id="{{$key}}" name="purchase_qty[{{$key}}]" value="{{$item->purchase_qty}}"  placeholder="PO-Qty" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 grn_rec_qty" id="grn_rec_qty-{{$key}}" tr-id="{{$key}}" name="received_qty[{{$key}}]" value="{{$item->received_qty}}"   placeholder="REC-Qty" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 grn_invoice_qty"id="grn_invoice_qty-{{$key}}" tr-id="{{$key}}" name="invoice_qty[{{$key}}]" value="{{$item->invoice_qty}}"  placeholder="INV-Qty" size="1" >
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 grn_free_qty" id="grn_free_qty-{{$key}}" tr-id="{{$key}}" name="free_qty[{{$key}}]" value="{{$item->free_qty}}"  placeholder="FREE-Qty" size="1">
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 grn_total_qty" id="grn_total_qty-{{$key}}" tr-id="{{$key}}" name="total_qty[{{$key}}]" value="{{$item->total_qty}}"  placeholder="T-Qty" size="1" readonly>
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="date" class="form-control p-1 itemExpiry"   id="itemExpiry-{{$key}}" tr-id="{{$key}}" name="expiry_date[{{$key}}]" value="{{$item->expiry_date}}"  placeholder="Expiry">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control p-1 grn_purchase_price" id="grn_purchase_price-{{$key}}" tr-id="{{$key}}" name="purchase_price[{{$key}}]" value="{{$item->purchase_price}}"   placeholder="P-Price" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control p-1 grn_sales_price" id="grn_sales_price-{{$key}}" tr-id="{{$key}}" name="sales_price[{{$key}}]" value="{{$item->sales_price}}"  placeholder="S-Price" size="1">
                            </div>
                        </td>

                        <td>
                            <div class="input-group mb-3">
                                <select class="form-select grn_discount_mode" id="grn_discount_mode-{{$key}}" tr-id="{{$key}}" name="discount_mode_id[{{$key}}]" value="{{$item->discount_mode_id}}" >
                                    @foreach($discount_modes as $mode)
                                    <option value="{{$mode->id}}" selected>{{$mode->name_en}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 grn_discount" id="grn_discount-{{$key}}" tr-id="{{$key}}" name="discount[{{$key}}]" value="{{$item->discount}}" placeholder="Discount" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 grn_tax_vat" id="grn_tax_vat-{{$key}}" tr-id="{{$key}}" name="tax_vat[{{$key}}]" value="{{$item->tax_vat}}" placeholder="Tax-Vt" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 grn_item_amount" id="grn_item_amount-{{$key}}" tr-id="{{$key}}" name="item_amount[{{$key}}]" value="{{$item->item_amount}}" placeholder="Amount" size="1">
                            </div>
                        </td>

                        <td>
                            <i class="fa fa-plus p-1 fireRepeaterClick"  aria-hidden="true"></i>
                            <i class="fa fa-trash p-1 destroyRepeater  {{$loop->count == 1 ? 'd-none':'' }}" tr-id="{{$key}}" id="itemDestroyer-{{$key}}" aria-hidden="true"></i>
                            <i type="button" class="fa fa-history p-1 grn_history_icon" tr-id="{{$key}}" id="grn_history_icon-{{$key}}" data-toggle="modal" aria-hidden="true"></i>
                        </td>
                    </tr>
                    @endforeach


                    <!-- Repeater Row -->
                    <tr class="item-row  d-none" id="repeater">
                        <!-- <th scope="row">1</th> -->
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control p-1 grn_item_name"   placeholder="Search Item">
                                <input type="hidden" class="grn_item_name_hidden">
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 grn_qty"   placeholder="PO-Qty" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 grn_rec_qty"   placeholder="REC-Qty" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 grn_invoice_qty"   placeholder="INV-Qty" size="1" >
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 grn_free_qty"    placeholder="FREE-Qty" size="1">
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 grn_total_qty"   placeholder="T-Qty" size="1" readonly>
                            </div>
                        </td>

                        <td>
                            <div class="input-group">
                                <input type="date" class="form-control p-1 itemExpiry"   placeholder="Expiry">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control p-1 grn_purchase_price"   placeholder="P-Price" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control p-1 grn_sales_price"    placeholder="S-Price" size="1">
                            </div>
                        </td>

                        <td>
                            <div class="input-group mb-3">
                                <select class="form-select grn_discount_mode"  >
                                    @foreach($discount_modes as $mode)
                                        <option value="{{$mode->id}}" selected>{{$mode->name_en}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 grn_discount"   placeholder="Discount" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 grn_tax_vat"   placeholder="Tax-Vt" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" min="0" class="form-control p-1 grn_item_amount"   placeholder="Amount" readonly size="1">
                            </div>
                        </td>

                        <td>
                            <i class="fa fa-plus p-1 fireRepeaterClick"  aria-hidden="true"></i>
                            <i class="fa fa-trash p-1 destroyRepeater" aria-hidden="true"></i>
                            <i type ='button' class="fa fa-history p-1 grnItemHistory" data-toggle="modal"  aria-hidden="true"></i>

                        </td>

                    </tr>
                    <!-- End of Repeater Row -->
                </tbody>
        </table>
    </div>
    {{-- End of item search design --}}
    <div class="main-container">
        <div class="row">
            <div class="col-6">
                <div class="input-group mb-3">
                    <span class="input-group-text">Comments</span>
                    <textarea type="text" class="form-control"id="comments" name="comments" placeholder="comments" rows="3"></textarea>
                </div>
            </div>
            <div class="col-6">
                <table class="table table-sm table-bordered">
                    <tbody>
                        <tr>
                        <th class="bg-primary text-white">Sub Total</th>
                        <td >
                            <input id="grn_gross_amount" type="text" name="gross_amt" value="{{$grn->gross_amt}}" class="form-control" readonly>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-primary text-white">Total Discount</th>
                        <td>
                            <input id="grn_discount_amount" type="text" name="discount_amt" value="{{$grn->discount_amt}}" class="form-control" readonly>
                        </td>
                    </tr>
                    <!-- <tr>
                        <th class="bg-primary text-white">Taxable Amount</th>
                        <td >
                            <input id="grn_taxable_amnt" type="text" name="taxable_amount" class="form-control" readonly>
                        </td>

                    </tr> -->
                    <tr>
                        <th class="bg-primary text-white">Taxable Amount</th>
                        <td >
                            <input id="grn_tax_amount" type="text" name="taxable_amt" class="form-control" readonly>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-primary text-white">Tax Total</th>
                        <td >
                            <input id="grn_tax_amount" type="text" name="tax_amt" value="{{$grn->tax_amt}}" class="form-control" readonly>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-primary text-white">Other Charges</th>
                        <td >
                            <input id="grn_other_charges" type="text" value="{{$grn->other_charges}}" name="other_charges" class="form-control">
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-primary text-white">Net Amount</th>
                        <td>
                            <input id="grn_net_amount" type="text" name="net_amount" value="{{$grn->net_amt}}" class="form-control" readonly>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- End of Price table --}}
    <div class="main-container mb-4">
        <div class="d-flex justify-content-end ">
                <input id="status" type="hidden" name="status_id" value="">
                <button id="save" type="submit" class="btn btn-primary st_save me-1">Save</button>
                    @if(backpack_user()->is_po_approver)
                        <button id="approve" type="submit" class="btn btn-success st_approve me-1">Approve</button>
                    @endif
                <button class="btn btn-danger">Cancel</button>
        </div>
    </div>
</form>


@include('customAdmin.grn.partials.modals')
@include('customAdmin.partial._inlineSequenceCreate')
{{-- end of the modal content --}}

@endsection

@include('customAdmin.grn.partials.scripts')
