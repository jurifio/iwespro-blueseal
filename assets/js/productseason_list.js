$(document).on('bs.action.add.productseason', function () {


    let bodyForm = '';
    let bsModal = new $.bsModal('Aggiungi Stagione', {
        body: `<div class="row">
                <div class="col-md-6">
                <div class="form-group clearfix">
                <label for="nameValue">Nome Stagione:</label>
                <input type="text" name="nameValue" id="nameValue"
                       class="nameValue form-control"
                       value=""
                       required/>
                </div>
                 <div class="col-md-6">
                <div class="form-group clearfix">
                <label for="year">Anno Stagione:</label>
                <input type="text" name="year" id="year"
                       class="nameValue form-control"
                       value=""
                       required/>
                </div>
                
             
              
</div>
</div>
<div class="row">
                <div class="col-md-4">
                <div class="form-group clearfix">
                <label for="dateStart">Data inizio Stagione:</label>
                <input type="datetime-local" name="dateStart" id="dateStart"
                       class="dateStart form-control"
                       value=""
                       required/>
                </div>
                </div>
                 <div class="col-md-4">
                <div class="form-group clearfix">
                <label for="dateEnd">Data Fine Stagione:</label>
                 <input type="datetime-local" name="dateEnd" id="dateEnd"
                       class="nameValue form-control"
                       value=""
                       required/>
                </div>
                </div>
                <div class="col-md-4">
                <div class="form-group clearfix">
               <select name="isActive" id="isActive"
                                                                     class="full-width selectpicker"
                                                                     placeholder="Seleziona"
                                                                    data-init-plugin="selectize">
                                                                    <option value="0">non Attiva</option>
                                                                    <option value="1">Attiva</option>
                                                                    
                                                                    </select>
                </div>
                </div>
                </div>
                
`

    });
    bsModal.addClass("modal-wide");
    bsModal.addClass("modal-high");

    bsModal.setOkEvent(function () {

        const data = {
            nameValue: $('#nameValue').val(),
            year: $('#year').val(),
            dateStart: $('#dateStart').val(),
            dateEnd: $('#dateEnd').val(),
            isActive: $('#isActive').val()


        };

        $.ajax({
            method: 'POST',
            url: '/blueseal/xhr/ProductSeasonListAjaxController',
            data: data,
            dataType: 'json'
        }).done(function (res) {
            bsModal.writeBody(res.message);
            setTimeout(function () {
                if (!res.error) {

                    bsModal.setOkEvent(function () {
                        bsModal.hide();
                        window.location.reload();
                    });
                }

            }, 2000);

        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });


    });
});
$(document).on('bs.action.modify.productseason', function () {
    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();
    let isActive = selectedRows[0]['DT_isActive'];
    let isActiveSelected = 'selected=selected';
    let isActiveNotSelected = 'selected=selected';

    if (isActive != 1) {
        isActiveSelected = '';

    } else {
        isActiveNotSelected = '';
    }


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare una stagione    per modificarla"
        }).open();
        return false;
    }


    let bodyForm = '';
    let bsModal = new $.bsModal('Modifica', {
        body: `<div class="row">
                <div class="col-md-6">
                <div class="form-group clearfix">
                <label for="nameValue">Nome Stagione:</label>
                <input type="text" name="nameValue" id="nameValue"
                       class="nameValue form-control"
                       value="` + selectedRows[0].name + `"
                       required/>
                </div>
                 <div class="col-md-6">
                <div class="form-group clearfix">
                <label for="year">Anno Stagione:</label>
                <input type="text" name="year" id="year"
                       class="nameValue form-control"
                       value="` + selectedRows[0].year + `"
                       required/>
                </div>
                
             
              
</div>
</div>
<div class="row">
                <div class="col-md-4">
                <div class="form-group clearfix">
                <label for="dateStart">Data inizio Stagione:</label>
                <input type="datetime-local" name="dateStart" id="dateStart"
                       class="dateStart form-control"
                       value="` + selectedRows[0].dateStart + `"
                       required/>
                </div>
                </div>
                 <div class="col-md-4">
                <div class="form-group clearfix">
                <label for="dateEnd">Data Fine Stagione:</label>
                 <input type="datetime-local" name="dateEnd" id="dateEnd"
                       class="nameValue form-control"
                       value="` + selectedRows[0].dateEnd + `"
                       required/>
                </div>
                </div>
                <div class="col-md-4">
                <div class="form-group clearfix">
               <select name="isActive" id="isActive"
                                                                     class="full-width selectpicker"
                                                                     placeholder="Seleziona"
                                                                    data-init-plugin="selectize">
                                                                    <option value="0" `+ isActiveNotSelected +`>non Attiva</option>
                                                                    <option value="1" `+ isActiveSelected+`>Attiva</option>
                                                                    
                                                                    </select>
                </div>
                </div>
                </div>
                
`

    });

    bsModal.addClass("modal-wide");
    bsModal.addClass("modal-high");

    bsModal.setOkEvent(function () {

        const data = {
            id:selectedRows[0].DT_RowId,
            nameValue: $('#nameValue').val(),
            year: $('#year').val(),
            dateStart: $('#dateStart').val(),
            dateEnd: $('#dateEnd').val(),
            isActive: $('#isActive').val()

        };

        $.ajax({
            method: 'PUT',
            url: '/blueseal/xhr/ProductSeasonListAjaxController',
            data: data,
            dataType: 'json'
        }).done(function (res) {
            bsModal.writeBody(res.message);
            setTimeout(function () {
                if (!res.error) {
                    dataTable.ajax.reload(null, false);
                    bsModal.setOkEvent(function () {
                        bsModal.hide();
                        dataTable.ajax.reload(null, false);
                    });
                }

            }, 2000);

        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });


    });
});
$(document).on('bs.action.delete.productseason', function () {
    let dataTable = $('.dataTable').DataTable();
    let selectedRows = dataTable.rows('.selected').data();


    if (selectedRows.length != 1) {
        new Alert({
            type: "warning",
            message: "Devi selezionare un record per Cancellarlo  "
        }).open();
        return false;
    }

    let bodyForm = '';
    let bsModal = new $.bsModal('Cancella', {
        body: `
    < div

    class

    = "row" > < div

    class

    = "col-md-12" > Cancella
    il
    Record < /div>  
</div>
    `});
    bsModal.addClass("modal-wide");
    bsModal.addClass("modal-high");
    bsModal.setOkEvent(function () {

        const data = {
            id:selectedRows[0].DT_RowId,

        };

        $.ajax({
            method: 'DELETE',
            url: '/blueseal/xhr/ProductSeasonListAjaxController',
            data: data,
            dataType: 'json'
        }).done(function (res) {
            bsModal.writeBody(res.message);
            setTimeout(function () {
                if (!res.error) {
                    dataTable.ajax.reload(null, false);
                    bsModal.setOkEvent(function () {
                        bsModal.hide();
                        dataTable.ajax.reload(null, false);
                    });
                }

            }, 2000);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });


    });
});