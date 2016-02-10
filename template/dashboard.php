<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"; ?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
    <?php include "parts/sidebar.php";?>
    <div class="page-container">
        <?php include "parts/header.php"; ?>
        <?php include "parts/operations.php" ?>

        <div class="page-content-wrapper">
            <div class="content sm-gutter">

                <div class="container-fluid container-fixed-lg bg-white">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                    </div>
                </div>

                <div class="container-fluid padding-25 sm-padding-10">
                    <div class="row">
                        <div class="col-md-4 col-lg-3 col-xlg-2 ">
                            <div class="row">
                                <div class="col-md-12 m-b-10">
                                    <div class="widget-8 panel no-border bg-success no-margin widget-loader-bar">
                                        <div class="container-xs-height full-height">
                                            <div class="row-xs-height">
                                                <div class="col-xs-height col-top">
                                                    <div class="panel-heading top-left top-right">
                                                        <div class="panel-title text-black hint-text">
                                                            <span class="font-montserrat fs-11 all-caps">Vendite giornaliere <i class="fa fa-chevron-right"></i>
                                                            </span>
                                                        </div>
                                                        <div class="panel-controls">
                                                            <ul>
                                                                <li>
                                                                    <a data-toggle="refresh" class="portlet-refresh text-black" href="#"><i class="portlet-icon portlet-icon-refresh"></i></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row-xs-height ">
                                                <div class="col-xs-height col-top relative">
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="p-l-20">
                                                                <h3 class="no-margin p-b-5 text-white">&euro; 2.000</h3>
                                                                <p class="small hint-text m-t-5">
                                                                    <span class="label  font-montserrat m-r-5">60%</span>più alte
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                        </div>
                                                    </div>
                                                    <div class='widget-8-chart line-chart' data-line-color="black" data-points="true" data-point-color="success" data-stroke-width="2">
                                                        <svg></svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 m-b-10">
                                    <div class="widget-9 panel no-border bg-primary no-margin widget-loader-bar">
                                        <div class="container-xs-height full-height">
                                            <div class="row-xs-height">
                                                <div class="col-xs-height col-top">
                                                    <div class="panel-heading  top-left top-right">
                                                        <div class="panel-title text-black">
                                                            <span class="font-montserrat fs-11 all-caps">Vendite settimanali <i class="fa fa-chevron-right"></i></span>
                                                        </div>
                                                        <div class="panel-controls">
                                                            <ul>
                                                                <li><a href="#" class="portlet-refresh text-black" data-toggle="refresh"><i class="portlet-icon portlet-icon-refresh"></i></a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row-xs-height">
                                                <div class="col-xs-height col-top">
                                                    <div class="p-l-20 p-t-15">
                                                        <h3 class="no-margin p-b-5 text-white">&euro; 12.000</h3>
                                                        <a href="#" class="btn-circle-arrow text-white"><i class="pg-arrow_minimize"></i></a>
                                                        <span class="small hint-text">20% più alte dell'ultimo mese</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row-xs-height">
                                                <div class="col-xs-height col-bottom">
                                                    <div class="progress progress-small m-b-20">
                                                        <!-- START BOOTSTRAP PROGRESS (http://getbootstrap.com/components/#progress) -->
                                                        <div class="progress-bar progress-bar-white" data-percentage="20%"></div>
                                                        <!-- END BOOTSTRAP PROGRESS -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 m-b-10">
                                    <div class="widget-9 panel no-border bg-white no-margin widget-loader-bar">
                                        <div class="container-xs-height full-height">
                                            <div class="row-xs-height">
                                                <div class="col-xs-height col-top">
                                                    <div class="panel-heading top-left top-right">
                                                        <div class="panel-title text-black">
                                                            <span class="font-montserrat fs-11 all-caps">Vendite mensili <i class="fa fa-chevron-right"></i></span>
                                                        </div>
                                                        <div class="panel-controls">
                                                            <ul>
                                                                <li><a href="#" class="portlet-refresh text-black" data-toggle="refresh"><i class="portlet-icon portlet-icon-refresh"></i></a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row-xs-height">
                                                <div class="col-xs-height col-top">
                                                    <div class="p-l-20 p-t-15">
                                                        <h3 class="no-margin p-b-5 text-black">&euro; 48.000</h3>
                                                        <a href="#" class="btn-circle-arrow text-black"><i class="pg-arrow_minimize"></i></a>
                                                        <span class="small hint-text">10% più alte dell'ultimo mese</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row-xs-height">
                                                <div class="col-xs-height col-bottom">
                                                    <div class="progress progress-small m-b-20">
                                                        <div class="progress-bar progress-bar-black" data-percentage="10%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-5 col-xlg-6 m-b-10">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="widget-12 panel no-border widget-loader-circle no-margin">
                                        <div class="row">
                                            <div class="col-xlg-8 ">
                                                <div class="panel-heading pull-up top-right ">
                                                    <div class="panel-controls">
                                                        <ul>
                                                            <li class="hidden-xlg">
                                                                <div class="dropdown">
                                                                    <a data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
                                                                        <i class="portlet-icon portlet-icon-settings"></i>
                                                                    </a>
                                                                    <ul class="dropdown-menu pull-right" role="menu">
                                                                        <li><a href="#">Vendite</a>
                                                                        </li>
                                                                        <li><a href="#">Utenti</a>
                                                                        </li>
                                                                        <li><a href="#">Resi</a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <a data-toggle="refresh" class="portlet-refresh text-black" href="#"><i class="portlet-icon portlet-icon-refresh"></i></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-xlg-8 ">
                                                    <div class="p-l-10">
                                                        <h2 class="pull-left">Vendite</h2>
                                                        <h2 class="pull-left m-l-50 text-success">
                                                            <span class="bold">448</span>
                                                            <span class="text-success fs-12">+121</span>
                                                        </h2>
                                                        <div class="clearfix"></div>
                                                        <div class="full-width">
                                                            <ul class="list-inline">
                                                                <li><a href="#" class="font-montserrat text-master">1D</a>
                                                                </li>
                                                                <li class="active"><a href="#" class="font-montserrat bg-master-light text-master">5D</a>
                                                                </li>
                                                                <li><a href="#" class="font-montserrat text-master">1M</a>
                                                                </li>
                                                                <li><a href="#" class="font-montserrat text-master">1Y</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div class="nvd3-line line-chart text-center" data-x-grid="false">
                                                            <svg></svg>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xlg-4 visible-xlg">
                                                    <div class="widget-12-search">
                                                        <p class="pull-left">KPI
                                                            <span class="bold">monitorati</span>
                                                        </p>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                    <div class="company-stat-boxes m-t-50">
                                                        <div data-index="0" class="company-stat-box m-t-15 active padding-20 bg-master-lightest">
                                                            <div>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <i class="pg-close fs-12"></i>
                                                                </button>
                                                                <p class="company-name pull-left text-uppercase bold no-margin">
                                                                    <span class="fa fa-circle text-success fs-11"></span> Vendite
                                                                </p>
                                                                <small class="hint-text m-l-10">al netto dei resi</small>
                                                                <div class="clearfix"></div>
                                                            </div>
                                                            <div class="m-t-10">
                                                                <p class="pull-left small hint-text no-margin p-t-5">9:42 CET</p>
                                                                <div class="pull-right">
                                                                    <p class="small hint-text no-margin inline">37</p>
                                                                    <span class=" label label-success p-t-5 m-l-5 p-b-5 inline fs-12">+ 4</span>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                            </div>
                                                        </div>
                                                        <div data-index="1" class="company-stat-box m-t-15  padding-20 bg-master-lightest">
                                                            <div>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <i class="pg-close fs-12"></i>
                                                                </button>
                                                                <p class="company-name pull-left text-uppercase bold no-margin">
                                                                    <span class="fa fa-circle text-complete fs-11"></span> Resi
                                                                </p>
                                                                <small class="hint-text m-l-10">rimborsati</small>
                                                                <div class="clearfix"></div>
                                                            </div>
                                                            <div class="m-t-10">
                                                                <p class="pull-left small hint-text no-margin p-t-5">9:42 CET</p>
                                                                <div class="pull-right">
                                                                    <p class="small hint-text no-margin inline">8</p>
                                                                    <span class=" label label-warning p-t-5 m-l-5 p-b-5 inline fs-12">+ 1</span>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                            </div>
                                                        </div>
                                                        <div data-index="2" class="company-stat-box m-t-15  padding-20 bg-master-lightest">
                                                            <div>
                                                                <button type="button" class="close" data-dismiss="modal">
                                                                    <i class="pg-close fs-12"></i>
                                                                </button>
                                                                <p class="company-name pull-left text-uppercase bold no-margin">
                                                                    <span class="fa fa-circle text-primary fs-11"></span> Clienti
                                                                </p>
                                                                <small class="hint-text m-l-10">acquisizioni</small>
                                                                <div class="clearfix"></div>
                                                            </div>
                                                            <div class="m-t-10">
                                                                <p class="pull-left small hint-text no-margin p-t-5">9:42 CET</p>
                                                                <div class="pull-right">
                                                                    <p class="small hint-text no-margin inline">14</p>
                                                                    <span class=" label label-success p-t-5 m-l-5 p-b-5 inline fs-12">+ 3</span>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4 m-b-10">
                            <div class="row">
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
                                            <h2 class="text-success no-margin">Il Salvagente</h2>
                                            <p class="no-margin">Vendite di oggi</p>
                                        </div>
                                        <h3 class="pull-right semi-bold"><sup><small class="semi-bold">&euro;</small></sup> 102,967</h3>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="auto-overflow widget-11-2-table">
                                        <table class="table table-condensed table-hover">
                                            <tbody>
                                            <tr>
                                                <td class="font-montserrat all-caps fs-12 col-lg-3">Ordine #2345</td>
                                                <td class="text-right col-lg-4">
                                                    <span class="hint-text small">Pantaloni Antani</span>
                                                </td>
                                                <td class="text-right b-r b-dashed b-grey col-lg-2">
                                                    <span class="hint-text small">Qty 1</span>
                                                </td>
                                                <td class="col-lg-3">
                                                    <span class="font-montserrat fs-18">&euro;87</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font-montserrat all-caps fs-12 col-lg-3">Ordine #2345</td>
                                                <td class="text-right col-lg-4">
                                                    <span class="hint-text small">Pantaloni Antani</span>
                                                </td>
                                                <td class="text-right b-r b-dashed b-grey col-lg-2">
                                                    <span class="hint-text small">Qty 1</span>
                                                </td>
                                                <td class="col-lg-3">
                                                    <span class="font-montserrat fs-18">&euro;87</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font-montserrat all-caps fs-12 col-lg-3">Ordine #2345</td>
                                                <td class="text-right col-lg-4">
                                                    <span class="hint-text small">Pantaloni Antani</span>
                                                </td>
                                                <td class="text-right b-r b-dashed b-grey col-lg-2">
                                                    <span class="hint-text small">Qty 1</span>
                                                </td>
                                                <td class="col-lg-3">
                                                    <span class="font-montserrat fs-18">&euro;87</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font-montserrat all-caps fs-12 col-lg-3">Ordine #2345</td>
                                                <td class="text-right col-lg-4">
                                                    <span class="hint-text small">Pantaloni Antani</span>
                                                </td>
                                                <td class="text-right b-r b-dashed b-grey col-lg-2">
                                                    <span class="hint-text small">Qty 1</span>
                                                </td>
                                                <td class="col-lg-3">
                                                    <span class="font-montserrat fs-18">&euro;87</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font-montserrat all-caps fs-12 col-lg-3">Ordine #2345</td>
                                                <td class="text-right col-lg-4">
                                                    <span class="hint-text small">Pantaloni Antani</span>
                                                </td>
                                                <td class="text-right b-r b-dashed b-grey col-lg-2">
                                                    <span class="hint-text small">Qty 1</span>
                                                </td>
                                                <td class="col-lg-3">
                                                    <span class="font-montserrat fs-18">&euro;87</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="font-montserrat all-caps fs-12 col-lg-3">Ordine #2345</td>
                                                <td class="text-right col-lg-4">
                                                    <span class="hint-text small">Pantaloni Antani</span>
                                                </td>
                                                <td class="text-right b-r b-dashed b-grey col-lg-2">
                                                    <span class="hint-text small">Qty 1</span>
                                                </td>
                                                <td class="col-lg-3">
                                                    <span class="font-montserrat fs-18">&euro;87</span>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="padding-25">
                                        <p class="small no-margin">
                                            <a href="#"><i class="fa fs-16 fa-arrow-circle-o-down text-success m-r-10"></i></a>
                                            <span class="hint-text ">Vai ai dettagli degli ordini</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include "parts/footer.php"; ?>
    <?php include "parts/bsmodal.php"; ?>
    <?php include "parts/alert.php"; ?>
</body>
</html>