<?php
/**
 * Helper bersama untuk seluruh aplikasi Feane.
 */

require_once __DIR__ . '/../config/database.php';

/** Escape output HTML (anti-XSS). */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Ambil semua kategori (urut nama). */
function getCategories(): array
{
    return getDB()->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll();
}

/**
 * Ambil item menu beserta slug kategorinya.
 * @param string|null $categorySlug filter opsional berdasarkan slug kategori
 */
function getMenuItems(?string $categorySlug = null): array
{
    $sql = 'SELECT m.*, c.slug AS category_slug, c.name AS category_name
            FROM menu_items m
            JOIN categories c ON c.id = m.category_id';
    $params = [];

    if ($categorySlug !== null && $categorySlug !== '') {
        $sql .= ' WHERE c.slug = ?';
        $params[] = $categorySlug;
    }
    $sql .= ' ORDER BY m.created_at DESC, m.id DESC';

    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/** Ambil satu item menu by id. */
function getMenuItem(int $id): ?array
{
    $stmt = getDB()->prepare('SELECT * FROM menu_items WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/** Ambil satu kategori by id. */
function getCategory(int $id): ?array
{
    $stmt = getDB()->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/** Buat slug dari teks bebas (untuk kategori). */
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

/* ----------------------- Autentikasi admin ----------------------- */

function ensureSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn(): bool
{
    ensureSession();
    return !empty($_SESSION['admin_id']);
}

/** Pasang di awal tiap halaman admin yang butuh login. */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: admin.php');
        exit;
    }
}

function currentAdminName(): string
{
    ensureSession();
    return $_SESSION['admin_username'] ?? '';
}
