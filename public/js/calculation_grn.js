$(document).ready(function() {
    // debugger;
    function setAllThings(rowId) {
        let purchaseOrderQty = parseInt($('#grngrn_qty-' + rowId).val());
        let purchaseOrderReceiveQty = parseInt($('#grn_rec_qty-' + rowId).val());
        let invoiceQty = parseInt($('#grn_invoice_qty-' + rowId).val());
        let freeQty = parseInt($('#grn_free_qty-' + rowId).val());
        let taxVat = parseInt($('#grn_tax_vat-' + rowId).val());
        let purchasePrice = parseFloat($('#grn_purchase_price-' + rowId).val());
        let totalQty = calcTotalQty(purchaseOrderReceiveQty, freeQty)
        let discountMode = $("#grn_discount_mode-" + rowId).val();
        let discount = parseFloat($('#grn_discount-' + rowId).val());
        let itemDiscount = calcItemDiscount(purchaseOrderReceiveQty, purchasePrice, discountMode, discount);
        let itemAmount = calcItemAmount(purchaseOrderReceiveQty, purchasePrice, itemDiscount, taxVat);
        console.log("itemAMMAT",purchaseOrderQty,freeQty,totalQty,purchasePrice,discountMode,discount);

        //Everything setter
        $("#grn_total_qty-" + rowId).val(totalQty);
        $('#grn_item_amount-' + rowId).val(itemAmount);
        calcBillAmount();
    }
    function checkNan(val) {
        return !isNaN(val) ? val : 0;
    }

    function calcBillAmount() {
        let grossAmt = 0;
        let totalDiscAmt = 0;
        let totalTaxAmt = 0;
        let otherCharges = parseFloat($("#grn_other_charges").val());
        let netAmt = 0;

        $(".grn_item_amount").each(function() {
            if ($(this).val()) {
                let currRow = $(this).attr('tr-id');
                let currItemAmt = checkNan(parseFloat($(this).val()));
                let taxVat = checkNan(parseFloat($('#grn_tax_vat-' + currRow).val()));
                

                let purchaseOrderReceiveQty = checkNan(parseInt($('#grn_rec_qty-' + currRow).val()));
                let purchasePrice = checkNan(parseFloat($('#grn_purchase_price-' + currRow).val()));
                let discountMode = $("#grn_discount_mode-" + currRow).val();
                let discount = checkNan(parseFloat($('#grn_discount-' + currRow).val()));
                let itemWiswDiscount = calcItemDiscount(purchaseOrderReceiveQty, purchasePrice, discountMode, discount);


                grossAmt = grossAmt + parseInt($(this).val()) + itemWiswDiscount;
                totalDiscAmt = totalDiscAmt + itemWiswDiscount;
                totalTaxAmt = totalTaxAmt + currItemAmt * taxVat / 100;

            }
        });

        if (!otherCharges) {
            otherCharges = 0;
        }
        netAmt = grossAmt - totalDiscAmt + totalTaxAmt + otherCharges;
        grn_taxable_amnt = grossAmt - totalDiscAmt;
        // debugger;
        $('#grn_gross_amount').val(grossAmt);
        $('#grn_taxable_amnt').val(grn_taxable_amnt);
        $('#grn_discount_amount').val(totalDiscAmt);
        $('#grn_tax_amount').val(totalTaxAmt);
        $('#grn_net_amount').val(netAmt);
    }

    function calcTotalQty(purchaseOrderReceiveQty, freeQty) {
        if (!freeQty) {
            freeQty = 0;
        }
        if (!purchaseOrderReceiveQty) {
            purchaseOrderReceiveQty = 0;
        }
        return purchaseOrderReceiveQty + freeQty;
    }

    function calcItemDiscount(purchaseOrderReceiveQty, purchasePrice, discountMode, discount) {

        if (!purchaseOrderReceiveQty || !purchasePrice || discountMode === '0' || !discount) {
            return 0;
        }

        let itemAmount = purchaseOrderReceiveQty * purchasePrice;
        if (discountMode === '1') {
            return discount * itemAmount / 100;
        }
        if (discountMode === '2') {
            return discount;
        }
    }

    function calcItemAmount(purchaseOrderReceiveQty, purchasePrice, itemDiscount, taxVat) {
        if (!purchaseOrderReceiveQty || !purchasePrice) {
            return 0;
        }
        // amount before tax-vat
        // if(purchaseOrderReceiveQty * purchasePrice > itemDiscount){
        //     if(taxVat){
        //         const amount_before_tax_vat = purchaseOrderReceiveQty * purchasePrice - itemDiscount;
        //         return amount_before_tax_vat + (amount_before_tax_vat * taxVat/100) ;
        //     }else{
                return purchaseOrderReceiveQty * purchasePrice - itemDiscount
        //     }
        // }else{
        //     return 0;
        // }
        // // amount after tax-vat

    }

    //Events
    $('.grngrn_qty').keyup(function() {

        let rowId = $(this).attr('tr-id');
        setAllThings(rowId);
    });
    $('.grn_rec_qty').keyup(function() {
        // debugger;
        let rowId = $(this).attr('tr-id');
        setAllThings(rowId);
    });
    $('.grn_invoice_qty').keyup(function() {
        let rowId = $(this).attr('tr-id');
        setAllThings(rowId);
    });
    $('.grn_free_qty').keyup(function() {
        let rowId = $(this).attr('tr-id');
        setAllThings(rowId);
    });
    $('.grn_purchase_price').keyup(function() {
        let rowId = $(this).attr('tr-id');
        setAllThings(rowId);
    });
    $('.grn_sales_price').keyup(function() {
        let rowId = $(this).attr('tr-id');
        setAllThings(rowId);
    });
    $('.grn_tax_vat').keyup(function() {
        let rowId = $(this).attr('tr-id');
        setAllThings(rowId);
    });
    $('.grn_discount_mode').change(function() {
        let rowId = $(this).attr('tr-id');
        setAllThings(rowId);

    });
    $('.grn_discount').keyup(function() {
        let rowId = $(this).attr('tr-id');
        setAllThings(rowId);
    });
    $("#grn_other_charges").keyup(function() {
        calcBillAmount();
    });
    $('.grn_discount_mode').change(function() {
        let rowId = $(this).attr('tr-id');

        let discountMode = $("#grn_discount_mode-" + rowId).val();

        if (discountMode === '2') {
            $("#grn_discount-" + rowId).removeAttr("max");
            // console.log("NRS is selected")
            // $("#po_discount-" + rowId).val();


        } else {
            // console.log("% is selected")

            $("#grn_discount-" + rowId).attr({
                "max": 100,
            });
        }
        setAllThings(rowId);
    });

    

   
    
});
