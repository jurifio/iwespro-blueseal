<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CPage;
use bamboo\domain\entities\CSidebarGroupTranslation;
use bamboo\domain\entities\CSidebar;
use bamboo\domain\entities\CSideBarSectionTranslation;
use bamboo\domain\entities\CSidebarGroup;


/**
 * Class CPageListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CPageListAjaxController extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    /**
     * @param $action
     * @return mixed
     */

    public function createAction($action)
    {
        $this->app->setLang(new CLang(1,'it'));
        $this->urls['base'] = $this->app->baseUrl(false)."/blueseal/";
        $this->urls['page'] = $this->urls['base']."applicazioni";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->page = $this->app->entityManagerFactory->create('Page');

        return $this->{$action}();
    }

    public function get()
    {
        $sql = "SELECT 
       `p`.`id` as id   ,  
       `p`.`id` as code   ,  
       `p`.`slug` as slug  ,
       `p`.`url` as url,
       `p`.`icon` as `icon`,
       `p`.`permission` as `permission`,
       `p`.`postId` as postId,
       `bpt`.`title` as `namePost`,
       `pt`.`title` as pageTitle,
       `sst`.`title` as sectionTitle,
       `sgt`.`title` as sideBarGroupTitle
    
               from Page p join PageTranslation pt  on p.id=pt.pageId and pt.langId=1
                left join PostTranslation bpt on p.postId=bpt.postId and bpt.langId=1 
                  
                   left join Sidebar  sb on p.id=sb.pageId 
                    LEFT  JOIN SidebarGroup sg ON sb.sidebarGroupId=sg.id
                  LEFT  join SidebarGroupTranslation sgt on sb.sidebarGroupId=sgt.sidebarGroupId and sgt.langId=1
                   left join SidebarSection ss on sg.sidebarSectionId=ss.id
                  LEFT  join SidebarSectionTranslation sst on ss.id=sst.sidebarSectionId and sst.langId=1
                GROUP BY p.id Order BY p.id asc
                  ";
        $datatable = new CDataTables($sql,['id'],$_GET,true);
        if(!empty($this->authorizedShops)){
            $datatable->addCondition('shopId',$this->authorizedShops);
        }
	    //$datatable->addSearchColumn('extId');
	    //$datatable->addSearchColumn('extSkuId');

        $page = \Monkey::app()->repoFactory->create('Page')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->page->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->page->findCountBySql($datatable->getQuery('full'), $datatable->getParams());
        $pageTranslationRepo=\Monkey::app()->repoFactory->create('PageTranslation');
        $sidebarGroupRepo=\Monkey::app()->repoFactory->create('SideBarGroup');
        $sidebarSectionRepo=\Monkey::app()->repoFactory->create('SidebarSection');
        $sidebarSectionTranslationRepo=\Monkey::app()->repoFactory->create('SidebarSectionTranslation');
        $sidebarRepo=\Monkey::app()->repoFactory->create('Sidebar');
        $sidebarGroupTranslationRepo=\Monkey::app()->repoFactory->create('SidebarGroupTranslation');
        $postTranslationRepo=\Monkey::app()->repoFactory->create('PostTranslation');




        $modifica = $this->urls['base']."applicazioni/modifica";

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($page as $val){



            $response['data'][$i]["DT_RowId"] = 'row__'.$val->id;
            $response['data'][$i]["postId"] = $val->postId;
            $response['data'][$i]["id"] = $val->id;
            $response['data'][$i]['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$modifica.'/'.$val->id.'">'.$val->id.'-'.$val->url.'</a>' : $val->id.'-'.$val->url;
            $pt=$pageTranslationRepo->findOneBy(['pageId'=>$val->id,'langId'=>1]);
            $response['data'][$i]["pageTitle"] = $pt->title;
            $response['data'][$i]["slug"] = $val->slug;
            $response['data'][$i]["icon"] = $val->icon;
            $response['data'][$i]["url"] = $val->url;
            $response['data'][$i]["permission"] = $val->permission;
            $namePost='';
            $btp=$postTranslationRepo->findOneBy(['postId'=>$val->postId,'langId'=>'1']);
            if($btp){
                $namePost=$btp->title;
            }
            $response['data'][$i]['namePost'] = $namePost;
            $sectionTitle='';
            $sidebarGroupTitle='';
            $sb=$sidebarRepo->findOneBy(['pageId'=>$val->id]);
            if($sb){
                $sidebarGroup=$sidebarGroupRepo->findOneBy(['id'=>$sb->sidebarGroupId]);
                if($sidebarGroup) {
                    $sidebarSectionId = $sidebarGroup->sidebarSectionId;
                    $sidebarSection = $sidebarSectionTranslationRepo->findOneBy(['sidebarSectionId'=>$sidebarSectionId,'langId'=>1]);
                    $sectionTitle = $sidebarSection->title;
                    $sidebarGroupTranslation = $sidebarGroupTranslationRepo->findOneBy(['sidebarGroupId'=>$sb->sidebarGroupId,'langId'=>1]);
                    $sidebarGroupTitle = $sidebarGroupTranslation->title;
                    $response['data'][$i]['sectionTitle'] = $sectionTitle;
                    $response['data'][$i]['sidebarGroupTitle'] = $sidebarGroupTitle;
                }else{
                    $response['data'][$i]['sectionTitle'] = '';
                    $response['data'][$i]['sidebarGroupTitle'] = '';
                }
            }else{
                $response['data'][$i]['sectionTitle'] = '';
                $response['data'][$i]['sidebarGroupTitle'] = '';
            }

            $i++;
        }
        return json_encode($response);
    }
}