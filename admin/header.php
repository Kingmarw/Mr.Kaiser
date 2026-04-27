<?php include 'auth_check.php'; 

if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl" id="main-html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js" integrity="sha512-6BTOlkauINO65nLhXhthZMtepgJSghyimIalb+crKRPhvhmsCdnIuGcVbR5/aQY2A+260iC1OPy1oCdB6pSSwQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen">
    
    <div class="md:hidden bg-white dark:bg-slate-800 border-b border-gray-100 dark:border-slate-700 p-4 flex justify-between items-center sticky top-0 z-50">
        <h1 class="text-xl font-black text-blue-600">لوحة التحكم</h1>
        <button id="mobile-menu-btn" class="p-2 bg-gray-100 dark:bg-slate-700 rounded-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
    </div>

    <div class="flex relative overflow-hidden h-screen">
        <aside id="sidebar" class="w-64 bg-white dark:bg-slate-800 h-full absolute md:relative z-40 border-l border-gray-100 dark:border-slate-700 p-4 transition-transform duration-300 transform translate-x-full md:translate-x-0 right-0 top-0 overflow-y-auto">
            
            <div class="mb-10 flex justify-between items-center md:block">
                <h1 class="text-2xl font-black text-blue-600 md:text-center">لوحة التحكم</h1>
                <button id="close-sidebar-btn" class="md:hidden p-1 bg-red-100 text-red-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <nav class="space-y-2">
             <a href="../index.php" class="flex items-center gap-3 p-4 rounded-2xl hover:bg-gray-100 dark:hover:bg-slate-700 transition font-bold"><span>🌏</span> الرئيسية</a>
                <a href="index.php" class="flex items-center gap-3 p-4 rounded-2xl hover:bg-gray-100 dark:hover:bg-slate-700 transition font-bold"><span>📊</span> الإحصائيات</a>
                <a href="manage_lectures.php" class="flex items-center gap-3 p-4 rounded-2xl hover:bg-gray-100 dark:hover:bg-slate-700 transition font-bold"><span>📚</span> الحصص والمراجعات</a>
                <a href="manage_students.php" class="flex items-center gap-3 p-4 rounded-2xl hover:bg-gray-100 dark:hover:bg-slate-700 transition font-bold"><span>👥</span> الطلاب</a>
                <a href="generate_codes.php" class="flex items-center gap-3 p-4 rounded-2xl hover:bg-gray-100 dark:hover:bg-slate-700 transition font-bold"><span>🎫</span> أكواد الشحن</a>
                <a href="manage_quizzes.php" class="flex items-center gap-3 p-4 rounded-2xl hover:bg-gray-100 dark:hover:bg-slate-700 transition font-bold"><span>☑</span> بنك الاختبارات</a>
                <a href="requests.php" class="flex items-center gap-3 p-4 rounded-2xl hover:bg-gray-100 dark:hover:bg-slate-700 transition font-bold"><span>💸</span> طلبات الدفع</a>
               <a href=" admin_quiz_results.php" class="flex items-center gap-3 p-4 rounded-2xl hover:bg-gray-100 dark:hover:bg-slate-700 transition font-bold"><span>🧾</span>النتائج</a>
               <a href=" mx_admins.php" class="flex items-center gap-3 p-4 rounded-2xl hover:bg-gray-100 dark:hover:bg-slate-700 transition font-bold"><span>✨</span>التحكم في المشرفين</a>
                <a href="../logout.php" class="flex items-center gap-3 p-4 rounded-2xl text-red-500 hover:bg-red-50 transition font-bold"><span>🚪</span> خروج</a>
            </nav>
        </aside>

        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>
        
        <main class="flex-1 p-4 md:p-8 h-full overflow-y-auto w-full">
    <script src="header.js"></script>
</body>
</html>