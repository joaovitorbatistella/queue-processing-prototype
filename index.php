<?php

use Infra\GenericConsts;
use Infra\Json;
use Infra\Routes;
use Validator\RequestValidator;

require_once('env.php');

try {
    $RequestValidator = new RequestValidator(Routes::getRoutes());
    $return = $RequestValidator->handleRequest();

    if(isset($return)) {
        $Json = new Json();
        $Json->handleReturnArray($return);
    }

} catch (Exception $exception) {
    echo json_encode([
        GenericConsts::TYPE => GenericConsts::ERROR_TYPE,
        GenericConsts::RESPONSE => utf8_encode($exception->getMessage())
    ], JSON_THROW_ON_ERROR, 512);
    exit;
}