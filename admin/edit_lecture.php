<?php
require_once '../config.php'; 

if (session_status() === PHP_SESSION_NONE) { session_start(); }
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}


if (!isset($_GET['id'])) {
    header("Location: manage_lectures.php");
    exit;
}

$id = $_GET['id'];


$stmt = $pdo->prepare("SELECT * FROM lectures WHERE id = ?");
$stmt->execute([$id]);
$lecture = $stmt->fetch();

if (!$lecture) {
    die("المحاضرة غير موجودة!");
}
$quizzes_stmt = $pdo->query("SELECT id, quiz_title FROM quizzes ORDER BY id DESC");
$all_quizzes = $quizzes_stmt->fetchAll();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $title        = $_POST['title'] ?? '';
    $description  = $_POST['description'] ?? '';
    $subject      = $_POST['subject'] ?? '';
    $target_grade = $_POST['target_grade'] ?? '';
    $video_url    = $_POST['video_id'] ?? ''; 
    $pdf_link     = $_POST['pdf_link'] ?? '';
    $duration     = $_POST['duration'] ?? '';
    $price        = $_POST['price'] ?? 0;
    $content_type = $_POST['content_type'] ?? 'lesson';
    $quiz_id      = !empty($_POST['quiz_id']) ? $_POST['quiz_id'] : null;

    $thumbnail = $lecture['thumbnail']; 
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $thumbnail = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], '../uploads/posters/' . $thumbnail);
        
        if ($lecture['thumbnail'] != 'default.jpg' && file_exists('../uploads/posters/' . $lecture['thumbnail'])) {
            unlink('../uploads/posters/' . $lecture['thumbnail']);
        }
    }

    $sql = "UPDATE lectures SET 
            title=?, description=?, subject=?, thumbnail=?, video_url=?, 
            pdf_link=?, duration=?, price=?, target_grade=?, content_type=?, quiz_id=? 
            WHERE id=?";
    
    $update_stmt = $pdo->prepare($sql);
    $update_stmt->execute([
        $title, $description, $subject, $thumbnail, $video_url, 
        $pdf_link, $duration, $price, $target_grade, $content_type, $quiz_id, $id
    ]);

    header("Location: manage_lectures.php?msg=updated");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل المحاضرة: <?php echo htmlspecialchars($lecture['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'IBM Plex Sans Arabic', sans-serif; }</style>
</head>
<body class="bg-gray-50 dark:bg-slate-900 p-4 md:p-10">

<div class="max-w-4xl mx-auto bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-xl border border-gray-100 dark:border-slate-700">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-black text-slate-800 dark:text-white">تعديل المحاضرة <i class="fa-solid fa-pen text-blue-500"></i></h2>
        <a href="manage_lectures.php" class="text-gray-400 hover:text-red-500 transition font-bold">إلغاء والعودة</a>
    </div>

    <form action="" method="POST" enctype="multipart/form-data" class="space-y-6" id="editForm">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-bold mb-2">عنوان المحاضرة</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($lecture['title']); ?>" required
                       class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold mb-2">الوصف</label>
                <textarea name="description" rows="3"
                          class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($lecture['description'] ?? ''); ?></textarea>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">نوع المحتوى</label>
                <select name="content_type" class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none focus:ring-2 focus:ring-blue-500">
                    <option value="lesson" <?php if($lecture['content_type'] == 'lesson') echo 'selected'; ?>>حصة عادية</option>
                    <option value="revision" <?php if($lecture['content_type'] == 'revision') echo 'selected'; ?>>مراجعة نهائية</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">رابط المذكرة (PDF)</label>
                <input type="url" name="pdf_link" value="<?php echo htmlspecialchars($lecture['pdf_link'] ?? ''); ?>" placeholder="رابط جوجل درايف..."
                       class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="md:col-span-2 bg-orange-50 dark:bg-orange-900/10 p-6 rounded-3xl border border-orange-100 dark:border-orange-800/30">
                <label class="block text-sm font-black mb-3 text-orange-700 dark:text-orange-400">ربط اختبار (Quiz) بالمحاضرة</label>
                <select name="quiz_id" class="w-full p-4 rounded-2xl bg-white dark:bg-slate-800 dark:text-white border-none shadow-sm focus:ring-2 focus:ring-orange-500">
                    <option value="">-- بدون اختبار --</option>
                    <?php foreach($all_quizzes as $quiz): ?>
                        <option value="<?= $quiz['id'] ?>" <?= ($lecture['quiz_id'] == $quiz['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($quiz['quiz_title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="md:col-span-2 bg-blue-50 dark:bg-blue-900/10 p-5 rounded-3xl border border-blue-100 dark:border-blue-800/30">
                <label class="block text-sm font-bold mb-2 text-blue-700 dark:text-blue-400">تحديث رابط الفيديو (YouTube)</label>
                <input type="text" id="video_url_input" placeholder="ضع الرابط الجديد هنا إذا أردت تغييره..."
                       class="w-full p-4 rounded-xl bg-white dark:bg-slate-800 border-none mb-2 shadow-sm focus:ring-2 focus:ring-blue-500">
                <p class="text-xs font-bold text-gray-500 mt-2">الـ ID الحالي: <span id="id_display" class="text-blue-600"><?php echo htmlspecialchars($lecture['video_url']); ?></span></p>
                <input type="hidden" name="video_id" id="extracted_id" value="<?php echo htmlspecialchars($lecture['video_url']); ?>">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">السعر (ج.م)</label>
                <input type="number" name="price" value="<?php echo htmlspecialchars($lecture['price']); ?>"
                       class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-bold mb-2">المدة (دقائق)</label>
                <input type="number" name="duration" value="<?php echo htmlspecialchars($lecture['duration']); ?>"
                       class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-bold mb-2">الصف الدراسي</label>
                <select name="target_grade" class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none appearance-none focus:ring-2 focus:ring-blue-500">
                    <option value="1" <?php if($lecture['target_grade']==1) echo 'selected'; ?>>الأول الإعدادي</option>
                    <option value="2" <?php if($lecture['target_grade']==2) echo 'selected'; ?>>الثاني الإعدادي</option>
                    <option value="3" <?php if($lecture['target_grade']==3) echo 'selected'; ?>>الثالث الإعدادي</option>
                    <option value="4" <?php if($lecture['target_grade']==4) echo 'selected'; ?>>الأول الثانوي</option>
                    <option value="5" <?php if($lecture['target_grade']==5) echo 'selected'; ?>>الثاني الثانوي</option>
                    <option value="6" <?php if($lecture['target_grade']==6) echo 'selected'; ?>>الثالث الثانوي</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-bold mb-2">المادة / التصنيف</label>
                <input type="text" name="subject" value="<?php echo htmlspecialchars($lecture['subject']); ?>"
                       class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold mb-2">تغيير البوستر (اتركه فارغاً للاحتفاظ بالقديم)</label>
                <div class="flex items-center gap-4">
                    <img src="../uploads/posters/<?php echo htmlspecialchars($lecture['thumbnail']); ?>" class="w-20 h-20 rounded-xl object-cover border-2 border-gray-200">
                    <input type="file" name="thumbnail" accept="image/*" class="flex-1 p-4 rounded-2xl border-2 border-dashed border-gray-200 dark:border-slate-700 focus:outline-none focus:border-blue-500">
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-2xl shadow-lg transition-transform active:scale-95">
            حفظ التعديلات الآن ✅
        </button>
    </form>
</div>

<script>
    function extractVideoID(url) {
        const regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : false;
    }

    const urlInput = document.getElementById('video_url_input');
    const hiddenIdInput = document.getElementById('extracted_id');
    const idDisplay = document.getElementById('id_display');

    urlInput.addEventListener('input', function(e) {
        const url = e.target.value.trim();
        const videoId = extractVideoID(url);
        
        if (videoId) {
            hiddenIdInput.value = videoId;
            idDisplay.innerText = videoId;
            idDisplay.className = "text-green-600 font-bold mt-2 inline-block";
        } else if(url !== "") {
            idDisplay.innerText = "رابط غير صالح ❌";
            idDisplay.className = "text-red-500 font-bold mt-2 inline-block";
        } else {

            hiddenIdInput.value = "<?php echo htmlspecialchars($lecture['video_url']); ?>";
            idDisplay.innerText = "<?php echo htmlspecialchars($lecture['video_url']); ?>";
            idDisplay.className = "text-blue-600 font-bold mt-2 inline-block";
        }
    });


    const form = document.getElementById('editForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'هل تريد حفظ التعديلات؟',
            text: "سيتم تحديث بيانات المحاضرة فوراً",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احفظ',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'جاري الحفظ...',
                    html: 'برجاء الانتظار قليلاً',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                form.submit();
            }
        });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js"></script>
</body>
</html>