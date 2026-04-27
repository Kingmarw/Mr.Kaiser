<?php
require_once 'header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$grades = [
    1 => 'أولى إعدادي',
    2 => 'تانية إعدادي',
    3 => 'تالتة إعدادي',
    4 => 'أولى ثانوي',
    5 => 'تانية ثانوي',
    6 => 'تالتة ثانوي'
];

$msg = '';
$error = '';

$full_name = '';
$phone = '';
$balance = '0.00';
$student_grade = 3;
$avatar_url = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name     = trim($_POST['full_name'] ?? '');
    $phone         = trim($_POST['phone'] ?? '');
    $password      = $_POST['password'] ?? '';
    $balance       = trim($_POST['balance'] ?? '0.00');
    $student_grade = (int)($_POST['student_grade'] ?? 3);
    $avatar_url    = trim($_POST['avatar_url'] ?? '');

    if ($full_name === '') {
        $error = "اسم الطالب مطلوب";
    } elseif ($phone === '') {
        $error = "رقم الهاتف مطلوب";
    } elseif ($password === '') {
        $error = "كلمة المرور مطلوبة";
    } elseif (!preg_match('/^[0-9]{11}$/', $phone)) {
        $error = "رقم الهاتف يجب أن يكون 11 رقم";
    } elseif (!is_numeric($balance)) {
        $error = "الرصيد يجب أن يكون رقمًا صحيحًا أو عشريًا";
    } elseif (!array_key_exists($student_grade, $grades)) {
        $error = "الصف الدراسي غير صحيح";
    } else {
        try {
            $check = $pdo->prepare("SELECT id FROM users WHERE phone = ? LIMIT 1");
            $check->execute([$phone]);

            if ($check->fetch()) {
                $error = "رقم الهاتف مستخدم بالفعل";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);

                if (!empty($avatar_url)) {
                    $stmt = $pdo->prepare("
                        INSERT INTO users (full_name, phone, password, balance, role, student_grade, avatar_url)
                        VALUES (?, ?, ?, ?, 'student', ?, ?)
                    ");
                    $stmt->execute([
                        $full_name,
                        $phone,
                        $hashed,
                        $balance,
                        $student_grade,
                        $avatar_url
                    ]);
                } else {
                    $stmt = $pdo->prepare("
                        INSERT INTO users (full_name, phone, password, balance, role, student_grade)
                        VALUES (?, ?, ?, ?, 'student', ?)
                    ");
                    $stmt->execute([
                        $full_name,
                        $phone,
                        $hashed,
                        $balance,
                        $student_grade
                    ]);
                }

                $msg = "تمت إضافة الطالب بنجاح";
                $full_name = '';
                $phone = '';
                $balance = '0.00';
                $student_grade = 3;
                $avatar_url = '';
            }
        } catch (Exception $e) {
            $error = "حدث خطأ أثناء إضافة الطالب";
        }
    }
}
?>

<div class="flex-1 w-full max-w-full overflow-x-hidden p-4 md:p-8">
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white">إضافة طالب جديد</h2>
            <p class="text-gray-500 text-sm md:text-base">إدخال بيانات طالب جديد في النظام</p>
        </div>

        <a href="manage_students.php" class="bg-slate-800 hover:bg-slate-900 text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all shadow-lg">
            العودة للإدارة
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

    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 md:p-8 max-w-4xl">
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-slate-200">اسم الطالب</label>
                <input
                    type="text"
                    name="full_name"
                    value="<?php echo htmlspecialchars($full_name); ?>"
                    class="w-full bg-gray-50 dark:bg-slate-900 border-none py-3 px-4 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all"
                    placeholder="أدخل اسم الطالب"
                    required
                >
            </div>

            <div>
                <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-slate-200">رقم الهاتف</label>
                <input
                    type="text"
                    name="phone"
                    value="<?php echo htmlspecialchars($phone); ?>"
                    class="w-full bg-gray-50 dark:bg-slate-900 border-none py-3 px-4 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all"
                    placeholder="01xxxxxxxxx"
                    required
                >
            </div>

            <div>
                <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-slate-200">كلمة المرور</label>
                <input
                    type="password"
                    name="password"
                    class="w-full bg-gray-50 dark:bg-slate-900 border-none py-3 px-4 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all"
                    placeholder="أدخل كلمة المرور"
                    required
                >
            </div>

            <div>
                <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-slate-200">الصف الدراسي</label>
                <select
                    name="student_grade"
                    class="w-full bg-gray-50 dark:bg-slate-900 border-none py-3 px-4 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all"
                    required
                >
                    <?php foreach ($grades as $val => $name): ?>
                        <option value="<?php echo $val; ?>" <?php echo ((int)$student_grade === $val) ? 'selected' : ''; ?>>
                            <?php echo $name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-slate-200">الرصيد</label>
                <input
                    type="number"
                    step="0.01"
                    name="balance"
                    value="<?php echo htmlspecialchars($balance); ?>"
                    class="w-full bg-gray-50 dark:bg-slate-900 border-none py-3 px-4 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all"
                    placeholder="0.00"
                >
            </div>

            <div class="md:col-span-2">
                <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-slate-200">رابط الصورة (اختياري)</label>
                <input
                    type="url"
                    name="avatar_url"
                    value="<?php echo htmlspecialchars($avatar_url); ?>"
                    class="w-full bg-gray-50 dark:bg-slate-900 border-none py-3 px-4 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all"
                    placeholder="https://res.cloudinary.com/..."
                >
            </div>

            <div class="md:col-span-2 flex flex-col sm:flex-row gap-3 pt-2">
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-blue-200 dark:shadow-none"
                >
                    إضافة الطالب
                </button>

                <a href="manage_students.php"
                   class="bg-gray-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 px-6 py-3 rounded-2xl font-bold text-sm transition-all text-center">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

<?php
echo "</main></div></body></html>";
?>