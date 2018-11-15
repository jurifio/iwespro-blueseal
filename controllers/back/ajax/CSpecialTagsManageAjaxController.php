<?php

namespace bamboo\controllers\back\ajax;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CProduct;
use bamboo\domain\entities\CProductHasTag;
use bamboo\domain\entities\CTag;
use bamboo\domain\repositories\CProductRepo;
use bamboo\domain\repositories\CTagRepo;

/**
 * Class CSpecialTagsManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/06/2018
 * @since 1.0
 */
class CSpecialTagsManageAjaxController extends AAjaxController
{
    public function get(){
        /** @var CTagRepo $tagRepo */
        $tagRepo = \Monkey::app()->repoFactory->create('Tag');

        $tags = $tagRepo->getAllSpecialTag();

        $specialTag = [];

        $i = 0;
        /** @var CTag $tag */
        foreach ($tags as $tag){

        $specialTag[$i]['id'] = $tag->id;
        $specialTag[$i]['trName'] = $tag->tagTranslation->findOneByKey('langId',1)->name;
        $i++;
        }

        return json_encode($specialTag);
    }


    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function post(){
        $data = \Monkey::app()->router->request()->getRequestData();
        $productIds = $data['p'];
        $tag = $data['tag'];
        $pos = $data['pos'];

        if(empty($tag)) return 'Devi selezionare un tag';

        /** @var CProductRepo $pRepo */
        $pRepo = \Monkey::app()->repoFactory->create('Product');
        /** @var CRepo $phtRepo */
        $phtRepo = \Monkey::app()->repoFactory->create('ProductHasTag');
        foreach ($productIds as $ids) {

            /** @var CProduct $product */
            $product = $pRepo->findOneBy(['id'=>explode('-',$ids)[0], 'productVariantId'=>explode('-',$ids)[1]]);

            /** @var CProductHasTag $extPht */
            $extPht = $phtRepo->findOneBy(['productId'=>$product->id, 'productVariantId'=>$product->productVariantId, 'tagId'=>$tag]);

            if(!is_null($extPht)) {
                $extPht->position = $pos;
                $extPht->update();
            } else {
                \Monkey::app()->dbAdapter->insert('ProductHasTag', ['productId' => $product->id, 'productVariantId' => $product->productVariantId, 'tagId' => $tag, 'position' => $pos], false, true);
            }
        }

        return 'Special tag inserita con successo';
    }



    /**
     * @return string
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function delete(){
        $data = \Monkey::app()->router->request()->getRequestData();
        $productIds = $data['p'];
        $tag = $data['tag'];

        if(empty($tag)) return 'Devi selezionare un tag';

        /** @var CProductRepo $pRepo */
        $pRepo = \Monkey::app()->repoFactory->create('Product');
        /** @var CRepo $phtRepo */
        $phtRepo = \Monkey::app()->repoFactory->create('ProductHasTag');
        foreach ($productIds as $ids) {

            /** @var CProduct $product */
            $product = $pRepo->findOneBy(['id'=>explode('-',$ids)[0], 'productVariantId'=>explode('-',$ids)[1]]);

            /** @var CProductHasTag $extPht */
            $extPht = $phtRepo->findOneBy(['productId'=>$product->id, 'productVariantId'=>$product->productVariantId, 'tagId'=>$tag]);

            if(is_null($extPht)) continue;

            \Monkey::app()->dbAdapter->delete('ProductHasTag',['productId'=>$product->id,'productVariantId'=>$product->productVariantId,'tagId'=>$tag]);
        }

        return 'Special tag eliminata con successo';
    }

}