<?php
/*
    Контроллер для обработки входных и выходных данных
*/
require_once '../Models/Wiki.php';

class WikiController extends Wiki
{

    public function searchPosts($page)
    {
        $page = quotemeta($page); // удаление лишних символов 
        $result = $this->postContent($page); // метод родительского класса для добавления статьи

        if ($result == '') { // проверка ответа
            print_r('error400');
        } elseif ($result == 'isset') {
            print_r('error500');
        } else {
            require '../Views/request.php'; // Подключение представления
        }

        $result = $this->getContent(); // метод родительского класса для вывода таблиц
        require '../Views/main.php';
    }
    public function add() // вывод таблиц при загрузке страницы
    {
        $result = $this->getContent();
        require '../Views/main.php';
    }
    public function search($word)
    {
        $word = quotemeta($word);
        $result = $this->searchWord($word); // метод родительского класса для поиска слова
        require '../Views/search.php';
    }
    public function getText($id)
    {
        $id = quotemeta($id);
        $text = $this->textId($id); // метод родительского класса для отображения текста статьи
        if ($text == 'Ошибка') {
            print_r('error500');
        }
        require '../Views/search-text.php';
    }
}
