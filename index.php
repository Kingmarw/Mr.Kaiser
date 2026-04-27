<?php
// معالج طلبات الشات بوت في أول ملف index.php
if (isset($_GET['chat_action'])) {
    header('Content-Type: application/json');
    
    $apiKey = "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";
    $jsonInput = file_get_contents('php://input');
    $input = json_decode($jsonInput, true);
    $userMsg = $input['message'] ?? 'أهلاً';

    $ch = curl_init("https://openrouter.ai/api/v1/chat/completions");
    
    $postData = [
        "model" => "inclusionai/ling-2.6-1t:free",
        "messages" => [
            ["role" => "system", "content" => "أنت مساعد ذكي لمنصة القيصر التعليمية. واسمك نينو بوت أجب بالعامية المصرية."],
            ["role" => "user", "content" => $userMsg]
        ]
    ];

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $apiKey,
        "Content-Type: application/json",
        "HTTP-Referer: https://kaiser.free.nf",
        "X-Title: Kaiser Academy"
    ]);
    

    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $res = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo json_encode(['error' => $error]);
    } else {
        echo $res;
    }
    exit;
}
?>

<?php

require_once 'config.php';

?>


<?php

try {
    $query = "SELECT * FROM lessons ORDER BY created_at DESC LIMIT 3";
    $stmt = $pdo->prepare($query); // التعديل هنا (pdo بدل conn)
    $stmt->execute();
    $lectures = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $lectures = [];
}

?>
<?php

$grades_data = [
    ['id' => 1, 'name' => 'الأول الإعدادي', 'image' => 'rev.jpeg'],
    ['id' => 2, 'name' => 'الثاني الإعدادي', 'image' => 'rev.jpeg'],
    ['id' => 3, 'name' => 'الثالث الإعدادي', 'image' => 'rev.jpeg'],
    ['id' => 4, 'name' => 'الأول الثانوي', 'image' => 'rev.jpeg'],
    ['id' => 5, 'name' => 'الثاني الثانوي', 'image' => 'rev.jpeg'],
    ['id' => 6, 'name' => 'الثالث الثانوي', 'image' => 'rev.jpeg'],
];

$grades = [];

foreach ($grades_data as $grade) {

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM lectures WHERE target_grade = ? AND is_active = 1");
    $stmt->execute([$grade['id']]);
    $count = $stmt->fetchColumn();

    $grade['count'] = $count . " محاضرة";
    $grades[] = $grade;
}
?>
<?php
if (isset($_POST['send_test'])) {
    $app_id = "124451b2-64b8-46aa-9a21-209f69ac79be"; // الـ ID بتاعك
    $rest_key = "os_v2_app_cjcfdmtexbdkvgrbecpwtldzx2j74mu7zm2usj5dgskbzkj5rlocynp3oqaw6ruv5ym7zsa3u7sckts55jbbwknxi7c3ldj7ka2t5hi"; // املأ الفراغ هنا!

    $content = array("en" => 'مبروك! الإشعارات شغالة في منصتك بنجاح 🚀');
    $headings = array("en" => 'تجربة إشعار');

    $fields = array(
        'app_id' => $app_id,
        'included_segments' => array('All'),
        'contents' => $content,
        'headings' => $headings
    );

    $fields = json_encode($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ' . $rest_key
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);
    
    $msg_push = "تم طلب إرسال الإشعار!";
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" id="main-html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="favicon.ico"> 
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png?v=5">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png?v=5">
    <link rel="manifest" href="site.webmanifest">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/kingmxse/platform@master/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"/>
    <title>منصة القيصر أحمد إبراهيم | العربي في جيبك </title>
    <meta name="description" content="منصة القيصر احمد إبراهيم - الشرح الأقوى والأسلوب الأسهل لمادة اللغة العربية لطلاب الصف الأول والثاني والثالث الإعدادي والثانوي. مراجعات نهائية وتدريبات شاملة للوصول للدرجة النهائية.">
    <meta name="keywords" content="القيصر , لغة عربية إعدادي, الصف الثالث الإعدادي, الصف الثاني الإعدادي, الصف الأول الإعدادي,الثانوي, مراجعة لغة عربية, نحو إعدادي, نصوص, نحو ثانوي, امتحانات عربي إعدادي, العربي في جيبك">
    <meta name="author" content="القيصر احمد إبراهيم">

    <meta property="og:title" content="منصة القيصر احمد إبراهيم - العربي في جيبك">
    <meta property="og:description" content="أقوى شرح ومراجعات للغة العربية للمرحلة الإعدادية. اشترك الآن وابدأ رحلة التفوق.">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ar_EG">

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "EducationalOrganization",
      "name": "منصة القيصر أحمد إبراهيم",
      "alternateName": "Al-Kaiser Ahmed Ibrahim",
      "url": "https://kaiser.free.nf",
      "logo": "https://kaiser.free.nf/m.jpeg",
      "sameAs": [
        "https://web.facebook.com/Caesar.AhmedIbrahim",
        "https://youtube.com/@kaisarAhmedibrahim"
      ],
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Giza",
        "addressCountry": "EG"
      }
    }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#1e3a8a',
                        secondary: '#fbbf24',
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; transition: all 0.3s ease; }
        .hero-pattern {
            background-color: #1e3a8a;
            background-image: url("https://www.transparenttextures.com/patterns/cubes.png");
        }
        .dark .hero-pattern {
            background-color: #0f172a;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script>
    window.OneSignalDeferred = window.OneSignalDeferred || [];
    OneSignalDeferred.push(async function(OneSignal) {
        await OneSignal.init({
            appId: "124451b2-64b8-46aa-9a21-209f69ac79be",
            serviceWorkerPath: 'OneSignalSDKWorker.js',
            serviceWorkerParam: { scope: './' },
            notifyButton: {
                enable: true,
            },
        });
    });
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100">
    <?php require_once 'nav.php'; ?>
    <div id="reading-bar" style="position: fixed; top: 0; left: 0; width: 0%; height: 4px; background: linear-gradient(to right, #247afb, #1e3a8a); z-index: 10000;"></div>
    <header class="hero-pattern relative overflow-hidden text-white pt-16 pb-32 px-4">
        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-center relative z-10">
            <div class="lg:w-3/5 text-center lg:text-right">
                <span class="inline-block bg-white/20 px-4 py-1 rounded-full text-sm mb-6 backdrop-blur-sm">أهلاً بكم في أقوى منصة تعليمية لمادة اللغه العربيه</span>
                <h1 class="text-5xl md:text-5xl font-extrabold leading-relaxed text-white dark:text-blue-400">
                   مع <span class="text-yellow-400 md:text-yellow-400">القيصر</span> العربي في أمان .. لسنين قدام
                </h1>
                <br>
                <p class="text-xl text-blue-100 mb-10 max-w-2xl">نظام شرح متكامل، تدريبات تفاعلية، ومتابعة دورية لحظة بلحظة للوصول بك إلى الدرجة النهائية.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <a href="register.php" class="bg-secondary text-primary px-10 py-4 rounded-2xl font-extrabold text-lg shadow-xl hover:bg-yellow-400 transition text-center">ابدأ رحلتك الآن</a>
                        <a href="javascript:void(0)" onclick="showGradeSelector()" class="glass-card px-10 py-4 rounded-2xl font-bold text-lg hover:bg-white/20 transition text-center">شوف فيديو مجاني</a>
                    <?php else: ?>
                        <a href="lectures.php" class="bg-secondary text-primary px-10 py-4 rounded-2xl font-extrabold text-lg shadow-xl hover:bg-yellow-400 transition text-center">استكمل محاضراتك</a>
                        <a href="javascript:void(0)" onclick="showGradeSelector()" class="glass-card px-10 py-4 rounded-2xl font-bold text-lg hover:bg-white/20 transition text-center">شوف فيديو مجاني</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="lg:w-2/5 mt-16 lg:mt-0 flex justify-center items-center w-full">
                <div class="relative group w-fit">
                    
                    <div class="absolute -inset-4 bg-secondary/40 rounded-[2.5rem] blur-2xl group-hover:bg-secondary/60 transition duration-500 opacity-70"></div>
                    
                    <div class="img-container relative z-10 rounded-[2rem] border-4 border-white/20 shadow-[0_0_40px_rgba(0,0,0,0.5)] overflow-hidden bg-white/5 backdrop-blur-sm">
                        <img src="https://cdn.jsdelivr.net/gh/kingmxse/platform@master/m.webp" fetchpriority="high" loading="lazy" class="w-[380px] md:w-[380px] h-auto object-cover block" alt="Kaiser">
                    </div>
                </div>
            </div>
        </div>
    </header>
    <section class="relative z-20 -mt-16 px-4">
        <div class="max-w-6xl mx-auto grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-8">
            <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-xl text-center border-b-4 border-primary">
                <p class="text-4xl font-black text-primary dark:text-blue-400 mb-2">+15k</p>
                <p class="text-gray-500 font-bold">طالب</p>
            </div>
            <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-xl text-center border-b-4 border-secondary">
                <p class="text-4xl font-black text-primary dark:text-blue-400 mb-2">+200</p>
                <p class="text-gray-500 font-bold">محاضرة</p>
            </div>
            <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-xl text-center border-b-4 border-primary">
                <p class="text-4xl font-black text-primary dark:text-blue-400 mb-2">+100</p>
                <p class="text-gray-500 font-bold">اختبار</p>
            </div>
            <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-xl text-center border-b-4 border-secondary">
                <p class="text-4xl font-black text-primary dark:text-blue-400 mb-2">+15</p>
                <p class="text-gray-500 font-bold">سنة خبرة</p>
            </div>
        </div>
    </section>
    <main class="pt-20"> </main>
    <section class="feature-card py-16 bg-gray-50 dark:bg-slate-900 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-center w-full mb-10">
                <h2 class="text-4xl md:text-5xl text-center font-black dark:text-white relative pb-4">
                    ليه تشترك مع 
                    <span class="text-blue-600 my-5 dark:text-blue-400">القيصر؟</span>
                    <svg class="absolute bottom-0 left-0 w-full h-3 text-yellow-400" viewBox="0 0 100 20" preserveAspectRatio="none">
                        <path d="M0 10 Q 25 20 50 10 T 100 10" stroke="currentColor" stroke-width="4" fill="transparent"/>
                    </svg>
                </h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="group bg-white dark:bg-slate-800 p-4 rounded-[3rem] shadow-2xl hover:shadow-blue-500/20 transition-all duration-500 border border-gray-100 dark:border-slate-700">
                    <div class="relative overflow-hidden rounded-[2.5rem] aspect-square mb-6">
                        <img src="uploads\des.jpeg" alt="شرح مبسط" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-blue-900/90 via-transparent to-transparent flex items-end p-8">
                            <p class="text-white font-bold text-2xl">أقوى نظام شرح وتأسيس 🚀</p>
                        </div>
                    </div>
                </div>

                <div class=" group bg-white dark:bg-slate-800 p-4 rounded-[3rem] shadow-2xl border border-gray-100 dark:border-slate-700">
                    <div class="relative overflow-hidden rounded-[2.5rem] aspect-square mb-6">
                        <img src="uploads\wly.jpeg" alt="امتحانات" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-green-900/90 via-transparent to-transparent flex items-end p-8">
                            <p class="text-white font-bold text-2xl">اختبارات إلكترونية ذكية ✍️</p>
                        </div>
                    </div>
                </div>
                <div class=" group bg-white dark:bg-slate-800 p-4 rounded-[3rem] shadow-2xl border border-gray-100 dark:border-slate-700">
                    <div class="relative overflow-hidden rounded-[2.5rem] aspect-square mb-6">
                        <img src="uploads\ms.jpeg" alt="امتحانات" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-red-900/90 via-transparent to-transparent flex items-end p-8">
                            <p class="text-white font-bold text-2xl">مراجعات شامله 🌏</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <main class="pt-20"> </main>
    <main class="max-w-7xl mx-auto py-24 px-4">
        <div class="flex flex-col md:flex-row justify-between items-center mb-16">
            <div>
                <h3 class="text-3xl font-black mb-2 uppercase tracking-tighter text-slate-800 dark:text-white">ليه تنضم لعيلتنا؟</h3>
                <div class="h-1.5 w-24 bg-secondary rounded-full"></div>
            </div>
        </div>

        <div class="feature-card grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 items-stretch">
            
            <a class="relative group block w-full transition-transform duration-500 hover:-translate-y-2">
                <div class="absolute -inset-2 bg-gradient-to-r from-orange-600 to-red-600 rounded-[2.5rem] blur-xl opacity-40 group-hover:opacity-80 transition duration-500"></div>
                <div class="relative h-full z-10 bg-white/10 dark:bg-slate-900/90 backdrop-blur-2xl border border-white/20 p-8 rounded-[2.5rem] shadow-2xl text-center overflow-hidden flex flex-col">
                    
                    <div class="absolute top-4 left-4 bg-red-600 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest animate-pulse">جديد</div>
                    <div class="relative mb-6 inline-flex items-center justify-center w-20 h-20 mx-auto bg-gradient-to-br from-orange-500 to-red-600 rounded-3xl shadow-xl -rotate-2 group-hover:rotate-0 transition-transform duration-500">
                        <i class="fa-solid fa-bolt-lightning text-3xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-3 text-slate-800 dark:text-white leading-tight">المراجعات <br> <span class="text-orange-600">النهائية</span></h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-8 flex-grow">خلاصة المنهج في محاضرات مكثفة، نواتج التعلم، وأهم التوقعات.</p>
                    <div class="flex justify-center gap-2 mb-4">
                        <span class="bg-gray-100 dark:bg-slate-800 px-3 py-1 rounded-lg text-[10px] font-bold text-gray-500">خرائط ذهنية</span>
                        <span class="bg-gray-100 dark:bg-slate-800 px-3 py-1 rounded-lg text-[10px] font-bold text-gray-500">توقعات</span>
                    </div>
                </div>
            </a>

            <a class="relative group block w-full transition-transform duration-500 hover:-translate-y-2">
                <div class="absolute -inset-2 bg-gradient-to-r from-blue-600 to-cyan-600 rounded-[2.5rem] blur-xl opacity-40 group-hover:opacity-80 transition duration-500"></div>
                <div class="relative h-full z-10 bg-white/10 dark:bg-slate-900/90 backdrop-blur-2xl border border-white/20 p-8 rounded-[2.5rem] shadow-2xl text-center overflow-hidden flex flex-col">
                    <div class="relative mb-6 inline-flex items-center justify-center w-20 h-20 mx-auto bg-gradient-to-br from-blue-500 to-cyan-600 rounded-3xl shadow-xl rotate-2 group-hover:rotate-0 transition-transform duration-500">
                        <i class="fa-solid fa-file-signature text-3xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-3 text-slate-800 dark:text-white leading-tight">بنك <br> <span class="text-blue-600">الأسئلة</span></h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-8 flex-grow">آلاف الأسئلة المحلولة لتدريبك على نظام الامتحان الجديد.</p>
                    <div class="flex justify-center gap-2 mb-4">
                        <span class="bg-gray-100 dark:bg-slate-800 px-3 py-1 rounded-lg text-[10px] font-bold text-gray-500">تصحيح تلقائي</span>
                        <span class="bg-gray-100 dark:bg-slate-800 px-3 py-1 rounded-lg text-[10px] font-bold text-gray-500">فيديو للحل</span>
                    </div>
                </div>
            </a>

            <a class="relative group block w-full transition-transform duration-500 hover:-translate-y-2">
                <div class="absolute -inset-2 bg-gradient-to-r from-green-600 to-emerald-600 rounded-[2.5rem] blur-xl opacity-40 group-hover:opacity-80 transition duration-500"></div>
                <div class="relative h-full z-10 bg-white/10 dark:bg-slate-900/90 backdrop-blur-2xl border border-white/20 p-8 rounded-[2.5rem] shadow-2xl text-center overflow-hidden flex flex-col">
                    <div class="relative mb-6 inline-flex items-center justify-center w-20 h-20 mx-auto bg-gradient-to-br from-green-500 to-emerald-600 rounded-3xl shadow-xl -rotate-2 group-hover:rotate-0 transition-transform duration-500">
                        <i class="fa-solid fa-headset text-3xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-black mb-3 text-slate-800 dark:text-white leading-tight">شرح دروس <br> <span class="text-green-600">بشكل مبسط</span></h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm font-medium mb-8 flex-grow">كل الدروس مشروحه بشكل مبسط</p>
                    <div class="flex justify-center gap-2 mb-4">
                        <span class="bg-gray-100 dark:bg-slate-800 px-3 py-1 rounded-lg text-[10px] font-bold text-gray-500">تبسيط الدروس</span>
                        <span class="bg-gray-100 dark:bg-slate-800 px-3 py-1 rounded-lg text-[10px] font-bold text-gray-500">التفوق وليس النجاح</span>
                    </div>
                </div>
            </a>

        </div>
    </main>

    <main class="max-w-7xl mx-auto py-24 px-4">
        <div class="flex flex-col md:flex-row justify-between items-center mb-12">
            <div>
                <h3 class="text-3xl font-black mb-2 uppercase tracking-tighter text-slate-800 dark:text-white">العروض</h3>
                <div class="h-1.5 w-24 bg-secondary rounded-full"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="feature-card 
    bg-white text-slate-900 
    dark:bg-gradient-to-br dark:from-slate-800 dark:to-slate-900 dark:text-white 
    rounded-[2rem] p-8 shadow-2xl relative overflow-hidden">

                <!-- Glow -->
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-yellow-400 opacity-20 blur-3xl"></div>

                <h2 class="text-2xl md:text-3xl font-black mb-4">
                     🔥 عرض لفتره محدوده🔥 
                </h2>

                <p class="mb-4 text-lg font-bold">
                    أخيراً وبعد طول انتظار المنصة الرسمية لكل الصفوف
                </p>

                <div class="bg-white/10 p-4 rounded-xl mb-6">
                    <p class="text-xl font-black text-yellow-400 mb-2">
                        🔥 عرض المراجعة النهائية 🔥
                    </p>
                    <p class="text-lg">
                        اشترك الآن في مادة اللغة العربية والدراسات بسعر 
                        <p class="text-3xl font-black text-yellow-300 mt-2">200 جنيه فقط 💰</p>
                    </p>
                </div>

                <!-- Features -->
                <div class="grid grid-cols-2 gap-3 mb-6 text-sm font-bold">
                    <div class="bg-white/10 p-3 rounded-xl">🎥 فيديوهات مراجعة مركزة</div>
                    <div class="bg-white/10 p-3 rounded-xl">📊 توقعات الامتحان</div>
                    <div class="bg-white/10 p-3 rounded-xl">📝 امتحانات إلكترونية</div>
                    <div class="bg-white/10 p-3 rounded-xl">💬 جروب متابعة</div>
                    <div class="bg-white/10 p-3 rounded-xl">📚 ملفات PDF لكل المواد</div>
                </div>

                <p class="mb-6 font-bold">
                    📌 كل اللي محتاجه عشان تقفل المادة في مكان واحد!
                </p>

                <!-- Button -->
                <a href="login.php" class="block text-center bg-gray-200 w-100 text-slate-900 dark:bg-white/10 dark:text-white backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10">
                    سجّل دلوقتي 🚀
                </a>

                <!-- How -->
                <div class="mt-6 text-sm text-blue-100 ">
                    <p class="font-bold mb-2 text-slate-900  dark:to-slate-900 dark:text-white ">ازاي تشحن؟ 🤔</p>
                    <p class="text-slate-900  dark:to-slate-900 dark:text-white ">بعد التسجيل ➜ ادخل على "شحن رصيد" ➜ اطلب كود ➜ حوّل وابعت الصورة 💙</p>
                </div>

            </div>
        </div>
    </main>
    <main class="feature-card max-w-6xl mx-auto py-24 px-4 overflow-hidden">
        <div class="flex flex-col md:flex-row justify-between items-center mb-12">
            <div>
                <h3 class="text-3xl font-black mb-2 uppercase tracking-tighter text-slate-800 dark:text-white">أحدث الحصص والمراجعات</h3>
                <div class="h-1.5 w-24 bg-blue-600 rounded-full"></div>
            </div>
            <div class="flex gap-2 mt-4 md:mt-0">
                <button class="swiper-prev bg-white dark:bg-slate-800 p-3 rounded-full shadow-sm hover:bg-blue-600 hover:text-white transition-all text-slate-800 dark:text-white border border-gray-100 dark:border-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <button class="swiper-next bg-white dark:bg-slate-800 p-3 rounded-full shadow-sm hover:bg-blue-600 hover:text-white transition-all text-slate-800 dark:text-white border border-gray-100 dark:border-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
            </div>
        </div>

        <div class="swiper gradesSwiper px-2 py-4">
            <div class="swiper-wrapper">
                <?php foreach($grades as $grade): ?>
                    <div class="swiper-slide">
                        <div class="group relative bg-slate-900 rounded-[2.5rem] overflow-hidden shadow-xl transition-all duration-500 aspect-[4/5] m-1">
                            <img src="uploads/<?php echo $grade['image']; ?>" 
                                loading="lazy"
                                alt="explain"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                                onerror="this.src='https://placehold.co/600x800?text=الصف+<?php echo urlencode($grade['name']); ?>'">

                            <div class="absolute inset-0 bg-gradient-to-t from-black/95 via-black/40 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-300"></div>

                            <div class="absolute inset-0 p-8 flex flex-col justify-end">
                                <h4 class="text-3xl font-black text-white mb-2 leading-tight transform group-hover:-translate-y-2 transition-transform duration-500">
                                    <?php echo $grade['name']; ?>
                                </h4>

                                <div class="group-hover:max-h-48 transition-all duration-700 group-hover:opacity-100 overflow-hidden">
                                    <div class="flex items-center gap-2 text-gray-300 text-xs mb-6 font-bold">
                                        <span class="bg-white/10 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/10">📖 <?php echo $grade['count']; ?></span>
                                        <span class="bg-white/10 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/10 text-yellow-400">✨ مراجعات</span>
                                    </div>
                                    
                                    <a href="lectures.php?grade=<?php echo $grade['id']; ?>" 
                                    class="block w-full text-center text-white bg-white/10 backdrop-blur-md px-3 py-1.5 rounded-full border border-white/10">
                                        دخول المنهج <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
    <main class="pt-20"> </main>
    <footer class="bg-slate-900 text-slate-400 py-12 px-4 border-t border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="text-center md:text-right">
                <h4 class="text-white font-black text-2xl mb-2">القيصر احمد إبراهيم</h4>
                <p class="text-sm">جميع الحقوق محفوظة © 2026</p>
                <!-- <div class="p-6 bg-white dark:bg-slate-800 rounded-3xl shadow-sm mt-5">
                    <form method="POST">
                        <button type="submit" name="send_test" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-bold">
                            إرسال إشعار تجريبي 🔔
                        </button>
                    </form>
                    <?php if(isset($msg_push)) echo "<p class='mt-2 text-green-500'>$msg_push</p>"; ?>
                </div> -->
            </div>
            <div class="flex gap-6 text-lg font-bold">
                <a href="https://web.facebook.com/Caesar.AhmedIbrahim" class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center hover:bg-primary hover:text-white transition"><i class="fa-brands fa-facebook text-xl"></i></a>
                <a href="tel:+201099534259" class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center hover:bg-green-600 hover:text-white transition"><i class="fa-solid fa-phone"></i></a>
                <a href="https://wa.me/+201099534259" class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center hover:bg-green-400 hover:text-white transition"><i class="fa-brands fa-whatsapp text-2xl"></i></a>
                <a href="https://youtube.com/@kaisarAhmedibrahim" class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center hover:bg-red-600 hover:text-white transition"><i class="fa-brands fa-youtube text-xl"></i></a>
            </div>
        </div>
        <div class="dev text-center mt-10">
            <p class="text-xl">< / <a class="kaiser" href="https://kingmarw.vercel.app">Kingmarw</a>  Developed By > </p>
        </div>
		<?php include 'footer.php'; ?>
    </footer>
    <script src="https://cdn.jsdelivr.net/gh/kingmxse/platform@master/nav.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/kingmxse/platform@master/loader_js.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js"></script>
    <script>
        document.querySelector('#progressbar').animate(
        {
            backgroundColor: ['red', 'darkred'],
            transform: ['scaleX(0)', 'scaleX(1)'],
        },
        {
            duration: 2500,
            fill: 'forwards',
            easing: 'linear',
        }
        );
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script>
        gsap.registerPlugin(ScrollTrigger);

        gsap.to("#reading-bar", {
            width: "100%",
            ease: "none",
            scrollTrigger: {
                trigger: "body",      // هيبدأ يحسب من أول الجسم
                start: "top top",     // البداية من أول الصفحة فوق
                end: "bottom bottom", // النهاية مع آخر الصفحة تحت
                scrub: 0.3            // الحركة تكون ناعمة (0.3 ثانية تأخير)
            }
        });
    </script>
    <script>
       
        const tl = gsap.timeline({
            scrollTrigger: {
                trigger: ".caesar-text",
                start: "top 85%",
            }
        });

        tl.from(".caesar-text", { y: 50, opacity: 0, duration: 0.8 })
        .set(".wavy-line", { visibility: "visible" })
        .from(".wavy-line path", { strokeDasharray: 200, strokeDashoffset: 200, duration: 1, ease: "power1.inOut" });

        // أنيميشن الكروت (تظهر بـ "Roll" احترافي)
    // أنيميشن كروت المحاضرات والمميزات
    gsap.utils.toArray('.lecture-card, .feature-card').forEach((card) => {
        gsap.from(card, {
            scrollTrigger: {
                trigger: card,
                start: "top 90%",
                toggleActions: "play none none none"
            },
            y: 60,            // يتحرك من تحت لفوق
            opacity: 0,       // يبدأ شفاف
            duration: 1,      // مدة الحركة ثانية
            ease: "power3.out" // حركة ناعمة في النهاية
        });
    });
    
    </script>
    <script>
        function showGradeSelector() {
            Swal.fire({
                title: 'اختار الصف الدراسي',
                icon: 'question',
                input: 'select',
                inputOptions: {
                    '1': 'الصف الثاني الاعدادي',
                    '2': 'الصف الثالث الاعدادي',
                    '3': 'الصف الأول الثانوي'
                },
                inputPlaceholder: 'اختر صفك الدراسي',
                showCancelButton: true,
                confirmButtonText: 'عرض الفيديو',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#5c3d99',
                reverseButtons: true, // بيخلي "إلغاء" على الشمال و"تأكيد" على اليمين (أفضل للمستخدم)
                inputValidator: (value) => {
                    if (!value) {
                        return 'لازم تختار الصف يا بطل!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let videoId = '';
                    
                    // تحديد الـ ID بناءً على الصف
                    if (result.value === '1') videoId = 'zHu011nnwkA'; 
                    else if (result.value === '2') videoId = '3rEm5B5xAUE'; 
                    else if (result.value === '3') videoId = 'SvQTtyNYXO8'; 

                    Swal.fire({
                        html: `
                            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 15px;">
                                <iframe 
                                    style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" 
                                    src="https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1&rel=0" 
                                    title="YouTube video player" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                        `,
                        width: 800,
                        background: '#111',
                        showCloseButton: true,
                        showConfirmButton: false,
                    });
                }
            });
        }
    </script>
    <script>
        var swiper = new Swiper(".gradesSwiper", {
            slidesPerView: 1, // في الموبايل يعرض كارت واحد
            spaceBetween: 20, // المسافة بين الكروت
            loop: false,
            grabCursor: true,
            autoplay: {
                delay: 3000, // المدة بين كل حركة (3 ثواني)
                disableOnInteraction: false, // يكمل شغل حتى لو المستخدم لمس السلايدر
                pauseOnMouseEnter: true, // يوقف مؤقتاً لو الماوس وقف على الكارت (عشان الطالب يلحق يقرأ)
            },
            navigation: {
                nextEl: ".swiper-next",
                prevEl: ".swiper-prev",
            },
            breakpoints: {
                // شاشات الموبايل الكبيرة
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                // شاشات التابلت
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
            },
        });
    </script>
<script src="//instant.page/5.2.0" type="module" integrity="sha384-jnZyxPjiipNiQMUZPWR32dqmsL3pEeqPHu9s9I9Hyr87U59I6D0290S10X83P45h"></script>
</body>
</html>