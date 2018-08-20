<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\jobs\ACronJob;
use bamboo\core\base\CObjectCollection;
use bamboo\utils\time\STimeToolbox;

/**
 * Class CPrestashopAlignCategory
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 02/08/2018
 * @since 1.0
 */
class CPrestashopAlignCategory extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {

        $this->AlignCategory();

    }

    /**
     * @throws \bamboo\core\exceptions\BambooDBALException
     *
     */
    public function AlignCategory()
    {

        \Monkey::app()->vendorLibraries->load('prestashop');
        define('DEBUG', true);
        define('PS_SHOP_PATH', 'http://www.iwes.shop/');
        define('PS_WS_AUTH_KEY', 'PWTA3F211GSY6APTTCJDP2Y3UHHYFSVW');


       $PrestashopCategory = $this->app->dbAdapter->query(" SELECT t1.id, t1.slug, 

  (SELECT id
               FROM ProductCategory t2
               WHERE t2.lft < t1.lft AND t2.rght > t1.rght
               ORDER BY t2.rght-t1.rght ASC
               LIMIT 1)
  AS parent FROM ProductCategory t1

  JOIN ProductCategoryTranslation PCT ON t1.id = PCT.productCategoryId
ORDER BY (rght-lft) DESC",array())->fetchAll();

        $today = new \DateTime();
       foreach ($PrestashopCategory as $item) {
           $n_is_root_category='0';
           $id=$item->id;
           $name=$item->slug;
           $n_id_parent=$item->parent;
           $date_add=$today->format('Y-m-d\TH:i:s');
           $date_upd=$today->format('Y-m-d\TH:i:s');
           $n_active='1';
           $languageCategoryTranslation=\Monkey::app()->repoFactory->create('ProductCategoryTranslation')->findOneBy(['productCategoryId'=>$id]);
           foreach ($languageCategoryTranslation as $translations){
               $n_l_id=$translations->langId;
               $n_desc=$translations->description;
               $n_name=$translations->name;
               $n_link_rewrite=$translations->slug;
               $n_meta_title=$translations->name;
               $n_meta_description=$translations->name;
               $n_meta_keywords=$translations->name;


           }


       }
        global $webService;
        $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);


        $xml = $webService -> get(array('url' => PS_SHOP_PATH . '/api/categories?schema=blank'));

        $resources = $xml -> children() -> children();

        unset($resources -> id);

        unset($resources -> position);

        unset($resources -> id_shop_default);

        unset($resources -> date_add);

        unset($resources -> date_upd);

        $resources -> active = $n_active;

        $resources -> id_parent = $n_id_parent;

        $resources -> id_parent['xlink:href'] = PS_SHOP_PATH . '/api/categories/' . $n_id_parent;

        $resources -> is_root_category = $n_is_root_category;

        $node = dom_import_simplexml($resources -> name -> language[0][0]);

        $no = $node -> ownerDocument;

        $node -> appendChild($no -> createCDATASection($n_name));

        $resources -> name -> language[0][0] = $n_name;

        $resources -> name -> language[0][0]['id'] = $n_l_id;

        $resources -> name -> language[0][0]['xlink:href'] = PS_SHOP_PATH . '/api/languages/' . $n_l_id;

        $node = dom_import_simplexml($resources -> description -> language[0][0]);

        $no = $node -> ownerDocument;

        $node -> appendChild($no -> createCDATASection($n_desc));

        $resources -> description -> language[0][0] = $n_desc;

        $resources -> description -> language[0][0]['id'] = $n_l_id;

        $resources -> description -> language[0][0]['xlink:href'] = PS_SHOP_PATH . '/api/languages/' . $n_l_id;

        $node = dom_import_simplexml($resources -> link_rewrite -> language[0][0]);

        $no = $node -> ownerDocument;

        $node -> appendChild($no -> createCDATASection($n_link_rewrite));

        $resources -> link_rewrite -> language[0][0] = $n_link_rewrite;

        $resources -> link_rewrite -> language[0][0]['id'] = $n_l_id;

        $resources -> link_rewrite -> language[0][0]['xlink:href'] = PS_SHOP_PATH . '/api/languages/' . $n_l_id;

        $node = dom_import_simplexml($resources -> meta_title -> language[0][0]);

        $no = $node -> ownerDocument;

        $node -> appendChild($no -> createCDATASection($n_meta_title));

        $resources -> meta_title -> language[0][0] = $n_meta_title;

        $resources -> meta_title -> language[0][0]['id'] = $n_l_id;

        $resources -> meta_title -> language[0][0]['xlink:href'] = PS_SHOP_PATH . '/api/languages/' . $n_l_id;

        $node = dom_import_simplexml($resources -> meta_description -> language[0][0]);

        $no = $node -> ownerDocument;

        $node -> appendChild($no -> createCDATASection($n_meta_description));

        $resources -> meta_description -> language[0][0] = $n_meta_description;

        $resources -> meta_description -> language[0][0]['id'] = $n_l_id;

        $resources -> meta_description -> language[0][0]['xlink:href'] = PS_SHOP_PATH . '/api/languages/' . $n_l_id;

        $node = dom_import_simplexml($resources -> meta_keywords -> language[0][0]);

        $no = $node -> ownerDocument;

        $node -> appendChild($no -> createCDATASection($n_meta_keywords));

        $resources -> meta_keywords -> language[0][0] = $n_meta_keywords;

        $resources -> meta_keywords -> language[0][0]['id'] = $n_l_id;

        $resources -> meta_keywords -> language[0][0]['xlink:href'] = PS_SHOP_PATH . '/api/languages/' . $n_l_id;

        try {

            $opt = array('resource' => 'categories');

            $opt['postXml'] = $xml -> asXML();

            $xml = $webService -> add($opt);

        } catch (PrestaShopWebserviceException $ex) {

            \Monkey::app()->ApplicationReport("CPrestashopAlignCategory: " . $ex -> getMessage(), 'Inserimento Categorie','Errore Inserimento');

// my log function

        }

    }






}