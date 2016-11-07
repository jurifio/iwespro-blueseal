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
                    <div class="panel-heading">
                        <h5>Traduzione Categorie <?php echo $shopName; ?></h5>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%" data-column-filter="true"
                               data-datatable-name="dictionary_edit"
                               data-controller="DictionaryCategoryEditAjaxController" data-shop="<?php echo $shopId; ?>"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-lenght-menu="50, 100, 200, 500"
                               data-display-length="50">
                            <thead>
                            <tr>
                                <th data-slug="term"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center sorting">Termine</th>
                                <th data-slug="foreign"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center sorting">Traduzione</th>
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
</body>
</html>