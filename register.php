<?php
require_once('classes/functions.php');
require_once('classes/database.php');
$con = new database();
 
$data = $con->opencon();
$sweetAlertConfig = "";
 
if (isset($_POST['multisave'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $position = $_POST['position'];
    $hourly_rate = filter_input(INPUT_POST, 'hourly_rate', FILTER_VALIDATE_FLOAT);
    $profile_picture_path = handleFileUpload($_FILES["profile_picture"]);
   
    // Check if username is already taken before proceeding
    $db = $con->opencon();
    $query = $db->prepare("SELECT Username FROM user_account WHERE Username = ?");
    $query->execute([$username]);
    $existingUser = $query->fetch();
   
    if ($existingUser) {
        $_SESSION['error'] = "Username is already taken. Please choose a different username.";
    } elseif ($profile_picture_path === false) {
        $_SESSION['error'] = "Sorry, there was an error uploading your file or the file is invalid.";
    } elseif ($hourly_rate === false || $hourly_rate < 0) {
        $_SESSION['error'] = "Please enter a valid rate (must be a non-negative number).";
    } else {
        $userID = $con->signupUser($username, $password, $position, $hourly_rate, $profile_picture_path);
       
        if ($userID) {
            $sweetAlertConfig = "
            <script>
            Swal.fire({
                icon: 'success',
                title: 'Registration Successful',
                text: 'Your account has been created successfully!',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php';
                }
            });
            </script>";
        } else {
            $_SESSION['error'] = "Sorry, there was an error signing up.";
        }
    }
}
?>
 
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mura Lahat | Registration</title>
    <!-- Bootstrap CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- SweetAlert2 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- jQuery (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <!-- Custom CSS to match index.php -->
    <style>
        .custom-container {
            max-width: 500px;
            margin: auto;
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- SweetAlert2 JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <?php
    if (!empty($sweetAlertConfig)) {
        echo $sweetAlertConfig;
        exit;
    }
    ?>
    <div class="container custom-container rounded-3 shadow p-4 bg-light mt-5">
        <h3 class="text-center mb-4">Registration</h3>
        <form method="post" action="" enctype="multipart/form-data" novalidate>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Enter username" required>
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Please enter a valid username.</div>
                <div id="usernameFeedback" class="invalid-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one special character.</div>
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm Password:</label>
                <input type="password" class="form-control" name="confirmPassword" placeholder="Re-enter your password" required>
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Passwords do not match.</div>
            </div>
            <div class="mb-3">
                <label for="position" class="form-label">Position:</label>
                <select class="form-select" name="position" id="position" required>
                    <option selected disabled value="">Select Position</option>
                    <?php
                    $positions = $con->viewPositions();
                    foreach ($positions as $position) {
                        echo "<option value='{$position['Position_Details_ID']}'>{$position['Position']}</option>";
                    }
                    ?>
                </select>
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Please select a position.</div>
            </div>
            <div class="mb-3">
                <label for="hourly_rate" class="form-label">Rate (₱ per 5 seconds):</label>
                <input type="number" class="form-control" name="hourly_rate" id="hourly_rate" placeholder="Enter rate (e.g., 1000)" step="0.01" min="0" required>
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Please enter a valid non-negative rate.</div>
            </div>
            <div class="mb-3">
                <label for="profile_picture" class="form-label">Profile Picture:</label>
                <input type="file" class="form-control" name="profile_picture" id="profile_picture" accept="image/*" required>
                <div class="valid-feedback">Looks good!</div>
                <div class="invalid-feedback">Please upload a valid image file.</div>
            </div>
            <button type="submit" name="multisave" id="submitButton" class="btn btn-primary w-100 py-2" disabled>Sign Up</button>
            <div class="text-center mt-4">
                <a href="index.php" class="text-decoration-none">Already have an account? Login here</a>
            </div>
        </form>
    </div>
 
    <!-- Bootstrap JS (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const form = document.querySelector("form");
        const submitButton = document.getElementById("submitButton");
        let isUsernameValid = false;
 
        const inputs = form.querySelectorAll("input, select");
        inputs.forEach(input => {
            input.addEventListener("input", () => validateInput(input));
            input.addEventListener("change", () => validateInput(input));
        });
 
        form.addEventListener("submit", (event) => {
            let valid = true;
            inputs.forEach(input => {
                if (!validateInput(input)) {
                    valid = false;
                }
            });
            if (!valid || !isUsernameValid) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add("was-validated");
        });
 
        function validateInput(input) {
            let isValid = false;
 
            if (input.name === 'password') {
                const password = input.value;
                const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
                isValid = regex.test(password);
            } else if (input.name === 'confirmPassword') {
                const passwordInput = form.querySelector("input[name='password']");
                isValid = input.value === passwordInput.value && input.value !== '';
            } else if (input.name === 'position') {
                isValid = input.value !== '' && input.value !== null;
            } else if (input.name === 'hourly_rate') {
                const rate = parseFloat(input.value);
                isValid = !isNaN(rate) && rate >= 0;
            } else if (input.type === 'file') {
                isValid = input.files.length > 0;
            } else {
                isValid = input.checkValidity();
            }
 
            if (isValid) {
                input.classList.remove("is-invalid");
                input.classList.add("is-valid");
                input.setCustomValidity('');
            } else {
                input.classList.remove("is-valid");
                input.classList.add("is-invalid");
                input.setCustomValidity('Invalid input');
            }
 
            updateSubmitButton();
            return isValid;
        }
 
        function updateSubmitButton() {
            const allValid = Array.from(inputs).every(input => input.checkValidity());
            submitButton.disabled = !allValid || !isUsernameValid;
            console.log({
                allValid,
                isUsernameValid,
                inputs: Array.from(inputs).map(input => ({
                    name: input.name,
                    valid: input.checkValidity()
                }))
            });
        }
 
        $('#username').on('input', function() {
            const username = $(this).val();
            if (username.length > 0) {
                $.ajax({
                    url: 'AJAX/check_username.php',
                    method: 'POST',
                    data: { username: username },
                    dataType: 'json',
                    success: function(response) {
                        if (response.exists) {
                            $('#username').removeClass('is-valid').addClass('is-invalid');
                            $('#usernameFeedback').text('Username is already taken.').show();
                            $('#username').siblings('.invalid-feedback').not('#usernameFeedback').hide();
                            isUsernameValid = false;
                        } else {
                            $('#username').removeClass('is-invalid').addClass('is-valid');
                            $('#usernameFeedback').text('').hide();
                            $('#username').siblings('.valid-feedback').show();
                            isUsernameValid = true;
                        }
                        updateSubmitButton();
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                        $('#username').removeClass('is-valid').addClass('is-invalid');
                        $('#usernameFeedback').text('Error checking username.').show();
                        isUsernameValid = false;
                        updateSubmitButton();
                    }
                });
            } else {
                $('#username').removeClass('is-valid is-invalid');
                $('#usernameFeedback').text('').hide();
                isUsernameValid = false;
                updateSubmitButton();
            }
        });
 
        updateSubmitButton();
    });
    </script>
 
    <?php if (isset($_SESSION['error'])): ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: '<?php echo addslashes($_SESSION['error']); ?>',
        confirmButtonText: 'OK'
    });
    </script>
    <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</body>
</html>