<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect user to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values of the receiver $current_balance_receiver
$email_receiver = $first_name_receiver = $current_balance_receiver = $amount = $amount_err = "";
$final_current_balance_receiver = 0.0;
$balance_receiver = $first_name_receiver = "";
$email_currentUser = $_SESSION["email"];
$current_balance_currentUser = $_SESSION["current_balance"];
$first_name_currentUser = $_SESSION["first_name"];
$last_name_currentUser = $_SESSION["last_name"];
?>
	<!DOCTYPE html>
	<html>

	<head>
		<link rel="stylesheet" href="css/styles.css">
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@700&display=swap" rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

	</head>

	<body class="profile-body">
		<div class="header">
			<a href="#default" class="profile-logo"><img src="images/logo_sampler_white.png" /></a>
			<div class="header-right"> <a class="tablink" id="activatedTab" href="#home" onclick="openTab('Home', this, '#5FC1B9')"><span class="child">Home</span></a> <a class="tablink" href="#Profile" onclick="openTab('Profile', this, '#5FC1B9')"><span class="child">Profile</span></a> <a class="tablink"  href="logout.php"><span class="child">Logout</span></a> </div>
		</div>
		<div id="Home" class="tabcontent">
			<div id="myBalanceDiv" class="my-balance-main-div">
				<div class="my-balance">My Balance</div>
				<h1 class="profile-h1"><?php echo "$".$current_balance_currentUser?></h1>
			</div>
			<h3 class="usersHeader">Users</h3>
			<div class="tableHeader">
				<table style="width:100%" class="tableone">
					<thead>
						<tr>
							<th style="color:#818181" class="tableone cell-width">Name</th>
							<th style="color:#818181" class="tableone">Balance</th>
						</tr>
					</thead>
					<tbody>
						<?php
							// Iterate the database table to render all the rows
							$selectquery = "select * from users";
							$query = mysqli_query($link, $selectquery);
							while($result = mysqli_fetch_array($query)){
						?>
							<tr id="<?php echo $result['email'];?>" onclick="openModal(this,'<?php echo $result['first_name']; ?>', '<?php echo $result['email']; ?>', '<?php echo $result['current_balance'] ?>')">
								<td style="color:#6E6E6E" class="tableone">
									<?php echo $result['first_name']." ".$result['last_name']?>
								</td>
								<td style="color:#6E6E6E" class="tableone">
									<?php echo "$".$result['current_balance']?>
								</td>
							</tr>
							<?php
							}
							?>

					</tbody>
				</table>
			</div>
			<div id="modalId" class="modal"> <span onclick="document.getElementById('modalId').style.display='none'" title="Close Modal">×</span>
            <form class="modal-content" action="/Sampler_Transactional_System/modalProcess.php" method="post">
					<div class="container">
					<a href="#default" class="logo" style="align:center"><img src="images/icon_dollar.png" /></a>
						<p class = amount-send-text id="userModalHeader"></p>
						
                            <?php
				// Processing form data when form is submitted - this code has been moved to modalProcess.php
                            ?>
							<div <?php echo (!empty($amount_err)) ? 'has-error' : ''; ?>">
								<label class=amount><br>Amount</label><br>
								<div class="input-symbol"><input type="text" name="amount" class="profile-form-control" value="<?php echo $amount; ?>"></div><br>
								<span class="help-block"><?php echo $amount_err; ?></span><br>
                            </div>
							<div>
								<div class="clearfix" style="display: inline-flex">
									<button type="button" onclick="document.getElementById('modalId').style.display='none'" class="modal-button">Cancel</button>
									<input type="submit" class="modal-button send-button" value="Send">
								</div>
							</div>
					</div>
				</form>
			</div>
			<script>
			// Get the modal 
			var modal = document.getElementById('modalId');
			// When the user clicks anywhere outside of the modal, close it
			window.onclick = function(event) {
				if(event.target == modal) {
					modal.style.display = "none";
				}
			}
			function openModal(element, idFirstName, idEmail, idBalance) {
				console.log("Entering onclick");
				document.getElementById('modalId').style.display = 'block';
				document.getElementById('userModalHeader').innerHTML = "Send money to " + idFirstName;
				var emailIDFromJavascript = idEmail;
                $.ajax({
                    type: "POST",
                    url: "/Sampler_Transactional_System/modalProcess.php",
                    dataType: "json",
                    data: {
                        email_receiver: emailIDFromJavascript,
                        balance_receiver: idBalance,
                        first_name_receiver: idFirstName,
                        submit: 'submit',
                    },
                    success: function(res) {
                        var response = res;
                        var row = response.data;
                        if (response.status == "success") {
                            console.log(response);
                        } else {
                        alert(response.msg);
                        }
                    }
                });
			}
			</script>
		</div>
		<div id="Profile" class="tabcontent">
		<div id="myBalanceDiv" class="my-balance-main-div">
				<div class="my-profile">My Profile</div>
			</div>
			<table style="margin-top:40px;margin-left:170px;border:none">
				<tr class="profile-th">
					<th>First Name</th>
					<th>Email</th>
				</tr>
				<tr class="profile-td">
					<td><?php echo $first_name_currentUser ?></td>
					<td><?php echo $email_currentUser ?></td>
				</tr>
				<tr style="padding-top:30px">
					<th style="padding-top:30px"></th>
					<th style="padding-top:30px"></th>
				</tr>
				<tr class="profile-th">
					<th>Last Name</th>
					<th>Password</th>
				</tr>
				<tr class="profile-td">
					<td><?php echo $last_name_currentUser ?></td>
					<td>●●●●●●●●●●●</td>
				</tr>
			</table>
		</div>
		<div id="Logout" class="tabcontent">
			<p> <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a> </p>
			
		</div>
		<script>
		function openTab(tabName, element, color) {
			var i, tabcontent, tablinks;
			tabcontent = document.getElementsByClassName("tabcontent");
			for(i = 0; i < tabcontent.length; i++) {
				tabcontent[i].style.display = "none";
			}
			tablinks = document.getElementsByClassName("tablink");
			for(i = 0; i < tablinks.length; i++) {
				tablinks[i].style.backgroundColor = "";
				tablinks[i].style.color = "";
				tablinks[i].getElementsByClassName('child')[0].style.borderBottom = "0px solid white";
			}
			document.getElementById(tabName).style.display = "block";
			element.style.backgroundColor = color;
			element.style.color = "#F1FBFB";
			element.getElementsByClassName('child')[0].style.borderBottom = "1px solid white";
		}
		// Fetch the element with id "activatedTab" and perform click opertion on it
		document.getElementById("activatedTab").click();
		</script>
	</body>
	</html>