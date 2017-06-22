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
                                    .replaceAll('{{id}}',res[i].id)
                                    .replaceAll('{{period}}',container.data('period')))
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
                if(res.orders === 0) {
                    color = 'alert-danger';
                } else {
                    cpo = res.cost / res.orders * 100;
                    if(cpo > 10) color = 'alert-danger';
                    else if(cpo > 5) color = 'alert-warning';

                    crb = res.visits / res.orders * 100;
                }
                const progressPercent = res.elapsed.toFixed(2) + '%';
                if(redraw) {
                    var container = template
                        .replaceAll('{{title}}',div.data('title'))
                        .replaceAll('{{visite}}',res.visits)
                        .replaceAll('{{costo}}',res.cost)
                        .replaceAll('{{ordini}}',res.orders)
                        .replaceAll('{{incasso}}',res.ordersValue)
                        .replaceAll('{{elapsed}}',res.elapsed.toFixed(2) + '%')
                        .replaceAll('{{cpo}}',cpo)
                        .replaceAll('{{crb}}',crb)
                        .replaceAll('{{campaignName}}',res.campaignName)
                    ;
                    div.html(container);
                } else {
                    div.find('#visits').html(res.visits);
                    div.find('#cost').html(res.cost);
                    div.find('#orders').html(res.orders);
                    div.find('#ordersValue').html(res.ordersValue);
                    div.find('#cpo').html(res.cost);
                    div.find('#crb').html(res.cost);
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

})(jQuery);