<?php
require_once 'config.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$swal_script = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['recharge_code'])) {
    $user_id = $_SESSION['user_id'];
    $input_code = trim($_POST['recharge_code']);

    $stmt = $pdo->prepare("SELECT * FROM recharge_codes WHERE code = ? AND is_used = 0 LIMIT 1");
    $stmt->execute([$input_code]);
    $code_data = $stmt->fetch();

    if ($code_data) {
        $amount = $code_data['amount'];
        $code_id = $code_data['id'];

        try {
            $pdo->beginTransaction();

            $update_user = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $update_user->execute([$amount, $user_id]);


            $update_code = $pdo->prepare("UPDATE recharge_codes SET is_used = 1 WHERE id = ?");
            $update_code->execute([$code_id]);

            $pdo->commit();

            $_SESSION['user_balance'] += $amount;

            $swal_script = "Swal.fire({
                title: 'مبروك! 🎉',
                text: 'تم شحن رصيدك بمبلغ $amount ج.م بنجاح.',
                icon: 'success',
                confirmButtonText: 'ممتاز',
                confirmButtonColor: '#2563eb'
            });";

        } catch (Exception $e) {
            $pdo->rollBack();
            $swal_script = "Swal.fire('خطأ!', 'حدثت مشكلة أثناء الشحن، حاول مرة أخرى.', 'error');";
        }
    } else {
        // الكود غير صحيح أو مستخدم مسبقاً
        $swal_script = "Swal.fire({
            title: 'كود غير صالح!',
            text: 'هذا الكود غير موجود أو تم استخدامه من قبل، تأكد من الكود وحاول ثانية.',
            icon: 'error',
            confirmButtonText: 'حاول مجدداً'
        });";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شحن الرصيد | منصة القيصر</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; }
        .bg-pattern {
            background-color: #1e293bFF;
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-pattern min-h-screen">

    <div class="max-w-xl mx-auto px-4 pt-20">
        <div class="bg-dark dark:bg-slate-800 rounded-[3rem] shadow-2xl shadow-blue-500/10 overflow-hidden border border-gray-100 dark:border-slate-700">
            
            <div class="p-8 text-center bg-blue-600 text-white">
                <div class="w-20 h-20 bg-white/20 rounded-3xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner">
                    <i class="fa-solid fa-bolt-lightning text-yellow-300"></i>
                </div>
                <h1 class="text-3xl font-black mb-2">شحن رصيد المحفظة</h1>
                <p class="text-blue-100 opacity-80">أدخل كود الشحن المكون من أرقام وحروف</p>
            </div>

            <div class="p-10">
                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-black text-slate-600 dark:text-slate-400 mb-3 mr-2">كود الشحن الخاص بك</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 right-5 flex items-center text-slate-400">
                                <i class="fa-solid fa-ticket"></i>
                            </span>
                            <input type="text" name="recharge_code" placeholder="مثال: KAISER-XXXX-XXXX" 
                                class="w-full pr-14 pl-5 py-5 rounded-2xl bg-gray-50 dark:bg-slate-900 border-2 border-transparent focus:border-blue-500 focus:bg-white transition-all outline-none font-mono text-xl font-bold tracking-widest text-center uppercase" required>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-black py-5 rounded-2xl shadow-xl shadow-yellow-400/20 transition-all transform active:scale-95 text-xl flex items-center justify-center gap-3">
                        <span>تفعيل الكود الآن</span>
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                </form>

                <div class="mt-10 pt-8 border-t border-gray-100 dark:border-slate-700">
                    <div class="flex items-center gap-4 text-slate-500 mb-4">
                        <i class="fa-solid fa-circle-info text-blue-500"></i>
                        <p class="text-sm font-bold">ليس لديك كود شحن؟</p>
                    </div>
                    <a href="upload_payment.php" class="block w-full text-center py-4 rounded-xl border-2 border-dashed border-gray-200 dark:border-slate-600 text-slate-500 font-bold hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                        اطلب كود شحن عن طريق التحويل 💸
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="index.php" class="text-slate-400 font-bold hover:text-blue-600 transition">
                <i class="fa-solid fa-house ml-2"></i> العودة للرئيسية
            </a>
        </div>
    </div>

    <script>
        <?php echo $swal_script; ?>
    </script>
</body>
</html>