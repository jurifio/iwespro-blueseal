(function($) {

    var templateRequest = $.getTemplate('marketplaceCampaignMonitorTemplate.html').promise();
    const containerSelector = '.marketplace-monit';

    templateRequest.then(function(template) {
        "use strict";
        $(containerSelector).each(function() {
            setInterval(drawCard(template,$(this)),15);
        });

        $(document).on('click','.portlet-refresh',function () {
            drawCard(template, $(this).closest(containerSelector));
        });
    });



    var drawCard = function(template,div) {
        div = $(div);
        $(div).html('<img src="/assets/img/ajax-loader.gif" />');
        "use strict";
        $.ajax({
            url: "/blueseal/xhr/MarketplaceCampaignMonitorDataProvider",
            data: {
                campaignId: $(div).data('id'),
                period: $(div).data('period')
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
        });
    }

})(jQuery);