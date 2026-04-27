<?php
require_once '../config.php';
require_once 'header.php';
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$db = null;
if (isset($pdo)) {
    $db = $pdo;
} elseif (isset($conn)) {
    $db = $conn;
} else {
    die("<h3 style='color:red; text-align:center;'>خطأ: مش قادر ألاقي متغير الاتصال بقاعدة البيانات في ملف config.php</h3>");
}


try {

    $query = "SELECT qr.*, u.full_name as student_name 
              FROM quiz_results qr
              JOIN users u ON qr.user_id = u.id 
              ORDER BY qr.updated_at DESC";
              
    $stmt = $db->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_msg = "خطأ في قاعدة البيانات: " . $e->getMessage();
    $results = [];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم | نتائج الاختبارات</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-slate-900 p-4 md:p-10">

    <div class="max-w-6xl mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-white mb-2">نتائج الاختبارات 📝</h1>
                <p class="text-gray-500">لوحة تحكم الإدارة لمتابعة درجات طلاب القيصر</p>
            </div>
            
            <div class="relative w-full md:w-80">
                <input type="text" id="searchInput" placeholder="ابحث باسم الطالب هنا..." 
                       class="w-full px-5 py-3 rounded-2xl border border-gray-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-bold">
            </div>
        </div>

        <?php if(isset($error_msg)): ?>
            <div class="bg-red-100 border-r-4 border-red-500 text-red-700 px-4 py-4 rounded-xl mb-6 font-bold shadow-sm">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto  rounded-[2rem] shadow-xl border border-gray-100">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="p-6 font-bold  whitespace-nowrap">اسم الطالب</th>
                        <th class="p-6 font-bold  whitespace-nowrap">رقم الاختبار</th>
                        <th class="p-6 font-bold  whitespace-nowrap">الدرجة</th>
                        <th class="p-6 font-bold  whitespace-nowrap">المحاولات</th>
                        <th class="p-6 font-bold  whitespace-nowrap">الحالة</th>
                        <th class="p-6 font-bold whitespace-nowrap">آخر محاولة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($results) > 0): ?>
                        <?php foreach($results as $res): ?>
                        <tr class="border-b border-gray-50 hover:bg-blue-50/50 transition-colors student-row">
                            <td class="p-6 font-black  student-name whitespace-nowrap">
                                <?php echo htmlspecialchars($res['student_name']); ?>
                            </td>
                            <td class="p-6 font-bold">#<?php echo $res['quiz_id']; ?></td>
                            <td class="p-6">
                                <span class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full font-black text-sm shadow-sm">
                                    <?php echo $res['score']; ?>
                                </span>
                            </td>
                            <td class="p-6  font-bold"><?php echo $res['attempts']; ?></td>
                            <td class="p-6 whitespace-nowrap">
                                <?php 
                                    $status = strtolower($res['status']);
                                    if($status == 'passed' || $status == 'ناجح'): 
                                ?>
                                    <span class="text-green-700 bg-green-100 border border-green-200 px-4 py-1.5 rounded-full text-sm font-bold shadow-sm">ناجح</span>
                                <?php else: ?>
                                    <span class="text-red-700 bg-red-100 border border-red-200 px-4 py-1.5 rounded-full text-sm font-bold shadow-sm">راسب</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-6 text-sm font-bold whitespace-nowrap" dir="ltr">
                                <?php echo date('Y-m-d h:i A', strtotime($res['last_attempt'])); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="p-10 text-center text-gray-400 font-black text-xl">لا توجد نتائج اختبارات حتى الآن!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('.student-row');

            rows.forEach(row => {
                let name = row.querySelector('.student-name').textContent.toLowerCase();
                if (name.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>