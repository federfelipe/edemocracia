<?php
/**
 * Created by PhpStorm.
 * User: falbernaz
 * Date: 18/01/2016
 * Time: 12:19.
 */
use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //15 Users and Proposals
        factory(App\User::class, 15)->create()->each(function ($user) {
            //echo "user: $user->id\n";

            foreach (range(1, rand(1, 10)) as $x) {
                $proposal = factory(App\Proposal::class)->create();

                foreach (range(1, rand(1, 20)) as $y) {
                    // Get random User
                    $user = User::all()->shuffle()->first();

                    // Approvals
                    $proposal->approvals()->attach($user->id);
                    //$proposal->likes()->attach($user->id);
                }

                //Likes
                factory(App\Like::class)->create();

                //Follows
                factory(App\ProposalFollow::class)->create();
            }
        });

        //Administrator
        factory(App\User::class, 'admin', 1)->create([
            'name'     => 'Adm',
            'email'    => 'adm@test.com',
            'password' => Hash::make('secret'),
        ]);
    }
}
