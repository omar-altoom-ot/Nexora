<?php
// Controllers/compatibility.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../Model/CompatibilityChecker.php';
try {
    if($_SERVER['REQUEST_METHOD']!=='POST') {http_response_code(405);header('Allow: POST');throw new InvalidArgumentException('POST required.');}
    $raw=file_get_contents('php://input');
    if(strlen($raw)>10000)throw new InvalidArgumentException('Request too large.');
    $body=json_decode($raw,true);
    if(!is_array($body)||!isset($body['products'])||!is_array($body['products'])||count($body['products'])>8)throw new InvalidArgumentException('Invalid products payload.');
    echo json_encode(['success'=>true,'data'=>(new CompatibilityChecker())->check($body['products'])],JSON_UNESCAPED_UNICODE);
} catch(InvalidArgumentException $e) {
    http_response_code(http_response_code()===405?405:400);
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
} catch(Throwable $e) {
    error_log('Compatibility: '.$e->getMessage());http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Unable to check compatibility. Confirm product_specs exists and database is available.']);
}
