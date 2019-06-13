<?php
/**
 * Created by PhpStorm.
 * User: falbernaz
 * Date: 26/01/2016
 * Time: 13:59.
 */

namespace App\Http\Controllers;

use App\BillsProject;
use App\Events\ProposalApprovedByCommittee;
use App\Events\ProposalClosedByCommittee;
use App\Events\ProposalTimeLimit;
use App\Http\Requests\BillProjectFormRequest;
use App\Http\Requests\ProposalFormRequest;
use App\Http\Requests\ResponseFormRequest;
use App\Proposal;
use App\ProposalHistory;
use App\Repositories\DataRepository;
use App\Repositories\ProposalsRepository;
use App\Repositories\UsersRepository;
use Auth;
use Carbon\Carbon;
use Gate;
use Illuminate\Support\Facades\Input;

class AdminController extends Controller
{
    /**
     * @var ProposalsRepository
     */
    private $proposalsRepository;
    private $usersRepository;
    private $dataRepository;

    public function __construct(ProposalsRepository $proposalsRepository, UsersRepository $usersRepository, DataRepository $dataRepository)
    {
        $this->proposalsRepository = $proposalsRepository;
        $this->usersRepository = $usersRepository;
        $this->dataRepository = $dataRepository;
    }

    public function index()
    {
        return view('admin.index');
    }

    public function users()
    {
        $users = $this->usersRepository->all(Input::get('q'));

        return view('admin.users.index')
            ->with(compact('users'));
    }

    public function showUser($id)
    {
        //Get User
        $user = $this->usersRepository->find($id);

        return view('admin.users.show')
            ->with(compact('user'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return Response
     */
    public function createUser()
    {
        $states = $this->dataRepository->getStates();
        //State::pluck('nome', 'uf');
        $roles = $this->dataRepository->getRoles();
        //Role::pluck('role', 'id');

        return view('admin.users.create')->with(compact('states'))->with(compact('roles'));
        //return view('admin.users.create')->with('states', $states)->with('roles', $roles);
    }

    /**
     * Store a newly created user in storage.
     *
     * @return Response
     */
    public function storeUser()
    {
        $this->usersRepository->storeUser();

        return redirect()->route('admin.users')->with('admin_user_crud_msg', 'Usuário Incluído com Sucesso');
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param int $id
     *
     * @return Response
     */
    public function editUser($id)
    {
        $user = $this->usersRepository->find($id);
        $roles = $this->dataRepository->getRoles();

        return view('admin.users.edit')->with(compact('user'))->with(compact('roles'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function updateUser($id)
    {
        $user = $this->usersRepository->find($id);

        $user->name = Input::get('name');
        $user->email = Input::get('email');
        $user->role_id = Input::get('role_id');

        $user->save();

        // Generating Redirects...
        return redirect()->route('admin.users.show', ['id' => $id])->with('admin_user_crud_msg', 'Usuário Editado com Sucesso');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroyUser($id)
    {
        $this->usersRepository->destroy($id);

        return redirect()->route('admin.users')->with('admin_user_crud_msg', 'Usuário Removido com Sucesso');
    }

    /**
     * Lists all proposals in Admin.
     *
     * @return Response
     */
    public function proposals()
    {
        $proposals = $this->proposalsRepository->all();

        return view('admin.proposals.index')->with(compact('proposals'));
    }

    /**
     * Show the specified proposal from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function showProposal($id)
    {
        $proposal = $this->proposalsRepository->find($id);

        //return view('admin.proposals.show', ['proposal' => $proposal]);
        //return view('admin.proposals.show')->with('proposal', $proposal);
        return view('admin.proposals.show')->with(compact('proposal'));
    }

    /**
     * Create the specified proposal form.
     *
     * @param int $id
     *
     * @return Response
     */
    public function createProposal()
    {
        return view('admin.proposals.create');
    }

    /**
     * Store the specified proposal in storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function storeProposal(ProposalFormRequest $formRequest)
    {
        $input = $formRequest->except('_token');

        $input['user_id'] = Auth::user()->id;
        $input['open'] = true;
        $input['pub_date'] = Carbon::now();
        $input['limit_date'] = Carbon::now();

        $proposal = Proposal::create($input);
        //event(new ProposalWasCreated($proposal));
        //Event::fire(new ProposalWasCreated($proposal));

        return redirect()->route('admin.proposal.show', ['proposal' => $proposal])->with('admin_proposal_crud_msg', 'Ideia Legislativa Incluída com Sucesso');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function editProposal($id)
    {
        //Get Proposal
        $proposal = $this->proposalsRepository->find($id);
        //$proposal = Proposal::findOrFail($id);

        //Via User Model
        //        if ($user->can('update', $post)) {
        //            //
        //        }

        if (Gate::allows('edit', $proposal)) {
            return view('admin.proposals.edit')->with('proposal', $proposal);
        } else {
            return redirect()->route('admin.proposals')->with('admin_error_msg', 'Você não é o dono desta Ideia Legislativa');
        }
    }

    /**
     * Show the form for editing the specified BillProject resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function billProject($id)
    {
        //Get Proposal
        $proposal = $this->proposalsRepository->find($id);

        if (Gate::allows('edit', $proposal)) {
            return view('admin.proposals.bill-project')->with('proposal', $proposal);
        } else {
            return redirect()->route('admin.proposals')->with('admin_error_msg', 'Você não tem direitos para realizar essa operação');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function updateProposal($id, ProposalFormRequest $formRequest)
    {
        $proposal = $this->proposalsRepository->find($id);

        $input = $formRequest->except('_token');

        $input['user_id'] = Auth::user()->id;
        $input['open'] = true;
        //        $input['pub_date'] = Carbon::now();
        //        $input['limit_date'] = Carbon::now();

        //Create ProposalHistory Object
        $proposal_history = new ProposalHistory();
        //Get attributes from Proposals Eloquent

        $proposal_history->setRawAttributes(array_except($proposal->getAttributes(), ['id', 'created_at', 'updated_at']));
        //dd($proposal_history);
        //Append Update Info
        $proposal_history->proposal_id = $id;
        $proposal_history->update_id = Auth::user()->id;
        $proposal_history->update_date = Carbon::now();
        //Save History
        $proposal_history->save();

        //Then update Proposal
        $proposal->fill($input)->save();

        return redirect()->route('admin.proposal.show', ['id' => $id])->with('admin_proposal_crud_msg', 'Ideia Legislativa Editada com Sucesso');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function updateBillProject($id, BillProjectFormRequest $formRequest)
    {
        //Get Proposal
        $proposal = $this->proposalsRepository->find($id);

        //Create BillProject Object
        $bill_project = new BillsProject();

        // Receive form input
        $input = $formRequest->except('_token');

        //Fill more data into input
        //$input['date'] = Carbon::now();

        //Update Bills_Project Table
        $bill_project->fill($input)->save();
        //Update Proposals Table
        $proposal->bill_project_id = $bill_project->id;
        $proposal->open = false;

        //Save Proposal
        $proposal->save();

        return redirect()->route('admin.proposal.approvedByCommittee', ['id' => $id])->with('admin_proposal_crud_msg', 'Ideia Legislativa transformada em Projeto de Lei com Sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroyProposal($id)
    {
        $proposal = $this->proposalsRepository->find($id);
        //$proposal = Proposal::findOrFail($id);

        if (Gate::allows('destroy', $proposal)) {
            $proposal->delete();

            return redirect()->route('admin.proposals')->with('admin_proposal_crud_msg', 'Ideia Legislativa Removida com Sucesso');
        } else {
            return redirect()->route('admin.proposals')->with('admin_error_msg', 'Você não é o dono desta Ideia Legislativa');
        }
    }

    /**
     * First Moderation: Approving Proposals to get them published and listed on frontend.
     *
     * @param int $id
     *
     * @return Response
     */
    public function approvedProposal($id)
    {
        //Append Moderation Info only if never been Moderated before
        if ($proposal = $this->proposalsRepository->publish($id)) {
            //return redirect()->route('admin.proposal.show', ['id' => $id])->with('admin_proposal_crud_msg', 'Ideia Legislativa Aprovada com Sucesso');
            return redirect()->route('admin.proposals')->with('admin_proposal_crud_msg', 'Ideia Legislativa Aprovada com Sucesso');
        } else {
            return redirect()->back()->with('admin_error_msg', 'Ideia Legislativa já foi Moderada!');
        }
    }

    /**
     * First Moderation: Disapproving Proposals to get them rid off frontend.
     *
     * @param int $id
     *
     * @return Response
     */
    public function disapprovedProposal($id, ResponseFormRequest $formRequest)
    {
        $proposal = $this->proposalsRepository->find($id);

        $input = $formRequest->except('_token', '_method', '_wysihtml5_mode');

        //Append Moderation Info only if never been Moderated before
        if ($proposal->approved_at == null && $proposal->approved_by == null && $proposal->disapproved_at == null && $proposal->disapproved_by == null) {
            $proposal->response = $input['response'];
            $proposal->response_id = Auth::user()->id;

            $proposal->disapproved_at = Carbon::now();
            $proposal->disapproved_by = Auth::user()->id;

            // Close
            $proposal->open = false;
            // and Save
            //$proposal->forcefill($input)->save();
            $proposal->save();

            //Fire Event
            event(new ProposalClosed($proposal));

            //return redirect()->back()->with(compact('proposal'))->with('admin_proposal_crud_msg', 'Ideia Legislativa Desaprovada com Sucesso');
            //                return redirect()->route('admin.proposal.show', ['id' => $id])->with(compact('proposal'))->with('admin_proposal_crud_msg', 'Ideia Legislativa Desaprovada e Respondida com Sucesso.');
            return redirect()->route('admin.proposals')->with(compact('proposal'))->with('admin_proposal_crud_msg', 'Ideia Legislativa Desaprovada e Respondida com Sucesso.');
        } else {
            return redirect()->back()->with(compact('proposal'))->with('admin_error_msg', 'Ideia Legislativa já foi Moderada!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function response($id)
    {
        //Get Proposal
        $proposal = $this->proposalsRepository->find($id);
        //$proposal = Proposal::findOrFail($id);

        return view('admin.proposals.response')->with('proposal', $proposal);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function updateResponse($id, ResponseFormRequest $formRequest)
    {
        $proposal = $this->proposalsRepository->find($id);
        //$proposal = Proposal::findOrFail($id);

        //Disapproval
        if (Input::get('disapprovalBtn')) {
            $button = 'disapprovalBtn';

            $input = $formRequest->except('_token', '_method', '_wysihtml5_mode', $button);

            $input['responder_id'] = Auth::user()->id;

            $proposal->disapproved_at = Carbon::now();
            $proposal->disapproved_by = Auth::user()->id;

            //Create ProposalHistory Object
            $proposal_history = new ProposalHistory();
            //Get attributes from Proposals Eloquent
            $proposal_history->setRawAttributes(array_except($proposal->getAttributes(), ['id', 'created_at', 'updated_at']));

            //Append Update Info + Response
            $proposal_history->proposal_id = $id;
            $proposal_history->update_id = Auth::user()->id;
            $proposal_history->update_date = Carbon::now();
            $proposal_history->response = $input['response'];
            $proposal_history->responder_id = $input['responder_id'];
            //$proposal_history->fill($input);
            //dd($proposal_history);

            //Save History
            $proposal_history->save();

            //Then update Proposal
            $proposal->forcefill($input)->save();
            // dd($proposal);
            //            return redirect()->route('admin.proposal.show', ['id' => $id])->with('admin_proposal_crud_msg', 'Ideia Legislativa Desaprovada e Respondida com Sucesso');
            return redirect()->route('admin.proposals')->with('admin_proposal_crud_msg', 'Ideia Legislativa Desaprovada e Respondida com Sucesso');
        }
        //Approval
        else {
            return $this->approvedProposal($id);
        }
    }

    public function toCommittee($id)
    {
        $this->proposalsRepository->toCommittee($id);

        return redirect()->route('admin.proposals.approvalGoal')->with('admin_proposal_crud_msg', 'Ideia Legislativa enviada ao Comitê com Sucesso');
    }

    public function bypass($id)
    {
        $this->proposalsRepository->publish($id);
        $this->proposalsRepository->setApprovalGoal($id);
        $this->proposalsRepository->toCommittee($id);

        return redirect()->route('admin.proposals.inCommittee')->with('admin_proposal_crud_msg', 'Ideia Legislativa enviada ao Comitê com Sucesso');
    }

    /**
     * Committee Moderation: Approving Proposal when it reached approval Goal and forward to respective Committee.
     *
     * @param int $id
     *
     * @return Response
     */
    public function approvedProposalByCommittee($id)
    {
        $proposal = $this->proposalsRepository->find($id);

        //Append Committee Moderation Info only if never been Moderated by Committee before
        if ($proposal->approved_at_committee == null && $proposal->approved_by_committee == null && $proposal->disapproved_at_committee == null && $proposal->disapproved_by_committee == null) {
            $proposal->approved_at_committee = Carbon::now();
            $proposal->approved_by_committee = Auth::user()->id;
            //Save
            $proposal->save();

            //Fire Event
            event(new ProposalApprovedByCommittee($proposal));

            return redirect()->route('admin.proposals.inCommittee')->with('admin_proposal_crud_msg', 'Ideia Legislativa Aprovada pelo Comitê com Sucesso');
        } else {
            return redirect()->back()->with('admin_error_msg', 'Ideia Legislativa já foi Moderada pelo Comitê!');
        }
    }

    /**
     * Committee Moderation: Disapproving Proposals by Committee and Close Proposal.
     *
     * @param int $id
     *
     * @return Response
     */
    public function disapprovedProposalByCommittee($id)
    {
        $proposal = $this->proposalsRepository->find($id);

        //Append Moderation Info only if never been Moderated before
        if ($proposal->approved_at_committee == null && $proposal->approved_by_committee == null && $proposal->disapproved_at_committee == null && $proposal->disapproved_by_committee == null) {
            $proposal->disapproved_at_committee = Carbon::now();
            $proposal->disapproved_by_committee = Auth::user()->id;

            // Close
            $proposal->open = false;
            // and Save
            $proposal->save();

            //Fire Event
            event(new ProposalClosedByCommittee($proposal));

            return redirect()->route('admin.proposals.inCommittee')->with(compact('proposal'))->with('admin_proposal_crud_msg', 'Ideia Legislativa Desaprovada pelo Comitê e Finalizada com Sucesso.');
        } else {
            return redirect()->back()->with(compact('proposal'))->with('admin_error_msg', 'Ideia Legislativa já foi Moderada pelo Comitê!');
        }
    }

    /**
     * Time Limit Moderation: Time Limit expired Proposals.
     *
     * @param int $id
     *
     * @return Response
     */
    public function timeLimit($id)
    {
        $proposal = $this->proposalsRepository->find($id);

        //Append Time Limit Moderation Info only if Proposal lifetime has expired (time_limit = true)
        if ($proposal->time_limit == true) {
            $proposal->time_limit_at = Carbon::now();
            $proposal->time_limit_by = Auth::user()->id;

            // Close
            $proposal->open = false;
            //Save
            $proposal->save();

            //Fire Event
            event(new ProposalTimeLimit($proposal));

            return redirect()->route('admin.proposals')->with('admin_proposal_crud_msg', 'Ideia Legislativa Expirada e Finalizada com Sucesso.');
        }
    }

    /**
     * List: Not Responded Proposals.
     *
     * @param  void
     *
     * @return Response
     */
    public function notResponded()
    {
        $proposals = $this->proposalsRepository->notResponded();

        return view('admin.proposals.notresponded')->with('notrespondeds', $proposals);
    }

    /**
     * List: First Moderation Approved Proposals.
     *
     * @param  void
     *
     * @return Response
     */
    public function approved()
    {
        $proposals = $this->proposalsRepository->approved();

        return view('admin.proposals.approved')->with('approveds', $proposals);
    }

    /**
     * List: First Moderation Disapproved Proposals.
     *
     * @param  void
     *
     * @return Response
     */
    public function disapproved()
    {
        $proposals = $this->proposalsRepository->disapproved();

        return view('admin.proposals.disapproved')->with('disapproveds', $proposals);
    }

    /**
     * List: Time Expired Proposals.
     *
     * @param  void
     *
     * @return Response
     */
    public function expired()
    {
        $proposals = $this->proposalsRepository->expired();

        return view('admin.proposals.expired')->with('expired', $proposals);
    }

    /**
     * List: Approval Goal Reached.
     *
     * @param  void
     *
     * @return Response
     */
    public function approvalGoal()
    {
        $proposals = $this->proposalsRepository->approvalGoal();

        return view('admin.proposals.approval-goal')->with('approveds', $proposals);
    }

    /**
     * List: in Committee.
     *
     * @param  void
     *
     * @return Response
     */
    public function inCommittee()
    {
        $proposals = $this->proposalsRepository->inCommittee();

        return view('admin.proposals.in-committee')->with('inCommittee', $proposals);
    }

    /**
     * List: Approved Proposals by Committee.
     *
     * @param  void
     *
     * @return Response
     */
    public function approvedByCommittee()
    {
        $proposals = $this->proposalsRepository->approvedByCommittee();

        return view('admin.proposals.approved-by-committee')->with('approvedsByCommittee', $proposals);
    }

    /**
     * List: Disapproved Proposals by Committee.
     *
     * @param  void
     *
     * @return Response
     */
    public function disapprovedByCommittee()
    {
        $proposals = $this->proposalsRepository->disapprovedByCommittee();

        return view('admin.proposals.disapproved-by-committee')->with('disapprovedsByCommittee', $proposals);
    }
}
