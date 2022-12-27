<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<style>
    #search {
        background-image: url("/css/images/search.png");
        background-position: left center;
        background-repeat: no-repeat;
        background-size: 15px;
        background-origin: content-box;
        text-indent: 20px;
        text-indent: 20px;
        padding: 1rem;
    }

    #search::placeholder {
        text-align: left;
        padding-left: 10px !important;
    }

    input[type=search]::-webkit-search-cancel-button {
        -webkit-appearance: searchfield-cancel-button;
    }


    .sidebar .nav-link.active {
        color: #a11918;
    }

    .sidebar {
        min-height: auto !important;
    }

    .ui-autocomplete {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 9000;
        float: left;
        display: none;
        min-width: 160px;
        padding: 4px 0;
        margin: 0 0 10px 25px;
        list-style: none;
        background-color: #ffffff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }

    .ui-menu-item>a.ui-corner-all {
        display: block;
        padding: 3px 15px;
        clear: both;
        font-weight: normal;
        line-height: 18px;
        color: #555555;
        white-space: nowrap;
        text-decoration: none;
    }
    .ui-state-hover,
    .ui-state-active {
        color: #ffffff;
        text-decoration: none;
        background-color: #0088cc;
        border-radius: 0px;
        -webkit-border-radius: 0px;
        -moz-border-radius: 0px;
        background-image: none;
    }

    .msg {
        text-align: center;
        color: red;
    }

    .ui-helper-hidden-accessible {
        display: none;
    }
</style>
@php
$user = backpack_user();
@endphp

<div class="my-2">
    <input class="form-control" id='search' type="text" autocomplete="off" placeholder="search..." aria-label="Search" style="width:95%;display:inline;" /><i class="la la-times-circle text-danger" id="search_cancel" style="margin-left: -14%;font-size: 24px;" hidden></i>
</div>

<div id="menu_filtered">
</div>

@if($user->hasAnyRole('superadmin|organizationadmin'))

@else
{{--@php--}}
{{--$model_names = backpack_user()->getAllPermissions()->map(function($permission){--}}
{{--$item = explode(' ', $permission->name);--}}
{{--return end($item);--}}
{{--});--}}

{{--$model_names = array_values(array_unique($model_names->toArray()));--}}
{{--$menus = App\Models\MenuItem::getTree($model_names);--}}
{{--@endphp--}}

{{--@foreach($menus as $menu)--}}
{{--@if(count($menu['children']) > 0)--}}
{{--<li class="nav-item nav-dropdown master_menu">--}}
{{--    <a class="nav-link nav-dropdown-toggle"><i class="nav-icon la la-file"></i><span>{{ $menu['name_lc'] }}</span></span></a>--}}
{{--    <ul class="nav-dropdown-items">--}}
{{--        @foreach($menu['children'] as $menu_child)--}}
{{--        <li class='nav-item'><a class='nav-link' href='{{ backpack_url($menu_child->link) }}'><i class='nav-icon la la-file'></i>{{ $menu_child->name_lc }}</a></li>--}}
{{--        @endforeach--}}
{{--    </ul>--}}
{{--</li>--}}
{{--@else--}}
{{--<li class="nav-item"><a class="nav-link" href="{{ backpack_url($menu['link']) }}"><i class="nav-icon la la-list"></i> <span>{{ $menu['name_lc'] }}</span></a></li>--}}
{{--@endif--}}
{{--@endforeach--}}
@endif


<script src="/js/jquery.min.js"></script>
<script src="/js/jquery-ui.min.js" crossorigin="anonymous"></script>
<script>
    function fetch_menu_data(query = '') {
        open = '';
        $.ajax({
            url: "{{ route('menu_search.action') }}",
            method: 'GET',
            data: {
                query: query
            },
            dataType: 'json',
            async: false,
            success: function(data) {
                localStorage.setItem('menu_data', JSON.stringify(data));
                load_html(data.menu_data,data.icon_list);
            }
        })

    }

    function filter_menu_data(query) {
        open = '';
        if (query != '') {
            open = 'open';
            $('.master_menu').hide()
            $('#search_cancel').prop('hidden', false)
        } else {
            $('.master_menu').show()
            $('#search_cancel').prop('hidden', true)

        }
        var menu_datas = JSON.parse(localStorage.getItem('menu_data'));
        if (query != '') {
            open = 'open';
            var filterdatas = {};
            $.each(menu_datas.menu_data, function(parent_name, rows) {
                // debugger;

                var filteredrows = rows.filter(function(obj) {
                    return (obj.name.includes(query)) || (obj.model_name.includes(query)) || (obj.link.includes(query))

                });

                if (filteredrows != '') {
                    filterdatas[parent_name] = filteredrows;
                }
            });
        } else {
            filterdatas = menu_datas.menu_data;
        }

        load_html(filterdatas,menu_datas.icon_list);
    }

    function load_html(filterdata,iconData) {
        str = '';

        $.each(filterdata, function(parent_name, rows) {
            if(rows.length == 1){
                $.each(rows, function(key, value) {
                    if (key == 'icon')
                        return;

                    selected = '';
                    if (window.location.href.split("/")[4] === value.link) {
                        selected = 'active';
                    }
                    str += "<li class='nav-item'><a class='nav-link " + selected + "' href='/admin/" + value.link + "'><i class='px-1 " + iconData[parent_name] + "'></i> " + value.name.replace('Mst','') + "</a></li>";
                });
            }else{
                str += "<li class='nav-item nav-dropdown " + open + "'><a class='nav-link nav-dropdown-toggle' href='#'><i class='px-2 " + iconData[parent_name] + "'></i>" + parent_name + "</a><ul class='nav-dropdown-items'>";
                $.each(rows, function(key, value) {
                    if (key == 'icon')
                        return;

                    selected = '';
                    if (window.location.href.split("/")[4] === value.link) {
                        selected = 'active';
                    }
                    str += "<li class='nav-item'><a class='nav-link " + selected + "' href='/admin/" + value.link + "'><i class='px-1 " + value.icon + "'></i> " + value.name.replace('Mst','') + "</a></li>";
                });
                str += "</ul></li>";
            }

        });

        $('#menu_filtered').html(str);
        if (filterdata.length == 0) {
            $('#menu_filtered').html('<div class="card msg"><p> No matching item not found !!!</p></div>');
        }
        $('a.active').parent().parent().parent().addClass('open');
    }

    $('#search_cancel').on('click', function() {
        $('#search').val('');
        $('#search').trigger('keyup');
    });
    $(document).ready(function() {
        fetch_menu_data();
        $(document).on('keyup', '#search', function() {
            var query = $(this).val();
            query = query.replace(" ", "_");
            filter_menu_data(query);
        });

    });
</script>