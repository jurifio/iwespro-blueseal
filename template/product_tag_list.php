<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"?>
    <?php echo $app->getAssets(['ui','forms','tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php";?>
<div class="page-container">
    <?php include "parts/header.php";?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="product_tag_list"
                               data-column-filter="true"
                               data-controller="ProductTagListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>">
                            <thead>
                                <tr>
                                    <th class="center">Codice</th>
                                    <th class="center">Shop</th>
                                    <th class="center">Gruppo Colore</th>
                                    <th class="center">Stagione</th>
                                    <!--<th class="center">Gruppo Taglie</th>-->
                                    <th class="center">Dettagli</th>
                                    <!--<th class="center">Immagine </th>-->
                                    <th class="center">Brand</th>
                                    <th class="center">Priorità</th>
                                    <th class="center">Tags</th>
                                    <th class="center">Qt.</th>
                                    <th class="center">SCA</th>
                                    <th class="center">Stato</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php"?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione tag">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-tag"
            data-permission="/admin/product/edit"
            data-event="bs.product.tag"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Tagga prodotti"
            data-placement="bottom"
            ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-sort-numeric-asc"
                data-permission="/admin/product/edit"
                data-event="bs.priority.edit"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Priorità"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>