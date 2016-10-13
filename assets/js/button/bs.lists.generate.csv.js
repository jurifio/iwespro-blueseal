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
    var data = table.DataTable().ajax.params();
    //console.log(data);

    $.ajax({
        url: url,
        method: 'GET',
        data: data,
        dataType: 'JSON'
    }).done(function (res) {
        var csv = '';
        var str = '';
        console.log(res.data);
        for (var i in res.data) {
            var line = '';
            for (var index in res.data[i]) {
                if (line != '') line += ','
                line += res.data[i][index];
            }
            str += line + '%0A';
        }
        console.log(str);
        modal.writeBody('<a href="data:text/csv;charset=UTF-8,' + str + '" download="download.csv">Scarica il file</a>');
    });

    var data = table.data('params');
    console.log(data);
});
