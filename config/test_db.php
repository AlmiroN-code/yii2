<?php

$db = require __DIR__ . '/db.php';
// test database! Important not to run tests on production or development databases
// Using main database for development - create separate test db for production
$db['dsn'] = 'mysql:host=localhost;dbname=yii2basic';

return $db;
