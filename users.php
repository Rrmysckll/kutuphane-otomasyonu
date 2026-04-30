<?php
session_start();
require_once 'config/db.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')

$mesaj = '';
$mesaj_tur = '';

// ÜYE EKLEME İŞLEMİ (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Şifreyi güvenlik için hashliyoruz
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'ogrenci'; // Buradan eklenen herkes varsayılan olarak öğrenci (üye) olur

    try {
        $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        $stmt->execute([
            'username' => $username,
            'password' => $hashed_password,
            'role' => $role
        ]);
        $mesaj = "Yeni üye başarıyla sisteme eklendi!";
        $mesaj_tur = "success";
    } catch (PDOException $e) {
        $mesaj = "Hata: Bu kullanıcı adı zaten başka biri tarafından kullanılıyor.";
        $mesaj_tur = "danger";
    }
}

// ÜYE SİLME İŞLEMİ (GET)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Yöneticinin kendi kendini silmesini engelliyoruz
    if ($id == $_SESSION['user_id']) {
        $mesaj = "Güvenlik İhlali: Kendi admin hesabınızı silemezsiniz!";
        $mesaj_tur = "danger";
    } else {
        try {
            $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $mesaj = "Üye sistemden başarıyla silindi.";
            $mesaj_tur = "warning";
        } catch (PDOException $e) {
            $mesaj = "Hata: Bu üyenin üzerinde teslim etmediği kitaplar olduğu için silinemez.";
            $mesaj_tur = "danger";
        }
    }
}

// SADECE ÖĞRENCİLERİ LİSTELEME (Adminleri listede göstermiyoruz)
$stmt = $db->query("SELECT * FROM users WHERE role = 'ogrenci' ORDER BY created_at DESC");
$usersList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üye İşlemleri | Niğde Merkez Kütüphane</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Tasarım Değişkenleri */
        :root {
            --bg-color: #F4F7FE; 
            --text-color: #2B3674; 
            --card-bg: #FFFFFF;
            --nav-bg: linear-gradient(135deg, #4318FF 0%, #868CFF 100%); 
            --nav-text: #FFFFFF;
            --accent-green: #05CD99;
            --border-color: #E2E8F0;
            --input-bg: #F4F7FE;
            --table-text: #2B3674;
        }

        [data-theme="dark"] {
            --bg-color: #0B1437; 
            --text-color: #FFFFFF;
            --card-bg: #111C44;
            --nav-bg: #111C44; 
            --nav-text: #FFFFFF;
            --accent-green: #05CD99;
            --border-color: #1A2652;
            --input-bg: #0B1437;
            --table-text: #E0E0E0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .navbar { background: var(--nav-bg) !important; padding: 18px 0; box-shadow: 0 4px 20px rgba(67, 24, 255, 0.15); }
        .navbar-brand { font-weight: 700; color: var(--nav-text) !important; font-size: 1.4rem; }
        .navbar-text { color: var(--nav-text) !important; }

        .custom-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            padding: 25px;
            margin-bottom: 25px;
        }

        .form-control {
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            border-radius: 12px;
            padding: 12px 15px;
        }
        .form-control:focus {
            background-color: var(--input-bg);
            color: var(--text-color);
            border-color: var(--accent-green);
            box-shadow: 0 0 0 3px rgba(5, 205, 153, 0.1);
        }

        .btn-custom {
            background-color: var(--accent-green);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 600;
        }
        .btn-custom:hover { background-color: #04b386; color: #fff; }

        /* Tablo Stilleri */
        .table { color: var(--table-text); vertical-align: middle; }
        .table thead th { border-bottom: 2px solid var(--border-color); color: var(--text-color); font-weight: 600; opacity: 0.8; }
        .table td { border-bottom: 1px solid var(--border-color); }

        .theme-toggle { background: none; border: none; color: #fff; font-size: 1.4rem; cursor: pointer; margin-right: 20px; }
        .btn-logout { background-color: rgba(255,255,255,0.15); color: #fff; border-radius: 10px; padding: 8px 18px; font-weight: 500; text-decoration: none; }
        .btn-logout:hover { background-color: #ff4757; color: #fff; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php"><i class="fa-solid fa-layer-group me-2"></i> NİĞDE MERKEZ KÜTÜPHANE</a>
            <div class="d-flex align-items-center">
                <button class="theme-toggle" id="themeToggle"><i class="fa-solid fa-moon"></i></button>
                <a href="dashboard.php" class="btn btn-logout me-3"><i class="fa-solid fa-house"></i> Panele Dön</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Bildirim Mesajları -->
        <?php if(!empty($mesaj)): ?>
            <div class="alert alert-<?= $mesaj_tur ?> alert-dismissible fade show rounded-4" role="alert">
                <?= $mesaj ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Üye Ekleme Formu -->
            <div class="col-md-4">
                <div class="custom-card">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-user-plus me-2" style="color: var(--accent-green);"></i>Yeni Üye Ekle</h5>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">Kullanıcı Adı (Öğrenci No / İsim)</label>
                            <input type="text" name="username" class="form-control" required placeholder="Örn: 20251010">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Şifre</label>
                            <input type="password" name="password" class="form-control" required placeholder="Sisteme giriş şifresi belirleyin">
                            <small class="text-muted mt-1 d-block" style="font-size: 0.8rem;">*Şifreler veri tabanında güvenle şifrelenir (hash).</small>
                        </div>
                        <button type="submit" class="btn btn-custom w-100"><i class="fa-solid fa-check me-2"></i>Üyeyi Kaydet</button>
                    </form>
                </div>
            </div>

            <!-- Üye Listesi Tablosu -->
            <div class="col-md-8">
                <div class="custom-card">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-users me-2" style="color: var(--accent-green);"></i>Kayıtlı Üyeler</h5>
                    <div class="table-responsive">
                        <table class="table table-hover border-transparent">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Kullanıcı Adı</th>
                                    <th>Kayıt Tarihi</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($usersList) > 0): ?>
                                    <?php foreach($usersList as $usr): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary bg-opacity-25 text-secondary border">#<?= htmlspecialchars($usr['id']) ?></span></td>
                                        <td class="fw-bold"><?= htmlspecialchars($usr['username']) ?></td>
                                        <td><small class="text-muted"><?= date('d.m.Y H:i', strtotime($usr['created_at'])) ?></small></td>
                                        <td>
                                            <a href="?delete=<?= $usr['id'] ?>" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Bu üyeyi silmek istediğinize emin misiniz?');">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Sistemde henüz kayıtlı öğrenci/üye bulunmuyor.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const themeToggleBtn = document.getElementById('themeToggle');
        const themeIcon = themeToggleBtn.querySelector('i');
        const htmlElement = document.documentElement;

        const savedTheme = localStorage.getItem('libraryTheme');
        if (savedTheme) {
            htmlElement.setAttribute('data-theme', savedTheme);
            updateIcon(savedTheme);
        }

        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = htmlElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('libraryTheme', newTheme); 
            updateIcon(newTheme);
        });

        function updateIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        }
    </script>
</body>
</html>