<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */
if (! function_exists('appurl')) {
    function appurl()
    {
        return config('app.url');
    }
}

if (! function_exists('hrefProfile')) {
    function href_profile($user)
    {
        $appurl = appurl();

        return sprintf('%s/users/%s', $appurl, $user->username);
    }
}

if (! function_exists('hrefArticle')) {
    function href_article($article)
    {
        $appurl = appurl();

        return sprintf('%s/articles/%s', $appurl, $article->id);
    }
}

if (! function_exists('hrefTorrent')) {
    function href_torrent($torrent)
    {
        $appurl = appurl();

        return sprintf('%s/torrents/%s', $appurl, $torrent->id);
    }
}

if (! function_exists('hrefRequest')) {
    function href_request($torrentRequest)
    {
        $appurl = appurl();

        return sprintf('%s/requests/%s', $appurl, $torrentRequest->id);
    }
}

if (! function_exists('hrefPoll')) {
    function href_poll($poll)
    {
        $appurl = appurl();

        return sprintf('%s/polls/%s', $appurl, $poll->id);
    }
}

if (! function_exists('hrefPlaylist')) {
    function href_playlist($playlist)
    {
        $appurl = appurl();

        return sprintf('%s/playlists/%s', $appurl, $playlist->id);
    }
}
