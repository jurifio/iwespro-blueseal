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
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive"
                               width="100%"

                               data-datatable-name="blog_list"
                               data-controller="AggregatorPublishLogListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="false"
                                    class="center">ID</th>
                                <th data-slug="dateCreate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center dataFilterType">data</th>
                                <th data-slug="marketplaceName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Aggregatore</th>
                                <th data-slug="marketplaceAccountName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Account Aggregatore</th>
                                <th data-slug="campaignName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Campagna Associata</th>
                                <th data-slug="subject"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center dataFilterType">Oggetto</th>
                                <th data-slug="email"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center dataFilterType">Email</th>
                                <th data-slug="result"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center dataFilterType">Risultato</th>
                                <th data-slug="action"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center dataFilterType">Azione</th>

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
    <bs-toolbar-group data-group-label="Gestione Log">
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>