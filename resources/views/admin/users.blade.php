<div class="container-fluid">
    <div class="row page-title align-items-center">
        <div class="col-sm-4 col-xl-6">
            <h4 class="mb-1 mt-0">Users</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="datatable" class="table dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Created at</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\User::get() as $user)
                                <tr onclick="redirect('/admin/user/{{ $user->_id }}')" style="cursor: pointer">
                                    <td><img alt src="{{ $user->avatar }}" style="width: 32px; height: 32px; margin-right: 5px;"> {{ $user->name }}</td>
                                    <td>{{ $user->created_at->format('d.m.Y H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
