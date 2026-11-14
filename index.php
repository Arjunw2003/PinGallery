<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $tokenHash = hash('sha256', $_COOKIE['remember_token']);
    $stmt = mysqli_prepare($conn, "SELECT id, username FROM users WHERE remember_token = ? AND token_expiry > NOW() LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $tokenHash);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($u = mysqli_fetch_assoc($res)) {
        $_SESSION['user_id'] = (int)$u['id'];
        $_SESSION['username'] = $u['username'];
    }
    mysqli_stmt_close($stmt);
}

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
        <h3 class="text-center mb-4">Login</h3>

        <div id="loginAlert" class="alert d-none" role="alert"></div>

        <form id="loginForm" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me" value="1">
                <label class="form-check-label" for="remember_me">Remember Me</label>
            </div>

            <button type="submit" class="btn btn-primary w-100" id="loginBtn">Log In</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
$(function() {
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        const $btn = $('#loginBtn');
        const $alert = $('#loginAlert');

        $btn.prop('disabled', true).text('Logging in...');
        $alert.addClass('d-none').removeClass('alert-danger alert-success');

        $.ajax({
            url: 'ajax/login_process.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    $alert.removeClass('d-none').addClass('alert-success').text(res.message);
                    window.location.href = 'dashboard.php';
                } else {
                    $alert.removeClass('d-none').addClass('alert-danger').text(res.message);
                    $btn.prop('disabled', false).text('Log In');
                }
            },
            error: function() {
                $alert.removeClass('d-none').addClass('alert-danger').text('Server error occurred. Please try again.');
                $btn.prop('disabled', false).text('Log In');
            }
        });
    });
});
</script>