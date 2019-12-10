$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'NewsletterTemplate',

    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#firstTemplateId');
    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        options: res2,
    });
});
$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'NewsletterTemplate',

    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#secondTemplateId');
    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        options: res2,
    });
});
$.ajax({
    method: 'GET',
    url: '/blueseal/xhr/GetTableContent',
    data: {
        table: 'NewsletterTemplate',

    },
    dataType: 'json'
}).done(function (res2) {
    var select = $('#thirdTemplateId');
    if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
    select.selectize({
        valueField: 'id',
        labelField: 'name',
        searchField: 'name',
        options: res2,
    });
});
$(document).ready(function () {
    var selection = $('#generateCoupon').val();
    if (selection == '1') {
        var amount1 = $('#amount1').val();
        var amountType1 = $('#amountType1').val();
        var amountTypeP1 = '';
        var amountTypeF1 = '';
        if (amountType1 == "P") {
            amountTypeP1 = 'selected="selected"';
            amountTypeF1 = '';
        } else {
            amountTypeF1 = 'selected="selected"';
            amountTypeP1 = '';
        }
        var P1Y1 = '';
        var P1M1 = '';
        var P7D1 = '';
        var P3D1 = '';
        var P14D1 = '';
        var validity1 = $('#validity1').val();
        switch (validity1) {
            case 'P1Y':
                P1Y1 = 'selected="selected"';
                P1M1 = '';
                P7D1 = '';
                P3D1 = '';
                P14D1 = '';


                break;
            case  'P1M':
                P1Y1 = '';
                P1M1 = 'selected="selected"';
                P7D1 = '';
                P3D1 = '';
                P14D1 = '';
                break;
            case  'P7D':
                P1Y1 = '';
                P1M1 = '';
                P7D1 = 'selected="selected"';
                P3D1 = '';
                P14D1 = '';
                break;
            case  'P3D':
                P1Y1 = '';
                P1M1 = '';
                P7D1 = '';
                P3D1 = 'selected="selected"';
                P14D1 = '';
                break;
            case  'P3D':
                P1Y1 = '';
                P1M1 = '';
                P7D1 = '';
                P3D1 = '';
                P14D1 = 'selected="selected"';
                break;
        }
        var validForCartTotal1 = $('#validForCartTotal1').val();
        var hasFreeShipping1 = $('#hasFreeShipping1').val();
        var hasFreeReturn1 = $('#hasFreeReturn1').val();
        var hasFreeShippingYes1 = '';
        var hasFreeShippingNo1 = '';
        var hasFreeReturnYes1 = '';
        var hasFreeReturnNo1 = '';
        if (hasFreeShipping1 == '1') {
            hasFreeShippingYes1 = 'selected="selected"';
            hasFreeShippingNo1 = '';
        } else {
            hasFreeShippingYes1 = '';
            hasFreeShippingNo1 = 'selected="selected"';
        }
        if (hasFreeReturn1 == '1') {
            hasFreeReturnYes1 = 'selected="selected"';
            hasFreeReturnNo1 = '';
        } else {
            hasFreeReturnYes1 = '';
            hasFreeReturnNo1 = 'selected="selected"';
        }

        $("#coupondiv").append(`<div class="row">
           <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled" >
                                                <label for="typeCoupon">Tipo Coupon</label>
                                                <select id="typeCoupon" name="typeCoupon"
                                                        class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Seleziona il tipo di Coupon"
                                                         <option value=""></option>
                                                          <option value=""></option>
                                                <option value="P" ${amountTypeP1}>Importo Percentuale</option>
                                                <option value="F" ${amountTypeF1}>Importo Fisso</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="amount">Importo Coupon</label>
                                                <input id="amount" class="form-control"
                                                       placeholder="Inserisci l'importo o la percentuale"
                                                       name="amount" required="required"
                                                        value="${amount1}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="validity">Scadenza Coupon</label>
                                                <select id="validity" name="validity"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona il tempo di Validità"
                                                        required="required"
                                                         <option value=""></option>
                                                          <option value=""></option>
                                                <option value="P1Y"${P1Y1}>Un Anno</option>
                                                <option value="P1M"${P1M1}>Un Mese</option>
                                                <option value="P7D"${P7D1}>Una settimana</option>
                                                 <option value="P3D"${P3D1}>3 Giorni</option>
                                                 <option value="P14D"${P14D1}>14 Giorni</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="validForCartTotal">Spesa</label>
                                                <input id="validForCartTotal" class="form-control" 
                                                       placeholder="Inserisci  il minimo importo di Spesa"
                                                       name="validForCartTotal" required="required"
                                                       value="${validForCartTotal1}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="freeShipping">Spedizione gratuita</label>
                                                <select id="freeShipping" name="freeShipping"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona"
                                                        required="required"
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1"${hasFreeShippingYes1}>Si</option>
                                                <option value="0"${hasFreeShippingNo1}>No</option>
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
                                                <option value="1"${hasFreeReturnYes1}>Si</option>
                                                <option value="0"${hasFreeReturnNo1}>No</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                  </div>
`);


    } else {
        $("#coupondiv").empty();

    }
});
$(document).ready(function () {
    var selection2 = $('#generateCoupon2').val();
    if (selection2 == '1') {
        var amount2 = $('#amount2').val();
        var amountType2 = $('#amountType2').val();
        var amountTypeP2 = '';
        var amountTypeF2 = '';
        if (amountType2 == "P") {
            amountTypeP2 = 'selected="selected"';
            amountTypeF2 = '';
        } else {
            amountTypeF2 = 'selected="selected"';
            amountTypeP2 = '';
        }
        var P1Y2 = '';
        var P1M2 = '';
        var P7D2 = '';
        var P3D2 = '';
        var P14D2 = '';
        var validity2 = $('#validity2').val();
        switch (validity2) {
            case 'P1Y':
                P1Y2 = 'selected="selected"';
                P1M2 = '';
                P7D2 = '';
                P14D2 = '';
                P3D2 = '';
                break;
            case  'P1M':
                P1Y2 = '';
                P1M2 = 'selected="selected"';
                P7D2 = '';
                P14D2 = '';
                P3D2 = '';
                break;
            case  'P7D':
                P1Y2 = '';
                P1M2 = '';
                P7D2 = 'selected="selected"';
                P14D2 = '';
                P3D2 = '';
                break;
            case  'P14D':
                P1Y2 = '';
                P1M2 = '';
                P7D2 = '';
                P14D2 = 'selected="selected"';
                P3D2 = '';
                break;
            case  'P3D':
                P1Y2 = '';
                P1M2 = '';
                P7D2 = '';
                P14D2 = '';
                P3D2 = 'selected="selected"';
                break;
        }
        var validForCartTotal2 = $('#validForCartTotal2').val();
        var hasFreeShipping2 = $('#hasFreeShipping2').val();
        var hasFreeReturn2 = $('#hasFreeReturn2').val();
        var hasFreeShippingYes2 = '';
        var hasFreeShippingNo2 = '';
        var hasFreeReturnYes2 = '';
        var hasFreeReturnNo2 = '';
        if (hasFreeShipping2 == '1') {
            hasFreeShippingYes2 = 'selected="selected"';
            hasFreeShippingNo2 = '';
        } else {
            hasFreeShippingYes2 = '';
            hasFreeShippingNo2 = 'selected="selected"';
        }
        if (hasFreeReturn2 == '1') {
            hasFreeReturnYes2 = 'selected="selected"';
            hasFreeReturnNo2 = '';
        } else {
            hasFreeReturnYes2 = '';
            hasFreeReturnNo2 = 'selected="selected"';
        }

        $("#coupondiv2").append(`<div class="row">
            <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="typeCoupon2">Tipo Coupon</label>
                                                <select id="typeCoupon2" name="typeCoupon2"
                                                        class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Seleziona il tipo di Coupon"
                                                         <option value=""></option>
                                                          <option value=""></option>
                                               <option value="P" ${amountTypeP2}>Importo Percentuale</option>
                                                <option value="F" ${amountTypeF2}>Importo Fisso</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="amount">Importo Coupon</label>
                                                <input id="amount2" class="form-control"
                                                       placeholder="Inserisci l'importo o la percentuale"
                                                       name="amount2" required="required"
                                                       value="${amount2}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="validity2">Scadenza Coupon</label>
                                                <select id="validity2" name="validity2"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona il tempo di Validità"
                                                        required="required"
                                                         <option value=""></option>
                                                          <option value=""></option>
                                                <option value="P1Y"${P1Y2}>Un Anno</option>
                                                <option value="P1M"${P1M2}>Un Mese</option>
                                                <option value="P7D"${P7D2}>Una settimana</option>
                                                <option value="P3D"${P3D2}>3 Giorni</option>
                                                 <option value="P14D"${P14D2}>14 Giorni</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="validForCartTotal2">Spesa</label>
                                                <input id="validForCartTotal2" class="form-control" 
                                                       placeholder="Inserisci  il minimo importo di Spesa"
                                                       name="validForCartTotal2" required="required"
                                                        value="${validForCartTotal2}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="freeShipping2">Spedizione Gratuita</label>
                                                <select id="freeShipping2" name="freeShipping2"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona"
                                                        required="required"
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1"${hasFreeShippingYes2}>Si</option>
                                                <option value="0"${hasFreeShippingNo2}>No</option>
                                                data-init-plugin="selectize"
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
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1"${hasFreeReturnYes2}>Si</option>
                                                <option value="0"${hasFreeReturnNo2}>No</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                  </div>
`);


    } else {
        $("#coupondiv2").empty();

    }
});
$(document).ready(function () {
    var selection3 = $('#generateCoupon3').val();
    if (selection3 == '1') {
        var amount3 = $('#amount3').val();
        var amountType3 = $('#amountType3').val();
        var amountTypeP3 = '';
        var amountTypeF3 = '';
        if (amountType3 == "P") {
            amountTypeP3 = 'selected="selected"';
            amountTypeF3 = '';
        } else {
            amountTypeF3 = 'selected="selected"';
            amountTypeP3 = '';
        }
        var P1Y3 = '';
        var P1M3 = '';
        var P7D3 = '';
        var P3D3 = '';
        var P14D3 = '';
        var validity3 = $('#validity3').val();
        switch (validity3) {
            case 'P1Y':
                P1Y3 = 'selected="selected"';
                P1M3 = '';
                P7D3 = '';
                P3D3 = '';
                P14D3 = '';
                break;
            case  'P1M':
                P1Y3 = '';
                P1M3 = 'selected="selected"';
                P7D3 = '';
                P3D3 = '';
                P14D3 = '';
                break;
            case  'P7D':
                P1Y3 = '';
                P1M3 = '';
                P7D3 = 'selected="selected"';
                P3D3 = '';
                P14D3 = '';
                break;
            case  'P3D':
                P1Y3 = '';
                P1M3 = '';
                P7D3 = '';
                P3D3 = 'selected="selected"';
                P14D3 = '';
                break;
            case  'P14D':
                P1Y3 = '';
                P1M3 = '';
                P7D3 = '';
                P3D3 = '';
                P14D3 = 'selected="selected"';
                break;
        }
        var validForCartTotal3 = $('#validForCartTotal3').val();
        var hasFreeShipping3 = $('#hasFreeShipping3').val();
        var hasFreeReturn3 = $('#hasFreeReturn3').val();
        var hasFreeShippingYes3 = '';
        var hasFreeShippingNo3 = '';
        var hasFreeReturnYes3 = '';
        var hasFreeReturnNo3 = '';
        if (hasFreeShipping3 == '1') {
            hasFreeShippingYes3 = 'selected="selected"';
            hasFreeShippingNo3 = '';
        } else {
            hasFreeShippingYes3 = '';
            hasFreeShippingNo3 = 'selected="selected"';
        }
        if (hasFreeReturn3 == '1') {
            hasFreeReturnYes3 = 'selected="selected"';
            hasFreeReturnNo3 = '';
        } else {
            hasFreeReturnYes3 = '';
            hasFreeReturnNo3 = 'selected="selected"';
        }

        $("#coupondiv3").append(`<div class="row">
           <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled" >
                                                <label for="typeCoupon3">Tipo Coupon</label>
                                                <select id="typeCoupon3" name="typeCoupon3"
                                                        class="full-width selectpicker"
                                                        required="required"
                                                        placeholder="Seleziona il tipo di Coupon"
                                                         <option value=""></option>
                                                          <option value=""></option>
                                                <option value="P"${amountTypeP3}>Importo Percentuale</option>
                                                <option value="F"${amountTypeF3}>Importo Fisso</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="amount3">Importo Coupon</label>
                                                <input id="amount3" class="form-control"
                                                       placeholder="Inserisci l'importo o la percentuale"
                                                       name="amount3" required="required"
                                                       value="${amount3}">
                                            </div>
                                        </div>
                                             
                                     <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="validity3">Scadenza Coupon</label>
                                                <select id="validity3" name="validity3"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona il tempo di Validità"
                                                        required="required"
                                                         <option value=""></option>
                                                          <option value=""></option>
                                                <option value="P1Y"${P1Y3}>Un Anno</option>
                                                <option value="P1M"${P1M3}>Un Mese</option>
                                                <option value="P7D"${P7D3}>Una settimana</option>
                                                 <option value="P3D"${P3D3}>3 Giorni</option>
                                                 <option value="P14D"${P14D3}>14 Giorni</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                       
                                        <div class="col-md-1">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="validForCartTotal3">Spesa</label>
                                                <input id="validForCartTotal3" class="form-control" 
                                                       placeholder="Inserisci  il minimo importo di Spesa"
                                                       name="validForCartTotal3" required="required"
                                                       value="${validForCartTotal3}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="freeShipping3">Spedizione Gratuita</label>
                                                <select id="freeShipping3" name="freeShipping3"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona"
                                                        required="required"
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1"${hasFreeShippingYes3}>Si</option>
                                                <option value="0"${hasFreeShippingNo3}>No</option>
                                                data-init-plugin="selectize"
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
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1"${hasFreeReturnYes3}>Si</option>
                                                <option value="0"${hasFreeReturnNo3}>No</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                  </div>
`);


    } else {
        $("#coupondiv3").empty();

    }
});


$("#generateCoupon").change(function () {
    var selection = $(this).val();
    if (selection == '1') {

        $("#coupondiv").append(`<div class="row">
           <div class="row">
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
                                                 <option value="P3D">3 Giorni</option>
                                                  <option value="P14D">Due settimane</option>
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
                                                <option value="0">No</option>
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
                                                <option value="0">No</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                  </div>
`);


    } else {
        $("#coupondiv").empty();

    }
});
$("#generateCoupon2").change(function () {
    var selection = $(this).val();
    if (selection == '1') {

        $("#coupondiv2").append(`<div class="row">
            <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="typeCoupon2">Tipo di Coupon</label>
                                                <select id="typeCoupon2" name="typeCoupon2"
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
                                                         <option value=""></option>
                                                          <option value=""></option>
                                                <option value="P1Y">Un Anno</option>
                                                <option value="P1M">Un Mese</option>
                                                <option value="P7D">Una settimana</option>
                                                  <option value="P3D">3 Giorni</option>
                                                  <option value="P14D">Due settimane</option>
                                                data-init-plugin="selectize"
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
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                                data-init-plugin="selectize"
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
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                  </div>
`);


    } else {
        $("#coupondiv2").empty();

    }
});
$("#generateCoupon3").change(function () {
    var selection = $(this).val();
    if (selection == '1') {

        $("#coupondiv3").append(`<div class="row">
           <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled" >
                                                <label for="typeCoupon3">Tipo di Coupon</label>
                                                <select id="typeCoupon3" name="typeCoupon3"
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
                                                         <option value=""></option>
                                                          <option value=""></option>
                                                <option value="P1Y">Un Anno</option>
                                                <option value="P1M">Un Mese</option>
                                                <option value="P7D">Una settimana</option>
                                                  <option value="P3D">3 Giorni</option>
                                                  <option value="P14D">Due settimane</option>
                                                data-init-plugin="selectize"
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
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                                data-init-plugin="selectize"
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
                                                         <option value=""></option>
                                                 <option value=""></option>
                                                <option value="1">Si</option>
                                                <option value="0">No</option>
                                                data-init-plugin="selectize"
                                                </select>
                                            </div>
                                        </div>
                                  </div>
`);


    } else {
        $("#coupondiv3").empty();

    }
});

$(document).on('bs.newPlanSendEmail.save', function () {
    let bsModal = new $.bsModal('Salva La Pianificazione', {
        body: '<div><p>Conferma' +
            '</div>'
    });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        var selectEmail = $('#selectEmail').val();
        var generateCoupon = $('#generateCoupon').val();
        var generateCoupon2 = $('#generateCoupon2').val();
        var generateCoupon3 = $('#generateCoupon3').val();
        var firstTemplateId = $('#firstTemplateId').val();
        var secondTemplateId = $('#secondTemplateId').val();
        var thirdTemplateId = $('#thirdTemplateId').val();
        var firstTimeEmailSendDay = $('#firstTimeEmailSendDay').val();
        var secondTimeEmailSendDay = $('#secondTimeEmailSendDay').val();
        var thirdTimeEmailSendDay = $('#thirdTimeEmailSendDay').val();
        var firstTimeEmailSendHour = $('#firstTimeEmailSendHour').val();
        var secondTimeEmailSendHour = $('#secondTimeEmailSendHour').val();
        var thirdTimeEmailSendHour = $('#thirdTimeEmailSendHour').val();
        var typeCoupon = $('#typeCoupon').val();
        var typeCoupon2 = $('#typeCoupon2').val();
        var typeCoupon3 = $('#typeCoupon3').val();
        var amount = $('#amount').val();
        var amount2 = $('#amount2').val();
        var amount3 = $('#amount3').val();
        var validity = $('#validity').val();
        var validity2 = $('#validity2').val();
        var validity3 = $('#validity3').val();
        var validForCartTotal = $('#validForCartTotal').val();
        var validForCartTotal2 = $('#validForCartTotal2').val();
        var validForCartTotal3 = $('#validForCartTotal3').val();
        var hasFreeShipping = $('#freeShipping').val();
        var hasFreeShipping2 = $('#freeShipping2').val();
        var hasFreeShipping3 = $('#freeShipping3').val();
        var hasFreeReturn = $('#freeReturn').val();
        var hasFreeReturn2 = $('#freeReturn2').val();
        var hasFreeReturn3 = $('#freeReturn3').val();
        var cartIdEmailParam1 = $('#cartIdEmailParam1').val();
        var coupon1TypeId = $('#coupon1TypeId').val();
        var coupon2TypeId = $('#coupon2TypeId').val();
        var coupon3TypeId = $('#coupon3TypeId').val();
        var shopId=$('#shopId').val();

        const data = {
            generateCoupon: generateCoupon,
            generateCoupon2: generateCoupon2,
            generateCoupon3: generateCoupon3,
            selectEmail: selectEmail,
            firstTemplateId: firstTemplateId,
            secondTemplateId: secondTemplateId,
            thirdTemplateId: thirdTemplateId,
            firstTimeEmailSendDay: firstTimeEmailSendDay,
            firstTimeEmailSendHour: firstTimeEmailSendHour,
            secondTimeEmailSendDay: secondTimeEmailSendDay,
            secondTimeEmailSendHour: secondTimeEmailSendHour,
            thirdTimeEmailSendDay: thirdTimeEmailSendDay,
            thirdTimeEmailSendHour: thirdTimeEmailSendHour,
            typeCoupon: typeCoupon,
            typeCoupon2: typeCoupon2,
            typeCoupon3: typeCoupon3,
            amount: amount,
            amount2: amount2,
            amount3: amount3,
            validity: validity,
            validity2: validity2,
            validity3: validity3,
            validForCartTotal: validForCartTotal,
            validForCartTotal2: validForCartTotal2,
            validForCartTotal3: validForCartTotal3,
            hasFreeShipping: hasFreeShipping,
            hasFreeShipping2: hasFreeShipping2,
            hasFreeShipping3: hasFreeShipping3,
            hasFreeReturn: hasFreeReturn,
            hasFreeReturn2: hasFreeReturn2,
            hasFreeReturn3: hasFreeReturn3,
            cartIdEmailParam1: cartIdEmailParam1,
            coupon1TypeId: coupon1TypeId,
            coupon2TypeId: coupon2TypeId,
            coupon3TypeId: coupon3TypeId,
            shopId:shopId

        };
        $.ajax({
            method: 'put',
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