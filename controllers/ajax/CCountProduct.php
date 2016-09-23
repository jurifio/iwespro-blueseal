<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CBrandDelete
 * @package bamboo\blueseal\controllers\ajax
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CCountProduct extends AAjaxController
{
    public function get()
    {
        if (!$this->app->getUser()->hasPermission('allShops')) throw new \Exception('Solo gli eletti, appartenenti alla Gilda degli Illuminati possono effettuare questa operazione. Contatta un amministratore');
        $season = $this->app->router->request()->getRequestData('season');
        $friend = $this->app->router->request()->getRequestData('friend');

        if (false === $season) throw new \Exception('la variabile "season" è obbligatoria');
        if (false === $friend) throw new \Exception('la variabile "friend" è obbligatoria');

        $params = [];

        $query = "SELECT count(DISTINCT `p`.`productVariantId`) AS `count` FROM `Product` as `p` JOIN `ProductSku` as `ps` ON `p`.`id` = `ps`.`productId` AND `p`.`productVariantId` = `ps`.`productVariantId` WHERE `ps`.`stockQty` > 0 ";

        $fields = [];
        $params = [];
        if ($season) {
            $fields[] = " `p`.`productSeasonId` = ? ";
            $params[] = $season;
        }
        if ($friend) {
            $fields[] = " `ps`.`shopId` = ? ";
            $params[] = $friend;
        }
        if (count($fields)) {
            $query .= " AND ";
            $query .= implode(' AND ', $fields);
        }
        $res = $this->app->dbAdapter->query($query, $params)->fetch();

        return (string)$res['count'];
    }
}