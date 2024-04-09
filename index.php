<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="120"> <!-- This line will refresh your page every 5 seconds -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="styles.css?version=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <title>FTR VPNAllow List</title>
</head> 

<body>
    <img src="FTR_logo.jpg" class="img-thumbnail" alt="FTR" width="100" height="100">
    <h2 class="text-center">FTR VPN Allow List</h2>
    <p>&nbsp;</p> <!-- Adds a space between the headings -->
    <h3 class="text-center red-text">Refresh page before new entry, edits and deletions</h3>
    <form id="editForm" method="post" action="save_changes.php">
        <div class="row d-flex justify-content-center align-content-center">
            <div class="container">
                <?php
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                if (isset($_SESSION['message'])) {
                    echo "<div class='alert alert-success auto-dismiss' role='alert'>" . $_SESSION['message'] . "</div>";
                    unset($_SESSION['message']); // Clear the message after displaying it
                }
                ?>
                <div class="container">
                    <h4>New Entry</h4>
                    <table class="table table-dark table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class='text-center'>Name</th>
                                <th class='text-center'>IP</th>
                                <th class='text-center'>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-dark text-white">
                                <td><input type='text' name='newName[]' class='form-control bg-dark text-white'
                                        required></td>
                                <td><input type='text' name='newIp[]' class='form-control bg-dark text-white'
                                        pattern='^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$'
                                        title='Enter a valid IPv4 address (e.g., 0.0.0.0)' required></td>
                                <td>
                                    <button type="submit" class="btn btn-success btn-sm w-100">Save Changes</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class='container'>
                    <div class="accordion" id="accordionEntries">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="existingEntriesHeading">
                                <button class="accordion-button collapsed bg-dark text-white" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#existingEntries" aria-expanded="true"
                                    aria-controls="existingEntries">
                                    Existing Entries
                                </button>
                            </h2>
                            <div id="existingEntries" class="accordion-collapse collapse"
                                aria-labelledby="existingEntriesHeading" data-bs-parent="#accordionEntries">
                                <div class="accordion-body bg-dark text-white">
                                    <table class="table table-dark table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th class='text-center'>Name</th>
                                                <th class='text-center'>IP</th>
                                                <th class='text-center'>Edit</th>
                                                <th class='text-center'>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $file_path = "/ftrallowlist/FTRAllowList/AllowList.txt"; // Correct path to your text document
                                            $lines = file($file_path, FILE_IGNORE_NEW_LINES);

                                            foreach ($lines as $index => $line) {
                                                list($name, $ip) = explode(',', $line);
                                                echo "<tr class='bg-dark text-white'>";
                                                echo "<td><input type='text' name='name[$index]' value='$name' class='form-control bg-dark text-white' readOnly></td>";
                                                echo "<td><input type='text' name='ip[$index]' value='$ip' class='form-control bg-dark text-white' pattern='^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$' title='Enter a valid IPv4 address (e.g., 0.0.0.0)' readOnly></td>";
                                                echo "<td><button type='button' class='btn btn-primary btn-sm w-100' onclick='enableEdit(this)'>Edit</button></td>";
                                                echo "<td><button type='button' class='btn btn-danger btn-sm w-100' onclick='markForDeletion(this, $index)'>Delete</button></td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center gap-2 mt-3">
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
                <input type="hidden" name="file_path" value="<?php echo htmlspecialchars($file_path); ?>">
            </div>
        </div>
    </form>

    <script src='scripts.js?v=1'></script>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this row?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="deleteRow()">Delete</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>