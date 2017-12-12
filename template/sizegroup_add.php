<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php" ?>
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
                        <div class="row clearfix">
                            <div class="col-md-12">
                                <div class="form-group form-group-default required">
                                    <label>Nome MacroGruppo Taglie</label>
                                    <input type="text" id="productSizeGroupMacroName" disabled="disabled"
                                           class="form-control" name="ProductSizeGroup_macroName"
                                           value="<?php echo isset($sizeEdit) ? $sizeEdit->getFirst()->productSizeMacroGroup->name : "" ?>"
                                           required title="">
                                </div>
                            </div>
                        </div>
                        <p>Taglie incluse</p>
                        <div class="table-responsive">
                            <div class="dataTables_wrapper no-footer">
                                <table class="table table-hover table-condensed size-table"
                                       role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th style="" class="sorting_asc" tabindex="0"
                                            aria-controls="condensedTable" rowspan="1" colspan="1"
                                            aria-sort="ascending"
                                            aria-label="Positions">ID
                                        </th>
                                        <?php
                                        /** @var \bamboo\domain\entities\CProductSizeGroup $productSizeGroup */
                                        $z = 0;
                                        foreach ($sizeEdit as $productSizeGroup): ?>
                                            <th style="" class="" tabindex=""
                                                aria-controls="condensedTable" rowspan="1" colspan="1"
                                                aria-sort=""
                                                aria-label="<?php echo $productSizeGroup->id ?>"><?php echo $productSizeGroup->id ?>
                                            </th>
                                            <?php $z++;
                                        endforeach;
                                        for (; $z < 18; $z++): ?>
                                            <th style="" class="" tabindex="0"
                                                aria-controls="condensedTable" rowspan="1" colspan="1"
                                                aria-sort="ascending"
                                                aria-label="Positions">
                                            </th>
                                        <?php endfor; ?>
                                    </tr>
                                    <tr role="row">
                                        <th style="" class="sorting_asc" tabindex="0"
                                            aria-controls="condensedTable" rowspan="1" colspan="1"
                                            aria-sort="ascending"
                                            aria-label="Positions">Locale
                                        </th>
                                        <?php
                                        /** @var \bamboo\domain\entities\CProductSizeGroup $productSizeGroup */
                                        $z = 0;
                                        foreach ($sizeEdit as $productSizeGroup): ?>
                                            <th style="" class="editable" data-name="locale" tabindex=""
                                                aria-controls="condensedTable" rowspan="1" colspan="1"
                                                aria-sort=""
                                                data-column="<?php echo $productSizeGroup->id ?>"
                                                aria-label="<?php echo $productSizeGroup->locale ?>"><?php echo $productSizeGroup->locale ?></th>
                                            <?php $z++;
                                        endforeach;
                                        for (; $z < 18; $z++): ?>
                                            <th style="" class="" tabindex="0"
                                                aria-controls="condensedTable" rowspan="1" colspan="1"
                                                aria-sort="ascending"
                                                aria-label="Positions">
                                            </th>
                                        <?php endfor; ?>
                                    </tr>
                                    <tr role="row">
                                        <th style="" class="sorting_asc" tabindex="0"
                                            aria-controls="condensedTable" rowspan="1" colspan="1"
                                            aria-sort="ascending"
                                            aria-label="Positions">Nome
                                        </th>
                                        <?php
                                        /** @var \bamboo\domain\entities\CProductSizeGroup $productSizeGroup */
                                        $z = 0;
                                        foreach ($sizeEdit as $productSizeGroup): ?>
                                            <th style="" class="editable" tabindex="" data-name="name"
                                                aria-controls="condensedTable" rowspan="1" colspan="1"
                                                aria-sort=""
                                                data-column="<?php echo $productSizeGroup->id ?>"
                                                aria-label="<?php echo $productSizeGroup->name ?>"><?php echo $productSizeGroup->name ?></th>
                                            <?php $z++;
                                        endforeach;
                                        for (; $z < 18; $z++): ?>
                                            <th style="" class="" tabindex="0"
                                                aria-controls="condensedTable" rowspan="1" colspan="1"
                                                aria-sort="ascending"
                                                aria-label="Positions">
                                            </th>
                                        <?php endfor; ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php for ($k = 0; $k < 51; $k++): ?>
                                        <tr data-position="<?php echo $k; ?>" role="row"
                                            class="<?php echo $k % 2 == 0 ? 'even' : 'odd' ?>">
                                            <td data-column="0"
                                                class="v-align-middle bold sorting_1"><?php echo $k ?></td>
                                            <?php $z = 0;
                                            foreach ($sizeEdit as $productSizeGroup):
                                                $productSizeGroupHasProductSize = $productSizeGroup->productSizeGroupHasProductSize->findOneByKey('position', $k);
                                                ?>
                                                <td data-productsizeid="<?php echo $productSizeGroupHasProductSize ? $productSizeGroupHasProductSize->productSize->id : '' ?>"
                                                    data-column="<?php echo $productSizeGroup->id ?>"
                                                    tabindex="<?php echo str_pad($z, 2, '0') . str_pad($k, 2, '0') ?>"
                                                    class="v-align-middle semi-bold sorting_1 edit-cell"><?php echo $productSizeGroupHasProductSize ? $productSizeGroupHasProductSize->productSize->name : '' ?></td>
                                                <?php $z++;
                                            endforeach;
                                            for (; $z < 18; $z++): ?>
                                                <td data-column="false"
                                                    class="v-align-middle bold sorting_1 edit-cell"></td>
                                            <?php endfor; ?>
                                        </tr>
                                    <?php endfor; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr role="row">
                                        <th style="" class="sorting_asc" tabindex="0"
                                            aria-controls="condensedTable" rowspan="1" colspan="1"
                                            aria-sort="ascending"
                                            aria-label="Positions">Posizioni
                                        </th>
                                        <?php
                                        /** @var \bamboo\domain\entities\CProductSizeGroup $productSizeGroup */
                                        foreach ($sizeEdit as $productSizeGroup): ?>
                                            <th style="" class="" tabindex="1"
                                                aria-controls="condensedTable" rowspan="1" colspan="1"
                                                aria-sort=""
                                                aria-label="<?php echo $productSizeGroup->locale ?>"><?php echo $productSizeGroup->locale ?>
                                            </th>
                                            <?php $z++;
                                        endforeach;
                                        for (; $z < 18; $z++): ?>
                                            <th style="" class="" tabindex="0"
                                                aria-controls="condensedTable" rowspan="1" colspan="1"
                                                aria-sort="ascending"
                                                aria-label="Positions">Nuovo Gruppo
                                            </th>
                                        <?php endfor; ?>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <table>

                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include "parts/footer.php" ?>
    </div>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Strumenti Colonna">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-plus"
                data-permission="/admin/content/add"
                data-event="bs-group-add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Aggiungi Gruppo"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-trash"
                data-permission="/admin/content/add"
                data-event="bs-group-delete"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Elimina Gruppo"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
    <bs-toolbar-group data-group-label="Strumenti Riga">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-outdent"
                data-permission="/admin/content/add"
                data-event="bs-group-row-add"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Inserisci Riga"
                data-placement="bottom"
        ></bs-toolbar-button>
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-strikethrough"
                data-permission="/admin/content/add"
                data-event="bs-group-row-delete"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Cancella Riga"
                data-placement="bottom"
        ></bs-toolbar-button>
    </bs-toolbar-group>
</bs-toolbar>
</body>
</html>