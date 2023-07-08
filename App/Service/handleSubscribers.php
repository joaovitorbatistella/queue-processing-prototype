<?php

namespace Service;

use InvalidArgumentException;
use Model\Subscriber;
use Model\AuthorizationToken;
use Infra\GenericConsts;

class handleSubscribers
{
    public const TABLE = 'subscribers';
    public const GET_RESOURCES = ['list', 'filterByEMail'];
    public const POST_RESOURCES = ['store', 'import'];
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
     * handleSubscribers constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
        $this->Subscriber = new Subscriber();
        // $this->AuthorizationToken = new AuthorizationToken();
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
    private function store()
    {
        [$name, $username, $password] = [$this->bodyDataRequests['name'], $this->bodyDataRequests['username'], $this->bodyDataRequests['password']];

        if ($name && $username && $password) {
            if ($this->Subscriber->getRegisterByLogin($username) > 0) {
                throw new InvalidArgumentException(GenericConsts::MSG_ERRO_LOGIN_EXISTENTE);
            }

            if ($this->Subscriber->insertUser($name, $username, $password) > 0) {
                $insertedId = $this->Subscriber->getConn()->getDb()->lastInsertId();
                $this->Subscriber->getConn()->getDb()->commit();
                return ['insertedId' => $insertedId];
            }

            $this->Subscriber->getConn()->getDb()->rollBack();

            throw new InvalidArgumentException(GenericConsts::MSG_ERRO_GENERICO);
        }
        throw new InvalidArgumentException(GenericConsts::MSG_ERROR_EMPTY_FIELDS);
    }

    /**
     * @return string
     */
    private function delete()
    {
        return $this->Subscriber->getConn()->delete(self::TABLE, $this->data['id']);
    }

    /**
     * @return string
     */
    private function update()
    {
        if ($this->Subscriber->updateUser($this->data['id'], $this->bodyDataRequests) > 0) {
            $this->Subscriber->getConn()->getDb()->commit();
            return GenericConsts::MSG_ATUALIZADO_SUCESSO;
        }
        $this->Subscriber->getConn()->getDb()->rollBack();
        throw new InvalidArgumentException(GenericConsts::MSG_ERRO_NAO_AFETADO);
    }

}
