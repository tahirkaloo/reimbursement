<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/admin_styles.css">
    <style>
        /* Align menu items to the right */
        .menu {
            margin-left: auto;
        }
        .menu ul {
            display: flex;
            justify-content: flex-end;
        }
        .menu ul li {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <a href="admin_dashboard.php">
                <img src="https://reimbursement-instance-bucket.s3.amazonaws.com/hanover.jpg" alt="Logo">
            </a>
        </div>
        <div class="menu">
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="mileage_responses.php">Local Mileage Responses</a></li>
		<li><a href="contact_responses.php">Contact Form Responses</a></li>
		<li><a href="manage-users.php">Manage Users</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</body>
</html>

