<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once '../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer' || !isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$errors = [];

if (!isset($_SESSION['profile_csrf'])) {
    $_SESSION['profile_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $csrfToken = (string)($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['profile_csrf'], $csrfToken)) {
        $errors[] = 'Your form session expired. Please try again.';
    } elseif ($name === '' || strlen($name) > 100) {
        $errors[] = 'Enter a valid name.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $errors[] = 'Enter a valid email address.';
    } elseif ($phone === '' || strlen($phone) > 20) {
        $errors[] = 'Enter a valid phone number.';
    } elseif ($password !== '' && strlen($password) < 6) {
        $errors[] = 'New password must be at least 6 characters.';
    } else {
        $safeName = $conn->real_escape_string($name);
        $safeEmail = $conn->real_escape_string($email);
        $safePhone = $conn->real_escape_string($phone);

        $emailCheck = $conn->query(
            "SELECT user_id FROM users
             WHERE email = '$safeEmail'
             AND user_id != $userId
             LIMIT 1"
        );

        if ($emailCheck->num_rows > 0) {
            $errors[] = 'This email address is already in use.';
        } else {
            try {
                $conn->begin_transaction();

                if ($password !== '') {
                    $safePassword = sha1($password);
                    $sql = "UPDATE users
                            SET name = '$safeName', email = '$safeEmail',
                                phone = '$safePhone', password = '$safePassword'
                            WHERE user_id = $userId AND role = 'customer'";
                } else {
                    $sql = "UPDATE users
                            SET name = '$safeName', email = '$safeEmail',
                                phone = '$safePhone'
                            WHERE user_id = $userId AND role = 'customer'";
                }

                $conn->query($sql);

                if ($conn->affected_rows < 1) {
                    $current = $conn->query(
                        "SELECT name, email, phone FROM users
                         WHERE user_id = $userId AND role = 'customer'"
                    );
                    $currentData = $current->fetch_assoc();
                    if (
                        !$currentData ||
                        $currentData['name'] !== $name ||
                        $currentData['email'] !== $email ||
                        $currentData['phone'] !== $phone
                    ) {
                        throw new Exception('Profile could not be updated.');
                    }
                }

                $conn->commit();
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                header('Location: profile.php?success=updated');
                exit;
            } catch (Throwable $exception) {
                $conn->rollback();
                error_log('Customer profile update error: ' . $exception->getMessage());
                $errors[] = 'Profile could not be updated.';
            }
        }
    }
}

$result = $conn->query(
    "SELECT name, email, phone, created_at
     FROM users
     WHERE user_id = $userId AND role = 'customer'"
);
$profile = $result->fetch_assoc();

if (!$profile) {
    header('Location: ../login.php');
    exit;
}

function escapeProfileHtml($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Profile · Routeline Bus</title>
    <link rel="stylesheet" href="../assets/css/seat.css">
    <link rel="stylesheet" href="../assets/css/customer_topbar.css">
    <style>
        .profile-wrap { max-width: 760px; margin: 24px auto; }
        .profile-card { background: var(--card); border: 1px solid var(--line); border-radius: 18px; padding: 24px; }
        .profile-head { display:flex; align-items:center; gap:16px; margin-bottom:20px; }
        .profile-avatar { width:64px; height:64px; border-radius:50%; background:var(--signal); color:var(--signal-ink); display:grid; place-items:center; font-size:24px; font-weight:800; }
        .profile-fields { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:16px; }
        .profile-fields label { display:block; margin-bottom:6px; font-weight:700; }
        .profile-fields input { width:100%; padding:11px; border:1px solid var(--line); border-radius:8px; background:var(--card); color:var(--ink); }
        .profile-field-full { grid-column:1/-1; }
        .notice { margin-bottom:16px; padding:12px 14px; border-radius:10px; background:var(--taken); }
        .notice.error { border-left:4px solid #dc3545; }
        .notice.success { border-left:4px solid #198754; }
        .profile-actions { display:flex; gap:10px; margin-top:18px; }
        .profile-actions .go { width:auto; flex:1; }
        .profile-form-cancel { display:inline-flex; align-items:center; justify-content:center; min-width:110px; padding:0 18px; border:1px solid var(--line); border-radius:12px; color:var(--ink); background:var(--card); font-weight:700; text-decoration:none; }
        @media (max-width:600px) {
            .profile-fields { grid-template-columns:1fr; }
            .profile-field-full { grid-column:auto; }
        }
    </style>
</head>
<body>
<?php include 'customer_topbar.php'; ?>
<main class="profile-wrap">
    <section class="profile-card">
        <?php if (isset($_GET['success'])): ?>
            <div class="notice success">Profile updated successfully.</div>
        <?php endif; ?>
        <?php foreach ($errors as $error): ?>
            <div class="notice error"><?php echo escapeProfileHtml($error); ?></div>
        <?php endforeach; ?>

        <div class="profile-head">
            <div class="profile-avatar">
                <?php echo escapeProfileHtml(strtoupper(substr($profile['name'], 0, 2))); ?>
            </div>
            <div>
                <h1 style="font-size:26px;">My Profile</h1>
                <p class="muted">Member since <?php echo escapeProfileHtml(date('d M Y', strtotime($profile['created_at']))); ?></p>
            </div>
        </div>

        <form method="post" action="profile.php">
            <input type="hidden" name="csrf_token" value="<?php echo escapeProfileHtml($_SESSION['profile_csrf']); ?>">
            <div class="profile-fields">
                <div>
                    <label for="profile-name">Full Name</label>
                    <input id="profile-name" type="text" name="name" maxlength="100" required value="<?php echo escapeProfileHtml($profile['name']); ?>">
                </div>
                <div>
                    <label for="profile-phone">Phone</label>
                    <input id="profile-phone" type="text" name="phone" maxlength="20" required value="<?php echo escapeProfileHtml($profile['phone']); ?>">
                </div>
                <div class="profile-field-full">
                    <label for="profile-email">Email Address</label>
                    <input id="profile-email" type="email" name="email" maxlength="100" required value="<?php echo escapeProfileHtml($profile['email']); ?>">
                </div>
                <div class="profile-field-full">
                    <label for="profile-password">New Password <span class="muted">(leave blank to keep current password)</span></label>
                    <input id="profile-password" type="password" name="password" minlength="6" autocomplete="new-password">
                </div>
            </div>
            <div class="profile-actions">
                <button type="submit" class="go">Save Profile</button>
                <a class="profile-form-cancel" href="home.php">Cancel</a>
            </div>
        </form>
    </section>
</main>
</body>
</html>
