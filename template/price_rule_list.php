<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
    <title>99Monkeys - <?php echo $page->getTitle(); ?></title>
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
                               data-datatable-name="product_list_list"

                               data-controller="PriceRuleListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500, 1000, 2000"
                               data-display-lenght="200">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">id</th>
                                <th data-slug="shopName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">Shop associato</th>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">Nome</th>
                                <th data-slug="typeVariation"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Tipo di Variazione</th>
                                <th data-slug="variation"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Variazione %</th>
                                <th data-slug="typeVariationSale"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Tipo di Variazione Sconti</th>
                                <th data-slug="variationSale"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Variazione %</th>
                                <th data-slug="dateStart"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Inizio Validità</th>
                                <th data-slug="dateEnd"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">fine Validità</th>
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
    <bs-toolbar-group data-group-label="Gestione  Tabella Listini">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-file-o fa-plus"
                data-permission="/admin/content/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Nuovo Listino"
                data-placement="bottom"
                data-href="<?php echo '/blueseal/listini/aggiungi-regola'; ?>"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-trash"
                data-permission="/admin/content/delete"
                data-event="bs.pricelist.delete"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Elimina un Listino"
                data-placement="bottom"
                data-toggle="modal"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>

</body>
</html>