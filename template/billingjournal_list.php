<?php
/**
 * Created by PhpStorm.
 * User: jurif
 * Date: 08/01/2018
 * Time: 16:35
 */
?>
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
                               data-datatable-name="site_list"
                               data-controller="BillingJournalListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500, 1000, 2000"
                               data-display-length="200">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">id
                                </th>
                                <th data-slug="date"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Data Corrispettivo
                                </th>
                                <th data-slug="totalUeNetReceipt"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Totale Imponibile Ricevute
                                </th>
                                <th data-slug="totalUeVatReceipt"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Totale Iva Ricevute
                                </th>
                                <th data-slug="totalUeReceipt"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Totale Corrispettivo Ricevute
                                </th>
                                <th data-slug="totalUeNetInvoice"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Totale Imponibile Fatture IntraCEE
                                </th>
                                <th data-slug="totalUeVatInvoice"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Totale Iva Fatture IntraCee
                                </th>
                                <th data-slug="totalUeInvoice"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Totale Corrispettivo Fatture IntraCEE
                                </th>
                                <th data-slug="totalXUeNetInvoice"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Totale Imponibile Fatture ExtraUE
                                </th>
                                <th data-slug="totalXUeVatInvoice"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Totale Iva Fatture ExtraUE
                                </th>
                                <th data-slug="totalXUeInvoice"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Totale Corrispettivo Fatture ExtraUe
                                </th>
                                <th data-slug="datePrint"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Data di Stampa
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
        <?php include "parts/footer.php" ?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione">
        <bs-toolbar-button
                data-remote="bs.billingjournal.insert"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.billingjournal.print"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
    </html><?php
