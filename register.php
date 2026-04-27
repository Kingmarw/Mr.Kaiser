<?php
require_once 'config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim(filter_var($_POST['full_name'], FILTER_SANITIZE_STRING));
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $grade = $_POST['student_grade'];

    if (strlen($phone) < 11) {
        $message = "<div class='alert alert-danger shadow-sm'>رقم الهاتف غير صحيح! يجب أن يكون 11 رقم.</div>";
    } elseif ($password !== $confirm_password) {
        $message = "<div class='alert alert-danger shadow-sm'>كلمتا المرور غير متطابقتين!</div>";
    } elseif (strlen($password) < 6) {
        $message = "<div class='alert alert-danger shadow-sm'>كلمة المرور ضعيفة، يجب أن تكون 6 أحرف على الأقل.</div>";
    } else {

        $check = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
        $check->execute([$phone]);
        
        if ($check->rowCount() > 0) {
            $message = "<div class='alert alert-warning shadow-sm'>عذراً، هذا الرقم مسجل مسبقاً!</div>";
        } else {

            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (full_name, phone, password, role, student_grade) VALUES (?, ?, ?, 'student', ?)");
            
            if ($stmt->execute([$name, $phone, $hashed_pass, $grade])) {

                header("Location: login.php?registered=success");
                exit;
            } else {
                $message = "<div class='alert alert-danger shadow-sm'>حدث خطأ أثناء التسجيل، حاول مرة أخرى.</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب طالب جديد</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; background-color: #f4f7f6; min-height: 100vh; display: flex; align-items: center; }
        .form-card { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(45deg, #1e3a8a, #3b82f6); color: white; border-radius: 20px 20px 0 0 !important; padding: 20px; border: none; }
        .register-btn { background: #1e3a8a; color: white; border: none; padding: 12px; border-radius: 10px; transition: 0.3s; font-weight: bold; }
        .register-btn:hover { background: #3b82f6; color: white; }
        .form-control { border-radius: 10px; padding: 12px; border: 1px solid #dee2e6; }
    </style>
</head>
<body class="bg-body">

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-xl-5">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-primary">انضم إلينا الآن 🎓</h2>
                <p class="text-muted">قم بإنشاء حسابك للوصول إلى أقوى المراجعات</p>
                <?php echo $message; ?>
            </div>

            <div class="card form-card shadow-sm">
                <div class="card-header text-center">
                    <h4 class="card-title fw-bold m-0"><i class="fa-solid fa-user-plus me-2"></i> إنشاء حساب جديد</h4>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">الاسم بالكامل</label>
                            <input type="text" name="full_name" class="form-control" placeholder="اكتب اسمك الثلاثي" required 
                                   value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">رقم الموبايل</label>
                            <input type="tel" name="phone" class="form-control" placeholder="01xxxxxxxxx" maxlength="11" required 
                                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">الصف الدراسي</label>
                            <select name="student_grade" class="form-control" required>
                                <option value="" selected disabled>اختر صفك الدراسي</option>
                                <optgroup label="المرحلة الإعدادية">
                                    <option value="1">الصف الأول الإعدادي</option>
                                    <option value="2">الصف الثاني الإعدادي</option>
                                    <option value="3">الصف الثالث الإعدادي</option>
                                </optgroup>
                                <optgroup label="المرحلة الثانوية">
                                    <option value="4">الصف الأول الثانوي</option>
                                    <option value="5">الصف الثاني الثانوي</option>
                                    <option value="6">الصف الثالث الثانوي</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">كلمة المرور</label>
                                <input type="password" name="password" class="form-control" placeholder="********" required />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">تأكيد الكلمة</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="********" required />
                            </div>
                        </div>

                        <button type="submit" class="register-btn w-100 mt-2">
                             إنشاء الحساب وابدأ الآن <i class="fa-solid fa-rocket ms-2"></i>
                        </button>
                    </form>
                </div>
                <div class="card-footer border-0 bg-transparent pb-4 px-4 text-center">
                    <span class="text-muted">لديك حساب بالفعل؟</span>
                    <a href="login.php" class="text-primary fw-bold text-decoration-none">تسجيل الدخول</a>
                    <br>
                    <span><a class="text-primary fw-bold text-decoration-none" href="index.php">الرجوع للرئيسية</a></span>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.tailwindcss.com"></script>
<?php include 'footer.php'; ?> </body>
</html>