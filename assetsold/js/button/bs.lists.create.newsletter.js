window.buttonSetup = {
    tag: "a",
    icon: "fa-file-o fa-plus",
    permission: "allShops||worker",
    class:"btn btn-default",
    rel:"tooltip",
    title:"Aggiungi un nuova newsletter",
    placement:"bottom",
    event: "add-new-newsletter"
};

$(document).on('add-new-newsletter', function () {

    let insertionId = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

    let url = `/blueseal/newsletter/aggiungi?insertionId=${insertionId}`;
    window.open(url, '_blank');
});