window.buttonSetup = {
    tag: "a",
    icon: "fa-line-chart",
    permission: "/admin/product/edit&&allShops",
    event: "bs-compare-newsletterRedemption-open",
    class: "btn btn-default",
    rel: "tooltip",
    title: "Opened",
    placement: "bottom",
    toggle: "modal"
};

$(document).on('bs-compare-newsletterRedemption-open', function () {

    $('#s-aperte').removeClass("hide");

});