;(function () {
    $(document).on('bs.add.presta.product', function () {

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
                        <option value="notModify">Non modificare</option>
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

            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/' + cron,
                data: data
            }).done(function (res) {
                bsModal.writeBody('Prodotti inseriti con successo');
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

        $.each(selectedRows, function (k, v) {
            prestaIds.push(v.prestaId + '-' + v.productCode);
        });

        let bsModal = new $.bsModal('Metti i prodotti in saldo', {
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
                productsPrestashopIds: prestaIds,
                marketplaceHasShopId: $('#selectMarketplace').val()
            };

            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/PrestashopHasProductManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody('Prodotti inseriti con successo');
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

    $(document).on('bs.cron.insert.product.prestashop', function () {

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

        let bsModal = new $.bsModal('Seleziona il marketplace a cui associare i prodotti selezionati', {
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
                method: 'post',
                url: '/blueseal/xhr/PrestashopHasProductManageWithCron',
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
})();