<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />


 <!-- Outer Row -->
 <div class="row justify-content-center">

    <div class="col-xl-10 col-lg-12 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                    <div class="col-lg-6">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Content de te revoir!</h1>
                            </div>
                            <form class="user" method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="email" name="email" :value="old('email')" required autofocus autocomplete="username" class="form-control form-control-user"
                                        id="email" aria-describedby="emailHelp"
                                        placeholder="Entrez l'adresse e-mail">

                                        @error('email')
                                            <div class="error">{{ $message }}</div>
                                        @enderror
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control form-control-user" id="password"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password" placeholder="Mot de passe">

                                    @error('password')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="customCheck" name="remember">
                                        <label class="custom-control-label" for="customCheck">Souviens-toi de moi</label>
                                    </div>
                                </div>

                                <button class="btn btn-primary btn-user btn-block">
                                    Se connecter
                                </button>

                            </form>
                            <hr>
                            @if (Route::has('password.request'))
                                <div class="text-center">
                                    <a class="small" href="{{Route('password.request')}}">Mot de passe oublié?</a>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>









</x-guest-layout>




