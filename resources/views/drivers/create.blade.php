<x-app-layout>
    <x-slot name="title">Ajouter un chauffeur</x-slot>

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
    <form action="{{ route('drivers.store') }}" method="POST">
        @include('drivers.form')
    </form>
</x-app-layout>
