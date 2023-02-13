<?php


namespace App\Service\paiement;


use GuzzleHttp\Client;
use http\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TransferZero\Api\CurrencyInfoApi;
use TransferZero\Api\TransactionsApi;
use TransferZero\ApiException;
use TransferZero\Configuration;
use TransferZero\Model\ApiLog;
use TransferZero\Model\PayinMethod;
use TransferZero\Model\PayinMethodDetails;
use TransferZero\Model\PayoutMethod;
use TransferZero\Model\PayoutMethodDetails;
use TransferZero\Model\Recipient;
use TransferZero\Model\Sender;
use TransferZero\Model\Transaction;
use TransferZero\Model\TransactionRequest;

class TransferzeroService
{
    private $params;
    /**
     * @var Client
     */
    private $client;
    private $tokencinet;
    private $logger;

    /**
     * EkolopayService constructor.
     * @param LoggerInterface $logger
     * @param ParameterBagInterface $params
     */
    public function __construct(LoggerInterface $logger, ParameterBagInterface $params)
    {
        $this->params = $params;
        $this->logger = $logger;
        $this->client = new Client([
            'base_uri' => $params->get('EKOLO_URL'),
        ]);
        Configuration::getDefaultConfiguration()
            ->setHost($params->get('ZERO_URL'))
            ->setApiKey($params->get('ZERO_API_KEY'))
            ->setApiSecret($params->get('ZERO_API_SECRET'));
    }

    function postpaiement($data)
    {
        $sender = new Sender();
        $sender->setFirstName($data['sender_firstname']);
        $sender->setLastName($data['sender_lastname']);

        $sender->setPhoneCountry($data['sender_countrycode']);
        $sender->setPhoneNumber($data['sender_phone']);

        $sender->setCountry($data['sender_countrycode']);
        $sender->setCity($data['sender_city']);
        $sender->setStreet($data['sender_street']);
        $sender->setPostalCode($data['sender_codepostal']);
        $sender->setAddressDescription("");

        $sender->setBirthDate($data['sender_birthdate']);

// you can usually use your company's contact email address here
        $sender->setEmail("info@agensic.com");

        $sender->setExternalId("Sender:CG:234523");

// you'll need to set these fields but usually you can leave them the default
        $sender->setIp("127.0.0.1");
        $sender->setDocuments([]);


        $details = new PayoutMethodDetails();
        $details->setFirstName($data['receiver_name']);
        $details->setLastName($data['receiver_name']);
        $details->setBankAccount($data['receiver_bankaccount']);
        $details->setBankCode($data['receiver_bankcode']);
        $details->setBankAccountType($data['receiver_banktype']);

        $payout = new PayoutMethod();
        $payout->setType("USD::Balance");
        $payout->setDetails($details);

        $recipient = new Recipient();
        $recipient->setRequestedAmount(100);
        $recipient->setRequestedCurrency($data['receiver_currency']);
        $recipient->setType("person");
        $recipient->setPayoutMethod($payout);


        $transaction = new Transaction();
        $transaction->setSender($sender);
        $transaction->setRecipients([$recipient]);
        $transaction->setInputCurrency($data['receiver_currency']);
        $transaction->setExternalId($data['reference']);


        $request = new TransactionRequest();
        $request->setTransaction($transaction);

        $api = new TransactionsApi();

        try {
            $response = $api->createAndFundTransaction($request);
        } catch (ApiException $e) {
            if ($e->isValidationError()) {
                $response = $e->getResponseObject();
                // Process validation error
            }
            throw $e;
        }

        return $response;
    }
    function postcollectionOut($data)
    {
        $this->logger->info(json_encode($data));
        $sender = new Sender();
        $sender->setFirstName($data['sender_firstname']);
        $sender->setLastName($data['sender_lastname']);
        $sender->setPhoneCountry($data['sender_countrycode']);
        $sender->setPhoneNumber($data['sender_phone']);
        $sender->setCountry($data['sender_countrycode']);
        $sender->setCity("Accra");
        $sender->setStreet("1 La Rd");
        $sender->setPostalCode("GA100");
        $sender->setAddressDescription("");
        $sender->setBirthDate("1974-12-24");
// you can usually use your company's contact email address here
        $sender->setEmail("info@transferzero.com");
        $sender->setExternalId("Sender:" . $data['sender_countrycode'] . ":" . $this->randomNumber());

// you'll need to set these fields but usually you can leave them the default
        $sender->setIp("127.0.0.1");
        $sender->setDocuments([]);
        $details = new PayoutMethodDetails();
        $details->setReference("Collection:" . $data['sender_countrycode'] . ":" . $this->randomNumber());
        //$details->setBankAccount('123456789');
       // $details->setBankCode("030100");
        $details->setIban($data['receiver_bankiban']);
        $details->setBankName('Deutsche Bank');
        $details->setFirstName("donald");
        $details->setLastName('eboundi');
        $payout = new PayoutMethod();
        $payout->setType($data['method_payment']);
        $payout->setDetails($details);

        $recipient = new Recipient();
        $recipient->setRequestedAmount(50);
        $recipient->setRequestedCurrency($data['sender_currency']);
        $recipient->setPayoutMethod($payout);

        $details = new PayinMethodDetails();
        $details->setPhoneNumber($data['sender_phone']);
        //$details->setMobileProvider("vodafone");

        $method = new PayinMethod();
        $method->setType($data['method_payment']);
        //$method->setUxFlow("ussd_popup");
        $method->setInDetails($details);

        $transaction = new Transaction();
        $transaction->setSender($sender);
        $transaction->setRecipients([$recipient]);
        $transaction->setPayinMethods([$method]);
        $transaction->setInputCurrency($data['sender_currency']);
        $transaction->setExternalId("Transaction:" . $data['sender_currency'] . ":" . $this->randomNumber());


        $request = new TransactionRequest();
        $request->setTransaction($transaction);

        $api = new TransactionsApi();

        try {
            $response = $api->postTransactions($request);
        } catch (ApiException $e) {
            if ($e->isValidationError()) {
                $response = $e->getResponseObject();
                // Process validation error
            }
            throw $e;
        }
        return $response;
    }
    function postcollection($data)
    {
        $this->logger->info(json_encode($data));
        $sender = new Sender();
        $sender->setFirstName($data['sender_firstname']);
        $sender->setLastName($data['sender_lastname']);
        $sender->setPhoneCountry($data['sender_countrycode']);
        $sender->setPhoneNumber($data['sender_phone']);
        $sender->setCountry($data['sender_countrycode']);
        $sender->setCity("Accra");
        $sender->setStreet("1 La Rd");
        $sender->setPostalCode("GA100");
        $sender->setAddressDescription("");
        $sender->setBirthDate("1974-12-24");
// you can usually use your company's contact email address here
        $sender->setEmail("info@transferzero.com");
        $sender->setExternalId("Sender:" . $data['sender_countrycode'] . ":" . $this->randomNumber());

// you'll need to set these fields but usually you can leave them the default
        $sender->setIp("127.0.0.1");
        $sender->setDocuments([]);
        $details = new PayoutMethodDetails();
        $details->setReference("Collection:" . $data['sender_countrycode'] . ":" . $this->randomNumber());

        $payout = new PayoutMethod();
        $payout->setType("USD::Balance");
        $payout->setDetails($details);

        $recipient = new Recipient();
        $recipient->setRequestedAmount(50);
        $recipient->setRequestedCurrency($data['sender_currency']);

        $recipient->setPayoutMethod($payout);

        $details = new PayinMethodDetails();
        $details->setPhoneNumber($data['sender_phone']);
        $details->setMobileProvider("vodafone");

        $method = new PayinMethod();
        $method->setType($data['method_payment']);
        $method->setUxFlow("ussd_popup");
        $method->setInDetails($details);

        $transaction = new Transaction();
        $transaction->setSender($sender);
        $transaction->setRecipients([$recipient]);
        $transaction->setPayinMethods([$method]);
        $transaction->setInputCurrency($data['sender_currency']);
        $transaction->setExternalId("Transaction:" . $data['sender_currency'] . ":" . $this->randomNumber());


        $request = new TransactionRequest();
        $request->setTransaction($transaction);

        $api = new TransactionsApi();

        try {
            $response = $api->postTransactions($request);
        } catch (ApiException $e) {
            if ($e->isValidationError()) {
                $response = $e->getResponseObject();
                // Process validation error
            }
            throw $e;
        }
        return $response;
    }

    function posttransactiontest($data)
    {
        $sender = new Sender();
        $sender->setFirstName("Jane");
        $sender->setLastName("Doe");
        $sender->setPhoneCountry("GH");
        $sender->setPhoneNumber("0301234567");
        $sender->setCountry("GH");
        $sender->setCity("Accra");
        $sender->setStreet("1 La Rd");
        $sender->setPostalCode("GA100");
        $sender->setAddressDescription("");
        $sender->setBirthDate("1974-12-24");
// you can usually use your company's contact email address here
        $sender->setEmail("info@transferzero.com");
        $sender->setExternalId("Sender:GH:9547");

// you'll need to set these fields but usually you can leave them the default
        $sender->setIp("127.0.0.1");
        $sender->setDocuments([]);
        $details = new PayoutMethodDetails();
        $details->setReference("Collection:GHS:91476");

        $payout = new PayoutMethod();
        $payout->setType("USD::Balance");
        $payout->setDetails($details);

        $recipient = new Recipient();
        $recipient->setRequestedAmount(50);
        $recipient->setRequestedCurrency("GHS");
        $recipient->setPayoutMethod($payout);


        $details = new PayinMethodDetails();
        $details->setPhoneNumber("+2339999999");
        $details->setMobileProvider("vodafone");

        $method = new PayinMethod();
        $method->setType("GHS::Mobile");
        $method->setUxFlow("ussd_popup");
        $method->setInDetails($details);

        $transaction = new Transaction();
        $transaction->setSender($sender);
        $transaction->setRecipients([$recipient]);
        $transaction->setPayinMethods([$method]);
        $transaction->setInputCurrency("GHS");
        $transaction->setExternalId("Transaction:GHS:91475");


        $request = new TransactionRequest();
        $request->setTransaction($transaction);

        $api = new TransactionsApi();

        try {
            $response = $api->postTransactions($request);
        } catch (ApiException $e) {
            if ($e->isValidationError()) {
                $response = $e->getResponseObject();
                // Process validation error
            }
            throw $e;
        }


        return $response;
    }

    function randomNumber()
    {
        $number = [0, 2, 3, 6, 8, 8, 8, 8, 5, 7,];
        $list = "";
        for ($i = 0; $i < 9; $i++) {
            $list .= random_int(0, 9);
        }
        return $list;
    }
}
