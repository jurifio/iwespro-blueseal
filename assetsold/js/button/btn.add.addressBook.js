window.buttonSetup = {
    tag: "a",
    icon: "fa-address-card-o",
    permission: "/admin/product/edit",
    event: "btn-add-addressBook",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Aggiungi Indirizzo",
    placement: "bottom"
};

$(document).on('btn-add-addressBook', function (e, element, button) {
    let modal = new $.bsModal(
        'Aggiungi Indirizzo',
        {}
    );
    let selector = '.shippingAddress';
    Pace.ignore(function () {
        "use strict";
        $.when(
            $.getTemplate('addressBookFormMock'),
            $.ajax({
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'Country'
                },
                dataType: 'json'
            })).then(function (template, countries) {
            "use strict";
            modal.writeBody(template[0]);
            let select = $(selector).find('#country');
            if (typeof (select[0].selectize) !== 'undefined') select[0].selectize.destroy();
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: countries[0]
            });

            modal.setOkEvent(function () {
                "use strict";
                let data = {};
                let element = $(selector);
                data.id = element.find('#id').val();
                data.name = element.find('#name').val();
                data.subject = element.find('#subject').val();
                data.address = element.find('#address').val();
                data.extra = element.find('#extra').val();
                data.city = element.find('#city').val();
                data.countryId = element.find('#country').val();
                data.postcode = element.find('#postcode').val();
                data.phone = element.find('#phone').val();
                data.cellphone = element.find('#cellphone').val();
                data.province = element.find('#province').val();
                data.iban = element.find('#iban').val();
                data.note = element.find('#note').val();
                modal.showLoader();
                modal.setOkEvent(function() {
                    modal.hide();
                });
                modal.setCloseEvent(function () {
                    $.refreshDataTable();
                });
                $.ajax({
                    method: 'post',
                    url: '/blueseal/xhr/AddressBookController',
                    data: data
                }).done(function () {
                    modal.writeBody('Indirizzo inserito');

                });
                return data;
            });
        });

    });


});