

<x-guest-layout>




    <div class="row justify-content-center">

        <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">


                                <form class="user" method="POST" action="{{ route('password.store') }}">
                                        @csrf

                                        <!-- Password Reset Token -->
                                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                        <div class="form-group">
                                            <input type="email" name="email" value="{{ old('email', request()->email) }}"
                                            required autofocus autocomplete="username"
                                            class="form-control form-control-user"
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
                                            <input type="password" class="form-control form-control-user" id="password_confirmation"
                                            type="password"
                                            name="password_confirmation"
                                            required autocomplete="new-password" placeholder="Confirmez le mot de passe">

                                            @error('password_confirmation')
                                                <div class="error">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        {{ __('Réinitialiser le mot de passe') }}
                                    </button>
                                </form>
                                <hr>

                                <div class="text-center">
                                    <a class="small" href="{{route('login')}}">Vous avez déjà un compte ? Connectez-vous !</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>



</x-guest-layout>
