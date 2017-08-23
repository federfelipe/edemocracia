<?php

use App\Proposal;
use App\User;
use Faker\Factory;

//use Illuminate\Foundation\Testing\WithoutMiddleware;

class ProposalsTest extends TestCase
{
    /**
   * A basic functional test example.
   *
   * @return void
   */
  public function testRandomProposal()
  {
      //Use Eloquent
        $proposal = Proposal::all()->random();
      $this->seeInDatabase('proposals', ['name' => $proposal->name]);

        //Not use Eloquent
        $proposal = factory(App\Proposal::class)->create();

      $this->visit('/proposals/'.$proposal->id);
//         ->seeInDatabase('proposals', ['name' => $proposal->name]);

        //Not with Paginator
        //->see($proposal->name);
  }

    public function testPageProposal()
    {
        $proposal = Proposal::paginate(20)->shuffle()->first();
        $url_proposal = '/proposals/'.$proposal->id;
        $this->visit($url_proposal)
             ->see($proposal->name)
             ->see($proposal->central_idea)
             ->see($proposal->problem);
    }



    /* public function testCreateProposalUserNotloggedIn()     NÃO TERMINADO
    {
        $this->visit('/')
            ->click('Sua ideia legislativa')
            ->seeElement('.confirm');
        By default Laravel uses PHPUnit and PHPBrowser for testing. PHPBrowser does not support
        Javascript. If you want to test javascript you will need to use something like selenium and
    codeception. Codeception has a built in Laravel adapter so it is still super easy to use.
        I have used it in many applications and it works really well.

           <a href="http://local.edemocracia.com/proposals/create"
           onclick="if(!confirm('Para incluir nova ideia legislativa você deve estar logado')){return false;};">
    }*/

    /*
     public function testProposalResponse()  //InvalidArgumentException: Could not find a link with a body, name, or ID attribute of [Sem Resposta]
    {
        //use the factory to create a Faker\Generator instance
        $faker = Faker\Factory::create();

        //generate a 6 words proposal response
        $response = $faker->sentence($nbWords = 6, $variableNbWords = true);

        //get first admin user in DB
        $user = User::where('role_id', 0)->first();

        //authenticate the given user -> navigate through not responded menu -> fill proposal response -> see if it's in DB
        $this->actingAs($user)
            ->visit('/')
            ->click('Sem Resposta')
            ->seePageIs('/proposals/notresponded')
            ->click('Responder Proposta')
            ->see('Responder Proposta Legislativa')
            ->type($response, 'response')
            ->press('Responder')
            ->see('Ideia Legislativa Respondida com Sucesso')
            ->seeInDatabase('proposals', ['response' => $response]);
    }*/

    public function testProposalPaginates()      // isso serve para alguma coisa?!
    {
        factory(App\Proposal::class, 50)->create();
        $proposals = App\Proposal::paginate(20);
        $this->assertEquals(20, $proposals->count());
    }

    public function testCookie()
    {
        $this->call('GET', '/');
        $this->seeCookie('uuid', Cookie::get('uuid'));
    }

    /*
    public function testProposalsControllerIndex()     // resposta não é uma View
    {
        //Visit /proposals
        $response = $this->call('GET', 'proposals');

        $this->assertViewHas('proposals');
        // getData() returns all vars attached to the response.
        $proposals = $response->original->getData()['proposals'];

        // When calling the paginate method, you will receive an instance of Illuminate\Pagination\LengthAwarePaginator
        $this->assertInstanceOf('Illuminate\Pagination\LengthAwarePaginator', $proposals);
        //$this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $proposals);
    } */
}
