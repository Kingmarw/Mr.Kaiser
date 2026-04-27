<?php
$notif_count = 0;
if (isset($_SESSION['user_id'])) {
    
    $stmt_count = $pdo->prepare("SELECT COUNT(*) FROM recharge_requests WHERE user_id = ? AND status IN ('approved', 'rejected') AND is_read = 0");
    $stmt_count->execute([$_SESSION['user_id']]);
    $notif_count = $stmt_count->fetchColumn();
}
?>
<?php
$avatar = !empty($_SESSION['user_avatar'])
    ? htmlspecialchars($_SESSION['user_avatar'])
    : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['user_name']);
?>
<style>
#preloader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #000;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    transition: opacity 0.5s ease, visibility 0.5s ease;
}

.loader {
  --fill-color: #5c3d99;
  --shine-color: #5c3d9933;
  transform: scale(0.5);
  width: 100px;
  height: auto;
  position: relative;
  filter: drop-shadow(0 0 10px var(--shine-color));
}

.loader #pegtopone {
  position: absolute;
  animation: flowe-one 1s linear infinite;
}

.loader #pegtoptwo {
  position: absolute;
  opacity: 0;
  transform: scale(0) translateY(-200px) translateX(-100px);
  animation: flowe-two 1s linear infinite;
  animation-delay: 0.3s;
}

.loader #pegtopthree {
  position: absolute;
  opacity: 0;
  transform: scale(0) translateY(-200px) translateX(100px);
  animation: flowe-three 1s linear infinite;
  animation-delay: 0.6s;
}

.loader svg g path:first-child {
  fill: var(--fill-color);
}
#preloader.loaded {
    opacity: 0;
    visibility: hidden;
    pointer-events: none; /* ده اللي بيخلي الماوس يمر من خلاله للمحتوى */
}
@keyframes flowe-one {
  0% {
    transform: scale(0.5) translateY(-200px);
    opacity: 0;
  }
  25% {
    transform: scale(0.75) translateY(-100px);
    opacity: 1;
  }
  50% {
    transform: scale(1) translateY(0px);
    opacity: 1;
  }
  75% {
    transform: scale(0.5) translateY(50px);
    opacity: 1;
  }
  100% {
    transform: scale(0) translateY(100px);
    opacity: 0;
  }
}

@keyframes flowe-two {
  0% {
    transform: scale(0.5) rotateZ(-10deg) translateY(-200px) translateX(-100px);
    opacity: 0;
  }
  25% {
    transform: scale(1) rotateZ(-5deg) translateY(-100px) translateX(-50px);
    opacity: 1;
  }
  50% {
    transform: scale(1) rotateZ(0deg) translateY(0px) translateX(-25px);
    opacity: 1;
  }
  75% {
    transform: scale(0.5) rotateZ(5deg) translateY(50px) translateX(0px);
    opacity: 1;
  }
  100% {
    transform: scale(0) rotateZ(10deg) translateY(100px) translateX(25px);
    opacity: 0;
  }
}

@keyframes flowe-three {
  0% {
    transform: scale(0.5) rotateZ(10deg) translateY(-200px) translateX(100px);
    opacity: 0;
  }
  25% {
    transform: scale(1) rotateZ(5deg) translateY(-100px) translateX(50px);
    opacity: 1;
  }
  50% {
    transform: scale(1) rotateZ(0deg) translateY(0px) translateX(25px);
    opacity: 1;
  }
  75% {
    transform: scale(0.5) rotateZ(-5deg) translateY(50px) translateX(0px);
    opacity: 1;
  }
  100% {
    transform: scale(0) rotateZ(-10deg) translateY(100px) translateX(-25px);
    opacity: 0;
  }
}


@keyframes floatingHero {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

</style>
<div id="preloader">
        <div class="loader">
        <svg
            id="pegtopone"
            xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink"
            viewBox="0 0 100 100"
        >
            <defs>
            <filter id="shine">
                <feGaussianBlur stdDeviation="3"></feGaussianBlur>
            </filter>
            <mask id="mask">
                <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="white"
                ></path>
            </mask>
            <radialGradient
                id="gradient-1"
                cx="50"
                cy="66"
                fx="50"
                fy="66"
                r="30"
                gradientTransform="translate(0 35) scale(1 0.5)"
                gradientUnits="userSpaceOnUse"
            >
                <stop offset="0%" stop-color="black" stop-opacity="0.3"></stop>
                <stop offset="50%" stop-color="black" stop-opacity="0.1"></stop>
                <stop offset="100%" stop-color="black" stop-opacity="0"></stop>
            </radialGradient>
            <radialGradient
                id="gradient-2"
                cx="55"
                cy="20"
                fx="55"
                fy="20"
                r="30"
                gradientUnits="userSpaceOnUse"
            >
                <stop offset="0%" stop-color="white" stop-opacity="0.3"></stop>
                <stop offset="50%" stop-color="white" stop-opacity="0.1"></stop>
                <stop offset="100%" stop-color="white" stop-opacity="0"></stop>
            </radialGradient>
            <radialGradient
                id="gradient-3"
                cx="85"
                cy="50"
                fx="85"
                fy="50"
                xlink:href="#gradient-2"
            ></radialGradient>
            <radialGradient
                id="gradient-4"
                cx="50"
                cy="58"
                fx="50"
                fy="58"
                r="60"
                gradientTransform="translate(0 47) scale(1 0.2)"
                xlink:href="#gradient-3"
            ></radialGradient>
            <linearGradient
                id="gradient-5"
                x1="50"
                y1="90"
                x2="50"
                y2="10"
                gradientUnits="userSpaceOnUse"
            >
                <stop offset="0%" stop-color="black" stop-opacity="0.2"></stop>
                <stop offset="40%" stop-color="black" stop-opacity="0"></stop>
            </linearGradient>
            </defs>
            <g>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="currentColor"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="url(#gradient-1)"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="none"
                stroke="white"
                opacity="0.3"
                stroke-width="3"
                filter="url(#shine)"
                mask="url(#mask)"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="url(#gradient-2)"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="url(#gradient-3)"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="url(#gradient-4)"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="url(#gradient-5)"
            ></path>
            </g>
        </svg>
        <svg
            id="pegtoptwo"
            xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink"
            viewBox="0 0 100 100"
        >
            <defs>
            <filter id="shine">
                <feGaussianBlur stdDeviation="3"></feGaussianBlur>
            </filter>
            <mask id="mask">
                <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="white"
                ></path>
            </mask>
            <radialGradient
                id="gradient-1"
                cx="50"
                cy="66"
                fx="50"
                fy="66"
                r="30"
                gradientTransform="translate(0 35) scale(1 0.5)"
                gradientUnits="userSpaceOnUse"
            >
                <stop offset="0%" stop-color="black" stop-opacity="0.3"></stop>
                <stop offset="50%" stop-color="black" stop-opacity="0.1"></stop>
                <stop offset="100%" stop-color="black" stop-opacity="0"></stop>
            </radialGradient>
            <radialGradient
                id="gradient-2"
                cx="55"
                cy="20"
                fx="55"
                fy="20"
                r="30"
                gradientUnits="userSpaceOnUse"
            >
                <stop offset="0%" stop-color="white" stop-opacity="0.3"></stop>
                <stop offset="50%" stop-color="white" stop-opacity="0.1"></stop>
                <stop offset="100%" stop-color="white" stop-opacity="0"></stop>
            </radialGradient>
            <radialGradient
                id="gradient-3"
                cx="85"
                cy="50"
                fx="85"
                fy="50"
                xlink:href="#gradient-2"
            ></radialGradient>
            <radialGradient
                id="gradient-4"
                cx="50"
                cy="58"
                fx="50"
                fy="58"
                r="60"
                gradientTransform="translate(0 47) scale(1 0.2)"
                xlink:href="#gradient-3"
            ></radialGradient>
            <linearGradient
                id="gradient-5"
                x1="50"
                y1="90"
                x2="50"
                y2="10"
                gradientUnits="userSpaceOnUse"
            >
                <stop offset="0%" stop-color="black" stop-opacity="0.2"></stop>
                <stop offset="40%" stop-color="black" stop-opacity="0"></stop>
            </linearGradient>
            </defs>
            <g>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="currentColor"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="url(#gradient-1)"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="none"
                stroke="white"
                opacity="0.3"
                stroke-width="3"
                filter="url(#shine)"
                mask="url(#mask)"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="url(#gradient-2)"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="url(#gradient-3)"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="url(#gradient-4)"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="url(#gradient-5)"
            ></path>
            </g>
        </svg>
        <svg
            id="pegtopthree"
            xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink"
            viewBox="0 0 100 100"
        >
            <defs>
            <filter id="shine">
                <feGaussianBlur stdDeviation="3"></feGaussianBlur>
            </filter>
            <mask id="mask">
                <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="white"
                ></path>
            </mask>
            <radialGradient
                id="gradient-1"
                cx="50"
                cy="66"
                fx="50"
                fy="66"
                r="30"
                gradientTransform="translate(0 35) scale(1 0.5)"
                gradientUnits="userSpaceOnUse"
            >
                <stop offset="0%" stop-color="black" stop-opacity="0.3"></stop>
                <stop offset="50%" stop-color="black" stop-opacity="0.1"></stop>
                <stop offset="100%" stop-color="black" stop-opacity="0"></stop>
            </radialGradient>
            <radialGradient
                id="gradient-2"
                cx="55"
                cy="20"
                fx="55"
                fy="20"
                r="30"
                gradientUnits="userSpaceOnUse"
            >
                <stop offset="0%" stop-color="white" stop-opacity="0.3"></stop>
                <stop offset="50%" stop-color="white" stop-opacity="0.1"></stop>
                <stop offset="100%" stop-color="white" stop-opacity="0"></stop>
            </radialGradient>
            <radialGradient
                id="gradient-3"
                cx="85"
                cy="50"
                fx="85"
                fy="50"
                xlink:href="#gradient-2"
            ></radialGradient>
            <radialGradient
                id="gradient-4"
                cx="50"
                cy="58"
                fx="50"
                fy="58"
                r="60"
                gradientTransform="translate(0 47) scale(1 0.2)"
                xlink:href="#gradient-3"
            ></radialGradient>
            <linearGradient
                id="gradient-5"
                x1="50"
                y1="90"
                x2="50"
                y2="10"
                gradientUnits="userSpaceOnUse"
            >
                <stop offset="0%" stop-color="black" stop-opacity="0.2"></stop>
                <stop offset="40%" stop-color="black" stop-opacity="0"></stop>
            </linearGradient>
            </defs>
            <g>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="currentColor"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="url(#gradient-1)"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="none"
                stroke="white"
                opacity="0.3"
                stroke-width="3"
                filter="url(#shine)"
                mask="url(#mask)"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="url(#gradient-2)"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="url(#gradient-3)"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="url(#gradient-4)"
            ></path>
            <path
                d="M63,37c-6.7-4-4-27-13-27s-6.3,23-13,27-27,4-27,13,20.3,9,27,13,4,27,13,27,6.3-23,13-27,27-4,27-13-20.3-9-27-13Z"
                fill="url(#gradient-5)"
            ></path>
            </g>
        </svg>
    </div>
    <h5 class="text-white mt-12 fw-bold" dir="rtl">جاري تجهيز المنصه...</h5>
</div>
<nav class="fixed top-0 left-0 right-0 w-full bg-white/80 dark:bg-slate-800/80 backdrop-blur-md shadow-sm top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center text-white shadow-lg rotate-3">
                    <img src="https://cdn.jsdelivr.net/gh/kingmxse/platform@master/m.webp" class="w-full h-full object-cover rounded-xl" alt="Mr kaiser">
                </div>
                <div>
                    <h1 class="text-xl font-bold leading-tight text-primary dark:text-blue-400">القيصر أحمد إبراهيم</h1>
                </div>
            </div>

            <div class="hidden md:flex items-center space-x-8 space-x-reverse">
                <a href="index.php" class="font-medium hover:text-primary transition">الرئيسية</a>
                <a href="lectures.php" class="font-medium hover:text-primary transition">المحاضرات</a>
                <a href="chatbot.php" class="font-medium hover:text-primary transition">المساعد</a>
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <a href="/admin/index.php" title="لوحة التحكم" class="font-medium hover:text-primary transition">لوحة التحكم</a>
                <?php endif; ?>
                <?php if(isset($_SESSION['user_id'])): ?>
                	<a href="recharge.php" class="font-medium hover:text-primary transition">شحن رصيد</a>
                    <a href="notification.php" class="relative p-2.5 bg-gray-100 dark:bg-slate-700 rounded-xl text-gray-500 dark:text-slate-300 hover:text-primary transition-all">
                        <i class="fa-solid fa-bell text-xl"></i>
                        <?php if($notif_count > 0): ?>
                            <span class="absolute top-1 right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white dark:border-slate-800 animate-bounce">
                                <?php echo $notif_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                	    <a href="https://call.xo.je" class="relative p-2.5 bg-gray-100 dark:bg-slate-700 rounded-xl text-gray-500 dark:text-slate-300 hover:text-primary transition-all">
                            <i class="fa-solid fa-user-group"></i>
                        </a>
                    <button id="theme-toggle" class="theme-toggle-btn p-2 mx-4 rounded-lg bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                        <span id="moon-icon" class="dark:hidden"><i class="fa-solid fa-moon"></i></span>
                        <span id="sun-icon" class="hidden dark:inline"><i class="fa-solid fa-sun"></i></span>
                    </button>
<div class="relative group">
    <button id="user-menu-button" class="flex items-center gap-3 p-1 pr-4 pl-2 rounded-full bg-gray-50 dark:bg-slate-700/50 border border-gray-200 dark:border-slate-600 hover:bg-gray-100 dark:hover:bg-slate-600 transition-all cursor-pointer">
        
        <div class="text-right hidden sm:block">
            <p class="text-xs font-bold text-slate-900 dark:text-white leading-none mb-1">
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </p>
            <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold leading-none">
                <?php echo number_format($_SESSION['user_balance'] ?? 0, 2); ?> ج.م
            </p>
        </div>

        <div class="w-10 h-10 rounded-full border-2 border-white dark:border-slate-800 shadow-sm overflow-hidden bg-indigo-100 flex items-center justify-center">
            <?php if(!empty($_SESSION['user_avatar'])): ?>
                <img 
                  src="<?php echo $avatar; ?>" 
                  onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>'"
                  class="w-full h-full object-cover"
                >
            <?php else: ?>
                <span class="text-indigo-600 font-bold text-lg">
                    <?php echo mb_substr($_SESSION['user_name'], 0, 1); ?>
                </span>
            <?php endif; ?>
        </div>

        <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 mr-1"></i>
    </button>

       <div id="user-dropdown" class="absolute left-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-gray-100 dark:border-slate-700 opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-[100]">
            <div class="p-2">
                <a href="profile.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/20 text-slate-700 dark:text-slate-200 transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <span class="font-bold text-sm">عرض البروفايل</span>
                </a>

                <a href="recharge.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 text-slate-700 dark:text-slate-200 transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <span class="font-bold text-sm">شحن رصيد</span>
                </a>

                <hr class="my-2 border-gray-100 dark:border-slate-700">

                <a href="logout.php" class="flex items-center gap-3 p-3 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </div>
                    <span class="font-bold text-sm">تسجيل الخروج</span>
                </a>
            </div>
        </div>
    </div>
                <?php else: ?>
                    <button id="theme-toggle" class="theme-toggle-btn p-2 mx-4 rounded-lg bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 transition">
                        <span id="moon-icon" class="dark:hidden"><i class="fa-solid fa-moon"></i></span>
                        <span id="sun-icon" class="hidden dark:inline"><i class="fa-solid fa-sun"></i></span>
                    </button>
                    <a href="login.php" class="bg-primary dark:bg-blue-600 text-white px-8 py-2.5 rounded-xl font-bold transition">تسجيل دخول</a>
                <?php endif; ?>
            </div>

            <div class="flex items-center gap-2 md:hidden">
                <button class="theme-toggle-btn p-2.5 rounded-xl bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-amber-400 transition-all active:scale-90">
                    <span class="dark:hidden"><i class="fa-solid fa-moon"></i></span>
                    <span class="hidden dark:inline"><i class="fa-solid fa-sun"></i></span>
                </button>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="notification.php" class="flex justify-between items-center font-bold p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700">
                        
                        <span><i class="fa-solid fa-bell text-xl"></i></span>
                        <?php if($notif_count > 0): ?>
                            <span class="bg-red-500 text-white px-2 py-0.5 rounded-lg text-xs"><?php echo $notif_count; ?> جديد</span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <button id="mobile-menu-button" class="p-2.5 rounded-xl bg-primary/10 dark:bg-blue-500/10 text-primary dark:text-blue-400 transition-all active:scale-90">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div id="mobile-menu" class="hidden md:hidden bg-white dark:bg-slate-800 border-t dark:border-slate-700 p-4 space-y-4 shadow-xl">
        <a href="index.php" class="block font-bold p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700">🏠 الرئيسية</a>
        <a href="lectures.php" class="block font-bold p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700">📚 المحاضرات</a>
        <a href="chatbot.php" class="block font-bold p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700">🤖 المساعد</a>
        <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
        <a href="/admin/index.php" title="لوحة التحكم" class="block font-bold p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700">لوحة التحكم</a>
        <?php endif; ?>
        <?php if(isset($_SESSION['user_id'])): ?>
        	<a href="recharge.php" class="block font-bold p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700">💱 شحن رصيد</a>
            <a href="https://call.xo.je" class="block font-bold p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700">👥 المجتمع الطلابي</a>
            <div class="bg-white dark:bg-slate-800/50 p-4 rounded-3xl border border-gray-100 dark:border-slate-700 shadow-sm mb-4">
                <?php if(isset($_SESSION['user_id']) && in_array($_SESSION['role'] ?? '', ['student','admin'])): ?>
                    <div class="flex items-center gap-4 mb-4">
                        <div class="relative">
                            <div class="w-16 h-16 rounded-full border-2 border-white dark:border-slate-700 shadow-md overflow-hidden bg-blue-100 flex items-center justify-center">
                                <?php if(!empty($_SESSION['user_avatar'])): ?>
                                               <img 
                                                  src="<?php echo $avatar; ?>" 
                                                  onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>'"
                                                  class="w-full h-full object-cover"
                                                >
                                <?php else: ?>
                                    <span class="text-blue-600 font-black text-2xl"><?php echo mb_substr($_SESSION['user_name'], 0, 1); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="absolute bottom-1 right-1 w-4 h-4 bg-green-500 border-2 border-white dark:border-slate-800 rounded-full"></div>
                        </div>

                        <div class="flex-1">
                            <p class="font-black text-lg text-slate-900 dark:text-white leading-tight">
                                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </p>
                            <div class="mt-1 flex items-center gap-2">
                                <span class="text-[11px] font-bold px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 rounded-full">
                                    💰 <?php echo number_format($_SESSION['user_balance'] ?? 0, 2); ?> ج.م
                                </span>
                            </div>
                        </div>
                    </div>

                    <a href="profile.php" class="block w-full text-center bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 py-2.5 rounded-xl font-bold text-sm transition-colors mb-2">
                        عرض الملف الشخصي
                    </a>
                <?php endif; ?>

                <a href="logout.php" class="flex items-center justify-center gap-2 w-full bg-red-50 dark:bg-red-900/20 text-red-600 py-2.5 rounded-xl font-bold text-sm hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    تسجيل خروج
                </a>
            </div>
        <?php else: ?>
            <a href="login.php" class="block text-center bg-primary text-white p-4 rounded-2xl font-bold shadow-lg">تسجيل دخول</a>
            <a href="register.php" class="block text-center bg-secondary text-primary p-4 rounded-2xl font-bold">إنشاء حساب جديد</a>
        <?php endif; ?>
    </div>
    
</nav>
<main class="pt-20"> </main>
<script src="https://cdn.jsdelivr.net/gh/kingmxse/platform@master/nav.js"></script>
<script src="https://cdn.jsdelivr.net/gh/kingmxse/platform@master/loader_js.js"></script>
