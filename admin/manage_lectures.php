<?php 
require_once 'header.php'; 
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id <= 0) {
        die("ID غير صالح");
    }
    
    try {
        $pdo->beginTransaction();


        $delete_purchases = $pdo->prepare("DELETE FROM purchases WHERE lecture_id = ?");
        $delete_purchases->execute([$id]);


        $get_img = $pdo->prepare("SELECT thumbnail FROM lectures WHERE id = ?");
        $get_img->execute([$id]);
        $img = $get_img->fetchColumn();
        
        if ($img && file_exists("../uploads/posters/" . $img)) {
            unlink("../uploads/posters/" . $img);
        }

        $stmt = $pdo->prepare("DELETE FROM lectures WHERE id = ?");
        $stmt->execute([$id]);

        $pdo->commit(); 
        $msg = "تم حذف المحاضرة وكل سجلات الشراء المرتبطة بها بنجاح!";
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "حدث خطأ أثناء الحذف: " . $e->getMessage();
    }
}


$lectures = $pdo->query("SELECT * FROM lectures ORDER BY id DESC")->fetchAll();
?>
<div class="flex-1 w-full max-w-full overflow-x-hidden">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white">إدارة المحاضرات</h1>
            <p class="text-gray-500 text-sm md:text-base">هنا تقدر تعدل أو تمسح أي حصة رفعتها</p>
        </div>
        <a href="add_lecture.php" class="w-full md:w-auto text-center bg-blue-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-blue-500/30 hover:scale-105 transition active:scale-95">
            + إضافة حصة جديدة
        </a>
    </div>

    <?php if(isset($msg)): ?>
        <div class="bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 p-4 rounded-2xl mb-6 border border-green-200 dark:border-green-800 font-bold text-center text-sm">
            ✅ <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-700 overflow-hidden">
        
        <div class="overflow-x-auto w-full custom-scrollbar">
            <table class="w-full text-right min-w-[650px]">
                <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-100 dark:border-slate-700">
                    <tr class="text-xs md:text-sm font-black text-gray-500 dark:text-gray-300">
                        <th class="p-5">المحاضرة</th>
                        <th class="p-5">الصف</th>
                        <th class="p-5">السعر</th>
                        <th class="p-5 text-center">التحكم</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-slate-700 text-sm md:text-base">
                    <?php foreach($lectures as $lec): ?>
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/20 transition-colors">
                        <td class="p-5">
                            <div class="flex items-center gap-4">
                                <img src="../uploads/posters/<?php echo htmlspecialchars($lec['thumbnail'] ?? 'default.jpg'); ?>" 
                                    class="w-16 h-10 object-cover rounded-lg shadow-sm flex-shrink-0" 
                                    onerror="this.src='https://placehold.co/100x60?text=No+Img'">
                                <span class="font-bold text-slate-800 dark:text-white whitespace-nowrap">
                                    <?php echo $lec['title']; ?>
                                </span>
                            </div>
                        </td>
                        <td class="p-5">
                            <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-3 py-1 rounded-lg text-xs font-black whitespace-nowrap">
                                <?php 
                                    $grades_names = [
                                        1 => 'أولى إعدادي', 2 => 'تانية إعدادي', 3 => 'تالتة إعدادي',
                                        4 => 'أولى ثانوي', 5 => 'تانية ثانوي', 6 => 'تالتة ثانوي'
                                    ];
                                    echo $grades_names[$lec['target_grade']] ?? 'غير محدد';
                                ?>
                            </span>
                        </td>
                        <td class="p-5 font-black text-slate-700 dark:text-slate-200 whitespace-nowrap">
                            <?php echo number_format($lec['price'], 2); ?> ج.م
                        </td>
                        <td class="p-5">
                            <div class="flex justify-center gap-3">
                                <a href="edit_lecture.php?id=<?php echo $lec['id']; ?>" 
                                   class="p-2.5 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400 rounded-xl hover:bg-yellow-100 transition shadow-sm"
                                   title="تعديل">
                                    ✏️
                                </a>
                                <a href="?delete=<?php echo $lec['id']; ?>" 
                                   onclick="return confirm('هل أنت متأكد من حذف هذه المحاضرة؟ لا يمكن التراجع!')"
                                   class="p-2.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-100 transition shadow-sm"
                                   title="حذف">
                                    🗑️
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if(empty($lectures)): ?>
                    <tr>
                        <td colspan="4" class="p-20 text-center text-gray-400 font-bold">
                            <div class="text-4xl mb-4">📢</div>
                            لا توجد محاضرات حالياً.. ابدأ برفع أول حصة!
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
<script>
    Swal.fire({
        title: 'تم التعديل بنجاح! ✅',
        text: 'تم تحديث بيانات المحاضرة وحفظها في قاعدة البيانات.',
        icon: 'success',
        confirmButtonText: 'حسناً',
        confirmButtonColor: '#2563eb',
        timer: 3000,
        timerProgressBar: true
    });
    window.history.replaceState({}, document.title, window.location.pathname);
</script>
<?php endif; ?>
<?php 

echo "</main></div></body></html>"; 
?>