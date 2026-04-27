<?php 
require_once 'header.php';
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}
$student_id = $_GET['id'] ?? 0;


if (isset($_GET['refund_purchase_id'])) {
    $purchase_id = $_GET['refund_purchase_id'];

    try {
        $pdo->beginTransaction();


        $stmt = $pdo->prepare("SELECT user_id, price_paid FROM purchases WHERE id = ?");
        $stmt->execute([$purchase_id]);
        $purchase = $stmt->fetch();

        if ($purchase) {

            $update_user = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $update_user->execute([$purchase['price_paid'], $purchase['user_id']]);


            $delete_purchase = $pdo->prepare("DELETE FROM purchases WHERE id = ?");
            $delete_purchase->execute([$purchase_id]);

            $pdo->commit();
            $msg = "تم استرجاع المبلغ بنجاح.";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "حدث خطأ أثناء عملية الاسترجاع.";
    }
}


$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'student'");
$stmt->execute([$student_id]);
$student = $stmt->fetch();

if (!$student) { die("الطالب غير موجود"); }


$purchases = $pdo->prepare("
    SELECT p.*, l.title 
    FROM purchases p 
    JOIN lectures l ON p.lecture_id = l.id 
    WHERE p.user_id = ? 
    ORDER BY p.created_at DESC
");
$purchases->execute([$student_id]);
$all_purchases = $purchases->fetchAll();


$codes = $pdo->prepare("SELECT * FROM recharge_codes WHERE used_by = ? ORDER BY used_at DESC");
$codes->execute([$student_id]);
$all_codes = $codes->fetchAll();

$grades = [
    1 => 'أولى إعدادي',
    2 => 'تانية إعدادي',
    3 => 'تالتة إعدادي',
    4 => 'أولى ثانوي',
    5 => 'تانية ثانوي',
    6 => 'تالتة ثانوي'
];
?>

<div class="flex-1">
    <?php if(isset($msg)): ?>
        <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-6 font-bold"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-black text-slate-800 dark:text-white">ملف الطالب: <?php echo $student['full_name']; ?></h1>
        <a href="manage_students.php" class="bg-gray-200 dark:bg-slate-700 px-6 py-2 rounded-xl font-bold">← عودة</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-slate-700">
                <div class="w-20 h-20 bg-blue-600 text-white rounded-3xl flex items-center justify-center text-3xl font-black mb-6 mx-auto">
                    <?php echo mb_substr($student['full_name'], 0, 1); ?>
                </div>
                <div class="space-y-4 text-center">
                    <div>
                        <p class="text-gray-400 text-sm">الصف</p>
                        <p class="font-bold text-lg text-blue-600 dark:text-blue-400">
                            <?php echo $grades[$student['student_grade']] ?? 'غير محدد'; ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">رقم الموبايل</p>
                        <p class="font-bold text-xl"><?php echo $student['phone']; ?></p>
                    </div>
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-2xl">
                        <p class="text-green-600 text-sm font-bold">الرصيد الحالي</p>
                        <p class="text-2xl font-black text-green-700 dark:text-green-400"><?php echo number_format($student['balance'], 2); ?> ج.م</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-slate-700">
                    <h3 class="font-black text-lg">🎥 الحصص المشتراة</h3>
                </div>
                <table class="w-full text-right">
                    <thead class="bg-gray-50 dark:bg-slate-700/50 text-sm">
                        <tr>
                            <th class="p-4">اسم الحصة</th>
                            <th class="p-4">تاريخ الشراء</th>
                            <th class="p-4">التحكم</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                        <?php foreach($all_purchases as $p): ?>
                        <tr>
                            <td class="p-4 font-bold"><?php echo $p['title']; ?></td>
                            <td class="p-4 text-gray-500 text-sm"><?php echo date('Y-m-d', strtotime($p['created_at'])); ?></td>
                            <td class="p-4">
                                <a href="?id=<?php echo $student_id; ?>&refund_purchase_id=<?php echo $p['id']; ?>" 
                                   onclick="return confirm('هل تريد استرداد المبلغ؟')"
                                   class="text-xs bg-red-50 text-red-600 px-3 py-1 rounded-lg font-bold">
                                   ❌ استرجاع
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-gray-50 dark:border-slate-700">
                    <h3 class="font-black text-lg">🎫 سجل شحن الأكواد</h3>
                </div>
                <table class="w-full text-right text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="p-4">الكود</th>
                            <th class="p-4">القيمة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($all_codes as $c): ?>
                        <tr>
                            <td class="p-4 font-mono"><?php echo $c['code']; ?></td>
                            <td class="p-4 text-green-600 font-bold">+<?php echo $c['amount']; ?> ج.م</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>