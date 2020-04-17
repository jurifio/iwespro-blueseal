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
                               data-datatable-name="crm_comanyliker_list"
                               data-controller="CrmCompanyLikerListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="asc"
                                    class="center">Id
                                </th>
                                <th data-slug="companyName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Company Liker
                                </th>
                                <th data-slug="interest"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Interessi
                                </th>
                                <th data-slug="message"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Rubrica
                                </th>
                                <th data-slug="address"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Indirizzo
                                </th>
                                <th data-slug="phone"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Telefono
                                </th>
                                <th data-slug="phone2"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Telefono 2
                                </th>
                                <th data-slug="email"
                                    data-searchable="false"
                                    data-orderable="false"
                                    class="center">Email
                                </th>
                                <th data-slug="contactName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Contatto
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
    <bs-toolbar-group data-group-label="Operazioni">
        <bs-toolbar-button
            data-remote="btn.shopEnableDisable"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="btn.shopVisibleInvisible"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="interessi">
        <bs-toolbar-button
            data-remote="btn.shopEnableDisable"
        ></bs-toolbar-button>
        <bs-toolbar-button
            data-remote="btn.shopVisibleInvisible"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Consultazioni">
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