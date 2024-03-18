<?php

namespace App\Support;

use File;

class LangCleanup
{
    static $remove = '{"* * * ...":"* * * ...","/data/userPinList":"/data/userPinList","/data/userPinnedDataTotal":"/data/userPinnedDataTotal","/pinning/pinFileToIPFS":"/pinning/pinFileToIPFS","/pinning/pinJobs":"/pinning/pinJobs","/pinning/removePinFromIPFS":"/pinning/removePinFromIPFS","/user/confirmed-two-factor-authentication":"/user/confirmed-two-factor-authentication","/user/two-factor-authentication":"/user/two-factor-authentication","/user/two-factor-qr-code":"/user/two-factor-qr-code","/user/two-factor-recovery-codes":"/user/two-factor-recovery-codes","/user/two-factor-secret-key":"/user/two-factor-secret-key","00":"00","01":"01","2d":"2d","[data-clear]":"[data-clear]","[data-tags]":"[data-tags]","\\n":"\\n","admin.amms.create":"admin.amms.create","admin.amms.edit":"admin.amms.edit","admin.amms.index":"admin.amms.index","admin.bets.index":"admin.bets.index","admin.coins.index":"admin.coins.index","admin.dashboard":"admin.dashboard","admin.settings":"admin.settings","admin.users.index":"admin.users.index","mail::button":"mail::button","mail::footer":"mail::footer","mail::header":"mail::header","mail::layout":"mail::layout","mail::message":"mail::message","mail::subcopy":"mail::subcopy","soccer_fixtures_{$leagueId}_valid":"soccer_fixtures_{$leagueId}_valid","soccer_fixtures{$leagueId}_valid":"soccer_fixtures{$leagueId}_valid","soccer_leagues_valid":"soccer_leagues_valid","soccer_{$leagueId}_teams_valid":"soccer_{$leagueId}_teams_valid","https://galxe.com/credential/265396831468036096":"https://galxe.com/credential/265396831468036096","sports.bookies":"sports.bookies","sports.index":"sports.index","sports.validate":"sports.validate"}';

    public static function cleanUp()
    {
        $lang = json_decode(File::get(lang_path('en.json')), true);
        $remove = json_decode(static::$remove, true);
        $clean = array_diff($lang, $remove);
        File::put(lang_path('en.json'), json_encode($clean));
    }
}
