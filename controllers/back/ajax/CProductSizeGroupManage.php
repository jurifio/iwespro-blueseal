<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeGroupHasProductSize;
use function MongoDB\BSON\toJSON;


/**
 * Class CProductSizeGroupManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CProductSizeGroupManage extends AAjaxController
{
    /**
     * @return string
     */
    public function put()
    {
        $checkSql = "SELECT * 
                FROM ProductSizeGroup psg JOIN 
                  ProductSizeGroupHasProductSize psghps ON psg.id = psghps.productSizeGroupId
                WHERE psg.macroName = ? AND psghps.position = ?";

        $moveSql = "UPDATE ProductSizeGroup psg 
                      JOIN ProductSizeGroupHasProductSize psghps ON psg.id = psghps.productSizeGroupId 
                    SET psghps.position = psghps.position + ?
                    WHERE psg.macroName = ? AND psghps.position > ?
                    ORDER BY position ";

        \Monkey::app()->router->response()->setContentType('application/json');
        try {
            $fromRow = \Monkey::app()->router->request()->getRequestData('fromRow');
            $versus = \Monkey::app()->router->request()->getRequestData('versus');
            $macroGroupName = \Monkey::app()->router->request()->getRequestData('macroGroupName');

            if ($versus == 'up') {
                $maxRowNum = 35;
                $versusName = 'alto';
                $modifier = +1;
                $updateVersus = 'DESC';
            } elseif ($versus == 'down') {
                $maxRowNum = 0;
                $versusName = 'basso';
                $modifier = -1;
                $updateVersus = 'ASC';
            } else {
                throw new BambooException('Verso non valido');
            }
            $res = \Monkey::app()->dbAdapter->query($checkSql, [
                $macroGroupName,
                $maxRowNum
            ])->fetchAll();
            if (count($res)) throw new BambooException('Non posso scorrere in %s se la riga %d non è vuota', [$versusName, $maxRowNum]);

            $res = \Monkey::app()->dbAdapter->query($moveSql . $updateVersus, [
                $modifier,
                $macroGroupName,
                $fromRow
            ]);
            return json_encode(true);

        } catch (\Throwable $e) {
            \Monkey::app()->router->response()->raiseProcessingError();
            $res = ['message' => $e->getMessage()];
            $res['trace'] = $e->getTrace();

            return json_encode($res);
        }
    }

    public function post()
    {
        try {
            $data = \Monkey::app()->router->request()->getRequestData();
            $productSizeGroup = \Monkey::app()->repoFactory->create('ProductSizeGroup')->getEmptyEntity();
            $productSizeGroup->macroName = $data['macroName'];
            $productSizeGroup->locale = $data['locale'];
            $productSizeGroup->name = $data['name'];
            //$productSizeGroup->publicName = $data['publicName'];

            return json_encode([
                    'id' => $productSizeGroup->insert()]
            );
        } catch (\Throwable $e) {
            return json_encode([
                'message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ]);
        }
    }

    public function delete()
    {
        try {
            $macroName = \Monkey::app()->router->request()->getRequestData('macroName');
            $rowNum = \Monkey::app()->router->request()->getRequestData('rowNum');

            $productSizeGroups = \Monkey::app()->repoFactory->create('ProductSizeGroup')->findBy([
                'macroName' => $macroName
            ]);

            $products = [];
            foreach ($productSizeGroups as $productSizeGroup) {
                /** @var CProductSizeGroup $productSizeGroup */
                /** @var CProductSizeGroupHasProductSize $productSizeGroupHasProductSize */
                $productSizeGroupHasProductSize = $productSizeGroup->productSizeGroupHasProductSize->findOneByKey('position', $rowNum);

                if ($productSizeGroupHasProductSize) {
                    if (!$productSizeGroupHasProductSize->isProductSizeCorrespondenceDeletable()) {
                        $products += $productSizeGroupHasProductSize->getProductCorrespondences();
                    } else if (count($products) == 0) {
                        $productSizeGroupHasProductSize->delete();
                    }
                }
            }

            if (count($products) == 0) {
                \Monkey::app()->dbAdapter->commit();
                return json_encode(true);
            } else {
                \Monkey::app()->dbAdapter->rollBack();
                \Monkey::app()->router->response()->raiseProcessingError();
                return json_encode([
                    'message' => 'Non ho potuto eliminare la riga perchè ci sono Prodotti collegato',
                    'products' => $products
                ]);
            }

        } catch (\Throwable $e) {
            \Monkey::app()->dbAdapter->rollBack();
            \Monkey::app()->router->response()->raiseProcessingError();
            return json_encode([
                'message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ]);
        }
    }
}