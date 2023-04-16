<?php

use App\Entity\Interface\UserInterface;

include dirname(__DIR__, 2) .'/util/common/ide_helper.php';

/**
 * @var UserInterface $user
 * @var ?array<int, string> $errors
 */
$title = 'Profiel';
$metaTitle = $title;
$metaDescription = $title;
?>

<?php $start_block('body'); ?>

<div class="container p-4">
    <div class="row mb-4">
        <div class="col-6">
            <h1><?php echo $title ?></h1>
        </div>
    </div>
    <div class="row justify-content-start">
        <div class="col-12 col-md-8 col-xl-6">
            <form method="post">
                <?php if (!empty($errors)) {
                    echo sprintf('
                        <div class="mb-3 text-danger">
                            <span>%s</span>
                        </div>
                    ', $errors);
                } ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Studentnummer</label>
                    <?php echo sprintf('<input class="form-control" type="number" disabled value="%s">',
                        $user->getId(),
                    ) ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Naam</label>
                    <?php echo sprintf('<input class="form-control" type="text" disabled value="%s">',
                        $user->getName(),
                    ) ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <?php echo sprintf('<input class="form-control" type="text" disabled value="%s">',
                        $user->getEmail(),
                    ) ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <input class="form-control" name="password" type="password" required>
                </div>
                <div class="mb-3">
                    <input class="btn btn-primary" type="submit">
                </div>
            </form>
        </div>
    </div>
</div>

<?php $end_block() ?>

<?php require $globals['basePath'].'util/common/main.php'; ?>
