<!DOCTYPE html>
<html>
<head>
    <?php include 'parts/head.php'; ?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php";?>
<div class="page-container">
    <?php include "parts/header.php" ?>
    <?php include "parts/operations.php" ?>
    <div class="page-content-wrapper full-height">
        <div class="content full-height">
            <div class="email-wrapper">
                <nav class="email-sidebar padding-30">
                    <p class="menu-title">CONTENUTI E TRADUZIONI</p>
                    <ul class="main-menu">
                        <li class="active">
                            <a href="#" class="section-selector" data-section="homepage">
                                <span class="title"><i class="pg-inbox"></i> Homepage</span>
                                <span class="badge pull-right">5</span>
                            </a>
                        </li>
                        <li class="">
                            <a href="#" class="section-selector" data-section="catalog">
                                <span class="title section-selector"><i class="pg-folder"></i> Catalogo</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="section-selector" data-section="psheet">
                                <span class="title"><i class="pg-sent"></i> Scheda prodotto</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="section-selector" data-section="checkout">
                                <span class="title"><i class="pg-spam"></i> Processo d'acquisto</span>
                                <span class="badge pull-right">10</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="section-selector" data-section="general">
                                <span class="title"><i class="pg-spam"></i> Generale</span>
                                <span class="badge pull-right">10</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="section-selector" data-section="otherpages">
                                <span class="title"><i class="pg-spam"></i> Altre pagine</span>
                                <span class="badge pull-right">10</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="section-selector" data-section="db">
                                <span class="title"><i class="pg-spam"></i> Database</span>
                                <span class="badge pull-right">10</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="section-selector" data-section="prdet">
                                <span class="title"><i class="pg-spam"></i> Dettagli prodotto</span>
                                <span class="badge pull-right">10</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- END EMAL SIDEBAR MENU -->
                <!-- START EMAILS LIST -->
                <div class="email-list b-r b-grey"><!--<a class="email-refresh" href="#"><i class="fa fa-refresh"></i></a>-->
                    <div id="emailList">
                        <div id="emailList" class="list-view"><h2 class="list-view-fake-header">Seleziona una sezione</h2>
                            <div class="list-view-wrapper" data-ios="false">
                                <div class="list-view-group-container">
                                    <div class="list-view-group-header"><span>Seleziona una sezione</span></div>
                                    <ul class="no-padding" id="sectionList">
                                        <!-- qui vengono caricate le sezioni da modificare -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END EMAILS LIST -->
                <!-- START OPENED EMAIL -->
                <div class="email-opened">
                    <div class="no-email">
                        <h1>Seleziona un elemento</h1>
                    </div>
                    <div class="email-content-wrapper">
                        <div class="actions-wrapper menuclipper bg-master-lightest">
                            <ul class="actions menuclipper-menu no-margin p-l-20 ">
                                <li class="no-padding"><button class="btn btn-complete btn-xs"><i class="fa fa-floppy-o"></i> salva</button></li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div id="content-wrapper" style="padding-top:50px;"></div>
                    </div>
                </div>
                <!-- END OPENED EMAIL -->
            </div>
        </div>
        <?php include 'parts/footer.php'; ?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<script src="<?php echo $app->urlForBlueseal() ?>/assets/js/contentmgr.js" type="text/javascript"></script>
</body>
</html>