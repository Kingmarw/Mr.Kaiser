<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'] ?? '', ['student', 'admin'])) {
    die("Access denied");
}

$user_id = (int) $_SESSION['user_id'];

$grades = [
    1 => 'أولى إعدادي', 2 => 'تانية إعدادي', 3 => 'تالتة إعدادي',
    4 => 'أولى ثانوي', 5 => 'تانية ثانوي', 6 => 'تالتة ثانوي'
];

// جلب بيانات الطالب الحالية
$stmt = $pdo->prepare("SELECT id, full_name, phone, balance, student_grade, avatar_url FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$user_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("الطالب غير موجود");
}

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $password  = $_POST['password'] ?? '';
    $avatar_url = trim($_POST['avatar_url'] ?? '');

    if (empty($full_name) || empty($phone)) {
        $error = "الاسم ورقم الموبايل مطلوبان";
    } elseif (!preg_match('/^[0-9]{11}$/', $phone)) {
        $error = "رقم الموبايل يجب أن يكون 11 رقم";
    } else {
        try {
            $checkPhone = $pdo->prepare("SELECT id FROM users WHERE phone = ? AND id != ?");
            $checkPhone->execute([$phone, $user_id]);
            if ($checkPhone->fetch()) {
                $error = "رقم الموبايل هذا مسجل لمستخدم آخر";
            } else {
                $sql = "UPDATE users SET full_name = ?, phone = ?";
                $params = [$full_name, $phone];

                if (!empty($password)) {
                    $sql .= ", password = ?";
                    $params[] = password_hash($password, PASSWORD_DEFAULT);
                }

                if ($avatar_url !== '') {
                    $sql .= ", avatar_url = ?";
                    $params[] = $avatar_url;
                }

                $sql .= " WHERE id = ? AND role = 'student'";
                $params[] = $user_id;

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                $_SESSION['user_name'] = $full_name;
                if ($avatar_url !== '') {
                    $_SESSION['user_avatar'] = $avatar_url;
                }

                $msg = "✅ تم تحديث بياناتك بنجاح";
                
                // إعادة جلب البيانات المحدثة
                $stmt = $pdo->prepare("SELECT id, full_name, phone, balance, student_grade, avatar_url FROM users WHERE id = ? LIMIT 1");
                $stmt->execute([$user_id]);
                $student = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            $error = "حدث خطأ أثناء الحفظ، يرجى المحاولة لاحقاً";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إعدادات الحساب</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        *{
            color: #fff;
        }
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; }
        .input-group {
            position: relative;
        }

        .input-field {
            width: 100%;
            padding: 16px 14px;
            border-radius: 16px;
            background: #000;
            border: 2px solid transparent;
            outline: none;
            font-size: 14px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .dark .input-field {
            background: #000;
            color: #fff;
        }

        .input-field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,0.15);
        }

        .input-label {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            padding: 0 6px;
            color: #888;
            font-size: 13px;
            transition: 0.3s;
            pointer-events: none;
        }

        .input-field:focus + .input-label,
        .input-field:not(:placeholder-shown) + .input-label {
            top: -8px;
            font-size: 11px;
            color: #3b82f6;
            background: white;
        }

        .dark .input-field:focus + .input-label,
        .dark .input-field:not(:placeholder-shown) + .input-label {
            background: #000;
        }
    </style>
</head>

<body class="bg-[#f0f2f5] dark:bg-slate-900 transition-colors">

<?php include 'nav.php'; ?>

<div class="max-w-5xl mx-auto px-4 py-8">
    
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-800 dark:text-white">إعدادات الحساب</h1>
        <p class="text-gray-500 mt-1 text-sm">تحكم في بياناتك الشخصية وأمان حسابك</p>
    </div>

    <?php if ($msg): ?>
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3 animate-bounce">
            <i class="fa-solid fa-circle-check"></i> <?= $msg ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-slate-700 text-center">
                <div class="relative inline-block group">
                    <?php 
                        $avatar_src = !empty($student['avatar_url']) ? $student['avatar_url'] : 'https://ui-avatars.com/api/?name='.urlencode($student['full_name']).'&background=6366f1&color=fff';
                    ?>
                    <img id="avatarPreview" src="<?= $avatar_src ?>" class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-slate-700 shadow-xl mx-auto transition-transform group-hover:scale-105">
                    <label for="avatarInput" class="absolute bottom-1 right-1 w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center cursor-pointer hover:bg-blue-700 shadow-lg border-2 border-white dark:border-slate-800 transition-all">
                        <i class="fa-solid fa-camera text-sm"></i>
                    </label>
                    <input type="file" id="avatarInput" accept="image/*" class="hidden">
                </div>
                
                <h3 class="mt-4 font-black text-xl text-slate-800 dark:text-white"><?= htmlspecialchars($student['full_name']) ?></h3>
                <div class="mt-2 inline-flex items-center px-4 py-1.5 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 rounded-full text-sm font-bold">
                    الرصيد الحالي: <?= number_format($student['balance'], 2) ?> ج.م
                </div>

                <div id="uploadStatus" class="mt-4 text-xs font-bold hidden"></div>
            </div>

            <div class="bg-blue-600 rounded-3xl p-6 text-white shadow-lg shadow-blue-500/20">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-2xl">🎓</div>
                    <div>
                        <p class="text-blue-100 text-xs">المرحلة الدراسية</p>
                        <p class="font-bold text-lg"><?= $grades[(int)$student['student_grade']] ?? 'غير محدد' ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8">
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 dark:border-slate-700">
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="avatar_url" id="avatar_url" value="<?= htmlspecialchars($student['avatar_url'] ?? '') ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="input-group">
                        <input 
                            type="text" 
                            name="full_name" 
                            value="<?= htmlspecialchars($student['full_name']) ?>" 
                            class="input-field" 
                            placeholder=" "
                            required
                        >
                        <label class="input-label">الاسم الكامل</label>
                    </div>
                        <div class="input-group">
                            <input 
                                type="text" 
                                name="phone" 
                                value="<?= htmlspecialchars($student['phone']) ?>" 
                                class="input-field text-left" 
                                dir="ltr"
                                placeholder=" "
                            >
                            <label class="input-label">رقم الموبايل</label>
                        </div>
                    </div>

                    <hr class="border-gray-50 dark:border-slate-700">

                    <div class="input-group">
                        <input 
                            type="password" 
                            name="password" 
                            id="passwordField" 
                            class="input-field pr-12" 
                            placeholder=" "
                        >
                        <label class="input-label">كلمة المرور الجديدة</label>

                        <button type="button" onclick="togglePass()" 
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-500">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-slate-900 dark:bg-blue-600 text-white font-black py-4 rounded-2xl hover:bg-slate-800 dark:hover:bg-blue-700 transform transition-all active:scale-[0.98] shadow-xl shadow-slate-900/10">
                            <i class="fa-solid fa-floppy-disk ml-2"></i> حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    function togglePass() {
        const passInput = document.getElementById('passwordField');
        const eyeIcon = document.getElementById('eyeIcon');
        if (passInput.type === 'password') {
            passInput.type = 'text';
            eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passInput.type = 'password';
            eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }


    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');
    const uploadStatus = document.getElementById('uploadStatus');

    avatarInput.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('avatar', file);

        uploadStatus.innerText = "⏳ جاري الرفع...";
        uploadStatus.className = "mt-4 text-xs font-bold text-blue-600 block";

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'upload_avatar.php', true);

        xhr.onload = function() {
            try {
                const res = JSON.parse(xhr.responseText);
                if (res.success) {
                    avatarPreview.src = res.url;
                    document.getElementById('avatar_url').value = res.url;
                    uploadStatus.innerText = "✅ تم الرفع! اضغط حفظ الآن";
                    uploadStatus.className = "mt-4 text-xs font-bold text-emerald-600 block";
                } else {
                    uploadStatus.innerText = "❌ " + res.error;
                    uploadStatus.className = "mt-4 text-xs font-bold text-red-600 block";
                }
            } catch(e) {
                uploadStatus.innerText = "❌ خطأ في السيرفر";
            }
        };
        xhr.send(formData);
    });
</script>

</body>
</html>