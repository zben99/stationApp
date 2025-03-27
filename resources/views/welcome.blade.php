




<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bienvenue - GSS Manager</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      background: linear-gradient(to right, #f8f9fa, #e9ecef);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: #343a40;
    }

    .container {
      background: #ffffff;
      padding: 3rem;
      border-radius: 2rem;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      text-align: center;
      max-width: 500px;
      width: 90%;
    }

    .container h1 {
      font-size: 2.5rem;
      margin-bottom: 1rem;
    }

    .container p {
      font-size: 1.1rem;
      margin-bottom: 2rem;
      color: #6c757d;
    }

    .buttons {
      display: flex;
      justify-content: center;
      gap: 1rem;
    }

    .buttons a {
      text-decoration: none;
      background-color: #007bff;
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 999px;
      font-weight: 600;
      transition: background-color 0.3s;
    }

    .buttons a:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Bienvenue sur GSS Manager</h1>
    <p>Votre solution de gestion simplifiée pour le suivi et la performance</p>
    <ul class="mb-8 text-left space-y-3">
        <li class="flex items-center">
            <svg class="w-5 h-5 mr-2 text-orange-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11H9v4h2V7zm0 6H9v2h2v-2z"/></svg>
            Gestion des ventes et du stock en temps réel.
        </li>
        <li class="flex items-center">
            <svg class="w-5 h-5 mr-2 text-orange-600" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h14a1 1 0 010 2H3a1 1 0 01-1-1z"/></svg>
            Suivi des livraisons et de la consommation.
        </li>
        <li class="flex items-center">
            <svg class="w-5 h-5 mr-2 text-orange-600" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2h14V5a2 2 0 00-2-2H5zm0 4H3v8a2 2 0 002 2h10a2 2 0 002-2V7H5z"/></svg>
            Rapports automatiques pour un meilleur pilotage.
        </li>
    </ul>
    <br>
    <div class="buttons">
      <a href="{{ route('login') }}">Connexion</a>
    </div>
    <br><br>
    <footer class="mt-12 text-sm text-gray-500 dark:text-gray-400">
        &copy; {{ date('Y') }} GSS Manager. Tous droits réservés.
    </footer>
  </div>
</body>
</html>
