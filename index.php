<?php
session_start();
require_once 'config/db.php';

$hata = '';
$basari = '';

// Eğer zaten giriş yapılmışsa rolüne göre panele yönlendir
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: dashboard.php");
    } else {
        header("Location: user_dashboard.php");
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        
        $basari = "Giriş başarılı! Yönlendiriliyorsunuz...";
        
        if ($user['role'] == 'admin') {
            header("Refresh: 2; url=dashboard.php"); 
        } else {
            header("Refresh: 2; url=user_dashboard.php"); 
        }
    } else {
        $hata = "Kullanıcı adı veya şifre hatalı!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş | Niğde Merkez Kütüphane</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg-color: #F4F7FE; --text-color: #2B3674; --card-bg: #FFFFFF; --btn-grad: linear-gradient(135deg, #4318FF 0%, #868CFF 100%); --accent-blue: #4318FF; --border-color: #E2E8F0; --input-bg: #F4F7FE; }
        [data-theme="dark"] { --bg-color: #0B1437; --text-color: #FFFFFF; --card-bg: #111C44; --btn-grad: linear-gradient(135deg, #7551FF 0%, #4318FF 100%); --accent-blue: #7551FF; --border-color: #1A2652; --input-bg: #0B1437; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); color: var(--text-color); transition: all 0.3s ease; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .theme-toggle { position: absolute; top: 30px; right: 30px; background: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-color); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; cursor: pointer; transition: 0.3s; }
        .theme-toggle:hover { transform: scale(1.1) rotate(15deg); color: var(--accent-blue); }
        .login-card { background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 24px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); padding: 45px 35px; width: 100%; max-width: 420px; }
        .logo-icon { font-size: 3.5rem; background: var(--btn-grad); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 15px; }
        .form-control { background-color: var(--input-bg); border: 1px solid var(--border-color); color: var(--text-color); border-radius: 12px; padding: 14px 15px; }
        .form-control:focus { background-color: var(--input-bg); color: var(--text-color); border-color: var(--accent-blue); box-shadow: 0 0 0 4px rgba(67, 24, 255, 0.1); }
        .input-group-text { background-color: var(--input-bg); border: 1px solid var(--border-color); color: var(--text-color); border-radius: 12px 0 0 12px; border-right: none; opacity: 0.7; }
        .form-control.border-start-0 { border-left: none; border-radius: 0 12px 12px 0; }
        .btn-login { background: var(--btn-grad); color: #fff; border: none; border-radius: 12px; padding: 14px; font-weight: 600; transition: 0.3s; }
        .btn-login:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(67, 24, 255, 0.3); color: #fff; }
    </style>
</head>
<body>
    <button class="theme-toggle" id="themeToggle"><i class="fa-solid fa-moon"></i></button>
    <div class="login-card text-center">
        <div class="logo-icon"><i class="fa-solid fa-layer-group"></i></div>
        <h4 class="fw-bold mb-1">NİĞDE MERKEZ KÜTÜPHANE</h4>
        <p class="opacity-75 mb-4 small">Otomasyon Sistemi Girişi</p>

        <?php if(!empty($hata)): ?> <div class="alert alert-danger rounded-3 text-start small"><?= $hata ?></div> <?php endif; ?>
        <?php if(!empty($basari)): ?> <div class="alert alert-success rounded-3 text-start small"><?= $basari ?></div> <?php endif; ?>

        <form method="POST" action="" class="text-start mt-4">
            <div class="mb-3">
                <label class="form-label small fw-bold">Kullanıcı Adı</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-regular fa-user"></i></span>
                    <input type="text" name="username" class="form-control border-start-0" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">Şifre</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="password" class="form-control border-start-0" required>
                </div>
            </div>
            <button type="submit" class="btn btn-login w-100">Giriş Yap <i class="fa-solid fa-arrow-right-to-bracket ms-2"></i></button>
        </form>
    </div>
    <script>
        const themeToggleBtn = document.getElementById('themeToggle'); const themeIcon = themeToggleBtn.querySelector('i'); const htmlElement = document.documentElement;
        if (localStorage.getItem('libraryTheme')) { htmlElement.setAttribute('data-theme', localStorage.getItem('libraryTheme')); updateIcon(localStorage.getItem('libraryTheme')); }
        themeToggleBtn.addEventListener('click', () => {
            const newTheme = htmlElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            htmlElement.setAttribute('data-theme', newTheme); localStorage.setItem('libraryTheme', newTheme); updateIcon(newTheme);
        });
        function updateIcon(theme) { themeIcon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon'; }
    </script>
</body>
</html>