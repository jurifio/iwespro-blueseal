<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

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
class CBlogPostListAjaxController extends AAjaxController
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
        $this->urls['page'] = $this->urls['base']."prodotti";
        $this->urls['dummy'] = $this->app->cfg()->fetch('paths','dummyUrl');

        $this->em = new \stdClass();
        $this->em->products = $this->app->entityManagerFactory->create('Post');

        return $this->{$action}();
    }

    public function get()
    {
        $datatable = new CDataTables('vBluesealPostList',['id','blogId'],$_GET);

        $posts = $this->app->repoFactory->create('Post')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = $this->em->products->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = $this->em->products->findCountBySql($datatable->getQuery('full'), $datatable->getParams());

        $modifica = $this->urls['base']."blog/modifica";

        $okManage = $this->app->getUser()->hasPermission('/admin/content/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        $i = 0;

        foreach($posts as $post){

            $creationDate = new \DateTime($post->creationDate);
            $publishDate = new \DateTime($post->publishDate);

            $coverImage = empty($post->postTranslation->getFirst()->coverImage) ? "http://redpanda.clo.ud.it/dummyPictures/bs-dummy-16-9.png" : $post->postTranslation->getFirst()->coverImage;

            $response['data'][$i]["DT_RowId"] = 'row__'.$post->id.'__'.$post->blogId;
            $response['data'][$i]["DT_RowClass"] = 'colore';
            $response['data'][$i]['id'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$modifica.'?id='.$post->id.'&blogId='.$post->blogId.'">'.$post->id.'</a>' : $post->id;
            $response['data'][$i]['coverImage'] = '<img width="80" src="'.$coverImage.'" />';
            $response['data'][$i]['title'] = $post->postTranslation->getFirst()->title;
            $response['data'][$i]['content'] = substr(strip_tags($post->postTranslation->getFirst()->content),0,50)."&hellip;";
            $response['data'][$i]['creationDate'] = $creationDate->format('d-m-Y H:i');
            $response['data'][$i]['publishDate'] = $publishDate->format('d-m-Y H:i');
            $response['data'][$i]['stato'] = $post->postStatus->name;

            $i++;
        }

        echo json_encode($response);
    }
}