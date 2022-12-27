<style>
  .sbd {
    font-weight: 700;

  }

  .scan-header {
    background: none !important;
    font-weight: 700;
  }
</style>
@extends(backpack_view('blank'))
@section('content')
<div class="container-fluid">
  <h2>Scan Barcode to Search Product </h2>
  <div class="row">
    <div class="col">
      <div class="card shadow-lg">
        <div class="card-header ">
          <div class="row justify-content-center">
            <div class="col-md-6 input-group ">
              <span class="scan-header mx-3 mt-2"> Scan Barcode
              </span>
              <input type="text" class="form-control rounded-pill" id="barcode_no_input" placeholder="Enter Barcode Here.." size="10" required>
            </div>
          </div>
        </div>
        <div class="card-body" id="br-content">
         <p class="text-center">Please scan barcode to fetch informations</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>

<script>
  $(document).ready(function() {
    function getBarcodeReport(barcode_no) {
      let url = '{{ route("custom.barcode-report-details", ":num" ) }}'
      url = url.replace(':num', barcode_no?barcode_no:null);
     


      $.get(url).then(function(response) {
        $("#br-content").html(response);
        console.log("helllo",response);
      })
    }

    $('#barcode_no_input').keyup(function(event) {
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if (keycode == '13') {
        // let barcode_no=$(this).val();
        console.log("inside event")
        getBarcodeReport($(this).val());
      }
    });

   
  });
</script>