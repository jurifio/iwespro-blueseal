<?php
ini_set("display_errors",1);
error_reporting(~0);

require "../../../BlueSeal.php";

//$ninetyNineMonkey = new BlueSeal('BlueSeal','ilsalvagente','/data/www/redpanda');
$ninetyNineMonkey->enableDebugging();
$ninetyNineMonkey->setDefaultLanguage('it');

use bamboo\core\theming\nestedCategory\CCategoryManager;

/** @var CCategoryManager $cm */
$cm = $ninetyNineMonkey->categoryManager;

$ninetyNineMonkey->repoFactory->beginTransaction();
/** livello 1 */
$uomo = $cm->categories()->add('uomo','Uomo');
$donna = $cm->categories()->add('donna','Donna');

/** livello 2 */
$abbigliamentoD = $cm->categories()->add('abbigliamento','Abbigliamento',$donna);
$borseD = $cm->categories()->add('borse','Borse',$donna);
$calzatureD = $cm->categories()->add('calzature','Calzature',$donna);
$accessoriD = $cm->categories()->add('accessori','Accessori',$donna);

$abbigliamentoU = $cm->categories()->add('abbigliamento','Abbigliamento',$uomo);
$calzatureU = $cm->categories()->add('calzature','Calzature',$uomo);
$accessoriU= $cm->categories()->add('accessori','Accessori',$uomo);

/** livello 3  */
/** abbigliamento donna  */
$abitiD = $cm->categories()->add('abiti','Abiti',$abbigliamentoD);
$camicie_topD = $cm->categories()->add('camicie-top','Camicie e Top',$abbigliamentoD);
$capi_spallaD = $cm->categories()->add('capi-spalla','Capi Spalla',$abbigliamentoD);
$piuminiD = $cm->categories()->add('piumini','Piumini',$abbigliamentoD);
$giacche_completiD = $cm->categories()->add('giacche-completi','Giacche - Completi',$abbigliamentoD);
$gonneD = $cm->categories()->add('gonne','Gonne',$abbigliamentoD);
$maglieriaD = $cm->categories()->add('maglieria','Maglieria',$abbigliamentoD);
$pantaloniD = $cm->categories()->add('pantaloni','Pantaloni',$abbigliamentoD);
$denim_tuteD = $cm->categories()->add('denim-tute','Denim e Tute',$abbigliamentoD);
$beachwear = $cm->categories()->add('beachwear','Beachwear',$abbigliamentoD);
$lingerie = $cm->categories()->add('lingerie','Lingerie',$abbigliamentoD);
/** borse donna donna */
$borse_manoD = $cm->categories()->add('borse-a-mano','Borse a Mano',$borseD);
$borse_spallaD = $cm->categories()->add('borse-a-spalla','Borse a Spalla',$borseD);
$pochette = $cm->categories()->add('pochette','Pochette',$borseD);
$zaini = $cm->categories()->add('zaini','Zaini',$borseD);
$viaggio = $cm->categories()->add('viaggio','Viaggio',$borseD);

/** calzature donna */
$scarpeD = $cm->categories()->add('scarpe','Scarpe',$calzatureD);
$stivali_stivalettiD = $cm->categories()->add('stivali-stivaletti','Stivali e Stivaletti',$calzatureD);
$sneakersD = $cm->categories()->add('sneakers','Sneakers', $calzatureD);
$infraditoD = $cm->categories()->add('infradito','Infradito', $calzatureD);
$sandaliD = $cm->categories()->add('sandali','Sandali', $calzatureD);

/** accessori donna */
$piccola_pelletteriaD = $cm->categories()->add('piccola-pelletteria','Piccola Pelletteria', $accessoriD );
$sciarpe_foulardD = $cm->categories()->add('sciarpe-foulard','Sciarpe e Foulard', $accessoriD );
$gioielliD = $cm->categories()->add('gioielli','Gioielli', $accessoriD );
$cappelliD = $cm->categories()->add('cappelli','cappelli', $accessoriD );
$orologiD = $cm->categories()->add('orologi','Orologi', $accessoriD );
$occhialiD = $cm->categories()->add('occhiali','Occhiali', $accessoriD );
$profumiD = $cm->categories()->add('profumi','Profumi', $accessoriD );


/** abbigliamento uomo */
$abitiU = $cm->categories()->add('abiti','Abiti', $abbigliamentoU );
$camicieU = $cm->categories()->add('camicie','Camicie', $abbigliamentoU );
$capi_spallaU = $cm->categories()->add('capi-spalla','Capi Spalla', $abbigliamentoU );
$piuminiU = $cm->categories()->add('piumini','Piumini', $abbigliamentoU );
$giaccheU = $cm->categories()->add('giacche','Giacche', $abbigliamentoU );
$maglieriaU = $cm->categories()->add('maglieria','Maglieria', $abbigliamentoU );
$pantaloniU = $cm->categories()->add('pantaloni','Pantaloni', $abbigliamentoU );
$denimU = $cm->categories()->add('denim','Denim', $abbigliamentoU );
$beachwearU = $cm->categories()->add('beachwear','Beachwear', $abbigliamentoU );
$uderwearU = $cm->categories()->add('underwear','Underwear', $abbigliamentoU );

/** calzature uomo */
$classicheU = $cm->categories()->add('classiche','Classiche', $calzatureU );
$stivalettiU = $cm->categories()->add('stivaletti','Stivaletti', $calzatureU );
$sneakersU = $cm->categories()->add('sneakers','Sneakers', $calzatureU );
$infraditoU = $cm->categories()->add('infradito','Infradito', $calzatureU );
$sandaliU = $cm->categories()->add('sandali','Sandali', $calzatureU );

/** accessori uomo */
$borseU = $cm->categories()->add('borse','Borse', $accessoriU );
$piccola_pelletteriaU = $cm->categories()->add('piccola-pelletteria','Piccola Pelletteria', $accessoriU );
$sciarpe_cravatteU = $cm->categories()->add('sciarpe-cravatte','Sciarpe e Cravatte', $accessoriU );
$cappelliU = $cm->categories()->add('cappelli','Cappelli', $accessoriU );
$occhialiU = $cm->categories()->add('occhiali','Occhiali', $accessoriU );
$orologiU = $cm->categories()->add('orologi','Orologi', $accessoriU );
$viaggioU = $cm->categories()->add('viaggio','Viaggio', $accessoriU );
$profumiU = $cm->categories()->add('profumi','Profumi', $accessoriU );

/** livello 4 */
/** abiti donna */
$seraD = $cm->categories()->add('sera','Sera', $abitiD );
$miniD = $cm->categories()->add('mini','Mini', $abitiD );
$ginocchiD = $cm->categories()->add('ginocchio','Ginocchio', $abitiD );
$longuetteD = $cm->categories()->add('longuette','Longuette', $abitiD );

/** camicie e top */
$camiciaD= $cm->categories()->add('camicia','Camicia', $camicie_topD);
$tshirtD = $cm->categories()->add('t-shirt','T-Shirt', $camicie_topD);
$topD = $cm->categories()->add('top','Top', $camicie_topD);
$poloD = $cm->categories()->add('polo','Polo', $camicie_topD);
$denimD = $cm->categories()->add('denim','Denim', $camicie_topD);

/** capi spalla */
$cappottiD = $cm->categories()->add('cappotti','Cappotti', $capi_spallaD);
$giacconiD = $cm->categories()->add('giacconi','Giacconi', $capi_spallaD);
$impereabiliD = $cm->categories()->add('impermeabili','Impermeabili', $capi_spallaD);
$pelleD = $cm->categories()->add('pelle','Pelle', $capi_spallaD);
$pellicciaD = $cm->categories()->add('pelliccia','Pelliccia', $capi_spallaD);

/** giacche completi */
$balzerD = $cm->categories()->add('balzer','Balzer', $giacche_completiD);
$tailleurD = $cm->categories()->add('tailleur','Tailleur', $giacche_completiD);
$giubbottiD = $cm->categories()->add('giubbotti','Giubbotti', $giacche_completiD);

/** gonne */
$gSeraD = $cm->categories()->add('sera','Sera', $gonneD);
$gMiniD = $cm->categories()->add('mini','Mini', $gonneD);
$gginiocchioD = $cm->categories()->add('ginocchio','Ginocchio', $gonneD);
$gloungetteD = $cm->categories()->add('longuette','Longuette', $gonneD);

/** maglieria */
$CachemereD = $cm->categories()->add('cachemere','Cachemere', $maglieriaD);
$CardiganD = $cm->categories()->add('cardigan','Cardigan', $maglieriaD);
$PulloverD = $cm->categories()->add('pullover','Pullover', $maglieriaD);
$FelpeD = $cm->categories()->add('felpe','Felpe', $maglieriaD);
$Twin_setD = $cm->categories()->add('twin-set','Twin set', $maglieriaD);

/** pantaloni */
$ClassiciD = $cm->categories()->add('classici','Classici', $pantaloniD);
$CapriD = $cm->categories()->add('capri','capri', $pantaloniD);
$shortD = $cm->categories()->add('short','Short', $pantaloniD);
$AmpiD = $cm->categories()->add('ampi','Ampi', $pantaloniD);
$FelpaD= $cm->categories()->add('felpa','Felpa', $pantaloniD);

/** denime e tute */
$JeansD= $cm->categories()->add('jeans','Jeans', $denim_tuteD);
$CamicieD= $cm->categories()->add('camicie','Camicie', $denim_tuteD);
$GonneD = $cm->categories()->add('gonne','Gonne', $denim_tuteD);
$GiaccheD= $cm->categories()->add('giacche','Giacche', $denim_tuteD);

/** Scarpe  */
$DecolleteD = $cm->categories()->add('decollete','Decollete', $scarpeD);
$BallerineD = $cm->categories()->add('ballerine','Ballerine', $scarpeD);
$StringateD = $cm->categories()->add('stringate','Stringate', $scarpeD);

/** Capi Spalla */
$CappottiU = $cm->categories()->add('cappotti','Cappotti', $capi_spallaU);
$ImpermeabiliU = $cm->categories()->add('impermeabili','Impermeabili', $capi_spallaU);
$PelleU = $cm->categories()->add('pelle','Pelle', $capi_spallaU);

/** Maglieria  */
$CachemereU = $cm->categories()->add('cachemire','Cachemire', $maglieriaU);
$FelpeU = $cm->categories()->add('Felpe','Felpe', $maglieriaU);
$CardiganU = $cm->categories()->add('cardigan','Cardigan', $maglieriaU);
$PulloverU = $cm->categories()->add('pullover','Pullover', $maglieriaU);

/** Pantaloni */
$ClassiciU= $cm->categories()->add('calssici','Classici', $pantaloniU);
$SportiviU= $cm->categories()->add('sportivi','Sportivi', $pantaloniU);
$BermudaU= $cm->categories()->add('bermuda','Bermuda', $pantaloniU);

/** Denim */
$JeansU= $cm->categories()->add('jeans','Jeans', $denimU);
$CamicieU= $cm->categories()->add('camicie','Camicie', $denimU);
$GiaccheU= $cm->categories()->add('giacche','Giacche', $denimU);

//$ninetyNineMonkey->repoFactory->commit();