<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{url('css/style.css')}}">
    <title>Sales</title>

</head>

<body>
    <div class="po_navbar">
        <div class="billing_nav">
            <div class="heading">
                <h1>GRN</h1>
            </div>
            <h5 class="heading">Status:</h5>
            <div class="bill_icons">
                <button class='icon-btn'><i class="fa fa-plus" aria-hidden="true"></i></button>
                <button class='icon-btn'><i class="fa fa-home" aria-hidden="true"></i></button>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-3">
                <div class="input-group mb-3">
                    <label class="input-group-text" for="disc_type">GRN type</label>
                    <select class="form-select" id="store" name="store">
                        <option value="1" selected>Regular GRN</option>
                        <option value="2">Stock Transfer GRN</option>
                        <option value="2">GRN From Wharehouse</option>
                    </select>
                </div>
            </div>
            <div class="col-3">
                <div class="input-group mb-3">
                    <span class="input-group-text">Store</span>
                    <input type="text" class="form-control" id="po_date" name="po_date"placeholder="Store">
                </div>
            </div>
            <div class="col-3">
                <div class="input-group mb-3">
                        <span class="input-group-text">Supplier</span>
                        <input type="text" class="form-control" id="po_date" name="po_date"placeholder="Search Supplier">
                </div>
            </div>
            <div class="col-3">
                <div class="input-group mb-3">
                        <span class="input-group-text">Purchase Order No</span>
                        <input type="text" class="form-control" id="po_date" name="po_date"placeholder="PO No">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#grn_modal">Add</button>
                </div>
            </div>
            <div class="col-3">
                <div class="input-group mb-3">
                    <span class="input-group-text">PO Date</span>
                    <input type="date" class="form-control" id="po_date" name="po_date">
                </div>
            </div>
            <div class="col-3">
                <div class="input-group mb-3">
                    <span class="input-group-text">Invoice No</span>
                    <input type="text" class="form-control" id="inv_no" name="inv_no"placeholder="Invoice No">
                </div>
            </div>
            <div class="col-3">
                <div class="input-group mb-3">
                    <span class="input-group-text">Invoice Date</span>
                    <input type="date" class="form-control" id="Inv_date" name="Inv_date">
                </div>
            </div>
            <div class="col-3">
                <div class="input-group mb-3">
                    <span class="input-group-text">DC No</span>
                    <input type="text" class="form-control" id="dc_no" name="dc_no"placeholder="Delivery Chalan">
                </div>
            </div>
            <div class="col-3">
                <div class="input-group mb-3">
                    <span class="input-group-text">DC Date</span>
                    <input type="date" class="form-control" id="dc_date" name="dc_date">
                </div>
            </div>
            <div class="col-3">
                <div class="input-group mb-3">
                    <span class="input-group-text">GRN No</span>
                    <input type="text" class="form-control" id="grn_no" name="grn_no"placeholder="Grn No">
                </div>
            </div>
            <div class="col-3">
                <div class="input-group mb-3">
                    <span class="input-group-text">GRN Date</span>
                    <input type="date" class="form-control" id="grn_date" name="grn_date">
                </div>
            </div>
           
        </div>
    </div>
    {{-- End of upper form filter design --}}

    {{-- Modal for fetching po for grn entry --}}
    <div class="modal fade bd-example-modal-xl" id="grn_modal" tabindex="-1" role="dialog"
        aria-hidden="true">
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
                                  <input type="date" class="form-control p-1" placeholder="From date" size="1">
                              </div>
                          </div>
                            <div class="col-3">
                                <div class="input-group">
                                    <span class="input-group-text">To</span>
                                    <input type="date" class="form-control p-1" placeholder="To date" size="1">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="input-group">
                                    <span class="input-group-text">PO No</span>
                                    <input type="text" class="form-control p-1" placeholder="PO No" size="1">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="input-group">
                                    <span class="input-group-text">Supplier</span>
                                    <input type="text" class="form-control p-1" placeholder="Search Supplier" size="1">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col-3">
                            <div class="input-group mb-3">
                              <label class="input-group-text" for="bill_status">PO Type</label>
                              <select class="form-select" id="PO_type">
                                <option value="1" selected>Regular PO</option>
                                <option value="2">Stock Transfer PO</option>
                                <option value="2">PO to Wharehouse</option>
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
                            <button class="btn btn-primary">search</button>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table table-sm">
                        <thead>
                            <tr class="bg-danger text-white">
                                <th scope="col">Select</th>
                                <th scope="col">Date</th>
                                <th scope="col">From Store</th>
                                <th scope="col">PO No</th>
                                <th scope="col">PO Type</th>
                                <th scope="col">PO To</th>
                                <th scope="col">PO Status</th>
                                <th scope="col">Total Qty</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Created By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td scope="row">
                                  <input type="checkbox">
                                </td>
                                <td>2022/01/15</td>
                                <td>Bidh Store</td>
                                <td>Bill1524</td>
                                <td>Regular PO</td> 
                                <td>Nitesh</td>
                                <td>Approved</td>
                                <td>150</td>
                                <td>15000</td>
                                <td>Yogesh</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success">Fetch</button>
                </div>
            </div>
        </div>
    </div>
    {{-- end of the modal content --}}

    {{-- Start of main table design --}}
    <table class="table">
        <thead>
            <tr class="bg-danger text-white">
                <th scope="col">S.No</th>
                <th scope="col">Item Name</th>
                <th scope="col">PO-Qty</th>
                <th scope="col">Rec-Qty</th>
                <th scope="col">Invoice-Qty</th>
                <th scope="col">Free Qty</th>
                <th scope="col">Total Qty</th>
                <th scope="col">Batch No</th>
                <th scope="col">Exp-Date</th>
                <th scope="col">purchase Price</th>
                <th scope="col">Sales Price</th>
                <th scope="col">Discount Mode</th>
                <th scope="col">Discount </th>
                <th scope="col">Tax/Vat </th>
                <th scope="col">Item Amount</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th scope="row">1</th>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control p-1"id="item_name" name="item_name" placeholder="Search Item">
                    </div>
                </td>

                <td>
                    <div class="input-group">
                        <input type="text" class="form-control p-1"id="po-qty" name="po-qty" placeholder="PO-Qty" size="1" disabled>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control p-1"id="rec-qty" name="rec-qty" placeholder="REC-Qty" size="1" disabled>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control p-1"id="inv-qty" name="inv-qty" placeholder="INV-Qty" size="1">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control p-1"id="free-qty" name="free-qty" placeholder="FREE-Qty" size="1">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control p-1"id="total-qty" name="total-qty" placeholder="T-Qty" size="1">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control p-1"id="batch_no" name="batch_no" placeholder="Batch" size="1">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <input type="date" class="form-control p-1"id="exp_date" name="exp_date" size="1">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control p-1"id="purchase-price" name="purchase-price" placeholder="P-Price" size="1">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control p-1"id="sales-price" name="sales-price" placeholder="S-Price" size="1">
                    </div>
                </td>

                <td>
                    <div class="input-group mb-3">
                        <select class="form-select"id="discount_mode" name="discount_mode">
                            <option value="1" selected>%</option>
                            <option value="2">NRS</option>
                        </select>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control p-1" id="discount" name="discount"placeholder="Discount" size="1">
                    </div>
                </td>
                <td>13</td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control p-1" id="item_amount" name="item_amount"placeholder="Amount" size="1">
                    </div>
                </td>
                <td>
                    <i type = "button" class="fa fa-times p-1" aria-hidden="true"></i>
                    <i type = "button" class="fa fa-history p-1" data-toggle="modal" data-target="#purchase_order_modal" aria-hidden="true"></i>
                </td>
            </tr>
        </tbody>
    </table>
    {{-- start of Purchase order history modal --}}
        <div class="modal fade bd-example-modal-xl" id="purchase_order_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="container">
                        <div class="row">
                            <h3>GRN History of item {item}</h3>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="input-group">
                                    <span class="input-group-text">From</span>
                                    <input type="date" class="form-control"  name="po_history_from">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <span class="input-group-text">To</span>
                                    <input type="date" class="form-control" name="po_history_to">
                                </div>
                            </div>
                            <div class="col">
                                <button class="btn btn-success">Fetch</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table table-sm">
                        <thead>
                            <tr class="bg-danger text-white">
                                <th scope="col">S.No</th>
                                <th scope="col">Sup Name</th>
                                <th scope="col">Invoice No</th>
                                <th scope="col">Invoice Date</th>
                                <th scope="col">GRN No</th>
                                <th scope="col">GRN Date</th>
                                <th scope="col">GRN Qty</th>
                                <th scope="col">Free Qty</th>
                                <th scope="col">Purchase Price</th>
                                <th scope="col">Discount</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Created By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">1</th>
                                <td>XYZ Supp</td>
                                <td>10235</td>
                                <td>2022/01/15</td>
                                <td>60</td>
                                <td>125</td>
                                <td>563</td>
                                <td>100</td>
                                <td>152</td>
                                <td>10</td>
                                <td>1001</td>
                                <td>Yogesh</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <h5 class="left">Total Purchase Qty: {Qty}</h5>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
        </div>
{{-- end of the modal content --}}

    {{-- End of item search design --}}
    <hr>
    <div class="container-fluid">
        <div class="row">
            <div class="col-4">
                <div class="input-group mb-3">
                    <span class="input-group-text">Created By</span>
                    <input type="text" class="form-control"id="created_by" name="created_by" placeholder="Created By">
                </div>
                <div class="col">
                    <div class="input-group mb-3">
                        <span class="input-group-text">Approved By</span>
                        <select class="form-select"id="tax_vat" name="po_approver">
                            <option value="1" selected>Ramesh</option>
                            <option value="2">Suresh</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="input-group mb-3">
                    <span class="input-group-text">Comments</span>
                    <textarea type="text" class="form-control"id="comments" name="comments" placeholder="comments" rows="3"></textarea>
                </div>
            </div>
            <div class="col-4">
                <table class="table table-sm table-bordered">
                    <tbody>
                        <tr>
                            <th class="bg-primary text-white">Sub Total</th>
                            <td>रू 1000</td>
                        </tr>
                        <tr>
                            <th class="bg-primary text-white">Total Discount</th>
                            <td>रू 100</td>
                        </tr>
                        <tr>
                            <th class="bg-primary text-white">Taxable Amount</th>
                            <td>रू 900</td>
                        </tr>
                        <tr>
                            <th class="bg-primary text-white">Tax Total</th>
                            <td>रू 117</td>
                        </tr>
                        <tr>
                            <th class="bg-primary text-white">Other Charges</th>
                            <td>रू 50</td>
                        </tr>
                        <tr>
                            <th class="bg-primary text-white">Net Amount</th>
                            <td>रू 1067</td>
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
                <button class="btn btn-primary">Save</button>
                <button class="btn btn-success">Approve</button>
                <button class="btn btn-danger">Cancel</button>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
    integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
    integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
</script>

</body>

</html>
