@if(isset($stock))
            let totalItems = {{$stock->items->count()}};
            counterArray = [];

            for(let i=1;i<= totalItems;i++){
                counterArray.push(i)
                $("#itemStock-"+i).autocomplete({
                    source: availableTags,
                    minLength: 1,
                    select: function(event, ui) {
                        let itemStock = $("#itemStock-"+i);
                        itemStock.next().attr('name','itemStockHidden['+i+']').val(ui.item.id);
                        $('#itemHistory-'+i).attr('item-id',ui.item.id)
                        getStockItemDetails(ui.item.id,i);
                        // enableFields(1);
                    },
                });
             }
            @else
             counterArray = [1];

            $("#itemStock-1").autocomplete({
                source: availableTags,
                minLength: 1,
                select: function(event, ui) {

                    let itemStock = $("#itemStock-1");
                    $('#itemHistory-1').attr('item-id',ui.item.id)

                    itemStock.next().attr('name','itemStockHidden[1]').val(ui.item.id);
                    getStockItemDetails(ui.item.id,1);

                },
            });
            @endif