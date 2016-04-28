<div style="display:none">
    <img src="/assets/img/blueseal_logo_symbol_black.png" />
</div>
<div class="header ">
    <div class="pull-left full-height visible-sm visible-xs">
        <div class="sm-action-bar">
            <a href="#" class="btn-link toggle-sidebar" data-toggle="sidebar">
                <span class="icon-set menu-hambuger"></span>
            </a>
        </div>
    </div>
    <!--<div class="pull-right full-height visible-sm visible-xs">
        <div class="sm-action-bar">
            <a href="#" class="btn-link" data-toggle="quickview" data-toggle-element="#quickview">
                <span class="icon-set menu-hambuger-plus"></span>
            </a>
        </div>
    </div>-->
    <div class="pull-left sm-table">
        <div class="header-inner">
            <div class="brand inline">
                <img class="shop-logo" src="<?php echo $app->getUserShopLogo(); ?>" alt="logo" data-src="<?php echo $app->getUserShopLogo(); ?>" data-src-retina="<?php echo $app->getUserShopLogo(); ?>">
            </div>
            <!--<a href="#" class="search-link" data-toggle="search"><i class="pg-search"></i>Scrivi ovunque per <span class="bold">cercare</span></a>-->
        </div>
    </div>

    <div class="pull-left sm-table">
        <div class="header-inner">
            <div id="sessionMonitor"></div>
        </div>
    </div>

    <!--<div class=" pull-right">
        <div class="header-inner">
            <a href="#" class="btn-link icon-set menu-hambuger-plus m-l-20 sm-no-margin hidden-sm hidden-xs" data-toggle="quickview" data-toggle-element="#quickview"></a>
        </div>
    </div>-->
    <div class="pull-right">
        <div class="visible-lg visible-md m-t-10 user-account">
            <div class="pull-left p-r-10 p-t-10 fs-16 font-heading">
                <span class="semi-bold"><?php echo $app->user()->userDetails->name?></span>
                <span class="text-master"><?php echo $app->user()->userDetails->surname?></span>
            </div>
            <div class="thumbnail-wrapper d32 circular inline m-t-5">
                <img src="/assets/img/profiles/default.jpg" alt="" data-src="/assets/img/profiles/default.jpg" data-src-retina="/assets/img/profiles/default.jpg" width="55" height="55">
            </div>
            <div class="pull-left p-r-10 p-t-10 fs-14 font-heading">
                &nbsp;<a href="<?php echo $app->urlFor('blueseal/logout') ?>"><span class="semi-bold">Esci</span></a>
            </div>
        </div>
    </div>
</div>