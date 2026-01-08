<?php

namespace SaeidSharafi\LaravelSms\Gateways;

use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SaeidSharafi\LaravelSms\Exceptions\CouldNotSendNotification;
use SaeidSharafi\LaravelSms\Sms;

class RangineGateway extends GatewayAbstract
{
    public function __construct(Sms $sms)
    {
        parent::__construct($sms);
        $this->webService = config('sms.gateway.rangine.webService');
        $this->apiKey = $sms->apiKey ?: config('sms.gateway.rangine.apiKey');
        $this->from = $sms->from ?: config('sms.gateway.rangine.from');
    }

    /**
     * @inheritDoc
     */
    public function sendSms()
    {
        try {
            $response = Http::baseUrl($this->webService)
                ->withHeaders([
                    'apikey' => $this->apiKey,
                ])
                ->post(
                    '/sms/send/webservice/single',
                    [
                        'sender'    => $this->from,
                        'recipient' => $this->to,
                        'message'   => $this->message,
                    ]
                );
            if ($response->ok()) {
                return $response->json('data.message_id');
            }
            if ($response->failed()) {
                Log::error("Rangine Gateway Error Single SMS, to: {$this->to}, Message: {$this->message} | response: " .json_encode($response->json()));
                return null;
            }
            return null;
        } catch (RequestException $exception) {
            throw CouldNotSendNotification::serviceRespondedWithAnError(
                $exception->getMessage(),
                $exception->getCode()
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function getCredit()
    {
        // TODO: Implement getCredit() method.
    }

    public function sendPatternSms()
    {
        try {
            if (config(('app.debug'))) {
                Log::info("sending sms with from :".$this->from);
                Log::info("sending sms with to :".implode(",", $this->to));
                Log::info("sending sms with apiKey :".$this->apiKey);
                Log::info("sending sms with pattern :".$this->pattern);
                Log::info("sending sms with parameters :".implode(",", $this->parameters));
            }
            $response = Http::baseUrl($this->webService)
                ->withHeaders([
                    'apikey' => $this->apiKey,
                ])
                ->post(
                    '/sms/pattern/normal/send',
                    [
                        'code'    => $this->pattern,
                        'sender'    => $this->from,
                        'recipient' => $this->to[0] ?? null,
                        'variable'   => $this->parameters,
                    ]
                );
            if ($response->ok()) {
                return $response->json('data.message_id');
            }
            if ($response->failed()) {
                Log::error("Rangine Gateway Error (pattern: {$this->pattern}),to: {$this->to}, Status: ({$response->status()}), parameters :".implode(",", $this->parameters) . " response: " .json_encode($response->json()));
                return null;
            }
            return null;
        } catch (RequestException $exception) {
            throw CouldNotSendNotification::serviceRespondedWithAnError(
                $exception->getMessage(),
                $exception->getCode()
            );
        }
    }
}
