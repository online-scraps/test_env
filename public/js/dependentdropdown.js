// select province of country
$('#country_id').on('change', function() {
    $.urlParam = function(name) {
        try {
            var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
            return results[1] || 0;
        } catch {
            return null;
        }
    }

    var country_id = $(this).val();
    if (country_id) {
        $.ajax({
            url: '/api/getprovince/' + country_id,
            type: "GET",
            success: function(data) {
                if (data) {
                    $('#province_id').empty();
                    $('#province_id').focus();
                    $('#province_id').append('<option value="">-</option>');
                    var selected_id = $.urlParam("province_id");
                    $.each(data, function(key, value) {
                        var selected = "";
                        if (selected_id == value.id) {
                            selected = "SELECTED";
                        }

                        $('select[name="province_id"]').append('<option class="form-control nepali_td" value="' + value.id + '" ' + selected + '>' + value.name_lc + '</option>');
                        if (selected == "SELECTED") {
                            $("#province_id").trigger("change");
                        }
                    });
                } else {
                    $('#province_id').empty();
                }
            }
        });
    } else {
        $('#province_id').empty();
    }
});

// select district of province
$('#province_id').on('change', function() {
    $.urlParam = function(name) {
        try {
            var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
            return results[1] || 0;
        } catch {
            return null;
        }
    }

    var province_id = $(this).val();
    if (province_id) {
        $.ajax({
            url: '/api/getdistrict/' + province_id,
            type: "GET",
            success: function(data) {
                if (data) {
                    $('#district_id').empty();
                    $('#district_id').focus;
                    $('#district_id').append('<option value="">-</option>');
                    var selected_id = $.urlParam("district_id");
                    $.each(data, function(key, value) {
                        var selected = "";
                        if (selected_id == value.id) {
                            selected = "SELECTED";
                        }

                        $('select[name="district_id"]').append('<option class="form-control nepali_td" value="' + value.id + '" ' + selected + '>' + value.name_lc + '</option>');
                        if (selected == "SELECTED") {
                            $("#district_id").trigger("change");
                        }
                    });
                } else {
                    $('#district_id').empty();

                }
            }
        });
    } else {
        $('#district_id').empty();
    }
});