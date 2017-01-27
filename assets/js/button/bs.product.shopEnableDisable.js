window.buttonSetup = {
    tag:"a",
    icon:"fa-suitcase",
    permission:"/admin/product/mag&&allShops",
    event:"bs.product.shopEnableDisable",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Accendi e spegni i friend",
    placement:"bottom"
};

$(document).on('bs.product.shopEnableDisable', function () {

    modal = new  $.bsModal(
        'Il magico interruttore dei Friend',
        {
            body: 'Sto caricando la lista dei friend...',
            isCancelButton: true,
        }
    );

    $.ajax({
        url: '/blueseal/xhr/ShopEnableDisable',
        method: 'get',
        dataType: 'json'
    }).fail(function(res){
        modal.writeBody('OOPS! C\'è stato un problema. Contatta un amministratore');
        console.error(res);
    }).done(function(res){
        if (res.error) {
            modal.writeBody(res.message);
        } else {
            var options = '';
            for (var i in res) {
                var isActive = (1 == res[i].isActive) ? 'Attivo' : 'Non Attivo' ;
                options += '<option value="' + res[i].id + '-' + res[i].isActive + '">' + res[i].title + ': ' + isActive + '</option>';
            }
            var body = '<p>I friend dal colore chiaro sono offline.<br/>' +
                'Selezionando un friend offline sarà ripristinato, viceversa selezionando un friend attivo verrà portato offline</p>' +
                '<div class="form-group selectize-enabled">' +
                '<label for="selectFriend">Seleziona un Friend per procedere:</label>' +
                '<select class="form-control" id="selectFriend" name="selectFriend" >' +
                '<option value="" disabled selected>Seleziona un friend</option>' +
                options + '</select>' +
                '</div>';

            modal.writeBody(body);

            modal.setOkEvent(function () {
                var selVar = $('#selectFriend').val().split('-');
                var choosen = {};
                choosen.id = selVar[0];
                choosen.isActive = selVar[1];
                delete(selVar);

                modal.writeBody(
                    ((1 == choosen.isActive) ? 'Il Friend verrà messo offline.' : 'Il Friend verrà riportato online ripristinando le quantità.')
                    + ' Continuare?');
                var action = (0 == choosen.isActive) ? 'start' : 'stop';

                modal.setOkEvent(function(){
                    $.ajax({
                        url: '/blueseal/xhr/ShopEnableDisable',
                        method: 'post',
                        data: {shopId: choosen.id, action: action}
                    }).done(function(res){
                        modal.write(res);
                    }).fail(function(res){
                        modal.writeBody('OOPS! C\'è stato un problema. Contatta un amministratore');
                        console.error(res);
                    }).always(function(){
                        modal.setOkEvent(function(){
                            modal.hide();
                        });
                    });
                });
            });
        }
    });
});
