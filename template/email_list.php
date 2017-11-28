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
                                    data-isVisible="false"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Cc</th>
                                <th data-slug="bcc"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Bcc</th>
                                <th data-slug="submissionDate"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Submission</th>
                                <th data-slug="lastResponse"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Delivery</th>
                                <th data-slug="userName"
                                    data-searchable="true"
                                    data-orderable="true" class="center dataFilterType">Username</th>
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