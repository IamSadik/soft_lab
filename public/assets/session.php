<?php  
    require_once("dbconnect.php");
    session_start();

    $logged = false;

    // Check if anyone is logged in
    if (isset($_SESSION['logged']) && $_SESSION['logged'] == true) {
        $logged = true;
        $user_email = $_SESSION['email'];
        $user_type = $_SESSION['user_type'];
    } else {
        $logged = false;
    }

    // Process login if not already logged in
    if ($logged != true) {
        $user_email = "";
        
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $user_email = $_POST['email'];
            $password = $_POST['password'];

            // Sanitize inputs
            $user_email = stripslashes($user_email);
            $user_email = mysqli_real_escape_string($con, $user_email);
            $password = stripslashes($password);
            $password = mysqli_real_escape_string($con, $password);

            // Define a function to handle session logging
            function logSession($con, $user_id, $user_type) {
                $stmt = $con->prepare("INSERT INTO Session (user_id, user_type) VALUES (?, ?)");
                $stmt->bind_param("is", $user_id, $user_type);
                $stmt->execute();
                return $con->insert_id; // Return the session ID
            }

            // 1. Admin login check
            $sql = "SELECT * FROM Admin WHERE email='$user_email' AND password='$password'";
            $result = mysqli_query($con, $sql);
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                $_SESSION['logged'] = true;
                $_SESSION['user_id'] = $row['admin_id'];
                $_SESSION['email'] = $user_email;
                $_SESSION['user_type'] = "Admin";

                // Log session and set session ID
                $_SESSION['session_id'] = logSession($con, $row['admin_id'], "Admin");

                header("Location: admin_dashboard.php");
                exit();
            }

            // 2. Consumer login check
            $sql = "SELECT * FROM Consumer WHERE email='$user_email' AND password='$password'";
            $result = mysqli_query($con, $sql);
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                $_SESSION['logged'] = true;
                $_SESSION['user_id'] = $row['consumer_id'];
                $_SESSION['email'] = $user_email;
                $_SESSION['user_type'] = "Consumer";

                // Log session and set session ID
                $_SESSION['session_id'] = logSession($con, $row['consumer_id'], "Consumer");

                header("Location: consumer_dashboard.php");
                exit();
            }

            // 3. Stakeholder login check
            $sql = "SELECT * FROM Stakeholder WHERE email='$user_email' AND password='$password'";
            $result = mysqli_query($con, $sql);
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                $_SESSION['logged'] = true;
                $_SESSION['user_id'] = $row['stakeholder_id'];
                $_SESSION['email'] = $user_email;
                $_SESSION['user_type'] = "Stakeholder";

                // Log session and set session ID
                $_SESSION['session_id'] = logSession($con, $row['stakeholder_id'], "Stakeholder");

                header("Location: stakeholder_dashboard.php");
                exit();
            }

            // 4. Rider login check
            $sql = "SELECT * FROM Rider WHERE email='$user_email' AND password='$password'";
            $result = mysqli_query($con, $sql);
            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_assoc($result);
                $_SESSION['logged'] = true;
                $_SESSION['user_id'] = $row['rider_id'];
                $_SESSION['email'] = $user_email;
                $_SESSION['user_type'] = "Rider";

                // Log session and set session ID
                $_SESSION['session_id'] = logSession($con, $row['rider_id'], "Rider");

                header("Location: rider_dashboard.php");
                exit();
            }

            // Invalid credentials message
            echo "Invalid email or password. Please try again.";
        }
    }
?>

<!-- Logout Script -->
<?php
    // Call this script when logging out
    if (isset($_GET['logout']) && $_SESSION['logged'] == true) {
        $session_id = $_SESSION['session_id'];
        // Update logout_time in Session table
        $stmt = $con->prepare("UPDATE Session SET logout_time = NOW() WHERE session_id = ?");
        $stmt->bind_param("i", $session_id);
        $stmt->execute();
        
        // Destroy session variables
        session_unset();
        session_destroy();
        
        header("Location: login.php"); // Redirect to login page
        exit();
    }
?>
