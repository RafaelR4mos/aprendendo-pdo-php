<?php
require_once 'vendor/autoload.php';

use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Infrastructure\Persistence\ConnectionCreator;
use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;


$connection = ConnectionCreator::createConnection();
$studentRepository = new PdoStudentRepository($connection);

try {
  $connection->beginTransaction();
  $aStudent = new Student(
    null,
    'Nico Steppat',
    new DateTimeImmutable('1985-05-01')
  );

  $studentRepository->save($aStudent);

  $anotherStudent = new Student(
    null,
    'Sergio Steppat',
    new DateTimeImmutable('2000-01-01')
  );

  $studentRepository->save($anotherStudent);

  $connection->commit();
} catch (\PDOException $err) {
  echo $err->getMessage();
  $connection->rollBack();
}

//Salva e executa os comandos.
