<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
    <div class="page-container">
        <?php include "parts/header.php"?>
        <?php include "parts/operations.php" ?>
        <div class="page-content-wrapper">
            <div class="content sm-gutter">
                <div class="container-fluid container-fixed-lg bg-white">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-4 alert-container closed">

                        </div>
                    </div>
                </div>

            <div class="container-fluid container-fixed-lg bg-white">
                <div class="panel panel-transparent">
                    <div class="panel-body">
                        <form id="form-project" role="form" action="" method="POST" autocomplete="off">
                            <div class="row clearfix">
                                <div class="col-md-12">
                                    <div class="form-group form-group-default required">
                                        <label>Nome MacroGruppo Taglie</label>
                                        <input type="text" class="form-control" name="ProductSizeGroup_macroName" value="<?php echo isset($sizeEdit) ? $sizeEdit->getFirst()->macroName : "" ?>" required title="">
                                    </div>
                                </div>
                            </div>
                            <p>Taglie incluse</p>
                            <div class="row clearfix">
                            <?php for($k=0;$k<18;$k++): ?>
                                <?php $actual = isset($sizeEdit) && $sizeEdit->valid() ? $sizeEdit->current() : false ; ?>
                                <?php if($actual): ?>
                                <input type="hidden" name="ProductSizeGroup_<?php echo $k?>_id" value="<?php echo $actual->id?>">
                                <?php endif;?>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <table class="table table-striped table-condensed">
                                            <thead>
                                                <tr class="center">
                                                    <th><?php echo $k+1?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td scope="col"><label>Nome</label><input class="form-control" type="text" id="<?php echo "ProductSizeGroup_".($k)."_name" ?>" name="<?php echo "ProductSizeGroup_".($k)."_name" ?>" value="<?php echo $actual ? $actual->name : ""?>"></td>
                                                </tr>
                                                <tr>
                                                    <td scope="col"><label>Locale</label><input class="form-control" type="text" id="<?php echo "ProductSizeGroup_".($k)."_locale" ?>" name="<?php echo "ProductSizeGroup_".($k)."_locale" ?>" value="<?php echo $actual ? $actual->locale : ""?>"></td>
                                                </tr>
                                                <?php for($i=0;$i<36;$i++):?>
                                                    <?php if(isset($actual) && isset($actual->productSizeGroupHasProductSize)) {
                                                        foreach ($actual->productSizeGroupHasProductSize as $key => $val) {
                                                            if ($val->position == $i) {
                                                                foreach ($actual->productSize as $val2) {
                                                                    if ($val->productSizeId == $val2->id) {
                                                                        $nomeTaglia = $val2->name;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }?>
                                                    <tr>
                                                        <td><label>Posizione <?php echo $i+1; ?></label><input class="form-control" type="text"  id="<?php echo "ProductSizeGroup_position_".$k."_".$i ?>" name="<?php echo "ProductSizeGroup_".($k)."_position_".$i ?>" value="<?php echo isset($nomeTaglia) ? $nomeTaglia : "" ?>" ></td>
                                                    </tr>
                                                <?php unset($nomeTaglia); endfor; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php
                                if(isset($sizeEdit) && $sizeEdit->valid()) $sizeEdit->next();
                                endfor; ?>
                            </div>
                            <?php if(isset($sizeEdit)):?>
                            <button class="btn btn-success" type="submit">Modifica</button>
                            <?php else: ?>
                            <button class="btn btn-success" type="submit">Crea</button>
                            <?php endif; ?>
                            <button class="btn btn-default"><i class="pg-close"></i> Cancella</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php"?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
</body>
</html>