$(document).ready(function () {
    Pace.ignore(function () {
        var container = $('select[name="userId"]').parent();
        var content = container.html();
        container.html('<img src="/assets/img/ajax-loader.gif">');
        $.ajax({
            url: "/blueseal/xhr/UserData",
            dataType: "json"
        }).done(function (res) {
            container.html(content);
            select = $('select[name="userId"]');
            select.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['email', 'name', 'surname'],
                options: res,
                render: {
                    item: function (item, escape) {
                        var label = item.name + ' ' + item.surname || item.email;
                        var caption = (item.name + item.surname).length > 0 ? item.email : null;
                        return '<div>' +
                            '<span class="label">' + escape(label) + '</span>' +
                            (caption ? ' - <span class="caption">' + escape(caption) + '</span>' : '') +
                            '</div>'
                    },
                    option: function (item, escape) {
                        var label = item.name + ' ' + item.surname || item.email;
                        var caption = (item.name + item.surname).length > 0 ? item.email : null;
                        return '<div>' +
                            '<span class="label">' + escape(label) + '</span>' +
                            (caption ? ' - <span class="caption">' + escape(caption) + '</span>' : '') +
                            '</div>'
                    }
                }
            });
        });
    });


    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Country'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#countryId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'TypeFriend'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#typeFriendId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: 'name',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'Currency'

        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#currencyId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'code',
            searchField: 'code',
            options: res2,
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'BankRegistry'
        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#bankRegistryId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: ['name', 'location', 'abi', 'cab'],
            options: res2,
            render: {
                item: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.name) + escape(item.location) + '</span> - ' +
                        '<span class="caption">abi:' + escape(item.abi + ' cab:' + item.cab) + '</span>' +
                        '</div>'
                },
                option: function (item, escape) {
                    return '<div>' +
                        '<span class="label">' + escape(item.name) + escape(item.location) + '</span> - ' +
                        '<span class="caption">abi:' + escape(item.abi + ' cab:' + item.cab) + '</span>' +
                        '</div>'
                }
            }
        });
    });

    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'BillRegistryTypeTaxes'
        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#billRegistryTypeTaxesId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'description',
            searchField: ['description'],
            options: res2
        });

    });
    $.ajax({
        method: 'GET',
        url: '/blueseal/xhr/GetTableContent',
        data: {
            table: 'BillRegistryTypePayment'
        },
        dataType: 'json'
    }).done(function (res2) {
        var select = $('#billRegistryTypePaymentId');
        if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
        select.selectize({
            valueField: 'id',
            labelField: 'name',
            searchField: ['name'],
            options: res2
        });

    });

    document.getElementById('insertClient').style.display = "block";
});


function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}


$('#typeFriendId').change(function () {
    let ratingValue = $('#typeFriendId').val();
    let bodyRating = '';
    switch (ratingValue) {
        case 5:
            bodyRating = `<span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-starchecked"></span>`;
            break;
        case 4:
            bodyRating = `<span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star "></span>`;
            break;
        case 3:
            bodyRating = `<span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star "></span>
                        <span class="fa fa-star "></span>`;
            break;
        case 2:
            bodyRating = `<span class="fa fa-star checked"></span>
                        <span class="fa fa-star checked"></span>
                        <span class="fa fa-star "></span>
                        <span class="fa fa-star "></span>
                        <span class="fa fa-star "></span>`;
            break;
        case 1:
            bodyRating = `<span class="fa fa-star checked"></span>
                        <span class="fa fa-star "></span>
                        <span class="fa fa-star "></span>
                        <span class="fa fa-star "></span>
                        <span class="fa fa-star "></span>`;
            break;
    }
    $("#rating").empty();
    $("#rating").append(bodyRating);
});

