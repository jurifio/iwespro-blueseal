;(function () {

    $(document).on('bs.add.fields', function () {

        let bsModal = new $.bsModal('Seleziona i campi da aggiungere', {
            body: `
                   <div id="fieldsCheck">
                       <div>
                           <input id="stock" type="checkbox" value="stock">
                           <label for="stock">Taglie</label>
                       </div>
                   </div>                    
                   `
        });

        bsModal.showCancelBtn();
        bsModal.setOkEvent(function () {

            let url = '/blueseal/prodotti/catalog?';

            $('#fieldsCheck input:checked').each(function () {
                url = url + $(this).val() + '=1&'
            });

            window.open(url, '_blank');
        });
    });

})();