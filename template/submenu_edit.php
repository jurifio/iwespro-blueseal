<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms'],$page); ?>
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

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5>Modifica SubMenu</h5>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                              autocomplete="off">
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group form-group-default">
                                                <label for="slug">Slug</label>
                                                ​<input type="text" class="form-control" id="slug" name="slug"
                                                        required="required"
                                                        value="<?php echo $menuNav->slug?>"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group form-group-default">
                                                <label for="captionTitle">Titolo</label>
                                                ​<input type="text" class="form-control" id="captionTitle"
                                                        name="captionTitle"
                                                        required="required"
                                                        value="<?php echo $menuNav->captionTitle;?>"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="chooseOperation">Scegli Immagine</label>
                                                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                <select class="full-width selectpicker"
                                                        placeholder="Seleziona il livello"
                                                        data-init-plugin="selectize" tabindex="-1" title="sortingId"
                                                        name="chooseOperation" id="chooseOperation"
                                                        required="required">
                                                    <option value="2">Non Caricare nulla</option>
                                                    <option value="1">carica Nuova Immagine</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <?php if($menuNav->captionImage!='/assets/px.png') {
                                                    echo '<img width="150px" src="https://'.$_SERVER['HTTP_HOST'].'/assets/'.$val->captionImage.'"/>';
                                                }
                                                ?>
                                            </div>
                                        <div id="divUploadImage" class="col-sm-3 hide">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="photoUrl">Caricamento Immagini</label>
                                                    <div class="fallback">
                                                        <input id="photoUrl" name="photoUrl" type="text" value="$menuNav->captionImage"/>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="menuId">Seleziona il menu Padre</label>
                                                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                <select class="full-width selectpicker"
                                                        placeholder="Seleziona il menu Padre"
                                                        data-init-plugin="selectize" tabindex="-1" title="menu Padre"
                                                        name="menuId" id="menuId"
                                                        required="required">
                                                    <?php foreach ($menus as $menu) {
                                                        if($menu->id==$menuNav->menuId) {
                                                            echo '<option selected="selected" value="' . $menu->id . '">' . $menu->name . '</option>';
                                                        }else{
                                                            echo '<option value="' . $menu->id . '">' . $menu->name . '</option>';
                                                        }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="typeId">Seleziona il tipo di Sotto Menu</label>
                                                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                                <select class="full-width selectpicker"
                                                        placeholder="Seleziona il tipo di SottoMenu"
                                                        data-init-plugin="selectize" title="typeId"
                                                        name="typeId" id="typeId"
                                                        required="required">
                                                    <?php foreach ($menuNavType as $menunavTypes) {
                                                        if($menunavTypes->id==$menuNav->typeId) {
                                                            echo '<option selected="selected" value="' . $menunavTypes->id . '">' . $menunavTypes->name . '</option>';
                                                        }else{
                                                            echo '<option value="' . $menunavTypes->id . '">' . $menunavTypes->name . '</option>';
                                                        }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div id="selectElement" class="col-sm-12">
                                            <div class="col-sm-6">
                                            <input type="hidden" id="elementId" name="elementId" value="<?php echo $menuNav->elementId;?>"/>
                                            </div>
                                            <div class="col-sm-6">
                                            <input type="text" id="captionLink" name="captionLink" value="<?php echo $menuNav->captionLink;?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <?php
                                    foreach ($langs as $lang): ?>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group form-group-default">
                                                    <label><?php echo strtoupper($lang->name); ?></label>
                                                    <input
                                                        <?php if ($lang->id == 1) echo 'required="required" '; ?>type="text"
                                                        class="form-control"
                                                        name="tagName_<?php echo $lang->id; ?>"
                                                        value="">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php"; ?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione Menu">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-event="bs.submenu.add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>