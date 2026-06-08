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

## 3. Bukti Pengujian (screenshot)

> _Lampirkan screenshot di sini:_
- [x] Create instance & setting port (securty group)
<img width="1600" height="797" alt="image" src="https://github.com/user-attachments/assets/75bd85c4-31d9-4f8b-8799-ac2e88f7d0d7" />
<img width="1600" height="745" alt="image" src="https://github.com/user-attachments/assets/5056fd66-3384-40c4-8f3f-fbeb744fbc78" />

- [x] GitHub Actions berstatus sukses (centang hijau)
<img width="1919" height="950" alt="image" src="https://github.com/user-attachments/assets/55cf7250-faa6-4bb8-8d8e-ab772e5a833b" />


- [x] `http://13.55.219.242/` (web statis, port 80)
  <img width="1600" height="798" alt="image" src="https://github.com/user-attachments/assets/d6c6c937-0010-4b61-b9ad-56240fae90ea" />

- [x] `http://13.55.219.242:8080/` (web dinamis) + admin login
<img width="1600" height="792" alt="image" src="https://github.com/user-attachments/assets/8097d31c-145f-44dc-a901-41a216f70678" />


- [x] `docker compose ps` di EC2 (semua container Up & healthy)
<img width="1600" height="794" alt="image" src="https://github.com/user-attachments/assets/b9c5a841-14ab-4112-aab4-372e2e107030" />
<img width="1600" height="790" alt="image" src="https://github.com/user-attachments/assets/d5316bcd-e5bb-40ad-8355-a4b5256d10fa" />
<img width="1600" height="740" alt="image" src="https://github.com/user-attachments/assets/774af0a3-51f6-4d19-b004-7fbbf4b48596" />
<img width="1919" height="948" alt="image" src="https://github.com/user-attachments/assets/6eea1502-a4a7-4d13-8192-f2c9b8dae02f" />



---

**NIM:** 2388010007 · **Mata Kuliah:** Administrasi Server (Cloud Computing II)
