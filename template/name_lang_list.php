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
                    <div class="panel-heading">
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="name_lang_list"
                               data-controller="NameTranslateLangListAjaxController?marks=tutto&translated=tutto"
                               data-lang="<?php echo $langId; ?>"
                               data-ids="<?php echo $ids; ?>"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500, 1000"
                               data-display-length="50">
                            <thead>
                            <tr>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center sorting">Italiano</th>
                                <th data-slug="category"
                                    data-searchable="true"
                                    data-orderable="false"
                                    class="center sorting categoryFilterType">Categorie</th>
                                <th data-slug="count"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">N. Prodotti</th>
                                <th data-slug="trans"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center sorting">Traduzione</th>
                                <th data-slug="stepName"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center sorting">Stato</th>
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
    <?php if($allShops): ?>
    <bs-toolbar-group data-group-label="Visualizzazione nomi">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-list"
            data-permission="/admin/product/edit"
            data-event="bs.all.name"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Tutti"
            data-placement="bottom"
            data-href="<?php echo $urlAll; ?>"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-language"
            data-permission="/admin/product/edit"
            data-event="bs.refresh.name"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Solo non tradotti"
            data-placement="bottom"
            data-href="<?php echo $urlTrans; ?>"
        ></bs-toolbar-button>
        <bs-toolbar-button
        data-tag="a"
        data-icon="fa-exclamation-circle"
        data-permission="/admin/product/edit"
        data-event="bs.filterByMark"
        data-class="btn btn-default"
        data-rel="tooltip"
        data-title="Filtra per punti esclamativi"
        data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-map"
            data-permission="/admin/product/edit"
            data-event="bs.filterByTranslation"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Filtra per punti esclamativi"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <?php endif; ?>
    <bs-toolbar-group data-group-label="Termina lavorazione sul prodotto">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-step-forward"
                data-permission="worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.end.work.product.name.translation"
                data-title="Termina la lavorazione sui prodotti selezionati"
                data-placement="bottom"
        ></bs-toolbar-button>
        <?php if($allShops): ?>
        <bs-toolbar-button
                data-remote="bs.change.product.status.batch"
        ></bs-toolbar-button>
        <?php endif; ?>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Notifica termine lotto">
        <bs-toolbar-button
                data-remote="bs.end.product.batch"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>