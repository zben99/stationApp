<x-app-layout>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2>Associer un utilisateur à une station</h2>
            <a class="btn btn-danger btn-sm mb-2" href="{{ route('stations.index') }}">
                <i class="fa fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('stations.associate.store') }}">
        @csrf
        <div class="row">
            <div class="col-md-5">
                <div class="form-group">
                    <strong>Utilisateur :</strong>
                    <select name="user_id" class="form-control">
                        <option value="">Sélectionner un utilisateur</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-5">
                <div class="form-group">
                    <strong>Stations :</strong>
                    <select name="station_id[]" class="form-control" multiple>
                        @foreach($stations as $station)
                            <option value="{{ $station->id }}">{{ $station->name }} - {{ $station->location }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary mt-4"><i class="bi bi-link"></i> Associer</button>
            </div>
        </div>
    </form>

    <h3 class="mt-5">Utilisateurs associés à une station</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Station</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                @if($user->stations && $user->stations->isNotEmpty())
                    @foreach($user->stations as $station)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $station->name }}</td>
                            <td>
                                <form method="POST" action="{{ route('stations.associate.detach') }}">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    <input type="hidden" name="station_id" value="{{ $station->id }}">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-link-45deg"></i> Dissocier
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endforeach


        </tbody>
    </table>
</x-app-layout>
