<?php

use App\Lib\FileManager;
use App\Models\GeneralSetting;
use App\Jobs\ProcessNotificationJob;
use Illuminate\Support\Facades\Cache;


if (!function_exists('gs')) {
    function gs($key = null)
    {
        $general = Cache::remember('GeneralSetting', 3600, function () {
            return GeneralSetting::first();
        });

        if ($key) {
            return $general->$key ?? null;
        }

        return $general;
    }
}

function getFilePath($key)
{
    return fileManager()->$key()->path;
}

function fileManager()
{
    return new FileManager();
}

function fileUploader($file, $location, $size = null, $old = null, $thumb = null, $filename = null)
{
    $fileManager           = new FileManager($file);
    $fileManager->path     = $location;
    $fileManager->size     = $size;
    $fileManager->old      = $old;
    $fileManager->thumb    = $thumb;
    $fileManager->filename = $filename;
    $fileManager->upload();
    return $fileManager->filename;
}

function siteFavicon()
{
    return '';
}


// if (!function_exists('notify')) {
//     function notify($templateName, $user = null, $shortCodes = [], $sendVia = null, $queue = true)
//     {
//         $notificationData = [
//             'templateName' => $templateName,
//             'user' => $user,
//             'shortCodes' => $shortCodes,
//             'sendVia' => $sendVia,
//             'createLog' => true
//         ];

//         if ($queue) {
//             ProcessNotificationJob::dispatch($notificationData);
//         } else {
//             $notify = app('notifier');
//             $notify->templateName = $templateName;
//             $notify->user = $user;
//             $notify->shortCodes = $shortCodes;
//             $notify->sendVia = $sendVia;
//             $notify->send();
//         }
//     }
// }

// Updated helper function to support loop items
if (!function_exists('notify')) {
    function notify($templateName, $user = null, $shortCodes = [], $sendVia = null, $queue = true, $loopItems = [])
    {
        $notificationData = [
            'templateName' => $templateName,
            'user' => $user,
            'shortCodes' => $shortCodes,
            'loopItems' => $loopItems, // Add loop items
            'sendVia' => $sendVia,
            'createLog' => true
        ];

        if ($queue) {
            ProcessNotificationJob::dispatch($notificationData);
        } else {
            $notify = app('notifier');
            $notify->templateName = $templateName;
            $notify->user = $user;
            $notify->shortCodes = $shortCodes;
            $notify->loopItems = $loopItems; // Set loop items
            $notify->sendVia = $sendVia;
            $notify->send();
        }
    }
}