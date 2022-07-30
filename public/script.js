copy.onclick = function(){
        document.getElementById("copy").disabled = true; 
        const text = document.getElementById('search_import').value; // получаем значение
        const request = new XMLHttpRequest();
        var Div = document.getElementById('content-table');
        Div.innerHTML = `<img class="load" src="img/load.gif" alt=""></img>`;        
        request.onreadystatechange = function(){
            if(this.readyState == 4){ // если нет ошибок, получаем ответ

                if(document.getElementById('content-table') != null){
                    while (Div.firstChild) {
                        Div.removeChild(Div.firstChild); //удаление таблицы
                    }
                }
                str = this.responseText;
                // обработка ошибок
                if(this.responseText.indexOf('error400', 0) == 0){
                    swal({
                        title: 'Ошибка. Статья не найдена',                
                    });
                    var x = 'error400';
                    var rExp = new RegExp(x, "g");
                    var str = (this.responseText.replace(rExp, ''));
                }
                if(this.responseText.indexOf('error500', 0) == 0){
                    swal({
                        title: 'Ошибка. Статья уже добавлена',                
                    });
                    x = 'error500';
                    rExp = new RegExp(x, "g");
                    str = (this.responseText.replace(rExp, ''));
                }

                Div.innerHTML = str;
                document.getElementById("copy").disabled = false;
                               
            }
        }
        var str = text + "&" + "0";
        request.open("POST", "Controllers/frontController.php", true); // подготовка к отправке
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); // устанавливаеи заголовок
        request.send(str); // отправляем данные на сервер
      
    
}

var textContainer = document.getElementsByClassName('search_text')[0];
search.onclick = function(){
    const text = document.getElementById('search_name').value;
    const request = new XMLHttpRequest();
    request.onreadystatechange = function(){
        if(this.readyState == 4){
            var data = this.responseText;
            if(data == ''){
                textContainer.innerHTML = '';
                textContainer.style.display = 'none';
                swal({
                    title: 'Ошибка. Слово не найдено', // Заголовок окна                
                }); 
            }
            console.log(this.responseText);
            //try { // проверяем полученный json
                searchWord(data);
            // } catch (error) {
            //     alert("Элемент не найден");
            // }

        }
    }
    var str = text + "&" + "2";
    request.open("POST", "Controllers/frontController.php", true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(str);
}

function requestFunction(data){ // добавляем ответ на страницу
    let Div = document.getElementById('content-text');
    Div.innerHTML = data;
    console.log(Div.innerHTML);
}
function getSearch(data){
    console.log(JSON.parse(data));
}

function getTable(){ // получение таблицы с сервера
    if(document.getElementById('content-table') != null){
        let Div = document.getElementById('content-table');
        while (Div.firstChild) {
            Div.removeChild(Div.firstChild); //удаление таблицы
        }
    }
    const request = new XMLHttpRequest();
    request.onreadystatechange = function(){
        if(this.readyState == 4){
            var table = this.responseText;
            setTable(table);
        }
    }
    var str = "none&1";
    request.open("POST", "Controllers/frontController.php", true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(str);
    
}
function setTable(table){ // вывод таблицы
    let Div = document.getElementById('content-table');
    Div.innerHTML = table; 
}
function searchWord(table){ // поиск слова
    console.log(table);
    let Div = document.getElementById('search-links');
    while (Div.firstChild) {
        Div.removeChild(Div.firstChild); //удаление таблицы
    }
    Div.innerHTML = table;
    // table.forEach(row => { //новая таблица
    //     var div =  document.createElement('div');
    //     div.className = "table-flex";
    //     div.innerHTML = "<p onclick = getText(" + row.id_article + ")>"
    //      + row.art_name + " количество вхождений(" + row.count + ") </p>";
    //     Div.append(div);
    // });
 
}

function getText(id){ // получение текста с сервера
    const request = new XMLHttpRequest();
    request.onreadystatechange = function(){
        if(this.readyState == 4){
            var text = this.responseText;
            if(this.responseText.indexOf('error500', 0) == 0){
                swal({
                    title: 'Ошибка. Статья не найдена',                
                });
                x = 'error500'; // убираем  текст ошибки
                rExp = new RegExp(x, "g");
                str = (this.responseText.replace(rExp, ''));
            }
            else{
                if(screenWidth <= 900){
                    // всплывающее окно с текстом статьи 
                    var popup = document.getElementById('popup');                   
                    popup.style.display = 'block';
                    popup.innerHTML = `<button class = "close" onclick = "closePopup()">Закрыть</button>`;
                    popup.innerHTML += text; 
                }
                else{
                    textContainer.style.display = 'block';
                    setText(text);
                }

            }            
        }
    }
    var str = id + "&" + "3";
    request.open("POST", "Controllers/frontController.php", true);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.send(str); 
}

function setText(text){ // вывод текста
    let Div = document.getElementById('search-text');
    while (Div.firstChild) {
        Div.removeChild(Div.firstChild); //удаление таблицы
    }
    Div.innerHTML = text;
}
function closePopup(){
    console.log('a');
    var popup = document.getElementById('popup');
    popup.style.display = 'none';
}

window.onload = getTable(); // вывод таблицы при загрузке страницы
const screenWidth = window.screen.width;


// плагин кастомного алерта
(function () {
    if (typeof window.CustomEvent === "function") return false;
    function CustomEvent(event, params) {
        params = params || { bubbles: false, cancelable: false, detail: null };
        var evt = document.createEvent('CustomEvent');
        evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
        return evt;
    }
    window.CustomEvent = CustomEvent;
})();


