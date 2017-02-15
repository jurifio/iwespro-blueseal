window.buttonSetup = {
    tag:"a",
    icon:"fa-sitemap",
    permission:"/admin/product/edit&&allShops",
    event:"bs.product.showTotalBySeason",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Mostra prodotti per Friend",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs.product.showTotalBySeason', function (e, element, button) {

    $.when(
        $.ajax({
            url: '/blueseal/xhr/GetTableContent',
            method: 'get',
            dataType: 'json',
            data: {fields: ['id', 'name', 'year'], table: 'ProductSeason'}
        }),
        $.ajax({
            url: '/blueseal/xhr/GetTableContent',
            method: 'get',
            dataType: 'json',
            data: {fields: ['id', 'title'], table: 'Shop'}
        })
    ).done(function(resSeason, resFriends){
        var seasons = resSeason[0];
        var friends = resFriends[0];
        seaOptions = '';
        for(var i in seasons) {
            seaOptions += '<option value="' + seasons[i]['id'] + '">' + seasons[i]['name'] + ' ' + seasons[i]['year'] + '</option>';
        }
        friendsOptions = '';
        for(var i in friends) {
            friendsOptions += '<option value="' + friends[i]['id'] + '">' + friends[i]['title'] + '</option>';
        }

        var body = '<div>Seleziona i criteri di ricerca</div>' +
            '<div class="tot-search">' +
                '<div class="form-group form-group-default">' +
                    '<label for="tot-season">Stagione</label>' +
                    '<select class="tot-season form-control" name="tot-season">' +
                        '<option value="0">Tutte</option>' +
                         seaOptions +
                    '</select>' +
                '</div>' +
                '<div class="form-group form-group-default">' +
                    '<label for="tot-friend">Friend</label>' +
                    '<select class="tot-friend form-control" name="tot-friend">' +
                        '<option value="0">Tutti</option>' +
                        friendsOptions +
                    '</select>' +
                '</div>' +
            '</div>' +
            '<div style="font-size: 1.3em;">Prodotti trovati: <strong class="tot-res"></strong>, pubblicati: <strong class="pub-res"></strong> <span class="small">(<span class="percent-res"></span>)</span></div>';

        modal = new $.bsModal(
            'Trova il totale dei prodotti',
            {
                body: body,
                okButtonEvent: function(){
                    modal.hide();
                }
            }
        );

        $.ajax({
            url: '/blueseal/xhr/CountProduct',
            method: 'get',
            dataType: 'json',
            data: {season: 0, friend: 0}
        }).done(function(res){
            populateTotRes(res['all'], res['published']);
        });

        $('.tot-search select').each(function(){
            $(this).off().on('click', function(){
                var seasonId = $('.tot-season').val();
                var friendId = $('.tot-friend').val();
                $.ajax({
                    url: '/blueseal/xhr/CountProduct',
                    method: 'get',
                    dataType: 'json',
                    data: {season: seasonId, friend: friendId}
                }).done(function(res){
                    populateTotRes(res['all'], res['published']);
                });
            });
        });
    });
});

function populateTotRes(all, published){
    $('.tot-res').html(all);
    $('.pub-res').html(published);
    var percent = '';
    if (0 == all || 0 == published) percent = '-';
    else {
        percent = (published / all * 100).toFixed(2);
    }
    $('.percent-res').html(percent + '%');
}
