$.ajax({
    method:'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'Shop',
        condition :{hasEcommerce:1}
    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#shopId');
    if(typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        options: res2,
    });
});
$.ajax({
    method:'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'EmailTemplate',

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
        table: 'EmailTemplate',

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
        table: 'EmailTemplate',

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

        $("#coupondiv").append(`<div class="row">
           <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled" >
                                                <label for="typeCoupon">Tipo di Coupon</label>
                                                <select id="typeCoupon" name="typeCoupon"
                                                        class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Seleziona il tipo di Coupon"
                                                        data-init-plugin="selectize"
                                                         <option value=""></option>
                                                          <option value=""></option>
                                                <option value="P">Importo Percentuale</option>
                                                <option value="F">Importo Fisso</option>
                                                
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
                                                        data-init-plugin="selectize"
                                                        required="required"
                                                         <option value=""></option>
                                                          <option value=""></option>
                                                <option value="P1Y">Un Anno</option>
                                                <option value="P1M">Un Mese</option>
                                                <option value="P7D">Una settimana</option> 
                                                <option value="P3D">3 Giorni</option>
                                                <option value="P14D">14 Giorni</option>
                                                
                                                
                                                
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
                                                        data-init-plugin="selectize"
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                                
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
                                                        data-init-plugin="selectize"
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                                
                                                </select>
                                            </div>
                                        </div>
                                  </div>
`);



    }else{
        $("#coupondiv").empty();

    }
    });
$("#generateCoupon2").change(function () {
    var selection = $(this).val();
    if (selection =='1'){

        $("#coupondiv2").append(`<div class="row">
            <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="typeCoupon2">Tipo di Coupon</label>
                                                <select id="typeCoupon2" name="typeCoupon2"
                                                        class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Seleziona il tipo di Coupon"
                                                         data-init-plugin="selectize"
                                                         <option value=""></option>
                                                          <option value=""></option>
                                                <option value="P">Importo Percentuale</option>
                                                <option value="F">Importo Fisso</option>
                                               
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="amount">Inserisci l'importo o la percentuale</label>
                                                <input id="amount2" class="form-control"
                                                       placeholder="Inserisci l'importo o la percentuale"
                                                       name="amount2" required="required">
                                            </div>
                                        </div>
                                             
                                     <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="validity2">Validità dalla Generazione</label>
                                                <select id="validity2" name="validity2"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona il tempo di Validità"
                                                        required="required"
                                                         data-init-plugin="selectize"
                                                         <option value=""></option>
                                                          <option value=""></option>
                                                <option value="P1Y">Un Anno</option>
                                                <option value="P1M">Un Mese</option>
                                                <option value="P7D">Una settimana</option>
                                                <option value="P3D">3 Giorni</option>
                                                <option value="P14D">14 Giorni</option>
                                               
                                                </select>
                                            </div>
                                        </div>
                                       
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="validForCartTotal2">Inserisci  il minimo importo di Spesa</label>
                                                <input id="validForCartTotal2" class="form-control" 
                                                       placeholder="Inserisci  il minimo importo di Spesa"
                                                       name="validForCartTotal2" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="freeShipping2">Spedizione Gratuita</label>
                                                <select id="freeShipping2" name="freeShipping2"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona"
                                                        required="required"
                                                         data-init-plugin="selectize"
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                               
                                                </select>
                                            </div>
                                        </div>
                                         <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="freeReturn2">Reso Gratuito</label>
                                                <select id="freeReturn2" name="freeReturn2"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona"
                                                        required="required"
                                                        data-init-plugin="selectize"
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                                
                                                </select>
                                            </div>
                                        </div>
                                  </div>
`);



    }else{
        $("#coupondiv2").empty();

    }
});
$("#generateCoupon3").change(function () {
    var selection = $(this).val();
    if (selection =='1'){

        $("#coupondiv3").append(`<div class="row">
           <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled" >
                                                <label for="typeCoupon3">Tipo di Coupon</label>
                                                <select id="typeCoupon3" name="typeCoupon3"
                                                        class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Seleziona il tipo di Coupon"
                                                        data-init-plugin="selectize"
                                                         <option value=""></option>
                                                          <option value=""></option>
                                                <option value="P">Importo Percentuale</option>
                                                <option value="F">Importo Fisso</option>
                                               
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="amount3">Inserisci l'importo o la percentuale</label>
                                                <input id="amount3" class="form-control"
                                                       placeholder="Inserisci l'importo o la percentuale"
                                                       name="amount3" required="required">
                                            </div>
                                        </div>
                                             
                                     <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="validity3">Validità dalla Generazione</label>
                                                <select id="validity3" name="validity3"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona il tempo di Validità"
                                                        required="required"
                                                        data-init-plugin="selectize"
                                                         <option value=""></option>
                                                          <option value=""></option>
                                                <option value="P1Y">Un Anno</option>
                                                <option value="P1M">Un Mese</option>
                                                <option value="P7D">Una settimana</option>
                                                <option value="P3D">3 Giorni</option>
                                                <option value="P14D">14 Giorni</option>
                                                
                                                </select>
                                            </div>
                                        </div>
                                       
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="validForCartTotal3">Inserisci  il minimo importo di Spesa</label>
                                                <input id="validForCartTotal3" class="form-control" 
                                                       placeholder="Inserisci  il minimo importo di Spesa"
                                                       name="validForCartTotal3" required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="freeShipping3">Spedizione Gratuita</label>
                                                <select id="freeShipping3" name="freeShipping3"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona"
                                                        required="required"
                                                        data-init-plugin="selectize"
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                                
                                                </select>
                                            </div>
                                        </div>
                                         <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="freeReturn3">Reso Gratuito</label>
                                                <select id="freeReturn3" name="freeReturn3"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona"
                                                        required="required"
                                                        data-init-plugin="selectize"
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                               
                                                </select>
                                            </div>
                                        </div>
                                  </div>
`);



    }else{
        $("#coupondiv3").empty();

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
        var generateCoupon2 =$('#generateCoupon2').val();
        var generateCoupon3 =$('#generateCoupon3').val();
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
        var typeCoupon2 =$('#typeCoupon2').val();
        var typeCoupon3 =$('#typeCoupon3').val();
        var amount = $('#amount').val();
        var amount2 = $('#amount2').val();
        var amount3 = $('#amount3').val();
        var validity = $('#validity').val();
        var validity2 = $('#validity2').val();
        var validity3 = $('#validity3').val();
        var validForCartTotal=$('#validForCartTotal').val();
        var validForCartTotal2=$('#validForCartTotal2').val();
        var validForCartTotal3=$('#validForCartTotal3').val();
        var hasFreeShipping =$('#freeShipping').val();
        var hasFreeShipping2 =$('#freeShipping2').val();
        var hasFreeShipping3 =$('#freeShipping3').val();
        var hasFreeReturn =$('#freeReturn').val();
        var hasFreeReturn2 =$('#freeReturn2').val();
        var hasFreeReturn3 =$('#freeReturn3').val();
        var shopId=$('#shopId').val();
        const data = {
            shopId:shopId,
            generateCoupon:generateCoupon,
            generateCoupon2:generateCoupon2,
            generateCoupon3:generateCoupon3,
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
            typeCoupon2:typeCoupon2,
            typeCoupon3:typeCoupon3,
            amount:amount,
            amount2:amount2,
            amount3:amount3,
            validity:validity,
            validity2:validity2,
            validity3:validity3,
            validForCartTotal:validForCartTotal,
            validForCartTotal2:validForCartTotal2,
            validForCartTotal3:validForCartTotal3,
            hasFreeShipping:hasFreeShipping,
            hasFreeShipping2:hasFreeShipping2,
            hasFreeShipping3:hasFreeShipping3,
            hasFreeReturn:hasFreeReturn,
            hasFreeReturn2:hasFreeReturn2,
            hasFreeReturn3:hasFreeReturn3
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