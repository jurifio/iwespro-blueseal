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
<td class="center">
    <?php $shipmentPrint='';
    $orderLineHasShipment=\Monkey::app()->repoFactory->create('OrderLineHasShipment')->findOneBy(['orderLineId'=>$line->id,'orderId'=>$line->orderId]);
    if(count($orderLineHasShipment)>0) {
        $shipment = \Monkey::app()->repoFactory->create('Shipment')->findOneBy(['id' => $orderLineHasShipment->shipmentId]);
        if ($shipment) {
            $findCarrier = \Monkey::app()->repoFactory->create('Carrier')->findOneBy(['id' => $shipment->carrierId]);
            $btnclass = 'btn btn-success';
            if ($shipment->deliveryDate != null && $shipment->shipmentDate != null) {
                $btnclass = 'btn btn-success';
            } else if ($shipment->deliveryDate == null && $shipment->shipmentDate != null) {
                $btnclass = 'btn btn-warning';
            } else {
                $btnclass = 'btn btn-light';
            }
            if ($shipment->carrierId == 2) {
                $shipmentPrint .= '<button style="width: 200px ; height:32px;"  onclick="openTrackGlsDelivery(\'' . $shipment->trackingNumber . '\');" class=' . $btnclass . '> <i class="fa fa-truck" aria-hidden="true"></i>' . $findCarrier->name . '-' . $shipment->trackingNumber . '</button><br>';
            } else {
                $shipmentPrint .= '<button style="width: 200px ; height:32px;"  onclick="openTrackDelivery(\'' . $shipment->trackingNumber . '\');" class=' . $btnclass . '> <i class="fa fa-truck" aria-hidden="true"></i>' . $findCarrier->name . '-' . $shipment->trackingNumber . '</button><br>';
                //  $shipmentCollect.= '<button onclick="openTrackDelivery(\'1Z463V1V6897807419\');" class="btn btn-light" role="button"><i class="fa fa-truck" aria-hidden="true"></i>1Z463V1V6897807419</button>';
            }

    $shipmentPrint .='<br><button style="width: 200px ; height:32px;"  onclick="createDelivery('.$line->orderId.','.$line->id.')" class="btn btn-light"><i class="fa fa-plus" aria-hidden="true"></i>Crea Spedizione</button>';
    $shipmentPrint.='<br><button style="width: 200px ; height:32px;"  onclick="modifyDelivery(' . $line->orderId . ','.$line->id.')" class="btn btn-light"><i class="fa fa-pencil" aria-hidden="true"></i>Modifica Spedizione</button>';
    $shipmentPrint.='<br><button style="width: 200px ; height:32px;"  onclick="addToOtherDelivery(' . $line->orderId . ','.$line->id.')" class="btn btn-light"><i class="fa fa-list" aria-hidden="true"></i>Accorpa Spedizione</button>';
    $shipmentPrint.='<br><button style="width: 200px ; height:32px;"  onclick="printShipment(' . $shipment->id .')" class="btn btn-light"><i class="fa fa-list" aria-hidden="true"></i>Accorpa Spedizione</button>';
    echo $shipmentPrint;
        }
    }
    ?>

</td>
<td class="center"><img width="90" src="<?php echo $app->image($line->product->getPhoto(1,281),'amazon') ?>" /></td>
<td class="center"><?php echo $line->product->productBrand->name ;?></td>
<td class="center"><?php echo $line->product->productSeason->name . " " . $line->product->productSeason->year; ?></td>
<td class="center"><?php echo $line->product->itemno ;?></td>
<td class="center"><?php
    if(!$line->isFriendChangable()) {
        echo $line->productSku->shop->name;
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
<td class="center"><?php
    $productSize=\Monkey::app()->repoFactory->create('ProductSize')->findOneBy(['id'=>$line->productSizeId]);
    echo $productSize->name . ' / '. $productSize->name;?></td>
<td class="center"><?php
        if (($line->isFriendChangable()) && (4 > $line->orderLineStatus->phase)) echo "Seleziona il Friend";
        else echo number_format($line->fullPrice, 2,'.');
    ?></td>
<td class="center"><?php if (($line->isFriendChangable()) && (4 > $line->orderLineStatus->phase)) echo "Seleziona il Friend";
    else echo number_format($line->activePrice, 2,'.'); ?></td>
<td class="center"><?php if (($line->isFriendChangable()) && (4 > $line->orderLineStatus->phase)) echo "Seleziona il Friend";
    else echo number_format($line->netPrice, 2,'.');?></td>
<td class="center"><?php if (($line->isFriendChangable()) && (4 > $line->orderLineStatus->phase)){
    echo "Seleziona il Friend";
    }else{ ?>
    <form data-ajax="true" data-always="reloadLineFromForm" data-controller="ChangeCostLine"
          data-address="<?php echo $app->urlForBluesealXhr() ?>"
          enctype="multipart/form-data" role="form"  name="changeLineCostShop" method="PUT">
        <input type="hidden" name="orderId" value="<?php echo $line->orderId ?>" />
        <input type="hidden" name="orderLineId" value="<?php echo $line->id ?>" />
        <input type="text" name="change_cost" value="<?php echo isset($line->cost) && $line->cost > 1 ? number_format($line->cost,2,'.','') : number_format($line->cost,2,'.','') ?>" />
        <button id="changeCost" class="btn btn-success" type="submit"><i class="fa fa-sliders"></i></button>
    </form>
    <?php } ?></td>

<?php
if(number_format($line->friendRevenue,0)==0){
    $find=0;
    $lastGoodPrice=0;
$orderLineFind=\Monkey::app()->repoFactory->create('OrderLine')->findBy([
        'productId'=>$line->productId,
        'productVariantId'=>$line->productVariantId,
        'productSizeId'=>$line->productSizeId]);
        foreach($orderLineFind as $findLastPrice){
            if($findLastPrice->friendRevenue>0){
                $lastGoodPrice=$findLastPrice->friendRevenue;
                $find=1;
                break;
            }
        }
        if($find==0){
            $shopFind=\Monkey::app()->repoFactory->create('Shop')->findOneBy(['id'=>$line->shopId]);
            $lastGoodPrice=$line->netPrice-($line->netPrice/100*20);
            $find=1;
        }
} ?>
<td class="center"><?php if (($line->isFriendChangable()) && (4 > $line->orderLineStatus->phase)) {
                            if (4 > $line->orderLineStatus->phase) echo "Seleziona il Friend";
                            else echo number_format($line->friendRevenue, 2,'.');
                         } else { ?>
                            <form data-ajax="true" data-always="reloadLineFromForm" data-controller="ChangeFriendRevenue"
                                  data-address="<?php echo $app->urlForBluesealXhr() ?>"
                                  enctype="multipart/form-data" role="form"  name="changeLineShop" method="PUT">
                                <input type="hidden" name="orderId" value="<?php echo $line->orderId ?>" />
                                <input type="hidden" name="orderLineId" value="<?php echo $line->id ?>" />
                                <input type="text" name="change_revenue" value="<?php echo isset($line->friendRevenue) && $line->friendRevenue > 1 ? number_format($line->friendRevenue,2,'.','') : number_format($lastGoodPrice,2,'.','') ?>" />
                                <button id="changeRevenue" class="btn btn-success" type="submit"><i class="fa fa-sliders"></i></button>
                            </form>
                   <?php } ?>
</td>
