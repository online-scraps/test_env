{{-- Modal for fetching po for grn entry --}}
    <div class="modal fade bd-example-modal-xl" id="grn_po_search_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="container">
                        <div class="row">
                            <h3>Find PO's For GRN</h3>
                        </div>
                        <div class="row mb-3">
                            <div class="col-3">
                              <div class="input-group">
                                <span class="input-group-text">From</span>
                                    <input type="date" class="form-control p-1"  value="{{generate_date_with_extra_days(dateToday(),7)}}" id ='itemFrom_po_search' placeholder="from date" size="1">
                              </div>
                          </div>
                            <div class="col-3">
                                <div class="input-group">
                                    <span class="input-group-text">To</span>
                                    <input type="date" class="form-control p-1" value="{{convert_ad_from_bs()}}" id="itemTo_po_search" placeholder="to date" size="1">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="input-group">
                                    <span class="input-group-text">PO No</span>
                                    <input type="text" class="form-control p-1" id="po_no" placeholder="PO No" size="1">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="input-group">
                                    <span class="input-group-text">Supplier</span>
                                    <select class="form-select" id="supplier_po_search" name="supplier_id">
                                        <option value="">--select supplier--</option>
                                        @foreach($suppliers as $supplier)
                                        <option value="{{$supplier->id}}">{{$supplier->name_en}}</option>
                                        @endforeach
                                    </select>                                
                                </div>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col-3">
                            <div class="input-group mb-3">
                              <label class="input-group-text" for="bill_status">PO Type</label>
                            <select class="form-select po_type" id="po_type" name="po_type_id">
                                <option value="0" selected>--select--</option>
                                    @foreach($po_types as $type)
                                    <option value="{{$type->id}}">{{$type->name_en}}</option>
                                    @endforeach
                            </select>
                          </div>
                          </div>
                          <div class="col-3">
                            <div class="input-group mb-3">
                              <label class="input-group-text" for="bill_status">Status</label>
                              <select class="form-select" id="modal_status">
                                  <option value="1" selected>Created</option>
                                  <option value="2">Completed</option>
                                  <option value="2">Cancelled</option>
                              </select>
                          </div>
                          </div>
                          <div class="col">
                            <button class="btn btn-primary" id="fetchPoHistory">search</button>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div id="modal_table_content_po_search"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="fetchGrnItemsFromPO">Fetch</button>
                </div>
            </div>
        </div>
    </div>
{{-- end of the modal content --}}



{{-- start of Grn history modal --}}

<div class="modal fade bd-example-modal-xl" id="search_grn_item_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="container">
                        <div class="row">
                            <h3>GRN History of item <span id="grnmodalItemName"></span></h3>
                        </div>
                        <div class="row">
                        <div class="col-3">
                            <div class="input-group">
                                <input type="date" class="form-control p-1"  value="{{generate_date_with_extra_days(dateToday(),7)}}" id ='itemFrom' placeholder="from date" size="1">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="input-group">
                                <input type="date" class="form-control p-1" value="{{convert_ad_from_bs()}}" id="itemTo" placeholder="to date" size="1">
                            </div>
                        </div>
                        <div class="col-3">
                            <button class="btn btn-primary" id="fetchHistory">Search</button>
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