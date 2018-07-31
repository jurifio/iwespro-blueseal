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
                               data-datatable-name="newsletter_external_redemption_list"
                               data-controller="NewsletterExternalRedemptionListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500, 1000, 2000"
                               data-display-length="200">
                            <thead>
                            <tr>
                                <th data-slug="newsletterId"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Id
                                </th>
                                <th data-slug="newsletterCloneId"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Newsletter
                                </th>

                                <th data-slug="newsletterName"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Nome newsletter
                                </th>
                                <th data-slug="emailAddressCount"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">N. utenti inviata
                                </th>
                                <th data-slug="emailPending"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Email da Inviare
                                </th>
                                <th data-slug="emailNotQueued"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Email non In Coda
                                </th>
                                <th data-slug="emailAccepted"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Email Accettate
                                </th>
                                <th data-slug="emailDelivered"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Email Consegnate
                                </th>
                                <th data-slug="emailDropped"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Email Non Consegnate
                                </th>
                                <th data-slug="emailOpened"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Email Aperte
                                </th>
                                <th data-slug="emailClicked"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Email Cliccate
                                </th>
                                <th data-slug="openTimeSinceSent"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Tempo dall'apertura
                                </th>
                                <th data-slug="clickTimeSinceOpened"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Tempo accesso al sito da apertura
                                </th>
                                <th data-slug="aliveTime"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Tempo apertura a ultimo click
                                </th>
                                <th data-slug="sentPercent"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">% inviate
                                </th>
                                <th data-slug="openedPercent"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">% aperte
                                </th>
                                <th data-slug="clickedPercent"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center"> % cliccate
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
</bs-toolbar>
</body>
</html>