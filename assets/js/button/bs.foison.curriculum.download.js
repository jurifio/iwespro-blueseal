window.buttonSetup = {
    tag:"a",
    icon:"fa-download",
    permission:"worker",
    event:"bs-download-foison-curriculum",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Scarica Curriculum",
    placement:"bottom"
};



$(document).on('bs-download-foison-curriculum', function () {


    let val = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

    let foisonId = null;
    let selectedRows = null;

    if(val == 'foison'){

        selectedRows = $('.table').DataTable().rows('.selected').data();

        if(selectedRows.length > 1 || selectedRows.length == 0){
            new Alert({
                type: "warning",
                message: "Seleziona solo un foison"
            }).open();
            return false;
        }

        foisonId = selectedRows[0].Row_foison_id;
    } else {
        foisonId = val;
    }

    window.open(`/blueseal/download-contracts/${foisonId}?type=Foison`, '_blank');
    
});
