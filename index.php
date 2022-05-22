<?php
require_once "vendor/autoload.php";

header("Content-Type: application/json");

$users = \Pexess\ORM\Database::from("users");
$posts = \Pexess\ORM\Database::from("posts");

$selectBuilder = new \Pexess\ORM\Queries\SelectQuery();

$insertBuilder = new \Pexess\ORM\Queries\InsertQuery();

$selectBuilder->from("posts")->select("id,title")->where([
])->orderBy('id', 'desc')->take(2);

$insertBuilder->into('posts')->insert(['title' => 'My title', 'content' => 'My content', 'author_id' => 1]);

$selectQuery = $selectBuilder->getQuery();
$selectBindings = $selectBuilder->getBindings();

$insertQuery = $insertBuilder->getQuery();
$insertBindings = $insertBuilder->getBindings();

$db = \Pexess\ORM\Database::instance();

$deleteBuilder = new \Pexess\ORM\Queries\DeleteQuery();

$deleteBuilder->from("posts")->where([
    "title" => "title"
]);

$db->query($selectQuery);
$db->execute($selectBindings);

$selectResult = $db->result();

$db->query($insertQuery);
$db->execute($insertBindings);

$insertResult = $db->result();

$posts = \Pexess\ORM\Database::from('posts')->findMany();

exit(json_encode([
    "select query" => $selectQuery,
    "select bindings" => $selectBindings,
    "insert query" => $insertQuery,
    "insert bindings" => $insertBindings,
    "select result" => $selectResult,
    "insert result" => $insertResult,
    "posts" => $posts,
    "delete query" => $deleteBuilder->getQuery(),
    "delete b" => $deleteBuilder->getBindings(),
]));