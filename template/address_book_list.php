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
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed">

                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="address_book_list"
                               data-controller="AddressBookListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id
                                </th>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome
                                </th>
                                <th data-slug="subject"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Soggetto
                                </th>
                                <th data-slug="city"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Città
                                </th>
                                <th data-slug="address"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Indirizzo
                                </th>
                                <th data-slug="vatNumber"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Vat
                                </th>
                                <th data-slug="billingShop"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Di fatturazione Per
                                </th>
                                <th data-slug="shippingShop"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Di spedizione Per
                                </th>
                                <th data-slug="iban"
                                    data-searchable="true"
                                    data-orderable="true" class="center">IBAN
                                </th>
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
    <bs-toolbar-group data-group-label="Gestione Indirizzi">

    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>