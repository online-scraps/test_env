
@php
$status=[
   true=>'text-success',
   false=>'text-danger',
   ];
@endphp
@if(isset($data))
{{-- {{ dd($data) }} --}}
<div class="card-body">
 <h3 class="text-center mb-5 sbd ">Barcode Number:<span id="barcode_num">{{$data->barcode_details}}</span></h3>
 <div class="row">
   <div class="col-md-6 mb-3">
     <h3>Stock Detail</h3>
     <ul class="list-group">
       <li class="list-group-item">
         <span class="sbd">Store Name : </span>{{$data->stockItem->stock->mstStore->name_en}}
       </li>
       <li class="list-group-item">
         <span class="sbd">Product Name : </span>{{$data->stockItem->mstItem->name}}
       </li>
       <li class="list-group-item">
         <span class="sbd">Batch Number : </span>{{$data->stockItem->batch_no}}
       </li>
       <li class="list-group-item">
         <span class="sbd">Stock Entry Date : </span>{{$data->stockItem->stock->entry_date_bs}}
       </li>
       <li class="list-group-item">
         <span class="sbd">Stock Adj No : </span>{{$data->stockItem->stock->adjustment_no}}
       </li>
       <li class="list-group-item">
         <span class="sbd">Status : </span><span class="sbd {{ $status[$data->is_active]}}">{{$data->is_active?'In Stock':($data->sales_item_id?'Sold out':'Item not available')}}</span>
       </li>
     </ul>
   </div>

   @if(isset($data->sales_item_id))

   <div class="col-md-6">
     <h3>Buyer's Detail</h3>
     <ul class="list-group">
       <li class="list-group-item">
         <span class="sbd">Buyer Name : </span>{{ucfirst($data->salesItem->sales->customerEntity->name_en)}}
       </li>
       <li class="list-group-item">
         <span class="sbd">Phone : </span>{{isset($data->salesItem->sales->customerEntity->contact_number) ? $data->salesItem->sales->customerEntity->contact_number : 'N/A'}}
       </li>
       <li class="list-group-item">
         <span class="sbd">Bill No : </span>{{$data->salesItem->sales->bill_no}}
       </li>
       <li class="list-group-item">
         <span class="sbd">Bill Date : </span>{{$data->salesItem->sales->bill_date_bs}}
       </li>
       <li class="list-group-item">
         <span class="sbd">Company Name : </span>{{isset($data->salesItem->sales->customerEntity->company_name) ? $data->salesItem->sales->customerEntity->company_name : 'N/A'}}
       </li>
       <li class="list-group-item">
         <span class="sbd">Pan/Vat : </span>{{isset($data->salesItem->sales->customerEntity->pan_no) ? $data->salesItem->sales->customerEntity->pan_no : 'N/A'}}
       </li>


     </ul>
   </div>
   @endif
 </div>
</div>
@else
<div class="card-body">
 <h4 class="text-center text-danger">No Data matched with entered barcode</h3>
</div>
@endif
