# 📁 FinalProject - Panduan Kolaborasi dengan GitHub

Dokumen ini berisi panduan lengkap cara bekerjasama dalam mengerjakan project ini menggunakan Git dan GitHub.

---

## 🛠️ Struktur Project

```
FinalProject/
├── backend/     # Laravel 10
└── frontend/    # Ionic (Angular)
```

---

## 🔄 Alur Bekerjasama Menggunakan GitHub

### 1. Masuk ke Folder Project

Buka terminal dengan cara

```bash
klik kanan di folder FinalProject lalu open terminal here

atau

klik kanan dan Open Git Bash Here
```
cara lain

search cmd lalu jalankan perintah

```bash
cd C:/Path/FinalProject
```

### 2. Cek Status Project

Gunakan perintah ini untuk melihat apakah ada file yang berubah atau update dari tim lain:

```bash
git status
```

Jika muncul pesan seperti:

```
Your branch is behind 'origin/main' by 2 commits.
```

Berarti ada perubahan terbaru dari tim yang belum kamu ambil.

### 3. Ambil Update Terbaru dari GitHub

Untuk menyinkronkan dengan versi terbaru di GitHub, jalankan:

```bash
git pull origin main
```

---

## ✍️ Menyimpan dan Mengirim Perubahan ke GitHub

Setelah kamu mengedit atau menambahkan file ke project, lakukan langkah-langkah berikut:

### 1. Cek Perubahan

```bash
git status
```

### 2. Tambahkan Semua Perubahan

```bash
git add .
```

### 3. Commit Perubahan

Buat commit dengan pesan yang jelas:

```bash
git commit -m "Deskripsikan perubahan yang kamu buat"
```

Contoh:

```bash
git commit -m "Menambahkan fitur login di frontend dan API auth di backend"
```

### 4. Push ke GitHub

Kirim perubahan ke repository GitHub:

```bash
git push origin main
```

---

## 💡 Tips Kolaborasi Tim

- ✅ Selalu jalankan `git pull origin main` sebelum memulai kerja.
- ✅ Gunakan pesan commit yang jelas dan singkat.
- ✅ Gunakan branch baru jika ingin mengerjakan fitur secara terpisah.
- ✅ Gunakan `.gitignore` agar file yang tidak penting tidak ikut ke GitHub (misalnya: `node_modules`, `.env`, `vendor/`, dll).

---

## 🧹 Contoh `.gitignore` Laravel dan Ionic

### 📂 backend/.gitignore (Laravel)

```
/vendor/
/node_modules/
.env
.DS_Store
/public/storage
/storage/*.key
/database/*.sqlite
.idea/
*.log
*.lock
```

### 📂 frontend/.gitignore (Ionic/Angular)

```
/node_modules/
/www/
/dist/
/build/
/coverage/
*.log
*.lock
.env
.DS_Store
.idea/
```

---

### 📂 System Requirement 

```bash
composer require simplesoftwareio/simple-qrcode
```

Semoga panduan ini membantu semua anggota tim untuk bekerjasama dengan lancar dan rapi 🚀