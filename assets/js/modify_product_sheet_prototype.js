;(function () {

    $(document).on('bs.modify.product.sheet', function () {

        let detToSave = [];
        let detToAdd = [];

        let added = $('.allDetailsAdded');

        $(".allDetails").each(function(){
            detToSave.push({
                id: $(this).attr('id'),
                name: $('#det', this).val(),
                pr: $('#pr', this).val()
            })
        });

        if(added.length !== 0){

            $(".allDetailsAdded").each(function(){
                if($('#det', this).val() != '' && $('#pr', this).val() != '') {
                    detToAdd.push({
                        name: $('#det', this).val(),
                        pr: $('#pr', this).val()
                    })
                }
            });
        }

        const data = {
                dataSave: detToSave,
                dataAdd: detToAdd,
                ps: window.location.href.substring(window.location.href.lastIndexOf('/') + 1)
            };

            $.ajax({
                method: 'put',
                url: '/blueseal/xhr/ProductSheetPrototypeManage',
                data: data
            }).done(function (res) {
                new Alert({
                    type: "success",
                    message: "Salvato"
                }).open();
                return false;
            }).fail(function (res) {
                new Alert({
                    type: "Error",
                    message: "Errore"
                }).open();
            })
        });

        $(document).on('click', '#addnew', function () {
            $('#fulldetails').append(`<div class="allDetailsAdded col-md-4" id="" style="display: flex; padding: 40px; justify-content: space-evenly; border: 1px solid grey">
                            <div>
                                <strong>Dettaglio</strong>
                                <input id="det" type="text" value="">
                            </div>

                            <div>
                                <strong>Priorità</strong>
                                <input id="pr" type="text" value="">
                            </div>
                        </div>`);
        });

    $(document).on('click', '.delete', function () {

        let bsModal = new $.bsModal('Aggiungi nuovo tipo scheda prodotto', {
            body: `
        <p>Sicuro di voler eliminare questo dettaglio?</p>
        `
        });
        let id = $(this).attr('id').split('-')[1];
        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {


            const data = {
                psDetId: id,
                ps: window.location.href.substring(window.location.href.lastIndexOf('/') + 1)
            };

            $.ajax({
                method: 'delete',
                url: '/blueseal/xhr/ProductSheetPrototypeManage',
                data: data
            }).done(function (res) {
                bsModal.writeBody(res);
            }).fail(function (res) {
                bsModal.writeBody('Errore grave');
            }).always(function () {
                bsModal.setOkEvent(function () {
                    window.location.reload();
                    bsModal.hide();
                });
                bsModal.showOkBtn();
            });
        });
    });

})();