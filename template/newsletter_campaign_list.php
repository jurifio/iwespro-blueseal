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
                               data-datatable-name="newsletter_campaign_list"
                               data-controller="NewsletterCampaignListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500, 1000, 2000"
                               data-display-length="200">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center"
                                    data-default-order="desc">Id
                                </th>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Nome Campagna
                                </th>
                                <th data-slug="dateCampaignStart"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">data Inizio Campagna
                                </th>
                                <th data-slug="dateCampaignFinish"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Data Fine Campagna
                                </th>
                                <th data-slug="events"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Eventi collegati
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
                data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>

        <bs-toolbar-button
                data-remote="bs.lists.create.newslettercampaign"
        ></bs-toolbar-button>

        <bs-toolbar-button
                data-remote="bs.lists.modify.newslettercampaign"

        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.lists.delete.newslettercampaign"

        ></bs-toolbar-button>


    </bs-toolbar-group>
</bs-toolbar>
</body>
    </html><?php
