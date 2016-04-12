<div class="widget-11-2 panel no-border panel-condensed no-margin widget-loader-circle">
	<div class="panel-heading top-right">
		<div class="panel-controls">
			<ul>
				<li><a data-toggle="refresh" class="portlet-refresh text-black" href="#"><i class="portlet-icon portlet-icon-refresh"></i></a>
				</li>
			</ul>
		</div>
	</div>
	<div class="padding-25">
		<div class="pull-left">
			<h2 class="text-success no-margin"><?php echo $title ?></h2>
			<p class="no-margin">Ultime <?php echo $limit ?> Vendite</p>
		</div>
		<h3 class="pull-right semi-bold"><sup><small class="semi-bold">&euro;</small></sup> <?php echo $sum ?></h3>
		<div class="clearfix"></div>
	</div>
	<div class="auto-overflow widget-11-2-table">
		<table class="table table-condensed table-hover">
			<tbody>
            <?php foreach($orders as $orderLine): ?>
			<tr>
				<td class="text-left b-r b-dashed b-grey col-lg-2"><span class="hint-text small"><?php echo $orderLine->order->orderDate; ?></span></td>
				<td class="font-montserrat all-caps fs-12 col-lg-2"><?php echo $orderLine->printLineId(); ?></td>
				<td class="text-left col-lg-3">
					<span class="hint-text small"><?php echo $orderLine->productSku->product->productBrand->name; ?></span>
				</td>
				<td class="text-left b-r b-dashed b-grey col-lg-3">
					<span class="hint-text small"><?php echo $orderLine->productSku->product->itemno.' # '.$orderLine->productSku->product->productVariant->name; ?></span>
				</td>
				<td class="col-lg-2">
					<span class="font-montserrat fs-18">&euro;<?php echo $orderLine->show; ?></span>
				</td>
			</tr>
            <?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<!--<div class="padding-25">
		<p class="small no-margin">
			<a href="#"><i class="fa fs-16 fa-arrow-circle-o-down text-success m-r-10"></i></a>
			<span class="hint-text ">Vai ai dettagli degli ordini</span>
		</p>
	</div>-->
</div>