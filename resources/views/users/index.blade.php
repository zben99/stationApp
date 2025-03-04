<x-app-layout>


    <x-slot name="header">

        Gestion des utilisateurs

    </x-slot>


    @session('success')
        <div class="alert alert-success" role="alert">
            {{ $value }}
        </div>
    @endsession



                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="pull-right">
                                <a class="btn btn-success mb-2" href="{{ route('users.create') }}"><i class="fa fa-plus"></i> Créer un nouvel utilisateur</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Rôles</th>
                                            <th >Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($data as $key => $user)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                              @if(!empty($user->getRoleNames()))
                                                @foreach($user->getRoleNames() as $v)
                                                   <label class="badge bg-success">{{ $v }}</label>
                                                @endforeach
                                              @endif
                                            </td>
                                            <td>

                                                 <a class="btn btn-primary btn-sm" href="{{ route('users.edit',$user->id) }}"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                                                  <form method="POST" action="{{ route('users.destroy', $user->id) }}" style="display:inline">
                                                      @csrf
                                                      @method('DELETE')

                                                      <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i> Delete</button>
                                                  </form>
                                            </td>
                                        </tr>
                                     @endforeach


                                    </tbody>
                                </table>
                                {!! $data->links('pagination::bootstrap-5') !!}
                            </div>
                        </div>
                    </div>

    {!! $data->links('pagination::bootstrap-5') !!}

</x-app-layout>


