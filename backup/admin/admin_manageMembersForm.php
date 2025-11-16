<?php
include '../config/connect.php';
include 'navbar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Management</title>

    <!-- Modern Typeface -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <style>
        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 2rem;
        }

        .management-header {
            margin-bottom: 2rem;
        }

        .management-header h1 {
            font-size: 1.875rem;
            font-weight: 600;
            color: black;
        }

        /* Modern DataTable Styling */
        .dataTables_wrapper {
            padding: 1.5rem;
        }

        .dataTables_filter input,
        .dataTables_length select {
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .dataTables_filter input:focus,
        .dataTables_length select:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1);
        }

        table.dataTable {
            border-collapse: separate !important;
            border-spacing: 0;
            width: 100% !important;
            margin: 1rem 0 !important;
        }

        table.dataTable thead th {
            color: black;
            font-weight: 500;
            padding: 1rem;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        table.dataTable tbody td {
            padding: 1rem;
            font-size: 0.875rem;
            color: black;
        }

        table.dataTable tbody tr:last-child td {
            border-bottom: none;
        }

        table.dataTable tbody tr:hover {
            background-color: rgba(79, 70, 229, 0.04) !important;
        }

        .status-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .status-btn-disable {
            background-color: #DE3F44;
            color: white;
        }

        .status-btn-enable {
            background-color: #28A745;
            color: white;
        }

        .status-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        /* Pagination Styling */
        .dataTables_paginate {
            margin-top: 1rem;
            padding-top: 1rem;
        }

        .dataTables_paginate .paginate_button {
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            border-radius: 8px;
            color: black !important;
            transition: all 0.2s;
        }

        .dataTables_paginate .paginate_button:hover {
            color: black !important;
        }

        .dataTables_paginate .paginate_button.current {
            border-color: black;
            color: white !important;
        }

        .dataTables_info {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 1rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
                margin: 1rem;
            }

            .dataTables_wrapper {
                padding: 1rem;
            }

            table.dataTable thead th,
            table.dataTable tbody td {
                padding: 0.75rem;
            }

            .status-btn {
                padding: 0.375rem 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="management-header">
            <h1>Member Management</h1>
        </div>

        <table id="membersTable" class="display responsive nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>Library ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Fines</th> <!-- New fines column -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT member_id, name, email, contact, status, fines
                        FROM user_info
                        WHERE role = 'user'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $statusClass = $row['status'] === 'enabled' ? 'disable' : 'enable';
                        $statusText = $row['status'] === 'enabled' ? 'Disable' : 'Enable';

                        echo "<tr>
                            <td>" . htmlspecialchars($row['member_id']) . "</td>
                            <td>" . htmlspecialchars($row['name']) . "</td>
                            <td>" . htmlspecialchars($row['email']) . "</td>
                            <td>" . htmlspecialchars($row['contact']) . "</td>
                            <td>" . ucfirst(htmlspecialchars($row['status'])) . "</td>
                            <td>
                                <input type='number' value='" . htmlspecialchars($row['fines']) . "' id='fines-" . htmlspecialchars($row['member_id']) . "' min='0' class='fines-input'>
                            </td>
                            <td>
                                <button
                                    class='status-btn status-btn-{$statusClass}'
                                    onclick='changeStatus({$row['member_id']}, \"" . ($row['status'] === 'enabled' ? 'disabled' : 'enabled') . "\")'
                                >
                                    {$statusText}
                                </button>
                                <button
                                    class='status-btn status-btn-enable'
                                    onclick='updateFines({$row['member_id']})'
                                >
                                    Update Fines
                                </button>
                            </td>
                        </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#membersTable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    search: "Search members:",
                    lengthMenu: "Show _MENU_ members per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ members",
                    infoEmpty: "Showing 0 to 0 of 0 members",
                    infoFiltered: "(filtered from _MAX_ total members)"
                },
                columnDefs: [
                    { responsivePriority: 1, targets: [0, 1, 6] },
                    { responsivePriority: 2, targets: [2, 4] },
                    { responsivePriority: 3, targets: 3 }
                ]
            });
        });

        function changeStatus(memberId, newStatus) {
            if (confirm(`Are you sure you want to ${newStatus} this member?`)) {
                fetch('admin_changeUser.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        member_id: memberId,
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || "Error changing status.");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An unexpected error occurred.");
                });
            }
        }

        function updateFines(memberId) {
            const finesValue = document.getElementById(`fines-${memberId}`).value;
            if (finesValue < 0) {
                alert("Fines cannot be negative.");
                return;
            }

            fetch('admin_updateFines.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    member_id: memberId,
                    fines: finesValue
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Fines updated successfully.");
                } else {
                    alert(data.message || "Error updating fines.");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An unexpected error occurred.");
            });
        }
    </script>
</body>
</html>