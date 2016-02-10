$(document).on('bs.widget.translate',function(e,element,button) {

    var bsModal = $('#bsModal');
    var header = $('.modal-header h4');
    var body = $('.modal-body');
    var cancelButton = $('.modal-footer .btn-default');
    var okButton = $('.modal-footer .btn-success');

    header.html('Traduci widget');
    okButton.hide();
    cancelButton.html('Annulla').off().on('click', function() {
        bsModal.modal('hide');
    });

    var getVarsArray = [];
    var selectedRows = $('.table').DataTable().rows('.selected').data();
    var selectedRowsCount = selectedRows.length;

    if (selectedRowsCount < 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare uno widget da tradurre"
        }).open();
        return false;
    }

    if (selectedRowsCount > 1) {
        new Alert({
            type: "warning",
            message: "Puoi tradurre un solo widget per volta"
        }).open();
        return false;
    }

    var i = 0;
    $.each(selectedRows, function (k, v) {
        var rowId = v.DT_RowId.split('__');
        getVarsArray[i] = 'widgetKey=' + rowId[1] + '&widgetId=' + rowId[2] + '&contentEditorUrl=/blueseal/contenuti/catalogo/';
        i++;
    });

    var getVars = getVarsArray.join('&');

    $.ajax({
        type: "GET",
        url: "/blueseal/xhr/LanguageController",
        data: getVars
    }).done(function(content) {
        body.html(content);
        bsModal.modal();
    });
});