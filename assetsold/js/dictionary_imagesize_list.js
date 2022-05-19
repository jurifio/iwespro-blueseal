$(document).on('bs-dictionaryimagesize-insert', function () {
    let bsModal = new $.bsModal('Inserimento parametri Immagini da elaborare', {

        body: `
        <div class="form-group form-group-default required">
        <label for="shopId">Seleziona il Friend</label> 
        <select id="shopId" name="shopId">' 
        <option disabled selected value>Seleziona un'opzione</option>
       </select> 
       </div>
        <div class="form-group form-group-default">
         <label for="widthImage">Larghezza Immagine</label>
         <input id="widthImage" autocomplete="off" type="text"
         class="form-control" name="widthImage" value=""/>
         <span class="bs red corner label"><i
         class="fa fa-asterisk"></i></span> 
        </div>  
        <div class="form-group form-group-default">
         <label for="heightImage">Altezza Immagine</label>
         <input id="heightImage" autocomplete="off" type="text"
         class="form-control" name="heightImage" value=""/>
         <span class="bs red corner label"><i
         class="fa fa-asterisk"></i></span> 
        </div> 
        <div class="form-group form-group-default">
         <label for="widthImageCopy">Larghezza Immagine Copia</label>
         <input id="widthImageCopy" autocomplete="off" type="text"
         class="form-control" name="widthImageCopy" value=""/>
         <span class="bs red corner label"><i
         class="fa fa-asterisk"></i></span> 
        </div>  
        <div class="form-group form-group-default">
         <label for="heightImageCopy">Altezza Immagine Copia</label>
         <input id="heightImageCopy" autocomplete="off" type="text"
         class="form-control" name="heightImageCopia" value=""/>
         <span class="bs red corner label"><i
         class="fa fa-asterisk"></i></span> 
        </div> 
         <div class="form-group form-group-default">
         <label for="divisionByX">Rapporto Asse X di Posizionamento </label>
         <input id="divisionByX" autocomplete="off" type="text"
         class="form-control" name="divisionByX" value=""/>
         <span class="bs red corner label"><i
         class="fa fa-asterisk"></i></span> 
        </div>  
        <div class="form-group form-group-default">
         <label for="divisionByY">Rapporto Asse Y di Posizionamento</label>
         <input id="divisionByY" autocomplete="off" type="text"
         class="form-control" name="divisionByY" value=""/>
         <span class="bs red corner label"><i
         class="fa fa-asterisk"></i></span> 
        </div> 
        <div class="form-group form-group-default">
         <label for="widthPercentageVariation">Percentuale di ingrandimento Larghezza</label>
         <input id="widthPercentageVariation" autocomplete="off" type="text"
         class="form-control" name="widthPercentageVariation" value=""/>
         <span class="bs red corner label"><i
         class="fa fa-asterisk"></i></span> 
        </div>  
        <div class="form-group form-group-default">
         <label for="heightPercentageVariation">Percentuale di ingrandimento Altezza</label>
         <input id="heightPercentageVariation" autocomplete="off" type="text"
         class="form-control" name="heightPercentageVariation" value=""/>
         <span class="bs red corner label"><i
         class="fa fa-asterisk"></i></span> 
        </div> 
        <div class="form-group form-group-default required">
        <label for="destinationfile">Template File di Destinazione</label> 
        <select id="destinationfile" name="destinationfile">' 
        <option disabled selected value>Seleziona un'opzione</option>
        <option value="destination1125X1500.jpg">destination1125X1500.jpg</option>
        <option value="destination1200X1500.jpg">destination1200X1500.jpg</option>
        <option value="destination2000X2500.jpg">destination2000X2500.jpg</option>
        </select> 
        </div>
        <div class="form-group form-group-default required">
        <label for="renameAction">Rinomina File</label> 
        <select id="renameAction" name="renameAction"> 
        <option disabled selected value>Seleziona un'opzione</option>
        <option value="1">Si</option>
        <option value="0">No</option>
        </select> 
        </div>
        <div class="form-group form-group-default required">
        <label for="useDivision">Usa Divisore</label> 
        <select id="useDivision" name="useDivision"> 
        <option disabled selected value>Seleziona un'opzione</option>
        <option value="1">Si</option>
        <option value="0">No</option>
        </select> 
        </div>
       <div class="form-group form-group-default">
         <label for="destinationXPoint">Punto di Destinazione Immagine Asse X</label>
         <input id="destinationXPoint" autocomplete="off" type="text"
         class="form-control" name="destinationXPoint" value=""/>
         <span class="bs red corner label"><i
         class="fa fa-asterisk"></i></span> 
        </div>
    <div class="form-group form-group-default">
         <label for="destinationYPoint">Punto di Destinazione Immagine Asse Y</label>
         <input id="destinationYPoint" autocomplete="off" type="text"
         class="form-control" name="destinationYPoint" value=""/>
         <span class="bs red corner label"><i
         class="fa fa-asterisk"></i></span> 
        </div>
        <div class="form-group form-group-default required">
        <label for="emptyZero">Sequenziali con Zero Nome</label> 
        <select id="emptyZero" name="emptyZero">' 
        <option disabled selected value>Seleziona un'opzione</option>
        <option value="1">Si</option>
        <option value="0">No</option>
        </select> 
        </div>
        <div class="form-group form-group-default required">
        <label for="coverImage">Imponi immagine Cover</label> 
        <select id="coverImage" name="coverImage">' 
        <option disabled selected value>Seleziona un'opzione</option>
        <option value="1">Si</option>
        <option value="0">No</option>
        </select> 
        </div>`


    });
    $.ajax({
        method:'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Shop'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#shopId');
        if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        const data={
            shopId: $('#shopId').val(),
            widthImage:$('#widthImage').val(),
            heightImage:$('#heightImage').val(),
            widthImageCopy:$('#widthImageCopy').val(),
            heightImageCopy:$('#heightImageCopy').val(),
            divisionByX:$('#divisionByX').val(),
            divisionByY:$('#divisionByY').val(),
            widthPercentageVariation:$('#widthPercentageVariation').val(),
            heightPercentageVariation:$('#heightPercentageVariation').val(),
            destinationfile:$('#destinationfile').val(),
            renameAction:$('#renameAction').val(),
            useDivision:$('#useDivision').val(),
            destinationXPoint:$('#destinationXPoint').val(),
            destinationYPoint:$('#destinationYPoint').val(),
            emptyZero:$('#emptyZero').val(),
            coverImage:$('#coverImage').val(),

        };
        $.ajax({
            method: 'PUT',
            url: "/blueseal/xhr/DictionaryImageSizeManageAjaxController",
            data:data
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                // window.location.reload();
                bsModal.hide();
                // window.location.reload();
            });
            bsModal.showOkBtn();
        });

    });
});

$(document).on('bs-dictionaryimagesize-modify', function () {
    let selectedRows = $('.table').DataTable().rows('.selected').data();
    let parameterId = selectedRows[0].row_id;

    if(selectedRows.length != 1){
        new Alert({
            type: "warning",
            message: "Puoi modificare un parametro alla volta"
        }).open();
        return false;
    }
    $.ajax({
        method: "get",
        url: "/DictionaryImageSizeManageAjaxController",
        data: {
             id: parameterId
        }
    }).done(function (res) {
       alert(res)
    }).fail(function (res) {
        alert(res);
    }).always(function (res) {
       alert(res);
    });

    let bsModal = new $.bsModal('Modifica Parametri Dizionario', {
        body: `<div>
               <p>Inserire il nuovo nome dell'inserzione</p>
               <input type="text" id="name-modify">
               </div>`
    });

    bsModal.setOkEvent(function () {

        let insName = $('#name-modify').val();
        $.ajax({
            method: "get",
            url: "/blueseal/xhr/NewsletterInsertionManage",
            data: {
                name: insName,
                insertionId: insertionId
            }
        }).done(function (res) {
            bsModal.writeBody(res);
        }).fail(function (res) {
            bsModal.writeBody(res);
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                $.refreshDataTable()
            });
            bsModal.showOkBtn();
        });
    });

});