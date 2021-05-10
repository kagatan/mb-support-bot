<?php


namespace App\Services\MikBill\API;


class API
{
//    private $host = 'https://admin.mikbill.pro';
    private $host = 'https://admin2x.loc';
    private $login = 'admin';
    private $pass = 'admin';


    public function __construct()
    {
        $this->host = config('services.mikbill.host');
        $this->login = config('services.mikbill.login');
        $this->pass = config('services.mikbill.pass');
    }

    public function searchUsers($value, $type= 'login')
    {
        $transportClass = new TransportMikbill($this->host, $this->login, $this->pass);

        if ($transportClass->checkLoggedIn()) {

            switch ($type){
                case 'uid':
                    return $transportClass->searchByField('uid', 'uid', $value);
                    break;
                case 'login':
                    return $transportClass->searchByField('user', 'user', $value);
                    break;
                case 'numdogovor':
                    return $transportClass->searchByField('uid', 'numdogovor', $value);
                    break;
            }
        }

        return false;
    }

    public function getUser($uid)
    {
        $transportClass = new TransportMikbill($this->host, $this->login, $this->pass);

        if ($transportClass->checkLoggedIn()) {
            return $transportClass->getCustomer($uid);
        }

        return false;
    }

    public function getHistorySessions($uid)
    {
        $transportClass = new TransportMikbill($this->host, $this->login, $this->pass);

        if ($transportClass->checkLoggedIn()) {
            $all = $transportClass->getUserCanvasStat($uid);

            return $all['stattraf'];
        }

        return false;
    }


    public function getHistoryPayments($uid)
    {
        $transportClass = new TransportMikbill($this->host, $this->login, $this->pass);

        if ($transportClass->checkLoggedIn()) {
            $all = $transportClass->getUserCanvasStat($uid);

            return $all['statpay'];
        }

        return false;
    }

}
