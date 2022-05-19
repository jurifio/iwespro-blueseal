<!DOCTYPE html>
<html>
<head>
    <?php include "parts/head.php"?>
    <?php echo $app->getAssets(['ui','forms'], $page); ?>
    <title>99 Monkeys - Login</title>
</head>
<body class="fixed-header">
<div class="login-wrapper ">
    <div class="bg-pic">
        <img src="/assets/img/99monkeys.jpg" data-src="/assets/img/99monkeys.jpg" data-src-retina="/assets/img/99monkeys.jpg" alt="" class="lazy">
        <div class="bg-caption pull-bottom sm-pull-bottom text-white p-l-20 m-b-20">
            <h2 class="semi-bold text-white">
                99 Monkeys ti permette di gestire il tuo ecommerce in maniera facile e immediata</h2>
            <p class="small">
                Version 1.1.0 - Copyright © 2022 Iwes International Web Ecommerce Services.
            </p>
        </div>
    </div>
    <div class="login-container bg-white">
        <div class="p-l-50 m-l-20 p-r-50 m-r-20 p-t-50 m-t-30 sm-p-l-15 sm-p-r-15 sm-p-t-40">
            <img src="/assets/img/logo.png" alt="logo" data-src="/assets/img/logo.png" data-src-retina="/assets/img/logo_2x.png" width="150">
            <p class="p-t-35">Accedi al tuo account 99 Monkeys</p>
            <form id="form-login" class="p-t-15" role="form" method="POST" action="">
                <div class="form-group form-group-default">
                    <label>Utente</label>
                    <div class="controls">
                        <input type="text" name="username" placeholder="Nome Utente" class="form-control" required>
                    </div>
                </div>
                <div class="form-group form-group-default">
                    <label>Password</label>
                    <div class="controls">
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 no-padding">
                        <label for="remember">
                            <input type="checkbox" checked="checked" id="remember" name="remember" value="true">
                            Ricordati di me
                        </label>
                    </div>
                    <div class="col-md-6 text-right">
                    </div>
                </div>
                <button class="btn btn-primary btn-cons m-t-10" type="submit">Accedi <i class="fa fa-sign-in"></i></button>
            </form>
        </div>
    </div>
</div>
</body>
</html>