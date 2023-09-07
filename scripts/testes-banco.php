<?php

use Pdo\Infrastructure\Repository\PdoStudentRepository;

$banco = new PdoStudentRepository();

$listStudents = $banco->allStudents();

var_dump($listStudents);
