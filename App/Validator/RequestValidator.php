<?php

namespace Validator;

use InvalidArgumentException;
use Service\handleUser;
use Service\handleSubscriber;
use Infra\GenericConsts;
use Infra\Json;

class RequestValidator
{
    private array $request;
    private array $requestData;

    const DELETE = 'DELETE';
    const POST = 'POST';
    const GET = 'GET';

    const SUBSCRIBER = 'SUBSCRIBER';
    const USER = 'USER';


    /**
     * RequestValidator constructor.
     * @param array $request
     */
    public function __construct($request = [])
    {
        $this->request = $request;
    }

    /**
     * @return array|mixed|string|null
     */
    public function handleRequest()
    {
        $return = utf8_encode(GenericConsts::MSG_ERROR_ROUTER_TYPE);
        if(in_array($this->request['method'], GenericConsts::REQUEST_TYPES, true)) {
            $return = $this->directRequest();
        }
        return $return;
    }

    /**
     * Metodo para direcionar o tipo de Request
     * @return array|mixed|string|null
     */
    private function directRequest()
    {
        if(empty($this->request['route']) && $this->request['resource'] === NULL && $this->request['method'] === 'GET') {
            include dirname(__DIR__).'/resources/views/index.php';
            exit;
        } else if($this->request['route'] === 'SUBSCRIBER' && in_array($this->request['resource'], ['index', null]) && $this->request['method'] === 'GET') {           
            include dirname(__DIR__).'/resources/views/subscriber/list.php';
            exit;
        } else {
            if ($this->request['route'] === 'SUBSCRIBER' && $this->request['resource'] === 'import' && $this->request['method'] === 'POST'){
                $this->requestData = array_merge($_POST, $_FILES);
                $method = $this->request['method'];
            } else if ($this->request['route'] === 'SUBSCRIBER' && $this->request['resource'] === 'edit' && $this->request['method'] === 'POST'){
                $this->requestData = $_POST;
                $method = $this->request['method'];
            } else if ($this->request['route'] === 'USER' && $this->request['resource'] === 'login' && $this->request['method'] === 'POST'){
                $this->requestData = $_POST;
                $method = $this->request['method'];
            } else {
                if ($this->request['method'] !== self::GET && $this->request['method'] !== self::DELETE && $this->request['resource'] !== 'import') {
                    $this->requestData = Json::handleBodyRequest();
                    $method = $this->request['method'];
                } else {
                    $method = $this->request['method'];
                }
            }
            
            
            
            return $this->$method();
        }
    }

    /**
     * Metodo para tratar os GETS
     * @return array|mixed|string
     */
    private function get()
    {
        $return = utf8_encode(GenericConsts::MSG_ERROR_ROUTER_TYPE);
        if (in_array($this->request['route'], GenericConsts::GET_TYPE, true)) {
            switch ($this->request['route']) {
                case self::USER:
                    $handleUser = new handleUser($this->request);
                    $return = $handleUser->validateGet();
                    break;
                case self::SUBSCRIBER:
                    $handleSubscriber = new handleSubscriber($this->request);
                    $return = $handleSubscriber->validateGet();
                    break;
                default:
                    throw new InvalidArgumentException(GenericConsts::MSG_ERRO_RECURSO_INEXISTENTE);
            }
        }
        return $return;
    }

    /**
     * Metodo para tratar os POSTS
     * @return array|null|string
     */
    private function post()
    {
        $return = null;
        if (in_array($this->request['route'], GenericConsts::POST_TYPE, true)) {
            switch ($this->request['route']) {
                case self::USER:
                    $handleUser = new handleUser($this->request);
                    $handleUser->setBodyDataRequests($this->requestData);
                    $return = $handleUser->validatePost();
                    break;
                case self::SUBSCRIBER:
                    $handleSubscriber = new handleSubscriber($this->request);
                    $handleSubscriber->setBodyDataRequests($this->requestData);
                    $return = $handleSubscriber->validatePost();
                    break;
                default:
                    throw new InvalidArgumentException(GenericConsts::MSG_ERROR_ROUTER_TYPE);
            }
            return $return;
        }
        throw new InvalidArgumentException(GenericConsts::MSG_ERROR_ROUTER_TYPE);
    }

    /**
     * Metodo para tratar os PUTS
     * @return array|null|string
     */
    private function put()
    {
        $return = null;
        if (in_array($this->request['route'], GenericConsts::PUT_TYPE, true)) {
            switch ($this->request['route']) {
                case self::USER:
                    $handleUser = new handleUser($this->request);
                    $handleUser->setBodyDataRequests($this->requestData);
                    $return = $handleUser->validatePut();
                    break;
                case self::PRODUCT:
                    $handleProduct = new handleProduct($this->request);
                    $handleProduct->setBodyDataRequests($this->requestData);
                    $return = $handleProduct->validatePut();
                    break;
                case self::CUSTOMER:
                    $handleCustomer = new handleCustomer($this->request);
                    $handleCustomer->setBodyDataRequests($this->requestData);
                    $return = $handleCustomer->validatePut();
                    break;
                case self::PROVIDER:
                    $handleProvider = new handleProvider($this->request);
                    $handleProvider->setBodyDataRequests($this->requestData);
                    $return = $handleProvider->validatePut();
                    break;
                case self::ATTENDANCE:
                    $handleAttendance = new handleAttendance($this->request);
                    $handleAttendance->setBodyDataRequests($this->requestData);
                    $return = $handleAttendance->validatePut();
                    break;
                case self::GAME:
                    $handleGame = new handleGame($this->request);
                    $handleGame->setBodyDataRequests($this->requestData);
                    $return = $handleGame->validatePut();
                    break;
                case self::PRODUCTATTENDANCE:
                    $handleProductAttendance = new handleProductAttendance($this->request);
                    $handleProductAttendance->setBodyDataRequests($this->requestData);
                    $return = $handleProductAttendance->validatePut();
                    break;
                case self::REF:
                    $handleRef = new handleRef($this->request);
                    $handleRef->setBodyDataRequests($this->requestData);
                    $return = $handleRef->validatePut();
                    break;
                case self::PURCHASE:
                    $handlePurchase = new handlePurchase($this->request);
                    $handlePurchase->setBodyDataRequests($this->requestData);
                    $return = $handlePurchase->validatePut();
                    break;
                default:
                    throw new InvalidArgumentException(GenericConsts::MSG_ERROR_ROUTER_TYPE);
            }
            return $return;
        }
        throw new InvalidArgumentException(GenericConsts::MSG_ERROR_ROUTER_TYPE);
    }

     /**
     * Metodo para tratar os DELETES
     * @return mixed|string
     */
    private function delete()
    {
        $return = utf8_encode(GenericConsts::MSG_ERROR_ROUTER_TYPE);
        if (in_array($this->request['route'], GenericConsts::DELETE_TYPE, true)) {
            switch ($this->request['route']) {
                case self::USER:
                    $handleUser = new handleUser($this->request);
                    $return = $handleUser->validateDelete();
                    break;
                case self::PRODUCT:
                    $handleProduct = new handleProduct($this->request);
                    $return = $handleProduct->validateDelete();
                    break;
                case self::CUSTOMER:
                    $handleCustomer = new handleCustomer($this->request);
                    $return = $handleCustomer->validateDelete();
                    break;
                case self::PROVIDER:
                    $handleProvider = new handleProvider($this->request);
                    $return = $handleProvider->validateDelete();
                    break;
                case self::ATTENDANCE:
                    $handleAttendance = new handleAttendance($this->request);
                    $return = $handleAttendance->validateDelete();
                    break;
                case self::GAME:
                    $handleGame = new handleGame($this->request);
                    $handleGame->setBodyDataRequests($this->requestData);
                    $return = $handleGame->validateDelete();
                    break;
                case self::PRODUCTATTENDANCE:
                    $handleProductAttendance = new handleProductAttendance($this->request);
                    $handleProductAttendance->setBodyDataRequests($this->requestData);
                    $return = $handleProductAttendance->validateDelete();
                    break;
                case self::PURCHASE:
                    $handlePurchase = new handlePurchase($this->request);
                    $return = $handlePurchase->validateDelete();
                    break;
                default:
                    throw new InvalidArgumentException(GenericConsts::MSG_ERRO_RECURSO_INEXISTENTE);
            }
        }
        return $return;
    }

        /**
     * Metodo para tratar o POSTlogin
     * @return array|null|string
     */
    private function POSTlogin()
    {
        $return = null;
        if (in_array($this->request['route'], GenericConsts::POST_TYPE, true)) {
            switch ($this->request['route']) {
                case self::USER:
                    $handleUser = new handleUser($this->request);
                    $handleUser->setBodyDataRequests($this->requestData);
                    $return = $handleUser->validatelogin();
                    break;
                default:
                    throw new InvalidArgumentException(GenericConsts::MSG_ERROR_ROUTER_TYPE);
            }
            return $return;
        }
        throw new InvalidArgumentException(GenericConsts::MSG_ERROR_ROUTER_TYPE);
    }

    /**
     * Metodo para tratar o GETlogin
     * @return array|mixed|string
     */
    private function GETlogout()
    {
        $return = utf8_encode(GenericConsts::MSG_ERROR_ROUTER_TYPE);
        if (in_array($this->request['route'], GenericConsts::GET_TYPE, true)) {
            switch ($this->request['route']) {
                case self::USER:
                    $handleUser = new handleUser($this->request);
                    $return = $handleUser->validateLogout();
                    break;
                default:
                    throw new InvalidArgumentException(GenericConsts::MSG_ERRO_RECURSO_INEXISTENTE);
            }
        }
        return $return;
    }
}