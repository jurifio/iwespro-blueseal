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
                               data-datatable-name="product_look_list"

                               data-controller="ProductLookListAjaxController"
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
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">Nome</th>
                                <th data-slug="image"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Immagine</th>
                                <th data-slug="discountActive"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">In Promozione</th>
                                <th data-slug="typeDiscount"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">tipo Sconto</th>
                                <th data-slug="amount"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">importo</th>
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
    <bs-toolbar-group data-group-label="Gestione  Tabella Look">
        <bs-toolbar-button
                data-remote="bs.product.look.insert"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.look.modify"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.product.look.delete"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>

</body>
</html>