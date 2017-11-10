<?php
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
/** @var CRestrictedAccessWidgetHelper $app*/
/** @var $line \bamboo\domain\entities\COrderLine */
$sku = \bamboo\domain\entities\CProductSku::defrost($line->frozenProduct);
?>
<td class="center"><?php echo $line->id;?></td>
<td class="center"><a href="<?php echo $app->productBackofficeUrl($line->productId,$line->productVariantId) ?>" target="_blank" ><?php echo $sku->printPublicSku(); ?></a></td>
<td class="center"><?php echo '<span title="'.$line->orderLineStatus->description.'" data-toggle="tooltip" >'.$line->orderLineStatus->title.'<span>';?></td>
<td class="center">
    <?php if($line->isStatusManageable()): ?>
        <?php if(!is_null($line->getNextOkLineStatus())): ?>
        <button data-ajax="true"
                data-method="PUT"
                data-controller="ChangeLineStatus"
                data-always="reloadLineFromButton"
                data-address="<?php echo $app->urlForBluesealXhr() ?>"
                class="btn btn-primary btn-animated from-left fa fa-thumbs-o-up"
                value="<?php echo $line->id.'-'.$line->orderId.'-'.$line->getNextOkLineStatus()->id; ?>"
                data-toggle="tooltip"
                title="<?php echo $line->getNextOkLineStatus()->description; ?>"
                type="button"><span><?php echo $line->getNextOkLineStatus()->title; ?></span></button>
        <?php endif;
            if(!is_null($line->getNextErrLineStatus())):
        ?>
        <button data-ajax="true"
                data-method="PUT"
                data-controller="ChangeLineStatus"
                data-always="reloadLineFromButton"
                data-address="<?php echo $app->urlForBluesealXhr() ?>"
                class="btn btn-primary btn-animated from-left fa fa-thumbs-o-down"
                value="<?php echo $line->id.'-'.$line->orderId.'-'.$line->getNextErrLineStatus()->id; ?>"
                data-toggle="tooltip"
                title="<?php echo $line->getNextErrLineStatus()->description; ?>"
                type="button"><span><?php echo $line->getNextErrLineStatus()->title; ?></span></button>
        <?php endif; ?>
    <?php else: ?>
    <span>Non modificabile</span>
    <?php endif; ?>
</td>
<td class="center"><img width="90" src="<?php echo $app->image($line->product->getPhoto(1,281),'amazon') ?>" /></td>
<td class="center"><?php echo $line->product->productBrand->name ;?></td>
<td class="center"><?php echo $line->product->productSeason->name . " " . $line->product->productSeason->year; ?></td>
<td class="center"><?php echo $line->product->itemno ;?></td>
<td class="center"><?php
    if(!$line->isFriendChangable()) {
        echo $actualSku->shop->name;
    } else {
        /** Caso in cui ci sono piu shop possibili, select con bottone di conferma */
        $i = 0 ?>
        <form data-ajax="true" data-always="reloadLineFromForm" data-controller="ChangeLineShop" data-address="<?php echo $app->urlForBluesealXhr() ?>" enctype="multipart/form-data" role="form"  name="changeLineShop" method="PUT">
            <input type="hidden" name="orderId" value="<?php echo $line->orderId ?>" />
            <input type="hidden" name="orderLineId" value="<?php echo $line->id ?>" />
            <select id="select_shop" name="selectShop">
                <option value="0">Seleziona:</option>
                <?php
                foreach($line->skus as $sku): ?>
                    <option <?php if($sku->shopId == $line->shopId) echo 'selected="selected"'; ?> value="<?php echo $sku->shopId ?>"> <?php echo $sku->shop->name.' ('.number_format($sku->friendRevenue,2).') '.$sku->productSize->name; ?></option>
                <?php endforeach; ?>
            </select>
            <button id="changeShop" class="btn btn-success" type="submit"><i class="fa fa-random"></i></button>
        </form>
    <?php } ?>
    </td>
<td class="center"><?php echo $line->productSku->productSize->name . ' / '. $line->productSku->getPublicSize()->name;?></td>
<td class="center"><?php
        if (($line->isFriendChangable()) && (4 > $line->orderLineStatus->phase)) echo "Seleziona il Friend";
        else echo number_format($line->fullPrice, 2);
    ?></td>
<td class="center"><?php if (($line->isFriendChangable()) && (4 > $line->orderLineStatus->phase)) echo "Seleziona il Friend";
    else echo number_format($line->activePrice, 2); ?></td>
<td class="center"><?php if (($line->isFriendChangable()) && (4 > $line->orderLineStatus->phase)) echo "Seleziona il Friend";
    else echo number_format($line->netPrice, 2);?></td>
<td class="center"><?php if (($line->isFriendChangable()) && (4 > $line->orderLineStatus->phase)) echo "Seleziona il Friend";
    else echo number_format($line->cost, 2); ?></td>
<td class="center"><?php if (($line->isFriendChangable()) && (4 > $line->orderLineStatus->phase)) {
                            if (4 > $line->orderLineStatus->phase) echo "Seleziona il Friend";
                            else echo number_format($line->friendRevenue, 2);
                         } else { ?>
                            <form data-ajax="true" data-always="reloadLineFromForm" data-controller="ChangeFriendRevenue"
                                  data-address="<?php echo $app->urlForBluesealXhr() ?>"
                                  enctype="multipart/form-data" role="form"  name="changeLineShop" method="PUT">
                                <input type="hidden" name="orderId" value="<?php echo $line->orderId ?>" />
                                <input type="hidden" name="orderLineId" value="<?php echo $line->id ?>" />
                                <input type="text" name="change_revenue" value="<?php echo isset($line->friendRevenue) && $line->friendRevenue > 1 ? number_format($line->friendRevenue,2) : number_format($actualSku->friendRevenue,2) ?>" />
                                <button id="changeRevenue" class="btn btn-success" type="submit"><i class="fa fa-sliders"></i></button>
                            </form>
                   <?php } ?>
</td>