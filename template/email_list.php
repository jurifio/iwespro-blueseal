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

                    </div>
                </div>
            </div>

            <div class="legenda container">
                <div>
                    <div class="inviata colorLeg"></div>
                    <p>Email inviata a tutti i destinatari</p>
                </div>
                <div>
                    <div class="error colorLeg"></div>
                    <p>Email non inviata</p>
                </div>
                <div>
                    <div class="inviataP colorLeg"></div>
                    <p>Email inviata parzialmente</p>
                </div>
                <div>
                    <div class="accepted colorLeg"></div>
                    <p>Email accettata ma non inviata</p>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="email_list"
                               data-controller="EmailListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true" class="center">id</th>
                                <th data-slug="from"
                                    data-searchable="true"
                                    data-orderable="true" class="center">From</th>
                                <th data-slug="subject"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Subject</th>
                                <th data-slug="to"
                                    data-searchable="true"
                                    data-orderable="true" class="center">To</th>
                                <th data-slug="cc"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Cc</th>
                                <th data-slug="bcc"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Bcc</th>
                                <th data-slug="htmlBody"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Testo</th>
                                <th data-slug="submissionDate"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc" class="center dataFilterType">Submission</th>
                                <th data-slug="lastResponse"
                                    data-searchable="true"
                                    data-orderable="true" class="center dataFilterType">Delivery</th>
                                <th data-slug="responseTime"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Response Time</th>
                                <th data-slug="userName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Username</th>
                                <th data-slug="isError"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Error</th>
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
</body>
</html>