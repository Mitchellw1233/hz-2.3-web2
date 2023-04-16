<?php

use App\Entity\Admin;
use App\Entity\Interface\UserInterface;
use App\Entity\Student;
use App\Entity\Teacher;use Slimfony\HttpFoundation\Request;

/**
 * @var ?UserInterface $user
 * @var Request $request
 */
$user = $globals['user'];
$request = $globals['request'];

class NavItem
{
    public function __construct(
        public string $path,
        public string $name
    ) {
    }
}

$_studentNav = [
    new NavItem('/', 'Dashboard'),
    new NavItem('/grades', 'Cijfers'),
    new NavItem('/registrations', 'Tentameninschrijvingen'),
];
$_teacherNav = [
    new NavItem('/', 'Dashboard'),
    new NavItem('/admin/exams', 'Tentamens'),
];
$_adminNav = [
    new NavItem('/', 'Dashboard'),
    new NavItem('/admin/exams', 'Tentamens'),
    new NavItem('/admin/teachers', 'Leraren'),
    new NavItem('/admin/students', 'Studenten'),
];

if ($user instanceof Student) {
    $_nav = $_studentNav;
} elseif ($user instanceof Teacher) {
    $_nav = $_teacherNav;
} elseif ($user instanceof Admin) {
    $_nav = $_adminNav;
} else {
    $_nav = [];
}
?>

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand fw-semibold" href="/">Anubis</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <?php
                    foreach ($_nav as $navItem) {
                        echo sprintf('
                            <li class="nav-item">
                                <a class="nav-link %s" aria-current="page" href="%s">%s</a>
                            </li>
                        ',
                            (str_starts_with($request->getUri()->getPath(), $navItem->path)) ? 'active' : '',
                        $navItem->path,
                        $navItem->name
                        );
                    }
                ?>
            </ul>
            <div class="ms-auto">
                <?php
                    if ($user !== null) {
                        echo '<a href="/auth/profile" class="btn btn-secondary">Profiel</a>';
                        echo '<a href="/auth/logout" class="btn btn-danger ms-2">Uitloggen</a>';
                    } else {
                        echo '<a href="/auth/login" class="btn btn-primary">Inloggen</a>';
                    }
                ?>
            </div>
        </div>
    </div>
</nav>
