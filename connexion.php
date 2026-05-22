<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusCare</title>

    <!-- CSS -->
    <link rel="stylesheet" href="style.css">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

    <!-- NAVBAR -->

    <nav class="navbar navbar-expand-lg navbar-dark">

        <div class="container">

            <a class="navbar-brand" href="#">
                Campus<span>Care</span>
            </a>

            <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#menu">

                <span class="navbar-toggler-icon"></span>

            </button>

            <div class="collapse navbar-collapse" id="menu">

                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link active" href="#">Accueil</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#">Services</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#">Médecins</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#">Rendez-vous</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#">Connexion</a>
                    </li>

                </ul>

            </div>

        </div>

    </nav>

    <!-- HERO SECTION -->

    <section class="hero">

        <div class="hero-content">

            <h1>Bienvenue sur CampusCare</h1>

            <p>
                Système intelligent de gestion d’un centre
                de santé universitaire
            </p>

            <div class="buttons">

                <button class="btn-main">
                    <i class="fa-solid fa-calendar-check"></i>
                    Rendez-vous
                </button>

                <button class="btn-second">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Connexion
                </button>

            </div>

        </div>

    </section>

    <!-- SERVICES -->

    <section class="services">

        <div class="container">

            <div class="title">
                <h2>Nos Services</h2>
            </div>

            <div class="row g-4">

                <div class="col-md-4">

                    <div class="card-service">

                        <i class="fa-solid fa-user-doctor"></i>

                        <h3>Consultations</h3>

                        <p>
                            Consultations médicales modernes
                            avec suivi complet des patients.
                        </p>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="card-service">

                        <i class="fa-solid fa-pills"></i>

                        <h3>Pharmacie</h3>

                        <p>
                            Gestion du stock des médicaments
                            et des prescriptions.
                        </p>

                    </div>

                </div>

                <div class="col-md-4">

                    <div class="card-service">

                        <i class="fa-solid fa-calendar-days"></i>

                        <h3>Rendez-vous</h3>

                        <p>
                            Réservation simple et rapide
                            des consultations médicales.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- ABOUT -->

    <section class="about">

        <div class="container">

            <div class="row align-items-center">

                <div class="col-md-6">

                    <img src="images/medical.png" alt="medical">

                </div>

                <div class="col-md-6">

                    <h2>Pourquoi choisir CampusCare ?</h2>

                    <p>
                        CampusCare facilite la gestion des centres
                        de santé universitaires grâce à une plateforme
                        moderne, sécurisée et intuitive.
                    </p>

                    <p>
                        Le système permet la gestion des dossiers médicaux,
                        des rendez-vous, des consultations et du stock
                        de médicaments.
                    </p>

                    <button class="btn-main">
                        Découvrir plus
                    </button>

                </div>

            </div>

        </div>

    </section>

    <!-- FOOTER -->

    <footer>

        <p>
            © 2026 CampusCare | Tous droits réservés
        </p>

    </footer>

    <!-- JS -->

    <script src="script.js"></script>

    <!-- Bootstrap JS -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>