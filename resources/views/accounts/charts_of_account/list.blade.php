@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
        trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
        $crud->entity_name_plural => url($crud->route),
        trans('backpack::crud.add') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;

    $organizations = App\Models\SupOrganization::select('id','name_en')->get();
    foreach($organizations as $org){
        $organization[$org->id] = $org->name_en;
    }
    
    $stores = App\Models\MstStore::select('id','name_en')->get();
    foreach($stores as $s){
        $store[$s->id] = $s->name_en;
    }
@endphp

@section('header')
    <section class="main-container">
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
            <small>{!! $crud->getSubheading() ?? trans('backpack::crud.add').' '.$crud->entity_name !!}.</small>

            @if ($crud->hasAccess('list'))
            <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
            @endif
        </h2>
    </section>
@endsection

@section('after_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.fancybox.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.treegrid.css') }}">
    <style>
        .select2-selection__rendered {
            line-height: 31px !important;
        }
        .select2-container .select2-selection--single {
            height: 35px !important;
        }
        .select2-selection__arrow {
            height: 34px !important;
        }
    </style>
@endsection 

@section('content')
    <div class="row">
        <div class="{{ $crud->getListContentClass() }}">
            <div class="buttons mb-2">
                <a href="{{ url($crud->route) }}/create" class="btn btn-sm btn-primary" id="addLedgerBtn"><i class="la la-plus"></i>&nbsp;Add Ledger</a>
                <a data-fancybox data-type="ajax" data-src="{{ route('createGroup') }}" class="btn btn-sm btn-primary" id="addGroupBtn" href="javascript:;"><i class="la la-plus"></i>&nbsp;Add Group</a>
            </div>

            <div class="table">
                <table class="table table-hover bg-white" id="chartsOfAccountTable">
                    <thead>
                        <tr>
                            <th style="widht:60%">Generic Charts of Account</th>
                            <th style="widht:10%">Alias</th>
                            @if(backpack_user()->isSystemUser())
                                <th>Organization</th>
                                <th>Store</th>
                            @elseif(backpack_user()->isOrganizationUser() && backpack_user()->store_id == null)
                                <th>Store</th>
                            @endif
                            <th style="widht:25%">Amount</th>
                            <th style="widht:5%">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        {{ session()->put('i', 1)}}
                        @foreach($roots as $root)
                            @php 
                                $i = session()->get('i');
                            @endphp
                            <tr class="treegrid-{{ $i }}">
                                <td>
                                    {{ $root->name }}
                                </td>
                                <td>{{ isset($root->alias) ? $root->alias : null }}</td>

                                @if(backpack_user()->isSystemUser())
                                    <td>{{ isset($root->sup_org_id) ? $organization[$root->sup_org_id] : null }}</th>
                                    <td>{{ isset($root->store_id) ? $store[$root->store_id] : null }}</td>
                                @elseif(backpack_user()->isOrganizationUser() && backpack_user()->store_id == null)
                                    <td>{{ isset($root->store_id) ? $store[$root->store_id] : null }}</td>
                                @endif

                                <td>{{ isset($root->opening_balance) ? $root->dr_cr.$root->opening_balance : '-' }}</td>
                                <td>
                                    <!-- edit button -->
                                    @if(backpack_user()->isStoreUser() && isset($root->sup_org_id) && $root->store_id == null)
                                    @elseif($root->is_group == true && $root->sup_org_id != 1)
                                        <a data-fancybox data-type="ajax" data-src="{{ route('getGroupInfo', $root->id) }}" class="btn btn-sm btn-success" href="javascript:;" data-toggle="tooltip" title="Edit"><i class="la la-edit"></i></a>
                                    @elseif($root->is_ledger == true && $root->sup_org_id != 1)
                                        <a href="{{ url($crud->route.'/'.$root->id.'/edit') }}" class="btn btn-sm btn-success" data-toggle="tooltip" title="Edit"><i class="la la-edit"></i></a>
                                    @else
                                    @endif
                            
                                    <!-- delete button -->
                                    @if(backpack_user()->isStoreUser() && isset($root->sup_org_id) && $root->store_id == null)
                                    @elseif((count($root->childs) > 0) || (count($root->subLedgers) > 0)) 
                                    @elseif((!(count($root->childs) > 0) || !(count($root->subLedgers) > 0) ) && $root->sup_org_id != 1)
                                        <button class="btn btn-sm btn-danger" onclick="deleteCoa('{{ $root->id }}')" data-toggle="tooltip" title="Delete"><i class="la la-trash"></i></button>
                                    @else
                                    @endif
                                </td>
                            </tr>
                            @if(count($root->subLedgers))
                                {{ session()->put('i', $i) }}
                                @include('accounts.charts_of_account.partials.child', ['childs' => $root->subLedgers, 'parent_id' => $i])
                            @elseif(count($root->childs))
                                @include('accounts.charts_of_account.partials.child', ['childs' => $root->childs, 'parent_id' => $i])
                            @endif
                            
                            @php 
                                $i = session()->get('i');
                                $i = $i + 1;
                                session()->put('i', $i);
                            @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('after_scripts')
    <script src="{{ asset('js/jquery.treegrid.min.js') }}"></script>
    @include('accounts.charts_of_account.partials.scripts')
    <script>
        $('#chartsOfAccountTable').treegrid();

        $('#chartsOfAccountTable').DataTable({
            dom: '<"top"if>rt<"bottom"lp>',
            searching: true,
            paging: true,
            ordering: false,
            select: false,
            bInfo : true,
            lengthChange: true,
            lengthMenu: [
                [-1, 10, 25, 50, 100],
                ['All', '10', '25', '50', '100']
            ],
            oLanguage: {
                oPaginate: {
                    sPrevious: '<i class="la la-angle-left"></i>',
                    sNext: '<i class="la la-angle-right"></i>',
                }
            }
        });
    </script>
@endsection