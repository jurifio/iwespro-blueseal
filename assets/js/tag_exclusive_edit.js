$(document).on('bs.tag.exclusive.edit', function() {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Modifica Sezione Esclusiva');
    okButton.html('Fatto').off().on('click', function () {
        bsModal.modal('hide');
        okButton.off();
    });
    cancelButton.remove();

    $.ajax({
        type: "PUT",
        url: "#",
        data: $('form').serialize()
    }).done(function (content){
        body.html("Salvataggio riuscito");
        bsModal.modal();
    }).fail(function (){
        body.html("Errore grave");
        bsModal.modal();
    });
});

$('#shopId').change(function () {
    $('#storeHouse').removeClass('hide');
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Storehouse',
            condition: {shopId:$('#shopId').val() }
        },
        dataType: 'json'
    }).done(function (res2) {
        let selected = $('#storeHouseId');
        if (typeof (selected[0].selectize) != 'undefined') selected[0].selectize.destroy();
        selected.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            options: res2
        });

    });




});