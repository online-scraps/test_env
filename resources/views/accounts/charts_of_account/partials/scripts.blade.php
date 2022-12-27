<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="{{ asset('js/fancybox.v3.5.7.min.js') }}"></script>
<script src="{{ asset('packages/datatables.net-bs4/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('packages/select2/dist/js/select2.min.js') }}"></script>

<script>
    $(document).ready(function () {
        sdst = $('#specify_default_sales_type').val();
        specify_default_sales_type(sdst);

        sdst = $('#specify_default_purchase_type').val();
        specify_default_purchase_type(sdst);

        $('#group_id').select2();
        $('#ledger_id').select2();
        
        account_setting = '<?php echo ($account_setting) ?>';

        if(account_setting.length > 0){
            account_setting = JSON.parse(account_setting);
            if(account_setting.maintain_sub_ledgers){
                ledger_subledger();
            }
        }else{
            $('.group').removeClass('d-none');
        }
    });

    // ledger subledger toggle
    function ledger_subledger(){
        ledger_type  = $('#ledger_type').val();
        if(ledger_type == 1){
            $('.group').removeClass('d-none');
            $('.ledger_id').addClass('d-none');

            $('[name="ledger_id"]').val('').trigger('change');
            // console.log(ledger_id);
        }else{
            $('.group').addClass('d-none');
            $('.ledger_id').removeClass('d-none');

            $('[name="group_id"]').val('').trigger('change');
            // console.log(group_id);
        }
    }

    // copy name to print name
    $('#coa_name').keyup(function (e) { 
        $('#print_name').val($(this).val());
    });

    // on group add button click open bootstrap modal
    $('.modal ').insertAfter($('body'));
    $('#createNewGroup').click(function (e) { 
        e.preventDefault();
        $('#createNewGroupModal').modal();
        $('#createNewGroupModal').on('shown.bs.modal', function () {
            $(this).find('[autofocus]').focus();
        });
    });

    // copy group name to alias
    $('#group_name').keyup(function (e) { 
        $('#group_alias').val($(this).val());
    });

    //on speficy default sales type change enable or disable fields 
    $('#specify_default_sales_type').change(function (e) { 
        e.preventDefault();
        value = $(this).val();
        specify_default_sales_type(value);
    });
    function specify_default_sales_type(value){
        if(value == 0){
            $('#default_sales_type').prop('disabled','disabled');
            $('#default_sales_type').val(0).trigger('change');

            $('#freeze_sale_type').prop('disabled','disabled');
            $('#freeze_sale_type').val(0).trigger('change');
        }else{
            $('#default_sales_type').removeAttr('disabled');
            $('#freeze_sale_type').removeAttr('disabled');
        }
    }

    // on specify default purhase type enable or disable fields
    $('#specify_default_purchase_type').change(function (e) { 
        e.preventDefault();
        value = $(this).val();
        specify_default_purchase_type(value);
        
    });
    function specify_default_purchase_type(value){
        if(value == 0){
            $('#default_purchase_type').prop('disabled','disabled');
            $('#default_purchase_type').val(0).trigger('change');

            $('#freeze_purchase_type').prop('disabled','disabled');
            $('#freeze_purchase_type').val(0).trigger('change');
        }else{
            $('#default_purchase_type').removeAttr('disabled');
            $('#freeze_purchase_type').removeAttr('disabled');
        }
    }

    $('#ledger_type').change(function (e) { 
        ledger_subledger();
    });

    // charts of accoutn form submit and validation 
    $('#chartsOfAccountForm').on('submit', function () {
        $('#coa_name').rules("add", {
            required: true,
            messages: {
                required: "Field required",
            }
        });

        ledger_type  = $('#ledger_type').val();
        if(ledger_type == 1){
            groupValidate();
        }else{
            ledgerValidate();
        }

        $('#ledger_type').change(function (e) { 
            ledger_type  = $('#ledger_type').val();
            if(ledger_type == 1){
                groupValidate();
            }else{
                ledgerValidate();
            }
        });
    });
    function groupValidate(){
        $('#group_id').rules("add", {
            required: true,
            messages: {
                required: "Field required",
            }
        });
    }
    function ledgerValidate(){
        $('#ledger_id').rules("add", {
            required: true,
            messages: {
                required: "Field required",
            }
        });
    }
    $('#chartsOfAccountForm').validate({
        highlight: function(element) {
            element = $(element);

            if(element[0]['id'] == 'group_id' || element[0]['id'] == 'ledger_id'){
                spanElement = element.next();
                spanElement = spanElement.children('span.selection');
                spanElement = spanElement.children();

                spanElement.addClass('error');
            }
        },
        submitHandler: function(form){
            let data = $('#chartsOfAccountForm').serialize();
            let url = form.action;

            axios.post(url, data).then((response) => {
                if(response.data.status === 'success'){
                    // new Noty({
                    //     type: "success",
                    //     text: response.data.message,
                    // }).show();
                    window.location.href = response.data.route;
                }else{
                    new Noty({
                        type: "error",
                        text: response.data.message,
                    }).show();
                }
            });
        }
    });

    // group create submit and validation
    $('#saveGroupForm').on('submit', function () {
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
    $('#saveGroupForm').validate({
        submitHandler: function(form){
            let data = $('#saveGroupForm').serialize();
            let url = form.action;
            
            axios.post(url, data).then((response) => {
                if(response.data.status === 'success'){
                    new Noty({
                        type: "success",
                        text: response.data.message,
                    }).show();

                    $('#createNewGroupModal').modal('toggle');
                    $('#createNewGroupModal form')[0].reset();

                    $.ajax({
                        type: "get",
                        url: '{{ route("getGroupData") }}',
                        success: function (data) {
                            if(data){
                                $('#group_id').empty();
                                $('#group_id').focus();
                                $('#group_id').append('<option value="">-</option>');

                                var selected_id = response.data.id;

                                $.each(data, function(key, value){
                                    var selected = "";
                                    if(selected_id == value.id){
                                        selected = "SELECTED";
                                    }

                                    $('select[name="group_id"]').append('<option class="form-control" value="' + value.id + '" ' + selected + '>' + value.name + '</option>');
                                    if (selected == "SELECTED") {
                                        $("#group_id").trigger("change");
                                    }
                                });
                            }else{
                                $('#group_id').empty();
                            }
                        }
                    });
                }else{
                    new Noty({
                        type: "error",
                        text: response.data.message,
                    }).show();
                }
            });
        }
    });

    // delete charts of accounts data
    function deleteCoa(id){
        swal({
            title: "{!! trans('backpack::base.warning') !!}",
            text: "{!! trans('backpack::crud.delete_confirm') !!}",
            icon: "warning",
            buttons: ["{!! trans('backpack::crud.cancel') !!}", "{!! trans('backpack::crud.delete') !!}"],
            dangerMode: true,
		}).then((result) => {
            if(result == true){
                $.ajax({
                    url: '/admin/charts-of-account/'+id,
                    type: 'DELETE',
                    success: function(response){
                        if(response){
                            location.reload();
                        }
                    }
                });
            }
        });
    }
</script>