

<?php
require_once 'config.php';



if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$swal_script = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['screenshot'])) {
    $user_id = $_SESSION['user_id'];
    $amount = filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    
    $file = $_FILES['screenshot'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png'];

    if (in_array($ext, $allowed)) {
        if ($file['size'] < 5 * 1024 * 1024) {
            

            $upload_dir = 'uploads/payments/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = "PAY_" . time() . "_" . rand(1000, 9999) . "." . $ext;
            $target = $upload_dir . $file_name;

            if (move_uploaded_file($file['tmp_name'], $target)) {
                $stmt = $pdo->prepare("INSERT INTO recharge_requests (user_id, amount, screenshot) VALUES (?, ?, ?)");
                if ($stmt->execute([$user_id, $amount, $file_name])) {
                    $swal_script = "Swal.fire({
                        title: 'تم إرسال الطلب! 🚀',
                        text: 'جاري مراجعة عملية التحويل، سيصلك كود الشحن في الإشعارات فور التأكيد.',
                        icon: 'success',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#2563eb'
                    });";
                }
            } else {
                $swal_script = "Swal.fire({ title: 'خطأ!', text: 'فشل في رفع الصورة، حاول مرة أخرى.', icon: 'error' });";
            }
        } else {
            $swal_script = "Swal.fire({ title: 'الملف كبير جداً!', text: 'أقصى حجم مسموح به هو 5 ميجا بايت.', icon: 'warning' });";
        }
    } else {
        $swal_script = "Swal.fire({ title: 'تنسيق غير مدعوم!', text: 'يرجى رفع صورة بصيغة JPG أو PNG.', icon: 'warning' });";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد الدفع | منصة القيصر</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); }
    </style>
</head>
<body class="bg-gray-50 dark:bg-slate-900 min-h-screen pb-12">

    <div class="max-w-4xl mx-auto px-4 pt-8 mb-8">
        <a href="index.php" class="text-slate-500 font-bold hover:text-blue-600 transition flex items-center gap-2">
            <span>🔙</span> العودة للرئيسية
        </a>
    </div>

    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100 dark:border-slate-700">
            
            <div class="gradient-bg p-8 text-center text-white">
                <h1 class="text-3xl font-black mb-2">تأكيد عملية الدفع 💸</h1>
                <p class="text-blue-100 opacity-90">ارفع صورة التحويل ليتم إصدار كود الشحن لك</p>
            </div>

            <div class="p-8">
                <div class="mb-10 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800 rounded-2xl">
                        <span class="block text-orange-600 dark:text-orange-400 font-black text-sm mb-1">فودافون كاش</span>
                        <span class="text-lg font-mono dark:text-white">01099534259</span>
                    </div>
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-2xl">
                        <span class="block text-blue-600 dark:text-blue-400 font-black text-sm mb-1">أنستا باي</span>
                        <span class="text-lg font-mono dark:text-white">01147652465</span>
                    </div>
                </div>

                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label class="block text-sm font-black text-slate-700 dark:text-slate-300 mb-2 mr-2">المبلغ الذي قمت بتحويله (ج.م)</label>
                        <input type="number" name="amount" placeholder="مثلاً: 150" 
                            class="w-full px-5 py-4 rounded-2xl bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 focus:ring-2 focus:ring-blue-500 focus:outline-none dark:text-white font-bold" required>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-700 dark:text-slate-300 mb-2 mr-2">صورة إيصال التحويل / سكرين شوت</label>
                        <div class="relative group">
                            <input type="file" name="screenshot" id="file_input" accept="image/*" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required>
                            <div id="drop_zone" class="w-full p-8 border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-3xl text-center group-hover:border-blue-500 transition">
                                <div class="text-4xl mb-2">📸</div>
                                <p id="file_name_display" class="text-slate-500 dark:text-slate-400 font-bold">اضغط هنا أو اسحب الصورة لرفعها</p>
                                <p class="text-xs text-slate-400 mt-2">يدعم PNG, JPG (أقصى حجم 5 ميجا)</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-black py-5 rounded-2xl shadow-lg transition-all transform active:scale-95 text-lg">
                        إرسال الطلب للمراجعة ✨
                    </button>
                </form>
            </div>

            <div class="bg-gray-50 dark:bg-slate-900/50 p-6 text-center border-t border-gray-100 dark:border-slate-700">
                <p class="text-slate-500 dark:text-slate-400 text-sm italic">
                    سيتم مراجعة طلبك خلال 15-30 دقيقة من وقت الإرسال.
                </p>
            </div>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('file_input');
        const fileNameDisplay = document.getElementById('file_name_display');
        const dropZone = document.getElementById('drop_zone');

        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                fileNameDisplay.innerText = "تم اختيار: " + this.files[0].name;
                fileNameDisplay.classList.add('text-blue-600');
                dropZone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/10');
            }
        });

        <?php echo $swal_script; ?>
    </script>
    <?php include 'footer.php'; ?>
</body>
</html>