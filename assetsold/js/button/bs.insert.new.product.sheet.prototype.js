window.buttonSetup = {
    tag:"a",
    icon:"fa-plus-circle",
    permission:"/admin/product/edit&&allShops",
    event:"bs-new-product-sheet-prototype",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Aggiungi nuovo tipo scheda prodotto",
    placement:"bottom",
    toggle:"modal"
};


$(document).on('bs-new-product-sheet-prototype', function () {

    let i = 0;
    let index = [];
    let bsModal = new $.bsModal('Aggiungi nuovo tipo scheda prodotto', {
        body: `
        <div style="margin-bottom: 20px">
             <p>Inserisci una scheda prodotto</p>
             <input type="text" id="p-prototype">
        </div>
        <div id="details">
            <div style="margin-bottom: 10px" id="addRow">
            </div>
            <button id="addDetail">Aggiungi dettaglio</button>
        </div>
        `
    });

    $('#addDetail')
        .on('click', function () {
            i++;
            index.push(i);

            $('#addRow')
                .append(
                    `<div id="formDetail-${i}" style="margin-bottom: 6px">
                        <label for="det-${i}">Nome dettaglio</label>
                        <input type="text" id="det-${i}" name="det-${i}">
                        <input placeholder="Priorità" type="text" id="pr-${i}">
                        <button id="removeDetail" data-id="${i}">Rimuovi dettaglio</button>
                    </div>`
                );
    });

    $(document)
        .on('click','#removeDetail',function() {
            let id =  $(this).attr('data-id');
            $(`#formDetail-${id}`).remove();

            let z = index.indexOf(parseInt(id));
            if(z !== -1) {
                index.splice(z, 1);
            }
        });

    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {

        let psp = [];
        $.each(index, function (k, v) {

            psp.push({
                name: $(`#det-${v}`).val(),
                pr: $(`#pr-${v}`).val()
            });

        });

        const data = {
            pName: $('#p-prototype').val(),
            psp: psp
        };
        $.ajax({
            method: 'post',
            url: '/blueseal/xhr/ProductSheetPrototypeManage',
            data: data
        }).done(function (res) {

            bsModal.writeBody(res);

        }).fail(function (res) {
            bsModal.writeBody('Errore grave');
        }).always(function (res) {
            bsModal.setOkEvent(function () {
                bsModal.hide();
                window.location.reload();
            });
            bsModal.showOkBtn();
        });
    });

});