;(function () {


    $(document).on('bs.foison.user.address.save', function () {

        
        const data = {
             name: $('#foison_name').val(),
             surname: $('#foison_surname').val(),
             birthdate: $('#foison_birthdate').val(),
             fiscalCode: $('#foison_fiscal_code').val(),
             iban: $('#iban').val(),
             address: $('#address').val(),
             province: $('#province').val(),
             city: $('#city').val(),
             postalCode: $('#postcode').val(),
             country: $('#country').val(),
             phone: $('#foison_phone').val()
        };

            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/FoisonDetailManage',
                data: data
            }).done(function (res) {
                new Alert( {
                    type: "success",
                    message: "Dati salvati con successo"
                }).open();
                return false;


            }).fail(function (res) {
                new Alert( {
                    type: "error",
                    message: "Errore durante il salvataggio dei dati, contattare l'assistanza tecnica"
                }).open();
                return false;
            });
     
        
    });

})();