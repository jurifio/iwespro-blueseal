$(document).on('bs.product.sale', function(){
    var dataTable = $('.dataTable').DataTable();
    var bsModal = $('#bsModal');
    var header = $('#bsModal .modal-header h4');
    var body = $('#bsModal .modal-body');
    var cancelButton = $('#bsModal .modal-footer .btn-default');
    var okButton = $('#bsModal .modal-footer .btn-success');

    var selectedRows = $('.table').DataTable().rows('.selected').data();

    var selectedRowsCount = selectedRows.length;

    if (!selectedRowsCount) {
        new Alert({
            type: "warning",
            message: "Devi selezionare almeno un prodotto"
        }).open();
        return false;
    }

    var i = 0;
    var row = [];
    var getVars = '';
    $.each(selectedRows, function (k, v) {
        row[i] = {};
        var idsVars = v.DT_RowId.split('__');
        row[i].id = idsVars[1];
        row[i].productVariantId = idsVars[2];
        row[i].name = v.brand;
        row[i].cpf = v.CPF;
        row[i].brand = v.brand;
        row[i].shops = v.shops;
        row[i].price = v.price;
        row[i].sale = v.sale;
        row[i].percentage = v.percentage;
        i++;
        getVars += 'row_' + i + '=' + v.DT_RowId.split('__')[1] + '&';
    });
    console.log(selectedRows);
    console.log(row);
    header.html('Assegna sconti');

    body.html('<form>' +
        '<div class="container-fluid">' +
        '<div class="row">' +
        '<div class="col-xs-4">' +
            '<label for="isSale">I prodotti selezionati vanno in sconto?</label></div>' +
        '<div class="col-xs-8">' +
            '<input type="radio" name="isSale" class="checkit" value="1"/> Sì&nbsp;&nbsp;&nbsp;<input type="radio" name="isSale" value="0"/> No' +
        '<input type="hidden" name="isOnSale" value="1" />' +
        '</div>' +
        '</div>' +
        '<div class="row">' +
        '<div class="col-xs-4">' +
            '<label for="percentage">Percentuale di sconto: </label></div>' +
        '<div class="col-xs-8">' +
        '<input type="text" maxlength="2" name="percentage" class="percentage" />' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</form>');
    $('.checkit').prop("checked", true);
    $('input[name="isSale"]').change(function(){
        if (1 == $(this).val()) $('.percentage').prop("disabled", false);
        else $('.percentage').prop("disabled", true);

    });
    body.css("text-align", 'left');
    bsModal.modal('show');

    okButton.off().on('click', function(){
        console.log($('input[name="isSale"]:checked').val());
        console.log($('input[name="percentage"]').val());

        $.ajax({
            url: "/blueseal/xhr/ProductSales",
            method: "POST",
            data: {
                action: "assign",
                rows: row,
                isSale: $('input[name="isSale"]:checked').val(),
                percentage: $('input[name="percentage"]').val()
            }
        }).done(function(res, a, b){
            body.html(res);
            cancelButton.hide();
            okButton.html('ok').off().on("click", function(){
                bsModal.modal('hide');
                dataTable.ajax.reload();
            });
        });
    });
});
