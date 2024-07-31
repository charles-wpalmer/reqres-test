<?php

use Cwp\Users\Exceptions\ReqresException;
use Cwp\Users\Reqres\Reqres;
use Cwp\Users\Users;
use Cwp\Users\DTO\User;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

beforeEach(function () {
    $this->mockHandler = new MockHandler();
    $handlerStack = HandlerStack::create($this->mockHandler);
    $client = new Client(['handler' => $handlerStack]);

    $this->userRepository = new Reqres($client);

    $reflection = new ReflectionClass(Users::class);
    $instanceProperty = $reflection->getProperty('repository');
    $instanceProperty->setAccessible(true);
    $instanceProperty->setValue(null, $this->userRepository);
});

it('creates a new user', function () {
    $this->mockHandler->append(new Response(201, [], json_encode([
        'name' => 'Jane Doe',
        'job' => 'Developer',
        'id' => '123',
        'createdAt' => '2023-01-01T00:00:00.000Z',
    ])));

    $userDTO = new User(null, 'Jane Doe', 'Developer');
    $response = Users::create($userDTO);

    expect($response)->toBeInt();
    expect($response)->toBe(123);
});

it('fetches a single user', function () {
    $this->mockHandler->append(new Response(200, [], json_encode([
        'data' => [
            'id' => 2,
            'email' => 'janet.weaver@reqres.in',
            'first_name' => 'Janet',
            'last_name' => 'Weaver',
            'avatar' => 'https://reqres.in/img/faces/2-image.jpg'
        ]
    ])));

    $user = Users::get(2);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->getId())->toBe(2);
    expect($user->getName())->toBe('Janet Weaver');
});

it('fetches a single user by ID user not found', function () {
    $this->mockHandler->append(new Response(404, [], json_encode([])));

    $user = Users::get(2);

    expect($user)->toBeNull(User::class);
});

it('fetches all users with pagination', function () {
    $this->mockHandler->append(new Response(200, [], json_encode([
        'page' => 1,
        'per_page' => 6,
        'total' => 12,
        'total_pages' => 2,
        'data' => [
            [
                'id' => 1,
                'email' => 'george.bluth@reqres.in',
                'first_name' => 'George',
                'last_name' => 'Bluth',
                'avatar' => 'https://reqres.in/img/faces/1-image.jpg'
            ],
            [
                'id' => 2,
                'email' => 'janet.weaver@reqres.in',
                'first_name' => 'Janet',
                'last_name' => 'Weaver',
                'avatar' => 'https://reqres.in/img/faces/2-image.jpg'
            ]
        ]
    ])));

    $response = Users::all();

    expect($response)->toBeArray();
    expect($response['users'])->toBeArray();
    expect($response['users'][0])->toBeInstanceOf(User::class);
    expect($response['page'])->toBe(1);
    expect($response['total_pages'])->toBe(2);
});

it('handles exceptions when the API request fails', function () {
    $this->mockHandler->append(new Response(500, [], json_encode([])));

    Users::get(1);
})->expectException(ReqresException::class);
