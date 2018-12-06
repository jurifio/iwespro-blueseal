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
    <?php include "parts/header.php"; ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">

            <div class="container" style="margin-top: 90px">
                <div class="panel-group" id="faqAccordion">


                    <?php foreach ($faqs as $faq): ?>
                        <div class="panel panel-default ">
                            <div class="panel-heading accordion-toggle question-toggle collapsed" data-toggle="collapse"
                                 data-parent="#faqAccordion" data-target="#<?php echo $faq->id; ?>">
                                <h4 class="panel-title">
                                    <a href="#" class="ing">Q: <?php echo $faq->question; ?></a>
                                </h4>

                            </div>
                            <div id="<?php echo $faq->id; ?>" class="panel-collapse collapse" style="height: 0px;">
                                <div class="panel-body">
                                    <h5><span class="label label-primary">Answer</span></h5>

                                    <p>
                                        <?php echo $faq->answer; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!--/panel-group-->
            </div>
        </div>
    </div>
    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Download fattura">
        <bs-toolbar-button

        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>