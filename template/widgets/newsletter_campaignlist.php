<div class="container-fluid container-fixed-lg bg-white">
    <div class="row">
        <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
    </div>
</div>
<div class="container-fluid container-fixed-lg bg-white">
    <div class="panel panel-transparent">
        <div class="panel-body">
            <table class="table table-striped responsive" width="100%"
                   data-datatable-name="newsletter_campaign_list"
                   data-controller="NewsletterCampaignListAjaxController"
                   data-url="<?php echo $app->urlForBluesealXhr() ?>"
                   data-inner-setup="true"
                   data-length-menu-setup="100, 200, 500, 1000, 2000"
                   data-display-length="200">
                <thead>
                <tr>
                    <th data-slug="id"
                        data-searchable="true"
                        data-orderable="true"
                        class="center"
                        data-default-order="desc">Id
                    </th>
                    <th data-slug="name"
                        data-searchable="true"
                        data-orderable="true"
                        class="center">Nome Campagna
                    </th>
                    <th data-slug="dateCampaignStart"
                        data-searchable="true"
                        data-orderable="true"
                        class="center">data Inizio Campagna
                    </th>
                    <th data-slug="dateCampaignFinish"
                        data-searchable="true"
                        data-orderable="true"
                        class="center">Data Fine Campagna
                    </th>
                    <th data-slug="events"
                        data-searchable="true"
                        data-orderable="true"
                        class="center">Eventi collegati
                    </th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>