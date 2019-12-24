<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms', 'tables'], $page); ?>
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
                                    <h5 class="m-t-10">Aggiungi un Template </h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="name">Nome template</label>
                                                <input id="name" class="form-control"
                                                       placeholder="Inserisci il nome del file  template" name="name"
                                                       required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="shopId">Seleziona lo shop dove il template deve essere utilizzato</label>
                                                <select id="shopId" name="shopId"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona la Lista"
                                                        data-init-plugin="selectize">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="scope">Utilizzo Template</label>
                                                <input id="scope" class="form-control"
                                                       placeholder="Inserisci lo scopo " name="scope"
                                                       required="required">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="subject">Oggetto Email</label>
                                                <input id="subject" class="form-control"
                                                       placeholder="Inserisci la descrizione del file  template" name="subject"
                                                       required="required">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="isActive">Seleziona se attivo</label>
                                                <select id="isActive" name="isActive"
                                                        class="full-width selectpicker"
                                                        placeholder="Seleziona"
                                                        data-init-plugin="selectize"
                                                <option value=""></option>
                                                <option value="1">Si</option>
                                                <option value="2">No</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="description">Descrizione</label>
                                                <input id="description" class="form-control"
                                                       placeholder="Inserisci la descrizione del file  template" name="description"
                                                       required="required">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">

                                            <label for="template">Template Default</label>
                                            <textarea id="template" name="template" data-json="PostTranslation.content"
                                                      rows="50"></textarea>
                                        </div>
                                    </div>
                                    <div id="templateLanguage">
                                        <input type="hidden" id="langarray" name="langarray" value="<?php echo $arrayl;?>"/>
                                        <?php
                                        foreach($languages as $key => $row){
                                            echo '<div class="row">';
                                            echo '<div class="col-md-12">';
                                            echo '<label for="'.$row['lang'].'">'.$row['name'].'</label>';
                                            echo '<textarea id="'.$row['lang'].'" name="'.$row['lang'].'" data-json="PostTranslation.content" rows="50"></textarea>';
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
        <bs-toolbar-group data-group-label="Gestione Email Template">
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-file-o fa-plus"
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