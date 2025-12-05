<?php
require __DIR__ . "/includes/header.php";
require __DIR__ . "/includes/db_connect.php";

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // this checks if email exists
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // if the email is not found it did this
        $error_message = "No account found for this email. Please register first.";
    } else {
        $user = $result->fetch_assoc();

        // it checks the pass
        if (!password_verify($password, $user["password"])) {
            $error_message = "Incorrect password.";
        } else {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];

            header("Location: index.php");
            exit();
        }
    }
}
?>

<link rel="stylesheet" href="/stayease/assets/css/login.css">

<div class="login-container">
    <div class="login-box">

        <h1>Welcome Back</h1>
        <p class="subtitle">Log in to continue</p>

        <?php if (!empty($error_message)): ?>
            <div class="error-msg"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit" class="login-btn">Log In</button>
        </form>


        <button type="button" id="googleLoginBtn" class="google-btn">
            <img src="/stayease/assets/img/google-icon.png" alt=""> Sign in with Google
        </button>

        <p class="signup-text">
            Don’t have an account?
            <a href="register.php">Create one</a>
        </p>

    </div>
</div>

<script type="module" src="/stayease/assets/js/google-login.js"></script>

<?php require __DIR__ . "/includes/footer.php"; ?>
