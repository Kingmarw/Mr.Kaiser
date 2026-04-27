<?php 
require_once 'config.php';

$error = "";
$login_success = false;
$redirect_url = "";     

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_balance'] = $user['balance'];
        $_SESSION['role'] = $user['role'];


        if ($_SESSION['role'] === 'admin') {
            $redirect_url = "admin/index.php";
        } else {
            $redirect_url = "index.php";
        }
        
        $login_success = true; 
        
    } else {
        $error = "عذراً، رقم الهاتف أو كلمة المرور غير صحيحة.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - منصة المراجعة</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; background-color: #f4f7f6; min-height: 100vh; display: flex; align-items: center; }
        .form-card { border: none; border-radius: 20px; shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .card-header { background: #12bd53; color: white; border-radius: 20px 20px 0 0 !important; padding: 20px; }
        .login-btn { background: #12bd53; color: white; border: none; padding: 12px; border-radius: 10px; transition: 0.3s; font-weight: bold; }
        .login-btn:hover { background: #3b82f6; color: white; }
        .form-control { border-radius: 10px; padding: 12px; }
        .login-btn{
            background-color: #000;
            border: none;
            padding: 10px;
            border-radius: 50px;
            transition: all .4s ease-in-out;
        }
        .login-btn:hover{
        color: #000;
        background-color: #fff;
        }
    </style>
</head>
<body class="bg-body-tertiary">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-xl-5">
            <div class="text-center mb-4">
                <?php if($error): ?>
                    <div class="alert alert-danger shadow-sm mb-3"><?php echo $error; ?></div>
                <?php endif; ?>
                <h2 class="fw-bold text-primary">مرحباً بك مجدداً</h2>
                <p class="text-muted">سجل دخولك للمتابعة في منصة المراجعة النهائية</p>
            </div>

            <div class="card form-card shadow-sm">
                <div class="card-header text-center">
                    <h4 class="card-title fw-bold m-0"><i class="fa-solid fa-right-to-bracket me-2"></i> تسجيل الدخول</h4>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">رقم الموبايل</label>
                            <input type="text" name="phone" class="form-control text-start" placeholder="01xxxxxxxxx" required />
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">كلمة المرور</label>
                            <input type="password" name="password" class="form-control text-start" placeholder="********" required />
                        </div>

                        <button type="submit" class="login-btn w-100">
                             دخول للمنصة <i class="fa-solid fa-arrow-left-to-bracket ms-2"></i>
                        </button>
                    </form>
                </div>
                <div class="card-footer border-0 bg-transparent pb-4 px-4 text-center">
                    <span class="text-muted">ليس لديك حساب؟</span>
                    <a href="register.php" class="text-primary fw-bold text-decoration-none">إنشاء حساب جديد</a>
                    <br>
                    <span>الرجوع للصفحه الرئيسيه : <a class="text-primary fw-bold text-decoration-none" href="index.php">الرئيسيه</a></span>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($login_success): ?>
<script>
    const successSound = new Audio('https://www.myinstants.com/media/sounds/super-mario-beedoo_F3cwLoe.mp3');
    successSound.play().catch(function(error) {
        console.log("المتصفح منع الصوت التلقائي");
    });

    Swal.fire({
        title: 'أهلاً بيك يا بطل! 🚀',
        text: 'تم تسجيل الدخول بنجاح، جاري تحويلك للمنصة...',
        icon: 'success',
        timer: 4000,
        showConfirmButton: false,
        allowOutsideClick: false,
        backdrop: `rgba(18, 189, 83, 0.2)`
    }).then(() => {

        window.location.href = '<?php echo $redirect_url; ?>';
    });
</script>
<?php endif; ?>
</body>
</html>