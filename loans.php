<?php
session_start();
require_once 'config/db.php';

// Oturum kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')

$mesaj = '';
$mesaj_tur = '';

// KİTAP ÖDÜNÇ VERME İŞLEMİ (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'borrow') {
    $user_id = $_POST['user_id'];
    $book_id = $_POST['book_id'];
    $borrow_date = date('Y-m-d'); // Bugünün tarihi

    try {
        // 1. Önce loans tablosuna kaydı ekle
        $stmt = $db->prepare("INSERT INTO loans (user_id, book_id, borrow_date) VALUES (:user_id, :book_id, :borrow_date)");
        $stmt->execute([
            'user_id' => $user_id,
            'book_id' => $book_id,
            'borrow_date' => $borrow_date
        ]);

        // 2. Kitabın durumunu books tablosunda "odunc_verildi" olarak güncelle
        $updateStmt = $db->prepare("UPDATE books SET status = 'odunc_verildi' WHERE id = :book_id");
        $updateStmt->execute(['book_id' => $book_id]);

        $mesaj = "Kitap başarıyla ödünç verildi!";
        $mesaj_tur = "success";
    } catch (PDOException $e) {
        $mesaj = "Hata: Ödünç verme işlemi sırasında bir sorun oluştu.";
        $mesaj_tur = "danger";
    }
}

// KİTAP TESLİM ALMA İŞLEMİ (GET)
if (isset($_GET['return_id']) && isset($_GET['book_id'])) {
    $loan_id = $_GET['return_id'];
    $returned_book_id = $_GET['book_id'];
    $return_date = date('Y-m-d');

    try {
        // 1. loans tablosunda iade tarihini güncelle
        $stmt = $db->prepare("UPDATE loans SET return_date = :return_date WHERE id = :loan_id");
        $stmt->execute([
            'return_date' => $return_date,
            'loan_id' => $loan_id
        ]);

        // 2. Kitabın durumunu books tablosunda tekrar "mevcut" yap
        $updateStmt = $db->prepare("UPDATE books SET status = 'mevcut' WHERE id = :book_id");
        $updateStmt->execute(['book_id' => $returned_book_id]);

        $mesaj = "Kitap başarıyla teslim alındı!";
        $mesaj_tur = "success";
    } catch (PDOException $e) {
        $mesaj = "Hata: Teslim alma işlemi başarısız oldu.";
        $mesaj_tur = "danger";
    }
}

// FORM İÇİN VERİ ÇEKME
// Sadece durumu 'mevcut' olan kitapları getir
$available_books = $db->query("SELECT id, title, author FROM books WHERE status = 'mevcut' ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
// Sadece öğrencileri getir
$usersList = $db->query("SELECT id, username FROM users WHERE role = 'ogrenci' ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);

// TABLO İÇİN İLİŞKİSEL VERİ ÇEKME (JOIN Kullanımı)
// Kim, hangi kitabı ne zaman aldı?
$query = "
    SELECT loans.id as loan_id, loans.borrow_date, loans.return_date, 
           users.username, 
           books.id as book_id, books.title 
    FROM loans 
    JOIN users ON loans.user_id = users.id 
    JOIN books ON loans.book_id = books.id 
    ORDER BY loans.borrow_date DESC
";
$loansList = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödünç Takibi | Niğde Merkez Kütüphane</title>
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
            --accent-orange: #EAB308;
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
            --accent-orange: #EAB308;
            --border-color: #1A2652;
            --input-bg: #0B1437;
            --table-text: #E0E0E0;
        }

        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); color: var(--text-color); transition: all 0.3s ease; }
        .navbar { background: var(--nav-bg) !important; padding: 18px 0; box-shadow: 0 4px 20px rgba(67, 24, 255, 0.15); }
        .navbar-brand { font-weight: 700; color: var(--nav-text) !important; font-size: 1.4rem; }
        .navbar-text { color: var(--nav-text) !important; }

        .custom-card { background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); padding: 25px; margin-bottom: 25px; }

        .form-select { background-color: var(--input-bg); border: 1px solid var(--border-color); color: var(--text-color); border-radius: 12px; padding: 12px 15px; }
        .form-select:focus { border-color: var(--accent-orange); box-shadow: 0 0 0 3px rgba(234, 179, 8, 0.1); }

        .btn-custom { background-color: var(--accent-orange); color: #fff; border: none; border-radius: 12px; padding: 12px 20px; font-weight: 600; }
        .btn-custom:hover { background-color: #CA8A04; color: #fff; }

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
        <?php if(!empty($mesaj)): ?>
            <div class="alert alert-<?= $mesaj_tur ?> alert-dismissible fade show rounded-4" role="alert">
                <?= $mesaj ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Kitap Verme Formu -->
            <div class="col-md-4">
                <div class="custom-card">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-hand-holding-hand me-2" style="color: var(--accent-orange);"></i>Yeni Ödünç Ver</h5>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="borrow">
                        
                        <div class="mb-3">
                            <label class="form-label">Üye Seçin</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Öğrenci Seçiniz...</option>
                                <?php foreach($usersList as $usr): ?>
                                    <option value="<?= $usr['id'] ?>"><?= htmlspecialchars($usr['username']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Kitap Seçin</label>
                            <select name="book_id" class="form-select" required>
                                <option value="">Mevcut Kitaplardan Seçiniz...</option>
                                <?php foreach($available_books as $bk): ?>
                                    <option value="<?= $bk['id'] ?>"><?= htmlspecialchars($bk['title']) ?> - <?= htmlspecialchars($bk['author']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if(empty($available_books)): ?>
                                <small class="text-danger mt-1 d-block"><i class="fa-solid fa-triangle-exclamation"></i> Kütüphanede verilebilecek mevcut kitap yok.</small>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-custom w-100" <?= empty($available_books) ? 'disabled' : '' ?>>
                            <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Kitabı Teslim Et
                        </button>
                    </form>
                </div>
            </div>

            <!-- İşlem Listesi Tablosu -->
            <div class="col-md-8">
                <div class="custom-card">
                    <h5 class="fw-bold mb-4"><i class="fa-solid fa-clock-rotate-left me-2" style="color: var(--accent-orange);"></i>Ödünç Geçmişi ve Takip</h5>
                    <div class="table-responsive">
                        <table class="table table-hover border-transparent">
                            <thead>
                                <tr>
                                    <th>Öğrenci</th>
                                    <th>Kitap</th>
                                    <th>Veriliş Tarihi</th>
                                    <th>Durum</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($loansList) > 0): ?>
                                    <?php foreach($loansList as $loan): ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($loan['username']) ?></td>
                                        <td><?= htmlspecialchars($loan['title']) ?></td>
                                        <td><small class="text-muted"><?= date('d.m.Y', strtotime($loan['borrow_date'])) ?></small></td>
                                        <td>
                                            <?php if(empty($loan['return_date'])): ?>
                                                <span class="badge bg-warning text-dark border border-warning rounded-pill px-3">Okunuyor</span>
                                            <?php else: ?>
                                                <span class="badge bg-success bg-opacity-25 text-success border border-success rounded-pill px-3">
                                                    İade Edildi <br> <small>(<?= date('d.m.Y', strtotime($loan['return_date'])) ?>)</small>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if(empty($loan['return_date'])): ?>
                                                <a href="?return_id=<?= $loan['loan_id'] ?>&book_id=<?= $loan['book_id'] ?>" class="btn btn-sm btn-outline-success rounded-3 fw-bold" onclick="return confirm('Kitabın sağlam bir şekilde geri alındığını onaylıyor musunuz?');">
                                                    <i class="fa-solid fa-check me-1"></i> İade Al
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted"><i class="fa-solid fa-lock"></i> Kapandı</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Henüz hiç ödünç verme işlemi yapılmamış.</td>
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