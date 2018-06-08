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
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed">

                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="size_full_list"
                               data-controller="ProductBatchDetailsListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500"
                               data-productbatchid="<?php echo $productBatchId ?>">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id</th>
                                <th data-slug="productCode"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Prodotto</th>
                                <th data-slug="brand"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Brand</th>
                                <th data-slug="productName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome del prodotto</th>
                                <th data-slug="season"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Stagione</th>
                                <th data-slug="description"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Descrizione</th>
                                <th data-slug="colorGroup"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Gruppo colore</th>
                                <th data-slug="colorNameManufacturer"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Colore produttore</th>
                                <th data-slug="details"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Dettagli</th>
                                <th data-slug="dummy"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Immagine</th>
                                <th data-slug="categoryId"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Categorie</th>
                                <th data-slug="stepName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Fase</th>
                                <th data-slug="productCard"
                                    data-searchable="false"
                                    data-orderable="false" class="center">Foto per normalizzazione</th>
                                <th data-slug="note"
                                    data-searchable="false"
                                    data-orderable="true" class="center">Note</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include "parts/footer.php"?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione prodotti">
        <bs-toolbar-button
                data-remote="bs.product.editVariantDescription"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-sitemap"
                data-permission="worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs-category-edit-worker"
                data-title="Cambia Categoria ai prodotti selezionati"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-paint-brush"
                data-permission="worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs-product-namesMerge-worker"
                data-title="Copia i nomi dei prodotti"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.details.new">
        </bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-tasks"
                data-permission="worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs-product-details.merge-worker"
                data-title="Copia dettagli"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-object-group"
                data-permission="worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs-product-model-insertIntoProducts-worker"
                data-title="Aggiorna I prodotti da un modello"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.name.insert"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Termina lavorazione sul prodotto">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-step-forward"
                data-permission="worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.end.work.product"
                data-title="Termina la lavorazione sui prodotti selezionati"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.change.product.status.batch"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Notifica termine lotto">
        <bs-toolbar-button
                data-remote="bs.end.product.batch"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Istruzioni">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-question-circle"
                data-permission="worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Istruzioni"
                data-placement="bottom"
                data-href="/document/NORMALIZZAZIONE-DEI-PRODOTTI-NEL-CATALOGO.pdf"
                data-target="_blank"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Elimina prodotti">
        <bs-toolbar-button
                data-remote="bs.delete.product.from.batch"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Inserisci nota">
        <bs-toolbar-button
                data-remote="bs.note.product.batch"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>