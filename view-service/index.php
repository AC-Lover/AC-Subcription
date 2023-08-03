<?php
if ((isset($_POST['userData']) and empty($_POST['userData'])) or !isset($_POST['userData'])){
    header('Location: /');
    exit();
}
$user=json_decode(base64_decode($_POST['userData']),true);

function bytesformat($bytes, $precision = 2): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?= $user['username'] ?> </title>
    <link href="build.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.6/flowbite.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script defer src="qrcode.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.1/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs-i18n@2.4.0/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@ryangjchandler/alpine-clipboard@2.2.0/dist/alpine-clipboard.js"></script>
    <script>
        <?php $expireDateInit = empty($user['expire']) ? '∞' : date('Y-m-d H:i:s', $user['expire']); ?>
        <?php $dataUsage=empty($user['data_limit'])? 100 :(($user['used_traffic']/$user['data_limit']) < 100 ? number_format($user['used_traffic']/$user['data_limit'], 2) : 100);?>
        const resetInterval = "<?php echo $user['data_limit_reset_strategy']; ?>";
        <?php $expireDateVar = empty($user['expire']) ? '∞' : date('Y-m-d H:i:s', $user['expire']);?>

        let appsJson = {
            "IOS": {
                "Streisand": {
                    "url": [
                        {
                            "name": "IOS 14+",
                            "url": "https://apps.apple.com/us/app/streisand/id6450534064"
                        }
                    ],
                    "tutorial": ""
                },
                "FoXray": {
                    "url": [
                        {
                            "name": "IOS 16+",
                            "url": "https://apps.apple.com/us/app/foxray/id6448898396"
                        }
                    ],
                    "tutorial": ""
                },
                "V2Box": {
                    "url": [
                        {
                            "name": "IOS 14+",
                            "url": "https://apps.apple.com/us/app/v2box-v2ray-client/id6446814690"
                        }
                    ],
                    "tutorial": ""
                },
                "Shadowrocket": {
                    "url": [
                        {
                            "name": "$3.99",
                            "url": "https://apps.apple.com/ca/app/shadowrocket/id932747118"
                        }
                    ],
                    "tutorial": ""
                }
            },
            "Android": {
                "v2rayNG": {
                    "url": [
                        {
                            "name": "Github",
                            "url": "https://github.com/2dust/v2rayNG/releases/download/1.8.5/v2rayNG_1.8.5.apk"
                        },
                        {
                            "name": "GooglePlay",
                            "url": "https://play.google.com/store/apps/details?id=com.v2ray.ang"
                        }
                    ],
                    "tutorial": ""
                },
                "NekoBox": {
                    "url": [
                        {
                            "name": "Arm64",
                            "url": "https://github.com/MatsuriDayo/NekoBoxForAndroid/releases/download/1.1.4/NB4A-1.1.4-arm64-v8a.apk"
                        },
                        {
                            "name": "Armeabi",
                            "url": "https://github.com/MatsuriDayo/NekoBoxForAndroid/releases/download/1.1.4/NB4A-1.1.4-armeabi-v7a.apk"
                        }
                    ],
                    "tutorial": ""
                }
            },
            "Windows": {
                "v2rayN": {
                    "url": [
                        {
                            "name": "",
                            "url": "https://github.com/2dust/v2rayN/releases/download/6.27/zz_v2rayN-With-Core-SelfContained.7z"
                        }
                    ],
                    "tutorial": ""
                },
                "nekoray": {
                    "url": [
                        {
                            "name": "",
                            "url": "https://github.com/MatsuriDayo/nekoray/releases/download/3.8/nekoray-3.8-2023-06-14-windows64.zip"
                        }
                    ],
                    "tutorial": ""
                }
            }
        };
        let langJson = {
            "en": {
                "active": "Active",
                "limited": "Limited",
                "expired": "Expired",
                "disabled": "Disabled",
                "dataUsage": "Data Usage: ",
                "expirationDate": "Expiration Date: ",
                "resetIntervalDay": "(Resets Every Day)",
                "resetIntervalWeek": "(Resets Every Week)",
                "resetIntervalMonth": "(Resets Every Month)",
                "resetIntervalYear": "(Resets Every Year)",
                "remainingDays": "Remaining Days: ",
                "remainingDaysSufix": " Days",
                "links": "Links",
                "apps": "Apps",
                "tutorials": "Tutorials",
                "subscription": "Subscription",
                "language": "Language",
                "settings": "Settings",
                "darkMode": "Dark mode",
                "copyAll": "Copy All",
                "copyAllMessage": "All configs copied"
            },
            "fa": {
                "active": "فعال",
                "limited": "تمام شده",
                "expired": "پایان یافته",
                "disabled": "غیرفعال",
                "dataUsage": "مصرف داده: ",
                "expirationDate": "تاریخ پایان: ",
                "resetIntervalDay": "(ریست روزانه)",
                "resetIntervalWeek": "(ریست هفتگی)",
                "resetIntervalMonth": "(ریست ماهانه)",
                "resetIntervalYear": "(ریست سالانه)",
                "remainingDays": "روزهای باقی‌مانده: ",
                "remainingDaysSufix": " روز",
                "links": "لینک‌ها",
                "apps": "برنامه‌ها",
                "tutorials": "آموزش‌ها",
                "subscription": "لینک اشتراک",
                "language": "زبان",
                "settings": "تنظیمات",
                "darkMode": "تم تیره",
                "copyAll": "کپی همه",
                "copyAllMessage": "تمام کانفیگ‌ها کپی شدند"
            },
            "ru": {
                "active": "активный",
                "limited": "ограниченное",
                "expired": "истекший",
                "disabled": "неполноценный",
                "dataUsage": "Использование данных: ",
                "expirationDate": "Дата окончания срока: ",
                "resetIntervalDay": "(сбрасывает каждый день)",
                "resetIntervalWeek": "(сбрасывается каждую неделю)",
                "resetIntervalMonth": "(сбрасывается каждый месяц)",
                "resetIntervalYear": "(сбрасывается каждый год)",
                "remainingDays": "оставшиеся дни: ",
                "remainingDaysSufix": " дни",
                "links": "ссылки",
                "apps": "Программы",
                "tutorials": "учебники",
                "subscription": "подписка",
                "language": "язык",
                "settings": "настройки",
                "darkMode": "настройки",
                "copyAll": "скопировать все",
                "copyAllMessage": "Все конфиги скопированы"
            }
        };
        let settings = {
            "darkMode": 1,
            "language": "fa"
        };

        document.addEventListener( 'alpine:init', () =>
        {
            darkMode = localStorage.getItem( "dark" ) ?? settings.darkMode;
            localStorage.setItem( "dark", darkMode );
        } );

        document.addEventListener( "alpine-i18n:ready", () =>
        {
            window.AlpineI18n.fallbackLocale = 'en';
            let locale = localStorage.getItem( "lang" ) ?? settings.language;
            window.AlpineI18n.create( locale, langJson );
            AlpineI18n.locale = locale;
            document.body.setAttribute( "dir", locale === "fa" ? "rtl" : "ltr" );
            if ( locale === "fa" ) $( document.body ).addClass( "font-[Vazirmatn]" );
            else $( document.body ).removeClass( "font-[Vazirmatn]" );
        } );
    </script>
</head>
<body :class="settings.darkMode == 1 ? 'dark' : ''" x-data>
    <div class="relative flex min-h-screen flex-col justify-center overflow-hidden bg-main-light dark:bg-main-dark sm:py-6 transition" id="page-container">
        <div class="relative bg-sub-light dark:bg-sub-dark px-6 pt-10 pb-8 shadow-main-sh dark:shadow-main-sh-dark sm:mx-auto sm:rounded-xl sm:px-10 bg-clip-padding backdrop-filter backdrop-blur-xl bg-opacity-0 w-full max-w-2xl">
            <div class="mx-auto max-w-xl">
                <div class="flex flex-col sm:flex-row space-y-10 sm:space-y-0 sm:divide-x sm:rtl:divide-x-reverse sm:divide-blue-600/50">
                    <div class="basis-1/3 space-y-4 flex flex-col items-center py-3 sm:ltr:pr-9 sm:rtl:pl-9">
                        <img src="https://cdn.jsdelivr.net/gh/MuhammadAshouri/marzban-templates@master/template-01/images/marzban.svg" class="w-28"  alt=""/>
                        <span class="inline-block dark:text-white text-black font-semibold text-lg"><?php echo $user['username']; ?></span>
                        <span class="px-4 py-2 rounded-full inline-block shadow-md shadow-green-900 font-bold text-gray-200" x-data="{status: '<?php echo $user['status']; ?>'}" x-text="[status == 'active' ? $t('active') : status == 'limited' ? $t('limited') : status == 'expired' ? $t('expired') : $t('disabled')]" :class="[status == 'active' ? 'bg-blue-600' : status == 'limited' ? 'bg-red-600' : status == 'expired' ? 'bg-orange-600' : 'bg-gray-600']"></span>
                        <span class="flex cursor-pointer" onclick="openSettings()">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" class="w-6 h-6 stroke-blue-600 drop-shadow-lg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                 <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-blue-600 drop-shadow-lg rtl:mr-2 ltr:ml-2" x-text="$t('settings')"></span>
                            <a class="rounded-md shadow-lg transition duration-300 bg-blue-600 text-white text-center text-lg py-2 w-4/5 cursor-pointer hover:shadow-xl stroke-blue-600" x-text="$t('proxy')" x-show="settings.proxy != ''" x-bind:href="settings.proxy"></a>
                        </span>
                    </div>
                    <div class="basis-2/3 flex flex-row items-center sm:ltr:pl-9 sm:rtl:pr-9">
                        <div class="data-usage w-full" x-data="progressBar" x-init="Alpine.data( 'progressBar', () => progressBar )">
                            <div class="dark:text-white text-black text-center"><span class="font-bold" x-text="$t('dataUsage')"></span><span dir="ltr"><?php echo bytesformat($user['used_traffic']).' / '. empty($user['data_limit']) ? '∞' : bytesformat($user['data_limit']); ?></span></div>
                            <div class="bg-gray-200 dark:bg-gray-900 rounded-full h-6 mt-5 drop-shadow-lg" role="progressbar" :aria-valuenow="width" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar rounded-full h-6 text-center dark:text-white text-black text-sm transition leading-6" :class="color" :style="`width: ${width}%; transition: width 2s;`" x-text="`${width}%`"></div>
                            </div>
                            <div class="dark:text-white text-black mt-10 text-center"><span class="font-bold" x-text="$t('expirationDate')"></span><span dir="ltr" x-data="{expireDate: ''}" x-init="Alpine.data( 'expireDate', expireDate = <?=$expireDateVar?> )" x-text="expireDate"></span></div><!--2023/06/31 10:43:59-->
                            <div class="dark:text-white text-black mt-3 text-sm text-center" x-text="resetInterval == 'year' ? $t('resetIntervalYear') : resetInterval == 'month' ? $t('resetIntervalMonth') : resetInterval == 'week' ? $t('resetIntervalWeek') : resetInterval == 'day' ? $t('resetIntervalDay') : ''"></div>
                            <div class="dark:text-white text-black mt-5 text-center"><span class="font-bold" x-text="$t('remainingDays')"></span><span><?php echo empty($user['expire']) ? '∞' : '(' . intval(($user['expire'] - time()) / (24 * 3600)) . ')'; ?></span><span x-text="$t('remainingDaysSufix')"></span></div>
                        </div>
                    </div>


                </div>
                <div class="shadow-box-shadow rounded-lg p-5 mt-7" x-data>
                    <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="main-tab" data-tabs-toggle="#tabs-content" role="tablist">
                            <li class="flex-1" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg w-full transition" id="profile-tab" data-tabs-target="#links" type="button" role="tab" aria-controls="links" aria-selected="false" x-text="$t('links')"></button>
                            </li>
                            <li class="flex-1" role="presentation">
                                <button class="inline-block w-full p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 transition" id="apps-tab" data-tabs-target="#apps" type="button" role="tab" aria-controls="apps" aria-selected="false" x-text="$t('apps')"></button>
                            </li>
                            <li class="flex-1" role="presentation">
                                <button class="inline-block w-full p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300 transition" id="tutorials-tab" data-tabs-target="#tutorials" type="button" role="tab" aria-controls="tutorials" aria-selected="false" x-text="$t('tutorials')"></button>
                            </li>
                        </ul>
                    </div>
                    <div id="tabs-content">
                        <div class="hidden" id="links" role="tabpanel" aria-labelledby="links-tab">
                            <ul class="list-none p-0 m-0">
                                <li class="flex px-3 mb-3 justify-between leading-[3.5rem] bg-black/20 rounded-md shadow-lg" x-data>
                                    <span class="font-semibold flex-1 dark:text-gray-200 text-black cursor-default" x-text="$t('subscription')"></span>
                                    <div class="flex justify-between items-center">
                                        <div class="w-8 h-8 ltr:mr-3 rtl:ml-3 cursor-pointer" x-data="{copyColor: 'stroke-blue-600'}" @click="() => { navigator.clipboard.writeText( '<?php echo $user['subscription_url']; ?>' ); copyColor = 'stroke-green-600'; setTimeout(() => copyColor = 'stroke-blue-600', 2000); }">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" class="dark:hover:stroke-gray-300 hover:stroke-gray-800 transition-colors" :class="copyColor" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                                            </svg>
                                        </div>
                                        <div class="w-8 h-8 cursor-pointer qr-button" :data-link="<?php echo $user['subscription_url']; ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" class="stroke-blue-600 dark:hover:stroke-gray-300 hover:stroke-gray-800 transition-colors" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                                            </svg>
                                        </div>

                                    </div>
                                </li>

                                <?php if($user['status']==='active'){ foreach($user['links'] as $link): ?>
                                    <li class="flex px-3 mb-1 justify-between leading-[3.5rem] hover:bg-black/10 rounded-md hover:shadow-lg transition duration-300" x-data="{link: '<?php echo $link; ?>'}">
                                        <span class="font-semibold flex-1 dark:text-gray-200 text-black cursor-default" x-text="getRemark(link)"></span>
                                        <div class="flex justify-between items-center">
                                            <div class="w-8 h-8 ltr:mr-3 rtl:ml-3 cursor-pointer" x-data="{copyColor: 'stroke-blue-600'}" @click="() => { navigator.clipboard.writeText( link ); copyColor = 'stroke-green-600'; setTimeout(() => copyColor = 'stroke-blue-600', 2000); }">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" class="dark:hover:stroke-gray-300 hover:stroke-gray-800 transition-colors" :class="copyColor" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                                                </svg>
                                            </div>
                                            <div class="w-8 h-8 cursor-pointer qr-button" :data-link="link">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" class="stroke-blue-600 dark:hover:stroke-gray-300 hover:stroke-gray-800 transition-colors" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; }?>

                                <li class="rounded-md shadow-lg transition duration-300 bg-blue-600 text-white text-center text-lg py-2 mt-3 cursor-pointer hover:shadow-2xl copyAllButton" x-text="$t('copyAll')"></li>
                            </ul>
                        </div>
                        <div class="hidden" id="apps" role="tabpanel" aria-labelledby="apps-tab">
                            <div class="flex sm:flex-row flex-col">
                                <div class="sm:basis-1/5 sm:rtl:ml-4 sm:ltr:mr-4">
                                    <ul class="flex sm:flex-col text-sm font-medium text-center" id="apps-tab" data-tabs-toggle="#apps-tabs-content" role="tablist">
                                        <template x-for="item in Object.keys(appsJson)">
                                            <li class="flex-grow mb-2" role="presentation">
                                                <button class="inline-block p-4 border-b-2 rounded-t-lg w-full transition" :id="item + '-tab'" :data-tabs-target="'#' + item" type="button" role="tab" :aria-controls="item" aria-selected="false" x-text="item"></button>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                                <div id="apps-tabs-content" class="sm:basis-4/5">
                                    <template x-for="item in Object.keys(appsJson)">
                                        <div class="hidden" :id="item" role="tabpanel" :aria-labelledby="item + '-tab'">
                                            <ul class="list-none p-0 m-0">
                                                <template x-for="app in Object.keys(appsJson[item])">
                                                    <template x-for="subApp in appsJson[item][app].url">
                                                        <li :class="subApp.best ? 'bg-green-600/30 shadow-lg' : 'hover:bg-black/10 hover:shadow-lg'" class="flex px-3 mb-1 justify-between leading-[3.5rem] rounded-md transition duration-300" x-data="{link: subApp.url}">
                                                            <div class="flex flex-row items-center space-x-3 rtl:flex-row-reverse cursor-default">
                                                                <span class="font-semibold flex-1 dark:text-gray-200 text-black" x-text="app"></span>
                                                                <span :class="subApp.best ? 'dark:text-gray-200 text-gray-800' : 'text-gray-600'" class="text-sm" x-text="subApp.name"></span>
                                                            </div>
                                                            <div class="flex justify-between items-center">
                                                                <a class="w-8 h-8 cursor-pointer" :href="link" target="_blank">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" class="stroke-blue-600 dark:hover:stroke-gray-300 hover:stroke-gray-800 transition-colors" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </li>
                                                    </template>
                                                </template>
                                            </ul>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="hidden" id="tutorials" role="tabpanel" aria-labelledby="tutorials-tab">
                            <div class="flex sm:flex-row flex-col">
                                <div class="sm:basis-1/5 sm:rtl:ml-4 sm:ltr:mr-4">
                                    <ul class="flex sm:flex-col text-sm font-medium text-center" id="vertical-tab" data-tabs-toggle="#tutorials-tabs-content" role="tablist">
                                        <template x-for="item in Object.keys( appsJson ).filter( ( platform ) => Object.keys( appsJson[ platform ] ).some( ( app ) => appsJson[ platform ][ app ].tutorial !== '' ) )">
                                            <li class="flex-grow mb-2" role="presentation">
                                                <button class="inline-block p-4 border-b-2 rounded-t-lg w-full transition" :id="item + '-tutorials-tab'" :data-tabs-target="'#' + item + '-tutorials'" type="button" role="tab" :aria-controls="item + '-tutorials'" aria-selected="false" x-text="item"></button>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                                <div id="tutorials-tabs-content" class="sm:basis-4/5">
                                    <template x-for="item in Object.keys(appsJson)">
                                        <div class="hidden" :id="item + '-tutorials'" role="tabpanel" :aria-labelledby="item + '-tutorials-tab'">
                                            <ul class="list-none p-0 m-0">
                                                <template x-for="app in Object.keys(appsJson[item]).reverse()">
                                                    <li class="flex px-3 mb-1 justify-between leading-[3.5rem] hover:bg-black/10 rounded-md hover:shadow-lg transition duration-300">
                                                        <span class="font-semibold flex-1 dark:text-gray-200 text-black cursor-default" x-text="app"></span>
                                                        <div class="flex justify-between items-center">
                                                            <button class="w-8 h-8 cursor-pointer video-button" :data-link="appsJson[item][app].tutorial" :data-title="app">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" class="stroke-blue-600 dark:hover:stroke-gray-300 hover:stroke-gray-800 transition-colors" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75H6A2.25 2.25 0 003.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0120.25 6v1.5m0 9V18A2.25 2.25 0 0118 20.25h-1.5m-9 0H6A2.25 2.25 0 013.75 18v-1.5M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="popup" class="fixed w-fit min-w-[20rem] max-h-[95vh] h-fit p-10 pt-7 shadow-dialog-shadow dark:shadow-2xl rounded-lg top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-main-light dark:bg-main-dark hidden">
        <h2 class="h-10 leading-[2.5rem] mb-4 inline-block font-semibold text-gray-950 dark:text-white"></h2>
        <a class="close absolute ltr:right-10 rtl:left-10 top-7 text-3xl cursor-pointer dark:text-white text-gray-950">&times;</a>
        <div class="content rounded-lg"></div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.6/flowbite.min.js"></script>
    <script>

        let progressBar = {
            width: '<?=$dataUsage?>',
            color: '<?php echo match (true){($dataUsage<40)=>'bg-green-600',($dataUsage<80)=>'bg-yellow-600',default=>'bg-red-500'} ?>'
        };

        let qrSize = $( window ).width() > 500 ? $( window ).height() > 500 ? 400 : $( window ).height() - 200 : $( window ).height() > 500 ? $( window ).width() - 100 : $( window ).height() - 200;
        $(window).resize(function () {
            $( window ).resize( function ()
            if ($(window).width() > 500) qrSize = 400;
            {
            else qrSize = $(window).width() - 100;
                qrSize = $( window ).width() > 500 ? $( window ).height() > 500 ? 400 : $( window ).height() - 200 : $( window ).height() > 500 ? $( window ).width() - 100 : $( window ).height() - 200;
            });
        } );

        const popup = $( "#popup" );
        const qrButtons = $( '.qr-button' );
        const popupClose = $( '#popup > a.close' ).on( "click", () =>
        {
            popup.toggleClass( "hidden" );
            $( "#popup > .content" ).removeClass( "bg-white p-5" ).html( "" );
            $( "#popup > h2" ).html( "" );
            $( "#page-container" ).removeClass( 'blur-sm scale-110 -z-10' );
            setTimeout( () =>
            {
                $( document.body ).removeClass( 'overflow-hidden' );
            }, 200 );
        } );

        qrButtons.each( ( i, elem ) =>
        {
            $( elem ).on( 'click', () =>
            {
                const link = $( elem ).attr( "data-link" );
                $( "#popup > .content" ).addClass( "bg-white p-5" ).html( "" ).qrcode(
                    {
                        size: qrSize,
                        radius: 0.2,
                        text: link,
                        colorDark: "#000000",
                        colorLight: "#ffffff"
                    }
                );
                $( document.body ).addClass( 'overflow-hidden' );
                $( "#page-container" ).addClass( 'blur-sm scale-110 -z-10' );
                $( "#popup > h2" ).html( getRemark( link ) );
                popup.removeClass( "hidden" );
            } );
        } );

        $(".copyAllButton").on('click', async ( a ) => {
            let links = [];
            $(".qr-button").each((i, ele) => {
                let link = $(ele).attr("data-link");
                if (!link.startsWith("http")) links.push(link);
            });
            await navigator.clipboard.writeText(links.join("\n"));
            const thisObj = $(a.target).css("background", "#16a34a");
            setTimeout( () => thisObj.css( "background", "#2563eb" ), 1500 );
        });

        document.addEventListener( 'alpine:initialized', () =>
        {
            $( '.video-button' ).each( ( i, elem ) =>
            {
                $( elem ).on( 'click', () =>
                {
                    const title = $( elem ).attr( "data-title" );
                    const link = $( elem ).attr( "data-link" );
                    $( document.body ).addClass( 'overflow-hidden' );
                    $( "#page-container" ).addClass( 'blur-sm scale-110 -z-10' );
                    $( "#popup > .content" ).html( "" );
                    let video = $( "<video>" ).attr( "controls", "" ).addClass( "rounded-lg" );
                    $( "<source>" ).attr( { "src": link, "type": "video/mp4" } ).appendTo( video );
                    video.appendTo( "#popup > .content" );
                    $( "#popup > h2" ).html( title );
                    popup.removeClass( "hidden" );
                } );
            } );
        } );

        window.addEventListener( "alpine-i18n:locale-change", function ()
        {
            const locale = window.AlpineI18n.locale;
            document.body.setAttribute( "dir", locale === "fa" ? "rtl" : "ltr" );
            if ( locale === "fa" ) $( document.body ).addClass( "font-[vazirmatn]" );
            else $( document.body ).removeClass( "font-[vazirmatn]" );
        } );

        function getRemark ( link )
        {
            if ( link.startsWith( "http" ) ) return AlpineI18n.t( "subscription" );
            if ( link.includes( "vmess://" ) )
            {
                const config = JSON.parse( atob( link.replace( "vmess://", "" ) ) );
                return config.ps;
            }
            else return decodeURIComponent( link.split( "#" )[ 1 ] );
        }

        function changeLang ( ele )
        {
            localStorage.setItem( "lang", ele.value );
            window.AlpineI18n.locale = ele.value;
            document.body.setAttribute( "dir", ele.value === "fa" ? "rtl" : "ltr" );
            if ( ele.value === "fa" ) $( document.body ).addClass( "font-[Vazirmatn]" );
            else $( document.body ).removeClass( "font-[Vazirmatn]" );
        }

        function changeTheme ( ele )
        {
            settings.darkMode = ele.checked ? 1 : 0;
            localStorage.setItem("dark", settings.darkMode);
            if ( !ele.checked ) $( document.body ).removeClass( "dark" );
            else $( document.body ).addClass( "dark" );
        }

        function openSettings ()
        {
            $( document.body ).addClass( 'overflow-hidden' );
            $( "#page-container" ).addClass( 'blur-sm scale-110 -z-10' );
            const content = $("#popup > .content");
            $( `<label for="default" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">` + AlpineI18n.t( 'language' ) + `</label>
<select id="default" class="bg-gray-50 border border-gray-300 text-gray-900 mb-6 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" onchange="changeLang(this)">
  <option value="en"` + ( AlpineI18n.locale === "en" ? " selected" : "" ) + `>English</option>
  <option value="fa"` + ( AlpineI18n.locale === "fa" ? " selected" : "" ) + `>فارسی</option>
  <option value="ru"` + ( AlpineI18n.locale === "ru" ? " selected" : "" ) + `>Русский</option>
</select>
<label class="relative flex justify-between items-center cursor-pointer">
  <input type="checkbox" value="" class="sr-only peer"` + (settings.darkMode === 1 ? " checked" : "") + ` onchange="changeTheme(this)">
  <span class="text-sm font-medium text-gray-900 dark:text-white">` + AlpineI18n.t( "darkMode" ) + `</span>
  <div class="w-11 h-6 relative bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
</label>
`).appendTo( content );
            $( "#popup > h2" ).html( AlpineI18n.t( "settings" ) );
            popup.removeClass( "hidden" );
        }

    </script>
</body>
</html>
