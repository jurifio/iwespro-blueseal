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
                               data-datatable-name="newsletter_redemption_list"
                               data-controller="NewsletterSingleRedemptionListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500, 1000, 2000"
                               data-display-length="200"
                               data-newsletterId="<?php echo $newsletterId?>">
                            <thead>
                            <tr>
                                <th data-slug="emailAddressId"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">EmailAddressId
                                </th>
                                <th data-slug="Email"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Destinatario
                                </th>
                                <th data-slug="responseDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">ResponseDate
                                </th>
                                <th data-slug="queuedTime"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">QueuedTime
                                </th>
                                <th data-slug="sentTime"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">SentTime
                                </th>
                                <th data-slug="bounceTime"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">BounceTime
                                </th>
                                <th data-slug="firstOpenTime"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">FirstOpenTime
                                </th>
                                <th data-slug="firstClickTime"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">FirstClickTime
                                </th>
                                <th data-slug="lastClickTime"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">LastClickTime
                                </th>
                                <th data-slug="emailId"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">EmailId
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
    <bs-toolbar-group data-group-label="URL">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/marketing"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.clicked.url"
                data-title="Vedi url cliccati dall'utente"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>