<?php
session_start();
include __DIR__ . '/../config.php';

// دریافت آخرین بک‌گراند
$bg_image = '../assets/bg/default.jpg';
$bg_result = mysqli_query($conn, "SELECT filename FROM backgrounds ORDER BY uploaded_at DESC LIMIT 1");
if ($bg_result && mysqli_num_rows($bg_result) > 0) {
    $bg_row = mysqli_fetch_assoc($bg_result);
    $bg_image = '../assets/bg/' . $bg_row['filename'];
}

// دسته‌بندی‌ها - یک بار اجرا می‌شود
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

// پارامترهای جستجو
$search_keyword = $_GET['search'] ?? '';
$category_id = $_GET['category_id'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$availability = $_GET['availability'] ?? 'all';
$sort_by = $_GET['sort_by'] ?? 'newest';

// ساخت کوئری داینامیک برای غذاها
$where_conditions = ["is_available = 1"];
$params = [];
$types = '';

if (!empty($category_id)) {
    $where_conditions[] = "category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
}

if (!empty($search_keyword)) {
    $where_conditions[] = "(name LIKE ? OR description LIKE ? OR ingredients LIKE ?)";
    $params[] = "%$search_keyword%";
    $params[] = "%$search_keyword%";
    $params[] = "%$search_keyword%";
    $types .= 'sss';
}

if (!empty($min_price)) {
    $where_conditions[] = "price >= ?";
    $params[] = $min_price;
    $types .= 'i';
}

if (!empty($max_price)) {
    $where_conditions[] = "price <= ?";
    $params[] = $max_price;
    $types .= 'i';
}

if ($availability === 'available') {
    $where_conditions[] = "is_available = 1";
} elseif ($availability === 'unavailable') {
    $where_conditions[] = "is_available = 0";
}

// مرتب‌سازی
$order_by = "id DESC";
switch ($sort_by) {
    case 'price_low': $order_by = "price ASC"; break;
    case 'price_high': $order_by = "price DESC"; break;
    case 'name': $order_by = "name ASC"; break;
    case 'popular': $order_by = "views DESC"; break;
    default: $order_by = "id DESC"; break;
}

$sql = "SELECT * FROM menu_items WHERE " . implode(" AND ", $where_conditions) . " ORDER BY $order_by";

if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $foods = $stmt->get_result();
} else {
    $foods = mysqli_query($conn, $sql);
}

$total_results = $foods ? mysqli_num_rows($foods) : 0;
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رستوران مدرن بهارستان | تجربه‌ای از طعم‌های بی‌نظیر</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* پالت رنگی شفاف و روشن */
            --primary-gold: #E8B959;    /* طلایی روشن و شفاف */
            --accent-rose: #D98F8F;     /* رزگلد شفاف */
            --bronze: #CD7F32;          /* برنزی */
            --pure-white: #FFFFFF;
            --cream-bg: #FFFBF0;        /* زمینه کرمی روشن */
            --light-cream: #F8F5EB;
            --text-dark: #2C2C2C;
            --text-light: #666666;
            --border-light: #E8E4D9;
            
            /* سایه‌های روشن */
            --shadow-soft: rgba(232, 185, 89, 0.15);
            --shadow-medium: rgba(232, 185, 89, 0.25);
            --shadow-strong: rgba(232, 185, 89, 0.35);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Vazirmatn', sans-serif;
            background: var(--cream-bg);
            color: var(--text-dark);
            overflow-x: hidden;
            line-height: 1.8;
        }

        /* Preloader */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--pure-white);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .loader {
            width: 80px;
            height: 80px;
            border: 4px solid var(--primary-gold);
            border-top: 4px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Navigation - Modern */
        .luxury-nav {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-bottom: 2px solid var(--primary-gold);
            padding: 1.2rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px var(--shadow-soft);
        }

        .nav-scrolled {
            background: rgba(255, 255, 255, 0.99);
            padding: 0.8rem 0;
        }

        .nav-brand {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary-gold), var(--bronze));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .nav-link {
            color: var(--text-dark) !important;
            font-weight: 500;
            margin: 0 1rem;
            padding: 0.5rem 1rem !important;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-gold) !important;
            transform: translateY(-2px);
        }

        .nav-link.active {
            background: linear-gradient(45deg, var(--primary-gold), var(--bronze));
            color: white !important;
        }

        /* Hero Section */
        .modern-hero {
            height: 100vh;
            background: linear-gradient(rgba(255, 251, 240, 0.9), rgba(255, 251, 240, 0.95)), url('<?= $bg_image ?>');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-top: 76px;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: 4.5rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--primary-gold), var(--bronze));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
        }

        .hero-subtitle {
            font-size: 1.4rem;
            color: var(--text-light);
            margin-bottom: 2rem;
            font-weight: 300;
        }

        .btn-modern {
            background: linear-gradient(45deg, var(--primary-gold), var(--bronze));
            border: none;
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px var(--shadow-medium);
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px var(--shadow-strong);
            color: white;
        }

        /* Search Section */
        .search-section {
            background: white;
            border: 2px solid var(--primary-gold);
            border-radius: 20px;
            padding: 3rem;
            margin: -50px auto 50px;
            position: relative;
            z-index: 10;
            box-shadow: 0 20px 60px var(--shadow-soft);
            max-width: 1200px;
        }

        .search-header {
            color: var(--primary-gold);
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
        }

        /* Food Grid */
        .food-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 2.5rem;
            padding: 2rem 0;
        }

        .modern-food-card {
            background: white;
            border: 2px solid var(--border-light);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s ease;
            position: relative;
            box-shadow: 0 10px 30px var(--shadow-soft);
        }

        .modern-food-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px var(--shadow-medium);
            border-color: var(--primary-gold);
        }

        .food-image-container {
            height: 250px;
            overflow: hidden;
            position: relative;
        }

        .food-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .modern-food-card:hover .food-image {
            transform: scale(1.1);
        }

        .food-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(45deg, var(--primary-gold), var(--bronze));
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .food-content {
            padding: 2rem;
        }

        .food-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
            font-family: 'Playfair Display', serif;
        }

        .food-description {
            color: var(--text-light);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .food-price {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--primary-gold);
            margin-bottom: 1.5rem;
        }

        /* Floating Cart */
        .floating-cart {
            position: fixed;
            bottom: 30px;
            left: 30px;
            z-index: 1000;
        }

        .cart-btn {
            width: 65px;
            height: 65px;
            background: linear-gradient(45deg, var(--primary-gold), var(--bronze));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 10px 30px var(--shadow-medium);
            transition: all 0.3s ease;
            position: relative;
        }

        .cart-btn:hover {
            transform: scale(1.1);
            color: white;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--accent-rose);
            color: white;
            border-radius: 50%;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            border: 2px solid white;
        }

        /* Footer */
        .modern-footer {
            background: white;
            border-top: 2px solid var(--primary-gold);
            padding: 4rem 0 2rem;
            margin-top: 6rem;
        }

        /* Form Controls */
        .form-control, .form-select {
            border: 2px solid var(--border-light);
            border-radius: 12px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-gold);
            box-shadow: 0 0 0 3px var(--shadow-soft);
        }

        /* Badge */
        .badge-modern {
            background: linear-gradient(45deg, var(--primary-gold), var(--bronze));
            color: white;
            padding: 6px 15px;
            border-radius: 15px;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title { font-size: 3rem; }
            .search-section { margin: -30px 20px 30px; padding: 2rem; }
            .food-grid { grid-template-columns: 1fr; }
            .floating-cart { bottom: 20px; left: 20px; }
        }
    </style>
</head>
<body>

<!-- Preloader -->
<div class="preloader" id="preloader">
    <div class="loader"></div>
</div>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg luxury-nav" id="mainNav">
    <div class="container">
        <a class="navbar-brand nav-brand" href="#">
            <i class="fas fa-utensils me-2"></i>
            بهارستان
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#home">خانه</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#menu">منو</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">درباره ما</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#gallery">گالری</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">رزرو میز</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="modern-hero" id="home">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">رستوران مدرن بهارستان</h1>
            <p class="hero-subtitle">تجربه‌ای بی‌نظیر از طعم‌های اصیل در فضایی مدرن و دل‌نشین</p>
            <a href="#menu" class="btn btn-modern">
                <i class="fas fa-utensils me-2"></i>
                مشاهده منو
            </a>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="container">
    <div class="search-section">
        <h2 class="search-header">
            <i class="fas fa-search me-2"></i>
            جستجوی پیشرفته در منو
        </h2>
        
        <form method="GET" action="">
            <div class="row g-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control form-control-lg" 
                           placeholder="🔍 نام غذا، مواد اولیه..." value="<?= htmlspecialchars($search_keyword) ?>">
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select form-select-lg">
                        <option value="">همه دسته‌بندی‌ها</option>
                        <?php 
                        mysqli_data_seek($categories, 0);
                        while ($cat = mysqli_fetch_assoc($categories)): 
                        ?>
                            <option value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="sort_by" class="form-select form-select-lg">
                        <option value="newest" <?= $sort_by == 'newest' ? 'selected' : '' ?>>جدیدترین</option>
                        <option value="price_low" <?= $sort_by == 'price_low' ? 'selected' : '' ?>>ارزان‌ترین</option>
                        <option value="price_high" <?= $sort_by == 'price_high' ? 'selected' : '' ?>>گران‌ترین</option>
                        <option value="name" <?= $sort_by == 'name' ? 'selected' : '' ?>>بر اساس نام</option>
                        <option value="popular" <?= $sort_by == 'popular' ? 'selected' : '' ?>>محبوب‌ترین</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-modern w-100">
                        <i class="fas fa-filter me-1"></i>
                        فیلتر
                    </button>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">💰 از</span>
                        <input type="number" name="min_price" class="form-control" 
                               placeholder="حداقل قیمت" value="<?= htmlspecialchars($min_price) ?>">
                        <span class="input-group-text">💰 تا</span>
                        <input type="number" name="max_price" class="form-control" 
                               placeholder="حداکثر قیمت" value="<?= htmlspecialchars($max_price) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="availability" class="form-select">
                        <option value="all" <?= $availability == 'all' ? 'selected' : '' ?>>همه موارد</option>
                        <option value="available" <?= $availability == 'available' ? 'selected' : '' ?>>فقط موجود</option>
                    </select>
                </div>
            </div>
        </form>
        
        <?php if ($search_keyword || $category_id || $min_price || $max_price): ?>
            <div class="alert alert-light mt-4 border-primary">
                <i class="fas fa-info-circle me-2 text-primary"></i>
                <strong class="text-primary"><?= $total_results ?></strong> 
                <span class="text-dark">مورد یافت شد</span>
                
                <?php if ($search_keyword): ?>
                    <span class="badge badge-modern me-2"><?= htmlspecialchars($search_keyword) ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Menu Grid -->
<section class="container" id="menu">
    <div class="text-center mb-5">
        <h2 class="hero-title mb-3">منوی بهارستان</h2>
        <p class="hero-subtitle">انتخابی از بهترین طعم‌ها با بالاترین کیفیت</p>
    </div>

    <div class="food-grid">
        <?php if ($foods && mysqli_num_rows($foods) > 0): ?>
            <?php while ($item = mysqli_fetch_assoc($foods)): ?>
                <div class="modern-food-card">
                    <div class="food-image-container">
                        <?php if (!empty($item['image'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($item['image']) ?>" class="food-image" alt="<?= htmlspecialchars($item['name']) ?>">
                        <?php else: ?>
                            <div class="food-image bg-light d-flex align-items-center justify-content-center">
                                <i class="fas fa-utensils fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($item['is_available']): ?>
                            <span class="food-badge">
                                <i class="fas fa-check me-1"></i>
                                موجود
                            </span>
                        <?php else: ?>
                            <span class="food-badge bg-secondary">
                                <i class="fas fa-clock me-1"></i>
                                به زودی
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="food-content">
                        <h3 class="food-title"><?= htmlspecialchars($item['name']) ?></h3>
                        
                        <?php if (!empty($item['description'])): ?>
                            <p class="food-description"><?= htmlspecialchars($item['description']) ?></p>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="food-price">
                                <?= number_format($item['price']) ?>
                                <small class="text-muted">تومان</small>
                            </div>
                        </div>
                        
                        <form class="add-to-cart-form">
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            <div class="input-group">
                                <input type="number" name="qty" value="1" min="1" 
                                       class="form-control" 
                                       style="max-width: 100px;"
                                       <?= !$item['is_available'] ? 'disabled' : '' ?>>
                                <button type="submit" class="btn btn-modern flex-grow-1" 
                                        <?= !$item['is_available'] ? 'disabled' : '' ?>>
                                    <i class="fas fa-cart-plus me-2"></i>
                                    افزودن به سبد
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-4x text-muted mb-4"></i>
                <h4 class="text-muted mb-3">هیچ موردی یافت نشد</h4>
                <p class="text-muted">لطفاً معیارهای جستجو را تغییر دهید</p>
                <a href="index.php" class="btn btn-modern mt-3">
                    <i class="fas fa-redo me-2"></i>
                    نمایش همه موارد
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Floating Cart -->
<div class="floating-cart">
    <a href="cart.php" class="cart-btn">
        <i class="fas fa-shopping-cart fa-lg text-white"></i>
        <span id="cart-count" class="cart-count">0</span>
    </a>
</div>

<!-- Footer -->
<footer class="modern-footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h4 class="nav-brand mb-3">بهارستان</h4>
                <p class="text-dark">
                    ارائه بهترین تجربه غذایی در فضایی مدرن با سرویس‌دهی اختصاصی و کیفیت بی‌نظیر
                </p>
            </div>
            <div class="col-lg-4 mb-4">
                <h5 class="text-dark mb-3">دسترسی سریع</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="#home" class="btn btn-outline-primary btn-sm">خانه</a>
                    <a href="#menu" class="btn btn-outline-primary btn-sm">منو</a>
                    <a href="#gallery" class="btn btn-outline-primary btn-sm">گالری</a>
                    <a href="#contact" class="btn btn-outline-primary btn-sm">رزرو</a>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <h5 class="text-dark mb-3">شبکه‌های اجتماعی</h5>
                <div class="social-links">
                    <a href="#" class="btn btn-modern btn-sm me-2"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="btn btn-modern btn-sm me-2"><i class="fab fa-telegram"></i></a>
                    <a href="#" class="btn btn-modern btn-sm"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
        <div class="text-center pt-4 border-top border-light">
            <p class="text-muted mb-0">
                <i class="fas fa-heart me-2 text-primary"></i>
                © 2024 رستوران مدرن بهارستان. تمام حقوق محفوظ است.
            </p>
        </div>
    </div>
</footer>

<script src="../assets/js/jquery-3.6.0.min.js"></script>
<script src="../assets/js/bootstrap.bundle.min.js"></script>

<script>
// Preloader
window.addEventListener('load', function() {
    setTimeout(function() {
        document.getElementById('preloader').style.opacity = '0';
        setTimeout(function() {
            document.getElementById('preloader').style.display = 'none';
        }, 500);
    }, 1000);
});

// Navbar Scroll Effect
window.addEventListener('scroll', function() {
    if (window.scrollY > 100) {
        document.getElementById('mainNav').classList.add('nav-scrolled');
    } else {
        document.getElementById('mainNav').classList.remove('nav-scrolled');
    }
});

// Cart Functions
function updateCartCount() {
    $.get("./cart_count.php", function(count) {
        $("#cart-count").text(count);
    });
}

function showNotification(message, type) {
    const notification = $(`
        <div class="alert alert-${type} position-fixed" 
             style="top: 100px; right: 20px; z-index: 9999; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
        </div>
    `);
    $('body').append(notification);
    setTimeout(() => notification.fadeOut(), 3000);
}

$(document).ready(function() {
    updateCartCount();

    // Add to Cart
    $(".add-to-cart-form").submit(function(e) {
        e.preventDefault();
        const form = $(this);
        const card = form.closest('.modern-food-card');
        const id = form.find("input[name='id']").val();
        const qty = form.find("input[name='qty']").val();

        card.css('transform', 'scale(0.95)');
        
        $.get("./add_to_cart.php", { id: id, qty: qty }, function(res) {
            card.css('transform', '');
            
            if (res.trim() === "added") {
                updateCartCount();
                showNotification('با موفقیت به سبد خرید اضافه شد', 'success');
            } else {
                showNotification('خطا در افزودن به سبد خرید', 'error');
            }
        });
    });

    // Smooth scrolling
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $($(this).attr('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 1000);
        }
    });
});
</script>

</body>
</html>