(function($) {

    var templateRequest = $.getTemplate('marketplaceCampaignMonitorTemplate.html').promise();
    const containerSelector = '.marketplace-monit';

    templateRequest.then(function(template) {
        "use strict";
        var i = 0;
        $(containerSelector).each(function() {
            var that = $(this);
            drawCard(template,that);
        });

        $(document).on('click','.portlet-refresh',function () {
            drawCard(template, $(this).closest(containerSelector));
        });
    });



    const drawCard = function(template,div) {
        div = $(div);
        $(div).html('<img src="/assets/img/ajax-loader.gif" />');
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
                var container = template
                    .replaceAll('{{title}}',div.data('title'))
                    .replaceAll('{{costo}}',res.cost)
                    .replaceAll('{{visite}}',res.visits)
                    .replaceAll('{{ordini}}',res.orders)
                    .replaceAll('{{incasso}}',res.ordersValue)
                    .replaceAll('{{elapsed}}',res.elapsed.toFixed(2) + '%')
                    .replaceAll('{{cpo}}',res.cost / (res.orders === 0 ? 1 : res.orders))
                    .replaceAll('{{roi}}',res.orders / (res.ordersValue === 0 ? 1 : res.ordersValue))
                    .replaceAll('{{crb}}',res.visits / (res.orders === 0 ? 1 : res.orders))
                    .replaceAll('{{campaignName}}',res.campaignName)
                ;
                div.html(container);
                var progress = new ProgressBar.Circle(div.find('.portlet-refresh div')[ 0 ], {
                    color: '#22bdcf',
                    duration: 8000,
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