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
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed">
                        <h2>BETA VERSION | 1.0</h2>
                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="size_full_list"
                               data-controller="FoisonListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Id</th>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome</th>
                                <th data-slug="surname"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Cognome</th>
                                <th data-slug="email"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Email</th>
                                <th data-slug="iban"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Iban</th>
                                <th data-slug="interestName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Categorie</th>
                                <th data-slug="totalRank"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Rank totale</th>
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
    <?php include "parts/footer.php"?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <?php if($perm): ?>
    <bs-toolbar-group data-group-label="Gestione Foison">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus"
                data-permission="allShops"
                data-event="bs-foison-add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Assegna utente a Foison"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestisci dati">
        <bs-toolbar-button
                data-remote="bs.user.password.change"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestisci imm profilo">
    <?php endif; ?><bs-toolbar-button
            data-remote="bs.foison.profile.image.manage"
    ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>