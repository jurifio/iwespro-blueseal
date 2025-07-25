<!--START QUICKVIEW -->
<div id="quickview" class="quickview-wrapper" data-pages="quickview">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs">
        <li class="">
            <a href="#quickview-notes" data-toggle="tab">Notes</a>
        </li>
        <li>
            <a href="#quickview-alerts" data-toggle="tab">Alerts</a>
        </li>
        <li class="active">
            <a href="#quickview-chat" data-toggle="tab">Chat</a>
        </li>
    </ul>
    <a class="btn-link quickview-toggle" data-toggle-element="#quickview" data-toggle="quickview"><i class="pg-close"></i></a>
    <!-- Tab panes -->
    <div class="tab-content">
        <!-- BEGIN Notes !-->
        <div class="tab-pane fade  in no-padding" id="quickview-notes">
            <div class="view-port clearfix quickview-notes" id="note-views">
                <!-- BEGIN Note List !-->
                <div class="view list" id="quick-note-list">
                    <div class="toolbar clearfix">
                        <ul class="pull-right ">
                            <li>
                                <a href="#" class="delete-note-link"><i class="fa fa-trash-o"></i></a>
                            </li>
                            <li>
                                <a href="#" class="new-note-link" data-navigate="view" data-view-port="#note-views" data-view-animation="push"><i class="fa fa-plus"></i></a>
                            </li>
                        </ul>
                        <button class="btn-remove-notes btn btn-xs btn-block" style="display:none"><i class="fa fa-times"></i> Delete</button>
                    </div>
                    <ul>
                        <!-- BEGIN Note Item !-->
                        <li data-noteid="1" data-navigate="view" data-view-port="#note-views" data-view-animation="push">
                            <div class="left">
                                <!-- BEGIN Note Action !-->
                                <div class="checkbox check-warning no-margin">
                                    <input id="qncheckbox1" type="checkbox" value="1">
                                    <label for="qncheckbox1"></label>
                                </div>
                                <!-- END Note Action !-->
                                <!-- BEGIN Note Preview Text !-->
                                <p class="note-preview">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam</p>
                                <!-- BEGIN Note Preview Text !-->
                            </div>
                            <!-- BEGIN Note Details !-->
                            <div class="right pull-right">
                                <!-- BEGIN Note Date !-->
                                <span class="date">12/12/14</span>
                                <a href="#"><i class="fa fa-chevron-right"></i></a>
                                <!-- END Note Date !-->
                            </div>
                            <!-- END Note Details !-->
                        </li>
                        <!-- END Note List !-->
                    </ul>
                </div>
                <!-- END Note List !-->
                <div class="view note" id="quick-note">
                    <div>
                        <ul class="toolbar">
                            <li><a href="#" class="close-note-link" data-navigate="view" data-view-port="#note-views" data-view-animation="push"><i class="pg-arrow_left"></i></a>
                            </li>
                            <li><a href="#" class="Bold"><i class="fa fa-bold"></i></a>
                            </li>
                            <li><a href="#" class="Italic"><i class="fa fa-italic"></i></a>
                            </li>
                            <li><a href="#" class=""><i class="fa fa-link"></i></a>
                            </li>
                        </ul>
                        <div class="body">
                            <div>
                                <div class="top">
                                    <span>21st april 2014 2:13am</span>
                                </div>
                                <div class="content">
                                    <div class="quick-note-editor full-width full-height js-input" contenteditable="true"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END Notes !-->
        <!-- BEGIN Alerts !-->
        <div class="tab-pane fade no-padding" id="quickview-alerts">
            <div class="view-port clearfix" id="alerts">
                <!-- BEGIN Alerts View !-->
                <div class="view bg-white">
                    <!-- BEGIN View Header !-->
                    <div class="navbar navbar-default navbar-sm">
                        <div class="navbar-inner">
                            <!-- BEGIN Header Controler !-->
                            <a href="javascript:;" class="inline action p-l-10 link text-master" data-navigate="view" data-view-port="#chat" data-view-animation="push-parrallax">
                                <i class="pg-more"></i>
                            </a>
                            <!-- END Header Controler !-->
                            <div class="view-heading">
                                Notications
                            </div>
                            <!-- BEGIN Header Controler !-->
                            <a href="#" class="inline action p-r-10 pull-right link text-master">
                                <i class="pg-search"></i>
                            </a>
                            <!-- END Header Controler !-->
                        </div>
                    </div>
                    <!-- END View Header !-->
                    <!-- BEGIN Alert List !-->
                    <div data-init-list-view="ioslist" class="list-view boreded no-top-border">
                        <!-- BEGIN List Group !-->
                        <div class="list-view-group-container">
                            <!-- BEGIN List Group Header!-->
                            <div class="list-view-group-header text-uppercase">
                                Calendar
                            </div>
                            <!-- END List Group Header!-->
                            <ul>
                                <!-- BEGIN List Group Item!-->
                                <li class="alert-list">
                                    <!-- BEGIN Alert Item Set Animation using data-view-animation !-->
                                    <a href="javascript:;" class="" data-navigate="view" data-view-port="#chat" data-view-animation="push-parrallax">
                                        <p class="col-xs-height col-middle">
                                            <span class="text-warning fs-10"><i class="fa fa-circle"></i></span>
                                        </p>
                                        <p class="p-l-10 col-xs-height col-middle col-xs-9 overflow-ellipsis fs-12">
                                            <span class="text-master">David Nester Birthday</span>
                                        </p>
                                        <p class="p-r-10 col-xs-height col-middle fs-12 text-right">
                                            <span class="text-warning">Today <br></span>
                                            <span class="text-master">All Day</span>
                                        </p>
                                    </a>
                                    <!-- END Alert Item!-->
                                    <!-- BEGIN List Group Item!-->
                                </li>
                                <!-- END List Group Item!-->
                            </ul>
                        </div>
                        <!-- END List Group !-->
                    </div>
                    <!-- END Alert List !-->
                </div>
                <!-- EEND Alerts View !-->
            </div>
        </div>
        <!-- END Alerts !-->
        <div class="tab-pane fade in active no-padding" id="quickview-chat">
            <div class="view-port clearfix" id="chat">
                <div class="view bg-white">
                    <!-- BEGIN View Header !-->
                    <div class="navbar navbar-default">
                        <div class="navbar-inner">
                            <!-- BEGIN Header Controler !-->
                            <a href="javascript:;" class="inline action p-l-10 link text-master" data-navigate="view" data-view-port="#chat" data-view-animation="push-parrallax">
                                <i class="pg-plus"></i>
                            </a>
                            <!-- END Header Controler !-->
                            <div class="view-heading">
                                Chat List
                                <div class="fs-11">Show All</div>
                            </div>
                            <!-- BEGIN Header Controler !-->
                            <a href="#" class="inline action p-r-10 pull-right link text-master">
                                <i class="pg-more"></i>
                            </a>
                            <!-- END Header Controler !-->
                        </div>
                    </div>
                    <!-- END View Header !-->
                    <div data-init-list-view="ioslist" class="list-view boreded no-top-border">
                        <div class="list-view-group-container">
                            <div class="list-view-group-header text-uppercase">a</div>
                            <ul>
                                <!-- BEGIN Chat User List Item  !-->
                                <li class="chat-user-list clearfix">
                                    <a data-view-animation="push-parrallax" data-view-port="#chat" data-navigate="view" class="" href="#">
                                            <span class="col-xs-height col-middle">
                                                <span class="thumbnail-wrapper d32 circular bg-success">
                                                    <img width="34" height="34" alt="" data-src-retina="/assets/img/profiles/1x.jpg" data-src="/assets/img/profiles/1.jpg" src="/assets/img/profiles/1x.jpg" class="col-top">
                                                </span>
                                            </span>
                                        <p class="p-l-10 col-xs-height col-middle col-xs-12">
                                            <span class="text-master">ava flores</span>
                                            <span class="block text-master hint-text fs-12">Hello there</span>
                                        </p>
                                    </a>
                                </li>
                                <!-- END Chat User List Item  !-->
                            </ul>
                        </div>
                        <div class="list-view-group-container">
                            <div class="list-view-group-header text-uppercase">b</div>
                            <ul>
                                <!-- BEGIN Chat User List Item  !-->
                                <li class="chat-user-list clearfix">
                                    <a data-view-animation="push-parrallax" data-view-port="#chat" data-navigate="view" class="" href="#">
                                            <span class="col-xs-height col-middle">
                                                <span class="thumbnail-wrapper d32 circular bg-success">
                                                    <img width="34" height="34" alt="" data-src-retina="assets/img/profiles/2x.jpg" data-src="/assets/img/profiles/2.jpg" src="/assets/img/profiles/2x.jpg" class="col-top">
                                                </span>
                                            </span>
                                        <p class="p-l-10 col-xs-height col-middle col-xs-12">
                                            <span class="text-master">bella mccoy</span>
                                            <span class="block text-master hint-text fs-12">Hello there</span>
                                        </p>
                                    </a>
                                </li>
                                <!-- END Chat User List Item  !-->
                                <!-- BEGIN Chat User List Item  !-->
                                <li class="chat-user-list clearfix">
                                    <a data-view-animation="push-parrallax" data-view-port="#chat" data-navigate="view" class="" href="#">
                                            <span class="col-xs-height col-middle">
                                                <span class="thumbnail-wrapper d32 circular bg-success">
                                                    <img width="34" height="34" alt="" data-src-retina="assets/img/profiles/3x.jpg" data-src="/assets/img/profiles/3.jpg" src="/assets/img/profiles/3x.jpg" class="col-top">
                                                </span>
                                            </span>
                                        <p class="p-l-10 col-xs-height col-middle col-xs-12">
                                            <span class="text-master">bob stephens</span>
                                            <span class="block text-master hint-text fs-12">Hello there</span>
                                        </p>
                                    </a>
                                </li>
                                <!-- END Chat User List Item  !-->
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- BEGIN Conversation View  !-->
                <div class="view chat-view bg-white clearfix">
                    <!-- BEGIN Header  !-->
                    <div class="navbar navbar-default">
                        <div class="navbar-inner">
                            <a href="javascript:;" class="link text-master inline action p-l-10" data-navigate="view" data-view-port="#chat" data-view-animation="push-parrallax">
                                <i class="pg-arrow_left"></i>
                            </a>
                            <div class="view-heading">John Smith<div class="fs-11 hint-text">Online</div>
                            </div>
                            <a href="#" class="link text-master inline action p-r-10 pull-right "><i class="pg-more"></i></a>
                        </div>
                    </div>
                    <!-- END Header  !-->
                    <!-- BEGIN Conversation  !-->
                    <div class="chat-inner" id="my-conversation">
                        <!-- BEGIN From Me Message  !-->
                        <div class="message clearfix">
                            <div class="chat-bubble from-me">
                                Hello there
                            </div>
                        </div>
                        <!-- END From Me Message  !-->
                        <!-- BEGIN From Them Message  !-->
                        <div class="message clearfix">
                            <div class="profile-img-wrapper m-t-5 inline">
                                <img class="col-top" width="30" height="30" src="<?php echo $app->urlForBlueseal() ?>/assets/img/profiles/avatar_small.jpg" alt="" data-src="<?php echo $app->urlForBlueseal() ?>/assets/img/profiles/avatar_small.jpg" data-src-retina="assets/img/profiles/avatar_small2x.jpg">
                            </div>
                            <div class="chat-bubble from-them">
                                Hey
                            </div>
                        </div>
                        <!-- END From Them Message  !-->
                        <!-- BEGIN From Me Message  !-->
                        <div class="message clearfix">
                            <div class="chat-bubble from-me">
                                Did you check out Pages framework ?
                            </div>
                        </div>
                        <!-- END From Me Message  !-->
                        <!-- BEGIN From Me Message  !-->
                        <div class="message clearfix">
                            <div class="chat-bubble from-me">
                                Its an awesome chat
                            </div>
                        </div>
                        <!-- END From Me Message  !-->
                        <!-- BEGIN From Them Message  !-->
                        <div class="message clearfix">
                            <div class="profile-img-wrapper m-t-5 inline">
                                <img class="col-top" width="30" height="30" src="<?php echo $app->urlForBlueseal() ?>/assets/img/profiles/avatar_small.jpg" alt="" data-src="<?php echo $app->urlForBlueseal() ?>/assets/img/profiles/avatar_small.jpg" data-src-retina="assets/img/profiles/avatar_small2x.jpg">
                            </div>
                            <div class="chat-bubble from-them">
                                Yea
                            </div>
                        </div>
                        <!-- END From Them Message  !-->
                    </div>
                    <!-- BEGIN Conversation  !-->
                    <!-- BEGIN Chat Input  !-->
                    <div class="b-t b-grey bg-white clearfix p-l-10 p-r-10">
                        <div class="row">
                            <div class="col-xs-1 p-t-15">
                                <a href="#" class="link text-master"><i class="fa fa-plus-circle"></i></a>
                            </div>
                            <div class="col-xs-8 no-padding">
                                <input type="text" class="form-control chat-input" data-chat-input="" data-chat-conversation="#my-conversation" placeholder="Say something">
                            </div>
                            <div class="col-xs-2 link text-master m-l-10 m-t-15 p-l-10 b-l b-grey col-top">
                                <a href="#" class="link text-master"><i class="pg-camera"></i></a>
                            </div>
                        </div>
                    </div>
                    <!-- END Chat Input  !-->
                </div>
                <!-- END Conversation View  !-->
            </div>
        </div>
    </div>
</div>
<!-- END QUICKVIEW-->