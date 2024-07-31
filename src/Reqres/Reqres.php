<?php

declare(strict_types=1);

namespace Cwp\Users\Reqres;

use Cwp\Users\Exceptions\ReqresException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Cwp\Users\DTO\User;

class Reqres
{
    public function __construct(
        private readonly Client $client,
        private readonly string $baseUri = 'https://reqres.in/api/',
    )
    {

    }

    public function createUser(User $user): int
    {
        try {
            $response = $this->client->post($this->baseUri . 'users', [
                'name' => $user->getName(),
                'job'  => $user->getJob(),
            ]);

            return (int) json_decode((string) $response->getBody(), true)['id'];
        } catch (GuzzleException $e) {
            Throw new ReqresException($e->getMessage());
        }
    }

    public function getUserById(int $id): ?User
    {
        try {
            $response = $this->client->get($this->baseUri . "users/{$id}");
            $data = json_decode((string) $response->getBody(), true);

            if (!array_key_exists('data', $data)) {
                return null;
            }

            return new User(
                $data['data']['id'],
                $data['data']['first_name'] . ' ' . $data['data']['last_name'],
                null,
            );
        } catch (GuzzleException $e) {
            if ($e->getCode() === 404) {
                return null;
            }

            Throw new ReqresException($e->getMessage());
        }
    }

    public function getAllUsers(int $page = 1): array
    {
        try {
            $response = $this->client->get($this->baseUri . 'users', [
                'query' => ['page' => $page]
            ]);

            $data = json_decode((string) $response->getBody(), true);

            $users = array_map(function ($userData) {
                return new User(
                    $userData['id'],
                    $userData['first_name'] . ' ' . $userData['last_name'],
                    null,
                );
            }, $data['data']);

            return [
                'users' => $users,
                'page' => $data['page'],
                'total_pages' => $data['total_pages'],
            ];
        } catch (GuzzleException $e) {
            Throw new ReqresException($e->getMessage());
        }
    }
}
