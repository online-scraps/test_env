@push('after_scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <script src="{{ asset('packages/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/nepali.datepicker.v2.2.min.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/numberToWords.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/fancybox.v3.5.7.min.js') }}"></script>
    <script>
        document.addEventListener('keydown', function(event) {
            if (event.shiftKey && event.code === 'KeyN') {
                repeater();
            }

            // if(event.code == 'Enter' || event.code == 'NumpadEnter'){
            //     repeater();
            // }

            if (event.shiftKey && event.code === 'KeyD') {
                row_id = event.path[0].id;

                if (row_id.length > 0) {
                    destroyRepeater(row_id);
                }
            }
        });

        $(document).ready(function() {
            mode = '{{ $crud->getActionMethod() }}';
            if (mode == 'edit') {
                generateWord();
            }

            getDateBs('voucher_date_bs', 'voucher_date_ad');

            $(".searchselect").select2();

            $('#voucher_date_bs').nepaliDatePicker({
                npdMonth: true,
                npdYear: true,
                npdYearCount: 10,
                onChange: function() {
                    getDateBs('voucher_date_bs', 'voucher_date_ad');
                }
            });

            let url = $('#journalVoucherForm').prop('method');

            $('.remarks').keydown(function(e) {
                if (e.which === 9) {
                    repeater();
                }
            });

            // vouhcer image datas
            @if (isset($voucher))
                voucher = JSON.parse('<?php echo $voucher; ?>');
                if (voucher.image_with_account_master) {
                    $('#image_with_account_voucher_fancy, .remove_account_voucher_image').removeClass('d-none');
                }

                if (voucher.image_with_account_voucher) {
                    $('#image_with_account_master_fancy, .remove_account_master_image').removeClass('d-none');
                }
            @endif
        });

        // on change of series no generate voucher number
        $('#series_no_id').change(function(e) {
            mode = '{{ $crud->getActionMethod() }}';

            val = $(this).val();
            $.ajax({
                type: "GET",
                url: "{{ route('get-voucher-series') }}",
                data: {
                    'id': val,
                },
                success: function(response) {
                    last_series_no = response.lastSeriesNo;

                    if (mode == 'edit' && last_series_no != null) {
                        selected_val = '<?php echo isset($voucher) ? $voucher->series_no_id : null; ?>';
                        if (selected_val == val) {
                            new_no = response.lastSeriesNo.voucher_no;
                            $('#voucher_no').val(new_no);
                        } else {
                            generateVoucherNo(response);
                        }
                    } else {
                        generateVoucherNo(response);
                    }

                }
            });
        });

        // generate voucher no from selected series number
        function generateVoucherNo(response) {
            starting_word = response.seriesNo.starting_word;
            starting_no = response.seriesNo.starting_no;

            last_series_no = response.lastSeriesNo;

            if (starting_word && last_series_no == null)
                new_no = starting_word + '-' + starting_no;
            else if (starting_word == null && last_series_no == null)
                new_no = starting_no;
            else if (starting_word && last_series_no) {
                voucher_no = response.lastSeriesNo.voucher_no.split('-');
                new_no = voucher_no[0] + '-' + (Number(voucher_no[1]) + 1);
            } else if (starting_word == null && last_series_no)
                new_no = Number(last_series_no.voucher_no) + 1;
            else
                new_no = null;

            $('#voucher_no').val(new_no);
        }

        let counterArray = [];
        let avaliableTags = [{
            'id': '',
            'text': 'Search General Ledger',
        }];

        let avaliableTagsExcept = [{
            'id': '',
            'text': 'Search General Ledger',
        }];

        let dr_amount = 0;
        let cr_amount = 0;

        let all_ledgers = '<?php echo $ledgers["allowed"]; ?>';
        let all_ledgers_except = '<?php echo $ledgers["except"]; ?>';
        let ledger_dr_cr = '<?php echo $ledgers["dr_cr"]; ?>';

        JSON.parse(all_ledgers).forEach(function(ledger) {
            avaliableTags.push({
                'id': ledger.id,
                'label': ledger.name,
            });
        });

        JSON.parse(all_ledgers_except).forEach(function(ledger) {
            avaliableTagsExcept.push({
                'id': ledger.id,
                'label': ledger.name,
            });
        });

        @if (isset($voucher_details))
            let totalVouchers = {{ $voucher_details->count() }};

            for (let i = 1; i <= totalVouchers; i++) {
                counterArray.push(i);
                val = $('#dr_cr-' + i).val();
                dr_cr = val;
                drCr(i, val);

                if (ledger_dr_cr == dr_cr) {
                    ledgerAutoComplete(avaliableTags,i);
                } else if (ledger_dr_cr == 2) {
                    ledgerAutoComplete(avaliableTags,i);
                }else{
                    loadExceptAutocomplete(avaliableTagsExcept, rowId);
                }
            }
        @else
            counterArray = [1];
            let rowId = 1;

            let dr_cr = $('#dr_cr-' + rowId).val();
            if (ledger_dr_cr == dr_cr) {
                ledgerAutoComplete(avaliableTags,rowId);
            } else if (ledger_dr_cr == 2) {
                ledgerAutoComplete(avaliableTags,rowId);
            }else{
                loadExceptAutocomplete(avaliableTagsExcept, rowId);
            }

            $('#cr_amount-1').prop('readonly', 'readonly');
        @endif

        // add new row to the table start
        $(document).on('click', '.fireRepeaterClick', function(e) {
            repeater(ledger_dr_cr);
        });

        function ledgerAutoComplete(avaliableTags, rowId) {
            $('#general_ledger-' + rowId).autocomplete({
                source: avaliableTags,
                minLength: 1,
                select: function(event, ui) {
                    let itemStock = $("#general_ledger-" + rowId);
                    itemStock.next().attr('name', 'general_ledger_hidden[' + rowId + ']').val(ui.item.id);
                },
            });
        }

        function loadExceptAutocomplete(avaliableTagsExcept, rowId) {
            $('#general_ledger-' + rowId).autocomplete({
                source: avaliableTagsExcept,
                minLength: 1,
                select: function(event, ui) {
                    let itemStock = $("#general_ledger-" + rowId);
                    itemStock.next().attr('name', 'general_ledger_hidden[' + rowId + ']').val(ui.item.id);
                },
            });
        }

        function getLastArrayData() {
            return counterArray[counterArray.length - 1];
        }

        function repeater(ledger_dr_cr) {
            let tr = $('#repeater').clone(true);
            tr.removeAttr('id');
            tr.removeAttr('class');

            setIdToRepeater(getLastArrayData() + 1, tr);

            $('#journal-voucher-table').append(tr);

            counterArray.push(getLastArrayData() + 1);

            // debugger;
            $('#cr_amount-' + getLastArrayData()).prop('readonly', 'readonly');

            if (counterArray.length > 1) {
                if ($('#itemDestroyer-1').hasClass('d-none')) {
                    $('#itemDestroyer-1').removeClass('d-none')
                }
            }

            if (counterArray.length == 2) {
                $('#itemDestroyer-' + counterArray[0]).removeClass('d-none')
            }

            $('#dr_cr-' + getLastArrayData()).trigger('change');
            $('#dr_cr-' + getLastArrayData()).trigger('change');

            // debugger;
        }

        function setIdToRepeater(cntr, cloneTr) {
            // let classArr = ['dr_cr','general_ledger','sub_ledger','dr_amount','cr_amount','remarks','destroyRepeater'];
            // let trDBfields = ['dr_cr','general_ledger_id','sub_ledger_id','dr_amount','cr_amount','remarks'];

            let classArr = ['dr_cr', 'general_ledger', 'dr_amount', 'cr_amount', 'remarks', 'destroyRepeater'];
            let trDBfields = ['dr_cr', 'general_ledger_id', 'dr_amount', 'cr_amount', 'remarks'];

            cloneTr.children(':first').find('select').attr('id', 'dr_cr-' + cntr).attr('tr-id', cntr).attr('name',
                'dr_cr[' + [cntr] + ']');
            cloneTr.children(':nth-child(' + 2 + ')').find('input').attr('id', 'general_ledger-' + cntr).attr('tr-id', cntr)
                .attr('name', 'general_ledger_id[' + [cntr] + ']');
            // cloneTr.children(':nth-child(' + 3 + ')').find('input').attr('id', 'sub_ledger-' + cntr).attr('tr-id', cntr).attr('name', 'sub_ledger_id[' + [cntr] + ']');
            cloneTr.children(':last').children('.destroyRepeater').attr('id', 'itemDestroyer-' + cntr).attr('tr-id', cntr);

            for (let i = 1; i < 14; i++) {

                let n = i + 1;
                attr = cloneTr.children(':nth-child(' + n + ')').attr('class');

                if (attr == undefined) {
                    cloneTr.children(':nth-child(' + n + ')').children('.input-group').children('.' + classArr[i]).attr(
                        'id', classArr[i] + '-' + cntr).attr('tr-id', cntr).attr('name', trDBfields[i] + '[' + cntr +
                        ']');
                } else {
                    cloneTr.children(':nth-child(' + n + ')').attr('id', classArr[i] + '-' + cntr).attr('tr-id', cntr);
                }
            }
        }
        // add new row to the table end

        // delete table row
        $(".destroyRepeater").click(function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let tr = this.parentNode.parentNode;

                    indexCntr = counterArray.indexOf(parseInt(this.getAttribute('tr-id')));

                    tr.remove();

                    counterArray.splice(indexCntr, 1);

                    if (counterArray.length == 1) {
                        $('#itemDestroyer-' + counterArray[0]).addClass('d-none');
                    }

                    calculateDrAmount();
                    calculateCrAmount();
                }
            })
        });

        function destroyRepeater(rowId) {
            if (counterArray.length != 1) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (result.isConfirmed) {
                            let tr = $('#' + rowId).closest('tr');

                            indexCntr = counterArray.indexOf(parseInt($('#' + rowId).attr('tr-id')));

                            tr.remove();

                            counterArray.splice(indexCntr, 1);

                            if (counterArray.length == 1) {
                                $('#itemDestroyer-' + counterArray[0]).addClass('d-none');
                            }

                            calculateDrAmount();
                            calculateCrAmount();
                        }
                    }
                });
            }
        }

        // set dr_amount or cr_amount according to dr/cr
        $('.dr_cr').change(function(e) {
            e.preventDefault();
            let rowId = this.getAttribute('tr-id');
            let val = $('#dr_cr-' + rowId).val();
            dr_cr = val;

            if (ledger_dr_cr == dr_cr) {
                ledgerAutoComplete(avaliableTags, rowId);
            } else if (ledger_dr_cr == 2) {
                ledgerAutoComplete(avaliableTags, rowId);
            }else{
                loadExceptAutocomplete(avaliableTagsExcept, rowId);
            }
            drCr(rowId, val);
        });

        function drCr(rowId, val) {
            if (val == 1) {
                $('#dr_amount-' + rowId).removeAttr("readonly");
                $('#cr_amount-' + rowId).val(null).prop('readonly', 'readonly');
                calculateDrAmount();
            } else {
                $('#cr_amount-' + rowId).removeAttr("readonly");
                $('#dr_amount-' + rowId).val(null).prop('readonly', 'readonly');

                let lastRowId = rowId;
                let lastRowVal = 0;
                let totalCrAmount = 0.00;

                do {
                    lastRowId = lastRowId - 1;
                    lastRowVal = $('#dr_cr-' + lastRowId).val();

                    totalCrAmount = parseFloat(totalCrAmount) + checkNan(parseFloat($('#dr_amount-' + lastRowId).val()));
                } while (lastRowVal == 1);

                totalCrAmount > 0 ? $('#cr_amount-' + rowId).val(totalCrAmount) : $('#cr_amount-' + rowId).val(null);
                calculateCrAmount();
            }
        }

        // dr amount calculation
        $('.dr_amount').keyup(function(e) {
            let rowId = $(this).attr('tr-id');

            dr_val = checkNan(parseFloat($(this).val()));

            calculateDrAmount();
        });

        function calculateDrAmount() {
            dr_amount = 0;
            $(".dr_amount").each(function() {
                if ($(this).val()) {
                    dr_amount = dr_amount + parseFloat($(this).val());
                }
                // console.log($(this).val());
            });

            if (dr_amount != 0) {
                $('#total_dr_amount').val(dr_amount);
                $('.total_dr_amount').text(dr_amount);
            } else {
                $('#total_dr_amount').val(null);
                $('.total_dr_amount').text(null);
            }

            enableSaveBtn(dr_amount, cr_amount);
        }

        // cr amount calculation
        $('.cr_amount').keyup(function(e) {
            let rowId = $(this).attr('tr-id');

            cr_val = checkNan(parseFloat($(this).val()));

            calculateCrAmount();
        });

        function calculateCrAmount() {
            cr_amount = 0;
            $(".cr_amount").each(function() {
                if ($(this).val()) {
                    cr_amount = cr_amount + parseFloat($(this).val());
                }
            });

            if (cr_amount != 0) {
                $('#total_cr_amount').val(cr_amount);
                $('.total_cr_amount').text(cr_amount);
            } else {
                $('#total_cr_amount').val(null);
                $('.total_cr_amount').text(null);
            }

            enableSaveBtn(dr_amount, cr_amount);
        }

        // enable disable save button according to dr and cr amount
        function enableSaveBtn(dr, cr) {
            if (dr == cr) {
                generateWord();
                $('#saveBtn').removeAttr("disabled");
            } else {
                $('#saveBtn').prop("disabled", "disabled");
                $('#in_word').val(null);
            }
        }

        // to genrate word from number
        function generateWord(dr_amount) {
            app_setting = '<?php echo $app_setting; ?>';

            if (app_setting.length > 0) {
                appsetting = JSON.parse(app_setting);
                number_system = appsetting.number_system;

                dr_amount = $('#total_dr_amount').val();

                if (parseInt(number_system) == 1) {
                    word = INVENTORY.numberToEnglishWord(dr_amount);
                } else {
                    word = INVENTORY.numberToEnglishNumberSystemWord(dr_amount);
                }
                $('#in_word').val(word);
            }
        }

        // date bs to ad and vice versa
        function getDateBs(selector_id, related_id) {
            $('#' + related_id).val(BS2AD($('#' + selector_id).val()));

            $('#' + selector_id).change(function() {
                DateChange('#' + selector_id, '#' + related_id);
                $('#' + related_id).val(BS2AD($('#' + selector_id).val()));
            });
            $('#' + related_id).change(function() {
                $('#' + selector_id).val(AD2BS($('#' + related_id).val()));
            });

            var regexname = '^[0-9]*$';

            $('#' + selector_id).keyup(function(e) {
                let selected_value = $('#' + selector_id).val();
                if (e.key === '-' || e.key === '/') {
                    if (selected_value.length > 10) {
                        $('#' + selector_id).val(selected_value.substr(0, 10));
                    }
                } else {
                    if (e.key.match(regexname)) {
                        if (selected_value.length > 10) {
                            $('#' + selector_id).val(selected_value.substr(0, 10));
                        }
                    } else {
                        $('#' + selector_id).val(selected_value.substr(0, selected_value.length - 1));
                    }
                }
            });
        }

        // to check if not a number
        function checkNan(val) {
            return !isNaN(val) ? val : 0;
        }

        // image_with_account_master
        $('#image_with_account_master').change(function(e) {
            showImage('remove_account_master_image', 'image_with_account_master', e);
        });

        // remove image with account master
        $('.remove_account_master_image').click(function(e) {
            removeImage('remove_account_master_image', 'image_with_account_master');
        });

        // image with account vouher
        $('#image_with_account_voucher').change(function(e) {
            showImage('remove_account_voucher_image', 'image_with_account_voucher', e);
        });

        // remove image with account master
        $('.remove_account_voucher_image').click(function(e) {
            removeImage('remove_account_voucher_image', 'image_with_account_voucher');
        });

        // show image in fancy box
        function showImage(removeBtnClass, imgId, e) {
            if (mode == 'edit') {
                $('#' + imgId + '_current').val(null);
            }

            if (typeof(FileReader) != "undefined") {
                var file_fancy = $("#" + imgId + "_fancy");
                var file = e.target.files[0];
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#' + imgId + '_preview').attr('src', e.target.result);
                    file_fancy.attr("href", e.target.result);
                    file_fancy.removeClass('d-none');
                    $('.' + removeBtnClass + '').removeClass('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                alert('This browser dose not support HTML5 FileReader.');
            }
        }

        // remove image and related values
        function removeImage(removeBtnClass, imgId) {
            if (mode == 'edit') {
                $('#' + imgId + '_current').val(null);
            }
            $('input[name="' + imgId + '"]').val('');

            $("#" + imgId + "_preview").attr("src", " ");
            $("." + removeBtnClass).addClass('d-none');

            var file_fancy = $("#" + imgId + "_fancy");
            file_fancy.attr("href", " ");
            file_fancy.addClass('d-none');
        }

        // validation while submitting form
        $('#journalVoucherForm').on('submit', function(e) {
            e.preventDefault();

            $(':disabled').each(function(e) {
                $(this).removeAttr('disabled');
            });

            $('#auto_no').each(function() {
                $(this).rules("add", {
                    required: true,
                    messages: {
                        required: "Field required",
                    }
                });
            });

            $('#total_dr_amount').each(function() {
                $(this).rules("add", {
                    required: true,
                    messages: {
                        required: "Field required",
                    }
                });
            });

            $('#total_cr_amount').each(function() {
                $(this).rules("add", {
                    required: true,
                    messages: {
                        required: "Field required",
                    }
                });
            });

            $.each(counterArray, function(index, value) {
                $('#general_ledger-' + value).rules("add", {
                    required: true,
                    messages: {
                        required: "Field  required",
                    }
                });
            });
        });
        $('#journalVoucherForm').validate({
            submitHandler: function(form) {
                let data = new FormData(form);
                // append image_with_account_master value to data
                image_with_account_master = '';
                if ($('[name="image_with_account_master"]').val()) {
                    image_with_account_master = $("[name='image_with_account_master']")[0].files;
                }
                if (image_with_account_master != '') {
                    data.append('image_with_account_master', image_with_account_master[0],
                        image_with_account_master[0].name)
                }

                // append image_with_account_voucher value to data
                image_with_account_voucher = '';
                if ($('[name="image_with_account_voucher"]').val()) {
                    image_with_account_voucher = $("[name='image_with_account_voucher']")[0].files;
                }
                if (image_with_account_voucher != '') {
                    data.append('image_with_account_voucher', image_with_account_voucher[0],
                        image_with_account_voucher[0].name)
                }
                let url = form.action;

                axios.post(url, data)
                    .then((response) => {
                        if (response.data.status === 'success') {
                            Swal.fire("Success !", response.data.message, "success")
                            window.location.href = response.data.route;
                        } else {
                            Swal.fire("Error !", response.data.message, "error")
                        }
                    })

            }
        });
    </script>
@endpush
