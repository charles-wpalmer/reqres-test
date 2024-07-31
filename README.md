Usage

Package includes a static 'Facade' class for ease of use, with the following methods (can be run from):
```
Users::all(); // returns array including users index with array of User DTO classes

Users::get(1); // returns User DTO of the user

Users::create(new User(null, 'Charles', 'Job')); // returns int id of the newly created user
```

To run unit tests:
```
composer install
./vendor/bin/pest
```
