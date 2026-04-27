<?php
require_once '../config.php';
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quiz_title = $_POST['quiz_title'];
    
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO quizzes (quiz_title) VALUES (?)");
        $stmt->execute([$quiz_title]);
        $quiz_id = $pdo->lastInsertId();

        if (isset($_POST['questions']) && is_array($_POST['questions'])) {
            foreach ($_POST['questions'] as $q) {
                if (!empty($q['text'])) {
                    $sql = "INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $pdo->prepare($sql)->execute([
                        $quiz_id, 
                        $q['text'], 
                        $q['a'], 
                        $q['b'], 
                        $q['c'], 
                        $q['d'], 
                        $q['correct']
                    ]);
                }
            }
        }

        $pdo->commit();
        header("Location: manage_quizzes.php?success=1");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "حدث خطأ أثناء الحفظ: " . $e->getMessage();
    }
}


require_once 'header.php'; 
?>

<div class="max-w-4xl mx-auto py-10 px-4">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-black text-blue-600 dark:text-blue-400">إنشاء اختبار جديد 📝</h2>
            <p class="text-gray-500 dark:text-gray-400 font-bold mt-1">أضف أسئلتك لتبني بنك اختبارات قوي</p>
        </div>
        <a href="manage_quizzes.php" class="bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-300 px-5 py-2 rounded-xl font-bold text-sm hover:bg-gray-200 transition">
            رجوع للبنك
        </a>
    </div>

    <?php if(isset($error)): ?>
        <div class="bg-red-100 text-red-600 p-4 rounded-2xl mb-6 font-bold text-center border border-red-200">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-8">
        <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-slate-700">
            <label class="block text-sm font-black mb-3 text-gray-700 dark:text-gray-300">عنوان الاختبار</label>
            <input type="text" name="quiz_title" 
                   placeholder="مثال: اختبار شامل على الوحدة الأولى" 
                   class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none ring-1 ring-gray-200 dark:ring-slate-600 focus:ring-2 focus:ring-blue-500 dark:text-white" required>
        </div>
        
        <div id="questions-area" class="space-y-6">
            </div>

        <div class="space-y-4">
            <button type="button" onclick="addNewQuestion()" 
                    class="w-full py-5 border-2 border-dashed border-blue-300 dark:border-blue-800 rounded-[2rem] text-blue-600 dark:text-blue-400 font-black hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all flex items-center justify-center gap-2">
                <span class="text-2xl">+</span> إضافة سؤال اختيار من متعدد (MCQ)
            </button>

            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-[2rem] shadow-xl shadow-blue-500/20 transition-all transform active:scale-95">
                حفظ الاختبار في البنك ✅
            </button>
        </div>
    </form>
</div>

<script>
    let qCount = 0;

    function addNewQuestion() {
        const area = document.getElementById('questions-area');
        const html = `
            <div class="p-8 bg-white dark:bg-slate-800 rounded-[2.5rem] border border-gray-100 dark:border-slate-700 shadow-sm relative animate-slide-up">
                <div class="flex justify-between items-center mb-6">
                    <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-4 py-1.5 rounded-xl text-xs font-black">سؤال رقم ${qCount + 1}</span>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600 transition text-sm font-bold italic">حذف السؤال</button>
                </div>

                <input type="text" name="questions[${qCount}][text]" placeholder="اكتب نص السؤال هنا..." 
                       class="w-full p-4 mb-6 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none ring-1 ring-gray-100 dark:ring-slate-600 focus:ring-2 focus:ring-blue-500 dark:text-white font-bold" required>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="relative">
                        <span class="absolute right-4 top-4 text-gray-400 font-black">أ</span>
                        <input type="text" name="questions[${qCount}][a]" placeholder="الخيار الأول" class="w-full pr-10 p-4 rounded-xl bg-gray-50 dark:bg-slate-700 border-none dark:text-white" required>
                    </div>
                    <div class="relative">
                        <span class="absolute right-4 top-4 text-gray-400 font-black">ب</span>
                        <input type="text" name="questions[${qCount}][b]" placeholder="الخيار الثاني" class="w-full pr-10 p-4 rounded-xl bg-gray-50 dark:bg-slate-700 border-none dark:text-white" required>
                    </div>
                    <div class="relative">
                        <span class="absolute right-4 top-4 text-gray-400 font-black">ج</span>
                        <input type="text" name="questions[${qCount}][c]" placeholder="الخيار الثالث" class="w-full pr-10 p-4 rounded-xl bg-gray-50 dark:bg-slate-700 border-none dark:text-white" required>
                    </div>
                    <div class="relative">
                        <span class="absolute right-4 top-4 text-gray-400 font-black">د</span>
                        <input type="text" name="questions[${qCount}][d]" placeholder="الخيار الرابع" class="w-full pr-10 p-4 rounded-xl bg-gray-50 dark:bg-slate-700 border-none dark:text-white" required>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-4 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-2xl border border-blue-100 dark:border-blue-900/30">
                    <label class="text-sm font-black text-blue-700 dark:text-blue-300">الإجابة الصحيحة:</label>
                    <select name="questions[${qCount}][correct]" class="p-2 rounded-lg bg-white dark:bg-slate-800 border-none shadow-sm font-bold text-blue-600">
                        <option value="a">الخيار (أ)</option>
                        <option value="b">الخيار (ب)</option>
                        <option value="c">الخيار (ج)</option>
                        <option value="d">الخيار (د)</option>
                    </select>
                </div>
            </div>`;
        
        area.insertAdjacentHTML('beforeend', html);
        qCount++;
    }

    window.onload = addNewQuestion;
</script>

<style>
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-slide-up { animation: slide-up 0.4s ease-out forwards; }
</style>