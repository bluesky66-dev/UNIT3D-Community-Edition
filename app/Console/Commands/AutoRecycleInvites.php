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

namespace App\Console\Commands;

use App\Invite;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoRecycleInvites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:recycle_invites';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recycle Invites That Are Expired.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $current = Carbon::now();
        $invites = Invite::whereNull('accepted_by')->whereNull('accepted_at')->where('expires_on', '<', $current)->get();

        foreach ($invites as $invite) {
            $invite->delete();
        }
    }
}
