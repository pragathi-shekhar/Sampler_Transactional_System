<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)
{
    header("location: profile.php");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$email = $password = $first_name = $current_balance = "";
$email_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST")
{

    // Check if email field is empty
    if (empty(trim($_POST["email"])))
    {
        $email_err = "Please enter your Email.";
    }
    else
    {
        $email = trim($_POST["email"]);
    }

    // Check if password field is empty
    if (empty(trim($_POST["password"])))
    {
        $password_err = "Please enter your Password.";
    }
    else
    {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($email_err) && empty($password_err))
    {
        // Prepare a select statement
        $sql = "SELECT id, email, password, first_name, current_balance, last_name FROM users WHERE email = ?";

        if ($stmt = mysqli_prepare($link, $sql))
        {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);

            // Setting parameters
            $param_email = $email;

            // Execute the prepared statement
            if (mysqli_stmt_execute($stmt))
            {
                // Storing result
                mysqli_stmt_store_result($stmt);

                // Check if email exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1)
                {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $email, $hashed_password, $first_name, $current_balance, $last_name);
                    if (mysqli_stmt_fetch($stmt))
                    {
                        if (password_verify($password, $hashed_password))
                        {
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["email"] = $email;
                            $_SESSION["first_name"] = $first_name;
                            $_SESSION["current_balance"] = $current_balance;
                            $_SESSION["emailReceiverInModal"] = "";
                            $_SESSION["last_name"] = $last_name;

                            // Redirect user to Profile page
                            header("location: profile.php");
                        }
                        else
                        {
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                }
                else
                {
                    // Display an error message if email doesn't exist
                    $email_err = "No account found with that email.";
                }
            }
            else
            {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sampler Login</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="login-body">
<a href="#default" class="logo" style="align:center"><img class= "login-img" src="images/logo_sampler.png" /></a>
    <div class="login-box login-box-height">
        <h2> Login</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label style="color: #676767">Email</label><br>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block errorMessage"><?php echo $email_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label style="color: #676767">Password</label><br>
                <input type="password" name="password" class="form-control"><br>
                <span class="help-block errorMessage"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="login-button" value="Login">
            </div>
            <p><a class="no-account-link" href="register.php">Don't have an account? Register</a></p>
        </form>
    </div>    
</body>
</html>
