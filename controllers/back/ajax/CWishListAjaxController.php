<?php
/**
 *
 */

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CWishList;
use bamboo\domain\entities\CUser;
use bamboo\domain\entities\CProduct;
use bamboo\utils\time\STimeToolbox;
use bamboo\utils\price\SPriceToolbox;

class CWishListAjaxController extends AAjaxController
{

    public function get()
    {
        $sql = "SELECT
                      w.id AS id ,
                      w.creationDate AS creationDate,
                      concat(ud.name ,' ', ud.surname ) AS user,
                      u.email AS email,
                      concat(p.id,'-',p.productVariantId) AS product,
                      pb.name AS brand,
                      p.dummyPicture AS picture,
                      w.statusId AS status,
                      w.deleteDate AS deleteDate,
                      S.price as price,
                      S.salePrice as salePrice,
                      p.isOnSale as isOnSale
                      

                FROM User u
                INNER  JOIN  UserDetails ud ON u.id = ud.userId
                INNER  JOIN  WishList w ON u.id = w.UserId
                INNER  JOIN  Product p ON w.productId =p.id
                INNER  JOIN  ProductBrand pb ON p.productBrandId = pb.id  
                INNER  JOIN  ProductPublicSku S ON p.id = S.productId AND p.productVariantId = S.productVariantId
                WHERE p.productVariantId = w.productVariantId
                GROUP BY  w.productId ";

        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings(true);

        foreach ($datatable->getResponseSetData() as $key => $row) {

            $row['picture'] = '<a href="#1" class="enlarge-your-img"><img width="50" src="' . $row['picture'] . '" /></a>';
            switch ($row['status']) {
                case "1":
                    $row['status'] = "<span class=\"label label-warning\">Presente Nella Lista</span>";
                    break;
                case  "2":
                    $row['status'] = "<span class=\"label label-success\">Inserito nel Carrello</span>";
                    break;
                case  "3":
                    $row['status'] = "<span class=\"label label-danger\">Cancellato dalla Lista</span>";
                    break;
            }
           // $creationDate=$row['creationDate'];
            $row['creationDate'] = STimeToolbox::FormatDateFromDBValue($row['creationDate'],'d-m-Y H:i:s');
            $row['deleteDate'] = STimeToolbox::FormatDateFromDBValue($row['deleteDate'],'d-m-Y H:i:s');
            if($row['isOnSale']=="1"){
                $row['isOnSale']="<span class=\"label label-warning\">Prodotto In Saldo</span>";
                $row['price']="<del>".SPriceToolbox::formatToEur($row['price'], true)."</del><span class=\"label label-warning\">".SPriceToolbox::formatToEur($row['salePrice'], true)."</span>";
            }else{
                $row['isOnSale']="<span class=\"label label-success\">Prodotto Non In Saldo</span>";
                $row['price']="<span class=\"label label-success\">".SPriceToolbox::formatToEur($row['price'], true)."</span>";
            }


            $datatable->setResponseDataSetRow($key, $row);


        }

        return $datatable->responseOut();

    }
}