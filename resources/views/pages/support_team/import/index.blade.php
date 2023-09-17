@extends('layouts.master')
@section('page_title', 'Import')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Import</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#import-students" class="nav-link active" data-toggle="tab">Import Students</a></li>
        </ul>

        <div class="tab-content">

            <div class="tab-pane fade show active" id="import-students">
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center alert alert-info border-0 alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>

                            <span> <a target="_blank" href="{{ URL::to('/').'/import/sample/users.csv' }}">Download</a> Sample Students file.</span>
                        </div>

                        @if(session()->has('success'))
                        <div class="alert alert-success">
                            {!! session()->get('success') !!}
                        </div>
                        @endif
                        <form action="{{route('import-data.store')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row">
                                <div class="col-12">
                                    <label for="text">Upload file</label>
                                    <input id="csv_file" name="csv_file" type="file" class="form-control">
                                    <input id="type" name="type" type="hidden" value="user" class="form-control">

                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12">
                                    <button name="submit" type="submit" class="btn btn-primary">Import</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>

            </div>

        </div>
    </div>
</div>


@endsection