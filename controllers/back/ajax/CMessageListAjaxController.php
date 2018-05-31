<?php
namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CMessage;
use bamboo\domain\entities\CMessageHasUser;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CFoisonRepo;


/**
 * Class CMessageListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 31/05/2018
 * @since 1.0
 */
class CMessageListAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws \Throwable
     */
    public function get()
    {

        $sql = "
            SELECT  m.id,
              m.title,
              LEFT(m.text, 100) as text,
              GROUP_CONCAT(CONCAT(f.name,' ',f.surname, ' | Data: ', mhu.seen)) as seen
            FROM Message m
              LEFT JOIN MessageHasUser mhu ON m.id = mhu.messageId
              LEFT JOIN Foison f ON mhu.foisonId = f.id
              GROUP BY m.id
              ";


        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        /** @var CFoisonRepo $foisonRepo */
        $foisonRepo = \Monkey::app()->repoFactory->create('Foison');

        $datatable->doAllTheThings(false);

        $url = $this->app->baseUrl(false) . "/blueseal/message/";

        /** @var CRepo $messageRepo */
        $messageRepo = \Monkey::app()->repoFactory->create('Message');

        foreach ($datatable->getResponseSetData() as $key=>$row) {

            /** @var CMessage $mex */
            $mex = $messageRepo->findOneBy(['id'=>$row['id']]);

            $row['id'] = "<a href='".$url.$mex->id."' target='_blank'>".$mex->id."</a>";
            $row['row_id'] = $mex->id;
            $row['title'] = $mex->title;
            $row['text'] = substr($mex->text, 0, 100).' ...';

            switch ($mex->priority){
                case 'L':
                    $row["DT_RowClass"] = "green";
                    break;
                case 'M':
                    $row["DT_RowClass"] = "yellow";
                    break;
                case 'H':
                    $row["DT_RowClass"] = "red";
                    break;
            }

            /** @var CObjectCollection $seen */
            $seen = $mex->messageHasUser;

            $usMes = '';
            if(!is_null($seen)){
                /** @var CMessageHasUser $val */
                foreach ($seen as $val) {

                    /** @var CFoison $foison */
                    $foison = $foisonRepo->findOneBy(['id'=>$val->foisonId]);

                    $usMes .= $foison->name. ' ' .$foison->surname.' | Data: '.$val->seen.'<br>';
                }
            }
            $row['seen'] = $usMes;


            $datatable->setResponseDataSetRow($key,$row);
        }


        return $datatable->responseOut();

    }

}