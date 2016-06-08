<div class="row" id="productDetails">
    <div class="col-md-12">
        <div class="tab-content bg-white">
            <?php foreach ($productSheetPrototype->productDetailLabel as $detaillabel): ?>
                <div class="col-md-6">
                    <div class="form-group form-group-default">
                        <label for="<?php echo "ProductDetail_1_" . $detaillabel->id ?>"><?php echo $detaillabel->slug ?></label>
                        <select type="text"
                                class="form-control details-form selectpicker"
                                data-init-plugin = "selectize"
                                data-init-selection = ""
                                id="<?php echo "ProductDetail_1_" . $detaillabel->id ?>"
                                name="<?php echo "ProductDetail_1_" . $detaillabel->id ?>"
                                value="">
                            <option></option>
                        </select>
                    </div>
                </div>
                <?php  endforeach; ?>
        </div>
    </div>
</div>