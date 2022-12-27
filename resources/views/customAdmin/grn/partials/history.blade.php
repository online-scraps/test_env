@php 
     $sum_of_qty=0;       
@endphp
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
    <tbody id="po_item_history">

    @foreach($datas as $d)
        @php 
        $sum_of_qty= $sum_of_qty+$d->total_qty;
        @endphp
   
        <tr>
            <th scope="row">{{$loop->iteration}}</th>
            <td>{{$d->invoice_no}}</td>
            <td>{{$d->invoice_no}}</td>
            <td>{{$d->invoice_date}}</td>
            <td>{{$d->grn_no}}</td>
            <td>{{$d->grn_date}}</td>
            <td>{{$d->total_qty}}</td>
            <td>{{$d->free_qty}}</td>
            <td>{{$d->purchase_price}}</td>
            <td>{{$d->discount}}</td>
            <td>{{$d->item_amount}}</td>
            <td>{{$d->item_amount}}</td>
        </tr>

    @endforeach
       
    </tbody>
</table>


<script>
    $('#total_qty_history').text('{{$sum_of_qty}}');
</script>
