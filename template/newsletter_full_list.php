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
            <div class="container" style="margin-top:100px">
                <ul class="nav nav-tabs">
                    <li class="tool active" id="camp"><a data-append="campaign" data-controller="CampaignListWidget"
                                                         data-toggle="tab" href="#campaign-tab">Campagne</a></li>
                    <li class="tool" id="event"><a data-toggle="tab" href="#event-tab">Eventi</a></li>
                    <li class="tool" id="ins"><a data-toggle="tab" href="#ins-tab">Inserzioni</a></li>
                    <li class="tool" id="sen"><a data-toggle="tab" href="#sen-tab">Invii</a></li>
                </ul>
            </div>

            <div class="tab-content">
                <div id="campaign-tab" class="tab-pane fade in active">
                </div>
                <div id="event-tab" class="tab-pane fade">
                </div>
                <div id="ins-tab" class="tab-pane fade">
                </div>
                <div id="sen-tab" class="tab-pane fade">
                </div>
            </div>
        </div>
    </div>
</div>


<?php include "parts/footer.php" ?>

<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Campagne" id="camp-tool" class="hide toolbar-tab">
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
    <bs-toolbar-group data-group-label="Eventi" id="event-tool" class="hide toolbar-tab">
        <bs-toolbar-button
                data-remote="bs.lists.generate.csv"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.lists.create.newsletterevent"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.lists.modify.newsletterevent"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-remote="bs.lists.delete.newsletterevent"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>