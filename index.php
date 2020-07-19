<?php
    session_start();

    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("Location: ./login.php");
    }

    $file_err = $latest_err = "";

    if (isset($_POST["submit"])) {
        if ($_FILES["accounts"]["tmp_name"] !== "") {
            if ($_FILES["accounts"]["error"] > 0) {
                $file_err = "An Error Occurred.";

            }


            $file_name = "accounts.csv";

            if (file_exists($file_name)) {
                unlink($file_name);
            }

            move_uploaded_file($_FILES["accounts"]["tmp_name"], $file_name);

        }

        else {
            $file_err = "Please Upload an Account.CSV File.";
        }

        $file_name = "latest.csv";
        if ($_FILES["latest"]["tmp_name"] !== "") {
            if ($_FILES["latest"]["error"] > 0) {
                $latest_err = "An Error Occurred.";
            }

            else {
                $file_name = "latest.csv";

                if (file_exists($file_name)) {
                    unlink($file_name);
                }

                move_uploaded_file($_FILES["latest"]["tmp_name"], $file_name);
            }
        }

        else {
            copy("latest_blank.csv", $file_name);
        }

        $cmd = escapeshellcmd("python3 scraper.py");
        $output = shell_exec($cmd);
        echo $output;
    }
?>

<html>
    <head>
        <title>Upload BitCoin Accounts File</title>
        <link href = "main.css" rel = "stylesheet">
    </head>

    <body>
        <div class = "container">
            <form method = "post" action = "index.php" enctype= "multipart/form-data">
                <label for = "accounts">Upload Your Accounts.CSV File</label>
                <input type = "file" class = "form-control" name = "accounts" id = "accounts" style = "width: 100%;" accept = ".csv">
                <span class = "err-help" id = "accounts-help"><?php echo $file_err; ?></span><br>

                <div style = "margin-top: 64px;"></div>

                <label for = "latest">Upload Your Latest.CSV File (Optional)</label>
                <input type = "file" class = "form-control" name = "latest" id = "latest" style = "width: 100%;" accept = ".csv">
                <span class = "err-help" id = "latest-help"><?php echo $latest_err; ?></span>

                <input type = "submit" class = "form-control submit" name = "submit">
            </form>
        </div>

        <script type = "text/javascript">
            if (document.getElementById("accounts-help").innerHTML !== "") {
                document.getElementById("accounts").classList.add("err");
            }

            if (document.getElementById("latest-help").innerHTML !== "") {
                document.getElementById("latest").classList.add("err");
            }
        </script>
    </body>
</html>
