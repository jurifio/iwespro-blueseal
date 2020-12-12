<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProduct;

/**
 * Class CProductListAjaxController
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
class CMenuListAjaxController extends AAjaxController
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
        $this->urls['page'] = $this->urls['base']."menu";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        if ($this->app->getUser()->hasPermission('allShops')) {

        } else{
            $res = $this->app->dbAdapter->select('UserHasShop',['userId'=>$this->app->getUser()->getId()])->fetchAll();
            foreach($res as $val) {
                $this->authorizedShops[] = $val['shopId'];
            }
        }

        $this->em = new \stdClass();
        $this->em->menu = $this->app->entityManagerFactory->create('Menu');

        return $this->{$action}();
    }

    public function get()
    {
        $sql = "SELECT 
       `m`.`id` as id   ,  
       `m`.`id` as code  ,
       `m`.`name` as name,
                m.slug as slug,
               `m`.`order` as `orderMenu`,
               `m`.`level` as level,
                group_concat(concat(mn.id,'-',mn.captionTitle)) as sublink
               from Menu m join MenuTranslation mt on m.id=mt.menuTranslationId
                join MenuNav mn on m.id=mn.menuId   
                join MenuNavType mnt on mn.typeId=mnt.id
WHERE mt.langId=1
                GROUP BY m.id
                  ";
        $datatable = new CDataTables($sql,['id'],$_GET,true);
        if(!empty($this->authorizedShops)){
            $datatable->addCondition('shopId',$this->authorizedShops);
        }
	    //$datatable->addSearchColumn('extId');
	    //$datatable->addSearchColumn('extSkuId');

        $menu = \Monkey::app()->repoFactory->create('Menu')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->menu->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->menu->findCountBySql($datatable->getQuery('full'), $datatable->getParams());
        $menuNavRepo=\Monkey::app()->repoFactory->create('MenuNav');
        $menuNavTypeRepo=\Monkey::app()->repoFactory->create('MenuNavType');



        $modifica = $this->urls['base']."menu/modifica";

        $okManage = $this->app->getUser()->hasPermission('/admin/product/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($menu as $val){



            $response['data'][$i]["DT_RowId"] = 'row__'.$val->id;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['code'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$modifica.'/'.$val->id.'">'.$val->id.'-'.$val->name.'</a>' : $val->id.'-'.$val->name;
            $response['data'][$i]['orderMenu'] = $val->order;
            $response['data'][$i]['level'] = $val->level;
            $sublink='';
            $menuNav=$menuNavRepo->findBy(['menuId'=>$val->id]);
            foreach($menuNav as $nav ){
                $typeMenu=$menuNavTypeRepo->findOneBy(['id'=>$nav->typeId]);
                $sublink .=$nav->id.'-'.$nav->captionTitle." tipo: ".$typeMenu->name.'</br>';

            }
            $response['data'][$i]['sublink']=$sublink;

            $i++;
        }
        return json_encode($response);
    }
}