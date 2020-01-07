<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php" ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="campaignId">Seleziona la Campagna</label>
                                    '<select id="campaignId" name="campaignId" class="full-width selectize"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                </div>
            </div>
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="campaign_list"
                               data-controller="CampaignVisitListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500, 1000"
                               data-campagnId="<?php echo $campaignId ?>">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">id</th>
                                <th data-slug="campaignName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Codice Campagna</th>
                                <th data-slug="codeProduct"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Prodotto</th>
                                <th data-slug="defaultCpc"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Costo Cpc</th>
                                <th data-slug="shopName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">shopName</th>
                                <th data-slug="visits"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Visite</th>
                                <th data-slug="cost"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Costo</th>
                                <th data-slug="priceModifier"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Price Modifier</th>
                                <th data-slug="multiplierIs"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Moltiplicatore</th>
                                <th data-slug="maxCos"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Costo Massimo</th>
                                <th data-slug="sizeFill"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Giacenza Media</th>
                                <th data-slug="messageDelete"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Stato</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php" ?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Filtra">
        <bs-toolbar-button
            data-remote="btn.datatable.date.filter"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Esportazione">
        <bs-toolbar-button
            data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>