@extends('layouts.alerj')

@section('title', 'Incluir Proposta Legislativa')

@section('content')

    <div class="">

        @include('partials.error')

        <div class="panel panel-default">
            <div class="panel-heading text-center">Propor Ideia Legislativa</div>
            <div class="panel-body">
                <p>
                    Tão importante quanto escolher, pelo voto, um representante na Assembléia Legislativa é participar propondo ideias,
                    posições e iniciativas ao processo parlamentar em favor da coletividade.
                </p>

                <h3 class="text-center">Envie-nos sua proposta!</h3>

                <br><br>

                {{ Form::open(array('route' => 'proposal.store', 'class' => 'form')) }}

                <div class="form-group coluna_02">
                    {{ Form::label('Nome da ideia legislativa', null , ['class' => 'bold double']) }}
                    {{ Form::text('name', null,
                        array('required',
                              'class'=>'campo',
                              'placeholder'=>'Nome')) }}
                </div>

                <br>

                <div class="form-group">
                    {{ Form::label('Problema a ser resolvido', null , ['class' => 'bold double']) }}
                    {{ Form::textarea('problem', null,
                        array('required',
                              'class'=>'textarea',
                              'placeholder'=>'Descreva o problema que deseja resolver com esta ideia')) }}
                </div>

                <br>

                <div class="form-group">
                    {{ Form::label('Ideia central', null , ['class' => 'bold double']) }}
                    {{ Form::textarea('idea_central', null,
                        array('required',
                              'class'=>'textarea',
                              'placeholder'=>'Descreva sua ideia resumidamente, use apenas uma frase se puder')) }}
                </div>

                <br>

                <div class="form-group">
                    {{ Form::label('Exposição completa da ideia', null , ['class' => 'bold double']) }}
                    {{ Form::textarea('idea_exposition', null,
                        array('required',
                              'class'=>'textarea',
                              'placeholder'=>'Descreva de forma mais completa a sua ideia. Dê todos os detalhes que achar necessário')) }}
                </div>

                <br>

                <div class="form-group">
                    {{ Form::submit('Enviar proposta de ideia',
                      array('class'=>'btn btn-primary botao')) }}
                </div>

                {{ Form::close() }}

            </div>
        </div>
    </div>

@stop
