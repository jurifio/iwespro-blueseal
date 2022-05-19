<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProduct;

/**
 * Class CProductListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CSubmenuListAjaxController extends AAjaxController
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
        $this->urls['page'] = $this->urls['base']."submenu";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->menunav = $this->app->entityManagerFactory->create('MenuNav');

        return $this->{$action}();
    }

    public function get()
    {
        $sql = "SELECT 
       `mt`.`id` as id,  
       `mt`.`id` as code  ,
       `mt`.`captionTitle` as captionTitle,
                mt.slug as slug,
               `mt`.`captionlink` as `captionLink`,
              `mnt`.`name` as type,
              `m`.`name` as menuName,
              `mt`.elementId as elementId
               from MenuNav mt join  Menu m on mt.menuId=m.id   
                join MenuNavType mnt on mt.typeId=mnt.id
                GROUP BY mt.id order by mt.menuId asc
                  ";
        $datatable = new CDataTables($sql,['id'],$_GET,true);
        if(!empty($this->authorizedShops)){
            $datatable->addCondition('shopId',$this->authorizedShops);
        }
	    //$datatable->addSearchColumn('extId');
	    //$datatable->addSearchColumn('extSkuId');

        $menunav = \Monkey::app()->repoFactory->create('MenuNav')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->menunav->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->menunav->findCountBySql($datatable->getQuery('full'), $datatable->getParams());
        $menuRepo=\Monkey::app()->repoFactory->create('Menu');
        $menuNavTypeRepo=\Monkey::app()->repoFactory->create('MenuNavType');



        $modifica = $this->urls['base']."submenu/modifica";

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($menunav as $val){



            $response['data'][$i]["DT_RowId"] = 'row__'.$val->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$modifica.'/'.$val->id.'">'.$val->id.'-'.$val->captionTitle.'</a>' : $val->id.'-'.$val->captionTitle;
            $response['data'][$i]['captionTitle'] = $val->captionTitle;
            $menu=$menuRepo->findOneBy(['id'=>$val->menuId]);
            $mainMenu=$menu->name;
            $response['data'][$i]['menuName']=$mainMenu;
            $menuNavType=$menuNavTypeRepo->findOneBy(['id'=>$val->typeId]);
            $type=$menuNavType->name;
            $response['data'][$i]['type']=$type;
            $response['data'][$i]['captionImage']='<img width="50px" src="https://'.$_SERVER['HTTP_HOST'].'/assets/'.$val->captionImage.'"/>';
            $elementId='';
            switch($val->typeId){
                case 1:
                    $page=\Monkey::app()->repoFactory->create('FixedPage')->findOneBy(['id'=>$val->elementId]);
                    if($page) {
                        $elementId = $page->title;
                    }else{
                        $elementId='';
                    }
                    break;
                case 2:
                    $elementId=$val->captionLink;
                    break;
                case 3:
                    $category=\Monkey::app()->repoFactory->create('ProductCategoryTranslation')->findOneBy(['productCategoryId'=>$val->elementId,'langId'=>1,'shopId'=>44]);
                    $elementId=$category->name;
                    break;
                case 4:
                    $tag=\Monkey::app()->repoFactory->create('Tag')->findOneBy(['id'=>$val->elementId]);
                    $elementId=$tag->slug;
                    break;
                case 5:
                    $tagExclusive=\Monkey::app()->repoFactory->create('Tag')->findOneBy(['id'=>$val->elementId]);
                    $elementId=$tagExclusive->exclusiven;
                    break;
                case 6:
                    $elementId='brand';
                    break;

            }
            $response['data'][$i]['elementId']=$elementId;

            $i++;
        }
        return json_encode($response);
    }
}