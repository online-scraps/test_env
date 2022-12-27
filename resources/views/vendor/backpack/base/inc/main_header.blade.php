@php
$date = convert_bs_from_ad();
@endphp


<header class="{{ config('backpack.base.header_class') }}">
  <!-- Logo -->
  <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto ml-3" type="button" data-toggle="sidebar-show" aria-label="{{ trans('backpack::base.toggle_navigation')}}">
    <span class="navbar-toggler-icon"></span>
  </button>
  <a class="navbar-brand" href="{{ url(config('backpack.base.home_link')) }}" title="{{ config('backpack.base.project_name') }}">
    <img src="{{ asset('css/images/dhanshar.png') }}" style="max-width: 140px" alt="Dhanshar logo"/>
  </a>
  <button class="navbar-toggler sidebar-toggler d-md-down-none ml-2" type="button" data-toggle="sidebar-lg-show" aria-label="{{ trans('backpack::base.toggle_navigation')}}">
    <span class="navbar-toggler-icon"></span>
  </button>

  @include(backpack_view('inc.menu'))
</header>

{{-- @php
$request = request();
$session = $request->getSession();
$current_tab = $session->get('current_tab');
@endphp --}}

@push('after_scripts')
<!-- <script>
  function startTime() {
    var today = new Date();
    var h = today.getHours();
    var ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12;
    h = h ? h : 12;
    var m = today.getMinutes();
    var s = today.getSeconds();
    h=checkTime(h);
    m = checkTime(m);
    s = checkTime(s);
    document.getElementById('txt').innerHTML =
    h + ":" + m + ":" + s+ " " + ampm;
    var t = setTimeout(startTime, 500);
  }
  function checkTime(i) {
    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
    return i;
  }
  
  window.onload=startTime();

  
  </script> -->
@endpush