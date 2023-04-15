<?php
include dirname(__DIR__, 3) .'/util/common/ide_helper.php';

/**
 * @var ?\App\Entity\Teacher $teacher
 * @var ?bool $editable
 * @var ?array<int, string> $errors
 */
$title = 'Teacher - ' . ($teacher?->getName() ?? 'create');
$metaTitle = 'Teacher - ' . ($teacher?->getName() ?? 'create') . ' - Admin';
$metaDescription = $title;
$editable ??= false;
?>

<?php $start_block('body'); ?>

<div class="container p-4">
    <div class="mb-4 row">
        <div class="col-6 d-flex align-items-center">
            <a href="/admin/teachers" class="btn btn-secondary fw-bold px-3 me-4"><-</a>
            <h1><?php echo $title ?></h1>
        </div>
        <div class="col-6 d-flex justify-content-end align-items-center">
            <?php
            if (!$editable && $teacher !== null) {
                echo sprintf('<a href="/admin/teachers/%s?edit=true" class="btn btn-primary">Edit</a>', $teacher->getId());
            }
            if ($teacher !== null) {
                echo sprintf('<a href="/admin/teachers/%s/delete" class="btn btn-danger ms-2" data-confirm>Delete</a>', $teacher->getId());
            }
            ?>
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
                    <label class="form-label fw-semibold">ID</label>
                    <?php echo sprintf('<input class="form-control" type="text" disabled value="%s">',
                        $teacher?->getId() ?? ''
                    ); ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Firstname</label>
                    <?php echo sprintf('<input class="form-control" name="firstname" type="text" %s value="%s">',
                        $editable ? '' : 'disabled',
                        $teacher?->getFirstName() ?? ''
                    );
                    ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Lastname</label>
                    <?php echo sprintf('<input class="form-control" name="lastname" type="text" %s value="%s">',
                        $editable ? '' : 'disabled',
                        $teacher?->getLastName() ?? ''
                    );
                    ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <?php echo sprintf('<input class="form-control" name="email" type="text" %s value="%s">',
                        $editable ? '' : 'disabled',
                        $teacher?->getLastName() ?? ''
                    );
                    ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Birth date</label>
                    <?php echo sprintf('<input class="form-control" name="birth_date" type="date" %s value="%s">',
                        $editable ? '' : 'disabled',
                        $teacher?->getBirthDate()->format('Y-m-d') ?? ''
                    ); ?>
                </div>
                <?php if ($editable === true) {
                    echo '
                        <div class="mb-3">
                            <input class="btn btn-primary" type="submit">
                        </div>
                        ';
                } ?>
            </form>
        </div>
    </div>
</div>

<?php $end_block() ?>

<?php require $globals['basePath'].'util/common/main.php'; ?>
