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
                               data-datatable-name="product_color_list"
                               data-column-filter="true"
                               data-controller="ProductColorListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu="100, 200, 500, 1000, 2000"
                               data-display-lenght="200">
                            <thead>
                            <tr>
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Codice</th>
                                <th data-slug="colorName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">Colore</th>
                                <th data-slug="colorGroupName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Gruppo Colore</th>
                                <th data-slug="var"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="centar">Colore Friend</th>
                                <th data-slug="dummyPic"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Immagine</th>
                                <th data-slug="categorie"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center categoryFilterType">Categorie</th>
                                <th data-slug="stato"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Stato</th>
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
    <bs-toolbar-group data-group-label="Gestione Colori">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-paint-brush"
            data-permission="/admin/product/edit"
            data-event="bs.manage.color"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Assegna Gruppo Colore"
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