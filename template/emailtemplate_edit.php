<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms','tables'],$page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
    <!--<script src="https://cdn.ckeditor.com/4.8.0/standard-all/ckeditor.js"></script>-->
    <script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=z3tiwzxrspg36g21tiusdfsqt9f27isw6547l88aw19e0qej"></script>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php" ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">


            <div class="container-fluid container-fixed-lg bg-white">
                <form id="form-project" enctype="multipart/form-data" role="form" action="" method="post"
                      autocomplete="off">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default clearfix">
                                <div class="panel-heading clearfix">
                                    <h5 class="m-t-10">modifica un Template </h5>
                                    <input type="hidden" name="emailTemplateId"
                                           id="emailTemplateId"
                                           value="<?php echo $emailTemplate->id; ?>"/>
                                </div>
                            </div>
                            <div class="panel-body clearfix">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="name">Nome template</label>
                                            <input id="name" class="form-control"
                                                   placeholder="nome  template" name="name"
                                                   required="required"
                                                   value="<?php echo $emailTemplate->name; ?> ">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="isActive">Attivo</label>
                                            <select id="isActive" name="isActive"
                                                    class="full-width selectpicker"
                                                    placeholder="Seleziona la Lista"
                                            <?php if ($emailTemplate->isActive != 0) {
                                                $selectedYes = "selected=\"selected\"";
                                                $selectedNo = "";
                                            } else {
                                                $selectedNo = "selected=\"selected\"";
                                                $selectedYes = "";
                                            }

                                            ?>

                                            <option value=""></option>
                                            <option value=""></option>
                                            <option value="1"<?php echo $selectedYes; ?>>Si</option>
                                            <option value="0"<?php echo $selectedNo; ?>>No</option>
                                            data-init-plugin="selectize">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="subject">Oggetto</label>
                                            <input id="subject" class="form-control"
                                                   placeholder="Oggetto Email  template" name="subject"
                                                   required="required"
                                                   value="<?php echo $emailTemplate->subject; ?> ">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="description">Descrizione</label>
                                            <input id="description" class="form-control"
                                                   placeholder="Descrizione Email  template" name="description"
                                                   required="required"
                                                   value="<?php echo $emailTemplate->description; ?> ">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="scope">scopo </label>
                                            <input id="scope" class="form-control"
                                                   placeholder="Scopo Email  template" name="scope"
                                                   required="required"
                                                   value="<?php echo $emailTemplate->scope; ?> ">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-group-default selectize-enabled">
                                            <label for="shopId">Shop di riferimento</label>
                                            <select class="full-width"
                                                    placeholder="Seleziona lo shop"
                                                    data-init-plugin="selectize" title="" name="shopId" id="shopId"
                                                    required>
                                                <option></option>
                                                <?php foreach ($shops as $shop) {
                                                    if ($shop->id == $emailTemplate->shopId) {
                                                        echo '<option selected value="' . $shop->id . '">' . $shop->name . '</option>';
                                                    } else {
                                                        echo '<option  value="' . $shop->id . '">' . $shop->name . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">

                                        <label for="template">Template </label>
                                        <textarea id="template" name="template" data-json="PostTranslation.content"
                                                  rows="50"><?php echo $emailTemplate->template; ?></textarea>
                                    </div>

                                </div>
                                <div id="templateLanguage">
                                    <input type="hidden" id="langarray" name="langarray" value="<?php echo $arrayl;?>"/>
                                    <?php
                                    foreach($languages as $key => $row){
                                        echo '<div class="row">';
                                        echo '<div class="col-md-12">';
                                        echo '<label for="'.$row['id'].'">'.$row['name'].'</label>';
                                        echo '<textarea id="'.$row['id'].'" name="'.$row['id'].'" data-json="PostTranslation.content" rows="50">'.$row['value'].'</textarea>';
                                        echo '</div>';
                                        echo '</div>';
                                    }?>

                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            </form>
        </div>

    </div>

    <?php include "parts/footer.php"; ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Gestione NewsletterTemplate">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-save"
                data-permission="allShops||worker"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs.newEmailTemplate.save"
                data-title="Salva il Template"
                data-placement="bottom"
                data-href="#"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>