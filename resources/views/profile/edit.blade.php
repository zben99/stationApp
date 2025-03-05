<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Informations du Profil -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section class="container mt-5">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Informations du Profil') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Mettez à jour les informations de votre profil et votre adresse e-mail.") }}
                            </p>
                        </header>

                        <!-- Formulaire de mise à jour du profil -->
                        <form method="post" action="{{ route('profile.update') }}" class="mt-4">
                            @csrf
                            @method('patch')

                            <!-- Champ Nom -->
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('Nom') }}</label>
                                <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Champ E-mail -->
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('E-mail') }}</label>
                                <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-800">
                                            {{ __('Votre adresse e-mail n\'est pas vérifiée.') }}
                                            <button form="send-verification" class="btn btn-link p-0 text-sm text-gray-600 hover:text-gray-900 rounded-md">
                                                {{ __('Cliquez ici pour renvoyer l\'email de vérification.') }}
                                            </button>
                                        </p>

                                        @if (session('status') === 'verification-link-sent')
                                            <p class="mt-2 font-medium text-sm text-green-600">
                                                {{ __('Un nouveau lien de vérification a été envoyé à votre adresse e-mail.') }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Section bouton de sauvegarde -->
                            <div class="d-flex justify-content-start gap-4">
                                <button type="submit" class="btn btn-primary">{{ __('Sauvegarder') }}</button>

                                @if (session('status') === 'profile-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-success"
                                    >{{ __('Enregistré.') }}</p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <!-- Mise à jour du mot de passe -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section class="container mt-5">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Mettre à jour le mot de passe') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé.') }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('password.update') }}" class="mt-6">
                            @csrf
                            @method('put')

                            <!-- Champ Mot de Passe Actuel -->
                            <div class="mb-3">
                                <label for="update_password_current_password" class="form-label">{{ __('Mot de passe actuel') }}</label>
                                <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password" required>
                                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                            </div>

                            <!-- Champ Nouveau Mot de Passe -->
                            <div class="mb-3">
                                <label for="update_password_password" class="form-label">{{ __('Nouveau mot de passe') }}</label>
                                <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password" required>
                                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                            </div>

                            <!-- Champ Confirmer le Mot de Passe -->
                            <div class="mb-3">
                                <label for="update_password_password_confirmation" class="form-label">{{ __('Confirmer le mot de passe') }}</label>
                                <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password" required>
                                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                            </div>

                            <!-- Section bouton de sauvegarde -->
                            <div class="d-flex justify-content-start gap-4">
                                <button type="submit" class="btn btn-primary">{{ __('Sauvegarder') }}</button>

                                @if (session('status') === 'password-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-success"
                                    >{{ __('Enregistré.') }}</p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <!-- Suppression du Compte -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section class="space-y-6">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Supprimer le compte') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées. Avant de supprimer votre compte, veuillez télécharger toutes les données ou informations que vous souhaitez conserver.') }}
                            </p>
                        </header>

                        <button class="btn btn-danger" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">{{ __('Supprimer le compte') }}</button>

                        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                            <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                                @csrf
                                @method('delete')

                                <h2 class="text-lg font-medium text-gray-900">
                                    {{ __('Êtes-vous sûr de vouloir supprimer votre compte ?') }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-600">
                                    {{ __('Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées. Veuillez entrer votre mot de passe pour confirmer que vous souhaitez supprimer définitivement votre compte.') }}
                                </p>

                                <div class="mt-6">
                                    <label for="password" class="form-label sr-only">{{ __('Mot de passe') }}</label>
                                    <input id="password" name="password" type="password" class="form-control" placeholder="{{ __('Mot de passe') }}" required>
                                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                                </div>

                                <div class="mt-6 d-flex justify-content-end">
                                    <button type="button" class="btn btn-secondary" x-on:click="$dispatch('close')">{{ __('Annuler') }}</button>
                                    <button type="submit" class="btn btn-danger ms-3">{{ __('Supprimer le compte') }}</button>
                                </div>
                            </form>
                        </x-modal>
                    </section>
                </div>
            </div>

        </div>
    </div>


</x-app-layout>
