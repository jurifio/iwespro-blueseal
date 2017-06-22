<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"; ?>
    <?php echo $app->getAssets(['ui', 'forms', 'charts'], $page); ?>
    <script src="/assets/progressbar.min.js"></script>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php"; ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                </div>
            </div>

            <div class="container-fluid padding-25 sm-padding-10">
                <div id="monitorsContainer" data-period="today" class="row">

                </div>
            </div>
        </div>
    </div>
    <?php include "parts/footer.php"; ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
</body>
</html>