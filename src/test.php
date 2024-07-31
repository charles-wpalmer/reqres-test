<?php

require __DIR__.'/../vendor/autoload.php';

use Cwp\Users\DTO\User;
use Cwp\Users\Users;

var_dump(Users::all());

var_dump(Users::get(1));

var_dump(json_encode(Users::get(1)));

var_dump(Users::create(new User(null, 'Charles', 'Job')));