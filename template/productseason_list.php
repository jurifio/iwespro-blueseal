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
    <?php include "parts/header.php"; ?>
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
                               data-datatable-name="productseason_list"
                               data-controller="ProductSeasonListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500"
                               data-display-length="50">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">id
                                </th>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Anno
                                </th>
                                <th data-slug="dateStart"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data Inizio
                                </th>
                                <th data-slug="dateEnd"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Data Fine
                                </th>
                                <th data-slug="isActive"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Attiva
                                </th>
                                <th data-slug="order"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Ordine
                                </th>
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
    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione Stagioni">
        <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-file-o fa-plus"
                    data-permission="allShops"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-title="Aggiungi una nuova stagione manuale"
                    data-placement="bottom"
                    data-href="/blueseal/prodotti/season-aggiungi"
            ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>