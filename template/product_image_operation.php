<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"; ?>
    <?php echo $app->getAssets(['ui','forms','charts'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
    <?php include "parts/sidebar.php";?>
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
                    <div class="row">
                        <div class="col-lg-12">
                            <?php if($app->user()->hasPermission('allShops')): ?>
                                <img style="width: 100%" src="/assets/img/rocketImage.png">
                            <?php else: ?>

                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                        <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 h2">
                           Benvenuti nella sezione di riposizionamento foto<br><p>
                                Per poter riposizionare le foto bisogna inserire nella path del nas <b>\\192.168.1.155\shootImport\resize</b> la cartella con le  foto da elaborare<br>
                                una volta posizionata la cartella  cliccare sul tasto Remasterizzazione Foto <span class="fa fa-rocket"></span><br>
                                attendere la fine dell'elaborazione e il risultato dell'elaborazione si troverà nella cartella <b>\\192.168.1.155\shootImport\newage2\topublish</b><br>
                                le  originali verrano inserite in <b>\\192.168.1.155\shootImport\newage2\original</b><br>
                            <p>
                                Per poter adattare le foto bisogna inserire nella path del nas <b>\\192.168.1.155\shootImport\resizedresses</b> la cartella con le  foto da elaborare<br>
                                una volta posizionata la cartella  cliccare sul tasto Remasterizzazione Foto Indossato <span class="fa fa-fighter-jet"></span><br>
                                attendere la fine dell'elaborazione e il risultato dell'elaborazione si troverà nella cartella <b>\\192.168.1.155\shootImport\newage2\topublish</b><br>
                                le  originali verrano inserite in <b>\\192.168.1.155\shootImport\newage2\original</b><br>
                            <p>
                                Per poter adattare le foto bisogna inserire nella path del nas <b>\\192.168.1.155\shootImport\resizesocial</b> la cartella con le  foto da elaborare<br>
                                una volta posizionata la cartella  cliccare sul tasto Remasterizzazione Foto Indossato <span class="fa fa-fighter-jet"></span><br>
                                attendere la fine dell'elaborazione e il risultato dell'elaborazione si troverà nella cartella <b>\\192.168.1.155\shootImport\newage2\topublish</b><br>
                                le  originali verrano inserite in <b>\\192.168.1.155\shootImport\newage2\original</b><br>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <hr>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php include "parts/footer.php"; ?>
    </div>
    <?php include "parts/bsmodal.php"; ?>
    <?php include "parts/alert.php"; ?>
    <bs-toolbar class="toolbar-definition">
        <bs-toolbar-group data-group-label="Gestione">

            <bs-toolbar-button
                    data-remote="bs.dictionarysizeimage.run"
            ></bs-toolbar-button>
            <bs-toolbar-button
                    data-remote="bs.dictionarysizeimagedresses.run"
            ></bs-toolbar-button>
            <bs-toolbar-button
                    data-remote="bs.dictionarysocialsizeimage.run"
            ></bs-toolbar-button>
        </bs-toolbar-group>
    </bs-toolbar>
</body>
</html>