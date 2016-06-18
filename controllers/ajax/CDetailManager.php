<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\core\utils\slugify\CSlugify;

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
        $search = $this->app->router->request()->getRequestData()['search'];
        //$repo = $this->app->repoFactory->create('ProductDetailTranslation',false);
        $res = $this->app->dbAdapter->query("SELECT `productDetailId` as `id`, `name` FROM `ProductDetailTranslation` WHERE `langId` = 1 AND `name` like '%" . $search . "%' ORDER BY `name` LIMIT 30", [])->fetchAll();
        //$res = $repo->findBy(['name' => $search, 'langId' => 1], " LIMIT 10 ")->toArray();
        return json_encode($res);
        //$emDetails = $repo->findAll();
        //$allDetails = [];
        //$i = 0;
        
        /*$i = 0;
        $get = $this->app->router->request()->getRequestData();
        $def = false; //una volta settato un valore di default
        foreach ($allDetails as $detailId) {
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
        $html .= '<input id="productDetailName" autocomplete="off" type="text" class="form-control" name="productDetailName" title="productDetailName" value="">';

        return json_encode(
            [
                'status' => 'ok',
                'bodyMessage' => $html,
                'okButtonLabel' => 'Unisci',
                'cancelButtonLabel' => 'Annulla'
            ]
        );*/
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function put()
    {
        $data = $this->app->router->request()->getRequestData();

	    $productDetailId = $data['productDetailId'];
	    $productDetailName = $data['productDetailName'];

	    unset($data['productDetailId']);
	    unset($data['productDetailName']);

        $ids = [];
        $this->app->dbAdapter->beginTransaction();

        foreach ($data as $key => $val) {
	        if($val == $productDetailId) continue;
            $ids[] = $val;
        }

	    $productDetailPrimary = $this->app->repoFactory->create("ProductDetail")->findOneBy(['id' => $productDetailId]);
	    $productDetailPrimary->productDetailTranslation->getFirst()->name = $productDetailName;
        $slug = new CSlugify();
        $productDetailPrimary->slug = $slug->slugify($productDetailName);
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