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
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="page_list"
                               data-controller="PageListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>">
                            <thead>
                            <tr>
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">Id Applicazione</th>
                                <th data-slug="pageTitle"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Nome</th>
                                <th data-slug="url"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">link</th>
                                <th data-slug="slug"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">pagina Template</th>
                                <th data-slug="namePost"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Guida Collegata</th>
                                <th data-slug="permission"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">permessi</th>
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
    <bs-toolbar-group data-group-label="Gestione menu">
        <bs-toolbar-button
                data-remote="bs.add.guide"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Esportazione">
        <bs-toolbar-button
            data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>