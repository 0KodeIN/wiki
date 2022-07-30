<?php
for ($i = 0; $i < count($result); $i++) { ?>
    <div class="search-flex">
        <p class="art_name" onclick=getText(<? echo $result[$i]['id_art'] ?>)> <? echo $result[$i]['art_name'] ?> </p>
        <p class="search-p"><? echo $result[$i]['count_entry'] . " вхождений" ?> </p>
    </div>
<?php } ?>