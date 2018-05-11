<?php
namespace bamboo\controllers\back\ajax;
use bamboo\core\exceptions\BambooException;

/**
 * Class CBrandDelete
 * @package bamboo\blueseal\controllers\ajax
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CGetTableContent extends AAjaxController
{
    protected $publicTables = [
        'Carrier',
        'Lang',
        'Country',
        'City',
        'Currency',
        'ProductCategory',
        'ProductDescriptionTranslation',
        'ProductDetail',
        'ProductName',
        'ProductNameTranslation',
        'ProductSeason',
        'ProductSize',
        'ProductStatus',
        'ProductColorGroup',
        'ShippingBox',
        'Tag',
        'TagTranslation',
        'Province',
        'ShipmentFault',
        'ProductSheetModelPrototypeGender',
        'ProductSheetModelPrototypeCategoryGroup',
        'ProductSheetModelPrototypeMaterial'
    ];

    public function get()
    {
        $table = $this->app->router->request()->getRequestData('table');
        $fields = $this->app->router->request()->getRequestData('fields');
        $extraFields = $this->app->router->request()->getRequestData('extraFields');
        $condition = $this->app->router->request()->getRequestData('condition');
        $orderBy = $this->app->router->request()->getRequestData('orderBy');
        if(!$orderBy) $orderBy = "";
        else $orderBy = " ORDER BY ".implode(',',$orderBy);
        if (!in_array($table,$this->publicTables) &&
            !$this->app->getUser()->hasPermission('allShops'))
            throw new \Exception('Solo gli eletti, appartenenti alla Gilda degli Illuminati possono effettuare questa operazione. Contatta un amministratore');

        if (!$table) throw new \Exception('la variabile "table" è obbligatoria');
        if (false !== $condition && !is_array($condition) || !count($condition)) throw new BambooException('Le condizioni devono essere passate sottoforma di array');

        if ($condition) $objectCollection = \Monkey::app()->repoFactory->create($table)->findBy($condition,"",$orderBy);
        else $objectCollection = \Monkey::app()->repoFactory->create($table)->findAll("",$orderBy);

        if(is_array($fields)) {
            $responseSet = [];
            foreach($objectCollection as $item) {
                $responseItem = [];
                foreach ($fields as $f) {
                    $responseItem[$f] = $item->{$f};
                }
                $responseSet[] = $responseItem;
            }
        } elseif (is_array($extraFields)) {
            foreach ($objectCollection as $item){
                foreach ($extraFields as $field){
                    $item->{$field};
                }
            } $responseSet = $objectCollection;
        } else $responseSet = $objectCollection;
        \Monkey::app()->router->response()->setContentType('application/json');
        return json_encode($responseSet);
    }
}