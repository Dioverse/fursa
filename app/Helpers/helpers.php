<?php

use App\Lib\FileManager;
use App\Models\GeneralSetting;
use App\Jobs\ProcessNotificationJob;
use Illuminate\Support\Facades\Cache;


if (!function_exists('gs')) {
    /**
     * Retrieves application general settings, cached for performance.
     *
     * @param string|array|null $key The key(s) of the setting(s) to retrieve.
     * @return mixed|\App\Models\GeneralSetting|null
     */
    function gs($key = null)
    {
        // 1. Retrieve or cache the entire GeneralSetting record
        $general = Cache::remember('GeneralSetting', 3600, function () {
            // Ensure the correct GeneralSetting model is imported and used
            return GeneralSetting::first();
        });

        // If the entire object could not be retrieved, return null early.
        if (!$general) {
            return null;
        }

        // 2. Handle key retrieval based on input type
        if (is_array($key)) {
            // If an array of keys is requested, return an array with only those keys.
            // We use the collect helper to easily filter the object properties.
            return collect($general)->only($key)->toArray();
        }

        if ($key) {
            // If a single key (string) is requested, return its value or null.
            return $general->$key ?? null;
        }

        // 3. If no key is provided, return the entire GeneralSetting object.
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