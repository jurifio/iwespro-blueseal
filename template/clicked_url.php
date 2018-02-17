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
                               data-controller="NewsletterClickedUrlRedemptionListAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-length-menu-setup="100, 200, 500, 1000, 2000"
                               data-display-length="200"
                               data-emailid="<?php echo $emailId?>"
                               data-emailaddressid="<?php echo $emailAddressId?>">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">id
                                </th>
                                <th data-slug="url"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">url
                                </th>
                                <th data-slug="type"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">type
                                </th>
                                <th data-slug="productCode"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Prodotto
                                </th>
                                <th data-slug="productImage"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">Image
                                </th>
                                <th data-slug="date"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">date
                                </th>
                                <th data-slug="emailId"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">emailId
                                </th>
                                <th data-slug="emailAddressId"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">emailAddressId
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
</body>
</html>