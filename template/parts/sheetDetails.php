<div class="col-md-12 selectContent" data-prototype-id="<?php echo $productSheetPrototype->id; ?>">
    <div class="tab-content bg-white">
        <?php foreach ($productSheetPrototype->productDetailLabel as $detaillabel): ?>
            <div class="col-md-6">
                <div class="form-group form-group-default selectize-enabled">
                    <label
                        for="<?php echo "ProductDetail_1_" . $detaillabel->id ?>"><?php echo $detaillabel->slug ?></label>
                    <select type="text"
                            class="full-width"
                            data-init-plugin="selectize"
                            data-init-selection="<?php echo (array_key_exists($detaillabel->id, $actual)) ? $actual[$detaillabel->id] : '' ?>"
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