<?php
require_once '../config.php';
if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}
if (!isset($_GET['id'])) {
    header("Location: manage_quizzes.php");
    exit;
}

$quiz_id = $_GET['id'];


$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();

if (!$quiz) {
    die("الاختبار غير موجود!");
}

$q_stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
$q_stmt->execute([$quiz_id]);
$questions = $q_stmt->fetchAll();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quiz_title = $_POST['quiz_title'];
    
    try {
        $pdo->beginTransaction();


        $stmt = $pdo->prepare("UPDATE quizzes SET quiz_title = ? WHERE id = ?");
        $stmt->execute([$quiz_title, $quiz_id]);


        $pdo->prepare("DELETE FROM quiz_questions WHERE quiz_id = ?")->execute([$quiz_id]);

        if (isset($_POST['questions']) && is_array($_POST['questions'])) {
            foreach ($_POST['questions'] as $q) {
                if (!empty($q['text'])) {
                    $sql = "INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $pdo->prepare($sql)->execute([
                        $quiz_id, $q['text'], $q['a'], $q['b'], $q['c'], $q['d'], $q['correct']
                    ]);
                }
            }
        }

        $pdo->commit();
        header("Location: manage_quizzes.php?msg=updated");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "حدث خطأ: " . $e->getMessage();
    }
}

require_once 'header.php';
?>

<div class="max-w-4xl mx-auto py-10 px-4">
    <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-black text-blue-600 dark:text-blue-400">تعديل الاختبار ✍️</h2>
        <a href="manage_quizzes.php" class="bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-gray-300 px-5 py-2 rounded-xl font-bold text-sm">إلغاء</a>
    </div>

    <form method="POST" class="space-y-8">
        <div class="bg-white dark:bg-slate-800 p-8 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-slate-700">
            <label class="block text-sm font-black mb-3 dark:text-gray-300">عنوان الاختبار</label>
            <input type="text" name="quiz_title" value="<?= htmlspecialchars($quiz['quiz_title']) ?>" 
                   class="w-full p-4 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none ring-1 ring-gray-200 dark:ring-slate-600 focus:ring-2 focus:ring-blue-500 dark:text-white" required>
        </div>
        
        <div id="questions-area" class="space-y-6">
            <?php foreach($questions as $index => $question): ?>
            <div class="p-8 bg-white dark:bg-slate-800 rounded-[2.5rem] border border-gray-100 dark:border-slate-700 shadow-sm relative animate-slide-up">
                <div class="flex justify-between items-center mb-6">
                    <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-4 py-1.5 rounded-xl text-xs font-black">سؤال رقم <?= $index + 1 ?></span>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600 transition text-sm font-bold">حذف السؤال</button>
                </div>

                <input type="text" name="questions[<?= $index ?>][text]" value="<?= htmlspecialchars($question['question_text']) ?>" 
                       class="w-full p-4 mb-6 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none ring-1 ring-gray-100 dark:ring-slate-600 dark:text-white font-bold" required>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="questions[<?= $index ?>][a]" value="<?= htmlspecialchars($question['option_a']) ?>" placeholder="أ" class="p-4 rounded-xl bg-gray-50 dark:bg-slate-700 border-none dark:text-white" required>
                    <input type="text" name="questions[<?= $index ?>][b]" value="<?= htmlspecialchars($question['option_b']) ?>" placeholder="ب" class="p-4 rounded-xl bg-gray-50 dark:bg-slate-700 border-none dark:text-white" required>
                    <input type="text" name="questions[<?= $index ?>][c]" value="<?= htmlspecialchars($question['option_c']) ?>" placeholder="ج" class="p-4 rounded-xl bg-gray-50 dark:bg-slate-700 border-none dark:text-white" required>
                    <input type="text" name="questions[<?= $index ?>][d]" value="<?= htmlspecialchars($question['option_d']) ?>" placeholder="د" class="p-4 rounded-xl bg-gray-50 dark:bg-slate-700 border-none dark:text-white" required>
                </div>

                <div class="mt-6 flex items-center gap-4 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-2xl border border-blue-100 dark:border-blue-900/30">
                    <label class="text-sm font-black text-blue-700 dark:text-blue-300">الإجابة الصحيحة:</label>
                    <select name="questions[<?= $index ?>][correct]" class="p-2 rounded-lg bg-white dark:bg-slate-800 border-none font-bold text-blue-600">
                        <option value="a" <?= $question['correct_option'] == 'a' ? 'selected' : '' ?>>الخيار (أ)</option>
                        <option value="b" <?= $question['correct_option'] == 'b' ? 'selected' : '' ?>>الخيار (ب)</option>
                        <option value="c" <?= $question['correct_option'] == 'c' ? 'selected' : '' ?>>الخيار (ج)</option>
                        <option value="d" <?= $question['correct_option'] == 'd' ? 'selected' : '' ?>>الخيار (د)</option>
                    </select>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="space-y-4">
            <button type="button" onclick="addNewQuestion()" class="w-full py-5 border-2 border-dashed border-blue-300 dark:border-blue-800 rounded-[2rem] text-blue-600 dark:text-blue-400 font-black hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all flex items-center justify-center gap-2">
                <span class="text-2xl">+</span> إضافة سؤال جديد
            </button>

            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-black py-5 rounded-[2rem] shadow-xl transition-all transform active:scale-95">
                تحديث وحفظ التعديلات ✅
            </button>
        </div>
    </form>
</div>

<script>
    let qCount = <?= count($questions) ?>;
    function addNewQuestion() {
        const area = document.getElementById('questions-area');
        const html = `
            <div class="p-8 bg-white dark:bg-slate-800 rounded-[2.5rem] border border-gray-100 dark:border-slate-700 shadow-sm relative animate-slide-up">
                <div class="flex justify-between items-center mb-6">
                    <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-4 py-1.5 rounded-xl text-xs font-black">سؤال جديد</span>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600 transition text-sm font-bold">حذف السؤال</button>
                </div>
                <input type="text" name="questions[${qCount}][text]" placeholder="نص السؤال..." class="w-full p-4 mb-6 rounded-2xl bg-gray-50 dark:bg-slate-700 border-none ring-1 ring-gray-100 dark:ring-slate-600 dark:text-white font-bold" required>
                <div class="grid grid-cols-2 gap-4">
                    <input type="text" name="questions[${qCount}][a]" placeholder="أ" class="p-4 rounded-xl bg-gray-50 dark:bg-slate-700 border-none dark:text-white" required>
                    <input type="text" name="questions[${qCount}][b]" placeholder="ب" class="p-4 rounded-xl bg-gray-50 dark:bg-slate-700 border-none dark:text-white" required>
                    <input type="text" name="questions[${qCount}][c]" placeholder="ج" class="p-4 rounded-xl bg-gray-50 dark:bg-slate-700 border-none dark:text-white" required>
                    <input type="text" name="questions[${qCount}][d]" placeholder="د" class="p-4 rounded-xl bg-gray-50 dark:bg-slate-700 border-none dark:text-white" required>
                </div>
                <div class="mt-6 flex items-center gap-4 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-2xl border border-blue-100 dark:border-blue-900/30 text-sm font-black text-blue-700">
                    الإجابة:
                    <select name="questions[${qCount}][correct]" class="p-2 rounded-lg bg-white dark:bg-slate-800 border-none text-blue-600 font-bold">
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
</script>

<style>
    @keyframes slide-up { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-slide-up { animation: slide-up 0.4s ease-out forwards; }
</style>
