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
                               data-datatable-name="detail_batch_translate_list"
                               data-controller="DetailBatchTranslateListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500"
                               data-lang="<?php echo $lang; ?>"
                               data-pb="<?php echo $pb; ?>" >
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-name="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center sorting">ID</th>
                                <th data-slug="source"
                                    data-name="translatedName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center sorting">Sorgente</th>
                                <th data-slug="target"
                                    data-name="translatedName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center sorting">Destinazione</th>
                                <th data-slug="workCategoryStepsId"
                                    data-name="workCategoryStepsId"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center sorting">Stato della lavorazione</th>
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
    <bs-toolbar-group data-group-label="Termina lavorazione sul dettagli">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-step-forward"
                data-permission="worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.end.work.product.detail.translation"
                data-title="Termina la lavorazione sui dettagli selezionati"
                data-placement="bottom"
        ></bs-toolbar-button>
        <?php if($allShops): ?>
            <bs-toolbar-button
                    data-remote="bs.change.product.status.batch"
            ></bs-toolbar-button>
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-close"
                    data-permission="worker"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-event="bs.delete.product.detail.translation"
                    data-title="Cancella i dettagli dal lotto"
                    data-placement="bottom"
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