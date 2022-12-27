@extends(backpack_view('blank'))

@php
  $defaultBreadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    $crud->entity_name_plural => url($crud->route),
    trans('backpack::crud.list') => false,
  ];

  // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
  $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
  <div class="container-fluid">
    <h2>
      <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
      <small id="datatable_info_stack">{!! $crud->getSubheading() ?? '' !!}</small>
    </h2>
  </div>
@endsection


<style>

    .report-heading {
        text-align: center;
        font-size:13px;
        /* font-family: 'Kalimati'; */
    }
    .report-data {
        font-size:13px;
        font-weight: 600;
        padding-left:25px !important; 
        color:black;
        line-height: 1.5rem;

        /* font-family: 'Kalimati'; */
    }
    tr>th{
        border-bottom: 1px solid white !important;
        border-right: 1px solid white !important;
        background-color:#c8ced3 !important;
        color:black;
    }
    .th_large{
         min-width:130px !important;
         line-height: 2rem;
     }
 </style>

@php
$permissions = \App\Models\Permission::all();
$models = modelCollection();
@endphp

@section('content')
  <!-- Default box -->
  <div class="row">

    <!-- THE ACTUAL CONTENT -->
    <div class="{{ $crud->getListContentClass() }}">
        <div class="col p-2" style="overflow-x:auto;">
            <table id="permission_data_table" class="table table-bordered table-striped table-hover table-sm" style="background-color:rgba(210, 218, 240, 0.05)">
                <thead>
                    <tr>
                        <th class="report-heading">S.N.</th>
                        <th class="report-heading th_large">Model Name</th>
                        <th class="report-heading th_large">Permissions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($models as $key=>$name)
                    <tr>
                        <td class="report-data text-center">{{$loop->iteration}}</td>
                        <td class="report-data">{{$name}}</td>
                        <td class="report-data"> list  ,  create  ,  update  ,  delete</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
         </div>
    </div>

  </div>

@endsection

@section('after_styles')
  <!-- DATA TABLES -->
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">

  <!-- CRUD LIST CONTENT - crud_list_styles stack -->
  @stack('crud_list_styles')
@endsection

@section('after_scripts')
<script>
    $(document).ready(function () {
    $('#permission_data_table').DataTable({
        searching: false,
        paging: true,
        ordering:false,
        select: false,
        bInfo : true,
        lengthChange: false
    });
});
</script>
@endsection

