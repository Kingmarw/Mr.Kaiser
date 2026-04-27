

<?php
require_once 'config.php';




if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$lecture_id = (int)($_GET['id'] ?? 0);
$error = '';
$success = '';


if (isset($_GET['buy_status']) && $_GET['buy_status'] == 'success') {
    $success = "تم شراء الحصة بنجاح! نتمنى لك مشاهدة ممتعة ومفيدة.";
}


$stmt = $pdo->prepare("SELECT * FROM lectures WHERE id = ?");
$stmt->execute([$lecture_id]);
$lecture = $stmt->fetch();

if (!$lecture) {
    die("المحاضرة غير موجودة");
}


$check_purchase = $pdo->prepare("SELECT id FROM purchases WHERE user_id = ? AND lecture_id = ?");
$check_purchase->execute([$user_id, $lecture_id]);
$already_purchased = $check_purchase->fetch();

if ($lecture['price'] == 0) {
    $already_purchased = true;
}

$show_video = (bool)$already_purchased;

if (!$show_video && isset($_POST['buy_now'])) {
    try {
        $pdo->beginTransaction();


        $u_stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ? FOR UPDATE");
        $u_stmt->execute([$user_id]);
        $current_balance = $u_stmt->fetchColumn();


        if ($current_balance >= $lecture['price']) {
            

            $update_balance = $pdo->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $update_balance->execute([$lecture['price'], $user_id]);
            

            $insert_purchase = $pdo->prepare("INSERT IGNORE INTO purchases (user_id, lecture_id, price_paid) VALUES (?, ?, ?)");
            $insert_purchase->execute([$user_id, $lecture_id, $lecture['price']]);
            
            $pdo->commit();


            $_SESSION['user_balance'] = $current_balance - $lecture['price'];


            header("Location: view_lecture.php?id=$lecture_id&buy_status=success");
            exit;

        } else {

            $pdo->rollBack();
            $error = "عفواً! رصيدك غير كافٍ لشراء هذه الحصة. برجاء الشحن أولاً.";
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "حدث خطأ أثناء إتمام العملية، برجاء المحاولة مرة أخرى.";
    }
}


$questions = [];
$quiz_title = "";
$user_quiz_data = null;

if ($show_video && !empty($lecture['quiz_id'])) {
    $q_stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
    $q_stmt->execute([$lecture['quiz_id']]);
    $quiz = $q_stmt->fetch();
    
    if ($quiz) {
        $quiz_title = $quiz['quiz_title'];
        $qq_stmt = $pdo->prepare("SELECT id, quiz_id, question_text, option_a, option_b, option_c, option_d FROM quiz_questions WHERE quiz_id = ?");
        $qq_stmt->execute([$lecture['quiz_id']]);
        $questions = $qq_stmt->fetchAll();


        $res_stmt = $pdo->prepare("SELECT * FROM quiz_results WHERE user_id = ? AND quiz_id = ?");
        $res_stmt->execute([$user_id, $lecture['quiz_id']]);
        $user_quiz_data = $res_stmt->fetch();
    }
}

$progress = ['video_done' => false, 'pdf_done' => false];
if ($show_video) {
    $prog_stmt = $pdo->prepare("SELECT video_done, pdf_done FROM lecture_progress WHERE user_id = ? AND lecture_id = ?");
    $prog_stmt->execute([$user_id, $lecture_id]);
    $prog_row = $prog_stmt->fetch();
    if ($prog_row) {
        $progress['video_done'] = (bool)$prog_row['video_done'];
        $progress['pdf_done']   = (bool)$prog_row['pdf_done'];
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lecture['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    
    <style>
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; }
        :root { --plyr-color-main: #2563eb; }
        .plyr--full-ui { border-radius: 1.5rem; overflow: hidden; }
        .plyr iframe { pointer-events: none !important; }

        .tab-content { display: none; animation: fadeIn 0.4s ease-in-out; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        .done-checkbox:checked + div {
            background-color: #22c55e !important; 
            border-color: #22c55e !important;
            color: white !important;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white min-h-screen">

    <?php if (!empty($error)): ?>
        <script>Swal.fire({title: 'خطأ!', text: '<?= addslashes(htmlspecialchars($error)) ?>', icon: 'error', confirmButtonText: 'حسناً'});</script>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <script>
            Swal.fire({title: 'مبروك!', text: '<?= addslashes(htmlspecialchars($success)) ?>', icon: 'success', confirmButtonText: 'ابدأ المذاكرة'});
            window.history.replaceState(null, null, window.location.pathname + '?id=<?= $lecture_id ?>');
        </script>
    <?php endif; ?>

    <nav class="bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 p-4 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-black text-blue-600 dark:text-blue-400 truncate w-2/3">
                📚 <?= htmlspecialchars($lecture['title']) ?>
            </h1>
            <a href="lectures.php" class="bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-gray-200 transition">
                العودة للمكتبة
            </a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-8 px-4">
        
        <?php if ($show_video): ?>
        <div class="flex flex-col md:flex-row gap-8">
            <aside class="w-full md:w-1/3 lg:w-1/4 space-y-4">
                <div class="bg-white dark:bg-slate-800 p-6 rounded-[2rem] shadow-sm border border-gray-100 dark:border-slate-700 sticky top-24">
                    <h3 class="font-black text-lg mb-6 text-slate-800 dark:text-white">محتويات الحصة 🎯</h3>
                    <ul class="space-y-4">
                        
                        <?php if(!empty($lecture['video_url'])): ?>
                        <li class="flex items-center gap-3">
                            <label class="cursor-pointer relative flex items-center">
                                <input type="checkbox" class="done-checkbox opacity-0 absolute h-0 w-0" id="check-video" onchange="saveProgress('video')">
                                <div class="w-6 h-6 rounded-full border-2 border-gray-300 dark:border-gray-600 flex items-center justify-center text-transparent transition-colors duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </label>
                            <button onclick="switchTab('video-tab')" class="tab-btn w-full text-right p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 font-bold transition hover:bg-blue-100 flex items-center gap-2">
                                <span>🎥</span> شرح الفيديو
                            </button>
                        </li>
                        <?php endif; ?>

                        <?php if(!empty($lecture['pdf_link'])): ?>
                        <li class="flex items-center gap-3">
                            <label class="cursor-pointer relative flex items-center">
                                <input type="checkbox" class="done-checkbox opacity-0 absolute h-0 w-0" id="check-pdf" onchange="saveProgress('pdf')">
                                <div class="w-6 h-6 rounded-full border-2 border-gray-300 dark:border-gray-600 flex items-center justify-center text-transparent transition-colors duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                            </label>
                            <button onclick="switchTab('pdf-tab')" class="tab-btn w-full text-right p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-600 dark:text-gray-300 font-bold transition flex items-center gap-2">
                                <span>📄</span> مذكرة الحصة
                            </button>
                        </li>
                        <?php endif; ?>

                        <?php if(!empty($questions)): ?>
                        <li class="flex items-center gap-3">
                            <div id="quiz-status-icon" class="w-6 h-6 rounded-full border-2 border-gray-300 dark:border-gray-600 flex items-center justify-center text-transparent transition-colors duration-300">
                                <span id="quiz-icon-mark" class="text-sm font-black"></span>
                            </div>
                            <button onclick="switchTab('quiz-tab')" class="tab-btn w-full text-right p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-600 dark:text-gray-300 font-bold transition flex items-center justify-between gap-2">
                                <span><span>📝</span> اختبار الحصة</span>
                                <span id="attempt-badge" class="text-[10px] bg-gray-200 dark:bg-slate-600 px-2 py-1 rounded-lg"></span>
                            </button>
                        </li>
                        <?php endif; ?>

                    </ul>
                </div>
            </aside>

            <main class="w-full md:w-2/3 lg:w-3/4">
                
                <?php if(!empty($lecture['video_url'])): ?>
                <div id="video-tab" class="tab-content active bg-white dark:bg-slate-800 p-2 md:p-6 rounded-[2rem] shadow-sm border border-gray-100 dark:border-slate-700">
                    <div class="shadow-2xl border border-gray-200 dark:border-slate-600 rounded-3xl overflow-hidden bg-black relative">
                        <div id="player" data-plyr-provider="youtube" data-plyr-embed-id="<?= htmlspecialchars($lecture['video_url']); ?>"></div>
                    </div>
                    <div class="mt-6 px-4">
                        <p class="text-gray-500 dark:text-gray-400 font-bold leading-loose"><?= nl2br(htmlspecialchars($lecture['description'])) ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if(!empty($lecture['pdf_link'])): ?>
                <div id="pdf-tab" class="tab-content bg-white dark:bg-slate-800 p-10 rounded-[2rem] shadow-sm border border-gray-100 dark:border-slate-700 text-center">
                    <div class="w-24 h-24 bg-red-100 text-red-500 rounded-3xl mx-auto flex items-center justify-center text-5xl mb-6 shadow-inner">📄</div>
                    <h2 class="text-2xl font-black mb-4">مذكرة الحصة</h2>
                    <a href="<?= htmlspecialchars($lecture['pdf_link']) ?>" target="_blank" 
                       class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-black px-8 py-4 rounded-2xl shadow-lg transition transform hover:-translate-y-1">
                        عرض وتحميل المذكرة 📥
                    </a>
                </div>
                <?php endif; ?>

                <?php if(!empty($questions)): ?>
                <div id="quiz-tab" class="tab-content bg-white dark:bg-slate-800 p-6 md:p-10 rounded-[2rem] shadow-sm border border-gray-100 dark:border-slate-700">
                    <div class="flex justify-between items-center mb-8 border-b border-gray-100 dark:border-slate-700 pb-4">
                        <h2 class="text-2xl font-black">📝 <?= htmlspecialchars($quiz_title) ?></h2>
                        <span class="bg-orange-100 text-orange-600 px-4 py-2 rounded-xl text-sm font-black"><?= count($questions) ?> أسئلة</span>
                    </div>

                    <form id="quiz-form" class="space-y-8">
                        <?php foreach($questions as $index => $q): ?>
                        <div class="quiz-question bg-gray-50 dark:bg-slate-700 p-6 rounded-3xl border border-gray-100 dark:border-slate-600" data-qid="<?= $q['id'] ?>">
                            <p class="text-lg font-black mb-6"><?= ($index + 1) ?>. <?= htmlspecialchars($q['question_text']) ?></p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <?php foreach(['a', 'b', 'c', 'd'] as $opt): ?>
                                <?php if (!empty($q['option_' . $opt])): ?>
                                <label class="flex items-center justify-between p-4 bg-white dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 rounded-2xl cursor-pointer transition-all hover:border-blue-500 group">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="q_<?= $q['id'] ?>" value="<?= $opt ?>" class="w-4 h-4 accent-blue-600" required>
                                        <span class="font-bold"><?= htmlspecialchars($q['option_' . $opt]) ?></span>
                                    </div>
                                </label>
                                <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <div id="quiz-feedback" class="hidden p-6 rounded-2xl text-center font-black text-xl mb-4"></div>

                        <button type="button" id="submit-quiz-btn" onclick="submitQuiz()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-2xl shadow-xl transition transform active:scale-95 text-lg">
                            تسليم الإجابات (محاولة <span id="attempt-num">1</span>/3)
                        </button>
                    </form>
                </div>
                <?php endif; ?>

            </main>
        </div>

        <?php else: // شاشة الدفع لو مشتراش ?>
        <div class="max-w-2xl mx-auto mt-10 md:mt-20 text-center bg-white dark:bg-slate-800 p-8 md:p-16 rounded-[3rem] border border-gray-100 dark:border-slate-700 shadow-2xl">
            <div class="text-6xl md:text-8xl mb-6">🔒</div>
            <h2 class="text-3xl md:text-4xl font-black mb-4">هذه الحصة مغلقة</h2>
            <p class="text-gray-500 mb-8 font-bold">لفتح المحاضرة والمذكرة والاختبار، يجب تفعيل الاشتراك.</p>
            
            <form id="purchaseForm" method="POST" class="w-full max-w-sm mx-auto space-y-4">
                <input type="hidden" name="buy_now" value="1">
                <div class="bg-blue-50 dark:bg-slate-900 px-6 py-5 rounded-2xl border border-blue-100 dark:border-slate-700">
                    <span class="text-gray-500 block text-sm font-bold">سعر الحصة</span>
                    <span class="text-3xl font-black text-blue-600 dark:text-blue-400"><?= htmlspecialchars($lecture['price']); ?> ج.م</span>
                </div>
                <button type="button" onclick="confirmPurchase()" class="w-full bg-yellow-400 hover:bg-yellow-500 text-slate-900 font-black px-6 py-5 rounded-2xl shadow-xl transition transform hover:scale-105 active:scale-95 text-xl">
                    تأكيد الشراء الآن ⚡
                </button>
            </form>
            <div class="max-w-sm mx-auto mt-4">
                <a href="recharge.php" class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-4 rounded-2xl shadow-lg transition transform hover:scale-105 text-lg">
                    اشحن رصيدك 💳
                </a>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <?php if (!$show_video): ?>
    <script>
        function confirmPurchase() {
            Swal.fire({
                title: 'تأكيد الخصم',
                text: "هل أنت متأكد من رغبتك في خصم <?= addslashes(htmlspecialchars($lecture['price'])) ?> ج.م من رصيدك؟",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#d33',
                confirmButtonText: 'نعم، اشترِ الآن',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('purchaseForm').submit();
                }
            });
        }
    </script>
    <?php endif; ?>

    <?php if ($show_video): ?>
    <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const playerElement = document.getElementById('player');
            if (playerElement) {
                const player = new Plyr(playerElement, {
                    controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'settings', 'fullscreen'],
                    settings: ['quality', 'speed'],
                    speed: { selected: 1, options: [0.5, 1, 1.25, 1.5, 2] },
                    youtube: { noCookie: true, rel: 0, showinfo: 0, iv_load_policy: 3, modestbranding: 1, controls: 0, disablekb: 1 }
                });
            }
        });
        document.addEventListener('contextmenu', event => event.preventDefault());
        document.onkeydown = function(e) {
            if(e.keyCode == 123 || (e.ctrlKey && e.shiftKey && (e.keyCode == 73 || e.keyCode == 67 || e.keyCode == 74)) || (e.ctrlKey && e.keyCode == 85)) return false;
        };


        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');

            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-blue-50', 'dark:bg-blue-900/20', 'text-blue-600', 'dark:text-blue-400');
                btn.classList.add('hover:bg-gray-50', 'dark:hover:bg-slate-700', 'text-gray-600', 'dark:text-gray-300');
            });
            event.currentTarget.classList.remove('hover:bg-gray-50', 'dark:hover:bg-slate-700', 'text-gray-600', 'dark:text-gray-300');
            event.currentTarget.classList.add('bg-blue-50', 'dark:bg-blue-900/20', 'text-blue-600', 'dark:text-blue-400');
        }

        const userId = <?= $user_id ?>;
        const lectureId = <?= $lecture_id ?>;


        const initialProgress = {
            video: <?= $progress['video_done'] ? 'true' : 'false' ?>,
            pdf: <?= $progress['pdf_done'] ? 'true' : 'false' ?>
        };

        function saveProgress(type) {
            const videoDone = document.getElementById('check-video')?.checked ? 1 : 0;
            const pdfDone   = document.getElementById('check-pdf')?.checked ? 1 : 0;

            const formData = new FormData();
            formData.append('lecture_id', lectureId);
            formData.append('video_done', videoDone);
            formData.append('pdf_done', pdfDone);

            fetch('save_progress.php', {
                method: 'POST',
                body: formData
            }).catch(err => console.error('Progress save error:', err));
        }

        const maxAttempts = 3;
        const quizId = <?= !empty($lecture['quiz_id']) ? $lecture['quiz_id'] : 0 ?>;
        let attempts = <?= $user_quiz_data ? (int)$user_quiz_data['attempts'] : 0 ?>;
        let quizStatus = '<?= $user_quiz_data ? addslashes(htmlspecialchars($user_quiz_data['status'])) : "none" ?>';

        function initQuizUI() {
            const iconDiv = document.getElementById('quiz-status-icon');
            const markSpan = document.getElementById('quiz-icon-mark');
            const badge = document.getElementById('attempt-badge');
            const btn = document.getElementById('submit-quiz-btn');
            const feedback = document.getElementById('quiz-feedback');

            if (quizStatus === 'passed') {
                iconDiv.className = "w-6 h-6 rounded-full flex items-center justify-center text-white bg-green-500 border-green-500";
                markSpan.innerHTML = "✔";
                badge.innerText = "ناجح";
                badge.className = "text-[10px] bg-green-100 text-green-700 px-2 py-1 rounded-lg";

                if (feedback) {
                    feedback.style.display = 'block';
                    feedback.className = 'p-6 rounded-2xl text-center font-black text-xl mb-4 bg-green-100 text-green-700';
                    feedback.innerHTML = "لقد اجتزت الاختبار بنجاح 🎉 ويمكنك إعادة المحاولة لتحسين نتيجتك.";
                }

                return;
            }

            if (quizStatus === 'failed' && attempts >= maxAttempts) {
                iconDiv.className = "w-6 h-6 rounded-full flex items-center justify-center text-white bg-red-500 border-red-500";
                markSpan.innerHTML = "✖";
                badge.innerText = "راسب";
                badge.className = "text-[10px] bg-red-100 text-red-700 px-2 py-1 rounded-lg";
                if (btn) btn.style.display = 'none';
                if (feedback) {
                    feedback.style.display = 'block';
                    feedback.className = 'p-6 rounded-2xl text-center font-black text-xl mb-4 bg-red-100 text-red-700';
                    feedback.innerHTML = "لقد استنفدت جميع المحاولات (رسوب) ❌";
                }
                disableForm();
            } else {
                if (badge) badge.innerText = `محاولة ${attempts}/3`;
                const attemptNumEl = document.getElementById('attempt-num');
                if (attemptNumEl) attemptNumEl.innerText = (attempts + 1);
            }
        }

        function disableForm() {
            document.querySelectorAll('.quiz-question input').forEach(inp => inp.disabled = true);
        }

        function submitQuiz() {
            const form = document.getElementById('quiz-form');
            if(!form.checkValidity()) { form.reportValidity(); return; }

            const btn = document.getElementById('submit-quiz-btn');
            btn.disabled = true;
            btn.innerHTML = 'جاري التصحيح... ⏳';

            const answers = {};
            document.querySelectorAll('.quiz-question').forEach((div) => {
                const qid = div.getAttribute('data-qid');
                const selected = div.querySelector('input:checked');
                if (selected) answers[qid] = selected.value;
            });

            const formData = new FormData();
            formData.append('quiz_id', quizId);
            formData.append('answers', JSON.stringify(answers));

            fetch('save_quiz_result.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;

                if (data.attempts !== undefined) {
                    let nextAttempt = parseInt(data.attempts) + 1;
                    if (nextAttempt <= maxAttempts) {
                        btn.innerHTML = "تسليم الإجابات (محاولة " + nextAttempt + "/3)";
                    }
                }

                if (data.success) {
                    quizStatus = data.status;
                    attempts = data.attempts;

                    const score = data.score;
                    const total = data.total;

                    if (data.status === 'passed') {
                        const successSound = new Audio('https://www.myinstants.com/media/sounds/yh-lrq-dh.mp3');
                        successSound.play();
                        Swal.fire({ 
                            title: 'عاش يا بطل! 🎉', 
                            text: "جبت " + score + " من " + total + "، وتقدر تعيد المحاولة لو حبيت.",
                            icon: 'success'
                        }).then(() => {
                            quizStatus = data.status;
                            initQuizUI();
                        });
                    } else {
                        if (attempts >= maxAttempts) {
                            const fail = new Audio('https://www.myinstants.com/media/sounds/error_CDOxCYm.mp3');
                            fail.play();
                            Swal.fire({ 
                                title: 'للأسف رسبت ❌', 
                                text: "استنفدت الـ 3 محاولات. جبت " + score + " من " + total, 
                                icon: 'error' 
                            }).then(() => location.reload());
                        } else {
                            const failm = new Audio('https://www.myinstants.com/media/sounds/mt-ytsh.mp3');
                            failm.play();
                            Swal.fire({ 
                                title: 'حاول تاني', 
                                text: "جبت " + score + " من " + total + ". باقي لك " + (maxAttempts - attempts) + " محاولات", 
                                icon: 'warning' 
                            }).then(() => {
                                form.reset();
                                document.querySelectorAll('.quiz-question label').forEach(label => {
                                    label.className = "flex items-center justify-between p-4 bg-white dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-700 rounded-2xl cursor-pointer transition-all hover:border-blue-500 group";
                                });
                                initQuizUI();
                            });
                        }
                    }
                } else {
                    btn.disabled = false;
                    Swal.fire('خطأ', data.message || 'حدث خطأ غير متوقع', 'error');
                }
            })
            .catch(error => {
                btn.disabled = false;
                console.error('Error:', error);
                Swal.fire('خطأ', 'فشل الاتصال بالخادم، تأكد من الإنترنت.', 'error');
            });
        }

        window.onload = () => {
            const videoCheck = document.getElementById('check-video');
            const pdfCheck = document.getElementById('check-pdf');
            if(videoCheck) videoCheck.checked = initialProgress.video;
            if(pdfCheck) pdfCheck.checked = initialProgress.pdf;
            initQuizUI();
        }
    </script>
    <?php endif; ?>
    <?php include 'footer.php'; ?>
</body>
</html>