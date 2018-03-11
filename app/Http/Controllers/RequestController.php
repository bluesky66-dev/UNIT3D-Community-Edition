<?php
/**
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     Mr.G
 */

namespace App\Http\Controllers;

use App\BonTransactions;
use App\Category;
use App\Type;
use App\Requests;
use App\RequestsBounty;
use App\RequestsClaims;
use App\Torrent;
use App\Shoutbox;
use App\User;
use App\PrivateMessage;
use App\Helpers\RequestViewHelper;
use App\Repositories\RequestFacetedRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request as IlluminateRequest;
use App\Achievements\UserFilled25Requests;
use App\Achievements\UserFilled50Requests;
use App\Achievements\UserFilled75Requests;
use App\Achievements\UserFilled100Requests;
use Carbon\Carbon;
use Decoda\Decoda;
use \Toastr;
use Cache;

class RequestController extends Controller
{
    /**
     * @var RequestFacetedRepository
     */
    private $repository;

    public function __construct(RequestFacetedRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Torrent Requests
     *
     *
     * @access public
     * @return view::make requests.requests
     */
    public function requests()
    {
        $user = Auth::user();
        $num_req = Requests::count();
        $num_fil = Requests::whereNotNull('filled_by')->count();
        $num_unfil = Requests::whereNull('filled_by')->count();
        $total_bounty = Requests::all()->sum('bounty');
        $claimed_bounty = Requests::whereNotNull('filled_by')->sum('bounty');
        $unclaimed_bounty = Requests::whereNull('filled_by')->sum('bounty');

        $requests = Requests::query();
        $repository = $this->repository;

        return view('requests.requests', ['requests' => $requests, 'repository' => $repository, 'user' => $user, 'num_req' => $num_req, 'num_fil' => $num_fil, 'num_unfil' => $num_unfil, 'total_bounty' => $total_bounty, 'claimed_bounty' => $claimed_bounty, 'unclaimed_bounty' => $unclaimed_bounty]);
    }

    public function faceted(IlluminateRequest $request, Requests $requests)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $imdb = $request->get('imdb');
        $tvdb = $request->get('tvdb');
        $tmdb = $request->get('tmdb');
        $mal = $request->get('mal');
        $categories = $request->get('categories');
        $types = $request->get('types');

        $terms = explode(' ', $search);
        $search = '';
        foreach ($terms as $term) {
            $search .= '%' . $term . '%';
        }

        $requests = $requests->newQuery();

        if ($request->has('search') && $request->get('search') != null) {
            $requests->where('name', 'like', $search);
        }

        if ($request->has('imdb') && $request->get('imdb') != null) {
            $requests->where('imdb', $imdb);
        }

        if ($request->has('tvdb') && $request->get('tvdb') != null) {
            $requests->where('tvdb', $tvdb);
        }

        if ($request->has('tmdb') && $request->get('tmdb') != null) {
            $requests->where('tmdb', $tmdb);
        }

        if ($request->has('mal') && $request->get('mal') != null) {
            $requests->where('mal', $mal);
        }

        if ($request->has('categories') && $request->get('categories') != null) {
            $requests->whereIn('category_id', $categories);
        }

        if ($request->has('types') && $request->get('types') != null) {
            $requests->whereIn('type', $types);
        }

        // pagination query starts
        $rows = $requests->count();

        if($request->has('page')){
            $page = $request->get('page');
            $qty = $request->get('qty');
            $requests->skip(($page-1)*$qty);
            $active = $page;
        }else{
            $active = 1;
        }

        if($request->has('qty')){
            $qty = $request->get('qty');
            $requests->take($qty);
        }else{
            $qty = 6;
            $requests->take($qty);
        }
        // pagination query ends

        if($request->has('sorting')){
            $sorting = $request->get('sorting');
            $order = $request->get('direction');
            $requests->orderBy($sorting,$order);
        }

        $listings = $requests->get();

        $helper = new RequestViewHelper();
        $result = $helper->view($listings);

        return ['result'=>$result,'rows'=>$rows,'qty'=>$qty,'active'=>$active];
    }

    /**
     * Torrent Request
     *
     *
     * @access public
     * @return view::make requests.request
     */
    public function request($id)
    {
        // Find the torrent in the database
        $request = Requests::findOrFail($id);
        $user = Auth::user();
        $requestClaim = RequestsClaims::where('request_id', '=', $id)->first();
        $voters = $request->requestBounty()->get();
        $comments = $request->comments()->orderBy('created_at', 'DESC')->paginate(6);
        $carbon = Carbon::now()->addDay();
        $client = new \App\Services\MovieScrapper(config('api-keys.tmdb'), config('api-keys.tvdb'), config('api-keys.omdb'));
        if ($request->category_id == 2) {
            if ($request->tmdb || $request->tmdb != 0) {
            $movie = $client->scrape('tv', null, $request->tmdb);
            } else {
            $movie = $client->scrape('tv', 'tt'. $request->imdb);
            }
        } else {
            if ($request->tmdb || $request->tmdb != 0) {
            $movie = $client->scrape('movie', null, $request->tmdb);
            } else {
            $movie = $client->scrape('movie', 'tt'. $request->imdb);
            }
        }

        return view('requests.request', ['request' => $request, 'voters' => $voters, 'user' => $user, 'comments' => $comments, 'carbon' => $carbon, 'movie' => $movie, 'requestClaim' => $requestClaim]);
    }

    /**
     * Add Torrent Request
     *
     *
     * @access public
     * @return Redirect::to
     */
    public function addrequest()
    {
        $user = Auth::user();
        // Post the Request
        if (Request::isMethod('post')) {
            // Validator
            $v = Validator::make(Request::all(), [
                "name" => "required|max:180",
                "imdb" => "required|numeric",
                "tvdb" => "required|numeric",
                "tmdb" => "required|numeric",
                "mal" => "required|numeric",
                "category_id" => "required|exists:categories,id",
                "type" => "required",
                "description" => "required|string",
                "bounty" => "required|numeric|min:0|max:{$user->seedbonus}"
            ]);

            if ($v->passes()) {
                // Find the right category
                $category = Category::findOrFail(Request::get('category_id'));

                // Holders for new data
                $requests = new Requests([
                    'name' => Request::get('name'),
                    'description' => Request::get('description'),
                    'category_id' => $category->id,
                    'user_id' => $user->id,
                    'imdb' => Request::get('imdb'),
                    'tvdb' => Request::get('tvdb'),
                    'tmdb' => Request::get('tmdb'),
                    'mal' => Request::get('mal'),
                    'type' => Request::get('type'),
                    'bounty' => Request::get('bounty'),
                    'votes' => 1,
                ]);
                $requests->save();

                $requestsBounty = new RequestsBounty([
                    'user_id' => $user->id,
                    'seedbonus' => Request::get('bounty'),
                    'requests_id' => $requests->id,
                ]);
                $requestsBounty->save();

                $BonTransactions = new BonTransactions([
                    'itemID' => 0,
                    'name' => 'request',
                    'cost' => Request::get('bounty'),
                    'sender' => $user->id,
                    'receiver' => 0,
                    'comment' => "new request - " . Request::get('name') . ""
                ]);
                $BonTransactions->save();

                $user->seedbonus -= Request::get('bounty');
                $user->save();

                $appurl = config('app.url');
                Shoutbox::create(['user' => "1", 'mentions' => "1", 'message' => "User [url={$appurl}/" . $user->username . "." . $user->id . "]" . $user->username . "[/url] has created a new request [url={$appurl}/request/" . $requests->id . "]" . $requests->name . "[/url]"]);
                Cache::forget('shoutbox_messages');

                return redirect('/requests')->with(Toastr::success('Request Added.', 'Yay!', ['options']));
            } else {
                return redirect('/requests')->with(Toastr::error('Not all the required information was provided, please try again.', 'Whoops!', ['options']));
            }
        } else {
            if ($user->seedbonus >= 100) {
                return view('requests.add_request', ['categories' => Category::all(), 'types' => Type::all()->sortBy('position'), 'user' => $user]);
            } else {
                return redirect('/requests')->with(Toastr::error('You dont have the minium of 100 BON to make a request!', 'Whoops!', ['options']));
            }
        }
    }

    /**
     * Edit Torrent Request
     *
     *
     * @access public
     * @return Redirect::to
     */
    public function editrequest($id)
    {
        $user = Auth::user();
        $request = Requests::findOrFail($id);
        if ($user->group->is_modo || $user->id == $request->user_id) {
            // Post the Request
            if (Request::isMethod('post')) {
                // Find the right category
                $name = Request::get('name');
                $imdb = Request::get('imdb');
                $tvdb = Request::get('tvdb');
                $tmdb = Request::get('tmdb');
                $mal = Request::get('mal');
                $category = Request::get('category_id');
                $type = Request::get('type');
                $description = Request::get('description');

                $request->name = $name;
                $request->imdb = $imdb;
                $request->tvdb = $tvdb;
                $request->tmdb = $tmdb;
                $request->mal = $mal;
                $request->category_id = $category;
                $request->type = $type;
                $request->description = $description;
                $request->save();

                return redirect()->route('requests', ['id' => $request->id])->with(Toastr::success('Request Edited Successfuly.', 'Yay!', ['options']));
            } else {
                return view('requests.edit_request', ['categories' => Category::all(), 'types' => Type::all(), 'user' => $user, 'request' => $request]);
            }
        } else {
            return redirect()->route('requests', ['id' => $request->id])->with(Toastr::error('You Dont Have Access To This Operation!', 'Whoops!', ['options']));
        }
    }

    /**
     * Add Bounty to a request
     *
     * @access public
     * @return Redirect::route
     */
    public function addBonus($id)
    {
        $user = Auth::user();

        if (Request::isMethod('POST') && $user->seedbonus >= 100) {
            $v = Validator::make(Request::all(), [
                'request_id' => "required|exists:requests,id",
                'bonus_value' => "required|numeric|min:100|max:{$user->seedbonus}",
            ]);

            if ($v->passes()) {
                $requests = Requests::findOrFail(Request::get('request_id'));

                $requests->votes += 1;
                $requests->bounty += Request::get('bonus_value');
                $requests->created_at = Carbon::now();
                $requests->save();

                $requestsBounty = new RequestsBounty([
                    'user_id' => $user->id,
                    'seedbonus' => Request::get('bonus_value'),
                    'requests_id' => $requests->id,
                ]);
                $requestsBounty->save();

                $BonTransactions = new BonTransactions([
                    'itemID' => 0,
                    'name' => 'request',
                    'cost' => Request::get('bonus_value'),
                    'sender' => $user->id,
                    'receiver' => 0,
                    'comment' => "adding bonus to {$requests->name}"
                ]);
                $BonTransactions->save();

                $user->seedbonus -= Request::get('bonus_value');
                $user->save();

                $appurl = config('app.url');
                Shoutbox::create(['user' => "1", 'mentions' => "1", 'message' => "User [url={$appurl}/" . $user->username . "." . $user->id . "]" . $user->username . "[/url] has addded " . Request::get('bonus_value') . " BON bounty to request " . "[url={$appurl}/request/" . $requests->id . "]" . $requests->name . "[/url]"]);
                Cache::forget('shoutbox_messages');
                PrivateMessage::create(['sender_id' => "1", 'reciever_id' => $requests->user_id, 'subject' => "Your Request " . $requests->name . " Has A New Bounty!", 'message' => $user->username . " Has Added A Bounty To " . "[url={$appurl}/request/" . $requests->id . "]" . $requests->name . "[/url]"]);

                return redirect()->route('request', ['id' => Request::get('request_id')])->with(Toastr::success('Your bonus has been successfully added.', 'Yay!', ['options']));
            } else {
                return redirect()->route('request', ['id' => Request::get('request_id')])->with(Toastr::error('You failed to adhere to the requirements.', 'Whoops!', ['options']));
            }
        } else {
            return redirect()->route('request', ['id' => Request::get('request_id')])->with(Toastr::error('The server doesnt unserstand your request.', 'Whoops!', ['options']));
        }
        return redirect()->route('request', ['id' => Request::get('request_id')])->with(Toastr::error('Something went horribly wrong', 'Whoops!', ['options']));
    }

    /**
     * Fill a request
     * @method fillRequest
     *
     * @param $id ID of the request
     *
     */
    public function fillRequest($id)
    {
        $user = Auth::user();

        if (Request::isMethod('POST')) {
            $v = Validator::make(Request::all(), [
                'request_id' => "required|exists:requests,id",
                'info_hash' => "required|exists:torrents,info_hash",
            ]);

            if ($v->passes()) {
                $torrent = Torrent::where('info_hash', '=', Request::get('info_hash'))->firstOrFail();

                if ($user->id == $torrent->user_id) {
                    $this->addRequestModeration(Request::get('request_id'), Request::get('info_hash'));

                    return redirect()->route('request', ['id' => Request::get('request_id')])->with(Toastr::success('Your request fill is pending approval by the Requestor.', 'Yay!', ['options']));
                } elseif ($user->id != $torrent->user_id && Carbon::now()->addDay() > $torrent->created_at) {
                    $this->addRequestModeration(Request::get('request_id'), Request::get('info_hash'));

                    return redirect()->route('request', ['id' => Request::get('request_id')])->with(Toastr::success('Your request fill is pending approval by the Requestor.', 'Yay!', ['options']));
                } else {
                    return redirect()->route('request', ['id' => Request::get('request_id')])->with(Toastr::error('You cannot fill this request for some weird reason', 'Whoops!', ['options']));
                }
            } else {
                return redirect()->route('request', ['id' => Request::get('request_id')])->with(Toastr::error('You failed to adhere to the requirements.', 'Whoops!', ['options']));
            }
        } else {
            return redirect()->route('request', ['id' => Request::get('request_id')])->with(Toastr::error('The server doesnt understand your request.', 'Whoops!', ['options']));
        }
    }

    /**
     * Function that handles the actual filling of requests
     * @method addRequestModeration
     *
     * @param $request_id ID of the Request being handled
     * @param $info_hash Hash of the torrent filling the hash
     */
    public function addRequestModeration($request_id, $info_hash)
    {
        $user = Auth::user();

        $request = Requests::findOrFail($request_id);

        $request->filled_by = $user->id;
        $request->filled_hash = $info_hash;
        $request->filled_when = Carbon::now();

        $request->save();

        $appurl = config('app.url');
        PrivateMessage::create(['sender_id' => "1", 'reciever_id' => $request->user_id, 'subject' => "Your Request " . $request->name . " Has Been Filled!", 'message' => $request->filled_by . " Has Filled Your Request [url={$appurl}/request/" . $request->id . "]" . $request->name . "[/url]" . " Please Approve or Decline The FullFill! "]);
    }

    /**
     * Approves the filled torrent on a request
     * @method approveRequest
     *
     */
    public function approveRequest($id)
    {
        $user = Auth::user();

        $request = Requests::findOrFail($id);

        if ($user->id == $request->user_id || Auth::user()->group->is_modo) {
            $request->approved_by = $user->id;
            $request->approved_when = Carbon::now();
            $request->save();

            //BON and torrent request hash code below
            $fill_user = User::findOrFail($request->filled_by);
            $fill_amount = $request->bounty;

            $BonTransactions = new BonTransactions([
                'itemID' => 0,
                'name' => 'request',
                'cost' => $fill_amount,
                'sender' => 0,
                'receiver' => $fill_user->id,
                'comment' => "{$fill_user->username} has filled {$request->name} and has been awared {$fill_amount} BONUS."
            ]);
            $BonTransactions->save();

            $fill_user->seedbonus += $fill_amount;
            $fill_user->save();
            //End BON code here.

            // Achievements
            $fill_user->addProgress(new UserFilled25Requests(), 1);
            $fill_user->addProgress(new UserFilled50Requests(), 1);
            $fill_user->addProgress(new UserFilled75Requests(), 1);
            $fill_user->addProgress(new UserFilled100Requests(), 1);

            $appurl = config('app.url');
            Shoutbox::create(['user' => "1", 'mentions' => "1", 'message' => "User [url={$appurl}/" . $fill_user->username . "." . $fill_user->id . "]" . $fill_user->username . "[/url] has filled [url={$appurl}/request/" . $request->id . "]" . $request->name . "[/url] and was awarded " . $fill_amount . " BON "]);
            Cache::forget('shoutbox_messages');
            PrivateMessage::create(['sender_id' => "1", 'reciever_id' => $request->filled_by, 'subject' => "Your Request Fullfill On " . $request->name . " Has Been Approved!", 'message' => $request->approved_by . " Has Approved Your Fullfillment On [url={$appurl}/request/" . $request->id . "]" . $request->name . "[/url] Enjoy The " . $request->bounty . " Bonus Points!"]);
            return redirect()->route('request', ['id' => $id])->with(Toastr::success("You have approved {$request->name} and the bounty has been awarded to {$fill_user->username}", "Yay!", ['options']));
        } else {
            return redirect()->route('request', ['id' => $id])->with(Toastr::error("You don't have access to approve this request", 'Whoops!', ['options']));
        }
    }

    /**
     * Rejects the filling torrent on a request
     * @method rejectRequest
     *
     */
    public function rejectRequest($id)
    {
        $user = Auth::user();

        $request = Requests::findOrFail($id);

        if ($user->id == $request->user_id) {
            PrivateMessage::create(['sender_id' => "1", 'reciever_id' => $request->filled_by, 'subject' => "Your Request Fullfill On " . $request->name . " Has Been Declined!", 'message' => $user->username . " Has Declined Your Fullfillment On [url={$appurl}/request/" . $request->id . "]" . $request->name . "[/url] It did not meet the requirements!"]);

            $request->filled_by = null;
            $request->filled_when = null;
            $request->filled_hash = null;

            $request->save();

            return redirect()->route('request', ['id' => $id])->with(Toastr::success("This request has been reset.", 'Yay!', ['options']));
        } else {
            return redirect()->route('request', ['id' => $id])->with(Toastr::success("You don't have access to approve this request", 'Yay!', ['options']));
        }
    }

    /**
     * Deletes a specific request
     * @method deleteRequest
     *
     */
    public function deleteRequest($id)
    {
        $user = Auth::user();
        $request = Requests::findOrFail($id);

        if ($user->group->is_modo || $request->user_id == $user->id) {
            $name = $request->name;
            $request->delete();

            return redirect()->route('requests')->with(Toastr::success("You have deleted {$name}", 'Yay!', ['options']));
        } else {
            return redirect()->route('request', ['id' => $id])->with(Toastr::error("You don't have access to delete this request.", 'Whoops!', ['options']));
        }
    }

    /**
     * User can claim a specific request
     * @method claimRequest
     *
     */
    public function claimRequest($id)
    {
        $user = Auth::user();
        $request = Requests::findOrFail($id);

        if ($request->claimed == null) {
            $requestClaim = new RequestsClaims([
                'request_id' => $id,
                'username' => $user->username,
                'anon' => Request::get('anon'),
            ]);
            $requestClaim->save();

            $request->claimed = 1;
            $request->save();

            return redirect()->route('request', ['id' => $id])->with(Toastr::success("Request Successfuly Claimed", 'Yay!', ['options']));
        } else {
            return redirect()->route('request', ['id' => $id])->with(Toastr::error("Someone else has already claimed this request buddy.", 'Whoops!', ['options']));
        }
    }

    /**
     * User can claim a specific request
     * @method claimRequest
     *
     */
    public function unclaimRequest($id)
    {
        $user = Auth::user();
        $request = Requests::findOrFail($id);
        $claimer = RequestsClaims::where('request_id', '=', $id)->first();

        if ($user->group->is_modo || $user->username == $claimer->username) {
            if ($request->claimed == 1) {
                $requestClaim = RequestsClaims::where('request_id', '=', $id)->firstOrFail();
                $requestClaim->delete();

                $request->claimed = null;
                $request->save();

                return redirect()->route('request', ['id' => $id])->with(Toastr::success("Request Successfuly Un-Claimed", 'Yay!', ['options']));
            } else {
                return redirect()->route('request', ['id' => $id])->with(Toastr::error("Nothing To Unclaim.", 'Whoops!', ['options']));
            }
        } else {
            abort(403, 'Unauthorized action.');
        }
    }
}
