<?php

use Cwp\Users\DTO\User;
use Cwp\Users\Exceptions\ReqresException;
use Cwp\Users\Reqres\Reqres;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

beforeEach(function () {
    $this->mockHandler = new MockHandler();
    $handlerStack = HandlerStack::create($this->mockHandler);
    $this->client = new Client(['handler' => $handlerStack]);

    $this->userRepository = new Reqres($this->client);
});

it('creates a new user', function () {
    $this->mockHandler->append(new Response(201, [], json_encode([
        'name' => 'Jane Doe',
        'job' => 'Developer',
        'id' => 123,
        'createdAt' => '2023-01-01T00:00:00.000Z',
    ])));

    $userDTO = new User(null, 'Jane Doe', 'Developer');
    $response = $this->userRepository->createUser($userDTO);

    expect($response)->toBeInt();
    expect($response)->toBe(123);
});

it('fetches a single user by ID', function () {
    $this->mockHandler->append(new Response(200, [], json_encode([
        'data' => [
            'id' => 2,
            'email' => 'janet.weaver@reqres.in',
            'first_name' => 'Janet',
            'last_name' => 'Weaver',
            'avatar' => 'https://reqres.in/img/faces/2-image.jpg'
        ]
    ])));

    $user = $this->userRepository->getUserById(2);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->getId())->toBe(2);
    expect($user->getName())->toBe('Janet Weaver');
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

    $response = $this->userRepository->getAllUsers();

    expect($response)->toBeArray();
    expect($response['users'])->toBeArray();
    expect($response['users'][0])->toBeInstanceOf(User::class);
    expect($response['page'])->toBe(1);
    expect($response['total_pages'])->toBe(2);
});

it('fetches all users with pagination no data', function () {
    $this->mockHandler->append(new Response(200, [], json_encode([
        'page' => 1,
        'per_page' => 6,
        'total' => 0,
        'total_pages' => 1,
        'data' => []
    ])));

    $response = $this->userRepository->getAllUsers(1);

    expect($response)->toBeArray();
    expect(count($response['users']))->toEqual(0);
    expect($response['users'])->toBeArray();
});

it('handles exceptions when the API request fails', function () {
    $this->mockHandler->append(new RequestException("Error Communicating with Server", new Request('GET', 'test')));

    $this->userRepository->getUserById(1);
})->expectException(ReqresException::class);

it('handles exceptions when the API request fail', function () {
    $this->mockHandler->append(new Response(500, [], json_encode([])));

    $this->userRepository->getUserById(1);
})->expectException(ReqresException::class);
