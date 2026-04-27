<?php
// ================================
// Backend: Chat API Handler
// ================================
if (isset($_GET['chat_action'])) {
    header('Content-Type: application/json; charset=utf-8');

    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);

    if (!is_array($input)) {
        echo json_encode(['error' => 'Invalid JSON'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $userMsg = trim($input['message'] ?? '');

    if ($userMsg === '') {
        echo json_encode(['error' => 'No message'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $apiKey = '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';

	$botName = "نينو بوت";
    $platformName = "منصة القيصر";
    $teacherName = "الأستاذ أحمد";

    $platformUrl = "https://kaiser.free.nf";
    $loginUrl = "https://kaiser.free.nf/login.php";
    $registerUrl = "https://kaiser.free.nf/register.php";

    $supportWhatsApp = "https://wa.me/+201099534259";

    $systemPrompt = "أنت $botName، المساعد الذكي لـ $platformName ($teacherName). 
    ردودك يجب أن تكون تعليمية، مشجعة، وباللهجة المصرية المحببة للطلاب.
    استخدم الـ Markdown عند الحاجة فقط، وخلّي الردود مختصرة وواضحة.
   	$supportWhatsApp صاحب المنصه هو القيصر أستاذ أحمد رقمه هو 
    $platformUrl والمنصه الخاصه به هي 
    معلومات المنصه هي 
    $loginUrl صفحة تسجيل الدخول 
    $registerUrl صفحة إنشاء حساب جديد هي 
   	$platformUrl رابط الصفحه الرئيسيه هي 
    رقم التواصل مع الدعم عن طريق الواتساب هو $supportWhatsApp
    لا تذكر معلومات المطور إلا اذا وجدت في الرساله معلومات عن المطور او اي شيء يخص المطور ولكن التسجيل وأي شيء يتعلق بالمنصه فلا تذكر اي معلومات عن المطور
    ";
    $payload = [
        "model" => "openai/gpt-oss-120b:free",
        "messages" => [
            [
                "role" => "system",
                "content" => $systemPrompt
            ],
            [
                "role" => "user",
                "content" => $userMsg
            ]
        ]
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "https://openrouter.ai/api/v1/chat/completions",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer {$apiKey}",
            "Content-Type: application/json",
            "Accept: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
    ]);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        $err = curl_error($ch);
        curl_close($ch);
        echo json_encode(['error' => 'cURL error: ' . $err], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode < 200 || $httpCode >= 300) {
        echo json_encode([
            'error' => 'API request failed',
            'status' => $httpCode,
            'raw' => $result
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    echo $result;
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f172a">
    <title>نينو بوت | مساعد القيصر الذكي</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dompurify@3.1.6/dist/purify.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap');

        :root {
            --bg-1: #07111f;
            --bg-2: #0f172a;
            --bg-3: #111827;
            --panel: rgba(255, 255, 255, 0.06);
            --panel-strong: rgba(255, 255, 255, 0.09);
            --border: rgba(255, 255, 255, 0.12);
            --text-dim: rgba(226, 232, 240, 0.72);
        }

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            color: #f8fafc;
            background:
                radial-gradient(circle at top right, rgba(59,130,246,0.22), transparent 28%),
                radial-gradient(circle at bottom left, rgba(99,102,241,0.18), transparent 30%),
                linear-gradient(160deg, var(--bg-1), var(--bg-2) 45%, var(--bg-3));
            overflow: hidden;
        }

        @supports (height: 100dvh) {
            body { min-height: 100dvh; }
        }

        .glass {
            background: var(--panel);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid var(--border);
        }

        .glass-strong {
            background: var(--panel-strong);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.14);
        }

        .scrollbar-hide::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .scrollbar-hide::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.16);
            border-radius: 999px;
        }

        .message-appear {
            animation: popIn 0.22s ease-out both;
        }

        @keyframes popIn {
            from { opacity: 0; transform: translateY(10px) scale(0.99); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .typing-dot {
            animation: typingPulse 1.2s infinite ease-in-out both;
        }

        @keyframes typingPulse {
            0%, 80%, 100% { transform: scale(0); opacity: 0.25; }
            40% { transform: scale(1); opacity: 1; }
        }

        .prose-custom {
            font-size: 0.96rem;
            line-height: 1.8;
            word-break: break-word;
        }

        .prose-custom p { margin: 0 0 0.75rem 0; }
        .prose-custom ul, .prose-custom ol {
            margin: 0.5rem 1rem;
            padding-inline-start: 1rem;
            list-style-position: outside;
        }
        .prose-custom ul { list-style-type: disc; }
        .prose-custom ol { list-style-type: decimal; }
        .prose-custom strong { font-weight: 800; }
        .prose-custom a {
            color: #93c5fd;
            text-decoration: underline;
            font-weight: 700;
        }
        .prose-custom code {
            background: rgba(255,255,255,0.10);
            padding: 2px 6px;
            border-radius: 8px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        input[type="text"] {
            -webkit-tap-highlight-color: transparent;
            background: #000;
        }

        @media (max-width: 768px) {
            input[type="text"] {
                font-size: 16px !important;
            }
        }

        .soft-shadow {
            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.35),
                inset 0 1px 0 rgba(255,255,255,0.04);
        }
    </style>
</head>
<body class="flex flex-col">

    <div class="w-full max-w-6xl mx-auto px-3 md:px-4 py-3 md:py-4 flex-1 flex flex-col min-h-0">
        <div class="glass soft-shadow rounded-[28px] flex flex-col flex-1 min-h-0 overflow-hidden">

            <header class="px-4 md:px-5 py-4 border-b border-white/10 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-11 h-11 rounded-2xl bg-blue-500/15 border border-blue-400/20 flex items-center justify-center text-blue-300 shadow-lg shadow-blue-500/10">
                        <i class="fa-solid fa-robot text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="font-bold text-base md:text-lg leading-none">نينو بوت</h1>
                            <span class="text-[10px] px-2 py-1 rounded-full bg-emerald-500/15 text-emerald-300 border border-emerald-500/20">
                                متصل الآن
                            </span>
                        </div>
                        <p class="text-xs md:text-sm text-slate-300/80 mt-1 truncate">
                            مساعد القيصر الذكي — ردود سريعة ومباشرة للطلاب
                        </p>
                    </div>
                </div>

                <a href="index.php" class="shrink-0 inline-flex items-center gap-2 text-xs md:text-sm px-3 md:px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all">
                    <i class="fa-solid fa-house"></i>
                    <span>الرئيسية</span>
                </a>
            </header>

            <main class="flex-1 min-h-0 flex flex-col overflow-hidden">


                <div id="chat-body" class="flex-1 min-h-0 overflow-y-auto scrollbar-hide px-4 md:px-5 py-4 space-y-4">
                    <div class="flex items-end gap-2 message-appear">
                        <div class="w-8 h-8 rounded-2xl bg-white/8 border border-white/10 flex items-center justify-center text-blue-300 shrink-0">
                            <i class="fa-solid fa-robot text-[11px]"></i>
                        </div>
                        <div class="glass-strong p-4 rounded-3xl rounded-br-md max-w-[92%] md:max-w-[75%] text-sm md:text-[0.95rem] leading-relaxed text-slate-50">
                            يا أهلاً بيك يا بطل في منصة القيصر 👑 أنا نينو بوت، اسألني في أي حاجة واقفة معاك.
                        </div>
                    </div>
                </div>

                <div class="px-4 md:px-5 pb-3">
                    <div id="quick-replies" class="flex gap-2 overflow-x-auto scrollbar-hide pb-1 pt-1 flex-nowrap">
                        <button onclick="handleQuickReply('ازاي اذاكر صح؟')" class="shrink-0 px-4 py-2 rounded-full text-xs md:text-sm bg-white/5 border border-white/10 hover:bg-blue-500/20 hover:border-blue-300/20 transition-all">
                            💡 نصيحة للمذاكرة
                        </button>
                        <button onclick="handleQuickReply('من هو القيصر؟')" class="shrink-0 px-4 py-2 rounded-full text-xs md:text-sm bg-white/5 border border-white/10 hover:bg-blue-500/20 hover:border-blue-300/20 transition-all">
                            👑 من هو القيصر؟
                        </button>
                        <button onclick="handleQuickReply('رابط الدعم الفني')" class="shrink-0 px-4 py-2 rounded-full text-xs md:text-sm bg-white/5 border border-white/10 hover:bg-blue-500/20 hover:border-blue-300/20 transition-all">
                            🛠️ الدعم الفني
                        </button>
                        <button onclick="handleQuickReply('يعني ايه منصه؟')" class="shrink-0 px-4 py-2 rounded-full text-xs md:text-sm bg-white/5 border border-white/10 hover:bg-blue-500/20 hover:border-blue-300/20 transition-all">
                            🌐 يعني ايه منصه؟
                        </button>
                        <button onclick="handleQuickReply('العروض المتاحة')" class="shrink-0 px-4 py-2 rounded-full text-xs md:text-sm bg-white/5 border border-white/10 hover:bg-blue-500/20 hover:border-blue-300/20 transition-all">
                            🔥 العروض
                        </button>
                        <button onclick="handleQuickReply('ازاي اسجل في المنصه؟')" class="shrink-0 px-4 py-2 rounded-full text-xs md:text-sm bg-white/5 border border-white/10 hover:bg-blue-500/20 hover:border-blue-300/20 transition-all">
                            🔥 ازاي اسجل في المنصه؟
                        </button>
                    </div>
                </div>
            </main>

            <footer class="px-4 md:px-5 py-4 border-t border-white/10 bg-black/10">
                <div class="mx-auto w-full max-w-4xl">
                    <div class="flex gap-2 md:gap-3 items-center">
                        <div class="flex-1 relative">
                            <input
                                type="text"
                                id="chat-input"
                                placeholder="اكتب رسالتك هنا..."
                                class="w-full bg-dark text-dark border border-white/10 placeholder:text-slate-400/80 px-4 py-3 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-300/20 transition-all"
                                autocomplete="off"
                            >
                        </div>
                        <button id="send-btn" class="w-12 h-12 rounded-2xl bg-blue-600 hover:bg-blue-500 active:scale-95 disabled:opacity-50 disabled:active:scale-100 transition-all flex items-center justify-center shadow-lg shadow-blue-600/20">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                    <p class="text-[11px] md:text-xs text-slate-400 mt-2 px-1">
                        اضغط Enter للإرسال — والردود الجاهزة بتظهر فورًا
                    </p>
                </div>
            </footer>
        </div>
    </div>


    <script>
        const chatBody = document.getElementById('chat-body');
        const chatInput = document.getElementById('chat-input');
        const sendBtn = document.getElementById('send-btn');
        let isLoading = false;
        let loadingId = null;

        marked.setOptions({
            breaks: true,
            gfm: true
        });

        function escapeHtml(str) {
            return String(str)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#39;');
        }

        function normalizeText(text) {
            return String(text || '')
                .trim()
                .toLowerCase()
                .replace(/[\u064B-\u065F\u0670\u0640ـ؟?!.,:;،()[\]{}"'«»]/gu, '')
                .replace(/\s+/g, ' ');
        }

        const savedResponses = {
            [normalizeText('من هو القيصر؟')]: "الأستاذ **أحمد (القيصر)** هو معلم خبير في اللغة العربية، هدفه تبسيط المنهج ووصولك للدرجة النهائية بأسلوب ممتع وغير تقليدي. 👑",

            [normalizeText('ازاي اذاكر صح؟')]: "المذاكرة الصح يا بطل هي:\n1. **التركيز:** ابعد عن المشتتات.\n2. **الفهم:** اسمع الشرح بتركيز.\n3. **التطبيق:** حل الأسئلة فورًا.\n4. **المراجعة:** لا تراكم الدروس!",

            [normalizeText('رابط الدعم الفني')]: "لو واجهتك أي مشكلة تقنية، تقدر تتواصل معانا مباشرة من خلال الرابط ده: [اضغط هنا للدعم](https://wa.me/201099534259)",

            [normalizeText('يعني ايه منصه؟')]: "💬 **المنصة التعليمية** هي مكان أونلاين بتلاقي عليه شرح منظم للمنهج + فيديوهات + ملفات تطبعها + ملازم مراجعة + تدريبات، وكل حاجة بتكون مترتبة علشان تساعدك تذاكر بسهولة وتحقق أعلى درجة.",

            [normalizeText('مين أنت؟')]: "أنا نينو بوت 🧡",

            [normalizeText('ازاي اسجل في المنصه')]: "لإنشاء حساب جديد:\n1. اضغط على زر التسجيل أعلى الصفحة.\n2. أدخل بياناتك (الاسم، رقم الهاتف، كلمة المرور).\n3. اختر الصف الدراسي الخاص بك واضغط تسجيل.\n4. اعمل تحديث للصفحة وبعدها سجّل دخول.",

            [normalizeText('إزاي أشحن وأشترك في المنصة؟')]: "💬 تقدر تشترك بسهولة من خلال الخطوات دي:\n📱 **ڤودافون كاش:**\nتحوّل على الرقم: 01099534259\n💳 **إنستا باي:**\nتحوّل على الرقم: 01147652465\n📸 بعد التحويل، ابعت اسكرين للتحويل للدعم هنا علشان يتم تفعيل الاشتراك بسرعة ✅",

            [normalizeText('ازاي اشحن واشترك في المنصة')]: "💬 تقدر تشترك بسهولة من خلال الخطوات دي:\n📱 **ڤودافون كاش:**\nتحوّل على الرقم: 01099534259\n💳 **إنستا باي:**\nتحوّل على الرقم: 01147652465\n📸 بعد التحويل، ابعت اسكرين للتحويل للدعم هنا علشان يتم تفعيل الاشتراك بسرعة ✅",

            [normalizeText('العروض المتاحة')]: "✨ **اختار العرض المناسب ليك وابدأ صح قبل الامتحان**\n\n🔹 **عرض 50 جنيه**\n📚 مراجعة المنهج كامل في ساعة واحدة\n📄 PDF (7 ورقات) ملخص شامل\n📝 حل اختبار على المنهج\n\n🔥 **عرض 100 جنيه (الأقوى 💪)**\n📚 كل مميزات عرض 50 جنيه +\n➕ حصة إضافية لشرح أعمق\n📝 حل جميع الامتحانات المتوقعة\n👨‍🏫 جروب متابعة مع المستر لحد الامتحان\n🔁 والعرضين تقدر تشوف أي مراجعتك أكتر من مرة على المنصة\n\n**المتاح مادة اللغة العربية والدراسات**\nاهلا بيك اختار العرض وحول وبعد كدا هضيفك",

            [normalizeText('الأسعار')]: "✨ **اختار العرض المناسب ليك وابدأ صح قبل الامتحان**\n\n🔹 **عرض 50 جنيه**\n📚 مراجعة المنهج كامل في ساعة واحدة\n📄 PDF (7 ورقات) ملخص شامل\n📝 حل اختبار على المنهج\n\n🔥 **عرض 100 جنيه (الأقوى 💪)**\n📚 كل مميزات عرض 50 جنيه +\n➕ حصة إضافية لشرح أعمق\n📝 حل جميع الامتحانات المتوقعة\n👨‍🏫 جروب متابعة مع المستر لحد الامتحان\n🔁 والعرضين تقدر تشوف أي مراجعتك أكتر من مرة على المنصة\n\n**المتاح مادة اللغة العربية والدراسات**\nاهلا بيك اختار العرض وحول وبعد كدا هضيفك"
        };

        function appendMessage(text, sender) {
            const wrapper = document.createElement('div');
            wrapper.className = `flex items-end gap-2 message-appear w-full ${sender === 'user' ? 'flex-row-reverse' : ''}`;

            const icon = sender === 'user'
                ? '<div class="w-8 h-8 rounded-2xl bg-blue-600 flex items-center justify-center text-white shrink-0 mb-1 shadow-lg shadow-blue-600/20"><i class="fa-solid fa-user text-[11px]"></i></div>'
                : '<div class="w-8 h-8 rounded-2xl bg-white/8 border border-white/10 flex items-center justify-center text-blue-300 shrink-0 mb-1"><i class="fa-solid fa-robot text-[11px]"></i></div>';

            const bubbleClass = sender === 'user'
                ? 'bg-blue-600 text-white rounded-3xl rounded-bl-md'
                : 'glass-strong text-white rounded-3xl rounded-br-md';

            const content = sender === 'bot'
                ? `<div class="prose-custom">${DOMPurify.sanitize(marked.parse(text || ''))}</div>`
                : `<div class="whitespace-pre-wrap">${escapeHtml(text)}</div>`;

            wrapper.innerHTML = `${icon}<div class="${bubbleClass} px-4 py-3 max-w-[92%] md:max-w-[76%] text-sm md:text-[0.95rem] leading-relaxed">${content}</div>`;
            chatBody.appendChild(wrapper);
            chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: 'smooth' });
        }

        function toggleLoading(show) {
            if (show) {
                loadingId = 'load-' + Date.now();
                const loader = document.createElement('div');
                loader.id = loadingId;
                loader.className = 'flex items-end gap-2 message-appear';

                loader.innerHTML = `
                    <div class="w-8 h-8 rounded-2xl bg-white/8 border border-white/10 flex items-center justify-center text-blue-300 shrink-0 mb-1">
                        <i class="fa-solid fa-robot text-[11px]"></i>
                    </div>
                    <div class="glass-strong px-4 py-3 rounded-3xl rounded-br-md flex gap-1 items-center h-11">
                        <div class="w-2 h-2 bg-blue-400 rounded-full typing-dot"></div>
                        <div class="w-2 h-2 bg-blue-400 rounded-full typing-dot"></div>
                        <div class="w-2 h-2 bg-blue-400 rounded-full typing-dot"></div>
                    </div>
                `;
                chatBody.appendChild(loader);
                chatBody.scrollTop = chatBody.scrollHeight;
            } else if (loadingId) {
                const el = document.getElementById(loadingId);
                if (el) el.remove();
                loadingId = null;
            }
        }

        async function processMessage(text) {
            const msg = String(text || '').trim();
            if (!msg || isLoading) return;

            appendMessage(msg, 'user');
            chatInput.value = '';
            isLoading = true;
            sendBtn.disabled = true;

            const key = normalizeText(msg);

            if (savedResponses[key]) {
                toggleLoading(true);
                setTimeout(() => {
                    toggleLoading(false);
                    appendMessage(savedResponses[key], 'bot');
                    isLoading = false;
                    sendBtn.disabled = false;
                }, 450);
                return;
            }

            toggleLoading(true);

            try {
                const response = await fetch("?chat_action=1", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ message: msg })
                });

                const data = await response.json();
                toggleLoading(false);

                if (data && data.choices && data.choices[0] && data.choices[0].message) {
                    appendMessage(data.choices[0].message.content || '...', 'bot');
                } else {
                    appendMessage(data.error ? `عذراً: ${data.error}` : "عذراً، جرب تاني كمان شوية.", "bot");
                }
            } catch (e) {
                toggleLoading(false);
                appendMessage("تأكد من اتصالك بالإنترنت.", "bot");
            } finally {
                isLoading = false;
                sendBtn.disabled = false;
            }
        }

        function handleQuickReply(text) {
            processMessage(text);
        }

        sendBtn.addEventListener('click', () => processMessage(chatInput.value));
        chatInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') processMessage(chatInput.value);
        });
    </script>
</body>
</html>