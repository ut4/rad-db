<?php

require __DIR__ . '/vendor/autoload.php';

use Rad\Db\RepositoryFactory;
use Demo\TodoRepository;

$pdo = new PDO('sqlite:../test.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/*
$pdo->query('CREATE TABLE todoItem (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    description TEXT,
    due DATE
)');
$pdo->query('CREATE TABLE checklistItem (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    description TEXT,
    todoItemId INTEGER,
    FOREIGN KEY (todoItemId) REFERENCES todoItem(id)
)');
die();
*/

$repoFactory = new RepositoryFactory();
$repoFactory->usePdo($pdo);
$todoRepository = $repoFactory->createAutoRepository(TodoRepository::class);

$path = filter_input(INPUT_GET, 'path') ?? '';
$id = filter_input(INPUT_GET, 'id') ?? '';
$data = json_decode(file_get_contents('php://input'), true);

switch ($path) {
    case 'insert' :
        echo json_encode($todoRepository->insert($data));
        break;
    case 'list' :
        echo json_encode($todoRepository->selectAll());
        break;
    case 'find-all' :
        echo json_encode($todoRepository->findAll(function ($q) use ($id) {
            $q->where('t.id = :idPlaceholder');
            $q->bindValue('idPlaceholder', $id);
        }));
        break;
    case 'find-one' :
        echo json_encode($todoRepository->findOne(function ($q) use ($id) {
            $q->where('t.id = :idPlaceholder');
            $q->bindValue('idPlaceholder', $id);
        }));
        break;
    case 'update' :
        echo json_encode($todoRepository->update($data));
        break;
    case 'delete' :
        echo json_encode($todoRepository->delete($data));
        break;
    default :
        echo '404';
        break;
}
