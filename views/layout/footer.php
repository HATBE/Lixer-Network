<?php
    $date = date('Y');
    if($date > $config['since']) {
        $copyDate = $config['since'].' - ' . $date;
    } else {
        $copyDate = $date;
    }
    if(isset($_SESSION['login'])) {
        $login =  '<a class="nolink" href="/users/logout">Ausloggen</a>';
    } else {
        $login = '<a class="nolink" href="/users/login">Anmelden</a>';
    }
?>

    <footer>
        <div class="pageframe">
            <span class="smalltext">v<?php echo $config['version'];?> &copy;<?php echo $copyDate;?> <?php echo $config['author'];?></span>
            <span><a class="nolink" href="/index/impressum">Impressum</a> | <a class="nolink" href="/index/impressum">Datenschutz</a> | <?php echo $login;?>
        
        </div>
    </footer>

</body>
</html>