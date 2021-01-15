(function ($) {
    $(document).on('bs.adding.presta', function () {

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
                    </select>
                    
                    <p>Inserisci l'importo con cui variare il prezzo</p>
                    <input type="number" step="0.01" min="1" id="variantValueSale">
                     <select id="modifyPriceSale">
                        <option value="nf">Non modificare</option>
                        <option value="p+">Percentuale +</option>
                        <option value="p-">Percentuale -</option> 
                    </select>
                    
                    <p>Inserisci l'importo con cui variare il prezzo</p>
                    <input type="numberSale" step="0.01" min="1" id="variantValue">
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
                modifyType: $('#modifyPrice').val(),
                variantValue: $('#variantValue').val(),
                modifyTypeSale: $('#modifyPriceSale').val(),
                variantValueSale: $('#variantValueSale').val()
            };

            bsModal.writeBody(loaderHtml);
            bsModal.okButton.attr("disabled", "disabled");
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/PrestashopHasProductManage',
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
    $(document).on('bs.prestashop.align.quantity', function () {

        let bsModal = new $.bsModal('Allinea le quantità ', {
            body: `
                <div>
                    <p>Allinea le quantità sugli shop</p>
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
                marketplaceHasShopId: $('#selectMarketplace').val()
            };

            $.ajax({
                method: 'POST',
                url: '/blueseal/xhr/PrestashopAlignQuantityProductManage',
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

    //bs.ebay.align.product;

    $(document).on('bs.ebay.align.product', function () {


        products='';


        let bsModal = new $.bsModal('Aggiornamento Massivo ', {
            body: `Aggiornamento Prodotti su Ebay`
        });


        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            bsModal.writeBody('<img src="/assets/img/ajax-loader.gif" />');

            const data = {
                products: products,
            };

            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/EbayReviseProductAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody(res);
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                });
                bsModal.showOkBtn();
            });
        });
    });
    $(document).on('bs.marketplace.prepare.product', function () {


        products='';


        let bsModal = new $.bsModal('Selezione Prodotti per Shop associato al Marketplace ', {
            body: `Emulatore Job popolamento tabella Prodotti per Marketplace `
        });


        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            bsModal.writeBody('<img src="/assets/img/ajax-loader.gif" />');

            const data = {
                products: products,
            };

            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/PrepareProductForMarketplaceAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody(res);
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                });
                bsModal.showOkBtn();
            });
        });
    });
    $(document).on('bs.marketplaceaccountrule.publish.product', function () {


        products='';


        let bsModal = new $.bsModal('Pubblicazione prodotti in base a regole Marketplace ', {
            body: `Emulatore Job popolamento tabella Prodotti per MarketplaceAccount  e gestione coda di pubblicazione e aggiornamento`
        });


        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            bsModal.writeBody('<img src="/assets/img/ajax-loader.gif" />');

            const data = {
                products: products,
            };

            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/MarketplaceHasProductJobAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody(res);
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    bsModal.hide();
                    $.refreshDataTable();
                });
                bsModal.showOkBtn();
            });
        });
    });
    $(document).on('bs.add.presta.product.all', function () {



        let bsModal = new $.bsModal('Pubblica tutti i Prodotti con stato pubblicato sui Marketplace', {
            body: `
                <div>
                    <p>Confermi?</p>
                
                </div>
            `
        });



        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            const data = {
                products: 1

            };

            $.ajax({
                method: 'POST',
                url: '/blueseal/xhr/PrestashopBookingProductListAjaxController',
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