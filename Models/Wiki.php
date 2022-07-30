<?php
/*
    Модель Wiki для реализации логики приложения
*/
require_once '../simple_html_dom.php';
require_once '../Configuration/config.php';


class Wiki
{
    private $dbh;   // подключение к БД
    public $result_array = array(); // Массив результата выполнения функций

    public function __construct()
    {
    }

    public function postContent($searchPage)
    {
        // Подключение к БД
        $this->dbh = new PDO(DATABASE_DSN, DATABASE_USERNAME, DATABASE_PASSWORD);
        // Поиск статьи. Если нашли статью, прекращаем работу скрипта.
        $search_sql = "SELECT * FROM articles where art_name = '$searchPage'";
        $sth = $this->dbh->prepare($search_sql);
        $sth->execute();

        if ($sth->rowCount() != 0) {
            return 'isset';   // Вывод ответа в контроллер
            exit();
        }

        if (preg_match("/[А-Яа-я]/", $searchPage)) { // Меняем ссылку в зависимости от $searchPage  
            $endPoint  =  "https://ru.wikipedia.org/w/api.php"; // Для кириллицы
            $parse_url = "https://ru.wikipedia.org/wiki/";
        } else {
            $endPoint  =  "https://en.wikipedia.org/w/api.php"; // Для латиницы
            $parse_url = "https://en.wikipedia.org/wiki/";
        }

        $params  =  [  // Параметры поиска для WikiApi
            "action"  =>  "query",
            "list"  =>  "search",
            "srsearch"  =>  $searchPage,
            "srlimit"   => 1,
            "format"  =>  "json"
        ];

        $url = $endPoint . "?" . http_build_query($params); // поиск статьи
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($output, true);

        // если статья не была найдена, завершаем работу скрипта
        if ($result['query']['search'][0]['title'] == 0) {
            return '';
            exit();
        } else {
            $title_search = $result['query']['search'][0]['title'];
        }

        // Убираем пробелы, если название статьи состоит из нескольких слов
        $title_search = preg_replace('~ ~', '_', $title_search);
        $parse_url = $parse_url . $title_search;
        $html = file_get_html($parse_url); // отображаем страницу в html
        // получаем голый текст по селектору класса 
        $html =  $html->find('.vector-body', 0)->plaintext;
        $html = preg_replace('~:~', ' ', $html);

        $atom_words = explode(" ", $html); // массив слов из статьи
        for ($i = 0; $i < count($atom_words); $i++) {
            $atom_words[$i] = preg_replace('/[^ a-zа-яё\d]/ui', '', $atom_words[$i]);
            $atom_words[$i] = mb_strtolower($atom_words[$i]);
        }
        $atom_words = array_count_values($atom_words); // считаем кол-во вхождений

        // Формируем данные для запроса 
        $size = round($result['query']['search'][0]['size'] / 1000);
        $this->result_array['link'] = $parse_url;
        $this->result_array['wordcount'] = $result['query']['search'][0]['wordcount'];
        $this->result_array['size'] = $size;

        // Добавление записи в таблицу 'articles'
        $sql = "INSERT INTO articles (id_art, art_name, art_text, link, count_words, size) 
                VALUES (?,?,?,?,?,?)";
        $sth = $this->dbh->prepare($sql);
        $sth->execute(array(
            $result['query']['search'][0]['pageid'],
            $searchPage,
            $html,
            $parse_url,
            $result['query']['search'][0]['wordcount'],
            $size
        ));


        // INSERT запросы для таблиц 'words' и 'entry'
        $insert_words = "INSERT INTO words (word) VALUES (?)";
        $insert_entry = "INSERT INTO entry (id_art, id_word, count_entry) VALUES (?,?,?)";

        /*
            Транзакция для ускоренного множественного добавления в таблицу 'words'
        */
        $this->dbh->beginTransaction();
        foreach ($atom_words as $word => $word_count) {
            // Проверка наличия слова в таблице
            $words_sql = "SELECT * FROM words where word = '$word'";
            $sth = $this->dbh->prepare($words_sql);
            $sth->execute();
            $word_valid = $sth->fetchAll();
            $word = preg_replace('/\s+/', '', $word);
            if ($sth->rowCount() == 0) {
                if ($word != '') {
                    // Добавление записи в таблицу `words`
                    $sth = $this->dbh->prepare($insert_words);
                    $sth->execute(array(
                        $word
                    ));
                }
            }
        }
        $this->dbh->commit();

        /*
            Транзакция для ускоренного множественного добавления в таблицу 'entry'
        */
        $this->dbh->beginTransaction();
        foreach ($atom_words as $word => $word_count) {

            // поиск id ранее добавленного слова
            $words_sql = "SELECT * FROM words where word = '$word'";
            $sth = $this->dbh->prepare($words_sql);
            $sth->execute();
            $word = preg_replace('/\s+/', '', $word);

            if ($sth->rowCount() != 0) {
                $entry = $sth->fetchAll();
                if ($word != '') {
                    $sth = $this->dbh->prepare($insert_entry);
                    $sth->execute(array(
                        $result['query']['search'][0]['pageid'],
                        $entry[0]['id_word'],
                        $word_count
                    ));
                }
            }
        }
        $this->dbh->commit();

        return $this->result_array;
    }
    public function getContent() // вывод таблицы `articles`
    {
        try {

            $this->dbh = new PDO(DATABASE_DSN, DATABASE_USERNAME, DATABASE_PASSWORD);
            $sth = $this->dbh->prepare("SELECT * from articles ORDER BY `art_name` DESC");
            $sth->execute();
            $result = $sth->fetchAll();
            return  $result;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    public function searchWord($word) // Поиск слова в бд. Обработка ошибки на стороне клиента
    {
        try {
            $this->dbh = new PDO(DATABASE_DSN, DATABASE_USERNAME, DATABASE_PASSWORD);

            $sql = "SELECT entry.count_entry, articles.id_art, words.word, articles.art_text, articles.art_name 
            FROM `entry`
            JOIN articles ON articles.id_art = entry.id_art
            JOIN words ON words.id_word = entry.id_word
            WHERE words.word = '$word'
            ORDER BY entry.count_entry DESC";

            $sth = $this->dbh->prepare($sql);
            $sth->execute();
            $result = $sth->fetchAll();

            return  $result;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    public function textId($id) // метод отображения текста по клику на название статьи
    {
        try {
            $this->dbh = new PDO(DATABASE_DSN, DATABASE_USERNAME, DATABASE_PASSWORD);
            $sql = "SELECT art_text FROM articles WHERE id_art ='$id'";
            $sth = $this->dbh->prepare($sql);
            $sth->execute();

            // обработка ошибки на случай, если кто-то влезет в html код
            if ($sth->rowCount() == 0) {
                return 'Ошибка';
            } else {
                $result = $sth->fetchAll();
                return  $result;
            }
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
}
