<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<style>
    #search { 
      background-image: url("/css/images/search.png");
      background-position: left center;
      background-repeat: no-repeat;
      background-size: 15px;
      background-origin: content-box;
      text-indent: 20px;
      /* padding-right: 0.5rem; */
    }
    #search::placeholder{
        text-align:left;
        padding-left: 10px !important;
    }
    input[type=search]::-webkit-search-cancel-button {
        -webkit-appearance: searchfield-cancel-button;
    }
    
    
    .sidebar .nav-link.active {
        color: #a11918;
    }
    .sidebar{
        height:100% !important;
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
    
    .ui-menu-item > a.ui-corner-all {
        display: block;
        padding: 3px 15px;
        clear: both;
        font-weight: normal;
        line-height: 18px;
        color: #555555;
        white-space: nowrap;
        text-decoration: none;
    }
    
    .ui-state-hover, .ui-state-active {
        color: #ffffff;
        text-decoration: none;
        background-color: #0088cc;
        border-radius: 0px;
        -webkit-border-radius: 0px;
        -moz-border-radius: 0px;
        background-image: none;
    }
    .msg{
        text-align:center;
        color: red;
    }
    .ui-helper-hidden-accessible{
        display:none;
    }
    </style>
@php 
$user = backpack_user();
@endphp

<div class="mb-2">
	<input class="form-control" id='search' type="text" placeholder="search..." aria-label="Search" style="width:95%;display:inline;"/><i class="fa fa-times-circle" id="search_cancel" style="margin-left: -14%;font-size: 24px;" hidden></i>
</div>

<div id="menu_filtered">
</div>

@if($user->hasAnyRole('superadmin|organizationadmin'))

    @if($user->hasRole('superadmin'))

    {{-- client --}}
    <li class="nav-item nav-dropdown master_menu">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-file-alt"></i>Organization</a>
        <ul class="nav-dropdown-items" style="overflow-x:hidden">
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('sup-organization') }}'><i class='nav-icon la la-cogs'></i>Super Organization</a></li>
            {{-- <li class='nav-item'><a class='nav-link' href='{{ backpack_url('appsettings') }}'><i class='nav-icon la la-cogs'></i>Client Setting</a>
    </li> --}}
    </ul>
    </li>


    {{-- Super Master --}}
    <li class="nav-item nav-dropdown master_menu">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-file-alt"></i>Super Master</a>
        <ul class="nav-dropdown-items" style="overflow-x:hidden">
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('mst-country') }}'><i class='nav-icon la la-cogs'></i> Countries</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('mst-province') }}'><i class='nav-icon la la-cogs'></i> Provinces</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('mst-district') }}'><i class='nav-icon la la-cogs'></i> Districts</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('mst-gender') }}'><i class='nav-icon la la-cogs'></i> Genders</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('sup-status') }}'><i class='nav-icon la la-cogs'></i> status</a></li>


        </ul>
    </li>
    @endif

    {{-- Primary Master --}}
    <li class="nav-item nav-dropdown master_menu">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-file-alt"></i>Primary Master</a>
        <ul class="nav-dropdown-items" style="overflow-x:hidden">
            <li class='nav-item'><a class='nav-link' href='{{backpack_url('mst-item')}}'><i class='nav-icon la la-cogs'></i> Items</a></li>
            <li class='nav-item'><a class='nav-link' href='{{backpack_url('mst-store')}}'><i class='nav-icon la la-cogs'></i> Stores</a></li>

            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('mst-units') }}'><i class='nav-icon la la-cogs'></i> Units</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('mst-disc-mode') }}'><i class='nav-icon la la-cogs'></i> Disc Modes</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('mst-category') }}'><i class='nav-icon la la-cogs'></i> Categories</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('mst-subcategory') }}'><i class='nav-icon la la-cogs'></i> Sub categories</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('mst-supplier') }}'><i class='nav-icon la la-cogs'></i> Suppliers</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('mst-brand') }}'><i class='nav-icon la la-cogs'></i> Brands</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('mst-position') }}'><i class='nav-icon la la-cogs'></i> Positions</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('mst-department') }}'><i class='nav-icon la la-cogs'></i>Departments</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('mst-relation') }}'><i class='nav-icon la la-cogs'></i>Relations</a></li>
        </ul>
    </li>

    @if($user->hasRole('superadmin'))

    {{-- Meta Master --}}
    <li class="nav-item nav-dropdown master_menu">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-file-alt"></i>Meta</a>
        <ul class="nav-dropdown-items" style="overflow-x:hidden">
            <li class='nav-item'><a class='nav-link' href='{{backpack_url('invoice-sequence')}}'><i class='nav-icon la la-cogs'></i> Invoice Sequences</a></li>
            <li class='nav-item'><a class='nav-link' href='{{backpack_url('purchase-return-sequence')}}'><i class='nav-icon la la-cogs'></i> PR Sequences</a></li>
            <li class='nav-item'><a class='nav-link' href='{{backpack_url('po-sequence')}}'><i class='nav-icon la la-cogs'></i> PO Sequences</a></li>
            <li class='nav-item'><a class='nav-link' href='{{backpack_url('grn-sequence')}}'><i class='nav-icon la la-cogs'></i> GRN Sequences</a></li>
            <li class='nav-item'><a class='nav-link' href='{{backpack_url('return-reason')}}'><i class='nav-icon la la-cogs'></i> Return Reasons</a></li>
        </ul>
    </li>
    @endif

    {{-- Sales --}}
    <li class="nav-item nav-dropdown master_menu">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-file-alt"></i>Sales</a>
        <ul class="nav-dropdown-items" style="overflow-x:hidden">
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('sales') }}'><i class='nav-icon la la-cogs'></i> Sales</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('sales-items-details') }}'><i class='nav-icon la la-cogs'></i> Sales items details</a></li>
        </ul>
    </li>


    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('hr-employee') }}'><i class='nav-icon la la-cogs'></i> Hr employees</a></li>


    <li class="nav-item nav-dropdown master_menu">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-file-alt"></i>Inventory Management</a>
        <ul class="nav-dropdown-items" style="overflow-x:hidden">
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('purchase-order-type') }}'><i class='nav-icon la la-cogs'></i> Purchase order types</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('purchase-order-detail') }}'><i class='nav-icon la la-cogs'></i> Purchase order details</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('purchase-item') }}'><i class='nav-icon la la-cogs'></i> Purchase items</a></li>



            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('grn') }}'><i class='nav-icon la la-cogs'></i> Grns</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('grn-item') }}'><i class='nav-icon la la-cogs'></i> Grn items</a></li>


            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('purchase-return') }}'><i class='nav-icon la la-cogs'></i> Purchase returns</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('purchase-return-item') }}'><i class='nav-icon la la-cogs'></i> Purchase return items</a></li>
        </ul>
    </li>

    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('menu-item') }}'><i class='nav-icon la la-list'></i> Menu Management</a></li>


    <!-- Users, Roles, Permissions -->
    <li class="nav-item nav-dropdown master_menu">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-users"></i>Users Management</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-id-badge"></i> <span>Roles</span></a></li>

            @if($user->hasRole('superadmin'))

            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>Permissions</span></a></li>
            @endif
            <li class='nav-item'><a class='nav-link' href='{{backpack_url('users')}}'><i class='nav-icon la la-users'></i> Users</a></li>
        </ul>
    </li>

@else
    @php
        $model_names = backpack_user()->getAllPermissions()->map(function($permission){
            $item = explode(' ', $permission->name);
            return end($item);
        });

        $model_names = array_values(array_unique($model_names->toArray()));     
        $menus = App\Models\MenuItem::getTree($model_names);
    @endphp

    @foreach($menus as $menu)
        @if(count($menu['children']) > 0)
            <li class="nav-item nav-dropdown master_menu">
                <a class="nav-link nav-dropdown-toggle"><i class="nav-icon la la-file"></i><span>{{ $menu['name_lc'] }}</span></span></a>
                <ul class="nav-dropdown-items">
                    @foreach($menu['children'] as $menu_child)
                        <li class='nav-item'><a class='nav-link' href='{{ backpack_url($menu_child->link) }}'><i class='nav-icon la la-file'></i>{{ $menu_child->name_lc }}</a></li>
                    @endforeach
                </ul>
            </li>
            @else
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url($menu['link']) }}"><i class="nav-icon la la-list"></i> <span>{{ $menu['name_lc'] }}</span></a></li>
        @endif
    @endforeach
@endif

<script src="/js/jquery.min.js"></script>
<script src="/js/jquery-ui.min.js" crossorigin="anonymous"></script>
<script>



    
var availableTags = [];
    $( "#search" ).autocomplete({
	  minLength: 0,
	  delay: 0,
	  autoFocus: true,
	  disabled: true,
      source: availableTags,
	  messages: {
        noResults: '',
        results: function() {
            return ''
        }
    }
    });

	function fetch_menu_data(query = '')
		{
			open = '';
			$.ajax({
				url:"{{ route('menu_search.action') }}",
				method:'GET',
				data:{query:query},
				dataType:'json',
				async: false,
				success:function(data)
				{
					localStorage.setItem('menu_data', JSON.stringify(data));
					load_html(data.menu_data);
					var tags = data.tags;
					$.each(tags,function(key,value){
						$.each(value.name_en.split('_'),function(index,val){
							if (!availableTags.includes(val))
								availableTags.push(val);
						});
					});
				}
			})

		}

	function filter_menu_data(query){
		open = '';
		if(query != ''){
			open ='open';
			$('.master_menu').hide()
			$('#search_cancel').prop('hidden',false)
		}
		else{
			$('.master_menu').show()
			$('#search_cancel').prop('hidden',true)

		}
		var menu_datas = JSON.parse(localStorage.getItem('menu_data'));
		
		if(query  !=''){
			open='open';
			filterdata = new Array();
		$.each(menu_datas.menu_data,function(index,rows){
				var filteredrows = rows.filter(function(obj) {
					return (obj.name.includes(query)) || (obj.model_name.includes(query) || (obj.link.includes(query)));
				});
				if(filteredrows !=''){
					filterdata.push(filteredrows);
				}
			
		});
	}
	else{
		filterdata = data.menu_data;
	}
		load_html(filterdata);
	}

	function load_html(filterdata){
		str = '';
		$.each(filterdata,function(parent_name,rows){
			str += "<li class='nav-item nav-dropdown "+open+"'><a class='nav-link nav-dropdown-toggle' href='#'><i class='nav-icon la la-folder'></i>"+parent_name+"</a><ul class='nav-dropdown-items'>";
                $.each(rows,function(key,value){
                    selected = '';
                    if(window.location.href.split("/")[4] === value.link){
                        selected = 'active';
                    }
                    
                str += "<li class='nav-item'><a class='nav-link " + selected + "' href='/admin/" + value.link + "'><i class='nav-icon la la-file'></i>" + value.name + "</a></li>";
                });
			str += "</ul></li>";
		});

		$('#menu_filtered').html(str);
		if(filterdata.length == 0){
			$('#menu_filtered').html('<div class="card msg"><p>सिफारिस प्रकार उपलब्ध छैन !!!</p></div>');
		}
		$('a.active').parent().parent().parent().addClass('open');
	}
	
	$('#search_cancel').on('click',function(){

		$('#search').val('');
		$('#search').trigger('keyup');
		

	});
$(document).ready(function(){

	fetch_menu_data();

	$(document).on('keyup', '#search', function(){
		var query = $(this).val();
		query = query.replace(" ", "_");
		filter_menu_data(query);
	});
	
});
</script>
