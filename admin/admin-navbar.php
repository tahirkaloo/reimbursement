<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/admin_styles.css">
    <style>
        /* CSS styles for the admin navbar */
        .admin-navbar {
            background-color: #333;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            color: #fff;
        }

        .admin-navbar .logo img {
            height: 50px;
        }

        .admin-navbar ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .admin-navbar ul li {
            margin-left: 10px;
        }

        .admin-navbar ul li a {
            color: #fff;
            text-decoration: none;
        }

        .admin-navbar ul li a:hover {
            color: #ddd;
        }
    </style>
</head>
<body>
    <div class="admin-navbar">
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
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</body>
</html>

