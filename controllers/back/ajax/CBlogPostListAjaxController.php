<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

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
class CBlogPostListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT
                  `p`.`id`           AS `id`,
                  `p`.`blogId`       AS `blogId`,
                  `p`.`postStatusId` AS `postStatusId`,
                  `pt`.`langId`      AS `langId`,
                  `p`.`userId`       AS `userId`,
                  `p`.`author`       AS `author`,
                  `p`.`hits`         AS `hits`,
                  `pt`.`coverImage`  AS `coverImage`,
                  `pt`.`title`       AS `title`,
                  `pt`.`subtitle`    AS `subtitle`,
                  `pt`.`content`     AS `content`,
                  `ps`.`name`        AS `name`,
                  `p`.`creationDate` AS `creationDate`,              
                   concat('/blueseal/work/blog/',p.id) as url ,
                  `p`.`publishDate`  AS `status`
                FROM ((`Post` `p`
                  JOIN `PostTranslation` `pt` ON (((`p`.`id` = `pt`.`postId`) AND (`p`.`blogId` = `pt`.`blogId`))))
                  JOIN `PostStatus` `ps` ON ((`p`.`postStatusId` = `ps`.`id`)))
                  join  `Blog` b on p.`blogId`=b.id";
        $datatable = new CDataTables($sql,['id','blogId'],$_GET,true);

	    $datatable->addCondition('postStatusId',[3],true);

        $posts = \Monkey::app()->repoFactory->create('Post')->em()->findBySql($datatable->getQuery(),$datatable->getParams());
        $count = \Monkey::app()->repoFactory->create('Post')->em()->findCountBySql($datatable->getQuery(true), $datatable->getParams());
        $totalCount = \Monkey::app()->repoFactory->create('Post')->em()->findCountBySql($datatable->getQuery('full'), $datatable->getParams());
        $blogRepo=\Monkey::app()->repoFactory->create('Blog');
        $modifica = "/blueseal/blog/modifica";

        $okManage = $this->app->getUser()->hasPermission('/admin/content/edit');

        $response = [];
        $response ['draw'] = $_GET['draw'];
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach($posts as $post){
            $row = [];
            $coverImage = empty($post->postTranslation->getFirst()->coverImage) ? "/assets/bs-dummy-16-9.png" : $post->postTranslation->getFirst()->coverImage;
            $row['url']='/blueseal/work/blog/'.$post->id;
            $row["DT_RowId"] = 'row__'.$post->id.'__'.$post->blogId;
            $row["DT_RowClass"] = 'colore';
            $row['id'] = $okManage ? '<a data-toggle="tooltip" title="modifica" data-placement="right" href="'.$modifica.'?id='.$post->id.'&blogId='.$post->blogId.'">'.$post->id.'</a>' : $post->id;
            $row['coverImage'] = '<img width="80" src="/assets/'.$coverImage.'" />';
            $row['title'] = utf8_encode($post->postTranslation->getFirst()->title);
            $row['content'] = utf8_encode(substr(strip_tags($post->postTranslation->getFirst()->content),0,50)."&hellip;");
            $row['creationDate'] = (new \DateTime($post->creationDate))->format('d-m-Y H:i');
            $row['publishDate'] = is_null($post->publishDate) ? 'Non definita' : (new \DateTime($post->publishDate))->format('d-m-Y H:i');
            $row['stato'] = $post->postStatus->name;
            $blog=$blogRepo->findOneBy(['id'=>$post->blogId]);
            $row['typeBlog']=$blog->name;

            $response ['data'][] = $row;
        }
        return json_encode($response);
    }
}