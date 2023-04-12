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
        public string $name,
        public string $active
    ) {
    }
}

class NavCollection
{
    public function __construct(
        public string $path,
        public string $name,
        public string $active
    ) {
    }
}

$_studentNav = [
    new NavItem('/', 'Dashboard', $request->getUri()->getPath() === '/'),
    new NavItem('/grades', 'Cijfers', $request->getUri()->getPath() === '/grades'),
    new NavItem('/exams', 'Tentamens', $request->getUri()->getPath() === '/exams'),
];
$_teacherNav = [
    new NavItem('/', 'Dashboard', $request->getUri()->getPath() === '/'),
    new NavItem('/admin/exams', 'Tentamens', $request->getUri()->getPath() === '/admin/exams'),
];
$_adminNav = [
    new NavItem('/', 'Dashboard', $request->getUri()->getPath() === '/'),
    new NavItem('/admin/exams', 'Tentamens', $request->getUri()->getPath() === '/admin/exams'),
    new NavItem('/admin/students', 'Studenten', $request->getUri()->getPath() === '/admin/students'),
];

if ($user instanceof Student) {
    $_nav = $_studentNav;
} elseif ($user instanceof Teacher) {
    $_nav = $_teacherNav;
} elseif ($user instanceof Admin) {
    $_nav = $_adminNav;
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
                        $navItem->active ? 'active' : '',
                        $navItem->path,
                        $navItem->name
                        );
                    }
                ?>
            </ul>
        </div>
    </div>
</nav>
