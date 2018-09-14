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
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <div class="col-md-3">
                        <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#checkGender">GENDER</button>
                        <div id="checkGender" class="collapse">
                            <label for="searchGender">Cerca Genders</label>
                            <input type="text" id="searchGender">
                            <button id="gndBtn">Cerca</button>
                            <?php foreach ($gendRes as $genderId => $numGP): ?>
                                <div><input class="sg" type="checkbox" data-searchgender="<?php echo $numGP['name']. "(". $numGP['count'] .")" ?>" name="<?php echo $genderId ?>" value="<?php echo $genderId ?>" /> <p class="sg" data-searchgender="<?php echo $numGP['name'] ."(". $numGP['count'] .")" ?>" style="display: inline"><?php echo $numGP['name'] . "(". $numGP['count'] .")"; ?></p> </div>
                            <?php endforeach; ?>
                        </div>
                        </div>

                        <div class="col-md-3">
                            <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#checkMacroCatGroup">MACRO CATEGORIE</button>
                            <div id="checkMacroCatGroup" class="collapse">
                                <label for="searchMacroCategory">Cerca Macro Categoria</label>
                                <input type="text" id="searchMacroCategory">
                                <button id="mcrBtn">Cerca</button>
                                <?php foreach ($macroCatRes as $macroCatiD => $numMCP): ?>
                                    <div><input class="smcg" type="checkbox" data-searchmacrocategory="<?php echo $numMCP['name'] . "(". $numMCP['count'] .")" ?>" name="<?php echo $macroCatiD ?>" value="<?php echo $macroCatiD ?>" /> <p class="smcg" data-searchmacrocategory="<?php echo $numMCP['name'] . "(". $numMCP['count'] .")" ?>" style="display:inline;"><?php echo $numMCP['name'] . "(". $numMCP['count'] .")"; ?></p></div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#checkCatGroup">CATEGORIE</button>
                            <div id="checkCatGroup" class="collapse">
                                <?php foreach ($catGroupRes as $catGroupId => $numCP): ?>
                                    <div><input class="sc" type="checkbox" data-searchcategory="<?php echo $numCP['name'] . "(". $numCP['count'] .")"; ?>" name="<?php echo $catGroupId ?>" value="<?php echo $catGroupId ?>" /><p class="sc" data-searchcategory="<?php echo $numCP['name'] . "(". $numCP['count'] .")"; ?>" style="display:inline;"> <?php echo $numCP['name'] . "(". $numCP['count'] .")"; ?> </p></div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#checkMaterial">MATERIALI</button>
                            <div id="checkMaterial" class="collapse">
                                <label for="searchMaterial">Cerca Materiali</label>
                                <input type="text" id="searchMaterial">
                                <button id="mBtn">Cerca</button>
                                <?php foreach ($matRes as $matId => $numMP): ?>
                                    <div><input class="sm" type="checkbox" data-searchmaterial="<?php echo $numMP['name'] . "(". $numMP['count'] .")"; ?>" name="<?php echo $matId ?>" value="<?php echo $matId ?>" /> <p class="sm" data-searchmaterial="<?php echo $numMP['name'] . "(". $numMP['count'] .")"; ?>" style="display:inline;"><?php echo $numMP['name'] . "(". $numMP['count'] .")"; ?> </p></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php"?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Elimina categorie">
        <bs-toolbar-button
                data-remote="bs.delete.category.research.fason"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>