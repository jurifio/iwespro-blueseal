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
                               data-controller="MarketPlaceHasShopListAjaxController"
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
                                <th data-slug="shopName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Shop
                                </th>
                                <th data-slug="markeplaceName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">MarketPlace
                                </th>
                                <th data-slug="typeSync"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Tipo Sincronizzazione
                                </th>
                                <th data-slug="imgMarketPlace"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Immagine
                                </th>
                                <th data-slug="prestashopId"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Prestashop Id
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
    <bs-toolbar-group data-group-label="Gestione">
        <bs-toolbar-button
                data-remote="bs.lists.create.associatemarketplace"
        ></bs-toolbar-button>

        <bs-toolbar-button
                data-remote="bs.lists.delete.asociatemarketplacehasshop"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <!--<bs-toolbar-group data-group-label="Nuovo shop">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/marketing"
                data-event="bs.marketplace.shop.add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>-->
</bs-toolbar>
</body>
</html>