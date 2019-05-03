<?php

/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 *
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Show All Notifications.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(25);

        return view('notification.index', ['notifications' => $notifications]);
    }

    /**
     * Uses Input's To Put Together A Search.
     *
     * @param \Illuminate\Http\Request $request
     * @param Notification  $notification
     *
     * @return array
     */
    public function faceted(Request $request, Notification $notification)
    {
        $user = $request->user();

        $notification = $user->notifications();

        if ($request->has('bon_gifts') && $request->input('bon_gifts') != null) {
            $notification->where('type', '=', 'App\Notifications\NewBon');
        }

        if ($request->has('comments') && $request->input('comments') != null) {
            $notification->where('type', '=', 'App\Notifications\NewComment');
        }

        if ($request->has('comment_tags') && $request->input('comment_tags') != null) {
            $notification->where('type', '=', 'App\Notifications\NewCommentTag');
        }

        if ($request->has('followers') && $request->input('followers') != null) {
            $notification->where('type', '=', 'App\Notifications\NewFollow');
        }

        if ($request->has('posts') && $request->input('posts') != null) {
            $notification->where('type', '=', 'App\Notifications\NewPost');
        }

        if ($request->has('post_tags') && $request->input('post_tags') != null) {
            $notification->where('type', '=', 'App\Notifications\NewPostTag');
        }

        if ($request->has('post_tips') && $request->input('post_tips') != null) {
            $notification->where('type', '=', 'App\Notifications\NewPostTip');
        }

        if ($request->has('request_bounties') && $request->input('request_bounties') != null) {
            $notification->where('type', '=', 'App\Notifications\NewRequestCounty');
        }

        if ($request->has('request_claims') && $request->input('request_claims') != null) {
            $notification->where('type', '=', 'App\Notifications\NewRequestClaim');
        }

        if ($request->has('request_fills') && $request->input('request_fills') != null) {
            $notification->where('type', '=', 'App\Notifications\NewRequestFill');
        }

        if ($request->has('request_approvals') && $request->input('request_approvals') != null) {
            $notification->where('type', '=', 'App\Notifications\NewRequestFillApprove');
        }

        if ($request->has('request_rejections') && $request->input('request_rejections') != null) {
            $notification->where('type', '=', 'App\Notifications\NewRequestFillReject');
        }

        if ($request->has('request_unclaims') && $request->input('request_unclaims') != null) {
            $notification->where('type', '=', 'App\Notifications\NewRequestUnclaim');
        }

        if ($request->has('reseed_requests') && $request->input('reseed_requests') != null) {
            $notification->where('type', '=', 'App\Notifications\NewReseedRequest');
        }

        if ($request->has('thanks') && $request->input('thanks') != null) {
            $notification->where('type', '=', 'App\Notifications\NewThank');
        }

        if ($request->has('upload_tips') && $request->input('upload_tips') != null) {
            $notification->where('type', '=', 'App\Notifications\NewUploadTip');
        }

        if ($request->has('topics') && $request->input('topics') != null) {
            $notification->where('type', '=', 'App\Notifications\NewTopic');
        }

        if ($request->has('unfollows') && $request->input('unfollows') != null) {
            $notification->where('type', '=', 'App\Notifications\NewUnfollowt');
        }

        if ($request->has('uploads') && $request->input('uploads') != null) {
            $notification->where('type', '=', 'App\Notifications\NewUpload');
        }

        $notifications = $notification->paginate(25);

        return view('notification.results', [
            'user'            => $user,
            'notifications' => $notifications,
        ])->render();
    }

    /**
     * Show A Notification And Mark As Read.
     *
     * @param \Illuminate\Http\Request $request
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function show(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect($notification->data['url'])
            ->withSuccess('Notification Marked As Read!');
    }

    /**
     * Set A Notification To Read.
     *
     * @param \Illuminate\Http\Request $request
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->user()->unreadNotifications()->findOrFail($id)->markAsRead();

        return redirect()->route('notifications.index')
            ->withSuccess('Notification Marked As Read!');
    }

    /**
     * Mass Update All Notification's To Read.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function updateAll(Request $request)
    {
        $current = new Carbon();
        $request->user()->unreadNotifications()->update(['read_at' => $current]);

        return redirect()->route('notifications.index')
            ->withSuccess('All Notifications Marked As Read!');
    }

    /**
     * Delete A Notification.
     *
     * @param \Illuminate\Http\Request $request
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id)
    {
        $request->user()->notifications()->findOrFail($id)->delete();

        return redirect()->route('notifications.index')
            ->withSuccess('Notification Deleted!');
    }

    /**
     * Mass Delete All Notification's.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function destroyAll(Request $request)
    {
        $request->user()->notifications()->delete();

        return redirect()->route('notifications.index')
            ->withSuccess('All Notifications Deleted!');
    }
}
