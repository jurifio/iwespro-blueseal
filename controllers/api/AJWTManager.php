<?php


namespace bamboo\controllers\api;

use bamboo\core\base\CConfig;
use bamboo\core\base\CCookie;
use bamboo\core\exceptions\BambooConfigException;


/**
 * Class AJWTManager
 * @package controllers\api
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/09/2018
 * @since 1.0
 */
abstract class AJWTManager
{

    protected $id = null;
    protected $pw = null;
    protected $conf = null;
    protected $auth = null;
    protected $ipConf = null;
    protected $clientIp = null;

    /**
     * AJWTManager constructor.
     * @throws BambooConfigException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\RedPandaCookieException
     */
    public function __construct()
    {
       //$this->checkIp();
        $this->conf = \Monkey::app()->cfg()->fetch("cookies", "jwt");

        \Monkey::app()->vendorLibraries->load('carbon');

        $id = !\Monkey::app()->router->request()->getRequestData('id') ? (isset($_GET['id']) ? $_GET['id'] : false) : \Monkey::app()->router->request()->getRequestData('id');
        $password = !\Monkey::app()->router->request()->getRequestData('password') ? (isset($_GET['password']) ? $_GET['password'] : false) : \Monkey::app()->router->request()->getRequestData('password');

        $this->id = $id;
        $this->pw = $password;

        $this->checkUser();
    }

    /**
     * @throws BambooConfigException
     */
    private function checkIp()
    {
        $this->readConfig();
        $this->clientIp = \Monkey::app()->router->request()->getClientIp();

        if (!in_array($this->clientIp, $this->ipConf->fetchAll('admitted'))) {
            $this->auth = 'Il tuo ip (' . $this->clientIp . ') non ha i permessi per richiedere i nostri dati';
        }

        return true;
    }

    /**
     * @return bool
     * @throws \bamboo\core\exceptions\RedPandaCookieException
     */
    private function checkUser()
    {

        $token = $this->getJWTToken();

        $tClass = new Token();

        if (is_null($token)) {
            $auth = $this->authAPI();
            if ($auth["res"]) {
                $newToken = $tClass::getToken($this->id, $this->pw, time() + 3600, 'iwes.pro');
                $this->setCookieToken($newToken);
            } else {
                $this->auth = $auth["mes"];
            }
        } else {
            try {
                Token::validate($token, $this->pw);
            } catch (\Throwable $e) {
                $this->auth = $e->getMessage();
            }

        }

        return true;
    }

    /**
     * @return null|string
     */
    private function getJWTToken()
    {
        $jwtCookie = new CCookie('jwt', \Monkey::app());

        return $jwtCookie->getCookieData();
    }

    /**
     * @return array
     */
    private function authAPI()
    {
        $authResponse = [];

        $userApi = \Monkey::app()->repoFactory->create('SiteApi')->findOneBy(['id' => $this->id]);

        if (is_null($userApi)) return $authResponse[] = [
            "res" => false,
            "mes" => "User not allowed"
        ];

        if (password_verify($this->pw, $userApi->password)) {
            return $authResponse[] = [
                "res" => true,
                "mes" => "",
            ];
        } else {
            return $authResponse[] = [
                "res" => false,
                "mes" => "Credential not correct"
            ];
        }


    }

    /**
     * @param $token
     * @return bool
     * @throws \bamboo\core\exceptions\RedPandaCookieException
     */
    private function setCookieToken($token)
    {
        $jwtCookie = new CCookie('jwt', \Monkey::app());
        $jwtCookie->prepare($this->conf["domain"], time() + 3600, $this->conf['path'], $this->conf['secure'], $this->conf['httpOnly']);
        $jwtCookie->set('jwt', $token);

        return true;
    }

    /**
     * @throws BambooConfigException
     */
    public function readConfig()
    {
        $filePath = __DIR__ . '/conf/admitted.json';

        if (!file_exists($filePath)) throw new BambooConfigException('Configuration not found for Importer: ' . $filePath);

        $this->ipConf = new CConfig($filePath);
        $this->ipConf->load();
    }
}