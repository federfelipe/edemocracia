
<!-- Fixed navbar -->
<nav class="navbar navbar-default">
    <div class="container">

        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            {{ Html::image(('img/alerj.png'), null, array( 'width' => 70, 'height' => 70 )) }}
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <a class="navbar-brand" href="/">e-Cidadania</a>
            <ul class="nav navbar-nav">
                <li class="active">{{ Html::linkRoute('proposals', 'Home')}}</li>
                <li>{{ Html::linkRoute('about', 'Como Funciona?')}}</li>
                <li><a href="#contact">Contato</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Outros Serviços <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        @if (Auth::guest())
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li role="separator" class="divider"></li>
                        <li class="dropdown-header">Nav header</li>
                        <li><a href="#">Separated link</a></li>
                        <li><a href="#">One more separated link</a></li>
                        @else
                            <a href="{{ route('proposal.create') }}">Incluir Nova Proposta</a>
                            <a href="{{ route('users.proposals', Auth::user()->id) }}">Listar Minhas Propostas</a>
                        @endif
                    </ul>


                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="nav navbar-nav navbar-right">
                <!-- Authentication Links -->

                @if (Auth::guest())
                    <li><a href="{{ url('/login') }}">Login | Registro</a></li>
                    {{--<li><a href="{{ url('/register') }}">Registro</a></li>--}}
                @else
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>