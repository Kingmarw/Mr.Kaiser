<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

@keyframes pulse-whatsapp {
    0% { box-shadow: 0 0 0 0 rgba(34,197,94,0.7); }
    70% { box-shadow: 0 0 0 15px rgba(34,197,94,0); }
    100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
}
.whatsapp-float { animation: pulse-whatsapp 2s infinite; }


.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div id="chatbot-window" class="fixed bottom-24 left-5 z-50 w-80 md:w-96 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-slate-700 hidden flex-col overflow-hidden transition-all duration-300">
    <div class="bg-blue-600 p-4 text-white flex justify-between items-center shadow-md">
        <div class="font-bold flex items-center gap-2">
            <i class="fa-solid fa-robot text-xl"></i> نينو بوت
        </div>
        <button id="close-chat" class="hover:text-red-300 transition text-xl"><i class="fa-solid fa-xmark"></i></button>
    </div>
    
    <div id="chat-body" class="p-4 h-72 overflow-y-auto flex flex-col gap-3 bg-gray-50 dark:bg-slate-900 text-sm">
        <div class="bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-100 p-3 rounded-2xl rounded-tr-none self-start max-w-[85%] shadow-sm">
            أهلاً بك في منصة القيصر! 👑<br>أنا المساعد الذكي، كيف يمكنني مساعدتك اليوم؟
        </div>
    </div>
    
    <div class="p-2 flex gap-2 overflow-x-auto bg-gray-100 dark:bg-slate-800 border-t border-gray-200 dark:border-slate-700 no-scrollbar">
        <button onclick="sendQuickReply('كيف أقوم بإنشاء حساب؟', 'لإنشاء حساب جديد:<br>1. اضغط على زر التسجيل أعلى الصفحة.<br>2. أدخل بياناتك (الاسم، رقم الهاتف، كلمة المرور).<br>3. اختر الصف الدراسي الخاص بك واضغط تسجيل.')" class="whitespace-nowrap bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-slate-600 px-3 py-1.5 rounded-full text-xs font-bold hover:bg-blue-50 transition shadow-sm">كيف أسجل؟</button>
        
        <button onclick="sendQuickReply('كيف أشحن رصيدي؟', 'طرق الشحن المتاحة:<br>1.  عن طريق فودافون كاش أو انستا باي.<br>2. تواصل مع الدعم الفني لمزيد من التفاصيل.<br> 01099534259')" class="whitespace-nowrap bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-slate-600 px-3 py-1.5 rounded-full text-xs font-bold hover:bg-blue-50 transition shadow-sm">طريقة الشحن</button>
    </div>
    
    <div class="p-3 bg-white dark:bg-slate-800 flex gap-2 border-t border-gray-200 dark:border-slate-700">
        <input type="text" id="chat-input" placeholder="اسأل عن أي شيء..." class="flex-1 p-3 rounded-xl border border-gray-300 dark:border-slate-600 bg-gray-50 dark:bg-slate-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
        <button id="send-btn" class="bg-blue-600 text-white w-12 h-12 rounded-xl flex justify-center items-center hover:bg-blue-700 transition shadow-md">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </div>
</div>

<div class="fixed bottom-5 left-5 z-40 flex flex-col gap-3 items-center">

    <a href="https://wa.me/201099534259" target="_blank" class="whatsapp-float w-16 h-16 bg-green-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-green-600 transition transform hover:scale-110 group relative">
        <i class="fa-brands fa-whatsapp text-3xl"></i>
        <span class="absolute left-20 bg-black text-white text-xs px-2 py-1 rounded hidden group-hover:block whitespace-nowrap">تواصل معنا</span>
    </a>

</div>

<script>

    const OPENROUTER_API_KEY = "sk-or-v1-8a119e27f88cd6852a7da5ec90c23b7mSyCNvXJhHHbqVY7rDG6YqRybKPQ6ErfX"; 
    
    const chatbotWindow = document.getElementById('chatbot-window');
    const chatbotToggle = document.getElementById('chatbot-toggle');
    const closeChat = document.getElementById('close-chat');
    const chatBody = document.getElementById('chat-body');
    const chatInput = document.getElementById('chat-input');
    const sendBtn = document.getElementById('send-btn');

    // فتح وإغلاق الشات
    chatbotToggle.addEventListener('click', () => {
        chatbotWindow.classList.toggle('hidden');
        chatbotWindow.classList.toggle('flex');
    });
    closeChat.addEventListener('click', () => {
        chatbotWindow.classList.add('hidden');
        chatbotWindow.classList.remove('flex');
    });

    function appendMessage(text, sender) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `p-3 rounded-2xl text-sm max-w-[85%] shadow-sm ${
            sender === 'user' 
            ? 'bg-blue-600 text-white self-end rounded-tl-none' 
            : 'bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-100 self-start rounded-tr-none'
        }`;
        msgDiv.innerHTML = text;
        chatBody.appendChild(msgDiv);
        chatBody.scrollTop = chatBody.scrollHeight;
    }


    function sendQuickReply(question, answer) {
        appendMessage(question, 'user');
        setTimeout(() => appendMessage(answer, 'bot'), 500);
    }

    async function sendMessageToAI() {
        const text = chatInput.value.trim();
        if (!text) return;

        appendMessage(text, 'user');
        chatInput.value = '';

        const loadingId = 'loading-' + Date.now();
        appendLoading(loadingId);

        try {
            const response = await fetch("?chat_action=1", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ "message": text })
            });

            const data = await response.json();
            document.getElementById(loadingId).remove();

            if (data.choices && data.choices.length > 0) {
                appendMessage(data.choices[0].message.content, 'bot');
            } else {
                appendMessage('عذراً، جرب مرة أخرى يا بطل.', 'bot');
            }
        } catch (error) {
            document.getElementById(loadingId).remove();
            appendMessage('مشكلة في الاتصال.. اتأكد إنك فاتح النت كويس.', 'bot');
        }
    }
    function appendLoading(id) {
        const loadingDiv = document.createElement('div');
        loadingDiv.id = id;
        loadingDiv.className = 'bg-gray-200 dark:bg-slate-700 text-gray-500 dark:text-gray-300 p-3 rounded-2xl rounded-tr-none self-start max-w-[85%] shadow-sm text-xs italic';
        loadingDiv.innerHTML = '<i class="fa-solid fa-ellipsis fa-beat-low"></i> المساعد يكتب الآن...';
        chatBody.appendChild(loadingDiv);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    sendBtn.addEventListener('click', sendMessageToAI);
    chatInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') sendMessageToAI();
    });
</script>
