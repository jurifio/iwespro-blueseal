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
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel-heading clearfix">
                            <h5 class="m-t-12">Informazioni di base</h5>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <input type="hidden" name="billRegistryProductId" id="billRegistryProductId"
                                       value="<?php echo $brp->id; ?>"/>
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="billRegistryGroupProductId">Seleziona il Gruppo Prodotti </label>
                                    <select id="billRegistryGroupProductId" name="billRegistryGroupProductId"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                        <?php foreach ($brgp as $groupProduct) {

                                            if ($groupProduct->id==$brp->billRegistryGroupProductId) {
                                                echo '<option selected="selected" value="' . $groupProduct->id . '">'.$groupProduct->codeProduct .'-'. $groupProduct->name . '</option>';
                                            } else {
                                                echo '<option value="' . $groupProduct->id . '">'.$groupProduct->codeProduct .'-'. $groupProduct->name . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-group-default selectize-enabled">
                                    <button class="btn btn-primary" id="addGroupProductId" onclick="addGroupProduct()"
                                            type="button"><span
                                                class="fa fa-plus-circle">Aggiungi Gruppo Prodotti</span></button>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="billRegistryCategoryProductId">Categoria Gruppo Prodotti</label>
                                    <select id="billRegistryCategoryProductId" name="billRegistryCategoryProductId"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                        <?php foreach ($brcp as $category) {

                                            if ($category->id == $brgpSelect->billRegistryCategoryProductId) {
                                                echo '<option value="' . $category->id . '" selected="selected">' . $category->name . '</option>';
                                            } else {
                                                echo '<option value="' . $category->id . '">' . $category->name . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group form-group-default selectize-enabled">
                                    <button class="btn btn-primary" id="addCategoryProductId"
                                            onclick="addCategoryProduct()" type="button"><span
                                                class="fa fa-plus-circle">Aggiungi Categoria Gruppo  Prodotti</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="codeProduct">Codice Prodotto</label>
                                    <input id="codeProduct" autocomplete="off" type="text"
                                           class="form-control" name="codeProduct"
                                           value="<?php echo $brp->codeProduct; ?> "
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="nameProduct">Nome Prodotto</label>
                                    <input id="nameProduct" autocomplete="off" type="text"
                                           class="form-control" name="nameProduct"
                                           value="<?php echo $brp->nameProduct; ?>"
                                    />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary" name="uploadLogo"
                                        id="uploadLogo">carica Immagine Prodotto
                                </button>
                                <input id="logoFile" type="hidden" value=""/>
                                <div id="returnFileLogo"><img src="<?php echo $brp->image; ?>"</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group form-group-default">
                                    <label for="um">unità di misura</label>
                                    <input id="um" autocomplete="off" type="text"
                                           class="form-control" name="um" value="<?php echo $brp->um; ?>"
                                    />

                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="cost">prezzo d'Acquisto</label>
                                    <input id="cost" autocomplete="off" type="text"
                                           class="form-control" name="cost" value="<?php echo $brp->cost; ?>"
                                    />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group form-group-default">
                                    <label for="price">Prezzo di Vendita</label>
                                    <input id="price" autocomplete="off" type="text"
                                           class="form-control" name="price" value="<?php echo $brp->price; ?>"
                                    />
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group form-group-default selectize-enabled">
                                    <label for="billRegistryTypeTaxesId">Seleziona Aliquota Iva</label>
                                    <select id="billRegistryTypeTaxesId" name="billRegistryTypeTaxesId"
                                            class="full-width selectpicker"
                                            placeholder="Seleziona la Lista"
                                            data-init-plugin="selectize">
                                        <?php foreach ($brtt as $taxes) {

                                            if ($taxes->id == $brp->billRegistryTypeTaxesId) {
                                                echo '<option value="' . $taxes->id . '" selected="selected">' . $taxes->description . '</option>';
                                            } else {
                                                echo '<option value="' . $taxes->id . '">' . $taxes->description . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-group-default">
                                    <label for="descriptionTemp">Descrizione</label>
                                    <input id="descriptionTemp" autocomplete="off" type="text"
                                           class="form-control" name="descriptionTemp"
                                           value="<?php echo $brp->description; ?>"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                            </div>
                            <div class="col-md-2">
                            </div>
                            <div class="col-md-2">
                            </div>
                            <div class="col-md-2">
                            </div>
                            <div class="col-md-2">
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary" id="addDescription" onclick="addDescription()"
                                        type="button"><span
                                            class="fa fa-plus-circle">Aggiungi Dettagli</span></button>

                            </div>
                        </div>
                        <div Class="row">
                            <br>
                        </div>

                        <div id="divDescription">
                            <?php
                            $i=1;
                            $descriptionArray='';
                            foreach ($brpd as $detaildesc) {
                                echo '<div class="row">';
                                echo '<div id="'.$i.'detaildiv" class="col-md-12">';
                                echo '<div class="form-group form-group-default">';
                                echo '<label for="detaildesc'.$i.'">Descrizione '.$i.'</label>';
                                echo '<textarea   id="detaildesc' . $i . '"  cols="120"  name="detaildesc' . $i.'">'.$detaildesc->detailDescription.'</textarea>';
                                echo '</div>';
                                echo'</div>';
                                echo'</div>';
                                $i++;
                                $descriptionArray.= $detaildesc->detailDescription.',';
                            }
                                echo '<input type="hidden" id="descdet" name"descdet" value="'.$i.'"/>';
                             echo '<input type="hidden" id="descriptionArray" name="descriptionArray" value="'.$descriptionArray.'"/>';

                            ?>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Operazioni Prodotti">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-event="bs.productIwes.save"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom">
        </bs-toolbar-button>
</bs-toolbar>
</body>
</html>