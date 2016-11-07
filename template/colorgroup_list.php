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
                    <div class="col-md-4 col-md-offset-4 alert-container closed">
                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-heading">
                        <div class="panel-title">Elenco gruppicolore
                        </div>
                        <div class="export-options-container pull-right"></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="container-fluid container-fixed-lg bg-white">
                        <div class="panel panel-transparent">
                            <div class="panel-body">
                                <table class="table table-striped responsive" width="100%"
                                       data-datatable-name="color_list" data-controller="ColorListAjaxController"
                                       data-url="<?php echo $app->urlForBluesealXhr() ?>"
                                       data-inner-setup="true"
                                       data-lenght-menu="50, 100, 200, 500"
                                       data-display-length="50">
                                    <thead>
                                    <tr>
                                        <th data-slug="name"
                                            data-searchable="true"
                                            data-orderable="true"
                                            data-default-order="desc"
                                            class="center sorting">Nome</th>
                                        <th data-slug="slug"
                                            data-searchable="true"
                                            data-orderable="true"
                                            class="center sorting">Slug</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PANEL -->
            </div>
            <!-- END CONTAINER FLUID -->
        </div>
        <!-- END PAGE CONTENT -->
        <?php include "parts/footer.php" ?>
    </div>
    <!-- END PAGE CONTENT WRAPPER -->
</div>
<!-- END PAGE CONTAINER -->

<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar>
    <bs-toolbar-group data-group-label="Gestione colori">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo colore"
            data-placement="bottom"
            data-href="<?php echo $addUrl; ?>"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-trash"
            data-permission="/admin/product/delete"
            data-event="bs.color.delete"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Elimina colore"
            data-placement="bottom"
            data-target="#bsModal"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>