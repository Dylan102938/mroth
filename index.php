<?php
    session_start();

    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("Location: ./login.php");
    }

    $file_err = "";
    $data = [];

    if (isset($_POST["submit"]) || isset($_POST["run_now"])) {
        if ($_FILES["accounts"]["tmp_name"] !== "") {
            if ($_FILES["accounts"]["error"] > 0) {
                $file_err = "An Error Occurred.";

            }

            if (($handle = fopen("accounts.csv", 'r')) !== false) {
                while (($dataValue = fgetcsv($handle, 1000)) !== false) {
                    $data[] = $dataValue;
                }
            }
            fclose($handle);

            if (($handle2 = fopen($_FILES["accounts"]["tmp_name"], "r")) !== false) {
                $count = 0;
                while (($dataValue = fgetcsv($handle2, 1000)) !== false) {
                    if ($count != 0) {
                        $data[] = $dataValue;
                    }
                    $count = 1;
                }
            }
            fclose($handle2);

            $masterFile = fopen("accounts.csv", "w+");
            foreach ($data as $value) {
                try {
                    fputcsv($masterFile, $value, ",", "'");
                }

                catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
            fclose($masterFile);
        }

        else {
            $file_err = "Please Upload an Account.CSV File.";
        }
    }

    if (isset($_POST["run_now"])) {
        $cmd = "python3 scraper.py";
        $output = shell_exec($cmd);
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
                <label for = "accounts">Upload New Accounts As CSV</label>
                <input type = "file" class = "form-control" name = "accounts" id = "accounts" style = "width: 100%;" accept = ".csv">
                <span class = "err-help" id = "accounts-help"><?php echo $file_err; ?></span><br>

                <div style = "margin-top: 64px;"></div>

                <label for = "current_latest">Current Latest.CSV File</label>
                <div class = "form-control" name = "current_latest" id = "current_latest" style = "height: 400px; overflow: auto;">

                </div>

                <div class = "row" style = "width: 100%;">
                    <div class = "col half" style = "padding-right: 10px;">
                        <input type = "submit" class = "form-control submit" name = "submit">
                    </div>

                    <div class = "col half" style = "padding-left: 10px;">
                        <input type = "submit" class = "form-control submit" name = "run_now"
                               style = "background-image: -webkit-linear-gradient(45deg, #13f80a 0%, #57ad4a 100%);"
                               value = "Run Now">
                    </div>
                </div>
            </form>
        </div>

        <script
                src="https://code.jquery.com/jquery-3.5.0.min.js"
                integrity="sha256-xNzN2a4ltkB44Mc/Jz3pT4iU1cmeR0FkXs4pru/JxaQ="
                crossorigin="anonymous">
        </script>

        <script type = "text/javascript">
            if (document.getElementById("accounts-help").innerHTML !== "") {
                document.getElementById("accounts").classList.add("err");
            }

            $(document).ready(function() {
                jQuery.get('latest.csv?t=' + Math.floor(Date.now() / 1000), function(data) {
                    data = data.replace(/\n/g, "<br>");
                    $("#current_latest").html("<span>" + data + "</span>");
                });
            });
        </script>
    </body>
</html>
