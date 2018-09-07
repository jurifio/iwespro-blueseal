;(function () {

    $(document).ready(function () {



        let fId = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);
        $.ajax({
            method:'GET',
            url: '/blueseal/xhr/GetTableContent',
            data: {
                table: 'Foison',
                condition: {
                    id: fId
                }
            },
            dataType: 'json'
        }).done(function (res1) {
            $.ajax({
                method:'GET',
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'UserAddress',
                    condition: {
                        userId: res1[0].userId
                    }
                },
                dataType: 'json'
            }).done(function (res2) {
                $('#country').val(res2[0].countryId)
            });
        });
    });

    $(document).on('bs.foison.user.address.save', function () {

        let foisonId = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

        const data = {
            foisonId: foisonId,
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
            phone: $('#foison_phone').val(),
            password: $('#password').val()
        };

        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/FoisonDetailManage',
            data: data
        }).done(function (res) {
            new Alert({
                type: "success",
                message: "Dati salvati con successo"
            }).open();
            return false;


        }).fail(function (res) {
            new Alert({
                type: "error",
                message: "Errore durante il salvataggio dei dati, contattare l'assistanza tecnica"
            }).open();
            return false;
        });


    });

})();