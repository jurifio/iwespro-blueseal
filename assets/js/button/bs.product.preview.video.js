window.buttonSetup = {
    tag:"a",
    icon:"fa-film",
    permission:"/admin/product/edit",
    event:"bs-product-preview-video",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Gestisci etichette",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-preview-video', function () {

    let selectedRows = $('.table').DataTable().rows('.selected').data();

    if(selectedRows.length != 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare una riga alla volta"
        }).open();
        return false;
    }
   var video =selectedRows[0].video;
    var body='';
    if (video!=null){
        body=`<video  width="450" height="600"  controls autoplay>
                        <source src="`+video+`" type="video/mp4">
                    </video>`
    }else{
        body `non Ci sono Video`;
    }

    let bsModal = new $.bsModal('Preview Video', {
        body: body
    });


    bsModal.addClass('modal-wide');
    bsModal.addClass('modal-high');
    bsModal.showCancelBtn();
    bsModal.setOkEvent(function () {
        bsModal.hide();
    });
});