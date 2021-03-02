

$(document).on('bs.shop.save', function () {
    let method;
    let data = {};
    data.id = $('#shop_id').val();
    data.title = $('#shop_title').val();
    data.owner = $('#shop_owner').val();
    data.referrerEmails = $('#shop_referrerEmails').val();
    data.eloyApiKey = $('#shop_eloyApiKey').val();
    data.secret = $('#shop_secret').val();
    data.currentSeasonMultiplier = $('#shop_currentSeasonMultiplier').val();
    data.pastSeasonMultiplier = $('#shop_pastSeasonMultiplier').val();
    data.saleMultiplier = $('#shop_saleMultiplier').val();
    data.minReleasedProducts = $('#shop_minReleasedProducts').val();
    data.dbHost=$('#shop_dbHost').val();
    data.dbUsername=$('#shop_dbUsername').val();
    data.dbPassword=$('#shop_dbPassword').val();
    data.dbName=$('#shop_dbName').val();
    data.logo=$('#shop_logo').val();
    data.logoThankYou=$('#shop_logoThankYou').val();
    data.paralellFee=$('#shop_paralellFee').val();
    data.feeParallelOrder=$('#shop_feeParallelOrder').val();
    data.billingParallelId=$('#shop_BillingParallelId').val();
    data.hasMarketplace=$('#shop_hasMarketplace').val();
    data.hasCoupon=$('#shop_hasCoupon').val();
    data.hasEcommerce=$('#shop_hasEcommerce').val();
    data.receipt=$('#shop_receipt').val();
    data.invoiceUe=$('#shop_invoiceUe').val();
    data.invoiceExtraUe=$('#shop_invoiceExtraUe').val();
    data.invoiceParalUe=$('#shop_invoiceParalUe').val();
    data.invoiceParalExtraUe=$('#shop_invoiceParalExtraUe').val();
    data.siteInvoiceChar=$('#shop_siteInvoiceChar').val();
    data.urlSite=$('#shop_urlSite').val();
    data.analyticsId=$('#analyticsId').val();
    data.emailShop=$('#shop_emailShop').val();
    data.amministrativeEmails=$('#shop_amministrativeEmails').val();
    data.billingEmails=$('#shop_billingEmails').val();
    data.billingContact=$('#shop_billingContact').val();
    data.importer=$('#shop_importer').val();
    data.couponType=$('#shop_couponType').val();
    data.config = {};
    data.config.refusalRate = $('#shop_config_refusalRate').val();
    data.config.refusalRateLastMonth = $('#shop_config_refusalRate_lastMonth').val();
    data.config.reactionRate = $('#shop_config_reactionRate').val();
    data.config.reactionRateLastMonth = $('#shop_config_reshop_config_reactionRate_lastMonthfusalRate').val();
    data.config.accountStatus = $('#shop_config_accountStatus').val();
    data.config.accountType = $('#shop_config_accountType').val();
    data.config.photoCost = $('#shop_config_photoCost').val();
    data.config.shootingTransportCost = $('#shop_config_shootingTransportCost').val();
    data.config.orderTransportCost = $('#shop_config_orderTransportCost').val();

    data.billingAddressBook = readShipment('#billingAddress');
    data.shippingAddresses = [];
    $.each($('#shippingAddresses .shippingAddress'), function (k, v) {
        data.shippingAddresses.push(readShipmentNotIban(v));
    });

    if (data.id.length) {
        method = "PUT";
    } else {
        method = "POST";
    }

    $.ajax({
        method: method,
        url: "/blueseal/xhr/ShopManage",
        data: {
            shop: data
        }
    }).done(function () {
        new Alert({
            type: "success",
            message: "Modifiche Salvate"
        }).open();
    }).fail(function (e) {
        console.log(e);
        new Alert({
            type: "danger",
            message: "Impossibile Salvare"
        }).open();
    });
});
$(document).on('bs.shop.add.user', function () {
    let url='/blueseal/utenti/'
    window.open(url,'_blank');
    /*let shopId = selectedRows[0].shopId;

    var modal = new $.bsModal('Conferma Ordine', {
        body: '<label for="userId">Seleziona l\'indirizzo di ritiro</label><br />' +
            '<select id="userId" name="userId" class="full-width selectize"></select><br />'

    });

    let addressSelect = $('select[name=\"userId\"]');


    Pace.ignore(function () {
        $.ajax({
            url: '/blueseal/xhr/SelectUserAjaxController',
            method: 'get',
            dataType: 'json'
        }).done(function (res) {
            console.log(res);
            addressSelect.selectize({
                valueField: 'id',
                labelField: 'name',
                searchField: ['name'],
                options: res,
                render: {
                    item: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.shopTitle) + '</span> - ' +
                            '<span class="caption">' + escape(item.address + ' ' + item.city) + '</span>' +
                            '</div>'
                    },
                    option: function (item, escape) {
                        return '<div>' +
                            '<span class="label">' + escape(item.shopTitle) + '</span>  - ' +
                            '<span class="caption">' + escape(item.address + ' ' + item.city) + '</span>' +
                            '</div>'
                    }
                }
            });
        });



*/
});




(function ($) {
var couponType=0;
    let params = $.decodeGetStringFromUrl(window.location.href);
    if (typeof params.id != 'undefined') {
        $.ajax({
            url: "/blueseal/xhr/ShopManage",
            data: {
                id: params.id
            },
            dataType: "json"
        }).done(function (res) {
            $('#shop_id').val(res.id);
            $('#shop_title').val(res.title);
            $('#shop_owner').val(res.owner);
            $('#shop_referrerEmails').val(res.referrerEmails);
            $('#shop_eloyApiKey').val(res.eloyApiKey);
            $('#shop_secret').val(res.secret);
            $('#shop_iban').val(res.iban);
            $('#shop_currentSeasonMultiplier').val(res.currentSeasonMultiplier);
            $('#shop_pastSeasonMultiplier').val(res.pastSeasonMultiplier);
            $('#shop_saleMultiplier').val(res.saleMultiplier);
            $('#shop_minReleasedProducts').val(res.minReleasedProducts);
            $('#shop_config_refusalRate').val(res.config.refusalRate);
            $('#shop_config_refusalRate_lastMonth').val(res.config.refusalRateLastMonth);
            $('#shop_config_reactionRate').val(res.config.reactionRate);
            $('#shop_config_reactionRate_lastMonth').val(res.config.reactionRateLastMonth);
            $('#shop_config_accountStatus').val(res.config.accountStatus);
            $('#shop_config_accountType').val(res.config.accountType);
            $('#shop_config_photoCost').val(res.config.photoCost);
            $('#shop_config_shootingTransportCost').val(res.config.shootingTransportCost);
            $('#shop_config_orderTransportCost').val(res.config.orderTransportCost);
            $('#shop_dbHost').val(res.dbHost);
            $('#shop_dbUsername').val(res.dbUsername);
            $('#shop_dbPassword').val(res.dbPassword);
            $('#shop_dbName').val(res.dbName);
            $('#shop_logo').val(res.logo);
            $('#shop_logoThankYou').val(res.logoThankYou);
            $('#shop_paralellFee').val(res.paralellFee);
            $('#shop_parallelFeeOrder').val(res.feeParallelOrder);
            $('#shop_billingParallelId').val(res.billingParallelId);
            $('#shop_hasMarketplace').val(res.hasMarketplace);
            $('#shop_hasEcommerce').val(res.hasEcommerce);
            $('#shop_hasCoupon').val(res.hasCoupon);
            $('#shop_receipt').val(res.receipt);
            $('#shop_invoiceUe').val(res.invoiceUe);
            $('#shop_invoiceExtraUe').val(res.invoiceExtraUe);
            $('#shop_invoiceParalUe').val(res.invoiceParalUe);
            $('#shop_invoiceParalExtraUe').val(res.invoiceParalExtraUe);
            $('#shop_siteInvoiceChar').val(res.siteInvoiceChar);
            $('#shop_urlSite').val(res.urlSite);
            $('#shop_analyticsId').val(res.analyticsId);
            $('#shop_emailShop').val(res.emailShop);
            $('#shop_amministrativeEmails').val(res.amministrativeEmails);
            $('#shop_billingEmails').val(res.billingEmails);
            $('#shop_billingContact').val(res.billingContact);
            $('#shop_importer').val(res.importer);
            $('#shop_couponType').val(res.couponType);
            if(res.couponType>0){
                $('#divModifyCouponType').removeClass('hide');
                $('#divModifyCouponType').addClass('show');

            }else{
                $('#divAddCouponType').removeClass('hide');
                $('#divAddCouponType').addClass('show');

            }
            couponType=res.couponType;




            checkPermission('allShops')
                .done(function () {
                    $.each($('input[disabled]'), function (k, v) {
                        $(v).prop("disabled", false)
                            .closest('div')
                            .removeClass('disabled')
                            .prop("disabled", false);
                    });
                }).fail(function () {
                "use strict";

            });

            $('#shop_referrerEmails').selectize({
                delimiter: ';',
                create: function (input) {
                    return {
                        value: input,
                        text: input
                    }
                }
            });

            createGraphs(res);

            appendShipment(res.billingAddressBook, '#billingAddress');
            res.shippingAddressBooks.forEach(function (addressData) {
                appendShipmentNotIban(addressData, '#shippingAddresses');
            });
            appendShipmentNotIban({}, '#shippingAddresses');
            $('#rowAggregator').empty();
            var aggregator=res.aggregatorHasShop;
            var bodyres;
            var isActive;
            bodyres = bodyres + '<table id="myTable"><tr><th style="width:20%;">id</th><th style="width:20%;">name</th><th style="width:20%;">immagine</th><th style="width:20%;">Stato</th><th style="width:20%;">Operazioni</th></tr>';
            $.each(aggregator, function (k, v) {
                if(v.isActive==1){
                    isActive='si';
                }else{
                    isActive='no';
                }
                bodyres = bodyres + '<tr><td style="width:20%;">' + v.id + '</td><td style="width:20%;">' + v.name + '</td><td style="width:20%;"><img width="80" src="' + v.imgAggregator + '"/></td><td style="width:20%;">' + isActive + '</td><td><button class="success" id="modifyRowAggregatorButton' + v.id + '" onclick="modifyRowAggregatorEdit(' + v.id + ')" type="button"><span class="fa fa-pencil">Modifica</span></button></td></tr>';
                // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
            });
            bodyres = bodyres + '</table>';
            $('#rowAggregator').append(bodyres);
            $('#rowMarketplace').empty();
            var marketplace=res.marketplaceHasShop;
            var bodyresi;
            let isActiveMarketplace;
            bodyresi = bodyresi + '<table id="myTable"><tr><th style="width:25%;">id</th><th style="width:25%;">Marketplace</th><th style="width:25%;">Stato</th><th style="width:25%;">Operazioni</th></tr>';
            $.each(marketplace, function (m, n) {
                if(n.isActive==1){
                    isActiveMarketplace='si';
                }else{
                    isActiveMarketplace='no';
                }
                bodyresi = bodyresi + '<tr><td style="width:25%;">' + n.id + '</td><td style="width:25%;">' + n.name + '</td><td style="width:25%;">' + isActiveMarketplace + '</td><td style="width:25%;">' + isActive + '</td><td><button class="success" id="modifyRowMarketplaceButton' + n.id + '" onclick="modifyRowMarketplaceEdit(' + n.id + ')" type="button"><span class="fa fa-pencil">Modifica</span></button></td></tr>';
                // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
            });
            bodyresi = bodyresi + '</table>';
            $('#rowMarketplace').append(bodyresi);
            $('#rowCampaign').empty();
            var campaign=res.campaign;
            var bodyresic;
            let isActiveCampaign;
            bodyresic = bodyresic + '<table id="myTable"><tr><th style="width:25%;">id</th><th style="width:25%;">Campagna</th><th style="width:25%;">Codice Monitoraggio</th><th style="width:25%;">Stato</th></tr>';
            $.each(campaign, function (o, p) {
                if(p.isActive==1){
                    isActiveCampaign='si';
                }else{
                    isActiveCampaign='no';
                }
                bodyresic = bodyresic + '<tr><td style="width:25%;">' + p.id + '</td><td style="width:25%;">' + p.name + '</td><td style="width:25%;">' + p.code + '</td><td style="width:25%;">' + isActiveCampaign + '</td></tr>';
                // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
            });
            bodyresic = bodyresic + '</table>';
            $('#rowCampaign').append(bodyresic);
            $('#rowCouponEvent').empty();
            var couponEvent=res.couponEvent;
            var bodyresicoupon;
            bodyresicoupon = bodyresicoupon + '<table id="myTable"><tr><th style="width:20%;">id</th><th style="width:20%;">Coupon</th><th style="width:20%;">Descrizione</th><th style="width:25%;">Validità</th><th style="width:25%;">Operazioni</th></tr>';
            $.each(couponEvent, function (r, s) {

                bodyresicoupon = bodyresicoupon + '<tr><td style="width:20%;">' + s.id + '</td><td style="width:20%;">' + s.name + '</td><td style="width:20%;">' + s.description + '</td><td style="width:20%;">valido da ' + s.startDate + ' a ' + s.endDate + '</td><td><button class="success" id="modifyRowCouponEventButton' + s.id + '" onclick="modifyRowCouponEvenEdit(' + s.id + ')" type="button"><span class="fa fa-pencil">Modifica</span></button></td></tr>';
                // $('#rawBrands').append('<option value="'+v.id+'-'+v.shopIdOrigin+'">'+v.brandName+'-'+v.shopName+'</option>');
            });
            bodyresicoupon = bodyresicoupon + '</table>';
            $('#rowCouponEvent').append(bodyresicoupon);

        });
    }
})(jQuery);


/**
 * Generate new key and insert into input value
 */

function addCoupon(){
    let url='/blueseal/tipocoupon/aggiungi'
    window.open(url,'_blank');
}
function modifyCoupon(){
    let couponType=$('#shop_couponType').val();
    let url='/blueseal/tipocoupon/modifica/'+couponType
    window.open(url,'_blank');
}

function createGraphs(shop) {
    "use strict";
    let chartContainer = $('#statisticGraphics');

    /*These lines are all chart setup.  Pick and choose which chart features you want to utilize. */
    /* Done setting the chart up? Time to render it!*/
    let productDatas = [];
    let productMinimumData = [];
    let orderDatas = [];
    let orderValueDatas = [];
    let index = 0;
    for (let i in shop.productStatistics) {
        let point = shop.productStatistics[i];
        let xpoint = (new Date(point.date).getTime());
        orderDatas[index] = {x: xpoint, y: 0};
        productMinimumData[index] = {x: xpoint, y: shop.minReleasedProducts};
        orderValueDatas[index] = {x: xpoint, y: 0};
        for (let k in shop.orderStatistics) {
            if (shop.orderStatistics[k].date == point.date) {
                orderDatas[index] = {x: xpoint, y: shop.orderStatistics[k].orders};
                orderValueDatas[index] = {x: xpoint, y: shop.orderStatistics[k].ordersValue};
                break;
            }
        }
        productDatas[index] = {x: xpoint, y: point.products};
        index++;
    }

    let productGraphData = [
        {
            values: productDatas,
            key: 'Prodotti Attivi',
            color: '#ff7f0e'
        },{
            values: productMinimumData,
            key: 'Prodotti Minimi Attivi',
            color: 'red'
        }

    ];   //You need data...

    let productChart = nv.models.lineChart();

    productChart
        .margin({top: 30, right: 60, bottom: 50, left: 70})  //Adjust chart margins to give the x-axis some breathing room.
        .options({
            duration: 300,
            useInteractiveGuideline: true
        })
        .color(d3.scale.category10().range())
    ;

    productChart.xAxis   //Chart x-axis settings
        .showMaxMin(false)
        .tickFormat(function (d, k) {
            //let dx = productGraphData[0].values[d] && productGraphData[0].values[d].x || '0';
            return d3.time.format('%Y-%m-%d')(new Date(d));
        })
        .showMaxMin(true)
    ;

    productChart.yAxis     //Chart y-axis settings
        .tickFormat(d3.format(',f'));
    //.tickFormat(d3.format(',.2r'));

    d3.select('#productGraph') //Select the <svg> element you want to render the chart in.
        .datum(productGraphData)         //Populate the <svg> element with chart data...
        .call(productChart);          //Finally, render the chart!

    //Update the chart when window resizes.
    nv.utils.windowResize(productChart.update);
    nv.addGraph(productChart);

    /** ------------------- */

    let orderGraphData = [
        {
            values: orderDatas,
            key: 'N° Ordini',
            color: 'yellow'
        }, {
            values: orderValueDatas,
            bar: true,
            key: 'Incasso',
            color: 'green'
        }];

    let orderChart = nv.models.linePlusBarChart();

    orderChart
        .margin({top: 30, right: 60, bottom: 50, left: 70})  //Adjust chart margins to give the x-axis some breathing room.
        .showLegend(true)       //Show the legend, allowing users to turn on/off line series.
        .color(d3.scale.category10().range())
    ;

    orderChart.xAxis   //Chart x-axis settings
        .tickFormat(function (d, k) {
            //let dx = orderGraphData[0].values[d] && orderGraphData[0].values[d].x || '0';
            return d3.time.format('%Y-%m-%d')(new Date(d));
        })
    ;

    orderChart.y1Axis     //Chart y-axis settings
        .tickFormat(function (d) {
            return '€' + d3.format(',f')(d)
        });
    //.tickFormat(d3.format(',.2r'));

    orderChart.y2Axis     //Chart y-axis settings
        .tickFormat(d3.format(',f'));
    //.tickFormat(d3.format(',.2r'));

    orderChart.bars.forceY([0]);
    orderChart.focusEnable(false);
    d3.select('#orderGraph') //Select the <svg> element you want to render the chart in.
        .datum(orderGraphData)         //Populate the <svg> element with chart data...
        .transition().duration(500)
        .call(orderChart);          //Finally, render the chart!

    //Update the chart when window resizes.
    nv.utils.windowResize(orderChart.update);

    nv.addGraph(orderChart);
}

function readShipment(containerSelector) {
    "use strict";
    let data = {};
    let element = $(containerSelector);
    data.id = element.find('#id').val();
    data.name = element.find('#name').val();
    data.subject = element.find('#subject').val();
    data.address = element.find('#address').val();
    data.extra = element.find('#extra').val();
    data.city = element.find('#city').val();
    data.countryId = element.find('#country').val();
    data.postcode = element.find('#postcode').val();
    data.phone = element.find('#phone').val();
    data.cellphone = element.find('#cellphone').val();
    data.province = element.find('#province').val();
    data.iban = element.find('#iban').val();
    return data;
}


function appendShipment(data, containerSelector) {
    let container = $(containerSelector);
    $.getTemplate('addressBookFormMock').done(function (res) {
        let element = $(res);
        Pace.ignore(function () {
            $.get({
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'Country'
                },
                dataType: 'json'
            }).done(function (res2) {
                let select = element.find('#country');
                if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                select.selectize({
                    valueField: 'id',
                    labelField: 'name',
                    searchField: ['name'],
                    options: res2,
                });
                if (data != null && Object.keys(data).length > 0) {
                    element.find('#id').val(data.id);
                    element.find('#name').val(data.name);
                    element.find('#subject').val(data.subject);
                    element.find('#address').val(data.address);
                    element.find('#extra').val(data.extra);
                    element.find('#city').val(data.city);
                    select[0].selectize.setValue(data.countryId);
                    element.find('#postcode').val(data.postcode);
                    element.find('#phone').val(data.phone);
                    element.find('#cellphone').val(data.cellphone);
                    element.find('#province').val(data.province);
                    element.find('#iban').val(data.iban);
                }
                container.append(element);
            });
        });
    });
}
function readShipmentNotIban(containerSelector) {
    "use strict";
    let data = {};
    let element = $(containerSelector);
    data.id = element.find('#id').val();
    data.name = element.find('#name').val();
    data.subject = element.find('#subject').val();
    data.address = element.find('#address').val();
    data.extra = element.find('#extra').val();
    data.city = element.find('#city').val();
    data.countryId = element.find('#country').val();
    data.postcode = element.find('#postcode').val();
    data.phone = element.find('#phone').val();
    data.cellphone = element.find('#cellphone').val();
    data.province = element.find('#province').val();
    //  data.iban = element.find('#iban').val();
    return data;
}
function appendShipmentNotIban(data, containerSelector) {
    let container = $(containerSelector);
    $.getTemplate('addressBookFormMock').done(function (res) {
        let element = $(res);
        Pace.ignore(function () {
            $.get({
                url: '/blueseal/xhr/GetTableContent',
                data: {
                    table: 'Country'
                },
                dataType: 'json'
            }).done(function (res2) {
                let select = element.find('#country');
                if (typeof (select[0].selectize) != 'undefined') select[0].selectize.destroy();
                select.selectize({
                    valueField: 'id',
                    labelField: 'name',
                    searchField: ['name'],
                    options: res2,
                });
                if (data != null && Object.keys(data).length > 0) {
                    element.find('#id').val(data.id);
                    element.find('#name').val(data.name);
                    element.find('#subject').val(data.subject);
                    element.find('#address').val(data.address);
                    element.find('#extra').val(data.extra);
                    element.find('#city').val(data.city);
                    select[0].selectize.setValue(data.countryId);
                    element.find('#postcode').val(data.postcode);
                    element.find('#phone').val(data.phone);
                    element.find('#cellphone').val(data.cellphone);
                    element.find('#province').val(data.province);
                }
                container.append(element);
            });
        });
    });
}

$(document).on('click', '#keygen', function(e){
    e.preventDefault();
    let k = generateUUID();
    $( '#shop_eloyApiKey' ).val(k);
});

function generateUUID()
{
    let d = new Date().getTime();

    if( window.performance && typeof window.performance.now === "function" )
    {
        d += performance.now();
    }

    let uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c)
    {
        let r = (d + Math.random()*16)%16 | 0;
        d = Math.floor(d/16);
        return (c=='x' ? r : (r&0x3|0x8)).toString(16);
    });

    return uuid;
}

document.getElementById('modifyClient').style.display = "block";

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
function modifyRowAggregatorEdit(aggregatorId){
    let url='/blueseal/aggregatori/account-shop/modifica/'+aggregatorId
    window.open(url,'_blank');

}
function modifyRowMarketplaceEdit(marketplaceId){
    let url='/blueseal/marketplace/account-shop/modifica/'+marketplaceId
    window.open(url,'_blank');

}
function modifyRowCouponEventEdit(couponEventId){
    let url='/blueseal/eventocoupon/modifica/'+couponEventId
    window.open(url,'_blank');

}