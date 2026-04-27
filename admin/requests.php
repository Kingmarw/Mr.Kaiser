<?php
include 'header.php';
require_once '../config.php';



$swal_script = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    
    if (isset($_POST['approve'])) {
        $stmt = $pdo->prepare("SELECT status FROM recharge_requests WHERE id = ?");
        $stmt->execute([$request_id]);
        $current = $stmt->fetch();

        if ($current['status'] !== 'pending') {
            die("تم معالجة الطلب بالفعل");
        }

        $generated_code = "KAISER-" . strtoupper(bin2hex(random_bytes(3)));


        $stmt = $pdo->prepare("SELECT amount FROM recharge_requests WHERE id = ?");
        $stmt->execute([$request_id]);
        $request = $stmt->fetch();

        if ($request) {
            $amount = $request['amount'];


            $stmt = $pdo->prepare("
                INSERT INTO recharge_codes (code, amount, request_id) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$generated_code, $amount, $request_id]);


            $stmt = $pdo->prepare("
                UPDATE recharge_requests 
                SET status = 'approved' 
                WHERE id = ?
            ");
            $stmt->execute([$request_id]);

            $swal_script = "Swal.fire('تمت الموافقة!', 'الكود: $generated_code', 'success');";
        }
    }
    
    if (isset($_POST['reject'])) {
        $stmt = $pdo->prepare("UPDATE recharge_requests SET status = 'rejected' WHERE id = ?");
        if ($stmt->execute([$request_id])) {
            $swal_script = "Swal.fire('تم الرفض', 'تم رفض الطلب بنجاح.', 'info');";
        }
    }
}


$sql = "SELECT r.*, u.full_name as student_name, u.phone as student_phone 
        FROM recharge_requests r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.status = 'pending' 
        ORDER BY r.created_at DESC";
$requests = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة طلبات الشحن | لوحة الإدمن</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'IBM Plex Sans Arabic', sans-serif; }</style>
</head>
<body class="bg-gray-100 dark:bg-slate-900 min-h-screen p-6">

    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-black text-slate-800 dark:text-white">طلبات الشحن المعلقة ⏳</h1>
            <div class="bg-blue-600 text-white px-4 py-2 rounded-2xl font-bold">
                لديك (<?php echo count($requests); ?>) طلب جديد
            </div>
        </div>

        <?php if (count($requests) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($requests as $req): ?>
                    <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden flex flex-col">
                        <div class="relative h-48 bg-gray-200 overflow-hidden group">
                            <img src="../uploads/payments/<?php echo $req['screenshot']; ?>" 
                                 class="w-full h-full object-cover cursor-zoom-in transition group-hover:scale-110" 
                                 onclick="previewImage('../uploads/payments/<?php echo $req['screenshot']; ?>')">
                        </div>

                        <div class="p-6 flex-1">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="font-black text-lg text-slate-800 dark:text-white"><?php echo $req['student_name']; ?></h3>
                                    <p class="text-sm text-slate-500 font-bold"><?php echo $req['student_phone']; ?></p>
                                </div>
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full font-black">
                                    <?php echo $req['amount']; ?> ج.م
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 mt-auto">
                                <form method="POST" class="w-full">
                                    <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                    <button type="submit" name="approve" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition">موافقة ✅</button>
                                </form>
                                <form method="POST" class="w-full">
                                    <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                    <button type="submit" name="reject" class="w-full bg-red-50 text-red-600 hover:bg-red-100 font-bold py-3 rounded-xl transition">رفض ❌</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-20 bg-white dark:bg-slate-800 rounded-[3rem] border-2 border-dashed border-gray-200 dark:border-slate-700">
                <h2 class="text-2xl font-black text-slate-400">لا توجد طلبات معلقة حالياً</h2>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function previewImage(url) {
            Swal.fire({
                imageUrl: url,
                imageAlt: 'إثبات الدفع',
                showConfirmButton: false,
                width: '80%',
                background: 'rgba(0,0,0,0.8)'
            });
        }
        <?php echo $swal_script; ?>
    </script>
</body>
</html>