@push('after_scripts')

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.js"></script> --}}
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="{{ asset('js/nepali.datepicker.v2.2.min.js') }}"></script>

    <script>
        $(document).on('show.bs.modal', '.modal', function () {
            $(this).appendTo('body');
        })
        document.getElementById('itemStock-1').focus();
        $(document).ready(function () {
                $('#menu-nav-link').click(function(){
                $('#drop-nav-link').toggleClass('show')
            })
            $('.barcodeScan').click(function () {
            $('#add_stock_item_modal').on('shown.bs.modal', function () {
                $('#barcodeScanner').focus();
                })
            });
            function ceil(x) {
                return Number.parseFloat(x).toFixed(2);
            }

            // select text
            $(function () {
                var focusedElement;
                $(document).on('focus', 'input', function () {
                    if (focusedElement == this) return; //already focused, return so user can now place cursor at specific point in input.
                    focusedElement = this;
                    setTimeout(function () { focusedElement.select(); }, 100); //select all text in any field on focus for easy re-entry. Delay sightly to allow focus to "stick" before selecting.
                });
            });



            function setAllThings(rowId) {
                let purchaseQty = parseInt($('#addQty-' + rowId).val());
                let freeQty = parseInt($('#freeQty-' + rowId).val());
                let purchasePrice = parseFloat($('#unitPrice-' + rowId).val());
                let availableQty = parseFloat($('#availableQty-' + rowId).val());
                let totalQty = calcTotalQty(purchaseQty, freeQty, availableQty);

                // let discountMode = $("#discount_mode-" + rowId).val();
                let discount = parseFloat($('#discount-' + rowId).val());
                let itemDiscount = calcItemDiscount(purchaseQty, purchasePrice, discount);

                let itemAmount = calcItemAmount(purchaseQty, purchasePrice, itemDiscount);


                //Everything setter
                $("#totalQty-" + rowId).val(totalQty);
                $('#totalAmnt-' + rowId).val(itemAmount);
                calcBillAmount();
            }

            function resetDiscount(rowId) {
                let purchaseQty = parseInt($('#addQty-' + rowId).val());
                let purchasePrice = parseFloat($('#unitPrice-' + rowId).val());
                let itemDiscount = 0;

                // let discountMode = $("#discount_mode-" + rowId).val();
                if (!$('#discountCheckbox').is(':checked')) {
                    itemDiscount = calcItemDiscount(purchaseQty, purchasePrice);

                } else {
                    let discount = parseFloat($('#discount-' + rowId).val());
                    itemDiscount = calcItemDiscount(purchaseQty, purchasePrice, discount);
                }

                let itemAmount = calcItemAmount(purchaseQty, purchasePrice, itemDiscount);

                //Everything setter
                $('#totalAmnt-' + rowId).val(itemAmount);
            }

            function EnterToNext(){

            }

            function calcBillAmount() {
                let grossAmt = 0;
                let totalDiscAmt = 0;
                let totalTaxAmt = 0;
                let taxableAmnt = 0;
                let netAmt = 0;

                $(".totalAmnt").each(function () {
                    if ($(this).val()) {
                        let currRow = $(this).attr('data-cntr');
                        resetDiscount(currRow);
                        let currItemAmt = checkNan(parseFloat($(this).val()));
                        let taxVat = checkNan(parseFloat($('#itemTax-' + currRow).val()));

                        let purchaseQty = checkNan(parseInt($('#addQty-' + currRow).val()));
                        let purchasePrice = checkNan(parseFloat($('#unitPrice-' + currRow).val()));
                        let discount = checkNan(parseFloat($('#discount-' + currRow).val()));
                        let itemWiswDiscount = calcItemDiscount(purchaseQty, purchasePrice, discount);

                        if (!$('#discountCheckbox').is(':checked')) {
                            grossAmt = grossAmt + currItemAmt;
                        } else {
                            grossAmt = grossAmt + currItemAmt + itemWiswDiscount;
                        }

                        totalDiscAmt = totalDiscAmt + itemWiswDiscount;
                        totalTaxAmt = totalTaxAmt + currItemAmt * taxVat / 100;

                    }
                });

                if (!$('#discountCheckbox').is(':checked')) {
                    totalDiscAmt = (checkNan(parseFloat($('#flatDiscount').val())) * grossAmt) / 100;
                }

                netAmt = grossAmt - totalDiscAmt + totalTaxAmt;

                taxableAmnt = grossAmt - totalDiscAmt;
                $('#st_gross_total').val(ceil(grossAmt));
                $('#st_discount_amount').val(ceil(totalDiscAmt));

                $('#st_taxable_amnt').val(ceil(taxableAmnt));
                $('#st_tax_amount').val(ceil(totalTaxAmt));
                $('#st_net_amount').val(ceil(netAmt));
            }

            function checkNan(val) {
                return !isNaN(val) ? val : 0;
            }


            function calcTotalQty(purchaseQty, freeQty, availableQty) {
                if (!freeQty) {
                    freeQty = 0;
                }
                if (!purchaseQty) {
                    purchaseQty = 0;
                }
                if (!availableQty) {
                    availableQty = 0;
                }
                return purchaseQty + freeQty + availableQty;
            }

            function calcItemDiscount(purchaseQty, purchasePrice, discount = 0) {
                if (!purchaseQty || !purchasePrice || !discount) {
                    return 0;
                }

                let itemAmount = purchaseQty * purchasePrice;
                return discount * itemAmount / 100;
            }

            function calcItemAmount(purchaseQty, purchasePrice, itemDiscount) {
                if (!purchaseQty || !purchasePrice) {
                    return 0;
                }
                if (!$('#discountCheckbox').is(':checked')) {
                    return purchaseQty * purchasePrice;
                }
                return purchaseQty * purchasePrice - itemDiscount;
            }

            function checkIfItemExist(rowId) {
                let idOfItemSelected = parseInt($("#itemHistory-" + rowId).attr('item-id'));
                console.log(listOfItems)
                let indexOfItemInArray = listOfItems.includes(idOfItemSelected);

                if (indexOfItemInArray) {
                    return true;
                }
                return false;
            }

            $('.itemstock').keyup(function(e){
                if(e.ctrlKey && e.which == 13){
                    repeater()
                }
            })

            //Events
            $('.itemStock').keyup(function (e) {
                let rowId = $(this).attr('data-cntr');
                setAllThings(rowId);
                if(e.ctrlKey && e.which == 13){
                    repeater()
                }
            });
            $('.addQty').keyup(function (e) {
                let rowId = $(this).attr('data-cntr');
                setAllThings(rowId);
                if(e.ctrlKey && e.which == 13){
                    repeater()
                }
            });

            $('.freeQty').keyup(function (e) {
                let rowId = $(this).attr('data-cntr');
                setAllThings(rowId);
                if(e.ctrlKey && e.which == 13){
                    repeater()
                }
            });
            $('.unitPrice').keyup(function (e) {
                let rowId = $(this).attr('data-cntr');
                setAllThings(rowId);
                if(e.ctrlKey && e.which == 13){
                    repeater()
                }
            });
            $('.salesPrice').keyup(function (e) {
                let rowId = $(this).attr('data-cntr');
                setAllThings(rowId);
                if(e.ctrlKey && e.which == 13){
                    repeater()
                }
            });

            $('.discount').keyup(function (e) {
                let rowId = $(this).attr('data-cntr');
                setAllThings(rowId);
                if(e.ctrlKey && e.which == 13){
                    repeater()
                }
            });
            $('#flatDiscount').keyup(function () {
                calcBillAmount();
                if(e.ctrlKey && e.which == 13){
                    repeater()
                }
            });


            let counterArray = [];
            let listOfItems = [];
            let availableTags = [{'id': '', 'text': 'Search an item'}];
            let all_items = '<?php echo isset($item_lists) ? json_encode($item_lists) : "[]" ?>';

            // enableFields(1,true);
            JSON.parse(all_items).forEach(function (item) {

                availableTags.push({'id': item.id, 'label': item.code + ' : ' + item.name});
            });

            //For autocomplete item search

            @if(isset($stock))
            let totalItems = {{$stock->items->count()}};
            let selectedItems = {{$stock->items->pluck('id')}};

            for (let i = 1; i <= totalItems; i++) {
                counterArray.push(i)
                listOfItems.push(selectedItems[i-1]);
                $("#itemStock-" + i).autocomplete({
                    source: availableTags,
                    autoFocus: true,
                    minLength: 1,
                    select: function (event, ui) {
                        let itemStock = $("#itemStock-" + i);
                        itemStock.next().attr('name', 'itemStockHidden[' + i + ']').val(ui.item.id);
                        $('#itemHistory-' + i).attr('item-id', ui.item.id)
                        getStockItemDetails(ui.item.id, i);
                        // enableFields(1);
                    },
                });
            }
            @else
                counterArray = [1];

            $("#itemStock-1").autocomplete({
                source: availableTags,
                minLength: 1,
                autoFocus: true,
                select: function (event, ui) {

                    let itemStock = $("#itemStock-1");
                    $('#itemHistory-1').attr('item-id', ui.item.id)

                    itemStock.next().attr('name', 'itemStockHidden[1]').val(ui.item.id);
                    getStockItemDetails(ui.item.id, 1);
                    if (checkIfItemExist(1)) {
                        Swal.fire({
                            title: 'Item Already Exits !',
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            /* Read more about isConfirmed, isDenied below */
                            if (result.isConfirmed) {
                                $("#itemStock-1").val('');
                                return;
                            }
                        })
                    } else {
                        listOfItems.push(ui.item.id)
                        // getStockItemDetails(ui.item.id, 1);
                        // enableFields(dataCntr);
                    }

                },
            });
            @endif


            $(document).on('keydown', '.discount', function (e) {
                var code = (e.keyCode ? e.keyCode : e.which);
                if (code == 78) {
                    repeater()
                    console.log(code)
                    var index = $('.discount').index(this) + 1;
                    $('.itemStock').eq(index).focus();
                }
            });

            $(document).on('click', '.fireRepeaterClick', function (e) {
                repeater();
                var index = $('.discount').index(this) + 1;
                $('.itemStock').eq(index+1).focus();
            });


            function getLastArrayData() {
                return counterArray[counterArray.length - 1];
            }

            function repeater() {
                let tr = $('#repeater').clone(true);
                tr.removeAttr('id');
                tr.removeAttr('class');
                tr.children(':first').children(':first').children(':first').addClass('customSelect2');

                setIdToRepeater(getLastArrayData() + 1, tr);
                $('#stock-table').append(tr);
                counterArray.push(getLastArrayData() + 1);
                let i = counterArray.length;

                $(document).on('keydown', '.discount', function(e) {
                    var code = (e.keyCode ? e.keyCode : e.which);
                    if (code == 13) {
                        var index = $('.discount').index(this) + 1;
                        $('.itemStock').eq(index).focus();
                    }
                });

                // $('#itemStock-' + (getLastArrayData() + 1)).focus();


                $("#itemStock-" + getLastArrayData()).autocomplete({
                    source: availableTags,
                    minLength: 1,
                    select: function (event, ui) {
                        let dataCntr = this.getAttribute('data-cntr');
                        let itemStock = $("#itemStock-" + dataCntr);

                        $('#itemHistory-' + dataCntr).attr('item-id', ui.item.id)
                        itemStock.next().attr('name', 'itemStockHidden[' + dataCntr + ']').val(ui.item.id);
                        getStockItemDetails(ui.item.id, dataCntr);

                        if (checkIfItemExist(dataCntr)) {
                            Swal.fire({
                                title: 'Item Already Exits !',
                                confirmButtonText: 'OK',
                            }).then((result) => {
                                /* Read more about isConfirmed, isDenied below */
                                if (result.isConfirmed) {
                                    $("#itemStock-" + dataCntr).val('');
                                    return;
                                }
                            })
                        } else {
                            listOfItems.push(ui.item.id)
                            // getStockItemDetails(ui.item.id, 1);
                            // enableFields(dataCntr);
                        }

                        // enableFields(dataCntr);
                    },
                });

                if (counterArray.length > 1) {
                    if ($('#itemDestroyer-1').hasClass('d-none')) {
                        $('#itemDestroyer-1').removeClass('d-none')
                    }
                }
                if (counterArray.length == 2) {
                    $('#itemDestroyer-' + counterArray[0]).removeClass('d-none')
                }
            }

            function setIdToRepeater(cntr, cloneTr) {
                let classArr = ['itemStock', 'availableQty', 'addQty', 'freeQty', 'totalQty', 'itemExpiry', 'unitPrice', 'salesPrice', 'discount', 'itemTax', 'totalAmnt', 'itemDestroyer'];
                let nameArr = ['mst_item_id', 'available_total_qty', 'add_qty', 'free_item', 'total_qty', 'expiry_date', 'unit_cost_price', 'unit_sales_price', 'discount', 'tax_vat', 'item_total', 'itemDestroyer'];
                cloneTr.children(':last').children('.destroyRepeater').attr('id', 'itemDestroyer-' + cntr).attr('data-cntr', cntr);
                cloneTr.children(':last').children('.itemHistory').attr('id', 'itemHistory-' + cntr).attr('data-cntr', cntr);
                cloneTr.children(':first').find('input').attr('id', 'itemStock-' + cntr).attr('data-cntr', cntr).attr('name', 'mst_item_id[' + cntr + ']');

                for (let i = 1; i < (nameArr.length -1); i++) {
                    let n = i + 1;
                    attr = cloneTr.children(':nth-child(' + n + ')').attr('class');
                    if (attr == undefined) {
                        if(classArr[i] == 'addQty' ){}
                        cloneTr.children(':nth-child(' + n + ')').children('.input-group').children('.barcodeScan').attr('id','barcodeScan-' + cntr).attr('data-cntr', cntr);

                        cloneTr.children(':nth-child(' + n + ')').children('.input-group').children('.' + classArr[i]).attr('id', classArr[i] + '-' + cntr).attr('data-cntr', cntr).attr('name', nameArr[i] + '[' + cntr + ']');
                    } else {
                        cloneTr.children(':nth-child(' + n + ')').attr('id', classArr[i] + '-' + cntr).attr('data-cntr', cntr);
                    }
                }

            }

            function getStockItemDetails(itemId, cntr) {
                let url = '{{ route("custom.stock-item", ":id") }}';
                url = url.replace(':id', itemId);
                $.get(url).then(function (response) {
                    $('#availableQty-' + cntr).val(response.availableQty);
                    $('#itemTax-' + cntr).val(response.taxRate);
                })
            }

            $("#discountCheckbox").click(function () {
                if ($(this).is(":checked")) {
                    $('.discount').each(function () {
                        $(this).prop('disabled', false);
                    })
                    $('#flatDiscount').prop('disabled', true)

                } else {
                    $('#flatDiscount').attr('disabled', false);
                    $('.discount').each(function () {
                        $(this).prop('disabled', true);
                    })
                }
                calcBillAmount()
            });
            $('#chalanEntryForm').on('submit', function (event) {
                $('#store_id').rules("add",
                    {
                        required: true,
                        messages: {
                            required: "Field  required",
                        }
                    });
                $.each(counterArray, function (index, value) {
                    if($('#itemStock-' + value).val()){
                        $('#itemStock-' + value).rules("add",
                        {
                            required: true,
                            messages: {
                                required: "Field  required",
                            }
                        });
                        $('#addQty-' + value).rules("add",
                            {
                                required: true,
                                number: true,
                                messages: {
                                    required: "Field Required",
                                    number: 'Field must be a number'
                                }
                            });
                        $('#unitPrice-' + value).rules("add",
                            {
                                required: true,
                                number: true,
                                messages: {
                                    required: "Field must be required",
                                    number: "Field must be a number",
                                }
                            });
                        $('#discount-' + value).rules("add",
                            {
                                number: true,
                                messages: {
                                    number: "Field must be a number",
                                }
                            });
                        $('#salesPrice-' + value).rules("add",
                            {
                                required: true,
                                number: true,
                                messages: {
                                    required: "Field must be required",
                                    number: "Field must be a number",
                                }
                            }
                        );

                    }

                });

            });

            $('#save').on('click', function () {
                $('#status').val({{\App\Models\SupStatus::CREATED}});
            });
            $('#approve').on('click', function () {
                $('#status').val({{\App\Models\SupStatus::APPROVED}});
            });

            $('#chalanEntryForm').validate({
                submitHandler: function (form) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085D6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, save it!',
                    }).then((response) => {
                        if (response.isConfirmed) {
                            let data = $('#chalanEntryForm').serialize();
                            let url = form.action;
                            axios.post(url, data)
                                .then((response) => {
                                    if (response.data.status === 'success') {
                                        Swal.fire("Success !", response.data.message, "success")
                                        window.location.href = response.data.route;
                                    } else {
                                        Swal.fire("Error !", response.data.message, "error");
                                    }
                                });
                        }
                    });
                }
            });

            $(".destroyRepeater").click(function () {
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
                        let dataCntr = this.getAttribute('data-cntr');
                        let itemId = $('#itemHistory-'+dataCntr).attr('item-id');

                        let indxOfItem = listOfItems.indexOf(parseInt(itemId));
                        listOfItems.splice(indxOfItem, 1);

                        indexCntr = counterArray.indexOf(parseInt(dataCntr));
                        tr.remove();
                        flushSession('barcode-'+itemId);
                        counterArray.splice(indexCntr, 1);
                        if (counterArray.length == 1) {
                            $('#itemDestroyer-' + counterArray[0]).addClass('d-none');
                        }
                        calcBillAmount();

                    }
                })
            });

        });

        // History
        $(document).on("click", ".itemHistory", function () {
            let currRow = $(this).attr('data-cntr');
            let item_name = $('#itemStock-' + currRow).val();
            let item_id = $('#itemHistory-' + currRow).attr('item-id');

            $("#search_stock_item_modal").attr('data-cntr', item_id);
            $('#modalItemName').html(item_name);
            let history_from = $('#itemFrom').val();
            let history_to = $('#itemTo').val();
            if (item_id === undefined) {
                Swal.fire('Please select an item before searching history')
            } else {
                getStockHistory(item_id, history_from, history_to);
                $('#search_stock_item_modal').modal('show');
            }

        });
        $("#fetchHistory").click(function () {
            let itemId = $('#search_stock_item_modal').attr("data-cntr");
            let history_from = $('#itemFrom').val();
            let history_to = $('#itemTo').val();
            getStockHistory(itemId, history_from, history_to);
        })

        function getStockHistory(itemId, history_from, history_to) {
            let url = '{{ route("custom.stock-item-search", [":id",":to",":from"] ) }}'
            url = url.replace(':id', itemId);
            url = url.replace(':to', history_from);
            url = url.replace(':from', history_to);

            $.get(url).then(function (response) {
                $("#modal_table_content").html(response);
            })
        }

        /**
         * Barcode scanner
         * Modal based code scanner
         */

        let barcodeList =  {!! getBarcodeJson(backpack_user()->sup_org_id) !!}

        $("#barcodeScanner").select2({

            tags: true,
            dropdownCssClass: 'hide',
            tokenSeparators: [',', ' ']
        })

        function flushSession(key)
        {
            let url = '{{ route("custom.stock-barcode-flush",":id" ) }}';
            url = url.replace(':id', key);
            axios.get(url)
                .then((response) => {
                    if (response.data.status === 'success') {
                       console.log('session flushed');
                    } else {
                        console.log('failed to flush session');
                    }
                });
        }


        $('#barcodeScanner').on("change",function (){
            let currentBarcode = $(this).val();
            var final_barcode_array = []
            let counter_check = 0

            $.each(currentBarcode,function (code){
                // var multiple_barcode = currentBarcode[code].toString()
                // const first_four_char = multiple_barcode.substring(0,4)
                // function countOcurrences(str, value) {
                //     var regExp = new RegExp(value, "gi");
                //     return (str.match(regExp) || []).length;
                // }
                // var ocurrance_count =  countOcurrences(multiple_barcode, first_four_char)
                // let withoutFirst = []
                // if(ocurrance_count > 3){
                //     var myarray = multiple_barcode.split(first_four_char).map(function(first_four_charrr){
                //     return multiple_barcode.substring(0,4)+first_four_charrr
                //     });
                //     withoutFirst = myarray.slice(1);

                //     $.each(withoutFirst,function (code){
                //         if(barcodeList[withoutFirst[code]]){
                //             withoutFirst.splice(withoutFirst.indexOf(withoutFirst[code]),1)
                //             Swal.fire('The scanned barcode already exists.')
                //             return;
                //         }
                //     });
                //     final_barcode_array += withoutFirst
                // }else{
                //     final_barcode_array += currentBarcode[code]
                // }

                if(barcodeList[currentBarcode[code]]){
                    currentBarcode.splice(currentBarcode.indexOf(currentBarcode[code]),1)
                    let timerInterval
                            Swal.fire({
                            title: 'The scanned barcode already exists!',
                            timer: 1000,
                            timerProgressBar: true,
                            didOpen: () => {
                                Swal.showLoading()
                                const b = Swal.getHtmlContainer().querySelector('b')
                                timerInterval = setInterval(() => {
                                }, 100)
                            },
                            willClose: () => {
                                clearInterval(timerInterval)
                            }
                            })
                            return;
                    return;
                }
                counter_check = 0;

            });
            $(this).val(currentBarcode);
        });

        $('.barcodeScan').on("click", function () {
            let currRow = $(this).attr('data-cntr');
            let item_name = $('#itemStock-' + currRow).val();
            let item_id = $('#itemHistory-' + currRow).attr('item-id');
            $("#add_stock_item_modal").attr('data-cntr', currRow);
            $('#barcodeItemName').html(item_name);
            $('#barcodeScanner').val('').trigger('change');

            if (item_id === undefined) {
                Swal.fire('Please select an item before scanning barcode.')
            } else {
                $('#add_stock_item_modal').modal('show');
            }

            $('#barcodeSave').on('click',function (){
                let data = $('#barcodeForm').serialize();
                console.log(data)
                let currRow =  $("#add_stock_item_modal").attr('data-cntr');
                let url = '{{ route("custom.stock-barcode",":id" ) }}';
                let itemId =$('#itemHistory-' + currRow).attr('item-id');
                url = url.replace(':id', itemId);
                axios.post(url, data)
                    .then((response) => {
                        if (response.data.status === 'success') {
                            let currentQtyRow =  $('#addQty-'+currRow);
                            currentQtyRow.val(response.data.count);
                            currentQtyRow.trigger('keyup');
                            currentQtyRow.prop('readonly',true);
                            barcodeList = JSON.parse(response.data.barcodeList);
                            $('#add_stock_item_modal').modal('hide');
                        } else {
                            Swal.fire("Error !", response.data.message, "error")
                        }
                    });
            });
        });



    </script>


    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
            integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
            crossorigin="anonymous"></script>

@endpush
