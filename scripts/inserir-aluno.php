<?php

use Alura\Pdo\Domain\Model\Student;

require_once 'vendor/autoload.php';

$pdo = ConnectionCreator::createConnection();


$student = new Student(null, 'Pedro', new \DateTimeImmutable('2001-08-14'));

$sqlInsert = "INSERT INTO students (name, birth_date) VALUES(:name, :birth_date);";
$statement = $pdo->prepare($sqlInsert);
$statement->bindValue(':name', $student->name());
$statement->bindValue('birth_date', $student->birthDate()->format('Y-m-d'));

if ($statement->execute()) {
  echo 'Aluno incluído';
}
