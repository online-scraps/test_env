<table class="table table-sm">
<thead>
    <tr class="bg-danger text-white">
        <th scope="col">Select</th>
        <th scope="col">S.N.</th>
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
    @foreach($datas as $d)
    

    <div class="po_row">
        <tr>
            <td scope="row">
                <input type="checkbox"  onchange="getValue(this.value)" name="hello" value="1" class="mycheck">
            </td>
            <th scope="row">{{$loop->iteration}}</th>
            <td>{{$d->po_date}}</td>
            <td>{{$d->store_id}}</td>
            <td>{{$d->purchase_order_num}}</td>
            <td>{{$d->purchase_order_type_id}}</td>
            <td>{{$d->supplier_id}}</td>
            <td>{{$d->status_id}}</td>
            <td>test</td>
            <td>{{$d->net_amt}}</td>
            <td>test</td>
        </tr>
    </div>
    @endforeach

</tbody>
</table>
