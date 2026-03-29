# To-Do-List | Documentation Setup

Welcome to this repository. This project is built using a modern architecture with Laravel as the API, Vue.js (Vite) as the frontend, and runs entirely within Docker to ensure a consistent development environment.

<p align="left">
  <img src="https://img.shields.io/badge/Laravel-13-red?logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/Vue-3-42b883?logo=vue.js&logoColor=white" />
  <img src="https://img.shields.io/badge/Docker-Containerized-2496ED?logo=docker&logoColor=white" />
</p>

#

## 🚀 Tech Stack & Infrastructure
***
| Category          | Stack                               |
|-------------------|-------------------------------------|
| ⚙️ Backend        | Laravel 13                          |
| 🎨 Frontend       | Vue.js 3 + Tailwind CSS             |
| 🗄️ Database       | PostgreSQL                          |
| ⚡ Cache & Queue  | Redis                               |
| 🔄 Worker         | Laravel Queue Worker                |
| 🐳 Infrastructure | Docker Compose                      |

#
> ⚠️ **Prasyarat**  
> Sebelum memulai, pastikan Anda sudah menginstal:
> - Docker Desktop  
> - Git

#
## ⚙️ Langkah Instalasi & Setup

Ikuti langkah-langkah di bawah ini untuk menjalankan proyek di mesin lokal Anda:

---

### 1️⃣ Clone Repositori
Clone project dari repository dan masuk ke direktori:

```bash
# Clone repository
git clone https://github.com/username/repository-name.git

# Masuk ke folder project
cd repository-name
```
### 2️⃣ Setup Environment File

Salin file `.env.example` menjadi `.env`.

> [!WARNING]
> ⚠️ Pastikan konfigurasi **database** dan **Redis** diarahkan ke nama service Docker.

```bash
# Copy file environment
cp .env.example .env
```

### 3️⃣ Build dan Jalankan Container

Gunakan Docker Compose untuk membangun image dan menjalankan seluruh service:

- App (Laravel)  
- Database (PostgreSQL)  
- Redis  
- Worker (Queue)  
- Frontend (Vue)  

```bash
# Build dan jalankan container
docker compose up -d --build
```

### 4️⃣ Instalasi Dependency (PHP & JS)

Instal dependency backend dan frontend di dalam container:

```bash
# Install Composer dependencies (Laravel)
docker compose exec app composer install

# Install NPM dependencies (Vue)
docker compose exec frontend npm install
```
### 5️⃣ Generate Application Key & Storage Link

Generate key Laravel dan buat symbolic link untuk storage:

```bash
# Generate app key
docker compose exec app php artisan key:generate

# Link storage ke public
docker compose exec app php artisan storage:link
```
### 6️⃣ Database Migration & Seeding

Pastikan container PostgreSQL sudah siap, lalu jalankan migrasi dan seeder:

```bash
# Migrasi database + seeding
docker compose exec app php artisan migrate --seed
```

___
> [!TIP]
> 💡 **NB:** Setup dependency **PHP (Composer)** dan **JavaScript (NPM)** sudah dikonfigurasi otomatis di dalam masing-masing container melalui `Dockerfile`.  
> Sehingga Anda **tidak perlu instal manual di lokal** — cukup jalankan:
> 
> - `docker compose up -d --build`
> 
> dan seluruh environment akan langsung siap digunakan 🚀
>
> ---
>
> [!WARNING]
> ⚠️ Pastikan Anda telah membuat file `.env` dari `.env.example`,  
> serta mengisi setiap value konfigurasi (database, Redis, dll) sesuai dengan environment Docker yang digunakan.


## ✅ Selesai

Aplikasi sekarang sudah siap dijalankan 🎉

---
