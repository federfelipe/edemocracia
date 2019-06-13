<?php
/**
 * Created by PhpStorm.
 * User: falbernaz
 * Date: 26/01/2016
 * Time: 13:59.
 */

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;

class AboutController extends Controller
{
    public function index()
    {
        return view('about.about');
    }

    /* relationship commissions */
    public function committee()
    {
        return view('about.committee');
    }

    public function terms()
    {
        return view('about.terms');
    }

    public function create()
    {
        return view('about.contact');
    }

    //    public function store(ContactFormRequest $request)
    //    {
    //        $msg = 'Obrigado por entrar em contato com a e-democracia da ALERJ. Você receberá uma cópia de sua mensagem e retornaremos o seu contato em breve!';
    //
    //        return \Redirect::route('contact')
    //            ->with('message', $msg);
    //
    //    }

    public function store(ContactFormRequest $request)
    {
        $msg = 'Obrigado por entrar em contato com a e-democracia da ALERJ. Você receberá uma cópia de sua mensagem e retornaremos o seu contato em breve!';

        \Mail::send('emails.contact',
            [
                'name'         => $request->get('name'),
                'email'        => $request->get('email'),
                'user_message' => $request->get('message'),
            ], function ($message) {
                $message->from('wj@wjgilmore.com');
                $message->to('wj@wjgilmore.com', 'Admin')->subject('TODOParrot Feedback');
            });

        return \Redirect::route('contact')->with('message', $msg);
    }
}
