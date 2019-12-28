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
                                    <h5 class="m-t-10">Modifica Acquisto</h5>
                                </div>
                                <div class="panel-body clearfix">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <input type="hidden" id="invoiceId" name="invoiceId" value="<?php echo $invoice->id?>"/>
                                            <input type="hidden" id="headInvoiceText" name="headInvoiceText" value="<?php echo htmlentities($headInvoiceText);?>"/>
                                            <input type="hidden" id="footerInvoiceText" name="footerInvoiceText" value="<?php echo htmlentities($footerInvoiceText);?>"/>
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="invoiceType">Sezionale</label>
                                                <input id="invoiceType" class="form-control" type="text"
                                                       value="<?php echo $invoice->invoiceType ?>"
                                                       placeholder="Inserisci il tipo di Fattura invoiceSiteChar "
                                                       name="invoiceType"
                                                       required="required">
                                                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="invoiceSiteChar">Sezionale Sito</label>
                                                <input id="invoiceSiteChar" class="form-control" type="text"
                                                       value="<?php echo $invoice->invoiceSiteChar ?>"
                                                       placeholder="inserici il tipo di Fattura Sito "
                                                       name="invoiceSiteChar"
                                                       required="required">
                                                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="invoiceNumber">Numero Fattura </label>
                                                <input id="invoiceNumber" class="form-control" type="text"
                                                       value="<?php echo $invoice->invoiceNumber ?>"
                                                       placeholder="inserici il numero Fattura "
                                                       name="invoiceNumber"
                                                       required="required">
                                                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="invoiceYear">Anno Fattura </label>
                                                <input id="invoiceYear" class="form-control" type="text"
                                                       value="<?php echo $invoice->invoiceYear ?>"
                                                       placeholder="inserici l'\anno di Fatturazione"
                                                       name="invoiceYear"
                                                       required="required">
                                                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="invoiceDate">Data Fattura</label>
                                                <input type="datetime-local" class="form-control" id="invoiceDate" name="invoiceDate" value="<?php echo str_replace(" ", "T", $invoice->invoiceDate) ?>" />
                                                <span class="bs red corner label"><i class="fa fa-asterisk"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group form-group-default selectize-enabled">
                                                <label for="shopId">Shop di riferimento</label>
                                                <select class="full-width"
                                                        placeholder="Seleziona lo shop"
                                                        data-init-plugin="selectize" title="" name="shopId" id="shopId"
                                                        required>

                                                    <?php echo'<option  value="' . $shops->id . '">' . $shops->title . '</option>';?>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $i=0;
                                     foreach ($orderLines as $orderLine){
                                        echo '<div class="row">';
                                        echo '<div class="col-md-4">';
                                        echo '<div align="center">';
                                        $productSku = \bamboo\domain\entities\CProductSku::defrost($orderLine->frozenProduct);
                                //$productSku=\Monkey::app()->repoFactory->create('ProductSku')->findOneBy(['productId' => $orderLine->productId , 'productVariantId '=> $orderLine->productVariantId , 'productSizeId' => $orderLine->productSizeId]);


                                $productNameTranslation = $productRepo->findOneBy(['productId' => $productSku->productId, 'productVariantId' => $productSku->productVariantId, 'langId' => '1']);
                                echo (($productNameTranslation) ? $productNameTranslation->name : '') . ($orderLine->warehouseShelfPosition ? ' / ' . $orderLine->warehouseShelfPosition->printPosition() : '') . '<br />' . $productSku->product->productBrand->name . ' - ' . $productSku->productId . '-' . $productSku->productVariantId;

                                    echo '</div>';
                                    echo '</div>';
                                    echo '<div class="col-md-4">';
                                    echo '<div align="center">';
                                    }
                                     ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div align="center"
                                            <label for="invoiceText">Testo Fattura</label>
                                            <textarea id="invoiceText" name="invoiceText" data-json="PostTranslation.content"><?php echo  $bodyInvoiceText;?>
                                        </textarea>
                                        </div>
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
    <bs-toolbar class="toolbar-definition">
        <bs-toolbar-group data-group-label="Salva il Movimento">
            <bs-toolbar-button
                    data-tag="a"
                    data-icon="fa-floppy-o"
                    data-permission="allShops||worker"
                    data-class="btn btn-default"
                    data-rel="tooltip"
                    data-event="bs.invoiceText.save"
                    data-title="Salva la Fattura"
                    data-placement="bottom"
                    data-href="#"
            ></bs-toolbar-button>
        </bs-toolbar-group>
    </bs-toolbar>
</body>
</html>