<?php

namespace Service;

use InvalidArgumentException;
use Session\Login;
use Model\User;
use Infra\GenericConsts;

class handleUser
{
    public const TABLE = 'users';
    public const GET_RESOURCES = ['list', 'login', 'logout'];
    public const POST_RESOURCES = ['store', 'login'];
    public const LOGIN_RESOURCES = ['login'];
    public const LOGOUT_RESOURCES = ['logout'];
    public const DELETE_RESOURCES = ['delete'];
    public const PUT_RESOURCES = ['update'];

    private array $data;
    private array $bodyDataRequests;
    /**
     * @var object|User
     */
    private object $User;

    /**
     * handleUser constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
        $this->User = new User();
    }

    /**
     * @return mixed
     */
    public function validateLogin()
    {
        $return = null;
        $resource = $this->data['resource'];
        if (in_array($resource, self::LOGIN_RESOURCES, true)) {
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
    public function validateLogout()
    {
        $return = null;
        $resource = $this->data['resource'];
        if (in_array($resource, self::LOGOUT_RESOURCES, true)) {
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
    public function validateGet()
    {
        $return = null;
        $resource = $this->data['resource'];
        if (in_array($resource, self::GET_RESOURCES, true)) {
            if( $this->data['params'] != null){
                $return = $this->data['id'] > 0 ? $this->getOneByKey() : $this->filterByName( $this->data['params']);
            } else {
                $return = $this->data['id'] > 0 ? $this->getOneByKey() : $this->$resource();
            }
        } else {
            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_RECURSO_INEXISTENTE);
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
        if($this->data['method'] === 'GET') {
            Login::requiredLogout();
            if(isset($_COOKIE['logged'])) {
                $data = json_decode($_COOKIE['logged']);
                Login::login((object) $data);
                header('location: '.APP_URL.'/');
            } else {
                include dirname(__DIR__).'/resources/views/user/login.php';
            }
        } else {
            [$email, $password] = [$this->bodyDataRequests['inputEmail'], $this->bodyDataRequests['inputPassword']];

            if ($email && $password) {
                if ($this->User->handleLogin($email, $password) > 0) {
                    header('location: '.APP_URL.'/');
                    exit;
                } else {
                    header('location: '.APP_URL.'/user/login');
                    exit;
                }
    
                throw new InvalidArgumentException(GenericConsts::MSG_ERRO_GENERICO);
            }
            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_LOGIN_SENHA_OBRIGATORIO);
        }
    }

      /**
     * @return mixed
     */
    private function logout()
    {
        Login::logout();
    }

    /**
     * @return mixed
     */
    private function list()
    {
        return $this->User->getAllUsers(self::TABLE);
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
        return $this->User->getUserByUsername($param[1]);
    }

    /**
     * @return mixed
     */
    private function getOneByKey()
    {
        return $this->User->getConn()->getOneByKey(self::TABLE, $this->data['id']);
    }

    /**
     * @return array
     */
    private function store()
    {
        [$name, $username, $password] = [$this->bodyDataRequests['name'], $this->bodyDataRequests['username'], $this->bodyDataRequests['password']];

        if ($name && $username && $password) {
            if ($this->User->getRegisterByLogin($username) > 0) {
                throw new InvalidArgumentException(GenericConsts::MSG_ERRO_LOGIN_EXISTENTE);
            }

            if ($this->User->insertUser($name, $username, $password) > 0) {
                $insertedId = $this->User->getConn()->getDb()->lastInsertId();
                $this->User->getConn()->getDb()->commit();
                return ['insertedId' => $insertedId];
            }

            $this->User->getConn()->getDb()->rollBack();

            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_GENERICO);
        }
        throw new InvalidArgumentException(GenericConsts::MSG_ERROR_EMPTY_FIELDS);
    }

    /**
     * @return string
     */
    private function delete()
    {
        return $this->User->getConn()->delete(self::TABLE, $this->data['id']);
    }

    /**
     * @return string
     */
    private function update()
    {
        if ($this->User->updateUser($this->data['id'], $this->bodyDataRequests) > 0) {
            $this->User->getConn()->getDb()->commit();
            return GenericConsts::MSG_ATUALIZADO_SUCESSO;
        }
        $this->User->getConn()->getDb()->rollBack();
        throw new InvalidArgumentException(GenericConsts::MSG_ERRO_NAO_AFETADO);
    }

}
