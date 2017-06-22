(function($) {

    var templateRequest = $.getTemplate('marketplaceCampaignMonitorTemplate.html').promise();
    const containerSelector = '.marketplace-monit';

    templateRequest.then(function(template) {
        "use strict";
        var i = 0;
        $(containerSelector).each(function() {
            var that = $(this);
            drawCard(template,that,true);
        });

        $(document).on('click','.portlet-refresh',function () {
            drawCard(template, $(this).closest(containerSelector), true);
        });
    });



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
                setTimeout(drawCard, 3000, template,div);
            });
        });
    }

})(jQuery);