<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms','tables'], $page); ?>
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
                    <div class="col-md-4 col-md-offset-4 alert-container closed">

                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped" data-column-filter="true" data-datatable-name="order_user_list"
                               data-controller="OrderByUserListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>" id="orderTable">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Utente</th>
                                <th>Email</th>
                                <th>Città</th>
                                <th>Paese</th>
                                <th class="dataFilterType">Data Ordine</th>
                                <th>Stato Ordine</th>
                                <th>Brand</th>
                                <th>Shop</th>
                                <th>Importo Pagato</th>
                                <th>Margine</th>
                                <th>Metodo pagamento</th>
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
    <bs-toolbar-group data-group-label="Gestione ordini">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/order/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo ordine manuale"
            data-placement="bottom"
            data-href="#"
            ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>

</body>
</html>