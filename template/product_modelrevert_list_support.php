<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"?>
    <?php echo $app->getAssets(['ui','forms','tables'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
    <script type="text/javascript" src="<?php echo \Monkey::app()->baseUrl(false)."/blueseal/assets/js/product_model_list.js"; ?>" ></script>
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
                        <div class="row" align="center" style="padding-top: 130px;">
                            <div class="col-md-2">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="detailLabelId">Seleziona l'etichetta Dettagli</label>
                                    <select id="detailLabelId" name="detailLabelId"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                        <?php echo '<option   value="">Seleziona</option>';
                                        foreach ($detailLabel as $label) {
                                            if ($label->name == $detailLabelId) {
                                                echo '<option  selected="selected" value="' . $label->name . '">' . $label->name . '</option>';
                                            } else {
                                                echo '<option  value="' . $label->name . '">' . $label->name . '</option>';
                                            }
                                        }; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="selectDefine">Condizione Se(Valore Dettaglio)</label>
                                    <select id="selectDefine" name="selectDefine"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">

                                        <option value="">seleziona se</option>
                                        <?if ($selectDefine==1){
                                        echo '<option selected="selected" value="1">Contiene</option>';
                                        }else{
                                        echo '<option  value="1">Contiene</option>';
                                        }
                                        if ($selectDefine==0){
                                        echo '<option selected="selected" value="0">Non Contiene</option>';
                                        }else{
                                        echo '<option  value="0"> Non Contiene</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default">
                                    <label for="textDefine">Testo Valore Dettaglio</label>
                                    <input type="text" id="textDefine" name="textDefine" value="<?php echo $textDefine?>"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="success" id="btnsearchplus"  name ='btnsearchplus' type="button"><span  class="fa fa-search-plus"> Esegui Ricerca</span></button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <table class="table table-striped responsive" width="100%"
                               data-datatable-name="product_model_list"
                               data-controller="ProductModelRevertListSupportAjaxController"
                               data-url="<?php echo $app->urlForBluesealXhr() ?>"
                               data-inner-setup="true"
                               data-detailLabelId="<?php echo $detailLabelId?>"
                               data-selectDefine="<?php echo $selectDefine?>"
                               data-textDefine="<?php echo $textDefine?>"
                               data-length-menu-setup="10,20,50, 100, 250, 1000, 1500, 2000">
                            <thead>
                            <tr>
                                <th data-slug="id"
                                    data-searchable="true"
                                    data-orderable="true"
                                    class="center">id</th>
                                <th data-slug="gendName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Genere</th>
                                <th data-slug="macroCategory"
                                    data-searchable="true"
                                    data-orderable="true" class="center">MacroCategoria</th>
                                <th data-slug="imageUrlMacroCategory"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Immagine MacroCategoria</th>
                                <th data-slug="catGroupName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Categoria</th>
                                <th data-slug="imageUrlCategory"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Immagine Categoria</th>
                                <th data-slug="matName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Materiale</th>
                                <th data-slug="code"
                                    data-searchable="true"
                                    data-orderable="true"
                                    data-default-order="desc" class="center">Codice</th>
                                <th data-slug="name"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome Modello</th>
                                <th data-slug="productName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Nome Prodotto</th>
                                <th data-slug="prototypeName"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Scheda Prodotto</th>
                                <th data-slug="details"
                                    data-searchable="true"
                                    data-orderable="true" class="center">Dettagli</th>
                                <th data-slug="categories"
                                    data-searchable="true"
                                    data-orderable="true" class="center categoryFilterType">Categorie<br>di<br>navigazione</th>


                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
    <bs-toolbar-group data-group-label="Gestione prodotti">
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Aggiungi un nuovo modello"
            data-placement="bottom"
            data-href="/blueseal/prodotti/modelli/modifica"
            ></bs-toolbar-button>
        <bs-toolbar-button
            data-tag="a"
            data-icon="fa-file-o fa-plus"
            data-permission="/admin/product/add"
            data-class="btn btn-default"
            data-rel="tooltip"
            data-title="Lista delle Categorie per numero di modelli assegnati"
            data-placement="bottom"
            data-href="/blueseal/prodotti/modelli/modifica"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestione visualizzazione elementi">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-minus"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs-hide-model-prototype"
                data-title="Nascondi elementi"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestione massiva modelli">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-exchange"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs-massive-copy-model-prototype"
                data-title="Clona massivamente"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-cloud-upload"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs-massive-update-model-prototype"
                data-title="Aggiorna massivamente"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-database"
                data-permission="/admin/product/add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-event="bs-massive-update-copy-model-prototype"
                data-title="Aggiorna e clona massivamente"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Gestione categorie">
        <bs-toolbar-button
                data-remote="bs.product.sheet.model.prototype.category.change"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>