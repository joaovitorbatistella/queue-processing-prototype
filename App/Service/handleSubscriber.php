<?php

namespace Service;

use InvalidArgumentException;
use Model\Subscriber;
use Model\AuthorizationToken;
use Infra\GenericConsts;
use Session\Login;

class handleSubscriber
{
    public const TABLE = 'subscribers';
    public const GET_RESOURCES = ['list', 'filterByEMail', 'delete', 'edit'];
    public const POST_RESOURCES = ['store', 'import', 'edit'];
    public const DELETE_RESOURCES = ['delete'];
    public const PUT_RESOURCES = ['update'];

    private array $data;
    private array $bodyDataRequests;
    /**
     * @var object|Subscriber
     */
    private object $Subscriber;
    // private object $AuthorizationToken;

    /**
     * handleSubscriber constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
        $this->Subscriber = new Subscriber();

    }
    

    /**
     * @return mixed
     */
    public function validateGet()
    {
        $return = null;
        $resource = $this->data['resource'];
        if (in_array($resource, self::GET_RESOURCES, true)) {
            if( $this->data['params'] != null){
                $return = $this->data['id'] > 0 ? $this->getOneByKey() : $this->filterByName( $this->data['params']);
            } else if ($resource === 'delete') {
                $return = $this->$resource();
            } else if ($resource === 'edit') {
                $return = $this->$resource();
            } else {
                $return = $this->data['id'] > 0 ? $this->getOneByKey() : $this->$resource();
            }
        } else {
            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        if ($return === null) {
            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_GENERICO);
        }

        return $return;
    }

    /**
     * @return mixed
     */
    public function validateDelete()
    {
        $return = null;
        $resource = $this->data['resource'];
        if (in_array($resource, self::DELETE_RESOURCES, true)) {
            if ($this->data['id'] > 0) {
                $return = $this->$resource();
            } else {
                throw new InvalidArgumentException(GenericConsts::MSG_ERRO_ID_OBRIGATORIO);
            }
        } else {
            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        if ($return === null) {
            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_GENERICO);
        }

        return $return;
    }

    /**
     * @return mixed
     */
    public function validatePost()
    {
        $return = null;
        $resource = $this->data['resource'];
        if (in_array($resource, self::POST_RESOURCES, true)) {
            $return = $this->$resource();
        } else {
            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        if ($return === null) {
            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_GENERICO);
        }

        return $return;
    }

    /**
     * @return mixed
     */
    public function validatePut()
    {
        $return = null;
        $resource = $this->data['resource'];
        if (in_array($resource, self::PUT_RESOURCES, true)) {
            if ($this->data['id'] > 0) {
                $return = $this->$resource();
            } else {
                throw new InvalidArgumentException(GenericConsts::MSG_ERRO_ID_OBRIGATORIO);
            }
        } else {
            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_RECURSO_INEXISTENTE);
        }

        if ($return === null) {
            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_GENERICO);
        }

        return $return;
    }

    /**
     * @param array $bodyDataRequests
     */
    public function setBodyDataRequests($bodyDataRequests)
    {
        $this->bodyDataRequests = $bodyDataRequests;

    }

    /**
     * @return mixed
     */
    private function login()
    {
        [$username, $password] = [$this->bodyDataRequests['username'], $this->bodyDataRequests['password']];

        if ($username && $password) {
            if ($this->Subscriber->handleLogin($username, $password) > 0) {
                $token = $this->AuthorizationToken->generateToken($username, $password);
                return ['token' => $token];
            }

            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_GENERICO);
        }
        throw new InvalidArgumentException(GenericConsts::MSG_ERRO_LOGIN_SENHA_OBRIGATORIO);
    }

      /**
     * @return mixed
     */
    private function logout()
    {
        $token = getallheaders()['Authorization'];
        $token = $this->AuthorizationToken->getOneByToken($token);
        $this->AuthorizationToken->destroyById($token['id']);
        return ['destroyedToken' => $token];
    }

    /**
     * @return mixed
     */
    private function list()
    {
        return $this->Subscriber->getAllUsers(self::TABLE);
    }

    /**
     * @param $username
     * @return mixed
     */
    private function filterByName($data)
    {
        $var = explode('&', $data);
        $params=[];
        for($i=0; $i < count($var); $i++) {
            $params[$i] = $var[$i];
        }
        $param = explode('=', $params[0]);
        return $this->Subscriber->getUserByUsername($param[1]);
    }

    /**
     * @return mixed
     */
    private function getOneByKey()
    {
        return $this->Subscriber->getConn()->getOneByKey(self::TABLE, $this->data['id']);
    }

    /**
     * @return array
     */
    private function import()
    {
        $assocHeadersArray=[];
        $assocDataArray=[];
        $newData=[];
        $inputFile = $this->bodyDataRequests['inputFile'];

        if ($inputFile) {
            $handle = fopen($inputFile['tmp_name'], "r");
            $row = 1;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if($row === 1) {
                    foreach ($data as $value) {
                        $assocHeadersArray[] = $value;
                    }
                    $row++;
                    continue;
                }
                foreach ($data as $key => $value) {
                    if(in_array($assocHeadersArray[$key], ['email','name','phone'])) {
                        $assocDataArray[$assocHeadersArray[$key]] = $value;
                    }
                }
                $row++;
                $newData[] = $assocDataArray;
            }
            $assocDataArray=null;
            fclose($handle);

            foreach ($newData as $key => $value) {
                $this->Subscriber->enQueue(base64_encode(json_encode($value)));
            }

            // exit;
            // if ($this->Subscriber->insertUser($name, $username, $password) > 0) {
            //     $insertedId = $this->Subscriber->getConn()->getDb()->lastInsertId();
            //     $this->Subscriber->getConn()->getDb()->commit();
            //     return ['insertedId' => $insertedId];
            // }

            // $this->Subscriber->getConn()->getDb()->rollBack();
            header('location: '.APP_URL.'/subscriber');

            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_GENERICO);
        }
        throw new InvalidArgumentException(GenericConsts::MSG_ERROR_EMPTY_FIELDS);
    }

    /**
     * @return string
     */
    private function delete()
    {
        if(!Login::isLogged()){
            header('location: '.APP_URL.'/user/login');
            exit;
        }
        $this->Subscriber->getConn()->delete(self::TABLE, $this->data['id']);

        header('location: '.APP_URL.'/subscriber');
    }

        /**
     * @return string
     */
    private function edit()
    {
        if(!Login::isLogged()){
            header('location: '.APP_URL.'/user/login');
            exit;
        }
        if($this->data['method'] === 'GET') {
            $editData = $this->getOneByKey();
            include dirname(__DIR__).'/resources/views/subscriber/edit.php';
            exit;
        } else {
            if ($this->Subscriber->update($this->data['id'], [
                "email" => $this->bodyDataRequests['inputEmail'],
                "name"  => $this->bodyDataRequests['inputName'],
                "phone" => $this->bodyDataRequests['inputPhone']
            ]) > 0) {
                $this->Subscriber->getConn()->getDb()->commit();
                header('location: '.APP_URL.'/subscriber');
                exit;
            } else {
                $this->Subscriber->getConn()->getDb()->rollBack();
                header('location: '.APP_URL.'/subscriber/edit/'.$this->data['id']);
                exit;
            }
        }
    }

}
