<?php

class HomeFunctionalTest extends TestCase
{


    public function testHome()
    {
        $this->visit('/')
                ->click('INÍCIO')
            ->seePageIs('/');
    }

   /**
     * @expectedException NotFoundHttpException
     * @expectedExceptionCode 404
     */
   /* public function testPageNotFoundException()
    {
        //throw new NotFoundHttpException('Some Message', 20);
        $this->visit('/page/does/not/exits')
             ->expectException(NotFoundHttpException::class);
        // ->seePageIs('');

    }*/
}
