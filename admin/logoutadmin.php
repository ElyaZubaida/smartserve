<!-- 
 Frontend: Qai 
 Backend: Qai 
 -->

 <?php
session_start();

// Check if the "Yes" button was clicked
if (isset($_POST['confirm_logout'])) {
    session_unset();
    session_destroy();
    header("Location:loginadmin.php?logout=success");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Serve - Logout</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="logout-page-container">
    <div class="logout-content">
        <span class="material-symbols-outlined logout-icon">logout</span>
        <h1>Log out</h1>
        <p>Are you sure you want to logout?</p>
        
        <div class="logout-buttons">
            <form method="POST">
                <button type="submit" name="confirm_logout" class="btn-confirm">Yes, Log Me Out</button>
            </form>
            
            <button onclick="window.history.back()" class="btn-cancel">No, Stay Logged In</button>
        </div>
    </div>
</div>

</body>
</html>