<?php
namespace bamboo\blueseal\jobs;

use bamboo\domain\entities\COrder;
use bamboo\core\jobs\ACronJob;

/**
 * Class CDispatchPreorderToFriend
 * @package bamboo\blueseal\jobs
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CCleanUserSessions extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        if(!is_null($args) && !empty($args)){
            $this->report('Deleting Manual Session', "Id ".$args);
            $this->deleteSession($args);
        }
        $this->report('Deleting Sessions', "Starting to delete Sessions");
        $this->deleteSessions();
    }

	/**
	 * @param $us
	 * @return bool
	 */
    public function deleteSession($us)
    {
        if($us instanceof COrder){
            $userSessionId = $us->id;
        } elseif(is_array($us)) {
	        $userSessionId = $us['id'];
        } else{
	        $userSessionId = $us;
        }
        $res = $this->app->dbAdapter->delete('UserSessionHasCart',['userSessionId'=>$userSessionId]);
        $res = $this->app->dbAdapter->delete('UserSession',['id'=>$userSessionId]);
        return $res>0;
    }

    public function deleteSessions()
    {
        $query = "SELECT us.id
                  FROM `UserSession` us
                  where us.expire < current_timestamp and userId is null";
	    $res = $this->app->dbAdapter->query($query,[])->fetchAll();
        $this->report('Delete Start', "To do: ".count($res));
		$i = 0;
        foreach($res as $us){
            if($i%100 == 0) \Monkey::app()->repoFactory->beginTransaction();
            $resp = $this->deleteSession($us);
            if($i%200 == 0) $this->report('Delete Running', "Deleted: ".$i);
	        if($i%100 == 0) \Monkey::app()->repoFactory->commit();
            if($resp) $i++;
        }
	    \Monkey::app()->repoFactory->commit();
        $this->report('Delete End', "Deleted: ".$i);
    }
}