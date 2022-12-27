<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

<script>
    $(document).on('show.bs.modal', '.modal', function() {
        $(this).appendTo('body');
    });
    
    $(document).ready(function () {
        if($('#party_dashboard').is(':checked')){
            $('.dashboard_after_selecting_party_div').show();
        }else{
            $('.dashboard_after_selecting_party_div').hide();
        }
        if($('#maintain_ac_category').is(':checked')){
            $('.ac_category_caption_div').show();
        }else{
            $('.ac_category_caption_div').hide();
        }

        maintainImageNoteConfigure();
    });

    $('#party_dashboard').click(function (e) { 
        if ($(this).is(':checked')) {
            $('.dashboard_after_selecting_party_div').show();
        }else{
            $('#dashboard_after_selecting_party').val(0).trigger('change');
            $('.dashboard_after_selecting_party_div').hide();
        }
    });

    $('#maintain_ac_category').click(function (e) { 
        if ($(this).is(':checked')) {
            $('.ac_category_caption_div').show();
        }else{
            $('#ac_category_caption').val(null);            
            $('.ac_category_caption_div').hide();            
        }
    });

    // hide or show maintaing image/notes with masters/voucher configrue button on checkbox click
    $('#maintain_image_note').click(function (e) { 
        maintainImageNoteConfigure();
    });
    function maintainImageNoteConfigure(){
        if ($('#maintain_image_note').is(':checked')) {
            $('#maintain_image_note_configure').removeClass('d-none');
        }else{
            $('#maintain_image_note_configure').addClass('d-none');
        }
    }

    // account setting create and validation
    $('#accountSetting').submit(function (e) { 
    });

    $('#accountSetting').validate({
        submitHandler: function(form){
            let data = $('#accountSetting').serialize();
            let url = form.action;
            
            axios.post(url, data).then((response) => {
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

    // image and note configure validate and save
    $('#imageNoteConfigureForm').validate({
        submitHandler: function(form){
            let data = new FormData(form);
            let url = form.action;
            
            maintain_image_note = $('#maintain_image_note').val();
            sup_org_id = $('#sup_org_id').val();
            data.append('maintain_image_note', maintain_image_note);
            data.append('sup_org_id', sup_org_id);

            axios.post(url, data).then((response) => {
                if(response.data.status === 'success'){
                    location.reload();
                    new Noty({
                        type: "success",
                        text: response.data.message,
                    }).show();
                    $('#imageNoteConfigureModal').modal('hide');
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