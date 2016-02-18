<?php

error_reporting(E_ALL);
ini_set('display_errors',1);
set_time_limit(0);

require_once "config.php";
require_once "autoload.php";

use bamboo\app\business\eBay,
    bamboo\app\business\eBaySeller;

$seller = new eBaySeller('AgAAAA**AQAAAA**aAAAAA**ZgfYUg**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AFlIekDZKGpw2dj6x9nY+seQ**YBcCAA**AAMAAA**NjJllHtVsOyjBnjFQYlWKkS/GNsBCR6ExGG+FbT2TdYSC3zOOsXly0cyOI02PmsZs9U8uoBdaxhX+AxLpBDS/NsxRoVdqhTxTqjCq96g2oCgLoCsU1xyHamYMPSxtiKybAFXo5Non+vp9DS9dMDgPBSA6aJKPSb1EuSgw7LCZJAQ1p3jP8608zU9JKelWi+QMFJC9BHzrgAXlUp5IyB0a73xR9/bqLxFD5lAMVk5rAelSu+cEWoo1BNjMIJvknx2re9xbi61wHDW+8Z5DYrkd52u+5cfTdiTIkBy7bZzjwgi9CIBkyGj4rDI9qv5+bHC/2rdv7xMP4+yxluR5Uokabqdub8MqPF+BELuLl2ErOER4ONye+eKQ0v2hdvEiKtfOGkn3oBqrVzStvKt+tIsC/wXzjf0gkswM1YdjSI2bC6S+F03k2rn9onJZvR1pWL2EyvlMDHFHGxGPdiGFyETTmt2IwjjeG3rt59LqglENv/BvTJSGtD1h9nMPF6pN/5E1oxivhBBMLl9lZFIlqxWXhis+0rYB+g1EXJmu1dEpmwprWIpyAqIi1QzEazKyCDEh7urW2IFQyVQb/KxXHR92cUs98iNp/+B5i0MWJ+JZ+B53I6OQWwk2Xi+V7tYF2oZBkbL49IHbQQeV1VJc1+C2smBlcaw3PoIhF4qegiI2q4wLGSDRSuKxN6F3iPKtkYmLQ4YXzsvLvpWYkEJw21lVMCU29MotI/U+LGKDr+szP+j+W9wteO0EtXEFRH7w/ei');
$ebay = new eBay($seller,$config);

$params = array(
    'CreateTimeFrom'=>'2014-01-01T00:00:00.000Z',
    'CreateTimeTo'=>'2014-01-31T00:00:00.000Z',
    'OrderRole'=>'Seller',
    'OrderStatus'=>'Completed'
);

try{
    $data = $ebay->call('getOrders',$params);
} catch (Exception $e){
    var_dump($e);
}

header("Content-type: text/xml; charset=utf-8");

echo $data;