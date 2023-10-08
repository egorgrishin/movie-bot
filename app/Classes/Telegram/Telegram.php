<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace App\Classes\Telegram;

use App\Exceptions\ServerError;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class Telegram
{
    private const URL = 'https://api.telegram.org';
    private static ?Client $client = null;

    /**
     * Отправляет сообщение
     */
    public static function send(array $data): string
    {
        return self::sendRequest(Method::SendMessage, $data)
            ->getBody()
            ->getContents();
    }

    /**
     * Обновляет сообщение
     */
    public static function update(array $data): string
    {
        return self::sendRequest(Method::EditMessageText, $data)
            ->getBody()
            ->getContents();
    }

    private static function sendRequest(Method $method, array $data): ResponseInterface
    {
        try {
            return self::getClient()->post($method->value, [
                'headers' => [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
            ]);
        } catch (GuzzleException $exception) {
            logger()->error($exception);
            throw new ServerError();
        }
    }

    private static function getClient(): Client
    {
        if (self::$client === null) {
            self::$client = new Client([
                'base_uri' => self::URL . '/bot'. env('BOT_TOKEN') . '/',
            ]);
        }
        return self::$client;
    }
}
