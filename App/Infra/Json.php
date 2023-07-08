<?php

namespace Infra;

use InvalidArgumentException;
use JsonException;

class Json
{
    /**
     * @param $return
     * @throws JsonException
     */
    public function handleReturnArray($return)
    {
        $data = [];
        $data[GenericConsts::TYPE] = GenericConsts::ERROR_TYPE;

        if ((is_array($return) && count($return) > 0) || strlen($return) > 10) {
            $data[GenericConsts::TYPE] = GenericConsts::SUCCESS_TYPE;
            $data[GenericConsts::RESPONSE] = $return;
        }

        $this->jsonReturner($data);
    }

    /**
     * @param $json
     * @throws JsonException
     */
    private function jsonReturner($json)
    {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE');
        echo json_encode($json, JSON_THROW_ON_ERROR, 1024);
        exit;
    }

    /**
     * @return array|mixed
     */
    public static function handleBodyRequest()
    {
        try {
            $postJson = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidArgumentException(GenericConsts::MSG_ERR0_JSON_VAZIO);
        }
        if (is_array($postJson) && count($postJson) > 0) {
            return $postJson;
        }
    }
}