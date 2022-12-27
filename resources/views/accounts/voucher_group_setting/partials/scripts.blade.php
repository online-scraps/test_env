<script src="{{ asset('packages/select2/dist/js/select2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

<script>
    // load multiple group value on edit
    @if(isset($voucher_group_setting))
        group = '<?php echo $voucher_group_setting->group_id; ?>';
        group = JSON.parse(group);

        i = 1;
        $.each(group, function (index, value) { 
            if(value.length > 0){
                $('select[id="group_id_'+ i +'"]').val(value).trigger('change');
            }
            i++;
        });
    @endif
    
    $('.group').select2({ 
        placeholder: "Select Group",
        allowClear: true, 
    });

    // charts of accoutn form submit and validation 
    $('#voucherGroupSetting').on('submit', function () {
        $(':disabled').each(function(e) {
            $(this).removeAttr('disabled');
        });

        i = 1;
        $('.group').each(function() {
            $('#group_id_' + i).rules("add", {
                required: true,
                messages: {
                    required: "Field required",
                },
            });
            i++;
        });
    });

    $('#voucherGroupSetting').validate({
        //put error message behind each form element
        errorPlacement: function (error, element) {
            error.insertAfter(element.next());
        },
        highlight: function(element) {
            element = $(element);

            spanElement = element.next();
            spanElement = spanElement.children('span.selection');
            spanElement = spanElement.children();

            spanElement.addClass('error');
        },
        submitHandler: function(form){
            let data = $('#voucherGroupSetting').serialize();
            let url = form.action;
            
            axios.post(url, data).then((response) => {
                console.log(response, 'here');
                if(response.data.status === 'success'){
                    window.location.href=response.data.route;
                }else{
                    new Noty({
                        type: "error",
                        text: response.data.message,
                    }).show();
                }
            });
        }
    });
</script>