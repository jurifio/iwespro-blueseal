<div class="row">
    <div class="col-md-12 detailContent" data-prototype-id="<?php if (isset($productSheetPrototype)) echo $productSheetPrototype->id; ?>" data-product-name="<?php echo $productName ?>">
        <div class="form-group form-group-default selectize-enabled">
            <label for="Product_dataSheet">Tipo scheda prodotto</label>
            <select class="full-width selectpicker Product_dataSheet" id="Product_dataSheet"
                    placeholder="Seleziona una scheda prodotto"
                    data-init-plugin="selectize" title="" name="Product_dataSheet" id="Product_dataSheet"
            >
                <option value=""></option>
                <?php foreach ($productSheets as $productSheet): ?>
                    <option value="<?php echo $productSheet->id ?>"><?php echo $productSheet->name ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>
<div class="row productDetails">
    <div class="col-md-12">
        <div class="tab-content bg-white">
            <?php foreach ($productSheetPrototype->productDetailLabel as $detaillabel): ?>
                <div class="col-md-6">
                    <div class="form-group form-group-default selectize-enabled">
                        <label
                            for="<?php echo "ProductDetail_1_" . $detaillabel->id ?>"><?php echo $detaillabel->slug ?></label>
                        <select type="text"
                                data-init-plugin="selectize"
                                data-init-selection="<?php echo (array_key_exists($detaillabel->id, $actual)) ? $actual[$detaillabel->id] : '' ?>"
                                class="full-width <?php echo "ProductDetail_1_" . $detaillabel->id ?>"
                                id="<?php echo "ProductDetail_1_" . $detaillabel->id ?>"
                                name="<?php echo "ProductDetail_1_" . $detaillabel->id ?>"
                                value="">
                            <option></option>
                        </select>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>