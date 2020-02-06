<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui','forms'],$page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
</head>
<body class="fixed-header">
<?php include "parts/sidebar.php"; ?>
<div class="page-container">
    <?php include "parts/header.php" ?>
    <?php include "parts/operations.php" ?>

    <div class="page-content-wrapper">
        <div class="content sm-gutter">
            <div class="container-fluid container-fixed-lg bg-white">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 alert-container closed"></div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="tab">
                        <div class="col-md-12">
                            <button class="tablinks" onclick="openTab(event, 'insertClient')">Dati Cliente</button>
                            <button class="tablinks" onclick="openTab(event, 'insertClientBillingInfo')">Dati
                                Amministrativi
                            </button>
                            <button class="tablinks" onclick="openTab(event, 'insertClientAccount')">Account
                            </button>
                            <button class="tablinks" onclick="openTab(event, 'insertClientLocation')">Sedi e Filiali
                            </button>
                            <button class="tablinks" onclick="openTab(event, 'insertClientContact')">Contatti</button>
                            <button class="tablinks" onclick="openTab(event, 'insertClientContract')">Contratti</button>
                        </div>
                    </div>
                </div>

                <div id="insertClient" class="tabcontent">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-12">Informazioni di base</h5>
                                <input type="hidden" id="billRegistryClientId" name="billRegistryClientId"
                                       value="<?php echo $brc->id ?>"/>
                                <input type="hidden" id="billRegistryClientAccountId" name="billRegistryClientAccountId"
                                       value="<?php echo $brca->id ?>"/>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="companyName">Nome Cliente</label>
                                        <input id="companyName" autocomplete="off" type="text"
                                               class="form-control" name="companyName"
                                               value="<?php echo $brc->companyName; ?>"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="address">indirizzo</label>
                                        <input id="address" autocomplete="off" type="text"
                                               class="form-control" name="address" value="<?php echo $brc->address; ?>"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="extra">Indirizzo 2</label>
                                        <input id="extra" autocomplete="off" type="text"
                                               class="form-control" name="extra" value="<?php echo $brc->extra; ?>"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="city">città</label>
                                        <input id="city" autocomplete="off" type="text"
                                               class="form-control" name="city" value="<?php echo $brc->city; ?>"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="zipCode">CAP</label>
                                        <input id="zipCode" autocomplete="off" type="text"
                                               class="form-control" name="zipCode" value="<?php echo $brc->zipcode; ?>"
                                        />

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="province">Provincia</label>
                                        <input id="province" autocomplete="off" type="text"
                                               class="form-control" name="province"
                                               value="<?php echo $brc->province; ?>"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="countryId">Seleziona la Nazione </label>
                                        <select id="countryId" name="countryId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <?php foreach ($country as $countries) {
                                                if ($countries->id == $brc->countryId) {
                                                    echo '<option  selected="selected" value="' . $countries->id . '">' . $countries->name . '</option>';
                                                } else {
                                                    echo '<option value="' . $countries->id . '">' . $countries->name . '</option>';
                                                }
                                            }; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="vatNumber">Partita Iva/Codice Fiscale</label>
                                        <input id="vatNumber" autocomplete="off" type="text"
                                               class="form-control" name="vatNumber"
                                               value="<?php echo $brc->vatNumber; ?>"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="phone">Telefono</label>
                                        <input id="phone" autocomplete="off" type="text"
                                               class="form-control" name="phone" value="<?php echo $brc->phone; ?>"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default ">
                                        <label for="mobile">Mobile</label>
                                        <input id="mobile" autocomplete="off" type="text"
                                               class="form-control" name="mobile" value="<?php echo $brc->mobile; ?>"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="fax">Fax</label>
                                        <input id="fax" autocomplete="off" type="text"
                                               class="form-control" name="fax" value="<?php echo $brc->fax; ?>"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="userId">Seleziona l'utente </label>
                                        <select id="userId" name="userId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <?php foreach ($userDetails as $userDetail) {
                                                if ($userDetail->userId == $brc->userId) {
                                                    echo '<option  selected="selected" value="' . $userDetail->userId . '">' . $userDetail->name . '-' . $userDetail->surname . ' </option>';
                                                } else {
                                                    echo '<option  value="' . $userDetail->userId . '">' . $userDetail->name . '-' . $userDetail->surname . ' </option>';
                                                }
                                            }; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="contactName"> Nome Contatto </label>
                                        <input id="contactName" autocomplete="off" type="text"
                                               class="form-control" name="contactName"
                                               value="<?php echo $brc->contactName; ?>"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="phoneAdmin"> Telefono contatto Amministratore </label>
                                        <input id="phoneAdmin" autocomplete="off" type="text"
                                               class="form-control" name="phoneAdmin"
                                               value="<?php echo $brc->phoneAdmin; ?>"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="mobileAdmin"> Mobile Contatto Amministratore </label>
                                        <input id="mobileAdmin" autocomplete="off" type="text"
                                               class="form-control" name="mobileAdmin"
                                               value="<?php echo $brc->mobileAdmin; ?>"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailAdmin"> Email Amministratore </label>
                                        <input id="emailAdmin" autocomplete="off" type="text"
                                               class="form-control" name="emailAdmin"
                                               value="<?php echo $brc->emailAdmin; ?>"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="website"> WebSite</label>
                                        <input id="website" autocomplete="off" type="text"
                                               class="form-control" name="website" value="<?php echo $brc->website; ?>"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="email"> email Azienda </label>
                                        <input id="email" autocomplete="off" type="text"
                                               class="form-control" name="email" value="<?php echo $brc->email; ?>"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailCc"> email Azienda CC </label>
                                        <input id="emailCc" autocomplete="off" type="text"
                                               class="form-control" name="emailCc" value="<?php echo $brc->emailCc; ?>"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailCcn"> email Azienda CCn </label>
                                        <input id="emailCcn" autocomplete="off" type="text"
                                               class="form-control" name="emailCcn"
                                               value="<?php echo $brc->emailCcn; ?>"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="emailPec"> PEC</label>
                                        <input id="emailPec" autocomplete="off" type="text"
                                               class="form-control" name="emailPec"
                                               value="<?php echo $brc->emailPec; ?>"
                                        />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default">
                                        <label for="note"> Note</label>
                                        <textarea class="form-control" name="note" id="note"
                                                  value="<?php echo $brc->note; ?>"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="insertClientBillingInfo" class="tabcontent">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-12"> Inserimento Dati amministrativi </h5>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="bankRegistryId">Seleziona la Banca di Appoggio</label>
                                        <select id="bankRegistryId" name="bankRegistryId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <?php foreach ($bankRegistry as $bank) {
                                                if ($bank->id == $brcbi->bankRegistryId) {
                                                    echo '<option  selected="selected" value="' . $bank->id . '">' . $bank->name . ' ' . $bank->location . ' </option>';
                                                } else {
                                                    echo '<option  value="' . $bank->id . '">' . $bank->name . ' ' . $bank->location . ' </option>';
                                                }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group form-group-default">
                                        <label for="iban">Iban</label>
                                        <input id="iban" autocomplete="off" type="text"
                                               class="form-control" name="iban" value="<?php echo $brcbi->iban; ?>"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="currencyId">Seleziona la divisa </label>
                                        <select id="currencyId" name="currencyId"
                                                class="full-width selectpicker"
                                                data-init-plugin="selectize">
                                            <?php foreach ($currency as $curr) {
                                                if ($brcbi->currencyId == $curr->id) {
                                                    echo '<option  selected="selected" value="' . $curr->id . '">' . $curr->code . '</option>';
                                                } else {
                                                    echo '<option  value="' . $curr->id . '">' . $curr->code . '</option>';
                                                }
                                            }; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="billRegistryTypePaymentId"> Seleziona il Pagamento
                                            desiderato </label>
                                        <select id="billRegistryTypePaymentId" name="billRegistryTypePaymentId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <?php foreach ($billRegistryTypePayment as $tp) {
                                                if ($tp->id == $brcbi->billRegistryTypePaymentId) {
                                                    echo '<option  selected="selected" value="' . $tp->id . '">' . $tp->name . '</option>';
                                                } else {
                                                    echo '<option  value="' . $tp->id . '">' . $tp->name . '</option>';
                                                }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="billRegistryTypeTaxesId"> Seleziona l'aliquota Iva</label>
                                        <select id="billRegistryTypeTaxesId" name="billRegistryTypeTaxesId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <?php foreach ($billRegistryTypeTaxes as $tt) {
                                                if ($tt->id == $brcbi->billRegistryTypeTaxesId) {
                                                    echo '<option  selected="selected" value="' . $tt->id . '">' . $tt->description . '</option>';
                                                } else {
                                                    echo '<option  value="' . $tt->id . '">' . $tt->description . '</option>';
                                                }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="sdi">Codice UNIVOCO SDI</label>
                                        <input id="sdi" autocomplete="off" type="text"
                                               class="form-control" name="sdi" value="<?php echo $brcbi->sdi; ?>"
                                        />
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div id="insertClientAccount" class="tabcontent">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-12">Inserimento Parametri Account Cliente</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="accountStatusId">Seleziona lo Stato del Cliente</label>
                                        <select id="accountStatusId" name="accountStatusId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <?php switch ($brca->accountStatusId) {
                                                case 1:
                                                    $selectActive = 'selected="selected"';
                                                    $selectNotActive = '';
                                                    $selectSuspend = '';
                                                    break;
                                                case 0:
                                                    $selectActive = '';
                                                    $selectNotActive = 'selected="selected"';
                                                    $selectSuspend = '';
                                                    break;
                                                case 2:
                                                    $selectActive = '';
                                                    $selectNotActive = '';
                                                    $selectSuspend = 'selected="selected"';
                                                    break;

                                            }
                                            ?>
                                            <option value=""></option>
                                            <option <?php echo $selectActive; ?> value="1">Attivo</option>
                                            <option <?php echo $selectNotActive; ?> value="0">non Attivo</option>
                                            <option <?php echo $selectSuspend; ?> value="2">sospeso</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="dateActivation">Data Attivazione</label>
                                        <?php $dateActivation = strtotime($brca->dateActivation);
                                        $dateActivation = date('Y-m-d\TH:i',$dateActivation); ?>
                                        <input type="datetime-local" class="form-control" id="dateActivation"
                                               name="dateActivation" value="<?php echo $dateActivation; ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="accountAsFriend">Seleziona Se Friend</label>
                                        <select id="accountAsFriend" name="accountAsFriend"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <?php switch ($brca->accountAsFriend) {
                                                case 1:
                                                    $selectActiveFriend = 'selected="selected"';
                                                    $selectNotActiveFriend = '';
                                                    break;
                                                case 0:
                                                    $selectActiveFriend = '';
                                                    $selectNotActiveFriend = 'selected="selected"';
                                                    break;

                                            }
                                            ?>
                                            <option value=""></option>
                                            <option <?php echo $selectActiveFriend; ?> value="1">Si</option>
                                            <option <?php echo $selectNotActiveFriend; ?> value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="typeFriendId">Seleziona il tipo di Friend</label>
                                        <select id="typeFriendId" name="typeFriendId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <?php foreach ($typeFriend as $tf) {
                                                if ($tf->id == $brca->typeFriendId) {
                                                    echo '<option  selected="selected" value="' . $tf->rating . '">' . $tf->name . '</option>';
                                                } else {
                                                    echo '<option  value="' . $tf->rating . '">' . $tf->name . '</option>';
                                                }
                                            } ?>

                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2" id="rating">
                                    <?php
                                    $checkStar1 = '';
                                    $checkStar2 = '';
                                    $checkStar3 = '';
                                    $checkStar4 = '';
                                    $checkStar5 = '';

                                    switch ($brca->typeFriendId) {
                                        case 1:

                                            $checkStar1 = 'checked';
                                            $checkStar2 = '';
                                            $checkStar3 = '';
                                            $checkStar4 = '';
                                            $checkStar5 = '';
                                            break;

                                        case 2:
                                            $checkStar1 = 'checked';
                                            $checkStar2 = 'checked';
                                            $checkStar3 = '';
                                            $checkStar4 = '';
                                            $checkStar5 = '';
                                            break;
                                        case 3:
                                            $checkStar1 = 'checked';
                                            $checkStar2 = 'checked';
                                            $checkStar3 = 'checked';
                                            $checkStar4 = '';
                                            $checkStar5 = '';
                                            break;
                                        case 4:
                                            $checkStar1 = 'checked';
                                            $checkStar2 = 'checked';
                                            $checkStar3 = 'checked';
                                            $checkStar4 = 'checked';
                                            $checkStar5 = '';
                                            break;
                                        case 5:
                                            $checkStar1 = 'checked';
                                            $checkStar2 = 'checked';
                                            $checkStar3 = 'checked';
                                            $checkStar4 = 'checked';
                                            $checkStar5 = 'checked';
                                            break;
                                        default:
                                            $checkStar1 = '';
                                            $checkStar2 = '';
                                            $checkStar3 = '';
                                            $checkStar4 = '';
                                            $checkStar5 = '';
                                    }
                                    ?>
                                    <span class="fa fa-star <?php echo $checkStar1 ?>"></span>
                                    <span class="fa fa-star <?php echo $checkStar2 ?>"></span>
                                    <span class="fa fa-star <?php echo $checkStar3 ?>"></span>
                                    <span class="fa fa-star <?php echo $checkStar4 ?>"></span>
                                    <span class="fa fa-star <?php echo $checkStar5 ?>"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="shopId">Seleziona Se ha uno Shop</label>
                                        <select id="shopId" name="shopId"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <?php foreach ($shop as $shops) {
                                                if ($shops->id == $brca->shopId) {
                                                    echo '<option  selected="selected" value="' . $shops->id . '">' . $shops->name . '</option>';
                                                } else {
                                                    echo '<option  value="' . $shops->id . '">' . $shops->name . '</option>';
                                                }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="accountAsParallel">Seleziona Se è Parallelo</label>
                                        <select id="accountAsParallel" name="accountAsParallel"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <?php switch ($brca->accountAsParallel) {
                                                case 1:
                                                    $selectActiveAsP = 'selected="selected"';
                                                    $selectNotActiveAsP = '';
                                                    break;
                                                case 0:
                                                    $selectActiveAsP = '';
                                                    $selectNotActiveAsP = 'selected="selected"';
                                                    break;

                                            }
                                            ?>
                                            <option value=""></option>
                                            <option <?php echo $selectActiveAsP; ?> value="1">Si</option>
                                            <option <?php echo $selectNotActiveAsP; ?> value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php if ($brca->accountAsParallel == 1) {
                                $classRawParallel = '';
                            } else {
                                $classRawParallel = 'hide';
                            } ?>
                            <div class="row <?php echo $classRawParallel; ?>" id="rawParallel">
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="accountAsParallelSupplier">Seleziona Se è Supplier </label>
                                        <select id="accountAsParallelSupplier" name="accountAsParallelSupplier"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <?php switch ($brca->accountAsParallel) {
                                                case 1:
                                                    $selectActiveAsPS = 'selected="selected"';
                                                    $selectNotActiveAsPS = '';
                                                    break;
                                                case 0:
                                                    $selectActiveAsPS = '';
                                                    $selectNotActiveAsPS = 'selected="selected"';
                                                    break;

                                            }
                                            ?>
                                            <option value=""></option>
                                            <option <?php echo $selectActiveAsPS; ?> value="1">Si</option>
                                            <option <?php echo $selectNotActiveAsPS; ?> value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="accountAsParallelSeller">Seleziona Se è Seller </label>
                                        <select id="accountAsParallelSeller" name="accountAsParallelSeller"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <?php switch ($brca->accountAsParallel) {
                                                case 1:
                                                    $selectActiveAsPSS = 'selected="selected"';
                                                    $selectNotActiveAsPSS = '';
                                                    break;
                                                case 0:
                                                    $selectActiveAsPSS = '';
                                                    $selectNotActiveAsPSS = 'selected="selected"';
                                                    break;

                                            }
                                            ?>
                                            <option value=""></option>
                                            <option <?php echo $selectActiveAsPSS; ?> value="1">Si</option>
                                            <option <?php echo $selectNotActiveAsPSS; ?> value="0">No</option>
                                            <option value=""></option>
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group form-group-default">
                                        <label for="parallelFee">Fee riconosciuta sul Parallelo</label>
                                        <input id="parallelFee" autocomplete="off" type="text"
                                               class="form-control" name="parallelFee"
                                               value="<?php echo $brca->parallelFee; ?>"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group form-group-default selectize-enabled">
                                        <label for="accountAsService">Seleziona Se ha Servizi</label>
                                        <select id="accountAsService" name="accountAsService"
                                                class="full-width selectpicker"
                                                placeholder="Seleziona la Lista"
                                                data-init-plugin="selectize">
                                            <?php switch ($brca->accountAsService) {
                                                case 1:
                                                    $selectActiveAsS = 'selected="selected"';
                                                    $selectNotActiveAsS = '';
                                                    break;
                                                case 0:
                                                    $selectActiveAsS = '';
                                                    $selectNotActiveAsS = 'selected="selected"';
                                                    break;

                                            }
                                            ?>
                                            <option value=""></option>
                                            <option <?php echo $selectActiveAsS; ?> value="1">Si</option>
                                            <option <?php echo $selectNotActiveAsS; ?> value="0">No</option>
                                            <option value=""></option>
                                            <option value="1">Si</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="rawProduct">
                                <?php echo $bodyres = ''; ?>
                                <?php if ($brca->accountAsService == 1) {
                                    $bodyres .= '<div class="row"><div class="col-md-4"><input type="text" id="myInput" onkeyup="myFunction()" placeholder="ricerca per Categoria"></div>';
                                    $bodyres .= '<div class="col-md-4"><input type="text" id="myShop" onkeyup="myShopFunction()" placeholder="ricerca per Codice"></div>';
                                    $bodyres .= '<div class="col-md-4"><input type="checkbox" class="form-control"  id="checkedAll" name="checkedAll"></div></div>';
                                    $bodyres .= '<table id="myTable"> <tr class="header1"><th style="width:40%;">Categoria</th><th style="width:20%;">Codice Prodotto</th><th style="width:20%;">Nome Prodotto</th><th style="width:20%;">Selezione</th></tr>';

                                    foreach ($brp as $product) {
                                        $brcahp = \Monkey::app()->repoFactory->create('BillRegistryClientAccountHasProduct')->findOneBy(['billRegistryProductId' => $product->id,'billRegistryClientAccountId' => $brca->id]);
                                        if ($brcahp != null) {
                                            $checked = 'checked="checked"';
                                        } else {
                                            $checked = '';
                                        }
                                        $brcp = \Monkey::app()->repoFactory->create('BillRegistryCategoryProduct')->findOneBy(['id' => $product->billRegistryCategoryProductId]);
                                        $categoryName = $brcp->name;
                                        $codeProduct = $product->codeProduct;
                                        $nameProduct = $product->name;
                                        $bodyres .= '<tr><td style="width:40%;">' . $categoryName . '</td><td style="width:40%;">' . $codeProduct . '</td><td style="width:40%;">' . $nameProduct . '</td><td style="width:20%;"><input type="checkbox" ' . $checked . ' class="form-control"  name="selected_values[]" value="' . $product->id . '"></td></tr>';

                                    }
                                    $bodyres .= '</table>';
                                    echo $bodyres;

                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="insertClientLocation" class="tabcontent">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel-heading clearfix">
                                <h5 class="m-t-12">Inserimento Filiali</h5>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="success" id="addLocation" type="button"><span
                                        class="fa fa-plus-circle">Aggiungi Filiale</span></button>
                        </div>
                    </div>
                    <div class="row">
                        <?php
                        $bodyLocation = '<div class="row"><div class="col-md-6"><input type="text" id="myInputLocation" onkeyup="myFunctionLocation()" placeholder="ricerca per nome"></div>';
                        $bodyLocation .= '<div class="col-md-6"><input type="text" id="myShopLocation" onkeyup="myShopFunctionLocation()" placeholder="ricerca per città"></div></div>';

                        $bodyLocation .= '<table id="myTableLocation"> <tr class="header2"><th style="width:20%;">id</th><th style="width:20%;">Nome Sede</th><th style="width:20%;">Città</th><th style="width:20%;">Modifica</th><th style="width:20%;">Elimina</th></tr>';

                        ?>
                        <div id="rawLocation">
                            <?php foreach ($brcl as $location) {
                                $bodyLocation .= '<tr id="trLocation' . $location->id . '"><td>' . $location->id . '</td><td>' . $location->name . '</td><td>' . $location->city . '</td><td><button class="success" id="editLocation" onclick="editLocation(' . $location->id . ')" type="button"><span
                                        class="fa fa-pencil">Modifica</span></button></td><td><button class="success" id="deleteLocation"  onclick="deleteLocation(' . $location->id . ')" type="button"><span
                                        class="fa fa-eraser">Elimina</span></button></td></tr>';
                            }
                            echo $bodyLocation;
                            ?>
                        </div>
                        </table>
                    </div>
                </div>
            </div>
            <div id="insertClientContact" class="tabcontent">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel-heading clearfix">
                            <h5 class="m-t-12">Inserimento Contatti</h5>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="success" id="addContact" type="button"><span
                                    class="fa fa-plus-circle">Aggiungi contatto</span></button>
                    </div>
                </div>
                <div class="row">
                    <?php
                    $bodyContact = '<div class="row"><div class="col-md-6"><input type="text" id="myInputContact" onkeyup="myFunctionContact()" placeholder="ricerca per nome"></div>';
                    $bodyContact .= '<div class="col-md-6"><input type="text" id="myShopContact" onkeyup="myShopFunctionContact()" placeholder="ricerca per Email"></div></div>';

                    $bodyContact .= '<table id="myTableContact"> <tr class="header3"><th style="width:20%;">id</th><th style="width:20%;">Nome Contatto</th><th style="width:20%;">Email-Telefono</th><th style="width:20%;">Modifica</th><th style="width:20%;">Elimina</th></tr>';

                    ?>
                    <div id="rawContact">
                        <?php foreach ($brcc as $contact) {
                            $bodyContact .= '<tr id="trContact' . $contact->id . '"><td>' . $contact->id . '</td><td>' . $contact->name . '</td><td>' . $contact->email . '-' . $contact->phone . '</td><td><button class="success" id="editContact" onclick="editContact(' . $contact->id . ')" type="button"><span
                                        class="fa fa-pencil">Modifica</span></button></td><td><button class="success" id="deleteContact"  onclick="deleteContact(' . $contact->id . ')" type="button"><span
                                        class="fa fa-eraser">Elimina</span></button></td></tr>';
                        }
                        echo $bodyContact;
                        ?>
                    </div>
                    </table>
                </div>
            </div>
        </div>
            <div id="insertClientContract" class="tabcontent">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel-heading clearfix">
                            <h5 class="m-t-12">Inserimento Contratti</h5>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button class="success" id="addContract" type="button"><span
                                    class="fa fa-plus-circle">Aggiungi contratto</span></button>
                    </div>
                </div>
                <div class="row">
                    <?php
                    $bodyContract = '<div class="row"><div class="col-md-6"><input type="text" id="myInputContract" onkeyup="myFunctionContract()" placeholder="ricerca per id contratto"></div>';
                    $bodyContract .= '<div class="col-md-6"><input type="text" id="myShopContract" onkeyup="myShopFunctionContract()" placeholder="ricerca per data Creazione"></div></div>';

                    $bodyContract .= '<table id="myTableContract"><tr class="header4"><th style="width:20%;">id contratto</th><th style="width:20%;">data Creazione</th><th style="width:20%;">data Scadenza</th><th style="width:10%;">Modifica<br>Testata</th><th style="width:10%;">Inserisci<br>Dettagli</th><th style="width:10%;">Lista<br>Dettagli</th><th style="width:10%;">Elimina<br>Contratto</th></tr>';

                    ?>
                    <div id="rawContract">
                        <?php foreach ($brcContract as $contract) {
                            $bodyContract .='<tr id="trContract'.$contract->id.'"><td>'.$contract->id.'-'.$contract->billRegistryClientId.'-'.$contract->billRegistryClientAccountId.'</td>';
                            $bodyContract .='<td>'.$contract->dateContractExpire.'</td><td>'.$contract->dateCreate.'</td>';
                            $bodyContract.='<td><button class="success" id="editContract" onclick="editContract(' . $contract->id . ')" type="button"><span class="fa fa-pencil">Modifica</span></button></td>';
                            $bodyContract.='<td><button class="success" id="addContractDetail" onclick="addContractDetail(' . $contract->id . ')" type="button"><span class="fa fa-plus-circle">Aggiungi</span></button></td>';
                            $bodyContract.='<td><button class="success" id="listContractDetail" onclick="listContractDetail(' . $contract->id . ')" type="button"><span class="fa fa-list">Elenca</span></button></td>';
                            $bodyContract.='<td><button class="success" id="deleteContract"  onclick="deleteContract('. $contract->id .')" type="button"><span class="fa fa-eraser">Elimina</span></button></td></tr>';
                        }
                        echo $bodyContract;
                        ?>
                    </div>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "parts/footer.php" ?>
</div>
<?php include "parts/bsmodal.php"; ?>
<?php include "parts/alert.php"; ?>
<bs-toolbar class="toolbar-definition">
    <bs-toolbar-group data-group-label="Operazioni Cliente">
        <bs-toolbar-button
                data-tag="a"
                data-icon="fa-floppy-o"
                data-permission="/admin/product/add"
                data-event="bs.client.save"
                data-class="btn btn-default"
                data-rel="tooltip"
                data-title="Salva"
                data-placement="bottom">
</bs-toolbar>
</body>
</html>



