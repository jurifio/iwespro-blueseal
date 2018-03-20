<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms'], $page); ?>
    <title><?php echo $page->getTitle(); ?></title>
    <style>
        .fancy-tree-container ul,.fancy-tree-container li {
            padding-left: 0;
            line-height: inherit;
        }
    </style>
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
                <!-- START PANEL -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="panel panel-transparent">
                            <div class="panel-heading">
                                <div class="panel-title">Albero permessi</div>
                            </div>
                            <div class="panel-body">
                                <div class="fancy-tree-container" id="permissionTree"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END PANEL -->
            </div>
        </div>
        <?php include "parts/footer.php" ?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Strumenti Permessi">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.permission.insert"
                data-title="Aggiungi Nuova"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>