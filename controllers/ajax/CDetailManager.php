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
        $repo = $this->app->repoFactory->create('ProductDetail',false);

        $html = 'Su quale dettaglio li vuoi unire?<br><br>';
        $html .= '<select class="full-width" placehoder="Seleziona il dettaglio da tenere" data-init-plugin="selectize"  title="productDetailId" name="productDetailId" id="productDetailId">';

        $i = 0;
        foreach ($this->app->router->request()->getRequestData() as $detailId) {
            $detail = $repo->findOneBy(['id'=>$detailId], 'LIMIT 0,999','ORDER BY slug');
            if ($i == 0){
                $name = $detail->productDetailTranslation->findOneByKey('langId',1)->name;
            }
	        $langs = [];
	        foreach ($detail->productDetailTranslation as $trans) {
		        if(!empty($trans->name)) $langs[] = $trans->lang->lang;
	        }

            $html .= '<option value="' . $detail->id . '" required>' . $detail->productDetailTranslation->findOneByKey('langId',1)->name . '('.implode(',',$langs).') </option>';
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
        $datas = $this->app->router->request()->getRequestData();

	    $productDetailId = $datas['productDetailId'];
	    $productDetailName = $datas['productDetailName'];

        $ids = [];
        $this->app->dbAdapter->beginTransaction();

        foreach ($datas as $key => $val) {
	        if($val == $productDetailId) continue;
            $ids[] = $val;
        }

	    $productDetailPrimary = $this->app->repoFactory->create("ProductDetail")->findOneBy(['id' => $productDetailId]);
	    $productDetailPrimary->productDetailTranslation->getFirst()->name = $productDetailName;
	    $productDetailPrimary->productDetailTranslation->getFirst()->update();

        $em = $this->app->entityManagerFactory->create('ProductSheetActual');
        try {
            foreach ($ids as $id) {
                $productSheets = $em->findBy(['productDetailId' => $id]);

                foreach ($productSheets as $productSheet) {
                    $productSheet->delete();
                    $productSheet->productDetailId = $productDetailId;
                    $productSheet->insert();
                }
	            $productDetail = $this->app->repoFactory->create("ProductDetail",false)->findOneBy(['id' => $id]);

	            foreach ($productDetail->productDetailTranslation as $detailTranslation) {
					$detailTranslation->delete();
	            }
	            $productDetail->delete();
            }
            $this->app->dbAdapter->commit();
            return true;
        } catch (\Exception $e){
            $this->app->dbAdapter->rollBack();
	        throw $e;
        }
    }


}