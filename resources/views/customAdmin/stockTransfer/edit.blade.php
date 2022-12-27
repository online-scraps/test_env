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
    @include('customAdmin.stockMgmt.partials.styles')
@endpush

@section('header')
    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
            <small>{!! $crud->getSubheading() ?? trans('backpack::crud.edit') . ' ' . $crud->entity_name !!}.</small>

            @if ($crud->hasAccess('list'))
                <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i
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
                <h1>Billing</h1>
            </div>
            <div class="header_icons">
                <a href="#" class='icon-btn' id="customer-div" data-toggle="tooltip" data-placement="top"
                    title="Click here to Search Existing Customer"><i class="fa-brands fa-searchengin"></i></a>
                <a href="{{ url($crud->route) . '/create' }}" class='icon-btn'><i class="fa fa-plus"
                        aria-hidden="true"></i></a>
                <a href="{{ url('/') }}" class='icon-btn'><i class="fa fa-home" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
    {{-- Modal for adding item to the Previous bill --}}


    <div class="modal fade bd-example-modal-xl" id="previous_bill_modal" tabindex="-1" role="dialog" aria-hidden="true">
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
    <div class="modal fade" id="add_stock_item_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <input type="hidden" class="barcode_item_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Scan Barcode to Add Qty in "<span id="barcodeItemName"></span>"</h5>
                </div>
                <form id="barcodeForm">
                    <div class="modal-body">
                        <select id="barcodeScanner" name="barcode_details[]" class="form-control" multiple="multiple"
                            style="width: 100%;height: auto;">
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" id="barcodeSave" class="btn btn-primary">Save changes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <form id="stockTransferForm" action="{{ url($crud->route) . '/' . $stockTransEntry->id }}" method="POST">
        @method('PUT')
        @csrf
        <div class="main-container">
            <div class="row mt-3">
                <div class="col-xl-4 col-md-4 col-sm-6">
                    <div class="input-group mb-3">
                        <input type="hidden" name="hidden_bill_type" id="hidden-bill-type">
                        <label class="input-group-text" for="bill_type">From</label>
                        <select class="form-select store_id" id="from_store_id" name="from_store_id"
                            style="border-radius: 0.25rem">
                            @foreach ($storeList as $key => $list)
                                <option value="{{ $key }}"
                                    {{ backpack_user()->hasAnyRole('', 'organizationadmin') ? '' : (backpack_user()->store_id == $key ? 'selected' : '') }}>
                                    {{ $list }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-xl-4 col-md-4 col-sm-6">
                    <div class="input-group mb-3">
                        <input type="hidden" name="hidden_bill_type" id="hidden-bill-type">
                        <label class="input-group-text" for="bill_type">To</label>
                        <select class="form-select store_id" id="to_store_id" name="to_store_id"
                            style="border-radius: 0.25rem"></select>
                    </div>
                </div>
                <div class="col-xl-4 col-md-4 col-sm-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Bill Date</span>
                        <input type="date" name="bill_date_ad" readonly class="form-control" placeholder="date">
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
                        <th class="{{ $stockTransEntry->sup_status_id == 2 ? 'd-none' : '' }}" scope="col">Total Qty
                        </th>
                        <th scope="col">Qty</th>
                        <th scope="col">Unit </th>
                        <th scope="col">Unit Price</th>
                        <th scope="col">Disc </th>
                        <th scope="col">Tax/Vat</th>
                        <th scope="col">Amount</th>
                        <th class="{{ $stockTransEntry->sup_status_id == 1 ? '' : 'd-none' }}" scope="col">Action</th>

                    </tr>
                </thead>
                <tbody id="sales-table">
                    @php
                        $count = $stockTransEntry->items->count();
                    @endphp

                    @foreach ($stockTransEntry->items as $key => $item)
                        @php
                            $key++;
                            $mstItem = $item->mstItem;
                            $itemId = json_encode($item->item_id);
                            if (isset($mstItem->itemQtyDetail->item_qty) ? ($itemQty = $mstItem->itemQtyDetail->item_qty) : ($itemQty = 0));
                            $cntr = json_encode($key);
                            $stockTransEntryItems = json_encode($stockTransEntry->items);

                        @endphp
                        <tr>
                            <td>
                                <div class="input-group">
                                    <input type="text" class="form-control p-1 salesItemStock"
                                        name="item_id[{{ $key }}]"
                                        value="{{ $mstItem->code . ':' . $mstItem->name }}"
                                        item-id="{{ $item->item_id }}" placeholder="Search Item"
                                        id="salesItemStock-{{ $key }}" data-cntr="{{ $key }}">
                                    <input type="hidden" name="itemSalesHidden[{{ $key }}]"
                                        value="{{ $item->item_id }}" class="itemSalesHidden">

                                </div>
                            </td>
                            <td class="{{ $stockTransEntry->sup_status_id == 2 ? 'd-none' : '' }}">
                                <div class="input-group">
                                    <input id="salesAvailableQty-{{ $key }}"
                                        class="form-control p-1 salesAvailableQty" value="{{ $itemQty }}"
                                        name="sales_availableQty[{{ $key }}]" type="text" value=""
                                        id="salesAvailableQty-1" data-cntr="{{ $key }}" size="1"
                                        readonly />
                                </div>
                            </td>
                            <td style="max-width:3rem">
                                <div class="input-group">
                                    <input type="number" min="0" class="form-control p-1 salesAddQty"
                                        size="1" value="{{ $item->item_qty }}"
                                        {{ $item->barcodeDetails->count() ? "readonly='true'" : '' }}
                                        name="total_qty[{{ $key }}]" id="salesAddQty-{{ $key }}"
                                        placeholder="Qty" size="1"
                                        data-cntr="{{ $key }}"{{ $multiple_barcode ? 'readonly' : '' }}>
                                    @if ($multiple_barcode)
                                        <button type="button" class="btn btn-primary btn-sm barcodeScan"
                                            id="barcodeScan-{{ $key }}" data-cntr="{{ $key }}"
                                            data-toggle="modal">+</button>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" class="form-control p-1 salesUnit"
                                        value="{{ $item->mstItem->mstUnitEntity->name_en }}"
                                        id="salesUnit-{{ $key }}" name="unit_id[{{ $key }}]" readonly
                                        placeholder=" Unit" size="1" data-cntr="{{ $key }}">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" class="form-control p-1 salesUnitPrice"
                                        value="{{ $item->item_price }}" name="unit_cost_price[{{ $key }}]"
                                        id="salesUnitPrice-{{ $key }}" readonly placeholder="unit Price"
                                        size="1" data-cntr="{{ $key }}">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" class="form-control p-1 salesDiscount"
                                        value="{{ $item->item_discount }}" name="item_discount[{{ $key }}]"
                                        id="salesDiscount-{{ $key }}" placeholder="Discount" size="1"
                                        data-cntr="{{ $key }}">
                                </div>
                            </td>
                            <td> <input type="text" class="form-control p-1 salesTax"
                                    name="tax_vat[{{ $key }}]" value="{{ $item->tax_vat }}"
                                    id="salesTax-{{ $key }}" data-cntr="{{ $key }}" readonly
                                    size="1">
                            </td>
                            <td><input type="text" class="form-control p-1 salesAmount"
                                    name="item_total[{{ $key }}]" value="{{ $item->item_total }}"
                                    id="salesAmount-{{ $key }}" data-cntr="{{ $key }}" readonly
                                    size="1">
                            </td>
                            <td class="{{ $stockTransEntry->sup_status_id == 1 ? '' : 'd-none' }}">
                                <i class="fa fa-plus p-1 fireRepeaterClick" aria-hidden="true"></i>
                                <i class="fa fa-trash p-1 destroyRepeater d-none" data-cntr="{{ $key }}"
                                    id="itemDestroyer-{{ $key }}" aria-hidden="true"></i>
                            </td>
                        </tr>
                    @endforeach
                    <tr class="d-none" id="repeater">
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control p-1 salesItemStock" placeholder="Search Item">
                                <input type="hidden" class="itemSalesHidden">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input class="form-control p-1 salesAvailableQty" type="text" value=""
                                    size="1" readonly />
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control p-1 salesAddQty" placeholder="Qty"
                                    size="1" {{ $multiple_barcode ? 'readonly' : '' }}>
                                @if ($multiple_barcode)
                                    <button type="button" class="btn btn-primary btn-sm barcodeScan"
                                        data-toggle="modal">+</button>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control p-1 salesUnit" readonly placeholder=" Unit"
                                    size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="text" name="salesUnitPrice" class="form-control p-1 salesUnitPrice"
                                    readonly placeholder="unit Price" size="1">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control p-1 salesDiscount" placeholder="Discount"
                                    size="1">
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
                        <td class="{{ $stockTransEntry->sup_status_id == 2 ? 'd-none' : '' }}">
                            <i class="fa fa-plus p-1 fireRepeaterClick" aria-hidden="true"></i>
                            <i class="fa fa-trash p-1 destroyRepeater" aria-hidden="true"></i>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        {{-- End of item search design --}}
        <hr>

        <div class="main-container mb-3">
            <div class="row">
                <div class="col-xl-8 col-md-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Remarks</span>
                        <textarea class="form-control" name="remarks" id="" cols="100" rows="5">{{ $stockTransEntry->remarks }}</textarea>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <table class="table table-sm table-bordered">
                        <tbody>
                            <tr>
                                <th class="bg-primary text-white">Sub Total</th>
                                <td><input id="sl_gross_total" type="text" name="gross_amt"
                                        value="{{ $stockTransEntry->gross_amt }}" class="form-control" readonly></td>
                            </tr>
                            <tr>
                                <th class="bg-primary text-white">Total Discount</th>
                                <td> <input id="sl_discount_amount" type="text" name="discount_amt"
                                        value="{{ $stockTransEntry->discount_amt }}" class="form-control" readonly></td>
                            </tr>
                            <tr>
                                <th class="bg-primary text-white">Taxable Amount</th>
                                <td><input id="sl_taxable_amnt" type="text" name="taxable_amt"
                                        value="{{ $stockTransEntry->taxable_amt }}" class="form-control" readonly></td>
                            </tr>
                            <tr>
                                <th class="bg-primary text-white">Tax Total</th>
                                <td><input id="sl_tax_amount" type="text" name="total_tax_vat"
                                        value="{{ $stockTransEntry->total_tax_vat }}" class="form-control" readonly></td>
                            </tr>
                            <tr>
                                <th class="bg-primary text-white">Net Amount</th>
                                <td> <input id="sl_net_amount" type="text" name="net_amt"
                                        value="{{ $stockTransEntry->net_amt }}" class="form-control" readonly></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- End of Price table --}}
        <div class="container mb-4">
            <div class="row ">
                <div class="col-9"></div>
                <div class="col-3">
                    <input id="status" type="hidden" name="sup_status_id" value="">
                    @if ($stockTransEntry->sup_status_id == 2)
                        <button id="cancel" class="btn btn-danger cancel_approved">Cancel this bill</button>
                    @elseif($stockTransEntry->sup_status_id == 3)
                    @else
                        <button id="save" class="btn btn-secondary sl_save">Draft</button>
                        <button id="approve" class="btn btn-success sl_approve">Approve</button>
                    @endif
                </div>
            </div>
        </div>
    </form>
@endsection

@push('after_scripts')
    <script>
        var FromSelectId = $('#from_store_id').find(":selected").val();
        storeTransferFromFieldHide(FromSelectId);
        $('#from_store_id').change(function(e) {
            var FromSelectId = $('#from_store_id').find(":selected").val();
            storeTransferFromFieldHide(FromSelectId);
        });

        function storeTransferFromFieldHide(FromSelectId) {
            let url = '{{ route('stockTransfer.getToStore', ':id') }}';
            url = url.replace(':id', FromSelectId);
            axios.get(url)
                .then((response) => {
                    if (response.data.status === 'success') {
                        var $select = $('#to_store_id');
                        $select.find('option').remove();
                        $.each(response.data.data, function(key, value) {
                            $select.append('<option value=' + value['id'] + '>' + value['name_en'] +
                                '</option>'); // return empty
                        });
                    } else {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: response.data.message,
                            icon: 'error',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Create New Store',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "/admin/mst-store/create";
                            }
                        })
                    }
                });
        }
    </script>
    @include('customAdmin.stockTransfer.partials.script');
@endpush
