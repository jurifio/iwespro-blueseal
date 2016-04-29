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
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%" data-datatable-name="blog_list" data-controller="BlogPostTrashListAjaxController" data-url="<?php echo $app->urlForBluesealXhr() ?>">
                            <thead>
                            <tr>
                                <th class="center">ID</th>
                                <th class="center">Cover</th>
                                <th class="center">Titolo</th>
                                <th class="center">Estratto</th>
                                <th class="center">Creazione</th>
                                <th class="center">Pubblicazione</th>
                                <th class="center">Stato</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php"?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione post">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-cloud-upload"
            data-permission="/admin/content/publish"
            data-event="bs.pub.post"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Ripristina Post"
            data-placement="bottom"
            data-toggle="modal"
            data-target="#bsModal"
            ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>