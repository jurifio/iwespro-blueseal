(function($) {

    var templateRequest = $.getTemplate('marketplaceCampaignMonitorTemplate.html').promise();
    const monitorsContainerSelector = '#monitorsContainer';
    const containerSelector = '.marketplace-monit';
    var drawnElements = [];
    templateRequest.then(function(template) {
        setupCards(template)
        setInterval(setupCards,60000,template);

        $(document).on('click','.portlet-refresh',function () {
            drawCard(template, $(this).closest(containerSelector), true);
        });
    });

    const containerMock = '<div class="col-md-4">' +
        '<div class="marketplace-monit" data-id="{{id}}" data-title="Oggi" data-period="{{period}}"></div>' +
        '</div>';

    const setupCards = function(template) {
        "use strict";
        var container = $(monitorsContainerSelector);
        Pace.ignore(function () {
            $.ajax({
                url: "/blueseal/xhr/MarketplaceActiveCampaignMonitorDataProvider",
                data: {
                    period: container.data('period')
                },
                dataType: 'JSON'
            }).done(function (res) {
                for(var i in res) {
                    if(res.hasOwnProperty(i)) {
                        if(!drawnElements.includes(res[i].id)) {
                            var div = container.append(
                                $(
                                    containerMock
                                    .monkeyReplaceAll('{{id}}',res[i].id)
                                    .monkeyReplaceAll('{{period}}',container.data('period')))
                            );
                            drawnElements.push(res[i].id);
                            drawCard(template, div.find('.marketplace-monit:last'), true);
                        }
                    }
                }
            });
        });
    };



    const drawCard = function(template,div, redraw) {
        if(typeof redraw === 'undefined') redraw = false;
        div = $(div);
        if(redraw) div.html('<img src="/assets/img/ajax-loader.gif" />');
        "use strict";
        Pace.ignore(function () {
            $.ajax({
                url: "/blueseal/xhr/MarketplaceCampaignMonitorDataProvider",
                data: {
                    campaignId: div.data('id'),
                    period: div.data('period')
                },
                dataType: 'JSON'
            }).done(function(res) {
                var color = '';
                var cpo = '∞';
                var crb = '∞';
                if(res.ordersValue === 0) {
                    color = 'alert-danger';
                } else {
                    cpo = (res.cost / res.ordersValue * 100) + '%';
                    if(cpo > 10) color = 'alert-danger';
                    else if(cpo > 5) color = 'alert-warning';

                    crb = (res.orders /  res.visits * 100).toFixed(2) + '%';
                }
                const progressPercent = res.elapsed.toFixed(2) + '%';
                if(redraw) {
                    var container = template
                        .monkeyReplaceAll('{{title}}',div.data('title'))
                        .monkeyReplaceAll('{{visite}}',res.visits)
                        .monkeyReplaceAll('{{costo}}',res.cost)
                        .monkeyReplaceAll('{{ordini}}',res.orders)
                        .monkeyReplaceAll('{{incasso}}',res.ordersValue)
                        .monkeyReplaceAll('{{elapsed}}',res.elapsed.toFixed(2) + '%')
                        .monkeyReplaceAll('{{cpo}}',cpo)
                        .monkeyReplaceAll('{{crb}}',crb)
                        .monkeyReplaceAll('{{campaignName}}',res.campaignName)
                    ;
                    div.html(container);
                    div.data('campaignName',res.campaignName);
                } else {
                    div.find('#visits').html(res.visits);
                    div.find('#cost').html('&euro; ' +res.cost);
                    div.find('#orders').html(res.orders);
                    div.find('#ordersValue').html('&euro; '+res.ordersValue);
                    div.find('#cpo').html(cpo);
                    div.find('#crb').html(crb);
                    div.find('#progress-bar').data('percentage',progressPercent);
                    div.find('.portlet-refresh div').html("");
                }

                div.find('div.widget-9').addClass(color);

                var progress = new ProgressBar.Circle(div.find('.portlet-refresh div')[ 0 ], {
                    color: '#22bdcf',
                    duration: 60000,
                    strokeWidth:30
                });

                progress.animate(1,function() {
                    drawCard(template,div);
                });
            }).fail(function() {
                setTimeout(drawCard, 8000, template,div);
            });
        });
    }

    $.getTemplate('marketplaceCampaignCategoryMonitorTemplate.html').done(function(template) {
        "use strict";
        $(document).on('click','.show-categories-detail',function () {
            var div = $(this).closest(containerSelector);

            var modal = new $.bsModal(
                'Dettaglio Catgorie: '+ div.data('campaignName'),
                { body: '' }
            );
            modal.okButton.hide();
            modal.cancelButton.hide();
            modal.showLoader();
            modal.addClass('modal-wide');
            modal.addClass('modal-high');

            Pace.ignore(function() {
                $.ajax({
                    url: "/blueseal/xhr/MarketplaceCampaignCategoryMonitorDataProvider",
                    data: {
                        campaignId: div.data('id'),
                        period: div.data('period')
                    },
                    dataType: 'JSON'
                }).done(function(res) {

                    modal.writeBody(template);
                    var table = $('.modal-body .widget-11 table');
                    var tableBody = table.find('tbody');
                    var rowTemplate = tableBody.find('tr')[0].outerHTML;

                    tableBody.html("");
                    for(var i in res) {
                        if(!res.hasOwnProperty(i)) continue;

                        var color = '';
                        var cpo = '∞';
                        var crb = '∞';
                        var cpoe = '∞';
                        var crbe = '∞';
                        if(res[i].ordersValue === 0) {
                            color = 'alert-danger';
                        } else {
                            cpo = (res[i].cost / res[i].ordersValue * 100) + '%';
                            if(cpo > 10) color = 'alert-danger';
                            else if(cpo > 5) color = 'alert-warning';

                            crb = (res[i].visits / res[i].orders * 100).toFixed() + '%';
                        }

                        if(res[i].exactOrdersValue === 0) {
                            color = 'alert-danger';
                        } else {
                            cpoe = (res[i].cost / res[i].exactOrdersValue * 100) + '%';
                            if(cpoe > 10) color = 'alert-danger';
                            else if(cpoe > 5) color = 'alert-warning';

                            crbe = (res[i].visits / res[i].exactOrders * 100).toFixed() + '%';
                        }


                        tableBody.append(
                            rowTemplate
                                .monkeyReplaceAll('{{category}}',res[i].categoryPath)
                                .monkeyReplaceAll('{{visits}}',res[i].visits)
                                .monkeyReplaceAll('{{cost}}',res[i].cost)
                                .monkeyReplaceAll('{{orders}}',res[i].orders)
                                .monkeyReplaceAll('{{ordersValue}}',res[i].ordersValue)
                                .monkeyReplaceAll('{{exactOrders}}',res[i].exactOrders)
                                .monkeyReplaceAll('{{exactOrdersValue}}',res[i].exactOrdersValue)
                                .monkeyReplaceAll('{{cpo}}',cpo)
                                .monkeyReplaceAll('{{cpoe}}',cpoe)
                                .monkeyReplaceAll('{{crb}}',crb)
                                .monkeyReplaceAll('{{crbe}}',crbe)
                        )
                    }
                })
            });
        });
    });
})(jQuery);