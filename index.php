<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WIki</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/css/bootstrap.min.css" integrity="sha512-T584yQ/tdRR5QwOpfvDfVQUidzfgc2339Lc8uBDtcp/wYu80d7jwBgAxbyMh0a9YM9F8N3tdErpFI8iaGx6x5g==" crossorigin="anonymous" referrerpolicy="no-referrer">
  <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script defer src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/js/bootstrap.min.js" integrity="sha512-UR25UO94eTnCVwjbXozyeVd6ZqpaAE9naiEUBK/A+QDbfSTQFhPGj5lOR6d8tsgbBk84Ggb5A3EkjsOgPRPcKA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <link rel="stylesheet" href="public/style.css">
</head>

<body>
  <header>
    <div class="navig">
      <h1 class="head-text" id="a1">Wiki</h1>
    </div>
  </header>
  <div class="tabs">
    <input type="radio" name="tab-btn" id="tab-btn-1" value="" checked>
    <label for="tab-btn-1" class="label1">Импорт статей</label>
    <input type="radio" name="tab-btn" id="tab-btn-2" value="">
    <label for="tab-btn-2" class="label2">Поиск</label>
    <div id="content-1">
      <input type="text" placeholder="Ключевое слово " id="search_import" name="name">
      <input type="submit" value="Скопировать" id="copy" name="sub">
      <div class="content-text" id="content-text">
      </div>
      <div class="content-table" id="content-table"></div>
    </div>
    <div id="content-2">
      <div id="content-1">
        <input type="text" placeholder="Ключевое слово " id="search_name" name="name">
        <input type="submit" value="Поиск" id="search">
        <div class="result" id="result">
          <div id="search-links"></div>
          <div id="search-text" class="search_text"></div>
        </div>
      </div>
    </div>
  </div>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script src="public/script.js"></script>
  <div class="form-popup" id="popup">
    <form action="/action_page.php" class="form-container">
      <h1>Логин</h1>

      <label for="email"><b>Почта</b></label>
      <input type="text" placeholder="Введите Почту" name="email" required>

      <label for="psw"><b>Пароль</b></label>
      <input type="password" placeholder="Введите Пароль" name="psw" required>

      <button type="submit" class="btn">Логин</button>
      <button type="submit" class="btn cancel" onclick="closeForm()">Закрыть</button>
    </form>
  </div>

</body>

</html>