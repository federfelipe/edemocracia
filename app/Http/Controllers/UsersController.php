<?php
/**
 * Created by PhpStorm.
 * User: falbernaz
 * Date: 18/02/2016
 * Time: 17:02.
 */

namespace App\Http\Controllers;

use App\Proposal;
use App\Repositories\ProposalsRepository;
use App\User;

class UsersController extends Controller
{
    private $proposalsRepository;

    /* 10/08/07
    public function __construct(ProposalsRepository $proposals)
    {
        $this->proposalsRepository = $proposals;
    }*/

    /*
     * Display a list of all user's proposals.
     *
     * @param Request $request
     *
     * @return Response
     */
    /* 10/08/17
     * public function proposals($user_id)
    {
        return view('users.proposals', [
            'proposals' => $this->proposalsRepository->forUser($user_id),
        ]);
    }*/

    /*
     * Display a list of all of the admin user's proposal responses.
     *
     * @param Request $request
     *
     * @return Response
     */

    /* 10/08/17
    public function responses($responder_id)
    {
        return view('users.responses', [
            'proposals' => $this->proposalsRepository->getResponsesForUser($responder_id),
        ]);
    }*/
}
