window.buttonSetup = {
    tag:"a",
    icon:"fa-film",
    permission:"/admin/product/edit",
    event:"bs-product-preview-video4",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Visualizza Video 4 ",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-preview-video4', function () {

    let selectedRows = $('.table').DataTable().rows('.selected').data();

    if(selectedRows.length != 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare una riga alla volta"
        }).open();
        return false;
    }
   var video =selectedRows[0].video4;
    var poster=selectedRows[0].dummyPicture;
    var body='';
    if (video!=null){
        body='<video  width="450" height="600" poster="'+poster+'" controls autoplay>'+
            '<source src="'+video+'" type="video/mp4">';
    }else{
        body =`non Ci sono Video`;
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