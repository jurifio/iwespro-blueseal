;(function () {


    $(document).on('bs-sale-price-modify', function () {

        let dataTable = $('.dataTable').DataTable();
        let selectedRows = dataTable.rows('.selected').data();

        var i = 0;
        var productCode = [];
        var productPrice = [];
        var multipleRow = 0;

        function checkIfArrayHasSameValue(arr){
            var x = arr[0];
            return arr.every(function(item){
                return item === x;
            });
        }

        if (selectedRows.length === 1) {
            let ids = selectedRows[0].id.split('-');
            var productId = ids[0];
            var productVariantId = ids[1];
            multipleRow = 0;

            let bsModal = new $.bsModal('Aggiorna i prezzi in saldo.', {
                body: '<p>Inserisci il nuovo prezzo scontato</p>' +
                '<div class="form-group form-group-default required">' +
                '<label for="changePrice">Metti il nuovo prezzo totale ('+ selectedRows[0].id +')</label>' +
                '<input autocomplete="off" type="text" id="changePrice" ' +
                'placeholder="Nuovo prezzo totale" class="form-control" name="changePrice" required="required">' +
                '</div>' +
                '<div class="form-group form-group-default required">' +
                '<label for="changeSalePrice">Metti il nuovo prezzo in saldo ('+ selectedRows[0].id +')</label>' +
                '<input autocomplete="off" type="text" id="changeSalePrice" ' +
                'placeholder="Nuovo prezzo in saldo" class="form-control" name="changeSalePrice" required="required">' +
                '</div>'
            });

            bsModal.showCancelBtn();
            bsModal.setOkEvent(function () {
                const data = {
                    productId:  productId,
                    productVariantId:  productVariantId,
                    multipleRow: multipleRow,
                    newPrice: $('input#changePrice').val(),
                    newSalePrice: $('input#changeSalePrice').val()
                };
                $.ajax({
                    method: 'put',
                    url: '/blueseal/xhr/SalePriceProductSkuModify',
                    data: data
                }).done(function (res) {
                    bsModal.writeBody(res);
                }).fail(function (res) {
                    bsModal.writeBody('Errore grave');
                }).always(function (res) {
                    bsModal.setOkEvent(function () {
                        //refresha solo tabella e non intera pagina
                        $.refreshDataTable();
                        bsModal.hide();
                    });
                    bsModal.showOkBtn();
                });
            });
        } else if(selectedRows.length < 1){
            new Alert({
                type: "warning",
                message: "Non hai selelezionato nessuna riga"
            }).open();
        } else if (selectedRows.length > 1) {

            $.each(selectedRows, function (k, v) {
                //Seleziono più righe, se seleziono due o più productCode uguali ne tengo solamente uno
                if(!productCode.includes(v.id)) {
                    productCode[i] = v.id;
                    productPrice[i] = v.p_price;
                    i++;
                }
            });

            //check se i prezzi dei prodotti selezionati sono uguali
            if(checkIfArrayHasSameValue(productPrice) && productCode.length != 1){

                multipleRow = 1;
                let bsModal = new $.bsModal('Aggiorna i prezzi in saldo.', {
                    body: '<p>Inserisci il nuovo prezzo scontato. Stai modificando '+ productCode.length +' prodotti</p>' +
                    '<div class="form-group form-group-default required">' +
                    '<label for="changePrice">Metti il nuovo prezzo totale ('+ selectedRows[0].id +')</label>' +
                    '<input autocomplete="off" type="text" id="changePrice" ' +
                    'placeholder="Nuovo prezzo totale" class="form-control" name="changePrice" required="required">' +
                    '</div>' +
                    '<div class="form-group form-group-default required">' +
                    '<label for="changeSalePrice">Metti il nuovo prezzo in saldo ('+ selectedRows[0].id +')</label>' +
                    '<input autocomplete="off" type="text" id="changeSalePrice" ' +
                    'placeholder="Nuovo prezzo in saldo" class="form-control" name="changeSalePrice" required="required">' +
                    '</div>'
                });

                bsModal.showCancelBtn();
                bsModal.setOkEvent(function () {
                    const data = {
                        productCode:  productCode,
                        newPrice: $('input#changePrice').val(),
                        newSalePrice: $('input#changeSalePrice').val(),
                        multipleRow: multipleRow
                    };
                    $.ajax({
                        method: 'put',
                        url: '/blueseal/xhr/SalePriceProductSkuModify',
                        data: data
                    }).done(function (res) {
                        bsModal.writeBody(res);
                    }).fail(function (res) {
                        bsModal.writeBody('Errore grave');
                    }).always(function (res) {
                        bsModal.setOkEvent(function () {
                            //refresha solo tabella e non intera pagina
                            $.refreshDataTable();
                            bsModal.hide();
                        });
                        bsModal.showOkBtn();
                    });
                });


                //alert("prodotti diversi con lo stesso prezzo");
            } else if (!checkIfArrayHasSameValue(productPrice) && productCode.length != 1){
                alert("Prodotti diversi con prezzi diversi");
            }
        }
    });

    $(document).on('bs-sale-price-go-modify-price', function () {
        //let dataTable = $('.dataTable').DataTable();
        let ids = $.getDataTableSelectedRowData(undefined,'id');
        //let selectedRows = dataTable.rows('.selected').data();
        //let ids = selectedRows[0].id.split('-');
        let link = {};
        link.baseUrl = "/blueseal/prodotti/gestione-prezzi";
        link.code = ids;
        let url = $.encodeGetString(link);
        window.open(url,'_blank');
    });


    /*
    $(document).on('bs-emergency-alignment', function () {


            let bsModal = new $.bsModal('Aggiorna i prezzi in saldo', {
                body: '<p>ALLINEA I PREZZI TOTALI E I PREZZI IN SALDO</p>' +
                '<div class="form-group form-group-default required">' +
                '<p>Stai per allineare tutti i prezzi totali e i prezzi in saldo. PROCEDERE?</p>' + '</div>'
            });

            bsModal.showCancelBtn();
            bsModal.setOkEvent(function () {
                $.ajax({
                    method: 'post',
                    url: '/blueseal/xhr/EmergencyPricesAlign'
                }).done(function (res) {
                    bsModal.writeBody(res);
                }).fail(function (res) {
                    bsModal.writeBody('Errore grave');
                }).always(function (res) {
                    bsModal.setOkEvent(function () {
                        //refresha solo tabella e non intera pagina
                        $.refreshDataTable();
                        bsModal.hide();
                    });
                    bsModal.showOkBtn();
                });
            });
    });*/


})();