<?php 
require_once 'header.php'; 

if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}


if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
        $stmt->execute([$id]);
        $msg = "تم حذف الطالب بنجاح";
    } catch (Exception $e) {
        $error = "لا يمكن حذف الطالب بسبب وجود سجلات مرتبطة به";
    }
}


$search = $_GET['search'] ?? '';
$grade_filter = $_GET['grade'] ?? '';

$query = "SELECT * FROM users WHERE role = 'student'";
$params = [];

if (!empty($search)) {
    $query .= " AND (full_name LIKE ? OR phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($grade_filter)) {
    $query .= " AND student_grade = ?";
    $params[] = $grade_filter;
}

$query .= " ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll();

$grades = [
    1 => 'أولى إعدادي',
    2 => 'تانية إعدادي',
    3 => 'تالتة إعدادي',
    4 => 'أولى ثانوي',
    5 => 'تانية ثانوي',
    6 => 'تالتة ثانوي'
];
?>

<div class="flex-1 w-full max-w-full overflow-x-hidden p-4 md:p-8">
    
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white">إدارة الطلاب</h2>
            <p class="text-gray-500 text-sm md:text-base">إجمالي النتائج: <?php echo count($students); ?></p>
        </div>
        <a href="add_students.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-blue-200 dark:shadow-none flex items-center gap-2">
            <span>+</span> إضافة طالب جديد
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 p-4 rounded-3xl mb-6 border border-gray-100 dark:border-slate-700 shadow-sm">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 right-4 flex items-center text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="ابحث باسم الطالب أو رقم الهاتف..." 
                       class="w-full bg-gray-50 dark:bg-slate-900 border-none pr-11 py-3 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all">
            </div>

            <div class="md:w-64">
                <select name="grade" onchange="this.form.submit()" 
                        class="w-full bg-gray-50 dark:bg-slate-900 border-none py-3 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all">
                    <option value="">كل المراحل الدراسية</option>
                    <?php foreach($grades as $val => $name): ?>
                        <option value="<?php echo $val; ?>" <?php echo $grade_filter == $val ? 'selected' : ''; ?>>
                            <?php echo $name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="bg-slate-800 dark:bg-slate-700 text-white px-8 py-3 rounded-2xl font-bold text-sm hover:bg-slate-900 transition-all">
                تصفية
            </button>
            
            <?php if(!empty($search) || !empty($grade_filter)): ?>
                <a href="?" class="bg-red-50 text-red-600 px-4 py-3 rounded-2xl text-sm font-bold flex items-center justify-center">
                    إلغاء البحث
                </a>
            <?php endif; ?>
        </form>
    </div>

    <?php if(isset($msg)): ?>
        <div class="bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 p-4 rounded-2xl mb-6 border border-green-200 dark:border-green-800 text-sm">
            ✅ <?php echo $msg; ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
        <div class="bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 p-4 rounded-2xl mb-6 border border-red-200 dark:border-red-800 text-sm">
            ⚠️ <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto w-full custom-scrollbar">
            <table class="w-full text-right min-w-[800px]">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-slate-700/50 text-gray-400 text-xs md:text-sm uppercase tracking-wider">
                        <th class="p-5 font-bold">الطالب</th>
                        <th class="p-5 font-bold">رقم الهاتف</th>
                        <th class="p-5 font-bold text-center">الرصيد الحالي</th>
                        <th class="p-5 font-bold text-center">الصف</th>
                        <th class="p-5 font-bold text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                    <?php foreach($students as $s): ?>
                    <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                    <?php
                                    $avatar = !empty($s['avatar_url']) 
                                        ? htmlspecialchars($s['avatar_url']) 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($s['full_name']) . '&background=3b82f6&color=fff&size=128';
                                    ?>

                                    <img 
                                        src="<?php echo $avatar; ?>" 
                                        alt="avatar"
                                        class="w-10 h-10 rounded-xl object-cover border border-gray-200 dark:border-slate-700 shadow-sm"
                                        onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($s['full_name']); ?>&background=3b82f6&color=fff&size=128';"
                                    />
                                <div>
                                    <div class="font-bold text-slate-700 dark:text-slate-200 whitespace-nowrap"><?php echo $s['full_name']; ?></div>
                                    <div class="text-[10px] text-gray-400">انضم في: <?php echo date('Y-m-d', strtotime($s['created_at'] ?? 'now')); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="p-5 text-gray-500 font-mono text-sm whitespace-nowrap">
                            <a href="tel:<?php echo $s['phone']; ?>" class="hover:text-blue-600"><?php echo $s['phone']; ?></a>
                        </td>
                        <td class="p-5 text-center">
                            <span class="inline-block bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 px-3 py-1.5 rounded-lg font-black text-sm whitespace-nowrap">
                                <?php echo number_format($s['balance'], 2); ?> ج.م
                            </span>
                        </td>
                        <td class="p-5 text-center">
                            <span class="inline-block bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-3 py-1.5 rounded-lg font-bold text-sm">
                                <?php echo $grades[$s['student_grade']] ?? 'غير محدد'; ?>
                            </span>
                        </td>
                        <td class="p-5">
                            <div class="flex justify-center gap-2">
                                <a href="view_student.php?id=<?php echo $s['id']; ?>" title="عرض" class="p-2.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-xl hover:bg-blue-100 transition shadow-sm">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="edit_student.php?id=<?php echo $s['id']; ?>" title="تعديل" class="p-2.5 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-xl hover:bg-amber-100 transition shadow-sm">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="?delete=<?php echo $s['id']; ?>" 
                                   onclick="return confirm('هل أنت متأكد من حذف الطالب؟ سيتم مسح بياناته بالكامل!')" 
                                   class="p-2.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-100 transition shadow-sm">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if(empty($students)): ?>
                    <tr>
                        <td colspan="5" class="p-20 text-center text-gray-400 font-bold">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fa-solid fa-user-slash text-4xl mb-2"></i>
                                <span>لا توجد نتائج تطابق بحثك.</span>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
</style>

<?php 
echo "</main></div></body></html>"; 
?>