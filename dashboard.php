<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetici Paneli | NİĞDE MERKEZ KÜTÜPHANE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg-color: #F4F7FE; --text-color: #2B3674; --card-bg: #FFFFFF; --nav-bg: linear-gradient(135deg, #4318FF 0%, #868CFF 100%); --nav-text: #FFFFFF; --accent-blue: #4318FF; --border-color: #E2E8F0; }
        [data-theme="dark"] { --bg-color: #0B1437; --text-color: #FFFFFF; --card-bg: #111C44; --nav-bg: #111C44; --nav-text: #FFFFFF; --accent-blue: #7551FF; --border-color: #1A2652; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); color: var(--text-color); transition: all 0.3s ease; }
        .navbar { background: var(--nav-bg) !important; padding: 18px 0; box-shadow: 0 4px 20px rgba(67, 24, 255, 0.15); }
        .navbar-brand { font-weight: 700; color: var(--nav-text) !important; }
        .custom-card { background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 20px; padding: 35px 20px; transition: 0.3s; height: 100%; }
        .custom-card:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0,0,0,0.08); }
        .icon-box { width: 75px; height: 75px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem; transition: 0.3s; }
        .icon-blue { background: rgba(67, 24, 255, 0.1); color: var(--accent-blue); }
        .icon-green { background: rgba(5, 205, 153, 0.1); color: #05CD99; }
        .icon-orange { background: rgba(255, 206, 32, 0.1); color: #EAB308; }
        .custom-card:hover .icon-box { transform: scale(1.1); }
        .btn-custom { background-color: var(--accent-blue); color: #fff; border-radius: 12px; padding: 12px 20px; font-weight: 600; transition: 0.3s; border: none; }
        .btn-custom:hover { background-color: #3311DB; color: #fff; transform: translateY(-2px); }
        .theme-toggle { background: none; border: none; color: #fff; font-size: 1.4rem; cursor: pointer; margin-right: 25px; transition: 0.3s; }
        .btn-logout { background-color: rgba(255,255,255,0.15); color: #fff; border-radius: 10px; padding: 8px 18px; text-decoration: none; transition: 0.3s; }
        .btn-logout:hover { background-color: #ff4757; color: #fff; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php"><i class="fa-solid fa-layer-group me-2"></i> YÖNETİCİ PANELİ</a>
            <div class="d-flex align-items-center">
                <button class="theme-toggle" id="themeToggle"><i class="fa-solid fa-moon"></i></button>
                <span class="text-white me-4 fw-bold">Admin: <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="logout.php" class="btn btn-logout"><i class="fa-solid fa-power-off"></i></a>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="custom-card text-center">
                    <div class="icon-box icon-blue"><i class="fa-solid fa-book-open"></i></div>
                    <h5 class="fw-bold">Kitap İşlemleri</h5>
                    <p class="opacity-75 small mb-4">Sisteme kitap ekle, düzenle, sil.</p>
                    <a href="books.php" class="btn btn-custom w-100">Kitapları Yönet</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="custom-card text-center">
                    <div class="icon-box icon-green"><i class="fa-solid fa-users"></i></div>
                    <h5 class="fw-bold">Üye İşlemleri</h5>
                    <p class="opacity-75 small mb-4">Öğrencileri görüntüle ve sisteme ekle.</p>
                    <a href="users.php" class="btn btn-custom w-100" style="background-color: #05CD99;">Üyeleri Yönet</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="custom-card text-center">
                    <div class="icon-box icon-orange"><i class="fa-solid fa-rotate"></i></div>
                    <h5 class="fw-bold">Ödünç Takibi</h5>
                    <p class="opacity-75 small mb-4">Kitap ver, teslim al, iadeleri takip et.</p>
                    <a href="loans.php" class="btn btn-custom w-100" style="background-color: #EAB308;">İşlemlere Git</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        const tBtn = document.getElementById('themeToggle'); const hEl = document.documentElement;
        if(localStorage.getItem('libraryTheme')) { hEl.setAttribute('data-theme', localStorage.getItem('libraryTheme')); tBtn.querySelector('i').className = localStorage.getItem('libraryTheme') === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon'; }
        tBtn.addEventListener('click', () => { const nT = hEl.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'; hEl.setAttribute('data-theme', nT); localStorage.setItem('libraryTheme', nT); tBtn.querySelector('i').className = nT === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon'; });
    </script>
</body>
</html>