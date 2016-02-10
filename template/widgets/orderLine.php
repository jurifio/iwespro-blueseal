<?php
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use redpanda\blueseal\business\COrderLineManager;
/** @var COrderLineManager $lineManager */
/** @var CRestrictedAccessWidgetHelper $app*/
$sku = unserialize($line->frozenProduct);
$sku->setEntityManager($app->application()->entityManagerFactory->create('ProductSku'));
?>
<td class="center"><?php echo $line->id;?></td>
<td class="center"><a href="<?php echo $app->productBackofficeUrl($line->productId,$line->productVariantId) ?>" target="_blank" ><?php echo $sku->printPublicSku(); ?></a></td>
<td class="center"><?php echo '<span title="'.$line->orderLineStatus->description.'" data-toggle="tooltip" >'.$line->orderLineStatus->title.'<span>';?></td>
<td class="center">
    <?php if($lineManager->isStatusManageable()): ?>
        <?php if(!is_null($lineManager->nextOk())): ?>
        <button data-ajax="true"
                data-method="PUT"
                data-controller="ChangeLineStatus"
                data-always="reloadLineFromButton"
                data-address="<?php echo $app->urlForBluesealXhr() ?>"
                class="btn btn-primary btn-animated from-left fa fa-thumbs-o-up"
                value="<?php echo $line->id.'-'.$line->orderId.'-'.$lineManager->nextOk()->id; ?>"
                data-toggle="tooltip"
                title="<?php echo $lineManager->nextOk()->description; ?>"
                type="button"><span><?php echo $lineManager->nextOk()->title; ?></span></button>
        <?php endif;
            if(!is_null($lineManager->nextErr())):
        ?>
        <button data-ajax="true"
                data-method="PUT"
                data-controller="ChangeLineStatus"
                data-always="reloadLineFromButton"
                data-address="<?php echo $app->urlForBluesealXhr() ?>"
                class="btn btn-primary btn-animated from-left fa fa-thumbs-o-down"
                value="<?php echo $line->id.'-'.$line->orderId.'-'.$lineManager->nextErr()->id; ?>"
                data-toggle="tooltip"
                title="<?php echo $lineManager->nextErr()->description; ?>"
                type="button"><span><?php echo $lineManager->nextErr()->title; ?></span></button>
        <?php endif; ?>
    <?php else: ?>
    <span>Non modificabile</span>
    <?php endif; ?>
</td>
<td class="center"><img width="90" src="<?php echo $app->image($line->product->getPhoto(1,281),'amazon') ?>" /></td>
<td class="center"><?php echo $line->product->productBrand->name ;?></td>
<td class="center"><?php echo $line->product->itemno ;?></td>
<td class="center"><?php
    $actualSku = $line->skus->getFirst();
    if(!$lineManager->isFriendChangable()) {
        echo $actualSku->shop->name;
    } else {
        /** Caso in cui ci sono piu shop possibili, select con bottone di conferma */
        $i = 0 ?>
        <form data-ajax="true" data-always="reloadLineFromForm" data-controller="ChangeLineShop" data-address="<?php echo $app->urlForBluesealXhr() ?>" enctype="multipart/form-data" role="form"  name="changeLineShop" method="PUT">
            <input type="hidden" name="orderId" value="<?php echo $line->orderId ?>" />
            <input type="hidden" name="orderLineId" value="<?php echo $line->id ?>" />
            <select id="select_shop" name="selectShop">
                <?php foreach($line->skus as $sku): ?>
                    <option <?php if($i == 0){ echo 'selected="selected"'; $i++; $actualSku = $sku; } ?> value="<?php echo $sku->shopId ?>"> <?php echo $sku->shop->name.' ('.number_format($sku->friendRevenue,2).')<br>'; ?></option>
                <?php endforeach; ?>
            </select>
            <button id="changeShop" class="btn btn-success" type="submit"><i class="fa fa-random"></i></button>
        </form>
    <?php } ?>
    </td>
<td class="center"><?php echo $line->productSize ;?></td>
<td class="center"><?php echo number_format($line->fullPrice,2); ?></td>
<td class="center"><?php echo number_format($line->activePrice,2); ?></td>
<td class="center"><?php echo number_format($line->netPrice,2);?></td>
<td class="center"><?php echo number_format($line->cost,2); ?></td>
<td class="center"><?php if(!$lineManager->isFriendValueChangable()) {
                            echo number_format($line->friendRevenue,2);
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
