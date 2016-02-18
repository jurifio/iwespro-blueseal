<?php
namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CDetailManager
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CDetailManager extends AAjaxController
{
    /**
     * @return string
     */
    public function get()
    {
        $repo = $this->app->repoFactory->create('ProductDetail');

        $html = 'Su quale dettaglio li vuoi unire?<br><br>';
        $html .= '<select class="full-width" placehoder="Seleziona il dettaglio da tenere" data-init-plugin="selectize"  title="productDetailId" name="productDetailId" id="productDetailId">';

        $i = 0;
        foreach ($this->app->router->request()->getRequestData() as $detailId) {
            $detail = $repo->findOneBy(['id'=>$detailId], 'LIMIT 0,999','ORDER BY slug');
            if ($i == 0){
                $name = $detail->productDetailTranslation->findOneByKey('langId',1)->name;
            }

            $html .= '<option value="' . $detail->id . '" required>' . $detail->productDetailTranslation->findOneByKey('langId',1)->name . '</option>';
        $i++;
        }
        $html .= "</select><br><br>";
        $html .= 'Inserisci il nuovo nome del dettaglio<br>';
        $html .= '<input id="productDetailName" autocomplete="off" type="text" class="form-control" name="productDetailName" title="productDetailName" value="'. $name . '">';

        return json_encode(
            [
                'status' => 'ok',
                'bodyMessage' => $html,
                'okButtonLabel' => 'Unisci',
                'cancelButtonLabel' => 'Annulla'
            ]
        );
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function put()
    {
        $productDetailName = "";
        $datas = $this->app->router->request()->getRequestData();

        $productDetailId = 0;
        $ids = [];
        $this->app->dbAdapter->beginTransaction();

        foreach ($datas as $key => $val) {
            if ($key == 'productDetailId') {
                $productDetailId = $val;
            } elseif ($key == 'productDetailName' ) {
                $productDetailName = $val;
            } else {
                $ids[$key] = $val;
            }
        }

        $em = $this->app->entityManagerFactory->create('ProductSheetActual');
        try {
            foreach ($ids as $id) {

                if ($productDetailId != $id) {
                    $productSheets = $em->findBy(['productDetailId' => $id]);

                    foreach ($productSheets as $productSheet) {
                        $productSheet->delete();
                        $productSheet->productDetailId = $productDetailId;
                        $productSheet->insert();
                    }
                }
                $productDetailTrans = $this->app->repoFactory->create("ProductDetailTranslation")->findOneBy(['productDetailId' => $id, 'langId' => 1]);
                if ($productDetailTrans->productDetailId != $productDetailId) {
                    $productDetailTrans->delete();
                } elseif ($productDetailName != ""){
                    $productDetailTrans->name = $productDetailName;
                    $productDetailTrans->update();
                }
                $productDetail = $this->app->repoFactory->create("ProductDetail")->findOneBy(['id' => $id]);
                if ($productDetail->id != $productDetailId) {
                    $productDetail->delete();
                } elseif ($productDetailName != ""){
                    $productDetail->name = $productDetailName;
                    $productDetail->update();
                }

            }
            $this->app->dbAdapter->commit();
            return true;
        } catch (\Exception $e){
            $this->app->dbAdapter->rollBack();
        }
    }


}