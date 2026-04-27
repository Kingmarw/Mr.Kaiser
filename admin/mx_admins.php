<?php
require_once 'header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$current_user_id = (int)($_SESSION['user_id'] ?? 0);

$msg = '';
$error = '';
$edit_admin = null;

$grades = [
    1 => 'أولى إعدادي',
    2 => 'تانية إعدادي',
    3 => 'تالتة إعدادي',
    4 => 'أولى ثانوي',
    5 => 'تانية ثانوي',
    6 => 'تالتة ثانوي'
];

/* =========================
  Delete Admin
========================= */
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int) $_GET['delete'];

    if ($delete_id === $current_user_id) {
        $error = "لا يمكنك حذف حسابك الحالي";
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
            $stmt->execute([$delete_id]);

            if ($stmt->rowCount() > 0) {
                $msg = "تم حذف الأدمن بنجاح";
            } else {
                $error = "الأدمن غير موجود";
            }
        } catch (Exception $e) {
            $error = "حدث خطأ أثناء الحذف";
        }
    }
}

/* =========================
  Edit admin
========================= */
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'admin' LIMIT 1");
    $stmt->execute([$edit_id]);
    $edit_admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$edit_admin) {
        $error = "الأدمن غير موجود";
    }
}

/* =========================
  save admin
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $password  = $_POST['password'] ?? '';
    $admin_id  = isset($_POST['admin_id']) && is_numeric($_POST['admin_id']) ? (int)$_POST['admin_id'] : 0;

    if ($full_name === '') {
        $error = "اسم الأدمن مطلوب";
    } elseif ($phone === '') {
        $error = "رقم الهاتف مطلوب";
    } else {
        try {
            if ($admin_id > 0) {
                /* =========================
                   edit
                ========================= */
                $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ? AND id != ? LIMIT 1");
                $stmt->execute([$phone, $admin_id]);
                $phone_used = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($phone_used) {
                    $error = "رقم الهاتف مستخدم بالفعل";
                } else {
                    if (!empty($password)) {
                        $hashed = password_hash($password, PASSWORD_DEFAULT);

                        $stmt = $pdo->prepare("
                            UPDATE users
                            SET full_name = ?, phone = ?, password = ?, role = 'admin'
                            WHERE id = ? AND role = 'admin'
                        ");
                        $stmt->execute([$full_name, $phone, $hashed, $admin_id]);
                    } else {
                        $stmt = $pdo->prepare("
                            UPDATE users
                            SET full_name = ?, phone = ?, role = 'admin'
                            WHERE id = ? AND role = 'admin'
                        ");
                        $stmt->execute([$full_name, $phone, $admin_id]);
                    }

                    $msg = "تم تعديل بيانات الأدمن بنجاح";

                    // إعادة تحميل بيانات التعديل
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'admin' LIMIT 1");
                    $stmt->execute([$admin_id]);
                    $edit_admin = $stmt->fetch(PDO::FETCH_ASSOC);
                }
            } else {

                if (empty($password)) {
                    $error = "كلمة المرور مطلوبة عند إضافة أدمن جديد";
                } else {
                    $stmt = $pdo->prepare("SELECT id, role FROM users WHERE phone = ? LIMIT 1");
                    $stmt->execute([$phone]);
                    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

                    $hashed = password_hash($password, PASSWORD_DEFAULT);

                    if ($existing) {
                        if ($existing['role'] === 'admin') {
                            $error = "رقم الهاتف مسجل بالفعل كأدمن";
                        } else {

                            $stmt = $pdo->prepare("
                                UPDATE users
                                SET full_name = ?,
                                    phone = ?,
                                    password = ?,
                                    role = 'admin',
                                    student_grade = NULL,
                                    balance = 0.00
                                WHERE id = ?
                            ");
                            $stmt->execute([$full_name, $phone, $hashed, $existing['id']]);

                            $msg = "تم تحويل الطالب إلى أدمن بنجاح";
                        }
                    } else {

                        $stmt = $pdo->prepare("
                            INSERT INTO users (full_name, phone, password, balance, role, student_grade)
                            VALUES (?, ?, ?, 0.00, 'admin', NULL)
                        ");
                        $stmt->execute([$full_name, $phone, $hashed]);

                        $msg = "تمت إضافة الأدمن بنجاح";
                    }
                }
            }
        } catch (Exception $e) {
            $error = "حدث خطأ أثناء حفظ البيانات";
        }
    }
}


$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'admin' ORDER BY id DESC");
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="flex-1 w-full max-w-full overflow-x-hidden p-4 md:p-8">
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white">إدارة الأدمنز</h2>
            <p class="text-gray-500 text-sm md:text-base">إجمالي الأدمنز: <?php echo count($admins); ?></p>
        </div>
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

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 p-6">
            <h3 class="text-xl font-black text-slate-800 dark:text-white mb-6">
                <?php echo $edit_admin ? 'تعديل أدمن' : 'إضافة أدمن جديد'; ?>
            </h3>

            <form method="POST" class="space-y-4">
                <input type="hidden" name="admin_id" value="<?php echo htmlspecialchars($edit_admin['id'] ?? 0); ?>">

                <div>
                    <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-slate-200">الاسم</label>
                    <input type="text" name="full_name"
                           value="<?php echo htmlspecialchars($edit_admin['full_name'] ?? ''); ?>"
                           class="w-full bg-gray-50 dark:bg-slate-900 border-none py-3 px-4 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all"
                           required>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-slate-200">رقم الهاتف</label>
                    <input type="text" name="phone"
                           value="<?php echo htmlspecialchars($edit_admin['phone'] ?? ''); ?>"
                           class="w-full bg-gray-50 dark:bg-slate-900 border-none py-3 px-4 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all"
                           required>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-bold text-slate-700 dark:text-slate-200">
                        كلمة المرور <?php echo $edit_admin ? '(اختياري)' : ''; ?>
                    </label>
                    <input type="password" name="password"
                           placeholder="<?php echo $edit_admin ? 'اتركها فارغة إذا لا تريد تغييرها' : 'أدخل كلمة المرور'; ?>"
                           class="w-full bg-gray-50 dark:bg-slate-900 border-none py-3 px-4 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white transition-all"
                           <?php echo $edit_admin ? '' : 'required'; ?>>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-blue-200 dark:shadow-none">
                        <?php echo $edit_admin ? 'حفظ التعديل' : 'إضافة الأدمن'; ?>
                    </button>

                    <?php if ($edit_admin): ?>
                        <a href="mx_admins.php"
                           class="bg-gray-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 px-6 py-3 rounded-2xl font-bold text-sm transition-all text-center">
                            إلغاء
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="xl:col-span-2 bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto w-full custom-scrollbar">
                <table class="w-full text-right min-w-[700px]">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-slate-700/50 text-gray-400 text-xs md:text-sm uppercase tracking-wider">
                            <th class="p-5 font-bold">الاسم</th>
                            <th class="p-5 font-bold">رقم الهاتف</th>
                            <th class="p-5 font-bold text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-slate-700">
                        <?php foreach ($admins as $admin): ?>
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="p-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center font-bold text-sm">
                                            <?php echo mb_substr($admin['full_name'], 0, 1, 'utf-8'); ?>
                                        </div>
                                        <div class="font-bold text-slate-700 dark:text-slate-200 whitespace-nowrap">
                                            <?php echo htmlspecialchars($admin['full_name']); ?>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-5 text-gray-500 font-mono text-sm whitespace-nowrap">
                                    <?php echo htmlspecialchars($admin['phone']); ?>
                                </td>

                                <td class="p-5">
                                    <div class="flex justify-center gap-2">
                                        <a href="?edit=<?php echo $admin['id']; ?>"
                                           title="تعديل"
                                           class="p-2.5 bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 rounded-xl hover:bg-amber-100 transition shadow-sm">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <?php if ((int)$admin['id'] !== $current_user_id): ?>
                                            <a href="?delete=<?php echo $admin['id']; ?>"
                                               onclick="return confirm('هل أنت متأكد من حذف الأدمن؟')"
                                               title="حذف"
                                               class="p-2.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-100 transition shadow-sm">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="p-2.5 bg-gray-100 dark:bg-slate-700 text-gray-400 rounded-xl cursor-not-allowed" title="هذا حسابك الحالي">
                                                <i class="fa-solid fa-user-shield"></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($admins)): ?>
                            <tr>
                                <td colspan="3" class="p-20 text-center text-gray-400 font-bold">
                                    <div class="flex flex-col items-center gap-2">
                                        <i class="fa-solid fa-user-shield text-4xl mb-2"></i>
                                        <span>لا يوجد أدمنز حتى الآن.</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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