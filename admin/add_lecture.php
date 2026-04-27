<?php
require_once '../config.php'; 


if (session_status() === PHP_SESSION_NONE) { session_start(); }
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

$success_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $title = $_POST['title'];
    $description = $_POST['description'];
    $subject = $_POST['subject'];
    $target_grade = $_POST['target_grade'];
    $video_url = $_POST['video_id']; 
    $pdf_link = $_POST['pdf_link'];
    $duration = $_POST['duration'];
    $price = $_POST['price'] ?? 0;
    $content_type = $_POST['content_type'] ?? 'lesson';
    $quiz_id = !empty($_POST['quiz_id']) ? $_POST['quiz_id'] : null;


    $thumbnail = ''; // defult image
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
        $ext = pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION);
        $thumbnail = time() . '.' . $ext; 
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], '../uploads/posters/' . $thumbnail);
    }

    $sql = "INSERT INTO lectures (title, description, subject, thumbnail, video_url, pdf_link, duration, price, target_grade, content_type, quiz_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    if($stmt->execute([$title, $description, $subject, $thumbnail, $video_url, $pdf_link, $duration, $price, $target_grade, $content_type, $quiz_id])) {

        header("Location: manage_lectures.php?success=1");
        exit;
    }
}


$quizzes = $pdo->query("SELECT id, quiz_title FROM quizzes ORDER BY id DESC")->fetchAll();

include 'header.php'; 
?>
<?php


$quizzes = $pdo->query("SELECT id, quiz_title FROM quizzes ORDER BY id DESC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quiz_id = $_POST['quiz_id'] != "" ? $_POST['quiz_id'] : null;

    $sql = "INSERT INTO lectures (title, description, subject, thumbnail, video_url, pdf_link, duration, price, target_grade, content_type, quiz_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $_POST['title'], $_POST['description'], $_POST['subject'], $thumbnail, 
        $_POST['video_id'], $_POST['pdf_link'], $_POST['duration'], 
        $_POST['price'], $_POST['target_grade'], $_POST['content_type'], $quiz_id
    ]);

    header("Location: manage_lectures.php?success=1");
    exit;
}
?>


<div class="flex-1 max-w-4xl mx-auto px-4 pb-12">
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-800 dark:text-white">إضافة محتوى جديد</h1>
        <p class="text-gray-500">ضع رابط اليوتيوب وسنقوم باستخراج المعرف (ID) تلقائياً</p>
    </div>

    <?php if(isset($success_msg)): ?>
        <div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-4 rounded-xl mb-6 shadow-sm font-bold">
            ✅ <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
        
        <div class="bg-blue-50 dark:bg-slate-800/50 p-8 rounded-[2rem] border-2 border-blue-100 dark:border-slate-700 shadow-sm">
            <h2 class="text-lg font-bold mb-4 flex items-center gap-2 text-blue-600">
                <span>🎥</span> فيديو المحاضرة
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold mb-2">رابط فيديو اليوتيوب</label>
                    <input type="text" id="video_url_input" placeholder="انسخ الرابط هنا... (https://www.youtube.com/watch?v=...)" 
                           class="w-full p-4 rounded-2xl bg-white dark:bg-slate-700 border-none focus:ring-2 focus:ring-blue-500 transition shadow-inner">
                </div>
                
                <input type="hidden" name="video_id" id="extracted_id" required>

                <div class="flex items-center gap-4 p-4 bg-white/50 dark:bg-slate-900 rounded-2xl border border-dashed border-blue-200 dark:border-slate-600">
                    <span class="text-sm font-bold">معرف الفيديو المستخرج:</span>
                    <span id="id_display" class="font-mono font-black text-blue-600 dark:text-blue-400">---</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-8 rounded-[2rem] shadow-sm border border-gray-100 dark:border-slate-700">
            <h2 class="text-lg font-bold mb-6 flex items-center gap-2 text-slate-700 dark:text-blue-400">
                <span>📝</span> تفاصيل المحتوى
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold mb-2">عنوان الحصة / المراجعة</label>
                    <input type="text" name="title" class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold mb-2 text-slate-700 dark:text-slate-300">وصف المحاضرة (اختياري)</label>
                    <textarea name="description" rows="3" placeholder="اكتب نبذة عن محتوى الحصة..." 
                            class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none focus:ring-2 focus:ring-blue-500 transition shadow-inner text-slate-800 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2">الصف الدراسي</label>
                    <select name="target_grade" class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none focus:ring-2 focus:ring-blue-500">
                        <option value="1">الأول الإعدادي</option>
                        <option value="2">الثاني الإعدادي</option>
                        <option value="3" selected>الثالث الإعدادي</option>
                        <option value="4">الأول الثانوي</option>
                        <option value="5">الثاني الثانوي</option>
                        <option value="6">الثالث الثانوي</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">نوع المحتوى</label>
                    <select name="content_type" class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none focus:ring-2 focus:ring-blue-500">
                        <option value="lesson">حصة عادية</option>
                        <option value="revision">مراجعة نهائية</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold mb-2 text-slate-700">ربط اختبار بالمحاضرة (اختياري)</label>
                    <select name="quiz_id" class="w-full p-4 rounded-2xl bg-gray-50 border-none ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500">
                        <option value="">-- بدون اختبار --</option>
                        <?php foreach($quizzes as $quiz): ?>
                            <option value="<?= $quiz['id'] ?>"><?= htmlspecialchars($quiz['quiz_title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-blue-500 mt-2 font-bold italic">* يمكنك إنشاء اختبارات جديدة من "صفحة بنك الاختبارات"</p>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2">السعر (0 للمجاني)</label>
                    <input type="number" name="price" value="0" class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">المادة</label>
                    <input type="text" name="subject" placeholder="مثلاً: جغرافيا" class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">رابط المذكرة (PDF)</label>
                    <input type="url" name="pdf_link" placeholder="رابط جوجل درايف" class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">مدة الفيديو (بالدقائق)</label>
                    <input type="number" name="duration" placeholder="60" class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold mb-2">بوستر المحاضرة (Thumbnail)</label>
                    <input type="file" name="thumbnail" class="w-full p-4 rounded-2xl border-2 border-dashed border-gray-200 dark:border-slate-700">
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-2xl shadow-lg transition-transform active:scale-95">
            حفظ ونشر المحاضرة الآن 🚀
        </button>
    </form>
</div>

<script>
    const urlInput = document.getElementById('video_url_input');
    const hiddenIdInput = document.getElementById('extracted_id');
    const idDisplay = document.getElementById('id_display');

    urlInput.addEventListener('input', function(e) {
        const url = e.target.value.trim();
        const videoId = extractVideoID(url);

        if (videoId) {
            hiddenIdInput.value = videoId;
            idDisplay.innerText = videoId;
            idDisplay.classList.replace('text-blue-600', 'text-green-600');
        } else {
            hiddenIdInput.value = "";
            idDisplay.innerText = url === "" ? "---" : "رابط غير صالح ❌";
            idDisplay.classList.replace('text-green-600', 'text-red-500');
        }
    });

    function extractVideoID(url) {
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length == 11) ? match[2] : false;
    }
</script>
