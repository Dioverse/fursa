<?php
namespace App\Http\Controllers\Api\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Notify\Sms;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function globalEmailUpdate(Request $request)
    {
        $request->validate([
            'email_from'      => 'required|email|string|max:40',
            'email_from_name' => 'required|string|max:100',
            'email_template'  => 'required|string',
        ]);

        $general                  = gs();
        $general->email_from      = $request->email_from;
        $general->email_from_name = $request->email_from_name;
        $general->email_template  = $request->email_template;
        $general->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Global email template updated successfully',
            'data'    => $general,
        ]);
    }

    public function globalSmsUpdate(Request $request)
    {
        $request->validate([
            'sms_from'     => 'required|string|max:40',
            'sms_template' => 'required|string',
        ]);

        $general               = gs();
        $general->sms_from     = $request->sms_from;
        $general->sms_template = $request->sms_template;
        $general->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Global SMS template updated successfully',
            'data'    => $general,
        ]);
    }

    public function globalPushUpdate(Request $request)
    {
        $request->validate([
            'push_template' => 'required|string',
            'push_title'    => 'required|string|max:255',
        ]);

        $general                = gs();
        $general->push_template = $request->push_template;
        $general->push_title    = $request->push_title;
        $general->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Global push notification template updated successfully',
            'data'    => $general,
        ]);
    }

    public function templates()
    {
        $templates = NotificationTemplate::orderBy('name')->get();

        return response()->json([
            'status'  => 'success',
            'message' => 'Notification templates fetched successfully',
            'data'    => $templates,
        ]);
    }

    public function templateEdit($type, $id)
    {
        $template = NotificationTemplate::findOrFail($id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Template fetched successfully',
            'data'    => [
                'type'     => $type,
                'template' => $template,
            ],
        ]);
    }

    public function templateUpdate(Request $request, $type, $id)
    {
        $validationRule = [];
        if ($type === 'email') {
            $validationRule = [
                'subject'    => 'required|string|max:255',
                'email_body' => 'required|string',
            ];
        } elseif ($type === 'sms') {
            $validationRule = [
                'sms_body' => 'required|string',
            ];
        } elseif ($type === 'push') {
            $validationRule = [
                'push_body'  => 'required|string',
                'push_title' => 'required|string|max:255',
            ];
        }

        $validated = $request->validate($validationRule);

        $template = NotificationTemplate::findOrFail($id);

        if ($type === 'email') {
            $template->subject                 = $validated['subject'];
            $template->email_body              = $validated['email_body'];
            $template->email_sent_from_name    = $request->email_sent_from_name;
            $template->email_sent_from_address = $request->email_sent_from_address;
            $template->email_status            = $request->boolean('email_status') ? Status::ENABLE : Status::DISABLE;
        } elseif ($type === 'sms') {
            $template->sms_body      = $validated['sms_body'];
            $template->sms_sent_from = $request->sms_sent_from;
            $template->sms_status    = $request->boolean('sms_status') ? Status::ENABLE : Status::DISABLE;
        } elseif ($type === 'push') {
            $template->push_title  = $validated['push_title'];
            $template->push_body   = $validated['push_body'];
            $template->push_status = $request->boolean('push_status') ? Status::ENABLE : Status::DISABLE;
        }

        $template->save();

        return response()->json([
            'message' => 'Notification template updated successfully',
            'data'    => $template,
        ]);
    }

    public function emailSetting()
    {
        return response()->json([
            'message' => 'Email settings fetched successfully',
            'data'    => gs('mail_config'),
        ]);
    }

    public function emailSettingUpdate(Request $request)
    {
        $request->validate([
            'email_method' => 'required|in:php,smtp,sendgrid,mailjet',
            'host'         => 'required_if:email_method,smtp',
            'port'         => 'required_if:email_method,smtp',
            'username'     => 'required_if:email_method,smtp',
            'password'     => 'required_if:email_method,smtp',
            'enc'          => 'required_if:email_method,smtp',
            'appkey'       => 'required_if:email_method,sendgrid',
            'public_key'   => 'required_if:email_method,mailjet',
            'secret_key'   => 'required_if:email_method,mailjet',
        ]);

        if ($request->email_method === 'php') {
            $data = ['name' => 'php'];
        } elseif ($request->email_method === 'smtp') {
            $data         = $request->only('host', 'port', 'enc', 'username', 'password');
            $data['name'] = 'smtp';
        } elseif ($request->email_method === 'sendgrid') {
            $data = ['name' => 'sendgrid', 'appkey' => $request->appkey];
        } elseif ($request->email_method === 'mailjet') {
            $data = [
                'name'       => 'mailjet',
                'public_key' => $request->public_key,
                'secret_key' => $request->secret_key,
            ];
        }

        $general              = gs();
        $general->mail_config = $data;
        $general->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Email settings updated successfully',
            'data'    => $general->mail_config,
        ]);
    }

    public function emailTest(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $config       = gs('mail_config');
        $receiverName = explode('@', $request->email)[0];
        $subject      = strtoupper($config->name) . ' Configuration Success';
        $message      = 'Your email notification setting is configured successfully for ' . gs('site_name');
        $ntfy         = [];
        
        if (gs('en')) {
            $user = [
                'username' => $request->email,
                'email'    => $request->email,
                'fullname' => $receiverName,
            ];
            $ntfy = notify($user, 'DEFAULT', [
                'subject' => $subject,
                'message' => $message,
            ], ['email'], false);
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email notifications are disabled in general settings',
            ], 400);
        }

            return response()->json([
                $ntfy
            ], 500);

        return response()->json([
            'status'  => 'success',
            'message' => 'Email sent to ' . $request->email . ' successfully',
        ]);
    }

    /**
     * Get SMS Notification Settings
     */
    public function smsSetting()
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'SMS settings fetched successfully',
            'data'    => gs('sms_config'),
        ]);
    }

    /**
     * Update SMS Notification Settings
     */
    public function smsSettingUpdate(Request $request)
    {
        $request->validate([
            'sms_method'             => 'required|in:clickatell,infobip,messageBird,nexmo,smsBroadcast,twilio,textMagic,custom',
            'clickatell_api_key'     => 'required_if:sms_method,clickatell',
            'message_bird_api_key'   => 'required_if:sms_method,messageBird',
            'nexmo_api_key'          => 'required_if:sms_method,nexmo',
            'nexmo_api_secret'       => 'required_if:sms_method,nexmo',
            'infobip_username'       => 'required_if:sms_method,infobip',
            'infobip_password'       => 'required_if:sms_method,infobip',
            'sms_broadcast_username' => 'required_if:sms_method,smsBroadcast',
            'sms_broadcast_password' => 'required_if:sms_method,smsBroadcast',
            'text_magic_username'    => 'required_if:sms_method,textMagic',
            'apiv2_key'              => 'required_if:sms_method,textMagic',
            'account_sid'            => 'required_if:sms_method,twilio',
            'auth_token'             => 'required_if:sms_method,twilio',
            'from'                   => 'required_if:sms_method,twilio',
            'custom_api_method'      => 'required_if:sms_method,custom|in:get,post',
            'custom_api_url'         => 'required_if:sms_method,custom',
        ]);

        $data = [
            'name'          => $request->sms_method,
            'clickatell'    => ['api_key' => $request->clickatell_api_key],
            'infobip'       => ['username' => $request->infobip_username, 'password' => $request->infobip_password],
            'message_bird'  => ['api_key' => $request->message_bird_api_key],
            'nexmo'         => ['api_key' => $request->nexmo_api_key, 'api_secret' => $request->nexmo_api_secret],
            'sms_broadcast' => ['username' => $request->sms_broadcast_username, 'password' => $request->sms_broadcast_password],
            'twilio'        => ['account_sid' => $request->account_sid, 'auth_token' => $request->auth_token, 'from' => $request->from],
            'text_magic'    => ['username' => $request->text_magic_username, 'apiv2_key' => $request->apiv2_key],
            'custom'        => [
                'method'  => $request->custom_api_method,
                'url'     => $request->custom_api_url,
                'headers' => [
                    'name'  => $request->custom_header_name ?? [],
                    'value' => $request->custom_header_value ?? [],
                ],
                'body'    => [
                    'name'  => $request->custom_body_name ?? [],
                    'value' => $request->custom_body_value ?? [],
                ],
            ],
        ];

        $general             = gs();
        $general->sms_config = $data;
        $general->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'SMS settings updated successfully',
            'data'    => $general->sms_config,
        ]);
    }

    /**
     * Test SMS Notification
     */
    public function smsTest(Request $request)
    {
        $request->validate(['mobile' => 'required']);

        if (! gs('sn')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'SMS notification is disabled in general settings',
            ], 400);
        }

        try {
            $sendSms               = new Sms;
            $sendSms->mobile       = $request->mobile;
            $sendSms->receiverName = ' ';
            $sendSms->message      = 'Your SMS notification setting is configured successfully for ' . gs('site_name');
            $sendSms->subject      = ' ';
            $sendSms->send();

            if (session('sms_error')) {
                return response()->json([
                    'status'  => 'error',
                    'message' => session('sms_error'),
                ], 500);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'SMS sent to ' . $request->mobile . ' successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Push Notification Settings
     */
    public function pushSetting()
    {
        $fileExists = file_exists(getFilePath('pushConfig') . '/push_config.json');

        return response()->json([
            'status'  => 'success',
            'message' => 'Push settings fetched successfully',
            'data'    => [
                'firebase_config' => gs('firebase_config'),
                'file_exists'     => $fileExists,
            ],
        ]);
    }

    /**
     * Update Push Notification Settings
     */
    public function pushSettingUpdate(Request $request)
    {
        $request->validate([
            'apiKey'            => 'required',
            'authDomain'        => 'required',
            'projectId'         => 'required',
            'storageBucket'     => 'required',
            'messagingSenderId' => 'required',
            'appId'             => 'required',
            'measurementId'     => 'required',
        ]);

        $data = $request->only([
            'apiKey', 'authDomain', 'projectId',
            'storageBucket', 'messagingSenderId',
            'appId', 'measurementId',
        ]);

        $general                  = gs();
        $general->firebase_config = $data;
        $general->save();

        try {
            $jsPath = 'assets/firebase/configs.js';
            $config = "var firebaseConfig = " . json_encode(gs('firebase_config'));
            file_put_contents($jsPath, $config);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error writing Firebase config: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Firebase settings updated successfully',
            'data'    => $general->firebase_config,
        ]);
    }

    /**
     * Upload Firebase Config File
     */
    public function pushSettingUpload(Request $request)
    {
        $request->validate([
            'file' => ['required', new FileTypeValidate(['json'])],
        ]);

        try {
            fileUploader($request->file, getFilePath('pushConfig'), filename: 'push_config.json');
            return response()->json([
                'status'  => 'success',
                'message' => 'Configuration file uploaded successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Could not upload file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download Firebase Config File
     */
    public function pushSettingDownload()
    {
        $filePath = getFilePath('pushConfig') . '/push_config.json';

        if (! file_exists($filePath)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Configuration file not found',
            ], 404);
        }

        return response()->download($filePath);
    }

}
