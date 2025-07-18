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
                               data-datatable-name="product_detail_list"
                               data-controller="ProductDetailListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th  data-slug="slug"
                                     data-searchable="true"
                                     data-orderable="true"
                                     class="center">Termine</th>
                                <th  data-slug="name"
                                     data-searchable="true"
                                     data-orderable="true"
                                     class="center">Italiano</th>
                                <th  data-slug="category"
                                     data-searchable="false"
                                     data-orderable="false"
                                     class="center">Categorie</th>
                                <th  data-slug="count"
                                     data-searchable="false"
                                     data-orderable="false"
                                     class="center">N. Prodotti</th>
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
    <bs-toolbar-group data-group-label="Gestione dettagli prodotto">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-magic"
            data-permission="/admin/product/edit"
            data-event="bs.manage.detail"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Unisci dettagli"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-search"
            data-permission="/admin/product/edit"
            data-event="bs.manage.detailproducts"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Visualizza Prodotti"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-exclamation-triangle"
            data-permission="/admin/product/edit"
            data-event="bs.manage.deletedetails"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Cancella i dettagli"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-pencil"
            data-permission="/admin/product/edit"
            data-event="bs.details.compare"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Evidenzia nomi simili"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
<script type="text/javascript">
    $(document).ready(function(){
        $(".visualizzaButton").click(
            function(){
                var id = "dettCollaps-" + $(this).data("rowCollapse");
                var state = ($(id).css("display") == "hidden") ? "block" : "hidden";
                $(id).css("display", state);
            }
        );
    });

</script>
</body>
</html>