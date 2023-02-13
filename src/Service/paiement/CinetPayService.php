<?php


namespace App\Service\paiement;


use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CinetPayService
{
    //const BASE_URL = "https://client.cinetpay.com";
    const BASE_URL="https://api-checkout.cinetpay.com/v2/";
    private ParameterBagInterface $params;
    /**
     * @var Client
     */
    private Client $client;
    private $tokencinet;
    private $logger;

    /**
     * ProductService constructor.
     * @param ParameterBagInterface $params
     */
    public function __construct(LoggerInterface $logger,ParameterBagInterface $params)
    {
        $this->params = $params;
        $this->logger=$logger;
        $this->client = new Client([
            'base_uri' => self::BASE_URL,
        ]);
    }
    public function authenticate(){
        $endpoint = '/v1/auth/login';
        $arrayJson=[
            "apikey"=>$this->params->get('CINET_KEY'),
            "password"=>$this->params->get('CINET_PASSWORD')
        ];
        $this->logger->info(json_encode($arrayJson));
        $options = [
            'headers' => [
                'Accept' => 'application/x-www-form-urlencoded',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'form_params' => $arrayJson,
        ];
        $res=$this->client->post($endpoint,$options);

        $valresp=json_decode($res->getBody(),true);
        if ($valresp['code']===0){
            $this->tokencinet=$valresp['data']['token'];
            return true;
        }else{
            $this->tokencinet="";
            return false;
        }

    }
    public function getSolde(){

        $endpoint = '/v1/transfer/check/balance/?token='.$this->tokencinet;
        if ($this->authenticate()){
            $res=$this->client->get($endpoint);
            $valresp=json_decode($res->getBody(),true);
            return [
                'code'=>200,
                'data'=>$valresp['data']
            ];
        }else{
            return [
                'code'=>400,
                'data'=>[]
            ];
        }
    }
    public function sendPrefund($data){
        $endpoint = '/v2/payment';
        $formData = array(
            "apikey"=> $this->params->get('CINET_KEY'),
            "site_id"=> $this->params->get('CINET_SITEID'),
            "transaction_id"=> $data['transaction_id'],
            "amount"=> $data['amount'],
            "currency"=> $data['currency'],
            "customer_surname"=> $data['customer_surname'],
            "customer_name"=> $data['customer_name'],
            "description"=> $data['description'],
            "notify_url" => $data['notify_url'],
            "return_url" => $data['return_url'],
            "customer_country" => $data['customer_country'],
            "customer_phone_number" => $data['customer_phone_number'],
            "channels" => "ALL",
            "metadata" => "",
            "alternative_currency" => "USD",
            "customer_email" => "exemple@wetransfercash.com",
            "customer_address" => "kinshasa",
            "customer_city" => "kinshasa",
            "customer_state" => "",
            "customer_zip_code" => ""
        );
        $this->logger->info("--------------");
        $this->logger->info(json_encode($formData));
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($formData),
        ];
        $res=$this->client->post($endpoint,$options);
        $valresp = json_decode($res->getBody(), true);
        $this->logger->info(json_encode($valresp));
        if ($valresp['code']=="201"){
            return [
                'code'=>200,
                "message"=>$valresp["data"]['payment_url']
            ];
        }else{
            return [
                'code'=>'500',
                'message'=>$valresp['message']
            ];
        }

    }
}
