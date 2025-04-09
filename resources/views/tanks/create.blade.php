<x-app-layout>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Ajouter une Cuve </h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-danger btn-sm mb-2" href="{{ route('tanks.index') }}"><i class="fa fa-arrow-left"></i> Retour</a>
            </div>
        </div>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
          <strong>Whoops!</strong> Il y a eu quelques problèmes avec votre saisie.<br><br>
          <ul>
             @foreach ($errors->all() as $error)
               <li>{{ $error }}</li>
             @endforeach
          </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tanks.store') }}" method="POST">
                @include('tanks._form')
            </form>
        </div>
    </div>
</x-app-layout>