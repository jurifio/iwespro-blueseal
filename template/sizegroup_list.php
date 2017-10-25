<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','tables','forms'], $page); ?>
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
                        <div class="panel-heading">
                            <div class="panel-title">Elenco Gruppi Taglie</div>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped" id="tableWithExportOptions">
                                <thead>
                                <tr>
                                    <th>MacroNome</th>
                                    <th>Nome</th>
                                    <th>Locale</th>
                                    <th>Modifica</th>
                                    <th>Elimina</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($groups as $val): ?>
                                    <tr class="odd gradeX">
                                        <td class="center"><?php echo $val->macroName; ?></td>
                                        <td class="center"><?php echo $val->name; ?></td>
                                        <td class="center"><?php echo isset($val->locale) ? $val->locale : "" ; ?></td>
                                        <td class="center"><a href="<?php echo $modifica . "?productSizeGroupId=" . $val->id ?>"><i class="fa fa-pencil-square-o"></i></a></td>
                                        <td class="center"><a href="<?php echo $pageURL . "?del=Y&productSizeGroupId=" . $val->id ?>"><i class="fa fa-ban"></i></a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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