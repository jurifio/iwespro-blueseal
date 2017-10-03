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
                                    <input type="text" class="form-control" name="ProductSizeGroup_macroName"
                                           value="<?php echo isset($sizeEdit) ? $sizeEdit->getFirst()->macroName : "" ?>"
                                           required title="">
                                </div>
                            </div>
                        </div>
                        <p>Taglie incluse</p>
                        <div class="table-responsive">
                            <div class="dataTables_wrapper no-footer">
                                <table class="table table-hover table-condensed dataTable no-footer" id="condensedTable"
                                       role="grid">
                                    <thead>
                                    <tr role="row">
                                        <th style="width:30%" class="sorting_asc" tabindex="0"
                                            aria-controls="condensedTable" rowspan="1" colspan="1" aria-sort="ascending"
                                            aria-label="Title: activate to sort column descending">Title
                                        </th>
                                        <th style="width: 189px;" class="sorting" tabindex="0"
                                            aria-controls="condensedTable" rowspan="1" colspan="1"
                                            aria-label="Key: activate to sort column ascending">Key
                                        </th>
                                        <th style="width: 266px;" class="sorting" tabindex="0"
                                            aria-controls="condensedTable" rowspan="1" colspan="1"
                                            aria-label="Condensed: activate to sort column ascending">Condensed
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    <tr role="row" class="odd">
                                        <td class="v-align-middle semi-bold sorting_1">Fifth tour</td>
                                        <td class="v-align-middle">Simple but not simpler</td>
                                        <td class="v-align-middle semi-bold">Wonders can be true. Believe in your
                                            dreams!
                                        </td>
                                    </tr>
                                    <tr role="row" class="even">
                                        <td class="v-align-middle semi-bold sorting_1">First tour</td>
                                        <td class="v-align-middle">Simple but not simpler</td>
                                        <td class="v-align-middle semi-bold">Wonders can be true. Believe in your
                                            dreams!
                                        </td>
                                    </tr>
                                    <tr role="row" class="odd">
                                        <td class="v-align-middle semi-bold sorting_1">Fourth tour</td>
                                        <td class="v-align-middle">Simple but not simpler</td>
                                        <td class="v-align-middle semi-bold">Wonders can be true. Believe in your
                                            dreams!
                                        </td>
                                    </tr>
                                    <tr role="row" class="even">
                                        <td class="v-align-middle semi-bold sorting_1">Second tour</td>
                                        <td class="v-align-middle">Simple but not simpler</td>
                                        <td class="v-align-middle semi-bold">Wonders can be true. Believe in your
                                            dreams!
                                        </td>
                                    </tr>
                                    <tr role="row" class="odd">
                                        <td class="v-align-middle semi-bold sorting_1">Third tour</td>
                                        <td class="v-align-middle">Simple but not simpler</td>
                                        <td class="v-align-middle semi-bold">Wonders can be true. Believe in your
                                            dreams!
                                        </td>
                                    </tr>
                                    </tbody>
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
</body>
</html>