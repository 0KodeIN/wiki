<?php 
for($i = 0; $i < count($result); $i++){ ?>
<div class="table-flex">
    <p class="main_name"><? echo $result[$i]["art_name"] ?></p>
    <a class="main_link" href=<? echo $result[$i]["link"] ?>><? echo $result[$i]["link"] ?></a>
    <p class="main_size"><? echo $result[$i]["size"] . "Кб" ?></p>
    <p class="main_count"><? echo $result[$i]["count_words"] . " слов" ?></p>  
</div>
<?php } ?>