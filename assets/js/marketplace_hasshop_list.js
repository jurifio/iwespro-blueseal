(function ($) {


    $(document).on('bs.marketplace.shop.add', function () {
        let bsModal = new $.bsModal('Crea un nuovo shop', {
            body: `
                <div>
                    <p>Seleziona uno shop</p>
                    <select id="selectShop"></select>
                </div>            
                <div>
                    <p>Seleziona un marketplace</p>
                    <select id="selectMarketplace"></select>
                </div>
                `
        });

        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Shop'
            },
            dataType: 'json'
        }).done(function (res) {
            let selectShop = $('#selectShop');
            if(typeof (selectShop[0].selectize) != 'undefined') selectShop[0].selectize.destroy();
            selectShop.selectize({
                valueField: 'id',
                labelField: ['name'],
                options: res
            });
        });

        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Marketplace'
            },
            dataType: 'json'
        }).done(function (res) {
            let selectMarketplace = $('#selectMarketplace');
            if(typeof (selectMarketplace[0].selectize) != 'undefined') selectMarketplace[0].selectize.destroy();
            selectMarketplace.selectize({
                valueField: 'id',
                labelField: ['name'],
                options: res
            });
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                shopId: $('#selectShop').val(),
                marketplaceId: $('#selectMarketplace').val()
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/FoisonManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                    //window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });
    });
})(jQuery);
    (function ($) {
    $(document).on('bs.marketplace.price.rules', function () {
        let getVarsArray = [];
        let selectedRows = $('.table').DataTable().rows('.selected').data();
        let selectedRowsCount = selectedRows.length;

        if (selectedRowsCount < 1) {
            new Alert({
                type: "warning",
                message: "Devi selezionare uno o più Marketplace  per poter applicare la politica Prezzi"
            }).open();
            return false;
        }

        $.each(selectedRows, function (k, v) {
            getVarsArray.push(v.DT_RowId);

        });
        let bsModal = new $.bsModal('Strategia Prezzi', {
            body: `
                <div>
                    <p>Seleziona la Strategia</p>
                    <select id="isPriceHub" name="isPriceHub">
                    <option value="">---Seleziona---</option>
                    <option value="1">Listino  Da Shop</option>
                    <option value="0">Listino Per Prodotto Su Marketplace</option>
</select>
                </div>            
              
                `
        });



        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            const data = {
                isPriceHub: $('#isPriceHub').val(),
                marketplaceShopIds: getVarsArray
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/ManageRulesPriceMarketplaceHasShopAjaxController',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                    //window.location.reload();
                });
                bsModal.showOkBtn();
            });
        });
    });

})(jQuery);