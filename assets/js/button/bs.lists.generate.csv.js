window.buttonSetup = {
    tag: "a",
    icon: "fa-eye",
    permission: "/admin/product/edit&&allShops",
    event: "bs.lists.generate.csv",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Esporta in CSV",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs.lists.generate.csv', function () {
    var table = $('.table');

    modal = new $.bsModal(
        'Download dei dati in CSV',
        {
            body: 'Sto generando il file da scaricare',
            okButtonLabel: 'Chiudi'
        }
    );

    var url = table.DataTable().ajax.url();
    var tempData = table.DataTable().ajax.params();
    tempData.length = 0;
    //console.log(data);
    Pace.ignore(function() {
        "use strict";
        $.ajax({
            url: url,
            method: 'GET',
            data: tempData,
            dataType: 'JSON'
        }).done(function (res) {
            var csv = '';
            var str = '';

            for (var i in res.data) {
                var line = '';
                for (var index in res.data[i]) {
                    if (line != '') line += ',';
                    var val = res.data[i][index];
                    //OriginalString.replace(/(<([^>]+)>)/ig,"");
                    //if($(val).find('table').length) val = 'escluso';
                    line += encodeURIComponent(val);
                }
                str += line + '%0A';
            }
            modal.writeBody('<a href="data:text/csv;charset=UTF-8,' + str + '" download="download.csv">Scarica il file</a>');
        }).fail(function(res) {
            modal.writeBody('Si è verificato un errore :/ riprova con meno elementi')
        });

    });
    //var data = table.data('params');
});