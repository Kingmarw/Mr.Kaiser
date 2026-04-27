<?php 
require_once 'header.php';
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}
if (isset($_POST['generate'])) {
    $amount = $_POST['amount'];
    $count = $_POST['count'];
    for ($i = 0; $i < $count; $i++) {
        $code = "KAISER-" . strtoupper(bin2hex(random_bytes(3)));
        $pdo->prepare("INSERT INTO recharge_codes (code, amount) VALUES (?, ?)")->execute([$code, $amount]);
    }
}

$codes = $pdo->query("SELECT * FROM recharge_codes ORDER BY id DESC LIMIT 15")->fetchAll();
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1">
        <h2 class="text-2xl font-black mb-6">توليد أكواد شحن</h2>
        <form method="POST" class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm space-y-4 border border-gray-100 dark:border-slate-700">
            <div>
                <label class="text-sm font-bold mb-2 block">قيمة الكود الواحد (سعر الكود بالجنيه)</label>
                <input type="number" name="amount" placeholder="مثلاً 100" class="w-full p-4 rounded-xl bg-gray-50 dark:bg-slate-700 border-none" required>
            </div>
            <div>
                <label class="text-sm font-bold mb-2 block">عدد الأكواد المطلوبة</label>
                <input type="number" name="count" placeholder="مثلاً 20" class="w-full p-4 rounded-xl bg-gray-50 dark:bg-slate-700 border-none" required>
            </div>
            <button name="generate" class="w-full bg-yellow-400 text-blue-900 font-black py-4 rounded-xl hover:bg-yellow-500 transition">توليد الآن ✨</button>
        </form>
    </div>

    <div class="lg:col-span-2">
        <h2 class="text-2xl font-black mb-6">آخر الأكواد المولدة</h2>
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-gray-50 dark:bg-slate-700/50">
                    <tr class="text-sm font-bold">
                        <th class="p-4">كود الشحن</th>
                        <th class="p-4">القيمة</th>
                        <th class="p-4">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    <?php foreach($codes as $c): ?>
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition">
                        <td class="p-4 font-mono font-bold text-blue-600 dark:text-blue-400"><?php echo $c['code']; ?></td>
                        <td class="p-4 font-black"><?php echo $c['amount']; ?> ج.م</td>
                        <td class="p-4">
                            <?php if($c['is_used']): ?>
                                <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold italic">مستخدم ❌</span>
                            <?php else: ?>
                                <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-bold">متاح للبيع ✅</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>