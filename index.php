<?php

require __DIR__.'/vendor/autoload.php';

use \App\Pix\payload;
use Mpdf\QrCode\QrCode;
use Mpdf\QrCode\Output;

//Instância principal do payload pix 
$obPayload = (new Payload)->setPixKey('03962262130')
                          ->setDescription('Pagamento para João Pedro')
                          ->setMerchantName('João Pedro')
                          ->setMerchantCity('GOIANIA')
                          ->setAmount(200.00)
                          ->setIdPix('JPAP2003');

//código de pagamento pix
$payloadQrCode = $obPayload->getPayload();


$obQrCode = new QrCode($payloadQrCode);

//Imagem gerada do QR code 
$image = (new Output\Png)->output($obQrCode, 400);


header('Content-Type: image/png');
echo $image;