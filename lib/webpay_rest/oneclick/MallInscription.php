<?php


namespace Transbank\Webpay\Oneclick;


use Transbank\Webpay\Exceptions\InscriptionFinishException;
use Transbank\Webpay\Exceptions\InscriptionStartException;
use Transbank\Webpay\Oneclick;

class MallInscription
{

    const INSCRIPTION_START_ENDPOINT = 'rswebpaytransaction/api/oneclick/v1.0/inscriptions';
    const INSCRIPTION_FINISH_ENDPOINT = 'rswebpaytransaction/api/oneclick/v1.0/inscriptions/$TOKEN$';
    const INSCRIPTION_DELETE_ENDPOINT = 'rswebpaytransaction/api/oneclick/v1.0/inscriptions';

    public static function start($userName, $email, $responseUrl, $options = null)
    {

        if ($options == null) {
            $commerceCode = Oneclick::getCommerceCode();
            $apiKey = Oneclick::getApiKey();
            $baseUrl = Oneclick::getIntegrationTypeUrl();
        } else {
            $commerceCode = $options->getCommerceCode();
            $apiKey = $options->getApiKey();
            $baseUrl = WebpayPlus::getIntegrationTypeUrl($options->getIntegrationType());
        }

        $http = Oneclick::getHttpClient();
        $headers = [
            "Tbk-Api-Key-Id" => $commerceCode,
            "Tbk-Api-Key-Secret" => $apiKey
        ];

        $payload = json_encode(["username" => $userName, "email" => $email, "response_url" => $responseUrl]);

        $httpResponse = $http->post($baseUrl,
            self::INSCRIPTION_START_ENDPOINT,
            $payload,
            ['headers' => $headers]
        );

        $httpCode = $httpResponse->getStatusCode();
        if ($httpCode != 200 || $httpCode != 204) {
            $reason = $httpResponse->getReasonPhrase();
            $message = "Could not obtain a response from the service: $reason (HTTP code $httpCode )";
            $body = json_decode($httpResponse->getBody(), true);

            if (isset($body["error_message"])) {
                $tbkErrorMessage = $body["error_message"];
                $message = "$message. Details: $tbkErrorMessage";
            }
            throw new InscriptionStartException($message, -1);
        }

        $responseJson = json_decode($httpResponse->getBody(), true);
        if (array_key_exists('error_message', $responseJson)) {
            throw new InscriptionStartException($responseJson['error_message']);
        }
        $inscriptionStartResponse = new InscriptionStartResponse($responseJson);

        return $inscriptionStartResponse;
    }


    public static function finish($token, $options = null)
    {
        if ($options == null) {
            $commerceCode = Oneclick::getCommerceCode();
            $apiKey = Oneclick::getApiKey();
            $baseUrl = Oneclick::getIntegrationTypeUrl();
        } else {
            $commerceCode = $options->getCommerceCode();
            $apiKey = $options->getApiKey();
            $baseUrl = WebpayPlus::getIntegrationTypeUrl($options->getIntegrationType());
        }

        $http = Oneclick::getHttpClient();
        $headers = [
            "Tbk-Api-Key-Id" => $commerceCode,
            "Tbk-Api-Key-Secret" => $apiKey
        ];

        $url = str_replace('$TOKEN$', $token, self::INSCRIPTION_FINISH_ENDPOINT);

        $httpResponse = $http->put($baseUrl,
            $url,
            null,
            ['headers' => $headers]
        );

        $httpCode = $httpResponse->getStatusCode();
        if ($httpCode != 200 || $httpCode != 204) {
            $reason = $httpResponse->getReasonPhrase();
            $message = "Could not obtain a response from the service: $reason (HTTP code $httpCode )";
            $body = json_decode($httpResponse->getBody(), true);

            if (isset($body["error_message"])) {
                $tbkErrorMessage = $body["error_message"];
                $message = "$message. Details: $tbkErrorMessage";
            }
            throw new InscriptionFinishException($message, -1);
        }

        $responseJson = json_decode($httpResponse->getBody(), true);

        if (array_key_exists('error_message', $responseJson)) {
            throw new InscriptionFinishException($responseJson['error_message']);
        }
        $json = json_decode($httpResponse->getBody(), true);

        $inscriptionFinishResponse = new InscriptionFinishResponse($json);

        return $inscriptionFinishResponse;
    }

    public static function delete($tbkUser, $userName, $options = null)
    {
        if ($options == null) {
            $commerceCode = Oneclick::getCommerceCode();
            $apiKey = Oneclick::getApiKey();
            $baseUrl = Oneclick::getIntegrationTypeUrl();
        } else {
            $commerceCode = $options->getCommerceCode();
            $apiKey = $options->getApiKey();
            $baseUrl = Oneclick::getIntegrationTypeUrl($options->getIntegrationType());
        }

        $http = Oneclick::getHttpClient();
        $headers = [
            "Tbk-Api-Key-Id" => $commerceCode,
            "Tbk-Api-Key-Secret" => $apiKey
        ];

        $payload = json_encode(["tbk_user" => $tbkUser, "username" => $userName]);

        $httpResponse = $http->delete($baseUrl,
            self::INSCRIPTION_DELETE_ENDPOINT,
            $payload,
            ['headers' => $headers]
        );

        $httpCode = $httpResponse->getStatusCode();
        if ($httpCode != 200 || $httpCode != 204) {
            $reason = $httpResponse->getReasonPhrase();
            $message = "Could not obtain a response from the service: $reason (HTTP code $httpCode )";
            $body = json_decode($httpResponse->getBody(), true);

            if (isset($body["error_message"])) {
                $tbkErrorMessage = $body["error_message"];
                $message = "$message. Details: $tbkErrorMessage";
            }
            throw new InscriptionFinishException($message, -1);
        }

        $responseJson = json_decode($httpResponse->getBody(), true);
        if (array_key_exists('error_message', $responseJson)) {
            throw new InscriptionFinishException($responseJson['error_message']);
        }

        $inscriptionFinishResponse = new InscriptionFinishResponse($responseJson);

        return $inscriptionFinishResponse;
    }
}
