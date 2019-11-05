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
                               data-datatable-name="shop_list"
                               data-controller="ShopListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">Id
                                </th>
                                <th data-slug="title"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Titolo
                                </th>
                                <th data-slug="owner"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Gestore
                                </th>
                                <th data-slug="currentSeasonMultiplier"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Moltiplicatore Stagione Corrente
                                </th>
                                <th data-slug="pastSeasonMultiplier"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Moltiplicatore Stagioni Passate
                                </th>
                                <th data-slug="saleMultiplier"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Moltiplicatore Saldi
                                </th>
                                <th data-slug="referrerEmails"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Email Notifica
                                </th>
                                <th data-slug="minReleasedProducts"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Minimo Prodotti Online
                                </th>
                                <th data-slug="releasedProducts"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Prodotti Attualmente Online
                                </th>
                                <th data-slug="isActive"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Attivo
                                </th>
                                <th data-slug="iban"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Iban Pagamento
                                </th>
                                <th data-slug="vatNumber"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Partita Iva
                                </th>
                                <th data-slug="users"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Utenti Attivi
                                </th>
                                <th data-slug="numberReceipt"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Ultima numero Ricevuta;
                                </th>
                                <th data-slug="numberInvoiceUe"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Ultima numero Fattura UE;
                                </th>
                                <th data-slug="numberInvoiceExtraUe"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Ultima numero Fattura ExtraUe;
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
    <bs-toolbar-group data-group-label="Funzioni">
        <bs-toolbar-button
            data-remote="btn.shopEnableDisable"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="btn.shopVisibleInvisible"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>