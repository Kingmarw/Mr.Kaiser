<?php 
require_once 'header.php';
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$count_students = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'")->fetchColumn();


$count_lectures = $pdo->query("SELECT COUNT(*) FROM lectures")->fetchColumn();


$total_revenue = $pdo->query("SELECT SUM(price_paid) FROM purchases")->fetchColumn() ?? 0;


$available_codes = $pdo->query("SELECT COUNT(*) FROM recharge_codes WHERE is_used = 0")->fetchColumn();
?>

<div class="flex-1">
    <header class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-800 dark:text-white">إحصائيات المنصة</h1>
            <p class="text-gray-500">نظرة عامة على أداء موقعك اليوم</p>
        </div>
        <div class="bg-white dark:bg-slate-800 px-6 py-3 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 font-bold">
            أهلاً يا مستر أحمد 👋
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white dark:bg-slate-800 p-6 rounded-[2rem] shadow-sm border-b-4 border-blue-600 transition hover:scale-105">
            <div class="flex items-center justify-between mb-4">
                <span class="text-3xl">👥</span>
                <span class="bg-blue-100 text-blue-600 text-xs font-black px-2 py-1 rounded-lg">نشط</span>
            </div>
            <p class="text-gray-500 dark:text-gray-400 font-bold">إجمالي الطلاب</p>
            <h3 class="text-4xl font-black mt-2 text-slate-800 dark:text-white">
                <?php echo number_format($count_students); ?>
            </h3>
        </div>

        <div class="bg-white dark:bg-slate-800 p-6 rounded-[2rem] shadow-sm border-b-4 border-yellow-400 transition hover:scale-105">
            <div class="flex items-center justify-between mb-4">
                <span class="text-3xl">🎥</span>
                <span class="bg-yellow-100 text-yellow-600 text-xs font-black px-2 py-1 rounded-lg">أونلاين</span>
            </div>
            <p class="text-gray-500 dark:text-gray-400 font-bold">المحاضرات</p>
            <h3 class="text-4xl font-black mt-2 text-slate-800 dark:text-white">
                <?php echo number_format($count_lectures); ?>
            </h3>
        </div>

        <div class="bg-white dark:bg-slate-800 p-6 rounded-[2rem] shadow-sm border-b-4 border-green-500 transition hover:scale-105">
            <div class="flex items-center justify-between mb-4">
                <span class="text-3xl">💰</span>
                <span class="bg-green-100 text-green-600 text-xs font-black px-2 py-1 rounded-lg">إجمالي</span>
            </div>
            <p class="text-gray-500 dark:text-gray-400 font-bold">إجمالي المبيعات</p>
            <h3 class="text-4xl font-black mt-2 text-slate-800 dark:text-white">
                <?php echo number_format($total_revenue); ?> <small class="text-sm">ج.م</small>
            </h3>
        </div>

        <div class="bg-white dark:bg-slate-800 p-6 rounded-[2rem] shadow-sm border-b-4 border-purple-500 transition hover:scale-105">
            <div class="flex items-center justify-between mb-4">
                <span class="text-3xl">🎫</span>
                <span class="bg-purple-100 text-purple-600 text-xs font-black px-2 py-1 rounded-lg">متاح</span>
            </div>
            <p class="text-gray-500 dark:text-gray-400 font-bold">أكواد جاهزة</p>
            <h3 class="text-4xl font-black mt-2 text-slate-800 dark:text-white">
                <?php echo number_format($available_codes); ?>
            </h3>
        </div>

    </div>

    <div class="mt-10 bg-white dark:bg-slate-800 rounded-[2.5rem] p-8 shadow-sm border border-gray-100 dark:border-slate-700">
        <h2 class="text-xl font-black mb-6 flex items-center gap-2">
            <span>🆕</span> آخر الطلاب المنضمين
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="text-gray-400 text-sm border-b border-gray-50 dark:border-slate-700">
                        <th class="pb-4">الطالب</th>
                        <th class="pb-4">رقم الهاتف</th>
                        <th class="pb-4">الرصيد</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                    <?php
                    $latest_students = $pdo->query("SELECT * FROM users WHERE role = 'student' ORDER BY id DESC LIMIT 5")->fetchAll();
                    foreach($latest_students as $student):
                    ?>
                    <tr>
                        <td class="py-4 font-bold"><?php echo $student['full_name']; ?></td>
                        <td class="py-4 text-gray-500"><?php echo $student['phone']; ?></td>
                        <td class="py-4 text-green-600 font-black"><?php echo $student['balance']; ?> ج.م</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>