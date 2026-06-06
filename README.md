# UAS-2388010007 — Multi-App Deployment (Cloud Computing II)

Repositori UAS mata kuliah **Administrasi Server (Cloud Computing II)**.
Men-deploy **2 aplikasi** ke **AWS EC2** dengan **Docker Compose** dan
**CI/CD GitHub Actions** (Zero-Touch Deployment).

| Aplikasi | Teknologi | Akses |
|----------|-----------|-------|
| **Web Statis** | Apache httpd (HTML/CSS) | `http://13.55.219.242/` (port 80) |
| **Web Dinamis** (Feane) | PHP 8.2 + Apache + MariaDB | `http://13.55.219.242:8080/` (port 8080) |
| **Database** | MariaDB 10.11 (auto-seed) | internal (tidak diekspos publik) |

> Admin panel web dinamis: `http://13.55.219.242:8080/admin/` — login default `admin` / `admin123`.

---

## 1. Arsitektur

```
                         AWS EC2 (UAS-2388010007)
                        ┌───────────────────────────────────────┐
   Internet  ──:80───▶  │  web-statis  (apache httpd)            │
                        │                                        │
   Internet  ──:8080─▶  │  web-dinamis (php:apache) ──▶ db       │
                        │                              (mariadb) │
                        │   docker network: uasnet               │
                        │   volume: db_data (persisten)          │
                        └───────────────────────────────────────┘
            ▲
            │  git push  ──▶  GitHub Actions  ──▶  build & push image (Docker Hub)
            │                                  └─▶  ssh deploy ke EC2 (pull & up -d)
```

### Struktur repo
```
.
├── web-dinamis/            # Aplikasi PHP (Feane) + Dockerfile
│   ├── index.php menu.php about.php book.php
│   ├── admin/              # panel admin (CRUD kategori & menu)
│   ├── config/database.php # koneksi PDO via ENV
│   ├── includes/  sql/init.sql  css/ js/ images/ fonts/
│   └── Dockerfile
├── web-statis/             # Landing page statis + Dockerfile
│   ├── index.html  css/style.css
│   └── Dockerfile
├── docker-compose.yml      # orkestrasi 3 service
└── .github/workflows/      # CI/CD (paths filter terpisah)
    ├── web-dinamis.yml
    └── web-statis.yml
```

---

## 2. Konfigurasi Environment

Variabel dibaca dari `.env` (lihat `.env.example`). **`.env` tidak di-commit** (rahasia).

| Variabel | Keterangan |
|----------|------------|
| `DATABASE_HOST` | `db` (nama service di compose) |
| `DATABASE_NAME` | `feane` |
| `DATABASE_USER` / `DATABASE_PASSWORD` | kredensial app |
| `MYSQL_ROOT_PASSWORD` | password root MariaDB |
| `DOCKERHUB_USERNAME`, `DOCKER_REPO_*` | nama image |

`web-dinamis/config/database.php` membaca `DATABASE_*` dari ENV; bila kosong
(misal saat XAMPP lokal) jatuh ke fallback `127.0.0.1 / root`.

### GitHub Secrets (untuk CI/CD)
| Secret | Contoh |
|--------|--------|
| `DOCKERHUB_USERNAME` | `superdeuuu` |
| `DOCKER_KEY` | Docker Personal Access Token |
| `DOCKER_REPO_DINAMIS` | `web-dinamis` |
| `DOCKER_REPO_STATIS` | `web-statis` |
| `AWS_HOST` | `13.55.219.242` |
| `AWS_USERNAME` | `ubuntu` |
| `AWS_KEY` | isi private key `.pem` |

---

## 3. Menjalankan secara Lokal

### a) XAMPP (web dinamis saja)
1. Taruh `web-dinamis/` di `htdocs`, jalankan Apache + MySQL XAMPP.
2. Import `web-dinamis/sql/init.sql` via phpMyAdmin.
3. Buka `http://localhost/web-dinamis/`.

### b) Docker Compose (full stack)
```bash
docker compose up -d --build
# Web statis  : http://localhost/
# Web dinamis : http://localhost:8080/
```

---

## 4. Setup Awal AWS EC2 (sekali saja)

```bash
# SSH ke EC2
ssh -i kunci.pem ubuntu@13.55.219.242

# Install Docker + plugin compose
sudo apt-get update
sudo apt-get install -y docker.io docker-compose-plugin
sudo usermod -aG docker ubuntu        # agar tanpa sudo (logout-login)

# Siapkan folder kerja deploy
mkdir -p ~/uas-super/web-dinamis/sql
```

**Security Group** (inbound) yang harus dibuka: `22` (SSH), `80` (web statis), `8080` (web dinamis).

---

## 5. CI/CD — Zero-Touch Deployment

Setiap `git push` ke branch `main`:
1. **Paths filter** menentukan workflow mana yang jalan
   (`web-dinamis/**` → `web-dinamis.yml`, `web-statis/**` → `web-statis.yml`)
   sehingga pipeline kedua app **terisolasi** dan hemat runner.
2. Workflow **build → push image** ke Docker Hub.
3. **SCP** `docker-compose.yml` (+ `init.sql`) ke EC2, lalu **SSH**
   `docker compose pull && docker compose up -d` → kontainer di-restart
   dengan image terbaru **tanpa downtime signifikan**.

### Demo Live Test
Ubah teks di `web-statis/index.html` atau menu di web dinamis → `git commit` → `git push`
→ tunggu Actions hijau → refresh browser → perubahan langsung tampil di AWS.

---

## 6. Bukti Pengujian (screenshot)

> _Lampirkan screenshot di sini:_
- [ ] GitHub Actions berstatus sukses (centang hijau)
- [ ] `http://13.55.219.242/` (web statis, port 80)
- [ ] `http://13.55.219.242:8080/` (web dinamis) + admin login
- [ ] `docker compose ps` di EC2 (semua container Up & healthy)
- [ ] Demo Zero-Touch (sebelum & sesudah git push)

---

**NIM:** 2388010007 · **Mata Kuliah:** Administrasi Server (Cloud Computing II)
