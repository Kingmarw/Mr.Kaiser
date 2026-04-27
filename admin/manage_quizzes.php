<?php
include 'header.php';
require_once '../config.php';
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

try {
    $sql = "SELECT q.*, (SELECT COUNT(*) FROM quiz_questions WHERE quiz_id = q.id) as questions_count 
            FROM quizzes q 
            ORDER BY q.id DESC";
    $quizzes = $pdo->query($sql)->fetchAll();
} catch (PDOException $e) {
    $quizzes = [];
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة بنك الاختبارات</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'IBM Plex Sans Arabic', sans-serif; }</style>
</head>
<body class="bg-gray-50 dark:bg-slate-900 p-4 md:p-10 text-right">

    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white">بنك الاختبارات 📝</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2 font-bold">يمكنك إنشاء الاختبارات هنا وربطها بالمحاضرات لاحقاً.</p>
            </div>
            <a href="add_quizzes.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-black shadow-lg transition-all flex items-center gap-2">
                <span>+</span> إنشاء اختبار جديد
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($quizzes as $quiz): ?>
            <div class="bg-white dark:bg-slate-800 p-6 rounded-[2.5rem] border border-gray-100 dark:border-slate-700 shadow-sm hover:shadow-xl transition-all group">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-2xl">📝</div>
                    <span class="bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-xl text-xs font-black">
                        ID: #<?= $quiz['id'] ?>
                    </span>
                </div>
                
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2 group-hover:text-blue-600 transition">
                    <?= htmlspecialchars($quiz['quiz_title']) ?>
                </h3>
                
                <p class="text-gray-500 dark:text-gray-400 text-sm font-bold mb-6">
                    يحتوي على: <span class="text-blue-600"><?= $quiz['questions_count'] ?> سؤال</span>
                </p>

                <div class="flex gap-2 border-t border-gray-50 dark:border-slate-700 pt-4">
                    <button onclick="deleteQuiz(<?= $quiz['id'] ?>)" class="flex-1 py-3 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 font-black text-sm hover:bg-red-100 transition">
                        حذف
                    </button>
                    <a href="edit_quiz.php?id=<?= $quiz['id'] ?>" class="flex-1 py-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-black text-sm text-center hover:bg-blue-100 transition">
                        تعديل
                    </a>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if(empty($quizzes)): ?>
                <div class="col-span-full py-20 text-center bg-white dark:bg-slate-800 rounded-[3rem] border-2 border-dashed border-gray-200 dark:border-slate-700">
                    <p class="text-gray-400 font-black text-xl italic">لا يوجد اختبارات في البنك حالياً.. 📝</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteQuiz(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف الاختبار وجميع الأسئلة المرتبطة به!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `delete_quiz.php?id=${id}`;
                }
            })
        }
    </script>
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
        <script>
            Swal.fire({
                title: 'تم التعديل بنجاح! ✅',
                text: 'تم تحديث بيانات الاختبار وحفظها في قاعدة البيانات.',
                icon: 'success',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#2563eb',
            });
            window.history.replaceState({}, document.title, window.location.pathname);
        </script>
    <?php endif; ?>
</body>
</html>