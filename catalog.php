<?php
session_start();
require_once 'config/db.php';

// Güvenlik: Sadece öğrenciler bu kataloğu görebilir
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ogrenci') {
    header("Location: index.php");
    exit;
}

// Sadece durumu 'mevcut' olan kitapları veri tabanından çekiyoruz
$stmt = $db->query("SELECT * FROM books WHERE status = 'mevcut' ORDER BY added_at DESC");
$availableBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitap Kataloğu | Niğde Merkez Kütüphane</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Öğrenci Paneli Tema Değişkenleri */
        :root { 
            --bg-color: #F4F7FE; 
            --text-color: #2B3674; 
            --card-bg: #FFFFFF;
            --nav-bg: linear-gradient(135deg, #05CD99 0%, #4318FF 100%); 
            --nav-text: #FFFFFF;
            --accent-blue: #4318FF;
            --accent-green: #05CD99;
            --border-color: #E2E8F0;
            --search-bg: #FFFFFF;
        }

        [data-theme="dark"] { 
            --bg-color: #0B1437; 
            --text-color: #FFFFFF;
            --card-bg: #111C44;
            --nav-bg: #111C44; 
            --nav-text: #FFFFFF;
            --border-color: #1A2652;
            --search-bg: #111C44;
        }

        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); color: var(--text-color); transition: all 0.3s ease; }
        .navbar { background: var(--nav-bg) !important; padding: 18px 0; box-shadow: 0 4px 20px rgba(5, 205, 153, 0.2); }
        .navbar-brand { font-weight: 700; color: #fff !important; }
        
        /* Arama Çubuğu */
        .search-box {
            background-color: var(--search-bg);
            border: 2px solid var(--border-color);
            border-radius: 50px;
            padding: 5px 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .search-box:focus-within {
            border-color: var(--accent-green);
            box-shadow: 0 10px 30px rgba(5, 205, 153, 0.15);
        }
        .search-input {
            border: none;
            background: transparent;
            color: var(--text-color);
            width: 100%;
            padding: 12px 10px;
            outline: none;
        }
        .search-input::placeholder { color: var(--text-color); opacity: 0.5; }

        /* Kitap Kartları */
        .book-card { 
            background-color: var(--card-bg); 
            border: 1px solid var(--border-color); 
            border-radius: 20px; 
            padding: 25px; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }
        .book-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 5px; height: 100%;
            background: var(--nav-bg); transform: scaleY(0); transform-origin: bottom; transition: transform 0.4s ease;
        }
        .book-card:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0,0,0,0.08); border-color: transparent; }
        .book-card:hover::before { transform: scaleY(1); }
        
        .book-icon {
            font-size: 2.5rem;
            color: var(--accent-green);
            margin-bottom: 15px;
            opacity: 0.8;
        }

        .category-badge {
            background-color: rgba(67, 24, 255, 0.1);
            color: var(--accent-blue);
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }

        [data-theme="dark"] .category-badge { color: #868CFF; }

        .theme-toggle { background: none; border: none; color: #fff; font-size: 1.4rem; cursor: pointer; margin-right: 20px; transition: 0.3s; }
        .theme-toggle:hover { transform: rotate(180deg) scale(1.2); }
        .btn-back { background-color: rgba(255,255,255,0.2); color: #fff; border-radius: 10px; padding: 8px 18px; text-decoration: none; transition: 0.3s; font-weight: 500;}
        .btn-back:hover { background-color: #fff; color: var(--accent-blue); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg mb-5">
        <div class="container">
            <a class="navbar-brand" href="user_dashboard.php"><i class="fa-solid fa-swatchbook me-2"></i> KÜTÜPHANE KATALOĞU</a>
            <div class="d-flex align-items-center">
                <button class="theme-toggle" id="themeToggle"><i class="fa-solid fa-moon"></i></button>
                <a href="user_dashboard.php" class="btn btn-back"><i class="fa-solid fa-arrow-left me-1"></i> Panele Dön</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Arama Çubuğu -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-8">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass text-muted ms-2 fs-5"></i>
                    <input type="text" id="searchInput" class="search-input" placeholder="Kitap adı, yazar veya kategori ara...">
                </div>
            </div>
        </div>

        <!-- Kitap Grid -->
        <div class="row g-4" id="bookGrid">
            <?php if(count($availableBooks) > 0): ?>
                <?php foreach($availableBooks as $book): ?>
                <div class="col-md-4 col-sm-6 book-item">
                    <div class="book-card">
                        <span class="category-badge"><?= htmlspecialchars($book['category']) ?></span>
                        <div class="book-icon"><i class="fa-solid fa-book-journal-whills"></i></div>
                        <h5 class="fw-bold book-title"><?= htmlspecialchars($book['title']) ?></h5>
                        <p class="text-muted mb-3 book-author"><i class="fa-solid fa-pen-fancy me-2"></i><?= htmlspecialchars($book['author']) ?></p>
                        
                        <div class="mt-auto pt-3 border-top" style="border-color: var(--border-color) !important;">
                            <small class="text-muted"><i class="fa-solid fa-barcode me-1"></i> ISBN: <?= htmlspecialchars($book['isbn']) ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fa-solid fa-ghost fs-1 text-muted mb-3 opacity-50"></i>
                    <h4 class="text-muted">Şu an kütüphanede mevcut kitap bulunmuyor.</h4>
                </div>
            <?php endif; ?>
            
            <!-- Arama sonucu bulunamadığında gösterilecek alan (Gizli başlar) -->
            <div class="col-12 text-center py-5 d-none" id="noResults">
                <i class="fa-solid fa-face-frown fs-1 text-muted mb-3 opacity-50"></i>
                <h5 class="text-muted">Aradığınız kriterlere uygun kitap bulunamadı.</h5>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tema Değiştirme JS
        const tBtn = document.getElementById('themeToggle'); const hEl = document.documentElement;
        if(localStorage.getItem('libraryTheme')) { hEl.setAttribute('data-theme', localStorage.getItem('libraryTheme')); tBtn.querySelector('i').className = localStorage.getItem('libraryTheme') === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon'; }
        tBtn.addEventListener('click', () => { const nT = hEl.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'; hEl.setAttribute('data-theme', nT); localStorage.setItem('libraryTheme', nT); tBtn.querySelector('i').className = nT === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon'; });

        // Gerçek Zamanlı JavaScript Arama Filtresi (Sayfa yenilemeden çalışır)
        const searchInput = document.getElementById('searchInput');
        const bookItems = document.querySelectorAll('.book-item');
        const noResults = document.getElementById('noResults');

        searchInput.addEventListener('keyup', function() {
            let filter = this.value.toLowerCase().trim();
            let visibleCount = 0;

            bookItems.forEach(function(item) {
                let title = item.querySelector('.book-title').textContent.toLowerCase();
                let author = item.querySelector('.book-author').textContent.toLowerCase();
                let category = item.querySelector('.category-badge').textContent.toLowerCase();

                if (title.includes(filter) || author.includes(filter) || category.includes(filter)) {
                    item.classList.remove('d-none');
                    visibleCount++;
                } else {
                    item.classList.add('d-none');
                }
            });

            // Eğer hiç sonuç yoksa "Bulunamadı" mesajını göster
            if (visibleCount === 0 && filter !== '') {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        });
    </script>
</body>
</html>