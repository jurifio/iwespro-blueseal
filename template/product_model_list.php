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
                               data-datatable-name="product_model_list"
                               data-controller="ProductModelListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 250, 1000, 1500, 2000">
                            <thead>
                                <tr>
                                    <th data-slug="id"
                                        data-searchable="true"
                                        data-orderable="true"
                                        class="center">id</th>
                                    <th data-slug="code"
                                        data-searchable="true"
                                        data-orderable="true"
                                        data-default-order="desc" class="center">Codice</th>
                                    <th data-slug="name"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Nome Modello</th>
                                    <th data-slug="productName"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Nome Prodotto</th>
                                    <th data-slug="prototypeName"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Scheda Prodotto</th>
                                    <th data-slug="categories"
                                        data-searchable="true"
                                        data-orderable="true" class="center categoryFilterType">Categorie</th>
                                    <th data-slug="details"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Dettagli</th>
                                    <th data-slug="catGroupName"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Categorie preImpostate</th>
                                    <th data-slug="gendName"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Genere</th>
                                    <th data-slug="matName"
                                        data-searchable="true"
                                        data-orderable="true" class="center">Materiale</th>
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
    <bs-toolbar-group data-group-label="Gestione prodotti">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo modello"
            data-placement="bottom"
            data-href="/blueseal/prodotti/modelli/modifica"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Lista delle Categorie per numero di modelli assegnati"
            data-placement="bottom"
            data-href="/blueseal/prodotti/modelli/modifica"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestione visualizzazione elementi">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-minus"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs-hide-model-prototype"
                data-title="Nascondi elementi"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestione massiva modelli">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-exchange"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs-massive-copy-model-prototype"
                data-title="Clona massivamente"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-cloud-upload"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs-massive-update-model-prototype"
                data-title="Aggiorna massivamente"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>