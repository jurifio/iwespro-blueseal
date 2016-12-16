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
                    <div class="col-md-4 col-md-offset-4 alert-container closed">

                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped" data-datatable-name="user_list"
                               data-controller="UserListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>" id="userTable"
                               data-inner-setup="true"
                               data-length-menu-setup="50, 100, 200, 500"
                               data-display-length="50">
                            <thead>
                            <tr>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Nome</th>
                                <th data-slug="surname"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Cognome</th>
                                <th data-slug="email"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Email</th>
                                <th data-slug="sex"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Sesso</th>
                                <th data-slug="status"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Stato</th>
                                <th data-slug="method"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Metodo</th>
                                <th data-slug="creationDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc"
                                    class="center dataFilterType">Registrazione</th>
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
    <bs-toolbar-group data-group-label="Gestione utenti">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-user-plus"
            data-permission="/admin/user/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo utente"
            data-placement="bottom"
            data-href="/blueseal/utente"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-user-times"
            data-permission="/admin/user/delete"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-event="bs.user.delete"
            data-title="Cancella Utenti"
            data-placement="bottom"
            data-href="/blueseal/utente"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-thumbs-o-up"
            data-permission="/admin/user/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-event="bs.user.activate"
            data-title="Attiva un utente"
            data-placement="bottom"
            data-href="#"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="bs.user.password.change"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Assegna Ruoli">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-street-view"
            data-permission="/admin/user"
            data-event="bs.roles.show"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Vedi ruoli"
            data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-gavel"
            data-permission="/admin/user"
            data-event="bs.permission.show"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Vedi permessi"
            data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>