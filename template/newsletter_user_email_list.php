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
                               data-datatable-name="newsletter_email_list"
                               data-controller="NewsletterUserEmailListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500, 1000, 2000"
                               data-display-length="200">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Id
                                </th>
                                <th data-slug="email"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Email
                                </th>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Nome
                                </th>
                                <th data-slug="surname"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Cognome
                                </th>
                                <th data-slug="subscriptionDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Data Sottoscrizione
                                </th>
                                <th data-slug="unsubscriptionDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Data Cancellazione
                                </th>
                                <th data-slug="isActive"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Attivo
                                </th>
                                <th data-slug="gender"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Sesso (Lista utenti)
                                </th>
                                <th data-slug="leadPage"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Lead age
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
    <bs-toolbar-group data-group-label="Esportazione">
        <bs-toolbar-button
                data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Aggiorna il sesso dell'utente">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-venus-mars"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.newsletter.user.gender"
                data-title="Assegna il sesso"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Importa contatti da Liste esterne">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-address-book"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.newsletter.user.import"
                data-title="Importa Csv"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>