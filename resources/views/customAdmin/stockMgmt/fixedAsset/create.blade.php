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
            <small>{!! $crud->getSubheading() ?? trans('backpack::crud.add').' '.$crud->entity_name !!}.</small>

            @if ($crud->hasAccess('list'))
                <small><a style="color:white" href="{{ url($crud->route) }}" class="d-print-none font-sm"><i class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
            @endif
        </h2>
    </section>
@endsection

@section('content')
{{-- <h1>hello woer</h1> --}}
{{--    <div class="billing_navbar">--}}
{{--        <div class="billing_nav" >--}}

{{--            <div class="heading">--}}
{{--                <h1>Stock Entry</h1>--}}
{{--            </div>--}}
{{--            <h5 class="heading">Status:</h5>--}}
{{--            <div class="header_icons">--}}
{{--                <button class='icon-btn'><i class="fa fa-plus" aria-hidden="true"></i></button>--}}
{{--                <button class='icon-btn'><i class="fa fa-home" aria-hidden="true"></i></button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{-- <div id="salesEntryFormWrapper"> --}}

<form id="fixedAssetEntryForm" action="{{url($crud->route)}}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="main-container billing-form">
        <div class="row mt-3">

            <div class="col-xl-4 col-lg-4 col-sm-6">
                <div class="input-group mb-3">
                    <select class="form-select store_id" id="store_id"  name="store_id" style="border-radius: 0.25rem">
                        {{-- <option value="" disabled selected>Select a store</option> --}}
                    @foreach($storeList as $key => $list)
                            <option value="{{$key}}" >{{$list}}</option>
                        @endforeach
                    </select>
                </div>
            </div>


            <div class="col-xl-4 col-lg-4 col-sm-6">
                <div class="input-group mb-3">
                    <span class="input-group-text">Date AD</span>
                        <input type="date" id="stockDateAD" value="{{dateToString(\Carbon\Carbon::now())}}"  name='entry_date_ad' value=""  class="form-control" placeholder="Date AD" readonly>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Date BS</span>
                        <input type="text" id="stockDateBS" name="entry_date_bs" value="{{convert_bs_from_ad(dateToString(\Carbon\Carbon::now()))}}" class="form-control" placeholder="Date BS" readonly >
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="batch_number">Batch number</label>
                        <select class="form-select" id="batch_number" name="batch_number">
                            @foreach ($batchNumbers as $code => $codeId)
                                <option value="{{ $codeId }}">{{ $code }}</option>
                            @endforeach
                        </select>
                        <span class="input-group-text bg-primary text-white" onclick="loadModal(this, '1')">
                            +
                        </span>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="adjustment_no">Adjustment Number</label>
                        <select class="form-select" id="adjustment_no" name="adjustment_no">
                            @foreach ($adjustmentNumbers as $code => $codeId)
                                <option value="{{ $codeId }}">{{ $code }}</option>
                            @endforeach
                        </select>
                        <span class="input-group-text bg-primary text-white" onclick="loadModal(this, '10')">
                            +
                        </span>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-sm-6">
                    <div class="input-group mb-3">
                        <span>Item Wise Depreciation</span>
                        <input type="checkbox" checked="true" name="itemWiseDiscount" id="depreciationCheckbox" class="mt-2 mx-2">
                    </div>
                </div>
        </div>
    </div>

    {{-- End of upper form filter design? --}}
   <div class="table-responsive">
    <table class="table" id="repeaterTable"  style="min-width: 1500px;">
        <thead>
        <tr class="text-white" style="background-color: #192840">
            {{--            <th scope="col">S.No</th>--}}
            <th scope="col">Code/Model Name</th>
            <th scope="col">Avl Qty</th>
            <th scope="col">Add Qty</th>
            <th scope="col">Free item</th>
            <th scope="col">Total Qty</th>
            <th scope="col">Expiry Date </th>
            <th scope="col">Unit cost </th>
            <th scope="col">Tax/vat</th>
            <th scope="col">Depreciation</th>
            <th scope="col">Amount</th>
            <th scope="col" style="width: 6rem">Action</th>
        </tr>
        </thead>
        <tbody id="stock-table">
        <tr>
            {{--            <th scope="row">1</th>--}}
            <td>
                <div class="input-group">
                    <input type="text" class="form-control p-1 itemStock"  name="mst_item_id[1]"  placeholder="Search item..." id='itemStock-1'data-cntr="1" size="1" style="width:10rem;">
                    <input type="hidden" name="itemStockHidden[1]" class="itemStockHidden">
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input id="availableQty-1"  data-cntr="1" type="number" min="0" class="form-control p-1 availableQty" name="available_total_qty[1]" value="0"   size="1" readonly>
                </div>
            </td>
            <td>
                <div class="input-group">
                    @if($multiple_barcode)
                     <button type="button" class="btn btn-primary btn-sm barcodeScan" id="barcodeScan-1" data-cntr="1" data-toggle="modal">+</button>
                    @endif
                    <input type="number" min="0" class="form-control p-1 addQty" name="add_qty[1]" placeholder="Qty" id='addQty-1' data-cntr="1" size="1" {{$multiple_barcode?'readonly':''}} style="border-top-left-radius: 0;border-bottom-left-radius: 0;">
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" min="0"  class="form-control p-1 freeQty" name="free_item[1]" placeholder="Free item" id="freeQty-1" data-cntr="1" size="1" {{$multiple_barcode?'readonly':''}}>
                </div>
            </td>
            <td >
                <div class="input-group">
                    <input id="totalQty-1"  data-cntr="1"  type="number" min="0" class="form-control p-1 totalQty" name="total_qty[1]" value="0"  size="1" readonly>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="date" class="form-control p-1 itemExpiry" name="expiry_date[1]" id="itemExpiry-1" placeholder="Expiry" data-cntr="1">
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" min="0" class="form-control p-1 unitPrice" name="unit_cost_price[1]" placeholder="Cost Price" id="unitPrice-1" size="1" data-cntr="1" >
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input id="itemTax-1" data-cntr="1"  type="number" min="0" class="form-control p-1 itemTax" name="tax_vat[1]" value="0"  size="1" readonly>
                </div>
            </td>
                        <td>
                <div class="input-group">
                    <input type="number" min="0" max="100" class="form-control p-1 fireRepeater depreciation" name="depreciation[1]" placeholder="Depreciation %" id="depreciation-1" size="1" data-cntr="1">
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input id="totalAmnt-1" data-cntr="1"    type="number" min="0" class="form-control p-1 totalAmnt" name="item_total[1]" value="0"  size="1" readonly>
                </div>
            </td>
            <td>
                <i class="fa fa-plus p-1 fireRepeaterClick" aria-hidden="true"></i>
                <i class="fa fa-trash p-1 destroyRepeater d-none" data-cntr="1" id="itemDestroyer-1" aria-hidden="true"></i>
                <i type ='button' class="fa fa-history p-1 itemHistory" data-toggle="modal" data-cntr="1"  id="itemHistory-1"  aria-hidden="true"></i>
            </td>
        </tr>
        <tr id="repeater" class="d-none">
            {{--            <th scope="row">1</th>--}}
            <td>
                <div class="input-group">
                    <input type="text" class="form-control p-1 itemStock"  placeholder="Search item..."  size="1" style="width: 10rem">
                    <input type="hidden" class="itemStockHidden">
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" min="0" class="form-control p-1 availableQty"  value="0"  size="1" readonly>
                </div>
            </td>
            <td>
                <div class="input-group">
                    @if($multiple_barcode)
                        <button type="button" class="btn btn-primary btn-sm barcodeScan" data-toggle="modal" >+</button>
                    @endif
                    <input type="number" min="0" class="form-control p-1 addQty" placeholder="Qty" size="1" {{$multiple_barcode?'readonly':''}} style="border-top-left-radius: 0;border-bottom-left-radius: 0;">
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" min="0" class="form-control p-1 freeQty" placeholder="Free item" size="1" {{$multiple_barcode?'readonly':''}}>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" min="0" class="form-control p-1 totalQty" value="0"  size="1" readonly>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="date" class="form-control p-1 itemExpiry" placeholder="Expiry" >
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" min="0" class="form-control p-1 unitPrice" placeholder="Cost Price" size="1">
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input  type="number" min="0" class="form-control p-1 itemTax"  value="0"  size="1" readonly>
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" min="0" max="100" class="form-control p-1 fireRepeater depreciation" placeholder="Depreciation %" size="1">
                </div>
            </td>
            <td>
                <div class="input-group">
                    <input  type="number" min="0" class="form-control p-1 totalAmnt" value="0"  size="1" readonly>
                </div>
            </td>
            <td>
                <i class="fa fa-plus p-1 fireRepeaterClick" aria-hidden="true"></i>
                <i class="fa fa-trash p-1 destroyRepeater" aria-hidden="true" ></i>
                <i type ='button' class="fa fa-history p-1 itemHistory" data-toggle="modal"  aria-hidden="true"></i>
            </td>
        </tr>

        </tbody>
    </table>
   </div>
    {{-- End of item search design --}}
    <hr>
    <div class="main-container">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="input-group mb-3">
                    <span class="input-group-text" >Depreciation</span>
                    <input type="number" min="0" max="100" id="flatDiscount" name="flat_depreciation" disabled="true" class="form-control" placeholder="Depreciation">
                </div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" >Upload Bill</span>
                    </div>
                    <div class="custom-file">
                        <input type="file"  class="custom-file-input" id="inputGroupFile01" name="upload_bill">
                        <label class="custom-file-label" for="body_imgUserImage">Choose file</label>
                    </div>
                </div>


                <div class="input-group mb-3">
                    <span class="input-group-text">Remarks</span>
                    <textarea class="form-control comment" name="comments" placeholder="Remarks" rows="5" ></textarea>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <table class="table table-sm table-bordered">
                    <tbody>
                    <tr>
                        <th class="bg-primary text-white">Sub Total</th>
                        <td >
                            <input id="st_gross_total" type="numner" min="0" name="gross_total" class="form-control" readonly>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-primary text-white">Total Depreciation</th>
                        <td>
                            <input id="st_depreciation_amount" type="number" min="0" name="total_depreciation" class="form-control" readonly>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-primary text-white">Taxable Amount</th>
                        <td >
                            <input id="st_taxable_amnt" type="number" min="0" name="taxable_amount" class="form-control" readonly>
                        </td>

                    </tr>
                    <tr>
                        <th class="bg-primary text-white">Tax Total</th>
                        <td >
                            <input id="st_tax_amount" type="number" min="0" name="tax_total" class="form-control" readonly>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-primary text-white">Net Amount</th>
                        <td>
                            <input id="st_net_amount" type="number" min="0" name="net_amount" class="form-control" readonly>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- End of Price table --}}
    <div class="main-container mb-4">
        <div class="row ">
            <div class="col d-flex justify-content-end ">
                    <input  id="status" type="hidden" name="status_id" value="">
                    <button id="save" type="submit"  class="btn btn-primary me-1 st_save">Save</button>
                        @if(backpack_user()->is_stock_approver)
                        <button id="approve" type="submit"  class="btn btn-success me-1 st_approve">Approve</button>
                        @endif
                <a href="{{url($crud->route)}}"><i  class="btn btn-danger me-1">Cancel</i></a>
            </div>
        </div>
    </div>
</form>
{{-- </div> --}}
@include('customAdmin.stockMgmt.partials.modals')
@include('customAdmin.partial._inlineSequenceCreate')

{{-- end of the modal content --}}

<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>

    <script>
        $(document).ready(function () {
            bsCustomFileInput.init()
        });
    </script>

@endsection

@include('customAdmin.stockMgmt.partials.fixedAsset.script')
