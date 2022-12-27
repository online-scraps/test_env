<div class="card" id="editGroupFancybox">
    <div class="card-header"></div>
    <div class="card-body">
        @if(isset($data))
        <form action="{{ url($crud->route.'/'. $data->id) }}" method="POST" id="updateGroupForm">
        @else
        <form action="{{ url($crud->route) }}" method="POST" id="createGroupForm">    
        @endif
            <div class="modal-body">
                @csrf

                @if(isset($data))
                    @method('PUT')
                @endif

                @include('accounts.charts_of_account.partials.org_store')

                <input type="hidden" name="group" value="1" id="groupValue">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="group_name">Group<span class="text-danger">&nbsp;*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ isset($data->name) ? $data->name : null }}" id="group_name">
                    </div>

                    <div class="form-group col-md-6">
                        <label for="group_alias">Alias</label>
                        <input type="text" class="form-control" name="alias" value="{{ isset($data->alias) ? $data->alias : null }}" id="group_alias">
                    </div>

                    <div class="form-goup col-md-4">
                        <label for="primary_group">Primary Group?</label>
                        <select class="form-control" name="primary_group" id="primary_group">
                            <option class="form-control" value="0" {{ isset($data->primary_group) && $data->primary_group == 0 ? 'selected' : null }}>No</option>
                            <option class="form-control" value="1" {{ isset($data->primary_group) && $data->primary_group == 1 ? 'selected' : null }}>Yes</option>
                        </select>
                    </div>

                    <div class="form-goup col-md-4">
                        <label for="group_id">Under<span class="text-danger">&nbsp;*</span></label>
                        <select class="form-control" name="group_id" id="group_id">
                            @foreach($groups as $under)
                                <option class="form-control" value="{{ $under->id }}"  {{ isset($data->group_id) && $data->group_id == $under->id ? 'selected' : null }}>{{ $under->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="closeFancyBox" class="btn btn-secondary"><i class="la la-ban"></i>&nbsp;Close</button>
                <button type="submit" class="btn btn-success" id="updateGroup"><i class="la la-save"></i>&nbsp;Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    // close the fancy box
    $('#closeFancyBox').click(function (e) { 
        e.preventDefault();
        $.fancybox.close(true);
    });

    // group create submit and validation
    $('#createGroupForm').submit(function (e) { 
        $('#group_name').rules("add", {
            required: true,
            messages: {
                required: "Field required",
            }
        });

        $('#group_id').rules("add", {
            required: true,
            messages: {
                required: "Field required",
            }
        });
    });

    $('#createGroupForm').validate({
        submitHandler: function(form){
            let data = $('#createGroupForm').serialize();
            let url = form.action;
            
            axios.post(url, data).then((response) => {
                if(response.data.status === 'success'){
                    new Noty({
                        type: "success",
                        text: response.data.message,
                    }).show();
                    $.fancybox.close(true);
                    location.reload();
                }else{
                    new Noty({
                        type: "error",
                        text: response.data.message,
                    }).show();
                }
            });
        }
    });
    
    // group update submit and validation
    $('#updateGroupForm').on('submit', function () {
        $('#group_name').rules("add", {
            required: true,
            messages: {
                required: "Field required",
            }
        });

        $('#group_id').rules("add", {
            required: true,
            messages: {
                required: "Field required",
            }
        });
    });
    $('#updateGroupForm').validate({
        submitHandler: function(form){
            let data = $('#updateGroupForm').serialize();
            let url = form.action;
            
            axios.post(url, data).then((response) => {
                if(response.data.status === 'success'){
                    new Noty({
                        type: "success",
                        text: response.data.message,
                    }).show();
                    $.fancybox.close(true);
                    location.reload();
                }else{
                    new Noty({
                        type: "error",
                        text: response.data.message,
                    }).show();
                }
            });
        }
    });

    // copy group name to alias
    $('#group_name').keyup(function (e) { 
        $('#group_alias').val($(this).val());
    });
</script>