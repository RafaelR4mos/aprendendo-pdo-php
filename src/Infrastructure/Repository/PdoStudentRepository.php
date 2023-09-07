<?php

namespace Alura\Pdo\Infrastructure\Repository;

use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Domain\Model\Phone;

use Alura\Pdo\Domain\Repository\StudentRepository;
use PDO;

class PdoStudentRepository implements StudentRepository
{
  private PDO $connection;

  public function __construct(PDO $connection)
  {
    $this->connection = $connection;
  }

  public function allStudents(): array
  {
    $statement = $this->connection->query('SELECT * FROM students;');
    return $this->hydrateStudentList($statement);
  }

  public function studentsBirthAt(\DateTimeInterface $birthDate): array
  {
    $statement = $this->connection->prepare('SELECT name FROM students WHERE birth_date = :birthDate;');
    $statement->bindValue(':birthDate', $birthDate->format('Y-m-d'));
    $statement->execute();

    return $this->hydrateStudentList($statement);
  }

  public function hydrateStudentList(\PDOStatement $stmt): array
  {
    $studentDataList = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $studentList = [];

    foreach ($studentDataList as $studentData) {
      $studentList[] = new Student(
        $studentData['id'],
        $studentData['name'],
        new \DateTimeImmutable($studentData['birth_date'])
      );
    }

    return $studentList;
  }

  // public function fillPhonesOf(Student $student): void
  // {
  //   $sqlQuery = 'SELECT id, area_code, number FROM phones WHERE student_id = :id;';
  //   $stmt = $this->connection->prepare($sqlQuery);
  //   $stmt->bindValue(":id", $student->id(), PDO::PARAM_INT);
  //   $stmt->execute();

  //   $phoneDataList = $stmt->fetchAll();
  //   foreach ($phoneDataList as $phoneData) {
  //     $phone = new Phone(
  //       $phoneData['id'],
  //       $phoneData['area_code'],
  //       $phoneData['number']
  //     );

  //     $student->addPhone($phone);
  //   }
  // }

  public function save(Student $student): void
  {
    if ($student->id() === null) {
      $this->insert($student);
    }

    $this->update($student);
  }

  public function insert(Student $student): bool
  {
    $preparedStatement = $this->connection->prepare('INSERT INTO students (name, birth_date)  VALUES(:name, :birthDate);');
    $preparedStatement->bindValue(':name', $student->name());
    $preparedStatement->bindValue(':birthDate', $student->birthDate()->format('Y-m-d'));

    $success = $preparedStatement->execute();

    if ($success) {
      $student->defineId($this->connection->lastInsertId());
    }

    return $success;
  }

  public function update(Student $student): bool
  {
    $updateQuery = 'UPDATE students set name = :name, birth_date = :birthDate WHERE id = :id;';
    $preparedStatement = $this->connection->prepare($updateQuery);
    $preparedStatement->bindValue(':name', $student->name());
    $preparedStatement->bindValue(':birthDate', $student->birthDate()->format('Y-m-d'));
    $preparedStatement->bindValue(':id', $student->id(), PDO::PARAM_INT);

    return $preparedStatement->execute();
  }

  public function remove(Student $student): bool
  {
    $preparedStatement = $this->connection->prepare('DELETE FROM students WHERE id = :id;');
    $preparedStatement->bindValue(':id', $student->id(), PDO::PARAM_INT);
    return $preparedStatement->execute();
  }

  public function studentsWithPhones(): array
  {
    $sqlQuery = ' SELECT students.id, students.name, students.birth_date, 
                         phones.id AS phone_id, phones.area_code, phones.number
                  FROM students
                  JOIN phones ON students.id = phones.student_id;
    ';

    $stmt = $this->connection->query($sqlQuery);
    $result = $stmt->fetchAll();
    $studentList = [];

    foreach ($result as $row) {
      if (!array_key_exists($row['id'], $studentList)) {
        $studentList[$row['id']] = new Student(
          $row['id'],
          $row['name'],
          new \DateTimeImmutable($row['birth_date'])
        );
      }

      $phone = new Phone($row['phone_id'], $row['area_code'], $row['number']);
      $studentList[$row['id']]->addPhone($phone);
    }

    return $studentList;
  }
}
