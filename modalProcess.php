<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
include 'config.php';
$amount = 0.0;
$id_receiver = $email_receiver = $first_name_receiver = $current_balance_receiver = $amount_err = "";
$email_currentUser = $_SESSION["email"];
$current_balance_currentUser = $_SESSION["current_balance"];
$first_name_currentUser = $_SESSION["first_name"];

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $emailReceiverInModal = isset($_POST['email_receiver']) ? $_POST['email_receiver'] : null;
    $cookie_name_receiver_email = "receiver_email";
    $cookie_value_receiver_email = $emailReceiverInModal;
    setcookie($cookie_name_receiver_email, $cookie_value_receiver_email);
    
    $balanceReceiverInModal = isset($_POST['balance_receiver']) ? $_POST['balance_receiver'] : null;
    $cookie_name_receiver_balance = "balance_receiver";
    $cookie_value_receiver_balance = $balanceReceiverInModal;
    setcookie($cookie_name_receiver_balance, $cookie_value_receiver_balance);
    $firstNameReceiverInModal = isset($_POST['first_name_receiver']) ? $_POST['first_name_receiver'] : null;
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate new amount
    if(empty(trim($_POST["amount"]))){
        $amount_err = "Please enter a valid amount in numbers";     
    } elseif(strlen(trim($_POST["amount"])) < 0){
        $amount_err = "Amount must be greater than $0";
    } else{
        $amount = trim($_POST["amount"]);
    }
    
    $double_balance = 0.0;
    $double_amount = (double) $amount;

    if(!isset($_COOKIE[$cookie_name_receiver_email])) {
        echo "Cookie named '" . $cookie_name_receiver_email . "' is not set!";
    } else {
        echo "Cookie '" . $cookie_name_receiver_email . "' is set!<br>";
        echo "Value is: " . $_COOKIE[$cookie_name_receiver_email];
    } 

    if(!isset($_COOKIE[$cookie_name_receiver_balance])) {
        echo "Cookie named '" . $cookie_name_receiver_balance . "' is not set!";
    } else {
        echo "Cookie '" . $cookie_name_receiver_balance . "' is set!<br>";
        echo "Value is: " . $_COOKIE[$cookie_name_receiver_balance];
        $double_balance = (double) $_COOKIE[$cookie_name_receiver_balance];
    }

    $final_current_balance_receiver = $double_amount + $double_balance;

    // Below stub of code sets the new decreased value of the current user after paying the amount to respective user
    $double_current_balance = (double) $_SESSION["current_balance"];
    $reduced_current_user_balance = $double_current_balance - $double_amount;
    $_SESSION["current_balance"] = $reduced_current_user_balance;

    // Check input errors before updating the database
    if(empty($amount_err)){
        // Prepare an update statement
        $sql = "UPDATE users SET current_balance = ? WHERE email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){

            // Set parameters
            $param_current_balance_receiver = $final_current_balance_receiver;
            $param_email_receiver = $_COOKIE[$cookie_name_receiver_email];

            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "si", $param_current_balance_receiver, $param_email_receiver);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                //session_destroy();
                $amount = 0.0;
                header("location: profile.php");
                //exit();
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