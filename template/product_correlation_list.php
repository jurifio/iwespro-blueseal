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
                               data-datatable-name="product_correlation_list"

                               data-controller="ProductCorrelationListAjaxController"
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
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">Tipo</th>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">Nome</th>
                                <th data-slug="image"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Immagine</th>
                                <th data-slug="description"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Descrizione</th>
                                <th data-slug="note"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">note</th>
                                <th data-slug="seo"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Seo</th>
                                <th data-slug="shopName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Shop Destinazione</th>
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
    <bs-toolbar-group data-group-label="Gestione  Tabella Correlazioni">
        <bs-toolbar-button
                data-remote="bs.product.correlation.insert"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.correlation.modify"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.correlation.delete"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.productpopulatecolour.correlation"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="\"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>

</body>
</html>