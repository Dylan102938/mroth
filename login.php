<?php
    session_start();

    $pass = "Slaussen3946";
    $pass_err = "";

    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        header("Location: ./");
    }

    if (isset($_POST["submit"])) {
        $password = trim($_POST["password"]);
        if ($password === $pass) {
            $_SESSION["loggedin"] = true;
            header("Location: ./");
        }

        else {
            $pass_err = "Incorrect Password.";
        }
    }

?>

<!DOCTYPE html>
<html lang = "en">
    <head>
        <title>Login to </title>
        <link href = "main.css" rel = "stylesheet">

        <style type = "text/css">

            .container {
                width: 35%;
                margin: 60px auto;
            }

        </style>
    </head>

    <body>
        <div class = "container">
            <form method = "post" action = "login.php">
                <input type = "password" placeholder = "Enter Password" name = "password" id = "password" class = "form-control">
                <span class = "err-help" id = "pass-help"><?php echo $pass_err; ?></span>

                <input type = "submit" value = "Submit"  class = "form-control submit" name = "submit">
            </form>
        </div>

        <script type = "text/javascript">
            if (document.getElementById("pass-help").innerHTML !== "") {
                document.getElementById("password").classList.add("err");
            }
        </script>
    </body>
</html>
