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
    label{
        font-weight: bold;
    }
</style>

@if(backpack_user()->isSystemUser())
    <div class="row border-bottom mb-2">
        <div class="form-group col-md-6">
            <label for="supOrgId">Organization</label>
            <select name="sup_org_id" id="supOrgId" class="form-control searchselect">
                <option disabled selected>-</option>
                @foreach ($organizations as $org)
                    <option value="{{ $org->id }}" {{ isset($data->sup_org_id) && $data->sup_org_id == $org->id ? 'selected' : null }}>{{ $org->name_en }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-6">
            <label for="store_id">Store</label>
            <select name="storeId" id="storeId" class="form-control searchselect">
                <option disabled selected>Select Organization First</option>
            </select>
        </div>
    </div>
@else 
    <input type="hidden" value="{{ backpack_user()->sup_org_id }}" name="sup_org_id">
    <input type="hidden" value="{{ backpack_user()->store_id }}" name="store_id">
@endif

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        sup_org_id = $('#supOrgId').val();
        getStoreDatas(sup_org_id);
    });

    $('#supOrgId').change(function (e) { 
        sup_org_id = $(this).val();
        getStoreDatas(sup_org_id);
    });

    function getStoreDatas(sup_org_id){
        if(sup_org_id){
            $.ajax({
                type: "GET",
                url: "/api/getStore/" + sup_org_id,
                success: function (data) {
                    if(data){
                        $('#storeId').empty();
                        $('#storeId').focus();

                        $.each(data, function (key, value) { 
                            $('#storeId').append('<option class="form-control" value="' + value.id + '">' + value.name_en + '</option>');
                        });
                    }else{
                        $('#storeId').empty();
                    }
                }
            });
        }
    }
</script>