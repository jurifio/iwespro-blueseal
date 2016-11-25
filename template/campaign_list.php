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
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                </div>
            </div>
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="campaign_list"
                               data-controller="CampaignListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500, 1000">
                            <thead>
                            <tr>
                                <th data-slug="campaignName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Nome</th>
                                <th data-slug="campaignType"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Tipo</th>
                                <th data-slug="campaignCode"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">code</th>
                                <th data-slug="firstAccess"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc" class="center">Primo Accesso</th>
                                <th data-slug="visits"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Visite</th>
                                <th data-slug="visits"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Visite Prodotti</th>
                                <th data-slug="converions"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Conversioni</th>
                                <th data-slug="totConversion"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Totale Conversioni</th>
                                <th data-slug="scontrinoMedio"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Scontrino Medio</th>
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