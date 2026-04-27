<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$selected_grade = isset($_GET['grade']) ? (int)$_GET['grade'] : 'all';


try {
    $stmt = $pdo->query("SELECT * FROM lectures ORDER BY id DESC");
    $lectures = $stmt->fetchAll();
} catch (PDOException $e) {
    $lectures = [];
}


$grades_map = [
    1 => "الأول الإعدادي",
    2 => "الثاني الإعدادي",
    3 => "الثالث الإعدادي",
    4 => "الأول الثانوي",
    5 => "الثاني الثانوي",
    6 => "الثالث الثانوي"
];
?>
<?php include 'footer.php'; ?>

<!DOCTYPE html>
<html lang="ar" dir="rtl" id="main-html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مكتبة المحاضرات | منصة القيصر</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;700;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { darkMode: 'class' }
        if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');
    </script>
    <style>
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; transition: background-color 0.3s ease; }
        .glass-nav { backdrop-filter: blur(10px); background-color: rgba(255, 255, 255, 0.8); }
        .dark .glass-nav { background-color: rgba(15, 23, 42, 0.8); }
        .card-hover { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .card-hover:hover { transform: translateY(-12px); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen">

    <nav class="glass-nav sticky top-0 z-50 border-b border-gray-200 dark:border-slate-700 p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-black shadow-lg">ع</div>
                <h1 class="text-xl font-black hidden md:block tracking-tight">منصة القيصر</h1>
            </div>
            
            <div class="flex items-center gap-3 md:gap-6">
                <span class="bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 px-4 py-2 rounded-2xl text-sm font-black shadow-sm">
                    💰 رصيدك: <?php echo $_SESSION['user_balance'] ?? 0; ?> ج.م
                </span>
                <button id="theme-toggle" class="p-2.5 bg-gray-100 dark:bg-slate-800 rounded-xl hover:scale-110 transition active:scale-95">
                   <span id="theme-icon">🌙</span>
                </button>
                <a href="index.php" class="text-sm font-black text-blue-600 dark:text-blue-400 hover:underline font-bold">الرئيسية</a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto pt-16 pb-10 px-4 text-right">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="space-y-2">
                <h2 class="text-4xl md:text-5xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-900 to-blue-600 dark:from-white dark:to-blue-400">
                    مكتبة المحاضرات
                </h2>
                <p class="text-gray-500 dark:text-gray-400 font-bold max-w-md">ابدأ رحلة التفوق مع أقوى المحاضرات التعليمية.</p>
            </div>
            
            <div class="flex gap-2 overflow-x-auto pb-4 no-scrollbar" id="filters-container">
                <button data-filter="all" class="filter-btn <?php echo ($selected_grade === 'all') ? 'active-filter bg-blue-600 text-white shadow-blue-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300'; ?> px-8 py-3 rounded-2xl text-sm font-black whitespace-nowrap shadow-lg transition-all border border-transparent">الكل</button>
                
                <?php foreach($grades_map as $id => $name): ?>
                <button data-filter="<?php echo $id; ?>" 
                        class="filter-btn <?php echo ($selected_grade == $id) ? 'active-filter bg-blue-600 text-white shadow-blue-500/20' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300'; ?> px-7 py-3 rounded-2xl text-sm font-black whitespace-nowrap border border-gray-200 dark:border-slate-700 hover:border-blue-600 transition-all">
                    <?php echo $name; ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto pb-20 px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="lectures-grid">
            <?php if(empty($lectures)): ?>
                <div class="col-span-full text-center py-20 bg-white dark:bg-slate-800 rounded-[3rem] shadow-sm border border-dashed border-gray-200 dark:border-slate-700">
                    <p class="text-2xl font-black text-gray-400 italic">لا توجد محاضرات متاحة في الوقت الحالي.. 📢</p>
                </div>
            <?php endif; ?>

            <?php foreach($lectures as $lecture): ?>
            <div data-grade="<?php echo $lecture['target_grade']; ?>" 
                 class="lecture-card group card-hover bg-white dark:bg-slate-800 rounded-[2.5rem] overflow-hidden border border-gray-100 dark:border-slate-700 flex flex-col h-full shadow-sm"
                 style="<?php echo ($selected_grade !== 'all' && $lecture['target_grade'] != $selected_grade) ? 'display: none;' : ''; ?>">
                
                <div class="relative overflow-hidden aspect-video">
                    <img src="uploads/posters/<?php echo htmlspecialchars($lecture['thumbnail']); ?>" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" 
                         onerror="this.src='https://placehold.co/600x400/1e3a8a/white?text=Mr.kaiser+Lecture'">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-60"></div>
                    
                    <div class="absolute top-4 right-4">
                        <span class="bg-blue-600/90 backdrop-blur-md text-white text-[10px] font-black px-3 py-1.5 rounded-xl uppercase shadow-lg">
                            <?php echo ($lecture['content_type'] == 'revision') ? 'مراجعة نهائية 🔥' : 'حصة أساسية 📚'; ?>
                        </span>
                    </div>
                </div>

                <div class="p-8 flex flex-col flex-1">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <span class="text-xs font-black text-blue-600 dark:text-blue-400 tracking-wider">
                            <?php echo $grades_map[$lecture['target_grade']] ?? 'عام'; ?>
                        </span>
                    </div>
                    
                    <h3 class="text-xl font-black mb-3 line-clamp-2 leading-snug group-hover:text-blue-600 transition">
                        <?php echo htmlspecialchars($lecture['title']); ?>
                    </h3>
                    
                    <p class="text-gray-500 dark:text-gray-400 text-sm line-clamp-2 mb-8 font-medium">
                        <?php echo htmlspecialchars($lecture['description'] ?? '' ); ?>
                    </p>

                    <div class="mt-auto pt-6 border-t border-gray-50 dark:border-slate-700 flex justify-between items-center">
                        <div class="flex flex-col">
                            <p class="text-[10px] text-gray-400 font-black uppercase">قيمة الاشتراك</p>
                            <span class="text-2xl font-black text-blue-900 dark:text-white">
                                <?php echo ($lecture['price'] == 0) ? 'مجانية' : number_format($lecture['price'], 0) . ' <small class="text-xs">ج.م</small>'; ?>
                            </span>
                        </div>
                        <a href="view_lecture.php?id=<?php echo $lecture['id']; ?>" 
                           class="bg-blue-600 hover:bg-yellow-400 hover:text-blue-900 text-white px-8 py-3.5 rounded-2xl font-black transition-all duration-300 shadow-xl shadow-blue-500/20 active:scale-95">
                            شاهد الآن
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Theme Toggle Logic
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        if(localStorage.getItem('theme') === 'dark') themeIcon.innerText = '☀️';

        themeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            const isDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            themeIcon.innerText = isDark ? '☀️' : '🌙';
        });

        // Smart Filtering Logic
        const filterButtons = document.querySelectorAll('.filter-btn');
        const lectureCards = document.querySelectorAll('.lecture-card');

        filterButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const filterValue = btn.getAttribute('data-filter');

                // Update UI Buttons
                filterButtons.forEach(b => {
                    b.classList.remove('bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-500/20');
                    b.classList.add('bg-white', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300');
                });
                btn.classList.add('bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-500/20');
                btn.classList.remove('bg-white', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300');

                // Animate and Filter Cards
                lectureCards.forEach(card => {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    
                    setTimeout(() => {
                        const cardGrade = card.getAttribute('data-grade');
                        if (filterValue === 'all' || cardGrade === filterValue) {
                            card.style.display = 'flex';
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'scale(1)';
                            }, 50);
                        } else {
                            card.style.display = 'none';
                        }
                    }, 300);
                });
            });
        });
    </script>
</body>
</html>