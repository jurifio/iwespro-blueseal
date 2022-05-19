window.buttonSetup = {
    tag:"a",
    icon:"fa-film",
    permission:"/admin/product/edit",
    event:"bs-product-preview-video3",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Visualizza Video 3 ",
    placement:"bottom",
    toggle:"modal"
};

$(document).on('bs-product-preview-video3', function () {

    let selectedRows = $('.table').DataTable().rows('.selected').data();

    if(selectedRows.length != 1){
        new Alert({
            type: "warning",
            message: "Devi selezionare una riga alla volta"
        }).open();
        return false;
    }
   var video =selectedRows[0].video3;
    var poster=selectedRows[0].dummyPicture;
    var body='';
    if (video!='no'){
        body='<video  width="450" height="600" poster="'+poster+'" controls autoplay>'+
            '<source src="'+video+'" type="video/mp4">';
    }else{
        body=`Il Video non è disponibile`;
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