<x-app-layout>
    <x-slot name="title">Modifier un chauffeur</x-slot>


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
    <form action="{{ route('drivers.update', $driver) }}" method="POST">
        @method('PUT')
        @include('drivers.form', ['driver' => $driver])
    </form>
</x-app-layout>
