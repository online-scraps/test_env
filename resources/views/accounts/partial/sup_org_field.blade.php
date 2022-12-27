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
    .left{
        float: left;
    }
    .right{
        position: absolute;
        right: 0px;
    }
    .width-60{
        width: 60% !important;
    }
</style>


@php
    $organizations = App\Models\SupOrganization::all();
@endphp

@if (backpack_user()->isSystemUser())
    <div class="row text-center border-bottom mb-3 pb-2">
        <div class="col-md-6 form-inline">
            <div class="form-group col mb-3">
                <label class="left" for="sup_org_id">Organization</label>
                <select name="sup_org_id" id="sup_org_id" class="js-example-basic-single form-control right width-60">
                    <option disabled selected value>-</option>
                    @foreach ($organizations as $org)
                        <option value="{{ $org->id }}" {{ isset($data->sup_org_id) ? ($data->sup_org_id == $org->id ? "selected" : "") : ""}}>{{ $org->name_en }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6 form-inline">
            <div class="form-group col mb-3">
                <label class="left" for="store_id">Store</label>
                <select name="store_id" id="store_id" class="js-example-basic-single form-control right width-60">
                    <option disabled selected value>Select Organization First</option>
                </select>
            </div>
        </div>
    </div>
@else
    <input type="hidden" value="{{ backpack_user()->sup_org_id }}" name="sup_org_id">
    <input type="hidden" value="{{ backpack_user()->store_id }}" name="store_id">
@endif

<script>
    $(document).ready(function () {
        sup_org_id = $('#sup_org_id').val();
        getStoreData(sup_org_id);
    });

    $('#sup_org_id').change(function (e) { 
        sup_org_id = $(this).val();
        getStoreData(sup_org_id);
    });

    function getStoreData(sup_org_id){
        if(sup_org_id){
            $.ajax({
                type: "GET",
                url: "/api/getStore/" + sup_org_id,
                success: function (data) {
                    if(data){
                        $('#store_id').empty();
                        $('#store_id').focus();

                        $.each(data, function (key, value) { 
                            $('#store_id').append('<option class="form-control" value="' + value.id + '">' + value.name_en + '</option>');
                        });
                    }else{
                        $('#store_id').empty();
                    }
                }
            });
        }
    }
</script>
