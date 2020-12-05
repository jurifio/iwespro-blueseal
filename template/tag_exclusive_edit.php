<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms'], $page); ?>
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

            <div class="container-fluid">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h5>Modifica Sezione Exclusivo</h5>
                    </div>
                    <div class="panel-body">
                        <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                              autocomplete="off">
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group form-group-default">
                                                <label for="exclusiven">Nome Sezione</label>
                                                ​<input type="text" class="form-control" id="exclusiven" name="exclusiven"
                                                        required="required"
                                                        placeholder="Nome delle Sezione"
                                                        value="<?php echo $tag->exclusiven; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group form-group-default">
                                                <label for="slug">Slug</label>
                                                ​<input type="text" class="form-control" id="slug" name="slug"
                                                        required="required"
                                                        placeholder="Minuscolo e  spazi con il -"
                                                        value="<?php echo $tag->slug; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="shopId">Shop di Destinazione</label>
                                                <select id="shopId" name="shopId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                        data-init-plugin="selectize">
                                                    <option value="">seleziona</option>
                                                    <?php
                                                    foreach($shops as $shop){
                                                        if($shop->id==$tag->shopId){
                                                            $selected='selected="selected"';
                                                        }else{
                                                            $selected='';
                                                        }
                                                        echo '<option '.$selected.' value="'.$shop->id.'">'.$shop->name.'</option>';
                                                    }?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group form-group-default">
                                                <label for="isPublic">Visibilità</label>
                                                <input type="checkbox" id="isPublic" name="isPublic" <?php if ($tag->isPublic) echo "checked=\"checked\" "; ?> />
                                            </div>
                                        </div>
                                    </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group form-group-default selectize-enabled">
                                                    <label for="storeHouseId">Rule StoreHouse</label>
                                                    <select id="storeHouseId" name="storeHouseId"
                                                            class="full-width selectpicker"
                                                            placeholder="Seleziona la Lista"
                                                            data-init-plugin="selectize">

                                                        <?php
                                                        foreach($storeHouses as $storeHouse){
                                                            if($storeHouse->id==$tag->storeHouseId){
                                                                $selected='selected="selected"';
                                                            }else{
                                                                $selected='';
                                                            }
                                                            echo '<option '.$selected.' value="'.$storeHouse->id.'">'.$storeHouse->name.'</option>';
                                                        }?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group form-group-default">
                                                    <label for="isActive">Attiva Rule StoreHouse</label>
                                                    <input type="checkbox" id="isActive" name="isactive" <?php if ($tag->isActive) echo "checked=\"checked\" "; ?> />
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                <div class="col-sm-4">
                                    <?php
                                    foreach ($langs as $lang):
                                        if (isset($name)) unset($name);
                                        foreach ($tagTrans as $val) {
                                            if ($val->langId == $lang->id) {
                                                $name = $val->name;
                                            }
                                        } ?>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group form-group-default">
                                                    <label><?php echo strtoupper($lang->name); ?></label>
                                                    <input <?php if($lang->id == 1) echo 'required="required" ';?>type="text" class="form-control"
                                                           name="tagExclusiveName_<?php echo $lang->id; ?>"
                                                           value="<?php echo isset($name) ? $name : "" ?>">
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
    </div>
    <?php include "parts/footer.php"; ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.tag.exclusive.edit"
                data-title="Salva"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>