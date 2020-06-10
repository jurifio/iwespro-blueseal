
<html lang="<?php echo $app->lang(); ?>">
<head>
    <meta charset="UTF-8"/>
    <style type="text/css">


        @page {
            size: A4;
            margin: 5mm 0mm 0mm 0mm;
        }

        @media print {
            body {
                zoom: 100%;
                width: 800px;
                height: 1100px;
                overflow: hidden;
            }

            .container {
                width: 100%;
            }

            .newpage {
                page-break-before: always;
                page-break-after: always;
                page-break-inside: avoid;
            }

            @page {
                size: A4;
                margin: 5mm 0mm 0mm 0mm;
            }

            .cover {
                display: none;
            }

            .page-container {
                display: block;
            }

            /*remove chrome links*/
            a[href]:after {
                content: none !important;
            }

            .col-md-1,
            .col-md-2,
            .col-md-3,
            .col-md-4,
            .col-md-5,
            .col-md-6,
            .col-md-7,
            .col-md-8,
            .col-md-9,
            .col-md-10,
            .col-md-11,
            .col-md-12 {
                float: left;
            }

            .col-md-12 {
                width: 100%;
            }

            .col-md-11 {
                width: 91.66666666666666%;
            }

            .col-md-10 {
                width: 83.33333333333334%;
            }

            .col-md-9 {
                width: 75%;
            }

            .col-md-8 {
                width: 66.66666666666666%;
            }

            .col-md-7 {
                width: 58.333333333333336%;
            }

            .col-md-6 {
                width: 50%;
            }

            .col-md-5 {
                width: 41.66666666666667%;
            }

            .col-md-4 {
                width: 33.33333333333333%;
            }

            .col-md-3 {
                width: 25%;
            }

            .col-md-2 {
                width: 16.666666666666664%;
            }

            .col-md-1 {
                width: 8.333333333333332%;
            }

            .col-md-pull-12 {
                right: 100%;
            }

            .col-md-pull-11 {
                right: 91.66666666666666%;
            }

            .col-md-pull-10 {
                right: 83.33333333333334%;
            }

            .col-md-pull-9 {
                right: 75%;
            }

            .col-md-pull-8 {
                right: 66.66666666666666%;
            }

            .col-md-pull-7 {
                right: 58.333333333333336%;
            }

            .col-md-pull-6 {
                right: 50%;
            }

            .col-md-pull-5 {
                right: 41.66666666666667%;
            }

            .col-md-pull-4 {
                right: 33.33333333333333%;
            }

            .col-md-pull-3 {
                right: 25%;
            }

            .col-md-pull-2 {
                right: 16.666666666666664%;
            }

            .col-md-pull-1 {
                right: 8.333333333333332%;
            }

            .col-md-pull-0 {
                right: 0;
            }

            .col-md-push-12 {
                left: 100%;
            }

            .col-md-push-11 {
                left: 91.66666666666666%;
            }

            .col-md-push-10 {
                left: 83.33333333333334%;
            }

            .col-md-push-9 {
                left: 75%;
            }

            .col-md-push-8 {
                left: 66.66666666666666%;
            }

            .col-md-push-7 {
                left: 58.333333333333336%;
            }

            .col-md-push-6 {
                left: 50%;
            }

            .col-md-push-5 {
                left: 41.66666666666667%;
            }

            .col-md-push-4 {
                left: 33.33333333333333%;
            }

            .col-md-push-3 {
                left: 25%;
            }

            .col-md-push-2 {
                left: 16.666666666666664%;
            }

            .col-md-push-1 {
                left: 8.333333333333332%;
            }

            .col-md-push-0 {
                left: 0;
            }

            .col-md-offset-12 {
                margin-left: 100%;
            }

            .col-md-offset-11 {
                margin-left: 91.66666666666666%;
            }

            .col-md-offset-10 {
                margin-left: 83.33333333333334%;
            }

            .col-md-offset-9 {
                margin-left: 75%;
            }

            .col-md-offset-8 {
                margin-left: 66.66666666666666%;
            }

            .col-md-offset-7 {
                margin-left: 58.333333333333336%;
            }

            .col-md-offset-6 {
                margin-left: 50%;
            }

            .col-md-offset-5 {
                margin-left: 41.66666666666667%;
            }

            .col-md-offset-4 {
                margin-left: 33.33333333333333%;
            }

            .col-md-offset-3 {
                margin-left: 25%;
            }

            .col-md-offset-2 {
                margin-left: 16.666666666666664%;
            }

            .col-md-offset-1 {
                margin-left: 8.333333333333332%;
            }

            .col-md-offset-0 {
                margin-left: 0;
            }
        }
    </style>
</head>
<body style="margin: 0px">
<table align="center" width="100%" cellpadding="0" cellspacing="0" border="0" data-mobile="true" dir="ltr"
       data-width="600" style="font-size: 14px; background-color:#f3f3f3; font-family:Helvetica,Arial,sans-serif">
    <tbody>
    <tr>
        <td align="center" valign="top" style="margin:0;padding:42px 0;">
            <table align="center" border="0" cellspacing="0" cellpadding="0" width="600" bgcolor="white"
                   style="background-position: center top; background-repeat: no-repeat; background-size: cover; width: 660px;"
                   class="wrapper">
                <tbody>
                <tr>
                    <td align="center" valign="top" style="margin:0;padding:0;">
                        <table border="0" cellpadding="0" cellspacing="0" align="center" data-editable="image"
                               data-mobile-width="0" width="140">
                            <tbody>
                            <tr>
                                <td valign="top" align="center"
                                    style="display: inline-block; padding: 20px 0px 10px; margin: 0px;" class="tdBlock">
                                    <a href="https:www.pickyshop.com" target="_blank">
                                        <img src="https://cdn.iwes.it/assets/logoIwes.png" alt="" height="80"
                                             border="0"
                                             style="border-width: 0px; border-style: none; border-color: transparent; font-size: 12px; display: block;"/>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center" style="padding:10px 10px 0px 10px; margin:0;">
                        <table align="center" width="620px" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"
                               data-editable="text">
                            <tbody>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 30px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                        Inviamo di seguito Estratto conto della distinta n. <?php echo $paymentBill->id ?> per € <?php echo $total ?>.
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                       Vi preghiamo cortesemente voler provvedere al pagamento indicando il numero di distinta.
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 20px 10px 0; margin: 0px; line-height: 1.5; font-size: 18px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:800;color:#3A3A3A; line-height:1.2;">
                                    DATI BANCARI
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                    Beneficiary: <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2; font-weight: 600">International Web Ecommerce Sevices snc</span>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                        Iban: <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2; font-weight: 600">IT 48L 05216 1340 0000 00000 2334</span>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                    Bank: CREDITO VALTELLINESE
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 10px 10px 10px; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                    BIC/SWIFT: BPCVT2S
                                    </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center" style="padding:10px 10px 0px 10px; margin:0;">
                        <table align="center" width="620px" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"
                               data-editable="text">
                            <thead>
                            <tr>
                                <th valign="top" align="left" class="lh-3"
                                    style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:800;color:#3A3A3A; line-height:1.2;">
                                Numero Fattura
                                </span>
                                </th>
                                <th valign="top" align="left" class="lh-3"
                                    style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:800;color:#3A3A3A; line-height:1.2;">
                                Data Fattura
                                        </span>
                                </th>
                                <th valign="top" align="left" class="lh-3"
                                    style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:800;color:#3A3A3A; line-height:1.2;">
                                Importo
                                        </span>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($payment as $invoice):
                                if ($invoice->note == "ANNULLATA") {continue;}
                                ?>
                                <tr>
                                    <td valign="top" align="left" class="lh-3"
                                        style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                        <?php echo $invoice->number; ?>
                                    </span>
                                    </td>
                                    <td valign="top" align="left" class="lh-3"
                                        style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                        <?php echo \bamboo\utils\time\STimeToolbox::EurFormattedDate($invoice->date); ?>
                                    </span>
                                    </td>
                                    <td valign="top" align="left" class="lh-3"
                                        style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                        <?php
                                        if($invoice->getSignedValueWithVat() < 0) {
                                            echo abs($invoice->getSignedValueWithVat());
                                        } else {
                                            echo $invoice->getSignedValueWithVat() *-1;
                                        }
                                        ?>
                                    </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 10px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:bold;color:#3A3A3A; line-height:1.2;">
                                        <?php echo $total; ?>
                                    </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="center" style="padding:10px 10px 0px 10px; margin:0;">
                        <table align="center" width="620px" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0"
                               data-editable="text">
                            <tbody>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 20px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                        In attesa di ricevere contabile di pagamento al seguente indirizzo mail: <a style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:300;color:#909090; line-height:1.2" href="mailto:billing@iwes.it">billing@iwes.it</a>.
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 20px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                        Cordialmente salutiamo.
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 20px 10px 0; margin: 0px; line-height: 1.5; font-size: 18px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:800;color:#3A3A3A; line-height:1.2;">
                                     I.W.E.S.
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 0px 10px 0; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:300;color:#878787; line-height:1.2;">
                                        International Web E-commerce Services
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="left" class="lh-3"
                                    style="padding: 2px 10px 20px; margin: 0px; line-height: 1.5; font-size: 16px; font-family: Times New Roman, Times, serif;">
                                    <span style="font-family: 'Poppins', sans-serif; font-size:15px;font-weight:300;color:#3A3A3A; line-height:1.2;">
                                     Financial Department
                                    </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>