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