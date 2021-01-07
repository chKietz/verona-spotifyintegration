    <table>
        <tr>
        <td>
            <i id="snav-left" style="" class="fas fa-chevron-left fa-2x snav-arrow" onClick="getNext(-1)"></i>
        </td><?php
    $i = 0;
    foreach ($resArr as $item){
    ?>
        <td id="carouselItem_<?php echo $i ?>" class="spotify-item" style="max-width: 30vw; min-height: 35vh; display: none;">
        <img class="albumImg" style="max-height:250px; max-width: 250px;" src="<?php echo $item['albumImg'] ?>"></img>
        <div class="trackTitle" style=""><?php echo $item['trackTitle'] ?></div>
        <div class="artistName" style=""><?php echo $item['artistName'] ?></div>
        <div class="albumTitle" style=""><?php echo $item['albumTitle'] ?></div>
        <?php if($item['songUrl'] != ""){ ?>
        <a class="songUrl" href="<?php echo $item['songUrl'] ?>" style="" target="_blank" rel="noopener noreferrer">auf Spotify hören</a>
        <?php } ?></td>
        
    <?php $i++;} ?>
        <td>
            <i id="snav-right" style="" class="fas fa-chevron-right fa-2x snav-arrow" onClick="getNext(1)"></i>
        </td>
        
</tr>
</table>