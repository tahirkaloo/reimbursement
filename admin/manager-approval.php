<?php
// Fetch pending reimbursement requests from the database
// Replace this with your actual code to retrieve data from the database
$pendingRequests = [
    [
        'id' => 1,
        'user' => 'John Doe',
        'date' => '2023-05-30',
        'purpose' => 'Business travel'
    ],
    [
        'id' => 2,
        'user' => 'Jane Smith',
        'date' => '2023-05-29',
        'purpose' => 'Client meeting'
    ]
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manager Approval</title>
    <!-- Include any necessary CSS stylesheets -->
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Manager Approval</h1>

    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Date</th>
                <th>Purpose</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pendingRequests as $request): ?>
            <tr>
                <td><?php echo $request['user']; ?></td>
                <td><?php echo $request['date']; ?></td>
                <td><?php echo $request['purpose']; ?></td>
                <td>
                    <form method="POST" action="approve.php">
                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                        <button type="submit">Approve</button>
                    </form>
                    <form method="POST" action="reject.php">
                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                        <button type="submit">Reject</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Include any necessary JavaScript files -->
    <script src="script.js"></script>
</body>
</html>

