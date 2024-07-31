<?php

declare(strict_types=1);

namespace Cwp\Users;

use Cwp\Users\DTO\User;
use Cwp\Users\Exceptions\ReqresException;
use Cwp\Users\Reqres\Reqres;
use GuzzleHttp\Client;

class Users
{
    private static ?Reqres $repository = null;

    private static function getInstance(): Reqres
    {
        if (self::$repository === null) {
            self::$repository = new Reqres(new Client());
        }

        return self::$repository;
    }

    /**
     * @throws ReqresException
     */
    public static function get(int $id): User|null
    {
        return self::getInstance()->getUserById($id);
    }

    /**
     * @throws ReqresException
     */
    public static function all(int $page = 1): array
    {
        return self::getInstance()->getAllUsers($page);
    }

    /**
     * @throws ReqresException
     */
    public static function create(User $user): int
    {
        return self::getInstance()->createUser($user);
    }
}
