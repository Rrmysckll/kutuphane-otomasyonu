<?php
session_start();
require_once 'config/db.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')

$mesaj = '';
$mesaj_tur = '';

// KİTAP EKLEME İŞLEMİ (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $isbn = trim($_POST['isbn']);
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category = trim($_POST['category']);

    try {
        $stmt = $db->prepare("INSERT INTO books (isbn, title, author, category) VALUES (:isbn, :title, :author, :category)");
        $stmt->execute([
            'isbn' => $isbn,
            'title' => $title,
            'author' => $author,
            'category' => $category
        ]);
        $mesaj = "Kitap başarıyla sisteme eklendi!";
        $mesaj_tur = "success";
    } catch (PDOException $e) {
        $mesaj = "Hata: Bu ISBN numarası zaten kayıtlı olabilir.";
        $mesaj_tur = "danger";
    }
}

// KİTAP SİLME İŞLEMİ (GET)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $db->prepare("DELETE FROM books WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $mesaj = "Kitap sistemden silindi.";
        $mesaj_tur = "warning";
    } catch (PDOException $e) {
        $mesaj = "Hata: Bu kitap şu an bir üyede olduğu için silinemez.";
        $mesaj_tur = "danger";
    }
}

// KİTAPLARI LİSTELEME
$stmt = $db->query("SELECT * FROM books ORDER BY added_at DESC");
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitap İşlemleri | Niğde Merkez Kütüphane</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Tasarım Değişkenleri (Dashboard ile aynı) */
        :root {
            --bg-color: #F4F7FE; 
            --text-color: #2B3674; 
            --card-bg: #FFFFFF;
            --nav-bg: linear-gradient(135deg, #4318FF 0%, #868CFF 100%); 
            --nav-text: #FFFFFF;
            --accent-blue: #4318FF;
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
            --accent-blue: #7551FF;
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

        .form-control, .form-select {
            background-color: var(--input-bg);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            border-radius: 12px;
            padding: 12px 15px;
        }
        .form-control:focus, .form-select:focus {
            background-color: var(--input-bg);
            color: var(--text-color);
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(67, 24, 255, 0.1);
        }

        .btn-custom {
            background-color: var(--accent-blue);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 600;
        }
        .btn-custom:hover { background-color: #3311DB; color: #fff; }

        /* Tablo Stilleri */
        .table { color: var(--table-text); vertical-align: middle; }
        .table thead th { 
            border-bottom: 2px solid var(--border-color); 
            color: var(--text-color);
            font-weight: 600;
            opacity: 0.8;
        }
        .table td { border-bottom: 1px solid var(--border-color); }
        .badge-status { padding: 8px 12px; border-radius: 8px; font-weight: 500; }

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
            <!-- Kitap Ekleme Formu -->
            <div class="col-md-4">
                <div class="custom-card">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-book-medical me-2 text-primary"></i>Yeni Kitap Ekle</h5>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="add">
                        <div class="mb-3">
                            <label class="form-label">ISBN Numarası</label>
                            <input type="text" name="isbn" class="form-control" required placeholder="Örn: 978-0131103627">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kitap Adı</label>
                            <input type="text" name="title" class="form-control" required placeholder="Örn: Suç ve Ceza">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Yazar Adı</label>
                            <input type="text" name="author" class="form-control" required placeholder="Örn: Dostoyevski">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Kategori</label>
                            <select name="category" class="form-select" required>
                                <option value="">Seçiniz...</option>
                                <option value="Roman">Roman</option>
                                <option value="Bilim Kurgu">Bilim Kurgu</option>
                                <option value="Tarih">Tarih</option>
                                <option value="Yazılım/Teknoloji">Yazılım/Teknoloji</option>
                                <option value="Kişisel Gelişim">Kişisel Gelişim</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-custom w-100"><i class="fa-solid fa-plus me-2"></i>Kitabı Kaydet</button>
                    </form>
                </div>
            </div>

            <!-- Kitap Listesi Tablosu -->
            <div class="col-md-8">
                <div class="custom-card">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-list me-2 text-primary"></i>Mevcut Kitaplar</h5>
                    <div class="table-responsive">
                        <table class="table table-hover border-transparent">
                            <thead>
                                <tr>
                                    <th>ISBN</th>
                                    <th>Kitap Adı</th>
                                    <th>Yazar</th>
                                    <th>Kategori</th>
                                    <th>Durum</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($books) > 0): ?>
                                    <?php foreach($books as $book): ?>
                                    <tr>
                                        <td><small class="text-muted"><?= htmlspecialchars($book['isbn']) ?></small></td>
                                        <td class="fw-bold"><?= htmlspecialchars($book['title']) ?></td>
                                        <td><?= htmlspecialchars($book['author']) ?></td>
                                        <td><span class="badge bg-secondary bg-opacity-25 text-secondary border"><?= htmlspecialchars($book['category']) ?></span></td>
                                        <td>
                                            <?php if($book['status'] == 'mevcut'): ?>
                                                <span class="badge bg-success bg-opacity-25 text-success border"><i class="fa-solid fa-check me-1"></i>Mevcut</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning bg-opacity-25 text-warning border"><i class="fa-solid fa-clock me-1"></i>Ödünçte</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="?delete=<?= $book['id'] ?>" class="btn btn-sm btn-outline-danger rounded-3" onclick="return confirm('Bu kitabı silmek istediğinize emin misiniz?');">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Sistemde henüz kayıtlı kitap bulunmuyor.</td>
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