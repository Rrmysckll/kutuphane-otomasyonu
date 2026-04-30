<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ogrenci') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT books.title, books.author, loans.borrow_date FROM loans JOIN books ON loans.book_id = books.id WHERE loans.user_id = :user_id AND loans.return_date IS NULL");
$stmt->execute(['user_id' => $user_id]);
$myBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenci Paneli | Niğde Merkez Kütüphane</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --bg-color: #F4F7FE; --text-color: #2B3674; --card-bg: #FFFFFF; --nav-bg: linear-gradient(135deg, #05CD99 0%, #4318FF 100%); --nav-text: #FFFFFF; --accent-blue: #4318FF; --border-color: #E2E8F0; }
        [data-theme="dark"] { --bg-color: #0B1437; --text-color: #FFFFFF; --card-bg: #111C44; --nav-bg: #111C44; --border-color: #1A2652; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); color: var(--text-color); transition: 0.3s; }
        .navbar { background: var(--nav-bg) !important; padding: 18px 0; }
        .navbar-brand { font-weight: 700; color: #fff !important; }
        .custom-card { background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 20px; padding: 30px; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative; overflow: hidden; }
        .custom-card::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 5px; background: linear-gradient(90deg, var(--accent-blue), #05CD99); transform: scaleX(0); transform-origin: left; transition: transform 0.4s ease; }
        .custom-card:hover { transform: translateY(-10px) scale(1.02); box-shadow: 0 20px 40px rgba(67, 24, 255, 0.15); border-color: transparent; }
        .custom-card:hover::before { transform: scaleX(1); }
        .btn-animated { background: var(--nav-bg); color: white; border: none; border-radius: 50px; padding: 12px 30px; font-weight: 600; position: relative; z-index: 1; overflow: hidden; transition: 0.3s; text-decoration: none; display: inline-block; }
        .btn-animated::before { content: ''; position: absolute; bottom: 0; left: 0; width: 0%; height: 100%; background-color: rgba(255,255,255,0.2); transition: 0.4s; z-index: -1; }
        .btn-animated:hover { color: #fff; transform: translateY(-3px); box-shadow: 0 8px 25px rgba(67, 24, 255, 0.4); }
        .btn-animated:hover::before { width: 100%; }
        .theme-toggle { background: none; border: none; color: #fff; font-size: 1.4rem; cursor: pointer; margin-right: 20px; transition: 0.3s; }
        .theme-toggle:hover { transform: rotate(180deg) scale(1.2); }
        .btn-logout { background-color: rgba(255,255,255,0.2); color: #fff; border-radius: 10px; padding: 8px 18px; text-decoration: none; transition: 0.3s; }
        .btn-logout:hover { background-color: #ff4757; color: #fff; transform: scale(1.05); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg mb-5">
        <div class="container">
            <a class="navbar-brand" href="user_dashboard.php"><i class="fa-solid fa-graduation-cap me-2"></i> ÖĞRENCİ PANELİ</a>
            <div class="d-flex align-items-center">
                <button class="theme-toggle" id="themeToggle"><i class="fa-solid fa-moon"></i></button>
                <span class="text-white me-4 fw-bold">Öğrenci: <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="logout.php" class="btn btn-logout"><i class="fa-solid fa-power-off"></i></a>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="row g-4">
            <div class="col-md-8">
                <div class="custom-card">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-book-reader me-2 text-primary"></i>Üzerimdeki Kitaplar</h5>
                    <?php if(count($myBooks) > 0): ?>
                        <div class="row">
                            <?php foreach($myBooks as $book): ?>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 border rounded-4 shadow-sm" style="background: rgba(67, 24, 255, 0.03);">
                                    <h6 class="fw-bold text-primary mb-1"><?= htmlspecialchars($book['title']) ?></h6>
                                    <p class="mb-2 text-muted small"><i class="fa-solid fa-pen-nib me-1"></i><?= htmlspecialchars($book['author']) ?></p>
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-calendar-day me-1"></i>Alış: <?= date('d.m.Y', strtotime($book['borrow_date'])) ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 opacity-50"><i class="fa-solid fa-box-open fs-1 mb-3"></i><p>Şu an üzerinde emanet kitap bulunmuyor.</p></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="custom-card text-center h-100 d-flex flex-column justify-content-center align-items-center">
                    <i class="fa-solid fa-magnifying-glass fs-1 text-success mb-3"></i>
                    <h5 class="fw-bold">Yeni Kitap Bul</h5>
                    <p class="text-muted small mb-4">Kütüphanedeki mevcut kitapları incele ve talep et.</p>
                    <a href="catalog.php" class="btn btn-animated w-100">Kataloğu İncele <i class="fa-solid fa-arrow-right ms-2"></i></a>
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