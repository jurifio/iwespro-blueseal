<?php

ini_set("display_errors",1);
error_reporting(~0);
//require "/data/www/redpanda/htdocs/pickyshop/BlueSeal.php";

use bamboo\core\db\pandaorm\adapter\CMySQLAdapter;
//$ninetyNineMonkey = new BlueSeal('BlueSeal','pickyshop','/data/www/redpanda');
$ninetyNineMonkey->enableDebugging();

/* nome scheda prodotto (al singolare non piu lunga di 45 caratteri)*/
$sheetName = 'Generica';
$attrArr = [];
die();
$attrArr[] = 'det1';
$attrArr[] = 'det2';
$attrArr[] = 'det3';
$attrArr[] = 'det4';
$attrArr[] = 'det5';
$attrArr[] = 'det6';
$attrArr[] = 'det7';
$attrArr[] = 'det8';
$attrArr[] = 'det9';
$attrArr[] = 'det10';
$attrArr[] = 'det11';
$attrArr[] = 'det12';
$attrArr[] = 'det13';
$attrArr[] = 'det14';
$attrArr[] = 'det15';

$db = $ninetyNineMonkey->dbAdapter;
$order = 1;
//FIXME quando dobbiamo inserire qualche nuova scheda prodotto
/** singola voce per 3 lingue */
try{
    $lang = [1,2,3];
    var_dump('start insert');
    if($db->beginTransaction()){
        foreach($attrArr as $val){
            $newId = $db->query("SELECT MAX(id)+1 as 'newId' from ProductAttribute",[])->fetchAll()[0];
            $newId = $newId['newId'];
            foreach($lang as $langId){
                var_dump('newId',$newId,'langId',$langId,'val',$val,'order',$order);
                $id = $db->insert('ProductAttribute',['id'=>$newId,'langId'=>$langId,'name'=>$val,'order'=>$order]);
                var_dump($id);
            }
	        //FIXME ragiona ancora con il vecchio sistema
            $db->insert('ProductSheetPrototype',array('productAttributeId'=>$newId,'name'=>$sheetName));
            var_dump('done '.$order);
            $order++;
        }
    }
    $db->commit();
    var_dump('commit');
} catch (Exception $e){
    $db->rollBack();
    var_dump($e);
}


/*
$sheetName = 'Jeans Uomo-Donna';
$attrArr = [];

$attrArr[] = 'Fit';
$attrArr[] = 'Tessuto';
$attrArr[] = 'Tessuto II';
$attrArr[] = 'Stampe';
$attrArr[] = 'Chiusura';
$attrArr[] = 'Modello a N° Tasche';
$attrArr[] = 'Tasche frontali';
$attrArr[] = 'Tasche dietro';
$attrArr[] = 'Passanti per cintura';
$attrArr[] = 'Accessorio';
$attrArr[] = 'Dettagli I';
$attrArr[] = 'Dettagli II';
$attrArr[] = 'Lavaggio';
$attrArr[] = 'Dettagli modella';
$attrArr[] = 'Vestibilita';
$attrArr[] = 'Made In';
$attrArr[] = 'Composizione Materiali';
$attrArr[] = 'Taglia misurata';
$attrArr[] = 'Lunghezza della gamba';
$attrArr[] = 'Lunghezza interna della gamba';
$attrArr[] = 'Larghezza Bacino';
$attrArr[] = 'Fianchi';
$attrArr[] = 'Larghezza Fondo';

 *
Pantaloni Leggings Uomo Donna

$attrArr[] = 'Taglio';
$attrArr[] = 'Tessuto';
$attrArr[] = 'Tessuto II';
$attrArr[] = 'Tessuto III';
$attrArr[] = 'Stampe';
$attrArr[] = 'Chiusura';
$attrArr[] = 'Descrizione Vita';
$attrArr[] = 'Tasche Laterali';
$attrArr[] = 'Tasche dietro';
$attrArr[] = 'Passanti per cintura';
$attrArr[] = 'Accessorio';
$attrArr[] = 'Dettagli I';
$attrArr[] = 'Dettagli II';
$attrArr[] = 'Lavaggio';
$attrArr[] = 'Dettagli modella';
$attrArr[] = 'Vestibilita';
$attrArr[] = 'Made In';
$attrArr[] = 'Composizione Materiali';
$attrArr[] = 'Taglia misurata';
$attrArr[] = 'Lunghezza della gamba';
$attrArr[] = 'Lunghezza interna della gamba';
$attrArr[] = 'Larghezza Bacino';
$attrArr[] = 'Fianchi';
$attrArr[] = 'Larghezza Fondo';
*/


/*
Gonna Donna
$attrArr[] = 'Taglio';
$attrArr[] = 'Tessuto';
$attrArr[] = 'Tessuto II';
$attrArr[] = 'Tessuto III';
$attrArr[] = 'Stampe';
$attrArr[] = 'Fodera';
$attrArr[] = 'Materiale Fodera';
$attrArr[] = 'Chiusura';
$attrArr[] = 'Fascia in vita';
$attrArr[] = 'Accessorio';
$attrArr[] = 'Dettagli I';
$attrArr[] = 'Dettagli II';
$attrArr[] = 'Lavaggio';
$attrArr[] = 'Dettagli modella';
$attrArr[] = 'Vestibilita';
$attrArr[] = 'Made In';
$attrArr[] = 'Composizione Materiali';
$attrArr[] = 'Taglia misurata';
$attrArr[] = 'Lunghezza';
$attrArr[] = 'Larghezza Bacino';
$attrArr[] = 'Fianchi';
*/

/*
Abito Donna
$attrArr[] = 'Taglio';
$attrArr[] = 'Tessuto';
$attrArr[] = 'Tessuto II';
$attrArr[] = 'Tessuto III';
$attrArr[] = 'Stampe';
$attrArr[] = 'Fodera';
$attrArr[] = 'Materiale Fodera';
$attrArr[] = 'Chiusura';
$attrArr[] = 'Maniche';
$attrArr[] = 'Tasche';
$attrArr[] = 'Accessorio';
$attrArr[] = 'Dettagli I';
$attrArr[] = 'Dettagli II';
$attrArr[] = 'Lavaggio';
$attrArr[] = 'Dettagli modella';
$attrArr[] = 'Vestibilita';
$attrArr[] = 'Made In';
$attrArr[] = 'Composizione Materiali';
$attrArr[] = 'Taglia misurata';
$attrArr[] = 'Lunghezza';
$attrArr[] = 'Larghezza Torace';
$attrArr[] = 'Lunghezza Manica';
$attrArr[] = 'Larghezza Bacino';
$attrArr[] = 'Fianchi';
*/

/** @var CMySQLAdapter $db */
/*$sheetName = 'Stivali-Tronchetti';
$attrArr = [];

$attrArr[] = 'Tomaia';
$attrArr[] = 'Tomaia II';
$attrArr[] = 'Tomaia III';
$attrArr[] = 'Fodera';
$attrArr[] = 'Materiale sottopiede';
$attrArr[] = 'Chiusura 1';
$attrArr[] = 'Chiusura 2';
$attrArr[] = 'Accessorio 1';
$attrArr[] = 'Accessorio 2';
$attrArr[] = 'Materiale Tacco';
$attrArr[] = 'Altezza Tacco';
$attrArr[] = 'Materiale Plateau';
$attrArr[] = 'Altezza Plateau';
$attrArr[] = 'Materiale Suola';
$attrArr[] = 'Altezza Suola';
$attrArr[] = 'Forma';
$attrArr[] = 'Calzata (on size & fit)';
$attrArr[] = 'Made In';
$attrArr[] = 'Composizione Materiali';
$attrArr[] = 'Indossabilità';
$attrArr[] = 'Taglia misurata';
$attrArr[] = 'Gruppo H Tacco';
$attrArr[] = 'Misura suoletta interna';
$attrArr[] = 'Misura Imboccatura';
$attrArr[] = 'Misura Polpaccio';
$attrArr[] = 'Misura Caviglia';
$attrArr[] = 'H stivale con tacco';
$attrArr[] = 'H stivale senza tacco';
$attrArr[] = 'Deviazione';
$attrArr[] = 'Taglia Campione';
*/


/* attributi scheda prodotto 3 pezzi Uomo
$attrArr[] = 'Taglio';
$attrArr[] = 'Collo';
$attrArr[] = 'Maniche';
$attrArr[] = 'Tessuto';
$attrArr[] = 'Tessuto II';
$attrArr[] = 'Tessuto III';
$attrArr[] = 'Stampe';
$attrArr[] = 'Fodera';
$attrArr[] = 'Chiusura Giacca';
$attrArr[] = 'Chiusura Pantaloni';
$attrArr[] = 'Chiusura Gilet';
$attrArr[] = 'Tasche Giacca';
$attrArr[] = 'Tasche Pantaloni';
$attrArr[] = 'Tasche Gilet';
$attrArr[] = 'Tasche Retro';
$attrArr[] = 'Tasche Interne';
$attrArr[] = 'Accessorio 1';
$attrArr[] = 'Accessorio 2';
$attrArr[] = 'Accessorio 3';
$attrArr[] = 'Lavaggio';
$attrArr[] = 'Dettagli modello';
$attrArr[] = 'Vestibilita';
$attrArr[] = 'Made In';
$attrArr[] = 'Composizione Materiali';
$attrArr[] = 'Taglia misurata';
$attrArr[] = 'Lunghezza Giacca';
$attrArr[] = 'Lunghezza Manica Giacca';
$attrArr[] = 'Larghezza Torace Giacca';
$attrArr[] = 'Larghezza Manica Giacca';
$attrArr[] = 'Larghezza Torace Gilet';
$attrArr[] = 'Lunghezza Gilet';
$attrArr[] = 'Larghezza Bacino Bacino';
$attrArr[] = 'Larghezza Bacino Pantaloni';
$attrArr[] = 'Fianchi';
$attrArr[] = 'Larghezza del girovita';
$attrArr[] = 'Larghezza della gamba';
$attrArr[] = 'Lunghezza interna della gamba';
$attrArr[] = 'Larghezza Fondo';
 */

/* GIACCHE
$attrArr[] = ['1'=>'Taglio', '2'=>'Style', '3'=>'Muster'];
$attrArr[] = ['1'=>'Collo', '2'=>'Neck type', '3'=>'Ausschnitt'];
$attrArr[] = ['1'=>'Maniche', '2'=>'Sleeves', '3'=>'Ärmellänge'];
$attrArr[] = ['1'=>'Tessuto', '2'=>'Textile composition', '3'=>'Material Oberstoff'];
$attrArr[] = ['1'=>'Tessuto II', '2'=>'Textile', '3'=>'Material II'];
$attrArr[] = ['1'=>'Tessuto III', '2'=>'Textile', '3'=>'Material III'];
$attrArr[] = ['1'=>'Stampe', '2'=>'Prints', '3'=>'Print'];
$attrArr[] = ['1'=>'Fodera', '2'=>'Lining', '3'=>'Futter'];
$attrArr[] = ['1'=>'Chiusura 1', '2'=>'Closure 1', '3'=>'Verschluss 1'];
$attrArr[] = ['1'=>'Chiusura 2', '2'=>'Closure 2', '3'=>'Verschluss 2'];
$attrArr[] = ['1'=>'Tasche', '2'=>'Pockets number', '3'=>'Taschen'];
$attrArr[] = ['1'=>'Tasche Retro', '2'=>'Back pockets', '3'=>'Hintertaschen'];
$attrArr[] = ['1'=>'Tasche Interne', '2'=>'Interior pockets', '3'=>'Interne Taschen'];
$attrArr[] = ['1'=>'Accessorio 1', '2'=>'Accessories 1', '3'=>'Accessoires 1'];
$attrArr[] = ['1'=>'Accessorio 2', '2'=>'Accessories 2', '3'=>'Accessoires 2'];
$attrArr[] = ['1'=>'Lavaggio', '2'=>'Wash at', '3'=>'Pflegehinweise'];
$attrArr[] = ['1'=>'Dettagli modella', '2'=>'Models Measurements', '3'=>'Modelmaße'];
$attrArr[] = ['1'=>'Vestibilita', '2'=>'Fits to size', '3'=>'Passform'];
$attrArr[] = ['1'=>'Made In', '2'=>'Made in', '3'=>'Made in'];
$attrArr[] = ['1'=>'Composizione Materiali', '2'=>'Composition', '3'=>'Material Oberstoff'];
$attrArr[] = ['1'=>'Taglia misurata', '2'=>'Sample size', '3'=>'Referenzgröße'];
$attrArr[] = ['1'=>'Lunghezza', '2'=>'Length', '3'=>'Länge'];
$attrArr[] = ['1'=>'Larghezza Torace', '2'=>'Bust/Chest (cm)', '3'=>'Brustbreite'];
$attrArr[] = ['1'=>'Larghezza Manica', '2'=>'Sleeve length', '3'=>'Ärmellbreite'];
$attrArr[] = ['1'=>'Larghezza Bacino', '2'=>'Waist (cm):', '3'=>'Taillenbreite'];
$attrArr[] = ['1'=>'Fianchi', '2'=>'Hips (cm)', '3'=>'Hüftbreite'];
$attrArr[] = ['1'=>'Larghezza del girovita', '2'=>'Waistline', '3'=>'Taillebreite'];
$attrArr[] = ['1'=>'Larghezza della gamba', '2'=>'Leg waist', '3'=>'Beinbreite'];
$attrArr[] = ['1'=>'Lunghezza interna della gamba', '2'=>'Inner leg length', '3'=>'Beininnenlänge'];
*/

/* OCCHIALI
$attrArr[] = ['1'=>'Lenti', '2'=>'Lens', '3'=>'Gläser'];
$attrArr[] = ['1'=>'Montatura', '2'=>'Bridge', '3'=>'Rahmen'];
$attrArr[] = ['1'=>'Filtri protezione', '2'=>'UV filter', '3'=>'UV-Filter'];
$attrArr[] = ['1'=>'Protezione UV', '2'=>'UV protection', '3'=>'UV Schutz'];
$attrArr[] = ['1'=>'Custodia', '2'=>'Case', '3'=>'Fall für Gläser'];
$attrArr[] = ['1'=>'Frame Larghezza', '2'=>'Bridge width', '3'=>'Rahmenbreite'];
$attrArr[] = ['1'=>'Frame Altezza', '2'=>'Bridge hight', '3'=>'Fronthöhe'];
$attrArr[] = ['1'=>'Diametro Lente', '2'=>'Lens diameter', '3'=>'Glasbreite'];
$attrArr[] = ['1'=>'Made In', '2'=>'Made in', '3'=>'Made in']; */
/* BORSE
$attrArr[] = ['1'=>'Pellame Patta 1', '2'=>'Leather Flap 1', '3'=>'Obermaterial 1'];
$attrArr[] = ['1'=>'Pellame Patta 2', '2'=>'Leather Flap 2', '3'=>'Obermaterial 2'];
$attrArr[] = ['1'=>'Pellame Retro', '2'=>'Back leather', '3'=>'Hintermaterial'];
$attrArr[] = ['1'=>'Pellame Soffietti', '2'=>'Leather bellows', '3'=>'Material'];
$attrArr[] = ['1'=>'Fodera', '2'=>'Lining', '3'=>'Futter'];
$attrArr[] = ['1'=>'Numero tasche interne', '2'=>'Internal patch pockets number', '3'=>'Interne aufgesetzte Taschen Nummer '];
$attrArr[] = ['1'=>'Numero divisori interni', '2'=>'Internal compartments number ', '3'=>'interne Hauptfächer Nummer'];
$attrArr[] = ['1'=>'Chiusura Interna', '2'=>'Interior closure', '3'=>'Interne Verschluss '];
$attrArr[] = ['1'=>'Piedini di Protezione', '2'=>'Studs at base ', '3'=>'Schutzfüßchen am Taschenboden'];
$attrArr[] = ['1'=>'Tipo tracolla', '2'=>'Shoulder Strap', '3'=>'Schulterriemen'];
$attrArr[] = ['1'=>'Chiusura 1', '2'=>'Closure 1', '3'=>'Verschluss 1'];
$attrArr[] = ['1'=>'Chiusura 2', '2'=>'Closure 2', '3'=>'Verschluss 2'];
$attrArr[] = ['1'=>'Accessorio 1', '2'=>'Accessories/Applications/detailing 1', '3'=>'Accessoires 1'];
$attrArr[] = ['1'=>'Accessorio 2', '2'=>'Accessories/Applications/detailing 2', '3'=>'Accessoires 2'];
$attrArr[] = ['1'=>'Forma', '2'=>'Lined/Shape', '3'=>'Muster'];
$attrArr[] = ['1'=>'Misurazione Altezza', '2'=>'Height', '3'=>'Höhe'];
$attrArr[] = ['1'=>'Misurazione Larghezza Max', '2'=>'Width', '3'=>'Breite'];
$attrArr[] = ['1'=>'Misurazioni Profondità', '2'=>'Depth', '3'=>'Tiefe'];
$attrArr[] = ['1'=>'Lunghezza Tracolla', '2'=>'Handle', '3'=>'Schulterriemen Länge'];
$attrArr[] = ['1'=>'Made In', '2'=>'Made In', '3'=>'Made In'];
*/
// OCCHIALI
/*
$attrArr[] = ['1'=>'Colore Cassa', '2'=>'Case colour', '3'=>'Gehäusefarbe'];
$attrArr[] = ['1'=>'Materiale Cassa', '2'=>'Case material', '3'=>'Gehäusematerial'];
$attrArr[] = ['1'=>'Colore quadrante', '2'=>'Dial colour', '3'=>'Zifferblattfarbe'];
$attrArr[] = ['1'=>'Materiale Cinturino', '2'=>'Strap material', '3'=>'Uhrenarmband Material'];
$attrArr[] = ['1'=>'Colore Cinturino', '2'=>'Strap colour', '3'=>'Uhrenarmband Farbe'];
$attrArr[] = ['1'=>'Custodia', '2'=>'Case of watches', '3'=>'Fall für Armbanduhr '];
$attrArr[] = ['1'=>'Diamanti e pietre', '2'=>'Diamonds and stones', '3'=>'Diamanten und Edelsteinen'];
$attrArr[] = ['1'=>'Dettaglio 1', '2'=>'Details 1', '3'=>'Detail 1'];
$attrArr[] = ['1'=>'Dettaglio 2', '2'=>'Details 2', '3'=>'Detail 2'];
$attrArr[] = ['1'=>'Dettaglio 3', '2'=>'Details 3', '3'=>'Detail 3'];
$attrArr[] = ['1'=>'Corona', '2'=>'Crown', '3'=>'Krone'];
$attrArr[] = ['1'=>'Ghiera', '2'=>'Bezel', '3'=>'drehbare Lünette'];
$attrArr[] = ['1'=>'Lancette', '2'=>'Hands', '3'=>'Zeiger'];
$attrArr[] = ['1'=>'Tipo Movimento', '2'=>'Movement', '3'=>'Antrieb und Funktionen'];
$attrArr[] = ['1'=>'Chiusura Cinturino', '2'=>'Buckle', '3'=>'Bandschließe'];
$attrArr[] = ['1'=>'Larghezza Cinturino', '2'=>'Spring lug', '3'=>'Armbandgröße'];
$attrArr[] = ['1'=>'Circonferenza Minima', '2'=>'Least circumference', '3'=>'Mindestumfang'];
$attrArr[] = ['1'=>'Diametro Cassa', '2'=>'Diameter case', '3'=>'Gehäusehöhe'];
$attrArr[] = ['1'=>'Made In', '2'=>'Made in', '3'=>'Made in'];
*/

?>
