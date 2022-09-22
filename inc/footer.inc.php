<div class="back-top"></div>
<!-- voltar ao topo -->

<footer class="footer" style="font-size: 11px;">
    <center>
        <div style="display: table-cell;">
            <p>
                <?php
                $version = "v1.001.20200912 - Setembro/2020";

                $changeLog = __DIR__ . "/../CHANGELOG.md";

                if (file_exists($changeLog)) {
                    $line = fgets(fopen($changeLog, 'r'));
                    $newVersion = explode("###", $line);
                    if (sizeof($newVersion) > 1) {
                        $version = trim(end($newVersion));
                    }
                }

                echo $version;
                ?></p>
        </div>
    </center>
</footer>
