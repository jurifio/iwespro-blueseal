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
    $(modal.okButton).prop('disabled', true);
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

            var datatable = $('.table').DataTable();
            var columns = [];
            var title = [];
            datatable.columns().every(function(k,v) {
                v = datatable.column(k);
                columns.push(v.dataSrc());
                title.push($(v.header()).attr('aria-label').split(':')[0].trim());

            });
            str += title.join(',') + '%0A';
            var line;
            for (var i in res.data) {
                line = [];
                for (var index in columns) {
                    var val = res.data[i][columns[index]];
                    var div = document.createElement("div");
                    div.innerHTML = val;
                    //OriginalString.replace(/(<([^>]+)>)/ig,"");
                    //if($(val).find('table').length) val = 'escluso';
                    line.push(encodeURIComponent(div.innerText));
                }
                str += line.join(',') + '%0A';
            }
            modal.writeBody('<a href="data:text/csv;charset=UTF-8,' + str + '" download="download.csv">Scarica il file</a>');
            $(modal.okButton).prop('disabled', false);
        }).fail(function(res) {
            modal.writeBody('Si è verificato un errore :/ riprova con meno elementi')
        });

    });
    //var data = table.data('params');
});