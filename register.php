<?php
// Include config.php file
require_once "config.php";
 
// Variable definition and initialization with empty values
$email = $password = $confirm_password = $first_name = $last_name = "";
$email_err = $password_err = $confirm_password_err = $first_name_err = $last_name_err = "";
 
// Process registration form data when the user hits submit
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate email field
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email is already taken.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password field
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a Password.";     
    } elseif(strlen(trim($_POST["password"])) < 10){
        $password_err = "Password must have atleast 10 characters.";
    } elseif(!preg_match("#[0-9]+#",$_POST["password"])) {
        $password_err = "Password must contain atleast 1 Number.";
    } elseif(!preg_match("#[A-Z]+#",$_POST["password"])) {
        $password_err = "Password must have atleast 1 Capital Letter.";
    } elseif(!preg_match("#[a-z]+#",$_POST["password"])) {
        $password_err = "Password must have atleast 1 Lowercase Letter.";
    }else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password field
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm Password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }

    // Validate first_name field
    if(empty(trim($_POST["first_name"]))){
        $first_name_err = "Please enter your First Name.";     
    } else{
        $first_name = trim($_POST["first_name"]);
    }

    // Validate last_name field
    if(empty(trim($_POST["last_name"]))){
        $last_name_err = "Please enter your Last Name.";     
    } else{
        $last_name = trim($_POST["last_name"]);
    }
    
    // Check input errors before inserting in database
    if(empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($first_name_err) && empty($last_name_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (email, PASSWORD, first_name, last_name, current_balance) VALUES (?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssss", $param_email, $param_password, $param_first_name, $param_last_name, $param_current_balance);
            
            // Set parameters
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            $param_current_balance = 100;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Something went wrong. Please try again later.";
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
    <title>Sampler Sign Up</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@700&display=swap" rel="stylesheet">
</head>
</head>
<body>
<a href="#default" class="logo" style="align:center"><img class="login-img"  src="images/logo_sampler.png" /></a>
    <div class="login-box register-box-height">
        <h2>Create your account</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label style="color: #676767">Email</label><br>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block errorMessage"><?php echo $email_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label style="color: #676767">Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block errorMessage"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label style="color: #676767">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block errorMessage"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($first_name_err)) ? 'has-error' : ''; ?>">
                <label style="color: #676767">First Name</label>
                <input type="text" name="first_name" class="form-control" value="<?php echo $first_name; ?>">
                <span class="help-block errorMessage"><?php echo $first_name_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($last_name_err)) ? 'has-error' : ''; ?>">
                <label style="color: #676767">Last Name</label>
                <input type="text" name="last_name" class="form-control" value="<?php echo $last_name; ?>">
                <span class="help-block errorMessage"><?php echo $last_name_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="submit-button" value="Submit">
            </div>
            <p><a href="login.php" style="color:#50A1DB; margin-left:15%;">Already have an account? Login</a></p>
        </form>
    </div>    
</body>
</html>