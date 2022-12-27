let INVENTORY = {
    _formSaveSingleKey: 113, // F2
    _formSaveGroupKey: 83, // S

    validate: (wrapperElement) => {
        let valid = true;
        $(wrapperElement).find('input, select, textarea,number,time').each(function () {
            /**
             * Validate if element has required attribute and no value/input given
             */
            if ($(this).attr('required') !== undefined && $(this).val() === "") {
                valid = false;
                $(this).addClass('is-invalid');
                if ($(this).next().hasClass('select2')) {
                    $(this).next().addClass('is-invalid');
                }
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        return valid;
    },

    inventoryLoading: (bool) => {
        let status = bool === true ? 'show' : 'hide';
        $.LoadingOverlay(status, { text: "Saving..." });
    },

    fetchEmployeeById: (item) => {
        let employeeId = item.value;
        let url = '/admin/fetch_employee_detail';
        if (employeeId != '') {
            $.ajax({
                type: 'GET',
                url: url,
                data: { employeeId: employeeId },
                success: function (response) {
                    if (response.message === 'success') {
                        $('#full_name').val(response.user.full_name);
                        $('#email').val(response.user.email);
                        $('#phone').val(response.user.contact_number);
                    }
                },
                error: function (error) { }
            });
        }
    },

    multipleDependentStore: () => {
        var store = $("#store_id").val();
        console.log(store)
        $("#store_hidden_id").val(store);
    },


    fetchPurchaseitem: () => {
        var val1 = parseInt($('#purchase_quantity').val()) ? parseInt($('#purchase_quantity').val()) : 0;
        var val2 = parseInt($('#free_quantity').val()) ? parseInt($('#free_quantity').val()) : 0;
        var val3 = parseInt($('#receive_quantity').val()) ? parseInt($('#receive_quantity').val()) : 0;
        var val4 = parseInt($('#invoice_quantity').val()) ? parseInt($('#invoice_quantity').val()) : 0;
        var val5 = parseInt($('#return_quantity').val()) ? parseInt($('#return_quantity').val()) : 0;
        if ((val1 != '') || (val2 != '') || (val3 != '') || (val4 != '') || (val5 != '')) {
            $('#total_quantity').val(val1 + val2 + val3 + val4 + val5);
        } else {
            $('#total_quantity').val(0);
        }

    },

    fetchSalesReceipt: () => {
        // var val1 = parseInt($('#quantity').val()) ? parseInt($('#quantity').val()) : 0;
        // var val2 = parseInt($('#price').val()) ? parseInt($('#price').val()) : 0;
        // if ((val1 != '') && (val2 != '')) {
        //     $('#grand_total').val(val1 * val2);
        // } else {
        //     $('#grand_total').val(0);
        // }

        // var val3 = parseInt($('#discount_percentage').val()) ? parseInt($('#discount_percentage').val()) : 0;
        // var val4 = parseInt($('#grand_total').val()) ? parseInt($('#grand_total').val()) : 0;
        // if (val3 >= 0 && val3 <= 100) {
        //     if ((val3 != '') && (val4 != '')) {
        //         var result = val4 - ((val3 / 100) * val4);
        //         $('#net_total').val(result);
        //     } else {
        //         $('#net_total').val(val4);
        //     }
        // } else {
        //     alert('Discount Percentage must be between 0 to 100');
        // }
        // return $('#grand_total').val();
        // return $('#net_total').val();

        var val1 = parseInt($('#quantity').val()) ? parseInt($('#quantity').val()) : 0;
        var val2 = parseFloat($('#price').val()) ? parseFloat($('#price').val()) : 0;
        if ((val1 != '') && (val2 != '')) {
            $('#grand_total').val(val1 * val2);
        } else {
            $('#grand_total').val(0);
        }

        var val3 = parseFloat($('#discount_percentage').val()) ? parseFloat($('#discount_percentage').val()) : 0;
        var val4 = parseFloat($('#grand_total').val()) ? parseFloat($('#grand_total').val()) : 0;
        if (val3 >= 0 && val3 <= 100) {
            if ((val3 != '') && (val4 != '')) {
                var result = val4 - ((val3 / 100) * val4);
                $('#net_total').val(result);
            } else {
                $('#net_total').val(val4);
            }
        } else {
            alert('Discount Percentage must be between 0 to 100');
        }
        return $('#grand_total').val();
        return $('#net_total').val();

    },

    setIsTaxableField: () => {
        if (!$('#is_taxable_1').is(':checked')) {
            $('#tax_vat').prop("readonly", true);
            $('#tax_vat').val(0);
        }else{
            $('#tax_vat').prop("readonly", false);
            $('#tax_vat').val('');
        };
    },

    
    numberToEnglishWord: (number) => {
        if(number == 0 | number < 0) 
            return '-';

        no = Math.floor(number);
        rs = no;

        point = Number(Math.round((number - no) * 100, 0));
        paisa = point;

        hundred = ''; 
        digits_1 = no.toString().length;

        i = 0;

        str = new Array();
        words = [
            " ",
            "ONE",
            "TWO",
            "THREE",
            "FOUR",
            "FIVE",
            "SIX",
            "SEVEN",
            "EIGHT",
            "NINE",
            "TEN",
            "ELEVEN",
            "TWELVE",
            "THIRTEEN",
            "FOURTEEN",
            "FIFTEEN",
            "SIXTEEN",
            "SEVENTEEN",
            "EIGHTEEN",
            "NINETEEN",
            "TWENTY",
            "TWENTY ONE",
            "TWENTY TWO",
            "TWENTY THREE",
            "TWENTY FOUR",
            "TWENTY FIVE ",
            "TWENTY SIX",
            "TWENTY SEVEN",
            "TWENTY EIGHT",
            "TWENTY NINE",
            "THIRTY",
            "THIRTY ONE",
            "THIRTY TWO",
            "THIRTY THREE",
            "THIRTY FOUR",
            "THIRTY FIVE",
            "THIRTY SIX",
            "THIRTY SEVEN",
            "THIRTY EIGHT",
            "THIRTY NINE",
            "FORTY", 
            "FORTY ONE",
            "FORTY TWO",
            "FORTY THREE",
            "FORTY FOUR",
            "FORTY FIVE",
            "FORTY SIX",
            "FORTY SEVEN",
            "FORTY EIGHT",
            "FORTY NINE",
            "FIFTY", 
            "FIFTY ONE",
            "FIFTY TWO",
            "FIFTY THREE",
            "FIFTY FOUR",
            "FIFTY FIVE",
            "FIFTY SIX",
            "FIFTY SEVEN",
            "FIFTY EIHT",
            "FIFTY NINE",
            "SIXTY", 
            "SIXTY ONE", 
            "SIXTY TWO", 
            "SIXTY THREE", 
            "SIXTY FOUR",
            "SIXTY FIVE",
            "SIXTY SIX",
            "SIXTY SEVEN",
            "SIXTY EIGHT",
            "SIXTY NINE",
            "SEVENTY", 
            "SEVENTY ONE",
            "SEVENTY TWO",
            "SEVENTY THREE",
            "SEVENTY FOUR",
            "SEVENTY FIVE",
            "SEVENTY SIX",
            "SEVENTY SEVEN",
            "SEVENTY EIGHT",
            "SEVENTY NINE",
            "EIGHTY", 
            "EIGHTY ONE",
            "EIGHTY TWO",
            "EIGHTY THREE",
            "EIGHTY FOUR",
            "EIGHTY FIVE",
            "EIGHTY SIX",
            "EIGHTY SEVEN",
            "EIGHTY EIGHT",
            "EIGHTY NINE",
            "NINETY",
            "NINETY ONE",
            "NINETY TWO",
            "NINETY THREE",
            "NINETY FOUR",
            "NINETY FIVE",
            "NINETY SIX",
            "NINETY SEVEN",
            "NINETY EIGHT",
            "NINETY NINE",
            "HUNDRED",
        ];

        digits = ['', 'HUNDRED', 'THOUSAND', 'LAKH', 'CRORE'];
        
        while(i < digits_1){
            divider = (i == 2) ? 10 : 100;
            number = Math.floor(no % divider);
            no = Math.floor(no / divider);
            i += (divider == 10) ? 1 : 2;
            
            if(number){
                plural = ((counter = str.length) && number > 9) ? '' : '';
                hundred = (counter == 1 && str[0]) ? '' : '';
                str.push((number < 100) ? words[number] + ' ' + digits[counter] + plural + ' ' + hundred : words[Math.floor(number/10)*10] + ' ' + words[number%10] + ' ' + digits[counter] + plural + ' ' + hundred);
            }else{
                str.push(null);
            }
        }

        if(rs > 0 && paisa >= 1){
            str = str.reverse();
            results = str.join('');
            points = point ? ' ' + words[point] + ' ' : '';

            return results + "RUPEES AND " + points + "PAISA ONLY.";
        }else if(rs > 0 && paisa < 1){
            str = str.reverse();
            results = str.join('');

            return results + "RUPEES ONLY.";
        }else if(rs == 0 && paisa == 0){
            return "ZERO RUPEES AND ZERO PAISA ONLY";
        }else{
            str = str.reverse();
            points = point ? '' + words[point] + '' : '';

            return points + "PAISA ONLY."
        }
    },

    numberToEnglishNumberSystemWord: (number) => {
        var numberToWords = require('number-to-words');
        if(number){
            word = numberToWords.toWords(number);

            no = Math.floor(number);
            point = Number(Math.round((number - no) * 100, 0));
            point_word = numberToWords.toWords(point);
            
            final_word = word + ' dollars';
            if(point){
                final_word += ' and ' + point_word + ' cents';
            }
            return final_word.toUpperCase();
        }
    },
}
window.INVENTORY = INVENTORY;