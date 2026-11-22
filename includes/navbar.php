
<?php
require_once __DIR__ . "/../config/app.php";
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">

    <div class="container">

        <a class="navbar-brand fw-bold"
           href="<?= BASE_URL ?>/index.php">
            Career<span>Connect</span>
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarMenu"
            aria-controls="navbarMenu"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/index.php#home">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/index.php#about">
                        About
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/index.php#jobs">
                        Explore Jobs
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/index.php#features">
                        Features
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/index.php#contact">
                        Contact
                    </a>
                </li>

            </ul>

            <div class="ms-lg-3 d-flex gap-2">

                <a href="<?= BASE_URL ?>/auth/login.php"
                   class="btn btn-outline-primary">
                    Login
                </a>

                <a href="<?= BASE_URL ?>/auth/register.php"
                   class="btn btn-primary">
                    Register
                </a>

            </div>

        </div>

    </div>

</nav>