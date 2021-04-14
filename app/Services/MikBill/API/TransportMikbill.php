<?php


namespace App\Services\MikBill\API;


use GuzzleHttp\Client;

class TransportMikbill
{
    private $_host = '';
    private $_token;

    function __construct($url, $login, $pass)
    {
        if ($url) {
            $this->_host = $url;
        }

        $this->client = new Client([
            'base_uri' => $this->_host,
            'cookies'  => true,
            'verify'   => false
        ]);

        if (!$this->checkLoggedIn()) {
            $this->authBilling($login, $pass);
        }
    }

    public function checkLoggedIn()
    {
        $res = $this->client->request('POST', '/json/users/getcountfl');

        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);
            return $res['success'];
        }
        return false;
    }

    public function authBilling($login, $pass)
    {
        $res = $this->client->request('POST', '/extjs/index/auth', [
            'form_params' => [
                'login'    => $login,
                'password' => md5($pass),
            ]
        ]);

        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);

            if (isset($res['jwt'])) {
                $this->_token = $res['jwt'];
            }
        }
    }

    public function getHousesFullList()
    {
        $res = $this->client->request('POST', 'json/users/housesfulllist');

        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);

            if (isset($res['data']) and is_array($res['data'])) {
                return $res['data'];
            }
        }

        return false;
    }

    public function getCustomer($user_id, $debug = false)
    {
        $param = [
            'uid' => $user_id,
        ];

        if ($debug) {
            $param['debug'] = true;
        }

        $res = $this->client->request('POST', 'json/users/getuserdatambp', [
            'form_params' => $param
        ]);

        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);

            if ($res['success'] == true) {
                return $res['data'];
            }
        }
        return false;
    }

    public function getPersonalServiceUser($user_id)
    {
        $res = $this->client->request('POST', 'json/users/getpersonalservicesuser', [
            'form_params' => [
                'uid' => $user_id
            ]
        ]);

        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);

            if ($res['success'] == true) {
                return $res['data'];
            }
        }
        return false;
    }

    public function setPersonalServiceUser($user_id, $serviceid)
    {
        $res = $this->client->request('POST', 'json/users/setpersonalserviceuser', [
            'form_params' => [
                'uid'       => $user_id,
                'serviceid' => $serviceid
            ]
        ]);

        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);

            if ($res['success'] == true) {
                return $res['data'];
            }
        }
        return false;
    }

    public function updateUser($uid, $data)
    {
        $data['useruid'] = $uid;

        $res = $this->client->request('POST', 'json/users/updateuserflex', [
            'form_params' => $data
        ]);

        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);

            return $res;
        }

        return false;
    }

    public function resetCredit($uid)
    {
        $data['uid'] = $uid;

        $res = $this->client->request('POST', 'json/users/creditnullfl', [
            'form_params' => $data
        ]);

        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);

            return $res;
        }
    }

    public function getFullUsersUid()
    {
        $res = $this->client->request('POST', '/json/users/searchflex', [
            'form_params' => [
                "search_normal_state"    => 0,
                "search_otkluchen_state" => 0,
                "search_frozen_state"    => 0,
                "search_deleted_state"   => 0,
                "search_all_states"      => 1,
                "op"                     => 1,
                "search_display_all"     => 0,
                "search_internet"        => 0,
                "usersgroupid"           => 0,
                "ext_legal_person"       => 0,
                "ext_regular_person"     => 0
            ]
        ]);

        $users = array();

        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);

            if (isset($res['data']) and is_array($res['data'])) {
                foreach ($res['data'] as $user) {
                    if ($user['useruid'] > 90000 and $user['useruid'] < 1000001) {
                        $users[] = $user['useruid'];
                    }
                }
            }
        }

        return $users;
    }

    public function searchDepositCredit()
    {
        $res = $this->client->request('POST', '/json/users/searchflex', [
            'form_params' => [
                "search_normal_state"    => 0,
                "search_otkluchen_state" => 0,
                "search_frozen_state"    => 0,
                "search_deleted_state"   => 0,
                "search_all_states"      => 1,
                "op"                     => 1,
                "search_display_all"     => 0,
                "search_internet"        => 0,
                "usersgroupid"           => 0,
                "ext_legal_person"       => 0,
                "ext_regular_person"     => 0
            ]
        ]);

        $users = array();

        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);

            if (isset($res['data']) and is_array($res['data'])) {
                foreach ($res['data'] as $user) {
                    if ($user['deposit'] > 0 and $user['credit'] > 0) {
                        $users[] = $user['useruid'];
                    }
                }
            }
        }

        return $users;
    }


    public function searchDisable($packets)
    {
        $res = $this->client->request('POST', '/json/users/searchflex', [
            'form_params' => [
                "search_normal_state"    => 0,
                "search_otkluchen_state" => 1,
                "search_frozen_state"    => 0,
                "search_deleted_state"   => 0,
                "search_all_states"      => 0,
                "op"                     => 1,
                "search_display_all"     => 0,
                "search_internet"        => 0,
                "usersgroupid"           => 0,
                "ext_legal_person"       => 0,
                "ext_regular_person"     => 0
            ]
        ]);

        $users = array();

        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);

            if (isset($res['data']) and is_array($res['data'])) {
                foreach ($res['data'] as $user) {
                    if (in_array($user['gid'], $packets)) {
                        $users[] = $user['useruid'];
                    }
                }
            }
        }

        return $users;
    }


    public function searchByField($field, $key, $value)
    {
        $res = $this->client->request('POST', '/json/users/searchflex', [
            'form_params' => [
                $field                   => $value,
                "search_normal_state"    => 0,
                "search_otkluchen_state" => 0,
                "search_frozen_state"    => 0,
                "search_deleted_state"   => 0,
                "search_all_states"      => 1,
                "op"                     => 1,
                "search_display_all"     => 0,
                "search_internet"        => 0,
                "ext_legal_person"       => 0,
                "ext_regular_person"     => 0
            ]
        ]);

        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);

            if (isset($res['data']) and is_array($res['data'])) {
                $users = [];
                foreach ($res['data'] as $user) {
                    if ($user[$key] == $value) {
                        $users[] = $user;
                    }
                }
                return  $users;
            }
        }

        return [];
    }

    /**
     * Search user
     *
     * @param array $params
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search($params = [])
    {
        $options = [
            "search_normal_state"    => 0,
            "search_otkluchen_state" => 0,
            "search_frozen_state"    => 0,
            "search_deleted_state"   => 0,
            "search_all_states"      => 1,
            "op"                     => 1,
            "search_display_all"     => 0,
            "search_internet"        => 0,
            "ext_legal_person"       => 0,
            "ext_regular_person"     => 0
        ];

        foreach ($params as $key => $value) {
            $options[$key] = $value;
        }

        $res = $this->client->request('POST', '/json/users/searchflex', [
            'form_params' => $options
        ]);


        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);

            if (isset($res['data']) and is_array($res['data'])) {
                return $res['data'];
            }
        }

        return [];
    }

    /**
     * Get devTypesFullList
     *
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getDevTypesFullList()
    {
        $res = $this->client->request('POST', '/json/users/getdevtypesfulllist');

        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);

            if (isset($res['data']) and is_array($res['data'])) {
                return $res['data'];
            }
        }

        return [];
    }

    public function getUserCanvasStat($uid)
    {
        $res = $this->client->request('POST', '/json/users/getusercanvasstat', [
            'form_params' => [
                'uid' => $uid
            ]
        ]);

        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);

            if (isset($res['data'][0]) and is_array($res['data'][0])) {
                return $res['data'][0];
            }
        }

        return [];
    }

    public function getUserDevicesByUid($uid)
    {
        $res = $this->client->request('POST', '/json/users/getuserdevicesbyuid', [
            'form_params' => [
                'useruid' => $uid
            ]
        ]);

        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);

            if (isset($res['data']) and is_array($res['data'])) {
                return $res['data'];
            }
        }

        return [];
    }

    public function payment($uid, $amount, $desc)
    {
        $res = $this->client->request('POST', '/json/users/paymentflex', [
            'form_params' => [
                'uid'            => $uid,
                'deposit'        => $amount,
                'prim2'          => $desc,
                'beznal_payment' => $desc,
            ]
        ]);

        if ($res->getStatusCode() == 200) { // 200 OK
            $res = json_decode($res->getBody()->getContents(), true);

            return $res;
        }

        return [];
    }


}
