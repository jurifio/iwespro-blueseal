window.buttonSetup = {
    tag:"a",
    icon:"fa-eye-slash",
    permission:"/admin/product/mag&&allShops",
    event:"bs-product-shopVisibleInvisible",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Accendi e spegni i friend",
    placement:"bottom"
};

$(document).on('bs-product-shopVisibleInvisible', function () {

    modal = new  $.bsModal(
        'Il magico interruttore dei Friend',
        {
            body: 'Sto caricando la lista dei friend...',
            isCancelButton: true,
        }
    );

    $.ajax({
        url: '/blueseal/xhr/ShopVisibleInvisible',
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
                var isVisible = (1 == res[i].isVisible) ? 'Visibile' : 'Invisibile' ;
                options += '<option value="' + res[i].id + '-' + res[i].isVisible + '">' + res[i].title + ': ' + isVisible + '</option>';
            }
            var body = '<p>I friend dal colore chiaro sono offline.<br/>' +
                'Selezionando un friend non Visibile sarà ripristinato, viceversa selezionando un friend Visible verrà portato in modalità invisible</p>' +
                '<div class="form-group selectize-enabled">' +
                '<label for="selectFriendVisible">Seleziona un Friend per procedere:</label>' +
                '<select class="form-control" id="selectFriendVisible" name="selectFriendVisible" >' +
                '<option value="" disabled selected>Seleziona un friend</option>' +
                options + '</select>' +
                '</div>';

            modal.writeBody(body);

            modal.setOkEvent(function () {
                var selVar = $('#selectFriendVisible').val().split('-');
                var choosen = {};
                choosen.id = selVar[0];
                choosen.isVisible = selVar[1];
                delete(selVar);

                modal.writeBody(
                    ((1 == choosen.isVisible) ? 'Il Friend verrà messo in Stato Invisibile.' : 'Il Friend verrà riportato Visible dal pulsante')
                    + ' Continuare?');
                var action = (0 == choosen.isVisible) ? 'start' : 'stop';

                modal.setOkEvent(function(){
                    let data = {shopId: choosen.id, action: action};
                    modal.showLoader();
                    $.ajax({
                        url: '/blueseal/xhr/ShopVisibleInvisible',
                        method: 'post',
                        data: data
                    }).done(function(res){
                        modal.writeBody(res);
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
