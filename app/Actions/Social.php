<?php

namespace App\Actions;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Str;

class Social
{
    public function update(Request $request, Model $socailable)
    {
        foreach ([
            'youtube',
            'website',
            'docs',
            'whitepaper',
            'telegram',
            'linktree',
            'snapchat',
            'twitter',
            'tiktok',
            'github',
            'discord',
            'facebook',
            'instagram',
            'medium',
            'reddit',
        ] as $link) {
            if (!$request->filled($link)) continue;
            $socailable->socials()->updateOrCreate([
                'network' => $link,
            ], [
                'link' => static::getLink($request->input($link), $link),
                'code' => static::uniqidReal(),
            ]);
        }
    }
    // converts usernames to links
    private static function getLink($link, $network)
    {
        $networks = [
            'telegram' => 't.me/{u}',
            'twitter' => ['twitter.com/{u}', 'x.com/@{u}', 'x.com/{u}', 'twitter.com/@{u}'],
            'tiktok' => 'tiktok.com/{u}',
            'github' => 'github.com/{u}',
            'whatsapp' => 'wa.me/{u}',
            'youtube' => 'youtube.com/@{u}',
            'discord' => 'discord.gg/{u}',
            'twitch' => 'twitch.tv/{u}',
            'facebook' => ['fb.me/{u}', 'facebook.com/{u}'],
            'behance' => 'behance.net/{u}',
            'blogger' => '{u}.blogspot.com',
            'buffer' => "{u}.buffer.com",
            'codepen' => 'codepen.io/{u}',
            'dribbble' => 'dribbble.com/{u}',
            'flickr' => 'flickr.com/photos/{u}',
            'instagram' => 'instagram.com/{u}',
            'linkedin' => 'linkedin.com/in/{u}',
            'medium' => 'medium.com/@{u}',
            'messenger' => ['fb.me/{u}', 'facebook.com/{u}'],
            'pinterest' => 'pinterest.com/{u}',
            'reddit' => ['reddit.com/r/{u}', 'reddit.com/user/{u}'],
            'skype' => 'join.skype.com/invite/{u}',
            'slack' => '{u}.slack.com',
            'snapchat' => 'snapchat.com/add/{u}',
            'stackoverflow' => 'stackoverflow.com/{u}',
            'trello' => 'trello.com/{u}',
            'tumblr' => 'tumblr.com/{u}',
            'vimeo' => 'vimeo.com/{u}',
            'yelp' => 'yelp.com/biz/{u}',
        ];
        $urlstr = str($link)->replace(['https://', 'https://'], '');
        $site =  $networks[$network] ?? null;
        if (!$site) return $link;
        $search = is_array($site)
            ? collect($site)->map(fn ($s) => str($s)->replace(['/{u}', '/@{u}', '{u}.'], '')->value())->all()
            : str($site)->replace(['/{u}', '/@{u}', '{u}.'], '')->value();
        if (!$urlstr->contains($search, true)) { //user sent username only
            $name = $urlstr->replace('@', '');
            $urlstr = str(is_array($site) ? $site[0] : $site)->replace('{u}', $name);
        }
        $socialLink = $urlstr->prepend('https://');
        return $socialLink->value();
    }

    private static function uniqidReal($length = 13)
    {
        if (function_exists("openssl_random_pseudo_bytes"))
            return substr(bin2hex(openssl_random_pseudo_bytes(ceil($length / 2))), 0, $length);
        if (function_exists("random_bytes"))
            return substr(bin2hex(random_bytes(ceil($length / 2))), 0, $length);
        return Str::random($length);
    }
}
