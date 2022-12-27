<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"
        integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{url('css/style.css')}}">
    <title>Sales</title>

</head>

<body>

    <div class="billing_navbar">
        <div class="billing_nav">
            <div class="heading">
                <h1>Billing</h1>
            </div>
            <div class="header_icons">
                <button class='icon-btn' data-toggle="modal" data-target="#previous_bill_modal" aria-hidden="true"><i class="fa fa-search" aria-hidden="true"></i></button>
                <button class='icon-btn'><i class="fa fa-plus" aria-hidden="true"></i></button>
                <button class='icon-btn'><i class="fa fa-home" aria-hidden="true"></i></button>
            </div>
        </div>
    </div>
    {{-- Modal for adding item to the Previous bill --}}
    <div class="modal fade bd-example-modal-xl" id="previous_bill_modal" tabindex="-1" role="dialog"
        aria-hidden="true">
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
                                    <span class="input-group-text">Mobile/Bill#</span>
                                    <input type="text" class="form-control p-1" placeholder="Mobile/Bill" size="1">
                                </div>
                            </div>
                            <div class="col-4">
                              <div class="input-group">
                                <span class="input-group-text">From</span>
                                  <input type="date" class="form-control p-1" placeholder="From date" size="1">
                              </div>
                          </div>
                            <div class="col-4">
                                <div class="input-group">
                                    <span class="input-group-text">To</span>
                                    <input type="date" class="form-control p-1" placeholder="To date" size="1">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col">
                            <div class="input-group mb-3">
                              <label class="input-group-text" for="bill_status">Status</label>
                              <select class="form-select" id="Payment_type">
                                  <option value="1" selected>Created</option>
                                  <option value="2">Completed</option>
                                  <option value="2">Cancelled</option>
                              </select>
                          </div>
                          </div>
                          <div class="col">
                            <button class="btn btn-primary">Search</button>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table table-sm">
                        <thead>
                            <tr class="bg-danger text-white">
                                <th scope="col">Select</th>
                                <th scope="col">Bill No</th>
                                <th scope="col">Name</th>
                                <th scope="col">Date</th>
                                <th scope="col">Paid Amount</th>
                                <th scope="col">Bill Amount</th>
                                <th scope="col">Billed By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td scope="row">
                                  <input type="checkbox">
                                </td>
                                <td>Bill1524</td>
                                <td>Nitesh</td>
                                <td>2022/01/15</td>
                                <td>100</td>
                                <td>50</td>
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

    <div class="container-fluid">
        <div class="row mt-3"> 
            <div class="col-3">
                <div class="input-group mb-3">
                    <label class="input-group-text" for="disc_type">Bill Type</label>
                    <select class="form-select" id="gender">
                        <option value="1" selected>Indivisual</option>
                        <option value="2">Corporate</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">Address</span>
                    <input type="text" class="form-control" placeholder="address">
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">Bill Date</span>
                    <input type="date" class="form-control" placeholder="date">
                </div>
            </div>
            <div class="col-3">
                <div class="input-group mb-3">
                    <span class="input-group-text">Buyer Name</span>
                    <input type="text" class="form-control" placeholder="Buyer" />
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">Contact No</span>
                    <input type="text" class="form-control" placeholder="Contact">
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">Company Name</span>
                    <input type="text" class="form-control" placeholder="company Name">
                </div>
            </div>
            <div class="col-3">
                <div class="input-group mb-3">
                    <label class="input-group-text" for="gender">Gender</label>
                    <select class="form-select" id="gender">
                        <option value="1" selected>Male</option>
                        <option value="2">Female</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">Age</span>
                    <input type="text" class="form-control" placeholder="Age">
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">Pan/Vat</span>
                    <input type="text" class="form-control" placeholder="Pan/Vat No">
                </div>
               
            </div>
            <div class="col-3">
                <div class="input-group mb-3">
                    <span class="input-group-text">Bill No</span>
                    <input type="text" class="form-control" placeholder="Bill No">
                </div>
                <div class="input-group mb-3">
                    <label class="input-group-text" for="disc_type">Discount Mode</label>
                    <select class="form-select" id="gender">
                        <option value="1" selected>%</option>
                        <option value="2">NRS</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <span>Item Wise Discount</span>
                    <input type="checkbox" class="mt-2 mx-2">
                </div>
            </div>
        </div>
    </div>

    {{-- End of upper form filter design? --}}
    <table class="table">
        <thead>
            <tr class="bg-danger text-white">
                <th scope="col">S.No</th>
                <th scope="col">Code/Model Name</th>
                <th scope="col">Total Qty</th>
                <th scope="col">Batch No</th>
                <th scope="col">Batch Qty</th>
                <th scope="col">Qty</th>
                <th scope="col">Unit </th>
                <th scope="col">Unit Price</th>
                <th scope="col">Disc </th>
                <th scope="col">Tax/Vat</th>
                <th scope="col">Amount</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th scope="row">1</th>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control p-1" placeholder="Search Item">
                    </div>
                </td>
                <td>100</td>
                <td>
                    <div class="input-group">
                        <select class="form-select p-1" size="1">
                            <option selected>BATCH120</option>
                            <option value="1">BAT021</option>
                        </select>
                    </div>
                </td>
                <td>50</td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control p-1" placeholder="Qty" size="1">
                    </div>
                </td>
                <td>pcs</td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control p-1" placeholder="unit Price" size="1">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control p-1" placeholder="Discount" size="1">
                    </div>
                </td>
                <td>13</td>
                <td>1017</td>
                <td><i class="fa fa-times" aria-hidden="true"></i></td>
            </tr>
        </tbody>
    </table>
    {{-- End of item search design --}}
    <hr>
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="input-group mb-3">
                    <span class="input-group-text">Discount</span>
                    <input type="text" class="form-control" placeholder="Discount">
                </div>
                <div class="input-group mb-3">
                    <label class="input-group-text" for="discount_approver">Discount Approver</label>
                    <select class="form-select" id="discount_approver">
                        <option value="1" selected>Ramesh</option>
                        <option value="2">Suresh</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">Remarks</span>
                    <input type="text" class="form-control" placeholder="Remarks">
                </div>
            </div>
            <div class="col">
                <div class="input-group mb-3">
                    <label class="input-group-text" for="Payment_type">Payment Type</label>
                    <select class="form-select" id="Payment_type">
                        <option value="1" selected>Cash</option>
                        <option value="2">Card</option>
                    </select>
                </div>
                <div class="input-group mb-3">
                    <span class="input-group-text">Receipt Amount</span>
                    <input type="text" class="form-control" placeholder="Amount">
                </div>
            </div>
            <div class="col">
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
                            <th class="bg-primary text-white">Net Amount</th>
                            <td>रू 1017</td>
                        </tr>
                        <tr>
                            <th class="bg-primary text-white">Paid Amount</th>
                            <td>रू 1500</td>
                        </tr>
                        <tr>
                            <th class="bg-primary text-white">Refund</th>
                            <td>रू 483</td>
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
