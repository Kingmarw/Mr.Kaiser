<?php
require_once 'config.php';



if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$update_notif = $pdo->prepare("UPDATE recharge_requests SET is_read = 1 WHERE user_id = ?");
$update_notif->execute([$_SESSION['user_id']]);

$stmt = $pdo->prepare("
    SELECT rr.*, rc.code 
    FROM recharge_requests rr
    LEFT JOIN recharge_codes rc 
        ON rc.request_id = rr.id
    WHERE rr.user_id = ?
    ORDER BY rr.created_at DESC
");
$stmt->execute([$user_id]);
$my_requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الإشعارات وطلبات الشحن | منصة القيصر</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .status-pulse { animation: pulse 2s infinite; }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-slate-900 min-h-screen pb-12">

    <div class="max-w-4xl mx-auto px-4 py-8 flex justify-between items-center">
        <h1 class="text-3xl font-black text-slate-800 dark:text-white">الإشعارات 🔔</h1>
        <a href="index.php" class="bg-white dark:bg-slate-800 px-4 py-2 rounded-xl shadow-sm text-sm font-bold text-blue-600 hover:scale-105 transition">الرئيسية 🏠</a>
    </div>

    <div class="max-w-4xl mx-auto px-4">
        
        <?php if (count($my_requests) > 0): ?>
            <div class="space-y-4">
                <?php foreach ($my_requests as $req): ?>
                    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-slate-700 flex flex-col md:flex-row items-center gap-6 transition hover:shadow-md">
                        
                        <div class="flex-shrink-0">
                            <?php if ($req['status'] == 'pending'): ?>
                                <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center text-3xl status-pulse">⏳</div>
                            <?php elseif ($req['status'] == 'approved'): ?>
                                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-3xl">✅</div>
                            <?php else: ?>
                                <div class="w-16 h-16 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center text-3xl">❌</div>
                            <?php endif; ?>
                        </div>

                        <div class="flex-grow text-center md:text-right">
                            <h3 class="text-lg font-black text-slate-800 dark:text-white">
                                طلب شحن بمبلغ <?php echo $req['amount']; ?> ج.م
                            </h3>
                            <p class="text-sm text-slate-400 font-bold mt-1">
                                بتاريخ: <?php echo date('Y-m-d (h:i A)', strtotime($req['created_at'])); ?>
                            </p>
                            
                            <?php if ($req['status'] == 'approved' && $req['code']): ?>
                                <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-800 inline-block w-full md:w-auto">
                                    <span class="block text-xs text-blue-500 font-bold mb-1 italic">انسخ كود الشحن واستخدمه الآن:</span>
                                    <div class="flex items-center justify-center gap-3">
                                        <span id="code-<?php echo $req['id']; ?>" class="font-mono text-xl font-black text-blue-700 dark:text-blue-400 tracking-wider">
                                            <?php echo $req['code']; ?>
                                        </span>
                                        <button onclick="copyCode('<?php echo $req['code']; ?>')" class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition">
                                            📋
                                        </button>
                                    </div>
                                </div>
                            <?php elseif ($req['status'] == 'pending'): ?>
                                <p class="mt-2 text-amber-600 font-bold text-sm italic">جاري مراجعة طلبك من قبل الإدارة...</p>
                            <?php elseif ($req['status'] == 'rejected'): ?>
                                <p class="mt-2 text-red-500 font-bold text-sm italic">نعتذر، تم رفض الطلب. يرجى التأكد من صورة التحويل.</p>
                            <?php endif; ?>
                        </div>

                        <?php if ($req['status'] == 'approved'): ?>
                            <div class="flex-shrink-0">
                                <a href="recharge.php" class="bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-black px-6 py-3 rounded-2xl shadow-lg shadow-yellow-400/20 block transition transform active:scale-95">
                                    اشحن الآن ⚡
                                </a>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-white dark:bg-slate-800 rounded-[3rem] p-20 text-center border-2 border-dashed border-gray-200 dark:border-slate-700">
                <div class="text-6xl mb-6">📭</div>
                <h2 class="text-2xl font-black text-slate-400 italic">لا توجد إشعارات حالياً</h2>
                <p class="text-slate-500 mt-2 font-bold">بمجرد قيامك بطلب شحن ستظهر حالته هنا</p>
                <a href="upload_payment.php" class="inline-block mt-8 bg-blue-600 text-white px-8 py-4 rounded-2xl font-black shadow-xl shadow-blue-600/20">ابدأ أول عملية شحن</a>
            </div>
        <?php endif; ?>

    </div>

    <script>
        function copyCode(text) {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    title: 'تم النسخ!',
                    text: 'تم نسخ كود الشحن، يمكنك استخدامه الآن.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });
        }
    </script>
</body>
</html>