<ul id="poems">
    <?php
        $_dir = __DIR__.'/poetry/';
        if ($handle = opendir(__DIR__.'/poetry/')) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    echo '<li>';
                        echo file_get_contents($_dir.$entry);
                    echo '</li>';
                }
            }
            closedir($handle);
        }
    ?>
</ul>
