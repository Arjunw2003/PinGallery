
<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {

    $token = hash('sha256', $_COOKIE['remember_token']);

    $stmt = mysqli_prepare($conn, "SELECT id, username FROM users WHERE remember_token = ? AND token_expiry > NOW()");

    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
    }

    mysqli_stmt_close($stmt);
}

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

require_once 'includes/header.php';
?>

<div class="container d-flex align-items-center justify-content-center min-vh-100">

    <div class="card shadow-sm p-4" style="max-width:400px; width:100%;">

        <h3 class="text-center mb-4">Login</h3>

        <div id="loginAlert" class="alert d-none"></div>

        <form id="loginForm">

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" name="remember_me" value="1">
                <label class="form-check-label">Remember Me</label>
            </div>

            <button type="submit" class="btn btn-primary w-100" id="loginBtn">
                Log In
            </button>

        </form>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
$(document).ready(function() {

    $('#loginForm').submit(function(e) {

        e.preventDefault();

        let btn = $('#loginBtn');
        let alertBox = $('#loginAlert');

        btn.prop('disabled', true).text('Logging in...');

        $.ajax({

            url: 'ajax/login.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',

            success: function(res) {

                if (res.status == 'success') {

                    alertBox.removeClass('d-none alert-danger')
                            .addClass('alert-success')
                            .text(res.message);

                    window.location.href = 'dashboard.php';

                } else {

                    alertBox.removeClass('d-none alert-success')
                            .addClass('alert-danger')
                            .text(res.message);

                    btn.prop('disabled', false).text('Log In');
                }
            },

            error: function() {

                alertBox.removeClass('d-none')
                        .addClass('alert-danger')
                        .text('Server error. Please try again.');

                btn.prop('disabled', false).text('Log In');
            }

        });

    });

});
</script>
