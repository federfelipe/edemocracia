@extends('layouts.admin')

@section('title', config('app.name'))

<!-- Current Proposals -->
@section('content')

    @if (Session::has('flash_msg'))
        <div class="alert alert-info">{{ Session::get('flash_msg') }}</div>
    @endif

    @if (Session::has('error_msg'))
        <div class="alert alert-danger">{{ Session::get('error_msg') }}</div>
    @endif

    @if(Session::has('proposal_crud_msg'))
        <div class="alert alert-info">{{ Session::get('proposal_crud_msg') }}</div>
    @endif

    @include('includes.status')

        <!-- Main row -->
        <div class="row">

            <div class="col-md-12">

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Propostas Legislativas Desaprovadas pelo Comitê</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div id="example3_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">

                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="example3" class="table table-bordered table-striped table-hover compact dataTable" role="grid" aria-describedby="example3_info">
                                        <thead>
                                        <tr role="row">
                                            <th>Id</th>
                                            <th>Nome</th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        @foreach ($disapprovedsByCommittee as $disapprovedByCommittee)
                                            <tr>
                                                <td>{{ $disapprovedByCommittee->id }}</td>
                                                <td><a href="{{ route('admin.proposal.show',array('id'=>$disapprovedByCommittee->id)) }}">{{ $disapprovedByCommittee->name }}</a></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th>Id</th>
                                            <th>Nome</th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@stop
