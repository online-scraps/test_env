@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
        trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
        $crud->entity_name_plural => url($crud->route),
        trans('backpack::crud.add') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;

    $i = 0;
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
    <style>
        label{
            font-weight: bold;
        }
        
        label.error{
            min-width: 100% !important;
            font-size: 0.8rem;
            color: red;
            display: block;
        }

        span.select2 span.selection .error{
            border-color: rgba(255, 0, 0, 0.26);
            box-shadow: 0 0 0 0.25rem rgba(255, 0, 0, 0.25);
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
        @if(isset($voucher_group_setting))
            <form action="{{ url($crud->route).'/'.$voucher_group_setting->id }}" method="POST" id="voucherGroupSetting">
                @method('PUT')
                @csrf
                @include('accounts.partial.sup_org',['data' => $voucher_group_setting])
            @else
            <form action="{{ url($crud->route) }}" method="POST" id="voucherGroupSetting">
                @csrf 
                @include('accounts.partial.sup_org')
        @endif
            <div class="row">
                    @foreach($vouchers as $key => $voucher)
                        <div class="col-6">
                            <h4 class="font-weight-bold">{{ $voucher['name'] }}</h4>
                            <div class="row">
                                <input type="hidden" name="voucher_id[]" value="{{ $key }}">

                                <div class="form-group col-md-4">
                                    <label for="dr_cr">Dr./Cr.</label>
                                    <select class="form-control" name="dr_cr[]" id="dr_cr" disabled>
                                        @foreach($dr_cr as $key => $value)
                                            <option value="{{ $key }}" {{ $voucher['dr_cr'] == $key ? 'selected' : null }}>{{ $value['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="from-group col-md-8">
                                    <label for="group_id_{{ $loop->iteration }}">Group</label>
                                    <select multiple class="form-control group" name="group_id[{{ $i }}][]" id="group_id_{{ $loop->iteration }}">
                                        @foreach($groups as $key => $value)
                                            <option class="from-control" value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @php 
                            $i++;
                        @endphp 
                    @endforeach
                </div>
                <div class="float-right">
                    <button type="submit" class="btn btn-md btn-success"><i class="la la-save"></i>&nbsp;Save</button>
                    <a href="{{ url($crud->route) }}" class="btn btn-md btn-secondary text-dark"><i class="la la-ban"></i>&nbsp;Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('after_scripts')
    @include('accounts.voucher_group_setting.partials.scripts')
@endsection