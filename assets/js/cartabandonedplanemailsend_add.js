$.ajax({
    method:'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'NewsletterTemplate',

    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#firstTemplateId');
    if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        options: res2,
    });
});
$.ajax({
    method:'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'NewsletterTemplate',

    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#secondTemplateId');
    if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        options: res2,
    });
});
$.ajax({
    method:'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'NewsletterTemplate',

    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#thirdTemplateId');
    if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        options: res2,
    });
});


$("#generateCoupon").change(function () {
    var selection = $(this).val();
    if (selection =='1'){
        $('#selectemaildiv').append(`<div class="col-md-6">
                                            <div class="form-group form-group-default selectize-enabled" >
                                                <label for="selectEmail">Su quali Invi Vuoi inviare il Coupon</label>
                                                <select id="selectEmail" name="selectEmail"
                                                        class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Seleziona l'invio"
                                                         <option value=""></option>
                                                          <option value=""></option>
                                                <option value="1">Primo Invio</option>
                                                <option value="2">Secondo Invio</option>
                                                <option value="3">Terzo Invio</option>
                                                <option value="4">Tutti gli Invii</option>
                                                
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>`);
        $("#coupondiv").append(`<div class="row">' +
           ' <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled" >
                                                <label for="typeCoupon">Tipo di Coupon</label>
                                                <select id="typeCoupon" name="typeCoupon"
                                                        class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Seleziona il tipo di Coupon"
                                                         <option value=""></option>
                                                          <option value=""></option>
                                                <option value="P">Importo Percentuale</option>
                                                <option value="F">Importo Fisso</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="amount">Inserisci l'importo o la percentuale</label>
                                                <input id="amount" class="form-control"
                                                       placeholder="Inserisci l'importo o la percentuale"
                                                       name="amount" required="required">
                                            </div>
                                        </div>
                                             
                                     <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="validity">Validità dalla Generazione</label>
                                                <select id="validity" name="validity"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona il tempo di Validità"
                                                        required="required"
                                                         <option value=""></option>
                                                          <option value=""></option>
                                                <option value="P1Y">Un Anno</option>
                                                <option value="P1M">Un Mese</option>
                                                <option value="P7D">Una settimana</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                       
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="validForCartTotal">Inserisci  il minimo importo di Spesa</label>
                                                <input id="validForCartTotal" class="form-control" 
                                                       placeholder="Inserisci  il minimo importo di Spesa"
                                                       name="validForCartTotal" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="freeShipping">Spedizione Gratuita</label>
                                                <select id="freeShipping" name="freeShipping"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona"
                                                        required="required"
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1">Si</option>
                                                <option value="2">No</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                         <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="freeReturn">Reso Gratuito</label>
                                                <select id="freeReturn" name="freeReturn"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona"
                                                        required="required"
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1">Si</option>
                                                <option value="2">No</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                  </div>
`);



    }else{
        $("#coupondiv").empty();

    }
    });

$(document).on('bs.newPlanSendEmail.save', function () {
    let bsModal = new $.bsModal('Salva La Pianificazione', {
        body: '<div><p>Conferma'+
        '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        var selectEmail=$('#selectEmail').val();
        var generateCoupon =$('#generateCoupon').val();
        var firstTemplateId =$('#firstTemplateId').val();
        var secondTemplateId=$('#secondTemplateId').val();
        var thirdTemplateId =$('#thirdTemplateId').val();
        var firstTimeEmailSendDay=$('#firstTimeEmailSendDay').val();
        var secondTimeEmailSendDay=$('#secondTimeEmailSendDay').val();
        var thirdTimeEmailSendDay=$('#thirdTimeEmailSendDay').val();
        var firstTimeEmailSendHour=$('#firstTimeEmailSendHour').val();
        var secondTimeEmailSendHour=$('#secondTimeEmailSendHour').val();
        var thirdTimeEmailSendHour=$('#thirdTimeEmailSendHour').val();
        var typeCoupon =$('#typeCoupon').val();
        var amount = $('#amount').val();
        var validity = $('#validity').val();
        var validForCartTotal=$('#validForCartTotal').val();
        var hasFreeShipping =$('#freeShipping').val();
        var hasFreeReturn =$('#freeReturn').val();
        const data = {
            generateCoupon:generateCoupon,
            selectEmail:selectEmail,
            firstTemplateId : firstTemplateId,
            secondTemplateId: secondTemplateId,
            thirdTemplateId:thirdTemplateId,
            firstTimeEmailSendDay:firstTimeEmailSendDay,
            firstTimeEmailSendHour:firstTimeEmailSendHour,
            secondTimeEmailSendDay:secondTimeEmailSendDay,
            secondTimeEmailSendHour:secondTimeEmailSendHour,
            thirdTimeEmailSendDay:thirdTimeEmailSendDay,
            thirdTimeEmailSendHour:thirdTimeEmailSendHour,
            typeCoupon:typeCoupon,
            amount:amount,
            validity:validity,
            validForCartTotal:validForCartTotal,
            hasFreeShipping:hasFreeShipping,
            hasFreeReturn:hasFreeReturn
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/CartAbandonedPlanEmailSendAddAjaxController',
            data: data
        }).done(function (res) {
            bsModal.writeBody(res);
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