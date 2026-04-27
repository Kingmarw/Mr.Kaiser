<?php
require_once 'header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID غير صالح");
}

$id = (int) $_GET['id'];

$grades = [
    1 => 'أولى إعدادي',
    2 => 'تانية إعدادي',
    3 => 'تالتة إعدادي',
    4 => 'أولى ثانوي',
    5 => 'تانية ثانوي',
    6 => 'تالتة ثانوي'
];


$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'student' LIMIT 1");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("الطالب غير موجود");
}

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $balance = trim($_POST['balance'] ?? '0');
    $student_grade = (int)($_POST['student_grade'] ?? 3);
    $password = $_POST['password'] ?? '';

    if ($full_name === '') {
        $error = "اسم الطالب مطلوب";
    } elseif ($phone === '') {
        $error = "رقم الهاتف مطلوب";
    } elseif (!is_numeric($balance)) {
        $error = "الرصيد يجب أن يكون رقمًا صحيحًا أو عشريًا";
    } elseif (!array_key_exists($student_grade, $grades)) {
        $error = "الصف الدراسي غير صحيح";
    } else {
        try {

            $checkPhone = $pdo->prepare("SELECT id FROM users WHERE phone = ? AND id != ? LIMIT 1");
            $checkPhone->execute([$phone, $id]);

            if ($checkPhone->fetch()) {
                $error = "رقم الهاتف مستخدم بالفعل مع طالب آخر";
            } else {
                if (!empty($password)) {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    $update = $pdo->prepare("
                        UPDATE users
                        SET full_name = ?, phone = ?, balance = ?, student_grade = ?, password = ?
                        WHERE id = ? AND role = 'student'
                    ");
                    $update->execute([
                        $full_name,
                        $phone,
                        $balance,
                        $student_grade,
                        $hashedPassword,
                        $id
                    ]);
                } else {
                    $update = $pdo->prepare("
                        UPDATE users
                        SET full_name = ?, phone = ?, balance = ?, student_grade = ?
                        WHERE id = ? AND role = 'student'
                    ");
                    $update->execute([
                        $full_name,
                        $phone,
                        $balance,
                        $student_grade,
                        $id
                    ]);
                }

                $msg = "تم تعديل بيانات الطالب بنجاح";

                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'student' LIMIT 1");
                $stmt->execute([$id]);
                $student = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            $error = "حدث خطأ أثناء حفظ التعديلات";
        }
    }
}
?>

<div class="flex-1 w-full max-w-full overflow-x-hidden p-4 md:p-8">
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white">تعديل بيانات الطالب</h2>
            <p class="text-gray-500 text-sm md:text-base">
                <?php echo htmlspecialchars($student['full_name']); ?>
            </p>
        </div>

        <a href="manage_students.php" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all shadow-lg">
            العودة للقائمة
        </a>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 p-4 rounded-2xl mb-6 border border-green-200 dark:border-green-800 text-sm">
            ✅ <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 p-4 rounded-2xl mb-6 border border-red-200 dark:border-red-800 text-sm">
            ⚠️ <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 md:p-8 max-w-3xl">
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-slate-200">اسم الطالب</label>
                <input type="text" name="full_name"
                       value="<?php echo htmlspecialchars($student['full_name']); ?>"
                       class="w-full bg-gray-50 dark:bg-slate-900 border-none py-3 px-4 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all"
                       required>
            </div>

            <div>
                <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-slate-200">رقم الهاتف</label>
                <input type="text" name="phone"
                       value="<?php echo htmlspecialchars($student['phone']); ?>"
                       class="w-full bg-gray-50 dark:bg-slate-900 border-none py-3 px-4 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all"
                       required>
            </div>

            <div>
                <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-slate-200">الرصيد</label>
                <input type="number" step="0.01" name="balance"
                       value="<?php echo htmlspecialchars($student['balance']); ?>"
                       class="w-full bg-gray-50 dark:bg-slate-900 border-none py-3 px-4 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all"
                       required>
            </div>

            <div>
                <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-slate-200">الصف الدراسي</label>
                <select name="student_grade"
                        class="w-full bg-gray-50 dark:bg-slate-900 border-none py-3 px-4 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all"
                        required>
                    <?php foreach ($grades as $val => $name): ?>
                        <option value="<?php echo $val; ?>" <?php echo ((int)$student['student_grade'] === $val) ? 'selected' : ''; ?>>
                            <?php echo $name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-slate-200">كلمة المرور الجديدة</label>
                <input type="password" name="password"
                       placeholder="اتركها فارغة إذا لا تريد تغييرها"
                       class="w-full bg-gray-50 dark:bg-slate-900 border-none py-3 px-4 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all">
            </div>

            <div class="md:col-span-2 flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-blue-200 dark:shadow-none">
                    حفظ التعديلات
                </button>

                <a href="manage_students.php"
                   class="bg-gray-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 px-6 py-3 rounded-2xl font-bold text-sm transition-all text-center">
                    رجوع
                </a>
            </div>
        </form>
    </div>
</div>

<?php
echo "</main></div></body></html>";
?>