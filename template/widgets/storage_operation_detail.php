<?php /** @var \bamboo\domain\entities\CStorehouseOperation $storehouseOperation */ ?>
<div class="row">
    <div class="col-xs-12">
        Utente: <?php echo $storehouseOperation->user->getFullName() ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        Registrazione: <?php echo $storehouseOperation->creationDate ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        Data operazione: <?php echo $storehouseOperation->operationDate ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        Causale: <?php echo $storehouseOperation->storehouseOperationCause->name ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        Descrizione: <?php echo $storehouseOperation->storehouseOperationCause->description ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        Segno: <?php echo $storehouseOperation->storehouseOperationCause->sign ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        Note: <?php echo is_null($storehouseOperation->notes) ? "" : $storehouseOperation->notes ?>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        Prodotti movimentati: <br />
<?php foreach ($storehouseOperation->storehouseOperationLine as $storehouseOperationLine): ?>
    <?php echo $storehouseOperationLine->productSku->product->printId() . " / " . $storehouseOperationLine->productSku->product->productBrand->name . " / " . $storehouseOperationLine->productSku->productSize->name . " / " . $storehouseOperationLine->productSku->product->printCpf() . ": " . $storehouseOperationLine->qty . '<br />'; ?>
<?php endforeach; ?>
    </div>
</div>
