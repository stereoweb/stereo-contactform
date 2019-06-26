<?php foreach($infos as $k => $v): ?>
    <strong><?=$k;?>: </strong><?=is_array($v)?implode('<br>',$v):nl2br($v);?><br>
<?php endforeach;?>
