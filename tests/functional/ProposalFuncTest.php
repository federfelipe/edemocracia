<?php

use App\Proposal;
use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Socialite\Facades\Socialite;


class ProposalFuncTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateProposalMainButton()
    {
        $proposal = factory(App\Proposal::class)->create();
        $user = User::all()->random();

        $this->actingAs($user)
            ->visit('/')
            ->click('Sua ideia legislativa')
            ->seePageIs('/proposals/create')
            ->type($proposal->name, 'name')
            ->type($proposal->idea_central, 'idea_central')
            ->type($proposal->problem, 'problem')
            ->type($proposal->idea_exposition, 'idea_exposition')
            ->press('Incluir')
            ->see('Sucesso')
            ->seeInDatabase('proposals', ['name' => $proposal->name]);
    }

    public function testCreateProposalBySecondButton()
    {
        $proposal = factory(App\Proposal::class)->create();
        $user = User::all()->random();

        $this->actingAs($user)
            ->visit('/proposals/'.$proposal->id)
            ->click('novaIdeia')
            ->seePageIs('/proposals/create')
            ->type($proposal->name, 'name')
            ->type($proposal->idea_central, 'idea_central')
            ->type($proposal->problem, 'problem')
            ->type($proposal->idea_exposition, 'idea_exposition')
            ->press('Incluir')
            ->see('Ideia Legislativa Incluída');
    }

    public function testUserLikeSupportFollowProposal()
    {
        $proposal = Proposal::all()->random();
        $user = User::all()->random();

        $this->actingAs($user)
            ->visit('/proposals/'.$proposal->id)
            ->see($proposal->name)
            ->click('Curtir')
            ->see('Sua curtida foi computada com sucesso')
            ->click('Curtir')
            ->see('Você já curtiu')
            ->click('Descurtir')
            ->see('Você deixou de curtir')
            ->click('Curtir')
            ->see('Você voltou a curtir')
            ->click('Apoiar!')
            ->see('Seu apoio foi incluído')
            ->click('Apoiar!')
            ->see('Você já apoiou')
            ->click('Acompanhar essa Ideia!')
            ->see('Esta Ideia Legislativa será acompanhada');
    }

    public function testUserEditIdea()
    {
        $proposal = factory(App\Proposal::class)->create();
        $user = User::all()->random();

        $this->actingAs($user)
            ->visit('/proposals/'.$proposal->id)
            ->click('editar')      // criei 'id' para o teste; o phpunit, às vezes, identificava o texto e, às vezes, não.
            ->seePageIs('/proposals/'.$proposal->id.'/edit')
            ->type($proposal->name, 'name')
            ->type($proposal->problem, 'problem')
            ->press('Gravar')
            ->see('Ideia Legislativa Editada')
            ->seePageIs('/proposals/'.$proposal->id)
            ->click('voltar')
            ->seePageIs('/')
            ->seeInDatabase('proposals', ['name' => $proposal->name]);
    }
    public function testAdminModeratingProposalApprove() {
        $user = factory(App\User::class, 'admin')->create();
        $proposal = Proposal::all()->random();

        $this->actingAs($user)
            ->visit('/admin')
            ->click('Aguardando Publicação')
            ->visit('/admin/proposals/'.$proposal->id.'/response')
            ->see($proposal->idea_central)
            ->see($proposal->problem)
            ->type('RESPOSTA', 'response')
            ->press('Aprovar!')
            ->seePageIs('/admin/proposals');
    }

    public function testAdminModeratingProposalApproveWithoutResponse() {
        $user = factory(App\User::class, 'admin')->create();
        $proposal = Proposal::all()->random();

        $this->actingAs($user)
            ->visit('/admin')
            ->click('Aguardando Publicação')
            ->visit('/admin/proposals/'.$proposal->id.'/response')
            ->see($proposal->idea_central)
            ->see($proposal->problem)
            ->press('Aprovar!')
            ->seePageIs('/admin/proposals');
    }

    public function testAdminModeratingProposalDisapprove() {
        $user = factory(App\User::class, 'admin')->create();
        $proposal = Proposal::all()->random();

        /* unit não reconhece aviso após click */
        $this->actingAs($user)
            ->visit('/admin')
            ->click('Aguardando Publicação')
            ->visit('/admin/proposals/'.$proposal->id.'/response')
            ->see($proposal->idea_central)
            ->see($proposal->problem)
            ->type('RESPOSTA', 'response')
            ->press('Desaprovar')
            ->dontSee($proposal->idea_central)
            ->seePageIs('/admin/proposals');
     }

    public function testAdminModeratingPublishedProposalEditing() {
        $user = factory(App\User::class, 'admin')->create();
        $proposal = Proposal::whereNotNull('approved_by')
            ->orderBy('updated_at', 'desc')
            ->first();
        $proposalTextFake = Proposal::all()->random();
        $limitDate = \Carbon\Carbon::now()->addMonth(config('global.timeLimitMonth'));

        $this->actingAs($user)
            ->visit('/admin')
            ->click('Publicadas')
            ->seePageIs('/admin/proposals/approved')
            ->visit('/admin/proposals/'.$proposal->id)
            ->see('Esta Ideia Legislativa já foi Moderada')
            ->click('Editar Proposta')
            ->type($proposalTextFake->name,'name')
            ->type($proposalTextFake->idea_central,'idea_central')
            ->type($proposalTextFake->problem,'problem')
            ->type($proposalTextFake->idea_exposition,'idea_exposition')
            ->type($limitDate,'limit_date')
            ->press('Gravar')
            ->see('Ideia Legislativa Editada com Sucesso')
            ->seePageIs('/admin/proposals/'.$proposal->id);
    }

    public function testAdminModeratingPublishedProposalSendACommission() {
        $user = factory(App\User::class, 'admin')->create();
        $proposal = Proposal::whereNotNull('approved_by')
            ->orderBy('updated_at', 'asc')
            ->first();

        $this->actingAs($user)
            ->visit('/admin')
            ->click('Publicadas')
            ->seePageIs('/admin/proposals/approved')
            ->visit('/admin/proposals/'.$proposal->id)
            ->see('Esta Ideia Legislativa já foi Moderada')
            ->click('Enviar diretamente')
            ->seePageIs('/admin/proposals/in-committee')
            ->see($proposal->name);
    }

   /* public function testAdminModeratingDisapprovedProposal() {
        $user = factory(App\User::class, 'admin')->create();
        $proposal = Proposal::whereNotNull('disapproved_by')
        ->orderBy('updated_at', 'desc')
        ->first();

        $this->actingAs($user)
            ->visit('/admin')
            ->click('Desaprovadas')
            ->seePageIs('/admin/proposals/disapproved')
            ->visit('/admin/proposals/'.$proposal->id)
            ->see('Esta Ideia Legislativa já foi Moderada')
            ->click('Enviar diretamente')
            ->seePageIs('/admin/proposals/in-committee')
            ->see($proposal->name);   // Sistema não envia proposta Desaprovada para comissão, mas existe o botão lá
    }*/

    public function testAdminModeratingWaitingAnalysisInCommitteeApprove() {
        $user = factory(App\User::class, 'admin')->create();
        $proposal = Proposal::whereNotNull('approved_by')
                ->where('in_committee', true)
                ->whereNull('approved_by_committee')
                ->whereNull('disapproved_by_committee')
                ->orderBy('updated_at', 'desc')
                ->first();

        $this->actingAs($user)
            ->visit('/admin')
            ->click('Aguardando Análise')
            ->seePageIs('/admin/proposals/in-committee')
            ->click('Aprovar')
            ->dontSee($proposal->name)
            ->visit('/admin/proposals/approved-by-committee')
            ->see($proposal->name);
    }

    public function testAdminModeratingWaitingAnalysisInCommitteeDisapprove() {
        $user = factory(App\User::class, 'admin')->create();
        $proposal = Proposal::whereNotNull('approved_by')
            ->where('in_committee', true)
            ->whereNull('approved_by_committee')
            ->whereNull('disapproved_by_committee')
            ->orderBy('updated_at', 'desc')
            ->first();

        $this->actingAs($user)
            ->visit('/admin')
            ->click('Aguardando Análise')
            ->seePageIs('/admin/proposals/in-committee')
            ->click('Desaprovar')
            ->dontSee($proposal->name)
            ->visit('/admin/proposals/disapproved-by-committee')
            ->see($proposal->name);
    }

    public function testAdminModeratingApprovedByCommitteeSignBill() {
        $user = factory(App\User::class, 'admin')->create();
        /*$proposal = Proposal::whereNotNull('approved_by_committee')
            ->orderBy('updated_at', 'desc')
            ->first();*/

        $this->actingAs($user)
            ->visit('/admin')
            ->click('Aprovadas')
            ->seePageIs('/admin/proposals/approved-by-committee')
            ->click('Assignar Projeto de Lei')
           /* ->seePageIs('/admin/proposals/'." ".'/bill-project')*/
            ->see('Projeto de Lei')
            ->type('121', 'number')
            ->type('121', 'year')
            ->type('Deputado Responsável Júnior do PoLvo','owner')
            ->type('wwww.alerj.gov.br/projeto-de-lei.php','link')
            ->press('Gravar')
            ->see('121')
            ->see('wwww.alerj.gov.br/projeto-de-lei.php')
            ->see('Deputado Responsável Júnior do PoLvo');
    }


    public function testSocialLogin() {

        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser
            ->shouldReceive('getId')
            ->andReturn(rand())
            ->shouldReceive('getName')
            ->andReturn(str_random(10))
            ->shouldReceive('getEmail')
            ->andReturn(str_random(10) . '@gmail.com')
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage');

        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

        $this->visit('/auth/facebook/callback');

     }
}
