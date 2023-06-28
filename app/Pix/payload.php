<?php

namespace App\Pix;

class Payload {

    /**
   * IDs do Payload do Pix
   * @var string
   */
  const ID_PAYLOAD_FORMAT_INDICATOR = '00';
  const ID_MERCHANT_ACCOUNT_INFORMATION = '26';
  const ID_MERCHANT_ACCOUNT_INFORMATION_GUI = '00';
  const ID_MERCHANT_ACCOUNT_INFORMATION_KEY = '01';
  const ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION = '02';
  const ID_MERCHANT_CATEGORY_CODE = '52';
  const ID_TRANSACTION_CURRENCY = '53';
  const ID_TRANSACTION_AMOUNT = '54';
  const ID_COUNTRY_CODE = '58';
  const ID_MERCHANT_NAME = '59';
  const ID_MERCHANT_CITY = '60';
  const ID_ADDITIONAL_DATA_FIELD_TEMPLATE = '62';
  const ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID = '05';
  const ID_CRC16 = '63';

  /** 
   * Chave Pix
   *  @var string
   */

     private $pixKey;

     /**
      * Descrição do processo do pagamento
      * @var string
      */
      
      private $description;
    
    /**
     * Nome da conta que deseja
     * @var string
     */

     private $merchantName;

     /**
      * Cidade do titular da conta
      * @var string
      */

      private $merchantCity;

      /**
       * ID da transação do Pix
       * @var string
       */

       private $idPix;

        /**
       * Valor da transação
       * @var string
       */

       private $amount;

       /**
        * Método responsável por definir o valor de $pixKey
        * @param string $pixKey
        */

       public function setPixKey($pixKey){

          $this->pixKey = $pixKey;
          return $this;
       }

        /**
        * Método responsável por definir o valor de $description
        * @param string $description
        */

        public function setDescription($description){

            $this->description = $description;
            return $this;
         }

          /**
        * Método responsável por definir o valor de $merchantName
        * @param string $merchantName
        */

       public function setMerchantName($merchantName){

        $this->merchantName = $merchantName;
        return $this;
     }

       /**
        * Método responsável por definir o valor de $merchantName
        * @param string $merchantName
        */

        public function setMerchantCity($merchantCity){

            $this->merchantCity = $merchantCity;
            return $this;
         }

          /**
        * Método responsável por definir o valor de $merchantCity
        * @param string $merchantCity
        */

        public function setIdPix($idPix){

            $this->merchantCity = $idPix;
            return $this;
         }

          /**
        * Método responsável por definir o valor de $idPix
        * @param string $idPix
        */

        public function setAmount($amount){

            $this->amount = (string)number_format($amount, 2, '.', '');
            return $this;
         }

          /**
        * Método responsável por definir o valor de $amount
        * @param float $amount
        */
    
    private function getValue($id, $value){
        $size = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
        return $id.$size.$value;
    }

    /**
     * Responsável por retornar o valor completo de um objeto do payload
     * @param string $id
     * @param string $value
     * @param string $id, $size, $value
     */

     private function getMerchantAccountInformation(){
        //domínio do banco
        $gui = $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_GUI, 'br.gov.bcb.pix');
        // Chave Pix 
        $key = $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_KEY, $this->pixKey);
        //Descrição do pagamento
        $description = strlen($this->description) ? $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION, $this->description) : '';
        //Valor completo da conta
        return $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION, $gui.$key.$description);
     }

     /**
      * Métodos responsáveis por gerar o código completo do payload Pix
      * @return string
      */

      private function getAdditionalDataFieldTemplate(){
        $idPix = $this->getValue(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID, $this->idPix);

        //Retorna o valor completo
        return $this->getValue(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE, $idPix);
      }

      /**
       * Método responsável por retornar os valores dos campos adicionais do pix
       * @return string
       */

     public function getPayload(){
        //criando o payload
        $payload =  $this->getValue(self::ID_PAYLOAD_FORMAT_INDICATOR, '01').
                    $this->getMerchantAccountInformation().
                    $this->getValue(self::ID_MERCHANT_CATEGORY_CODE, '0000').
                    $this->getValue(self::ID_TRANSACTION_CURRENCY, '986').
                    $this->getValue(self::ID_TRANSACTION_AMOUNT, $this->amount).
                    $this->getValue(self::ID_COUNTRY_CODE, 'BR').
                    $this->getValue(self::ID_MERCHANT_NAME, $this->merchantName).
                    $this->getValue(self::ID_MERCHANT_CITY, $this->merchantCity).
                    $this->getAdditionalDataFieldTemplate();
                    
        // retorna o payload mais crc16
        return $payload.$this->getCRC16($payload);
     }

     /**
      * Método responsável por gerar o código completo do payload Pix
      * @return string
      */

 
  private function getCRC16($payload) {
    //ADICIONA DADOS GERAIS NO PAYLOAD
    $payload .= self::ID_CRC16.'04';

    //DADOS DEFINIDOS PELO BACEN
    $polinomio = 0x1021;
    $resultado = 0xFFFF;

    //CHECKSUM
    if (($length = strlen($payload)) > 0) {
        for ($offset = 0; $offset < $length; $offset++) {
            $resultado ^= (ord($payload[$offset]) << 8);
            for ($bitwise = 0; $bitwise < 8; $bitwise++) {
                if (($resultado <<= 1) & 0x10000) $resultado ^= $polinomio;
                $resultado &= 0xFFFF;
            }
        }
    }

    //RETORNA CÓDIGO CRC16 DE 4 CARACTERES
    return self::ID_CRC16.'04'.strtoupper(dechex($resultado));
}
     /**
   * Método responsável por calcular o valor da hash de validação do código pix
   * @return string
   */
}