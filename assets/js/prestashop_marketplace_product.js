(function ($) {
    $(document).on('bs.add.presta.product', function () {

        let loaderHtml = '<img src="/assets/img/ajax-loader.gif" />';
        //Prendo tutti i lotti selezionati
        let products = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if (selectedRows.length == 0) {
            new Alert({
                type: "warning",
                message: "Seleziona almeno un prodotto"
            }).open();
            return false;
        }

        $.each(selectedRows, function (k, v) {
            products.push(v.productCode);
        });

        let bsModal = new $.bsModal('Inserisci prodotti all\'interno di un marketplace', {
            body: `
                <div>
                    <p>Seleziona un marketplace</p>
                    <select id="selectMarketplace"></select>
                </div>
                
                <div id="newPrice">
                    <p>Modifica il prezzo</p>
                    <select id="modifyPrice">
                        <option value="nf">Non modificare</option>
                        <option value="p+">Percentuale +</option>
                        <option value="p-">Percentuale -</option>
                        <option value="f+">Fisso +</option>
                        <option value="f-">Fisso -</option>
                    </select>
                    
                    <p>Inserisci l'importo con cui variare il prezzo</p>
                    <input type="number" step="0.01" min="1" id="variantValue">
                </div>
                
                <div>
                    <p>Utilizza cronjob</p>
                    <input type="checkbox" id="useCron">
                </div>
            `
        });


        $.ajax({
            url: '/blueseal/xhr/PrestashopHasProductManage',
            method: 'GET',
            dataType: 'json'
        }).done(function (res) {
            let select = $('#selectMarketplace');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'shop-marketplace',
                options: res
            });
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            let cron = $('#useCron').is(':checked') ? 'PrestashopHasProductManageWithCron' : 'PrestashopHasProductManage';

            const data = {
                products: products,
                marketplaceHasShopId: $('#selectMarketplace').val(),
                modifyType: $('#modifyPrice').val(),
                variantValue: $('#variantValue').val()
            };

            bsModal.writeBody(loaderHtml);
            bsModal.okButton.attr("disabled", "disabled");
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/' + cron,
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
                bsModal.okButton.removeAttr("disabled");
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
                bsModal.okButton.removeAttr("disabled");
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                });
                bsModal.showOkBtn();
            });
        });
    });


    $(document).on('bs.product.marketplace.sale', function () {

        //Prendo tutti i lotti selezionati
        let prestaIds = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if (selectedRows.length == 0) {
            new Alert({
                type: "warning",
                message: "Seleziona almeno un prodotto"
            }).open();
            return false;
        }

        let products = [];

        $.each(selectedRows, function (k, v) {
            products.push({
                'prestaId': v.prestaId,
                'productId': v.productId,
                'productVariantId': v.productVariantId
            });
        });

        let bsModal = new $.bsModal('Metti prodotti in saldo', {
            body: `
                <div>
                    <p>Seleziona un marketplace</p>
                    <select id="selectMarketplace"></select>
                </div>
                
                <div>
                    <p>Applica modifica al titolo</p>
                    <input type="checkbox" id="useNewTitle">
                </div>
                
                <div id="newSalePrice">
                    <p>Modifica il prezzo</p>
                    <select id="modifyPriceForSale">
                        <option disabled selected value>Seleziona un'opzione</option>
                        <option value="nf">Non modificare</option>
                        <option value="percentage">Percentuale -</option>
                        <option value="amount">Fisso -</option>
                    </select>
                    
                    <p>Inserisci l'importo con cui variare il prezzo</p>
                    <input type="number" step="0.01" min="0" id="variantValue">
                </div>
            `
        });

        $.ajax({
            url: '/blueseal/xhr/PrestashopHasProductManage',
            method: 'GET',
            dataType: 'json'
        }).done(function (res) {
            let select = $('#selectMarketplace');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'shop-marketplace',
                options: res
            });
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            const data = {
                products: products,
                marketplaceHasShopId: $('#selectMarketplace').val(),
                modifyType: $('#modifyPriceForSale').val(),
                titleModify: !!$('#useNewTitle').is(':checked'),
                variantValue: $('#variantValue').val()
            };

            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/PrestashopHasSaleProductManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                });
                bsModal.showOkBtn();
            });
        });
    });

    $(document).on('change', '#modifyPriceForSale', function () {
        if($(this).val() === 'nf'){
            $('#variantValue').val(0).prop('disabled', true);
        } else {
            $('#variantValue').val('').prop('disabled', false);
        }
    });

    $(document).on('bs.delete.product', function () {

        //Prendo tutti i lotti selezionati
        let prestaIds = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if (selectedRows.length == 0) {
            new Alert({
                type: "warning",
                message: "Seleziona almeno un prodotto"
            }).open();
            return false;
        }

        let products = [];

        $.each(selectedRows, function (k, v) {
            products.push({
                'prestaId': v.prestaId,
                'productId': v.productId,
                'productVariantId': v.productVariantId
            });
        });

        let bsModal = new $.bsModal('Elimina il prodotto da prestashop', {
            body: `Sicuro di voler eliminare il prodotto da tutti i marketplace?`
        });


        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            const data = {
                products: products,
            };

            $.ajax({
                method: 'delete',
                url: '/blueseal/xhr/PrestashopHasProductManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                });
                bsModal.showOkBtn();
            });
        });
    });
    $(document).on('bs.update.presta.product.feature', function () {


    products='';


        let bsModal = new $.bsModal('Allineamento Dettagli', {
            body: `Allineamento Dettaglio Prodotti da Pickyshop a Iwes`
        });


        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            const data = {
                products: products,
            };

            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/PrestashopManualAlignFeatureProduct',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                });
                bsModal.showOkBtn();
            });
        });
    });


    $(document).on('bs.marketplace.remove.sale', function () {

        //Prendo tutti i lotti selezionati
        let prestaIds = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if (selectedRows.length == 0) {
            new Alert({
                type: "warning",
                message: "Seleziona almeno un prodotto"
            }).open();
            return false;
        }

        let products = [];

        $.each(selectedRows, function (k, v) {
            products.push({
                'prestaId': v.prestaId,
                'productId': v.productId,
                'productVariantId': v.productVariantId
            });
        });

        let bsModal = new $.bsModal('Elimina i prodotti dal saldo', {
            body: `
                <div>
                    <p>Seleziona un marketplace</p>
                    <select id="selectMarketplace"></select>
                </div>
            `
        });

        $.ajax({
            url: '/blueseal/xhr/PrestashopHasProductManage',
            method: 'GET',
            dataType: 'json'
        }).done(function (res) {
            let select = $('#selectMarketplace');
            if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'shop-marketplace',
                options: res
            });
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            const data = {
                products: products,
                marketplaceHasShopId: $('#selectMarketplace').val()
            };

            $.ajax({
                method: 'delete',
                url: '/blueseal/xhr/PrestashopHasSaleProductManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                });
                bsModal.showOkBtn();
            });
        });
    });
})(jQuery);