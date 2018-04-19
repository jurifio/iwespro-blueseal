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

            <div class="container-fluid container-fixed-lg bg-white" style="margin: 40px 0">
                <div class="row" style="display: flex">
                <div class="col-sm-4" style="border: 1px solid #a7a7a752; margin: 7px">
                    <div class="panel-heading"><p><strong>Seleziona la categoria</strong></p></div>
                    <div id="categoriesTree" class="panel-body fancytree-colorize-hover fancytree-fade-expander"></div>
                </div>

                <div class="col-sm-4" style="border: 1px solid #a7a7a752; margin: 7px">
                    <div class="form-group form-group-default required pre-scrollable" style="max-height: 150px">
                        <label for="productSeason">Seleziona la stagione</label>
                        <div class="form-group form-group-default required" id="checkSeason">
                        </div>
                    </div>
                    <button class="btn btn-info" id="selectAllSeason">Seleziona tutto</button>
                </div>

                <div class="col-sm-4" style="border: 1px solid #a7a7a752; margin: 7px">
                    <div class="form-group form-group-default required pre-scrollable" style="max-height: 150px">
                        <label for="productSeason">Seleziona lo shop</label>
                        <div class="form-group form-group-default required" id="checkShop">
                        </div>
                    </div>
                    <button class="btn btn-info" id="selectAllShops">Seleziona tutto</button>
                </div>
                </div>
                <div class="row" style="display: flex">
                <div class="col-sm-4" style="border: 1px solid #a7a7a752; margin: 7px">
                    <div class="form-group form-group-default required">
                        <label for="haveShooting">Shooting</label>
                        <input type="checkbox" id="haveShooting" name="haveShooting">
                    </div>
                </div>

                <div class="col-sm-4" style="border: 1px solid #a7a7a752; margin: 7px">
                    <div class="form-group form-group-default required">
                        <label for="havePhoto">Foto</label>
                        <input type="checkbox" id="havePhoto" name="havePhoto">
                    </div>
                </div>


                <div class="col-sm-4" style="display: flex; justify-content: center; border: 1px solid #a7a7a752; margin: 7px">
                    <button class="btn btn-success" id="search">RICERCA</button>
                </div>
            </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white" id="printThis">
                <div class="container">
                    <table class="table" id="tableResult">
                        <thead>
                        <tr>
                            <th>Categoria</th>
                            <th>Numero Prodotti</th>
                            <th>Stagione</th>
                            <th>Foto</th>
                            <th>Shooting</th>
                            <th>Shop</th>
                            <th>Vedi prodotti</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <?php include "parts/footer.php"?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="STAMPA RISULTATO">
        <bs-toolbar-button
                data-remote="bs.product.print.filter.custom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>