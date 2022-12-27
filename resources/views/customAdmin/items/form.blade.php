

 {{--!! Start Of Page --}}


@php
    $ledgers = \App\Models\ChartsOfAccount::all();
    // dd($ledgers);
@endphp

<style>
    label {
        font-weight: bold;
    }

    fieldset {
        width: 100%;
    }

    legend {
        font-size: medium;
        font-weight: bold;
    }

    .left {
        float: left;
    }

    .right {
        position: absolute;
        right: 0px;
    }

    .center {
        text-align: center;
    }

    .width-60 {
        width: 60% !important;
    }
</style>

<div class="row">
    {{-- ** START:Main Unit Details --}}
    <div class="col-lg-4 col-md-12">
        <fieldset class="border p-2 mb-2">
            <legend>Main Unit Details</legend>
            <div class="form-inline">
                <div class="form-group col mb-3">
                    <label class="left" for="op_stock_qty">Op. Stock (Qty)</label>
                    <input type="number" name="op_stock_qty" id="op_stock_qty" class="form-control right width-60"
                        value="{{ isset($entry->op_stock_qty) ? $entry->op_stock_qty : old('op_stock_qty') }}">
                </div>
            </div>
            <div class="form-inline">
                <div class="form-group col mb-3">
                    <label class="left" for="op_stock_val">Op. Stock (Value)</label>
                    <input type="text" name="op_stock_val" id="op_stock_val" class="form-control right width-60"
                        value="{{ isset($entry->op_stock_val) ? $entry->op_stock_val : old('op_stock_val') }}">
                </div>
            </div>
        </fieldset>
    </div>
    {{-- ** END:Main Unit Details --}}

    {{-- ** START:Discount & Markup Det. --}}
    <div class="col-lg-8 col-md-12">
        <fieldset class="border p-2 mb-2">
            <legend>Discount & Markup Det.</legend>
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="form-inline">
                        <div class="form-group col mb-3">
                            <label class="left" for="sales_discount">Sales Discount</label>
                            <input type="text" name="sales_discount" id="sales_discount"
                                class="form-control right width-40"
                                value="{{ isset($entry->sales_discount) ? $entry->sales_discount : old('sales_discount') }}">
                        </div>
                        <div class="form-group col mb-3">
                            <label class="left" for="sales_compound_discount">Sales Compound Discount</label>
                        </div>
                        <div class="form-group col mb-3">
                            <label class="left" for="sales_disc_str">Sales Disc. Structure</label>
                                <select name="sales_disc_str" id="sales_disc_str" class="form-control right width-40">
                                    <option value="0" {{ isset($entry->sales_disc_str) ? ($entry->sales_disc_str == '1' ? "selected" : "") : (old('sales_disc_str') == "1" ? "selected" : "") }}>No</option>
                                    <option value="1" {{ isset($entry->sales_disc_str) ? ($entry->sales_disc_str == '1' ? "selected" : "") : (old('sales_disc_str') == "1" ? "selected" : "") }}>Yes</option>
                                </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="form-inline">
                        <div class="form-group col mb-3">
                            <label class="left" for="purchase_discount">Purchase Discount</label>
                            <input type="text" name="purchase_discount" id="purchase_discount"
                                class="form-control right width-40"
                                value="{{ isset($entry->purchase_discount) ? $entry->purchase_discount : old('purchase_discount') }}">
                        </div>
                        <div class="form-group col mb-3">
                            <label class="left" for="purchase_compound_discount">Purchase Compound Discount</label>
                        </div>
                        <div class="form-group col mb-3">
                            <label class="left" for="purchase_disc_str">Purc. Disc. Structure</label>
                                <select name="purchase_disc_str" id="purchase_disc_str" class="form-control right width-40">
                                    <option value="0" {{ isset($entry->purchase_disc_str) ? ($entry->purchase_disc_str == '1' ? "selected" : "") : (old('purchase_disc_str') == "1" ? "selected" : "") }}>No</option>
                                    <option value="1" {{ isset($entry->purchase_disc_str) ? ($entry->purchase_disc_str == '1' ? "selected" : "") : (old('purchase_disc_str') == "1" ? "selected" : "") }}>Yes</option>
                                </select>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
    {{-- ** END:Discount & Markup Det. --}}

    {{-- ** START:Item Price Info --}}
    <div class="col-12">
        <fieldset class="border p-2 mb-2">
            <legend>Item Price Info</legend>
            <div class="row mb-2">
                <div class="col-2">
                    <label class="" for=""></label>
                </div>
                <div class="col-5">
                    <label>Sales Price Applied On</label>
                </div>
                <div class="col-5">
                    <label>Purchase Price Applied On</label>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-2">
                    <label class="right">Sales Price <span class="unitTextId"></span> :</label>
                </div>
                <div class="col-5">
                    <input type="text" name="sales_price_sale" id="sales_price_sale"
                        class="form-control left width-100" value="{{ isset($entry->sales_price_sale) ? $entry->sales_price_sale : old('sales_price_sale') }}">
                </div>
                <div class="col-5">
                    <input type="text" name="sales_price_purchase" id="sales_price_purchase"
                        class="form-control left width-100" value="{{ isset($entry->sales_price_purchase) ? $entry->sales_price_purchase : old('sales_price_purchase') }}">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-2">
                    <label class="right">Purchase Price <span class="unitTextId"></span> :</label>
                </div>
                <div class="col-5">
                    <input type="text" name="purchase_price_sale" id="purchase_price_sale"
                        class="form-control left width-100" value="{{ isset($entry->purchase_price_sale) ? $entry->purchase_price_sale : old('purchase_price_sale') }}">
                </div>
                <div class="col-5">
                    <input type="text" name="purchase_price_purchase" id="purchase_price_purchase"
                        class="form-control left width-100" value="{{ isset($entry->purchase_price_purchase) ? $entry->purchase_price_purchase : old('purchase_price_purchase') }}">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-2">
                    <label class="right">MRP <span class="unitTextId"></span> :</label>
                </div>
                <div class="col-5">
                    <input type="text" name="mrp_sale" id="mrp_sale" class="form-control left width-100" value="{{ isset($entry->mrp_sale) ? $entry->mrp_sale : old('mrp_sale') }}">
                </div>
                <div class="col-5">
                    <input type="text" name="mrp_purchase" id="mrp_purchase" class="form-control left width-100" value="{{ isset($entry->mrp_purchase) ? $entry->mrp_purchase : old('mrp_purchase') }}">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-2">
                    <label class="right">Min. Sales Price :</label>
                    <br>
                    <label class="right">(Exclusive of Taxes)</label>
                </div>
                <div class="col-5">
                    <input type="text" name="min_sale_price_sale" id="min_sale_price_sale"
                        class="form-control left width-100" value="{{ isset($entry->min_sale_price_sale) ? $entry->min_sale_price_sale : old('min_sale_price_sale') }}">
                </div>
                <div class="col-5">
                    <input type="text" name="min_sale_price_purchase" id="min_sale_price_purchase"
                        class="form-control left width-100" value="{{ isset($entry->min_sale_price_purchase) ? $entry->min_sale_price_purchase : old('min_sale_price_purchase') }}">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-2">
                    <label class="right">Self-Val. Price :</label>
                </div>
                <div class="col-5">
                    <input type="text" name="self_val_price_sale" id="self_val_price_sale"
                        class="form-control left width-100" value="{{ isset($entry->self_val_price_sale) ? $entry->self_val_price_sale : old('self_val_price_sale') }}">
                </div>
                <div class="col-5">
                    <input type="text" name="self_val_price_purchase" id="self_val_price_purchase"
                        class="form-control left width-100" value="{{ isset($entry->self_val_price_purchase) ? $entry->self_val_price_purchase : old('self_val_price_purchase') }}">
                </div>
            </div>
        </fieldset>
    </div>
    {{-- ** END:Item Price Info --}}
</div>

{{--** START:default unit for sales --}}
<div class="row">
    <div class="col-lg-6 col-md-12 text-center">
        <label>Default Unit for Sales</label>
    </div>
    <div class="col-lg-6 col-md-12 text-center">
        <label>Default Unit for Purchase</label>
    </div>
</div>
<div class="row">
    <div class="col-lg-6 col-md-12 ">
        <div class="form-inline">
            <div class="form-group col mb-3">
                <label class="left" for="tax_inc_sales">Tax Inclusive Sales Prices</label>
                <select name="tax_inc_sales" id="tax_inc_sales" class="form-control right  width-60">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-12 ">
        <div class="form-inline">
            <div class="form-group col mb-2">
                <label class="left" for="tax_inc_purchase">Tax Inclusive Purchase</label>
                <select name="tax_inc_purchase" id="tax_inc_purchase" class="form-control right  width-60">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-12 mb-2">
        <div class="form-inline">
            <div class="form-group col mb-2">
                <label class="left" for="sales_account_sales">Specify Sales Account</label>
                    <select name="sales_account_sales" id="sales_account_sales" class="form-control right width-60">
                        <option value="0" {{ isset($entry->sales_account_sales) ? ($entry->sales_account_sales == '0' ? "selected" : "") : (old('sales_account_sales') == "0" ? "selected" : "") }}>Not Required</option>
                        <option value="1" {{ isset($entry->sales_account_sales) ? ($entry->sales_account_sales == '1' ? "selected" : "") : (old('sales_account_sales') == "1" ? "selected" : "") }}>Specify Here</option>
                    </select>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-12 mb-2">
            <div class="form-group col mb-2">
                <select name="sales_acount_ledger_id" id="sales_acount_ledger" class="form-control right width-100">
                    @foreach ($ledgers as $ledger)
                        <option value="{{ $ledger->id }}">{{ $ledger->name }}</option>
                    @endforeach
                </select>
            </div>
    </div>
    <div class="col-lg-6 col-md-12 mb-2">
        <div class="form-inline">
            <div class="form-group col mb-2">
                <label class="left" for="sales_account_purchase">Specify Purchase Account</label>
                <select name="sales_account_purchase" id="sales_account_purchase" class="form-control right  width-60">
                    <option value="0" {{ isset($entry->sales_account_purchase) ? ($entry->sales_account_purchase == '0' ? "selected" : "") : (old('sales_account_purchase') == "0" ? "selected" : "") }}>Not Required</option>
                    <option value="1" {{ isset($entry->sales_account_purchase) ? ($entry->sales_account_purchase == '1' ? "selected" : "") : (old('sales_account_purchase') == "1" ? "selected" : "") }}>Specify Here</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-12 mb-2">
            <div class="form-group col mb-2">
                <select name="purchase_acount_ledger_id" id="purchase_acount_ledger" class="form-control right  width-100">
                    @foreach ($ledgers as $ledger)
                        <option value="{{ $ledger->id }}">{{ $ledger->name }}</option>
                    @endforeach
                </select>
            </div>
    </div>
</div>
{{--** END:default unit for sales --}}

<script>
    $(document).ready(function () {
        $('#sales_acount_ledger').hide();
        $('#purchase_acount_ledger').hide();

        toggleLedgerFields('#sales_account_sales', '#sales_acount_ledger');
        toggleLedgerFields('#sales_account_purchase', '#purchase_acount_ledger');

        $("#unit_id").change(function (e) {
            var sel = document.getElementById('unit_id');
            var res = sel.options[sel.selectedIndex].text;
            var selectedUnit = '(' + res + ')';
            $('.unitTextId').text(selectedUnit);
        });

        function toggleLedgerFields(selectId, fieldId) {
            fieldToggler(selectId, fieldId);
            $(selectId).change(function (e) {
                fieldToggler(selectId, fieldId);
            });
        }

        function fieldToggler(selectId, fieldId) {
            var selection = $(selectId).val();
                switch(selection){
                    case "0":
                        $(fieldId).hide();
                        break;
                    case "1":
                        $(fieldId).show();
                        break;
                }
        }
    });

</script>

