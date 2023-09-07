<?php
require_once 'vendor/autoload.php';

use Alura\Pdo\Infrastructure\Persistence\ConnectionCreator;

$connection = ConnectionCreator::createConnection();

echo 'Conectei!';

$connection->exec("INSERT INTO phones (area_code, number, student_id) VALUES('51', '999998888', 1);");
exit();

$createTableSql = '
  CREATE TABLE IF NOT EXISTS students (
    id INTEGER PRIMARY KEY,
    name TEXT,
    birth_date TEXT
  );

  CREATE TABLE IF NOT EXISTS phones (
    id INTEGER PRIMARY KEY,
    area_code TEXT,
    number TEXT,
    student_id INTEGER,
    FOREIGN KEY(student_id) REFERENCES students(id)
  );
';

$connection->exec($createTableSql);
