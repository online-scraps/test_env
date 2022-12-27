@push('after_scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="{{ asset('js/nepali.datepicker.v2.2.min.js') }}"></script>

<script>
        $(document).on('show.bs.modal', '.modal', function() {
            $(this).appendTo('body');
        })
    </script>

<script>
           //Other Scripts
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
                    console.log(counterArray);
                    indexCntr = counterArray.indexOf(parseInt(this.getAttribute('tr-id')));

                    tr.remove();

                    counterArray.splice(indexCntr, 1);

                    console.log('aaaaaa',counterArray)

                    if (counterArray.length == 1) {
                        $('#itemDestroyer-' + counterArray[0]).addClass('d-none');
                    }

                }
            })
        });

</script>

<!-- new script -->
<script>
    let counterArray = [];
    let availableTags = [{
        'id': '',
        'text': 'Search an item'
    }];
    let all_items = '<?php echo $item_lists??'[]' ?>';
    JSON.parse(all_items).forEach(function(item) {

        availableTags.push({
            'id': item.id,
            'label': item.code + ' : ' + item.name
        });
    });
    //For autocomplete item search
    @if(isset($grn))
        let totalItems = {{ $grn->grn_items->count()}};
        let selectedItems = {{$grn->grn_items->pluck('id')}};
        for (let i = 1; i <= totalItems; i++) {
            counterArray.push(i)
            $("#grn_item_name-" + i).autocomplete({
                source: availableTags,
                minLength: 1,
                select: function(event, ui) {
                let itemStock = $("#grn_item_name-"+i);
                itemStock.next().attr('name', 'grn_item_name_hidden['+i+']').val(ui.item.id);
                $("#grnItemHistory-1").attr('item-id', ui.item.id);

                getStockItemDetails(ui.item.id, i);

                let rowId = $(this).attr('tr-id');
                // enableFields(rowId);
                },
            });
        }
    @else
        counterArray = [1];
        $("#grn_item_name-1").autocomplete({
            source: availableTags,
            minLength: 1,
            select: function(event, ui) {
                let itemStock = $("#grn_item_name-1");
                itemStock.next().attr('name', 'grn_item_name_hidden[1]').val(ui.item.id);
                $("#grnItemHistory-1").attr('item-id', ui.item.id);
                getStockItemDetails(ui.item.id, 1);
                let rowId = $(this).attr('tr-id');
                // enableFields(rowId);
            },
        });
    @endif



    $(document).on('keydown', '.fireRepeater', function(e) {
        if (e.keyCode != 13) return;
        repeater();
    });

    $(document).on('click', '.fireRepeaterClick', function(e) {
        repeater();
    });

    function getLastArrayData() {
        return counterArray[counterArray.length - 1];
    }


    function repeater() {
        let tr = $('#repeater').clone(true);
        tr.removeAttr('id');
        tr.removeAttr('class');
        tr.children(':first').children(':first').children(':first').addClass('customSelect2');

        setIdToRepeater(getLastArrayData()+1,tr);

        $('#grn-table').append(tr);

        counterArray.push(getLastArrayData()+1);


        $("#grn_item_name-" + getLastArrayData()).autocomplete({
            source: availableTags,
            minLength: 1,
            select: function(event, ui) {
                let dataCntr = this.getAttribute('tr-id');
                let itemStock = $("#grn_item_name-" + dataCntr);
                itemStock.next().attr('name', 'grn_item_name_hidden[' + dataCntr + ']').val(ui.item.id);
                $("#grnItemHistory-" + dataCntr).attr('item-id', ui.item.id);
                getStockItemDetails(ui.item.id, dataCntr);
                // enableFields(dataCntr);
            },


        });

        if (counterArray.length> 1) {
            if ($('#itemDestroyer-1').hasClass('d-none')) {
                $('#itemDestroyer-1').removeClass('d-none')
            }
        }
        if (counterArray.length == 2) {
            $('#itemDestroyer-' + counterArray[0]).removeClass('d-none')
        }

        console.log('cccccc',counterArray);
    }

    function setIdToRepeater(cntr, cloneTr) {
        let classArr = ['grn_item_name', 'grn_qty','grn_rec_qty','grn_invoice_qty','grn_free_qty', 'grn_total_qty','itemExpiry', 'grn_purchase_price', 'grn_sales_price', 'grn_discount_mode', 'grn_discount','grn_tax_vat', 'grn_item_amount', 'destroyRepeater'];
        let trDBfields = ['mst_items_id', 'purchase_qty','received_qty','invoice_qty', 'free_qty', 'total_qty','expiry_date','purchase_price','sales_price', 'discount_mode_id', 'discount', 'tax_vat',   'item_amount'];
        cloneTr.children(':last').children('.destroyRepeater').attr('id', 'itemDestroyer-' + cntr).attr('tr-id', cntr);
        cloneTr.children(':last').children('.grnItemHistory').attr('id', 'grnItemHistory-' + cntr).attr('tr-id', cntr);
        cloneTr.children(':first').find('input').attr('id', 'grn_item_name-' + cntr).attr('tr-id', cntr).attr('name', 'items_id[' + [cntr] + ']');

        for (let i = 1; i < 14; i++) {

            let n = i + 1;
            attr = cloneTr.children(':nth-child(' + n + ')').attr('class');

            if (attr == undefined) {
                cloneTr.children(':nth-child(' + n + ')').children('.input-group').children('.' + classArr[i]).attr('id', classArr[i] + '-' + cntr).attr('tr-id', cntr).attr('name', trDBfields[i] + '[' + cntr + ']');
            } else {
                cloneTr.children(':nth-child(' + n + ')').attr('id', classArr[i] + '-' + cntr).attr('tr-id', cntr);
            }

        }
    }

    function getStockItemDetails(itemId, cntr) {
        let url = '{{ route("custom.grn-details", ":id") }}'
        url = url.replace(':id', itemId);
        $.get(url).then(function(response) {
            $('#grn_tax_vat-' + cntr).val(response.taxRate);
            $('#grn_purchase_price-' + cntr).val(response.itemPrice);
            const $select = document.querySelector('#grn_discount_mode-' + cntr);
            $select.value = response.discountMode;
            // $('#grn_discount_mode-' + cntr).attr("style", grninter-events: none;");
        })
    }

    // validation script

    //
    // function enableFields(rowId) {
    //     $('#grngrn_qty-' + rowId).prop("disabled", false);
    //     $('#grn_rec_qty-' + rowId).prop("disabled", false);
    //     $('#grn_invoice_qty-' + rowId).prop("disabled", false);
    //     $('#grn_free_qty-' + rowId).prop("disabled", false);
    //     $('#grn_total_qty-' + rowId).prop("disabled", false);
    //     $('#grn_batch_no-' + rowId).prop("disabled", false);
    //     $('#itemExpiry-' + rowId).prop("disabled", false);
    //     $('#grn_purchase_price-'+ rowId).prop("disabled", false);
    //     $('#grn_sales_price-'+ rowId).prop("disabled", false);
    //     $('#grn_discount_mode-'+ rowId).prop("disabled", false);
    //     $('#grn_tax_vat-'+ rowId).prop("disabled", false);
    //     $('#item_amount-'+ rowId).prop("disabled", false);
    //
    // }



    $('#grn_type').change(function() {
        let val = $(this).find(":selected").val();
        $("#supplier").val('');
        if (val === '1') {
            $("#supplier").attr("disabled", false)
            $("#requested_store").attr("disabled", true)
        }
        if (val === '2') {

        }
    });

    // end of validation script
</script>

<!-- script for modal -->
<script>
    // History
        $(document).on("click", ".grnItemHistory", function () {
            let currRow = $(this).attr('tr-id');
            let item_name = $('#grn_item_name-' + currRow).val();
            let item_id = $('#grnItemHistory-' + currRow).attr('item-id');
            console.log(currRow,item_name,item_id);
            $("#search_grn_item_modal").attr('tr-id', item_id);
            $('#grnmodalItemName').html(item_name);
            let history_from = $('#itemFrom').val();
            let history_to = $('#itemTo').val();
            if (item_id === undefined) {
                Swal.fire('Please select an item before searching history')
            } else {
                getStockHistory(item_id, history_from, history_to);
                $('#search_grn_item_modal').modal('show');
            }

        });
        $("#fetchHistory").click(function () {
            let itemId = $('#search_grn_item_modal').attr("tr-id");
            let history_from = $('#itemFrom').val();
            let history_to = $('#itemTo').val();
            getStockHistory(itemId, history_from, history_to);
        })

        function getStockHistory(itemId, history_from, history_to) {
            let url = '{{ route("custom.grn-item-search", [":id",":to",":from"] ) }}'
            url = url.replace(':id', itemId);
            url = url.replace(':to', history_from);
            url = url.replace(':from', history_to);

            $.get(url).then(function (response) {
                console.log(response);
                $("#modal_table_content").html(response);
            });
        }

        $("#fetchPoHistory").click(function () {
            let history_from = $('#itemFrom_po_search').val();
            let history_to = $('#itemTo_po_search').val();
            let po_no = $('#po_no').val();
            let supplier = $('#supplier_po_search').val();
            let po_type = $('#po_type').val();
            getPoSearchHistory(po_no, history_from, history_to,supplier,po_type);
        })

        function getPoSearchHistory(po_no, history_from, history_to,supplier,po_type) {
            let url = '{{ route("custom.po-item-search", [":po_no",":from",":to",":supplier",":po_type"]) }}'
            url = url.replace(':po_no', po_no);
            url = url.replace(':from', history_from);
            url = url.replace(':to', history_to);
            url = url.replace(':supplier', supplier);
            url = url.replace(':po_type', po_type);
            console.log(url);
            $.get(url).then(function (response) {
                $("#modal_table_content_po_search").html(response);
            });
        }



        $(document).on("click", "#fetchGrnItemsFromPO", function () {
            // checkedListBox1_ItemCheck();

        });
        // function getItemFromPoId(){


            $('#save').on('click', function () {
                // debugger;
                $('#status').val({{\App\Models\SupStatus::CREATED}});
        });
        $('#approve').on('click', function () {
                 $('#status').val({{\App\Models\SupStatus::APPROVED}});
         });

        function addClassCheck(element){

            // if(element.checked){
            //     element.classList.add("marked");
            // }else{
            //     element.classList.remove("marked");
            // }

            // if(document.getElementsByClassName("marked").length>1){
            //     element.checked=false;
            //     element.classList.add("marked");

            // }

            // $('.po_row input[type="radio"]').click(function(){
                // var selectedOption = $("input:radio[name=hello]:checked").val()
                // alert("Your row is "+selectedOption);
            // });

        }
        $('#grn_form').on('submit', function() {
        $('.grn_item_amount').prop("disabled", false);
        $('.grn_tax_vat').prop("disabled", false);
        $('.grn_total_qty').prop("disabled", false);
        $('.grn_purchase_qty').prop("disabled", false);
        $('.grn_free_qty').prop("disabled", false);
        $('.grn_discount_mode').prop("disabled", false);
        $('.grn_discount').prop("disabled", false);
        $('.grn_purchase_price').prop("disabled", false);
        $('.grn_sales_price ').prop("disabled", false);
        $('.grn_type ').prop("disabled", false);
        $('.grn_item_name').each(function() {
            $(this).rules("add", {
                required: true,
                messages: {
                    required: "Field  required",
                }
            });
        });

    });


    $('#grn_form').on('submit', function(event) {
        $.each(counterArray, function(index, value) {
            $('#store').rules("add", {
                required: true,
                messages: {
                    required: "Field  required",
                }
            });
            $('#supplier').rules("add", {
                required: true,
                messages: {
                    required: "Field  required",
                }
            });
            $('#grn_type').rules("add", {
                required: true,
                messages: {
                    required: "Field  required",
                }
            });

            $('#grn_item_name-' + value).rules("add", {
                required: true,
                messages: {
                    required: "Field  required",
                }
            });
            $('#grn_rec_qty-' + value).rules("add", {
                number: true,
                messages: {
                    required: "Field Required",
                    number: 'Field must be a number'
                }
            });
            $('#grngrn_qty-' + value).rules("add", {
                number: true,
                messages: {
                    number: 'Field must be a number'
                }
            });
            $('#grn_invoice_qty-' + value).rules("add", {
                number: true,
                messages: {
                    number: 'Field must be a number'
                }
            });

        });

    });

    function atleastOneFieldRequired() {
            let filled_counter = 0;

            $('.grn_rec_qty').each(function() {
                if ($(this).val() !== '' && ($(this).val() > 0)) {
                    filled_counter++;
                }
            });

            if (filled_counter > 0) {
                return true
            } else {
                return false
            }
        }



    $('#grn_form').validate({
        submitHandler: function(form) {
            let isFilled = atleastOneFieldRequired();
                if (!isFilled) {
                    Swal.fire("Atleast One ReturnQty field is required");
                    return;
                }

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, save it!'
            }).then((confirmresponse) => {
                if (confirmresponse) {

                    $("#grn_type").attr('disabled',false);
                    $("#store").attr('disabled',false);
                    $("#supplier").attr('disabled',false);
                    $("#po_date").attr('disabled',false);

                    let data = $('#grn_form').serialize();
                    let url = form.action;
                    // debugger;

                    axios.post(url, data)
                        .then((response) => {
                            console.log(response.data.route);
                            if(response.data.status === 'success'){
                                Swal.fire("Success !", response.data.message, "success")
                                window.location.href=response.data.route;
                            }else{
                                Swal.fire("Error !", response.data.message, "error")
                            }
                        });
                }
            });
        }
    });



        $('input[name="hello"]').on('change', function() {
                    $('input[name="hello"]').not(this).prop('checked', false);
                    alert("The best cricketer is: " + $('input[name="hello"]:checked').val());
        });
        $('#pod-fetch-btn').click(function(){
        let url = '{{ route("custom.get-pod-for-grn",":po_no") }}'
        let po_no=$('#purchase_order').val();
        url = url.replace(':po_no', po_no);
        $.get(url).then(function(response) {
            if(response.nodata==='nodata'){
                Swal.fire("No Data Found")
            }else{
                // debugger;


                let pod=response.pod;
                $('#inv-qty-header').remove();
                $('#action-header').remove();



                $("#grn_type").val(pod.purchase_order_type_id).attr('disabled','disabled');
                $("#store").val(pod.store_id).attr('disabled','disabled');
                $("#supplier").val(pod.supplier_id).attr('disabled','disabled');
                $("#grn-table-body").html(response.view);
                $("#po_date").val(pod.po_date.slice(0,10)).attr('disabled','disabled');

            }


         })


    });





</script>

<script type="text/javascript" src="{{ asset('js/calculation_grn.js') }}"></script>
@endpush
