;(function () {

    $(document).on('bs-foison-accept-subscribe', function () {
        let selectedRows = $('.table').DataTable().rows('.selected').data();

        if (selectedRows.length != 1) {
            new Alert({
                type: "warning",
                message: "Accetta le competenze di un Fason alla volta"
            }).open();
            return false;
        }

        let id = selectedRows[0].id;

        let bsModal = new $.bsModal('Accetta fason', {
            body:
                `
                <p>Spunta le competenze a cui associare il Fason</p>
                <div class="expertise"></div>
                `
        });

        $.ajax({
            method: 'get',
            url: '/blueseal/xhr/FoisonSubscribeRequestInterest',
            data: {
                id: id
            },
            dataType: 'json'
        }).done(function (cat) {
            $.each(cat, function (k, v) {
                $(`.expertise`).append(`<div>
                                           <input class="interest" type="checkbox" value="${v.id}">
                                           <label>${v.interestName}</label>         
                                        </div>`)
            })

        });

        let ids = [];
        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {
            $('.interest:checked').each(function(){
               ids.push($(this).val())
            });

            const data = {
                idsInterest: ids,
                idRequest: id
            };
            $.ajax({
                method: 'post',
                url: '/blueseal/xhr/FoisonSubscribeRequestInterest',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function (res) {
                bsModal.setOkEvent(function () {
                    $.refreshDataTable();
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        });

    });

})();