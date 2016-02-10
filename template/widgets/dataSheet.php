<ul class="nav nav-tabs nav-tabs-simple bg-white" id="tab-3">
    <?php foreach ($langs as $lang): ?>
        <li class="<?php echo($lang->lang == 'it' ? "active" : "") ?>">
            <a data-toggle="tab" href="#<?php echo $lang->lang ?>"><?php echo $lang->name ?></a>
        </li>
    <?php endforeach;
    $langs->rewind(); ?>
</ul>

<div class="tab-content bg-white">
    <?php foreach ($langs as $lang): ?>
        <div class="tab-pane <?php echo($lang->lang == 'it' ? "active" : "") ?>" id="<?php echo $lang->lang ?>">
            <?php foreach ($detailsGroups[$lang->lang] as $detail): ?>
                <div class="col-md-6">
                    <div class="form-group form-group-default">
                        <label
                            for="<?php echo "ProductDetail_" . $lang->id . "_" . $detail->id ?>"><?php echo $detail->name ?></label>
                        <?php if (isset($productEdit) && isset($productEdit->productAttributeValue)) {
                            foreach ($productEdit->productAttributeValue as $key => $val) {
                                if ($val->productAttributeId == $detail->id && $val->langId == $lang->id) {
                                    $detailValue = $val;
                                }
                            }
                        } ?>
                        <input autocomplete="off" type="text" class="form-control"
                               id="<?php echo "ProductDetail_" . $lang->id . "_" . $detail->id ?>"
                               name="<?php echo "ProductDetail_" . $lang->id . "_" . $detail->id ?>"
                               value="<?php echo isset($detailValue) ? $detailValue->name : "" ?>"/>
                    </div>
                </div>
                <?php unset($detailValue); endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>