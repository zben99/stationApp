<x-app-layout>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="card shadow">
                    <div class="card-header text-center">
                        <h4>Choisissez une station-service</h4>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('station.select') }}">
                            @csrf

                            <div class="form-group">
                                <label for="station_id">Station</label>
                                <select name="station_id" id="station_id" class="form-control" required>
                                    <option value="" disabled selected>-- Sélectionner une station --</option>
                                    @foreach ($stations as $station)
                                        <option value="{{ $station->id }}">{{ $station->name }} {{ $station->location }}</option>
                                    @endforeach
                                </select>
                                @error('station_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary mt-3 w-100">
                                Accéder à la station
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
