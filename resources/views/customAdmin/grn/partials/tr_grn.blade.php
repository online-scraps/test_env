


@if(isset($poItems))
@foreach($poItems as $key=>$item)
@php
    $key=$key+1;
@endphp



<tr class="item-row" id="item-row-{{$key}}" tr-id="{{$key}}">
   
    <td>
        <div class="input-group">
            <input type="text" class="form-control p-1 grn_item_name" id="grn_item_name-{{$key}}" tr-id="{{$key}}" name="mst_items_id[{{$key}}]" placeholder="Search Item" value="{{$item->itemEntity->name}}">
            <input type="hidden" name="grn_item_name_hidden[{{$key}}]" class="grn_item_name_hidden" value="{{$item->items_id}}">
        </div>
    </td>

    <td>
        <div class="input-group">
            <input type="number" min="0" class="form-control p-1 grn_qty" id="grn_po_qty-{{$key}}" tr-id="{{$key}}" name="purchase_qty[{{$key}}]" value="{{$item->purchase_qty}}" placeholder="PO-Qty" size="1" readonly>
        </div>
    </td>
    <td>
        <div class="input-group">
            <input type="number" min="0" class="form-control p-1 grn_rec_qty" id="grn_rec_qty-{{$key}}" tr-id="{{$key}}" name="received_qty[{{$key}}]" placeholder="REC-Qty" size="1">
        </div>
    </td>
   

    <td>
        <div class="input-group">
            <input type="number" min="0" class="form-control p-1 grn_free_qty" id="grn_free_qty-{{$key}}" tr-id="{{$key}}" name="free_qty[{{$key}}]" placeholder="FREE-Qty" size="1">
        </div>
    </td>

    <td>
        <div class="input-group">
            <input type="number" min="0" class="form-control p-1 grn_total_qty" id="grn_total_qty-{{$key}}" tr-id="{{$key}}" name="total_qty[{{$key}}]" placeholder="T-Qty" size="1" readonly>
        </div>
    </td>

    <td>
        <div class="input-group">
            <input type="date" class="form-control p-1 itemExpiry" id="itemExpiry-{{$key}}" tr-id="{{$key}}" name="expiry_date[{{$key}}]" placeholder="Expiry">
        </div>
    </td>
    <td>
        <div class="input-group">
            <input type="number" step="0.01" class="form-control p-1 grn_purchase_price" id="grn_purchase_price-{{$key}}" tr-id="{{$key}}" name="purchase_price[{{$key}}]" placeholder="P-Price" size="1">
        </div>
    </td>
    <td>
        <div class="input-group">
            <input type="number" step="0.01" class="form-control p-1 grn_sales_price" id="grn_sales_price-{{$key}}" tr-id="{{$key}}" name="sales_price[{{$key}}]" placeholder="S-Price" size="1">
        </div>
    </td>

    <td>
        <div class="input-group mb-3">
            <select class="form-select grn_discount_mode" id="grn_discount_mode-{{$key}}" tr-id="{{$key}}" name="discount_mode_id[{{$key}}]">
               @foreach($discount_modes as $mode)
                    <option value="{{$mode->id}}" {{$mode->id===1?'selected':''}}>{{$mode->name_en}}</option>
               @endforeach
          
            </select>
        </div>
    </td>
    <td>
        <div class="input-group">
            <input type="number" min="0" max="100" class="form-control p-1 grn_discount" id="grn_discount-{{$key}}" tr-id="{{$key}}" name="discount[{{$key}}]" placeholder="Discount" min=0 size="1">
        </div>
    </td>
    <td>
        <div class="input-group">
            <input type="number" min="0" class="form-control p-1 grn_tax_vat" id="grn_tax_vat-{{$key}}" tr-id="{{$key}}" name="tax_vat[{{$key}}]" value="{{$item->tax_vat}}" placeholder="Tax-Vt" size="1">
        </div>
    </td>
    <td>
        <div class="input-group">
            <input type="number" min="0" class="form-control p-1 grn_item_amount" id="grn_item_amount-{{$key}}" tr-id="{{$key}}" name="item_amount[{{$key}}]" placeholder="Amount" readonly size="1">
        </div>
    </td>

   
</tr>
@endforeach

@endif
<script type="text/javascript" src="{{ asset('js/calculation_grn.js') }}"></script>
