<?php

require_once 'Controller.php';
require_once '../Models/Wiki.php';

$post = array();
if (isset($_POST)) { // Получение данных с клиента
    foreach ($_POST as $key => $value) {
        array_push($post, $key);
    }
}

$page = $post[0]; // Отправленное слово
$case = $post[1]; // Переменная для работы с контроллером

$controller = new WikiController(); // создаем экземпляр класса
switch ($case) {
    case 0:
        $controller->searchPosts($page); // поиск статьи
        break;
    case 1:
        $controller->add(); // вывод статей
        break;
    case 2:
        $controller->search($page); // поиск слова
        break;
    case 3:
        $controller->getText($page); // вывод статьи по клику
        break;
}
