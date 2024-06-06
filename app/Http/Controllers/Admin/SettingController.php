<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Chain as ResourcesChain;

use App\Models\Chain;
use Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Storage;

class SettingController extends Controller
{

    public function dashboard(Request $request)
    {

        return Inertia::render('Admin/Dashboard');
    }

    public function settings(Request $request)
    {

        $mail = [
            'MAIL_MAILER' => config('mail.default'),
            'MAIL_HOST' => config('mail.mailers.smtp.host'),
            'MAIL_PORT' => config('mail.mailers.smtp.port'),
            'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
            'MAIL_PASSWORD' =>  config('mail.mailers.smtp.password'),
            'MAIL_ENCRYPTION' =>  config('mail.mailers.smtp.encryption'),
            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            'MAIL_FROM_NAME' => config('mail.from.name'),
            'MAILGUN_SECRET' => config('services.mailgun.secret'),
            'MAILGUN_DOMAIN' => config('services.mailgun.domain'),
            'POSTMARK_TOKEN' =>  config('services.postmark.token'),
            'AWS_ACCESS_KEY_ID' => config('services.ses.key'),
            'AWS_SECRET_ACCESS_KEY' => config('services.ses.secret'),
            'AWS_DEFAULT_REGION' => config('services.ses.region'),
        ];

        $general = [
            'LOGO' => config('app.logo'),
            'LOGO_PREVIEW' => Storage::disk('public')->url(config('app.logo')),
            'DESCRIPTION' => config('app.description'),
            'DB_HOST' => config('database.connections.mysql.host'),
            'DB_PORT' => config('database.connections.mysql.port'),
            'DB_DATABASE' => config('database.connections.mysql.database'),
            'DB_USERNAME' => config('database.connections.mysql.username'),
            'DB_PASSWORD' => config('database.connections.mysql.password'),
            'APP_DEBUG' => config('app.debug'),
            'APP_URL' => config('app.url'),
            'APP_NAME' => config('app.name'),
            'ADMIN' => config('app.admin'),
            'WALLETCONNET_PROJECT_ID' => config('app.walletConnectProjectId'),
            'EIP712' => config('app.eip712'),
        ];

        $notification = [
            'BEAMS_INSTANCE_ID' => config('app.beams_instance_id'),
            'BEAMS_SECRET_KEY' => config('app.beams_secret_key'),
            'TELEGRAM_BOT_TOKEN' => config('app.telegram_bot_token'),
            'TELEGRAM_CHANNEL' => config('app.telegram_channel'),
        ];

        $services = [
            'ANKR_KEY' => config('services.ankr.apiKey'),
            'COINLAYER_APIKEY' => config('services.coinlayer.api_key'),
            'MORALIS_API_KEY' => config('services.moralis.apiKey'),
            'PINATA_API_KEY' => config('services.pinata.api_key'),
            'PINATA_API_SECRET' => config('services.pinata.secret_key'),
        ];

        $chainsList = ResourcesChain::collection(Chain::all());
        return Inertia::render('Admin/Settings', compact('mail', 'chainsList', 'general', 'notification', 'services'));
    }

    public function toggleChain(Request $request, $chainId)
    {
        $chain  = Chain::findOrFail($chainId);
        $chain->active = !$chain->active;
        $chain->save();
        return back();
    }

    public function update(Request $request)
    {

        $keys = [
            'MAIL_MAILER' => config('mail.default'),
            'MAIL_HOST' => config('mail.mailers.smtp.host'),
            'MAIL_PORT' => config('mail.mailers.smtp.port'),
            'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
            'MAIL_PASSWORD' =>  config('mail.mailers.smtp.password'),
            'MAIL_ENCRYPTION' =>  config('mail.mailers.smtp.encryption'),
            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            'MAIL_FROM_NAME' => config('mail.from.name'),
            'MAILGUN_SECRET' => config('services.mailgun.secret'),
            'MAILGUN_DOMAIN' => config('services.mailgun.domain'),
            'POSTMARK_TOKEN' =>  config('services.postmark.token'),
            'AWS_ACCESS_KEY_ID' => config('services.ses.key'),
            'AWS_SECRET_ACCESS_KEY' => config('services.ses.secret'),
            'AWS_DEFAULT_REGION' => config('services.ses.region'),
            'LOGO' => config('app.logo'),
            'DESCRIPTION' => config('app.description'),
            'DB_HOST' => config('database.connections.mysql.host'),
            'DB_PORT' => config('database.connections.mysql.port'),
            'DB_DATABASE' => config('database.connections.mysql.database'),
            'DB_USERNAME' => config('database.connections.mysql.username'),
            'DB_PASSWORD' => config('database.connections.mysql.password'),
            'APP_DEBUG' => config('app.debug'),
            'APP_URL' => config('app.url'),
            'APP_NAME' => config('app.name'),
            'ADMIN' => config('app.admin'),
            'WALLETCONNET_PROJECT_ID' => config('app.walletConnectProjectId'),
            'BEAMS_INSTANCE_ID' => config('app.beams_instance_id'),
            'BEAMS_SECRET_KEY' => config('app.beams_secret_key'),
            'TELEGRAM_BOT_TOKEN' => config('app.telegram_bot_token'),
            'TELEGRAM_CHANNEL' => config('app.telegram_channel'),
            'EIP712' => config('app.eip712'),
            'ANKR_KEY' => config('services.ankr.apiKey'),
            'COINLAYER_APIKEY' => config('services.coinlayer.api_key'),
            'MORALIS_API_KEY' => config('services.moralis.apiKey'),
            'PINATA_API_KEY' => config('services.pinata.api_key'),
            'PINATA_API_SECRET' => config('services.pinata.secret_key'),
        ];
        $editor = (object)[]; //DotenvEditor::load();
        foreach ($keys as $key => $default) {
            if ($request->has($key)) {
                if ($key === 'LOGO') {
                    if ($request->input($key) !=  config('app.logo')) {
                        $serverId = $request->input($key);
                        $filepond = app(\Sopamo\LaravelFilepond\Filepond::class);
                        $path = $filepond->getPathFromServerId($serverId);
                        $disk = config('filepond.temporary_files_disk');
                        $fullpath = Storage::disk($disk)->path($path);
                        $uploadedFile = 'logos/' . File::hash($fullpath) . '.' . File::guessExtension($fullpath);
                        File::move($fullpath, Storage::disk('public')->path($uploadedFile));
                        $editor->setKey('LOGO', $uploadedFile);
                    }
                    continue;
                }
                $editor->setKey($key, $request->input($key, $default));
            }
        }
        $editor->save();
        Artisan::call('optimize');
    }
}
