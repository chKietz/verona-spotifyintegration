
<div id="spot-plug-wrapper">
    <div id="img-wrapper" style="display: none;" onclick="update()">
        <img src='<?php echo IMGURL?>' style="max-height: 40px; max-width: 40px;">
    </div>
    <div id="spot-content-wrapper" style="display: none;">
        <div id="close">X</div>
        <div id="spot-menu">
            <div class="spot-ba-content" id="tops_anzeige">UKV-Top Songs</div>
            Von UKV-Verona Nutzern - für UKV-Verona Nutzern.
            Eine Mischung aus den Top drei Songs der Mitarbeiter*innen aus dem vergangenen Monat.
            <div class="spot-ba-content" style="margin-top: 10px;" id="spotify_login">Bei Spotify einloggen</div>
            Loggen Sie sich bei Spotify ein, um Ihre Ihr Songs in die UKV-Top Songs Liste einzutragen.
        </div>
        <div id="spot-log-wrapper" style="display: none;">
            <div style="font-weight: 400;"> Durch einen Login bei Spotify können Sie Ihre Top 3 des letzten Monats in die UKV-Top Songs einlesen.</div><br>    
            <a style="font-weight: 600;" href="#" onclick="login()" >Log In</a>
        <a id="infobox" title="Es werden ausschließlich Ihre meistgehörten Songs des vergangenen Monats gespeichert und verarbeitet.">i</a>
        </div>
    <div id="spot-tops" style="display: none;">
    <img id="spotify_loader" style="height:30px; width: 30px;" src="<?php echo LOADER ?>"></img>
    
    </div>
    </div>
</div>
