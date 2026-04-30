<h1 align="center">📚 Modern Kütüphane Otomasyon Sistemi</h1>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Bootstrap_5-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
  <img src="https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black" alt="JavaScript">
</p>

## 📌 Proje Hakkında
Bu proje, **Niğde Ömer Halisdemir Üniversitesi (NÖHÜ)** Bilgisayar Mühendisliği bölümü geliştirme projesi olarak tasarlanmıştır. Aynı zamanda **BTK Akademi Web Programlama Atölyesi** eğitiminin başarılı bir çıktısıdır. 

Sistem, geleneksel kütüphane yönetim süreçlerini dijitalleştirmek, hızlandırmak ve kullanıcı deneyimini maksimize etmek amacıyla **Role-Based Access Control (Rol Tabanlı Erişim Kontrolü)** mimarisiyle sıfırdan geliştirilmiştir.

## 📸 Ekran Görüntüleri

### 1. Akıllı Giriş Ekranı (Dark/Light Mod)
> Yöneticileri ve öğrencileri yetkilerine göre ilgili panellere yönlendiren güvenli giriş ekranı.
> 
> <img width="1915" height="876" alt="Ekran görüntüsü 2026-05-01 013808" src="https://github.com/user-attachments/assets/5684a490-491c-4826-8a48-0ced39a0a6dc" />



### 2. Yönetici (Admin) Dashboard ve Kitap İşlemleri
> Kütüphane görevlisinin kitap ekleyip sildiği, canlı verileri takip ettiği ana merkez.
> 
> <img width="1919" height="865" alt="Ekran görüntüsü 2026-05-01 013948" src="https://github.com/user-attachments/assets/5d4f7ade-ee8e-45ff-be58-a8e7b0df8ab2" />

<img width="1914" height="836" alt="Ekran görüntüsü 2026-05-01 014152" src="https://github.com/user-attachments/assets/2714ea30-bb58-40ca-ba25-4eb0ebf56007" />

<img width="1919" height="835" alt="Ekran görüntüsü 2026-05-01 014239" src="https://github.com/user-attachments/assets/dea21b0e-0f88-4605-bcd0-236d1a7a5434" />


### 3. Öğrenci Paneli ve Canlı Katalog (Live Search)
> Öğrencilerin üzerindeki emanet kitapları gördüğü ve sayfa yenilenmeden anlık arama (JS) yapabildiği modern arayüz.
> 
> <img width="1919" height="876" alt="Ekran görüntüsü 2026-05-01 014327" src="https://github.com/user-attachments/assets/a0c6dfbc-ce5d-4e16-af9d-bb58b1c3f841" />


### 4. Ödünç Takip Sistemi (Relational DB)
> Hangi kitabın kimde olduğunu gösteren, kitap teslim alındığında stok durumunu otomatik güncelleyen yapı.
> 
> <img width="1919" height="900" alt="Ekran görüntüsü 2026-05-01 014047" src="https://github.com/user-attachments/assets/7ff85c45-66a3-4c02-9130-21b9990de7ee" />


## ✨ Öne Çıkan Özellikler

*   **🔐 Güvenli Rol Yönetimi:** Yönetici (Admin) ve Üye (Öğrenci) için tamamen izole edilmiş, oturum (session) korumalı ayrı paneller.
*   **🌓 Dinamik Tema (Dark/Light Mode):** Kullanıcı tercihini `localStorage` ile hafızada tutan, göz yormayan modern karanlık ve aydınlık mod desteği.
*   **⚡ Gerçek Zamanlı Arama (Live Search):** Öğrenci panelinde, sayfa yenilenmeden çalışan, JavaScript tabanlı anlık kitap kataloğu filtreleme.
*   **🔄 Akıllı Ödünç Takibi (Relational DB):** Kitaplar ve Üyeler tablolarının `JOIN` işlemleriyle birleştirilerek, emanet sürelerinin ve stok durumlarının otomatik yönetimi.
*   **🛡️ Güvenli Veri İşleme:** SQL Injection saldırılarına karşı `PDO (PHP Data Objects)` ve `Prepared Statements` kullanımı. Şifrelerin `password_hash()` ile kriptolanarak saklanması.
*   **📱 Responsive Tasarım:** Bootstrap 5 ile mobil, tablet ve masaüstü cihazlarla %100 uyumlu modern arayüz (SaaS UI/UX standartlarında).

## 🛠️ Kullanılan Teknolojiler
*   **Backend:** PHP 8+ (PDO Mimarisi)
*   **Veri Tabanı:** MySQL (İlişkisel Şema: *users, books, loans*)
*   **Frontend:** HTML5, CSS3, JavaScript (ES6)
*   **UI Framework:** Bootstrap 5
*   **Tipografi & İkonlar:** Google Fonts (Poppins), FontAwesome 6

## 🚀 Kurulum ve Çalıştırma

Projeyi kendi yerel sunucunuzda (XAMPP/WAMP) çalıştırmak için aşağıdaki adımları izleyin:

1.  Bu depoyu klonlayın veya `.zip` olarak indirip `C:\xampp\htdocs\kutuphane` dizinine çıkartın.
2.  XAMPP Control Panel'den **Apache** ve **MySQL** servislerini başlatın.
3.  `phpMyAdmin` veya `MySQL Workbench` üzerinden `kutuphane_db` adında yeni bir veri tabanı oluşturun (UTF-8 formatında).
4.  Proje içindeki `config/db.php` dosyasını açarak kendi MySQL kullanıcı adı ve şifrenizi girin.
5.  Tarayıcınızda `localhost/kutuphane/setup.php` adresine giderek tabloların ve ilk Admin hesabının otomatik kurulmasını sağlayın.
6.  `localhost/kutuphane` adresine giderek sistemi kullanmaya başlayabilirsiniz.

**Varsayılan Admin Girişi:**
*   **Kullanıcı Adı:** `admin`
*   **Şifre:** `123456`

## 👨‍💻 Geliştirici
**Rümeysa Çekli**
*   Niğde Ömer Halisdemir Üniversitesi - Bilgisayar Mühendisliği
*   [LinkedIn](www.linkedin.com/in/rumeysacekli9068672b0) | [GitHub](https://github.com/Rrmysckll)
