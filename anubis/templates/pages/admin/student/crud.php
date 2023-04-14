<?php
include dirname(__DIR__, 3) .'/util/common/ide_helper.php';

/**
 * @var ?\App\Entity\Student $student
 * @var ?bool $editable
 */
$title = 'Student - ' . ($student?->getFirstName().' '.$student?->getLastName() ?? 'create');
$metaTitle = 'Student - ' . ($student?->getFirstName().' '.$student?->getLastName() ?? 'create') . ' - Admin';
$metaDescription = $title;
$editable ??= false;
?>

<?php $start_block('body'); ?>

<div class="container p-4">
    <div class="mb-4 row">
        <div class="col-6 d-flex align-items-center">
            <a href="/admin/students" class="btn btn-secondary fw-bold px-3 me-4"><-</a>
            <h1><?php echo $title ?></h1>
        </div>
        <div class="col-6 d-flex justify-content-end align-items-center">
            <?php
            if (!$editable && $student !== null) {
                echo sprintf('<a href="/admin/students/%s?edit=true" class="btn btn-primary">Edit</a>', $student->getId());
            }
            if ($student !== null) {
                echo sprintf('<a href="/admin/students/%s/delete" class="btn btn-danger ms-2" data-confirm>Delete</a>', $student->getId());
            }
            ?>
        </div>
    </div>
    <div class="row justify-content-start">
        <div class="col-12 col-md-8 col-xl-6">
            <form method="post">
                <div class="mb-3">
                    <label class="form-label fw-semibold">ID</label>
                    <?php echo sprintf('<input class="form-control" name="id" type="text" disabled value="%s">',
                        $student?->getId() ?? ''
                    ); ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Firstname</label>
                    <?php echo sprintf('<input class="form-control" name="firstname" type="text" %s value="%s">',
                        $editable ? '' : 'disabled',
                        $student?->getFirstName() ?? ''
                    );
                    ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Lastname</label>
                    <?php echo sprintf('<input class="form-control" name="lastname" type="text" %s value="%s">',
                        $editable ? '' : 'disabled',
                        $student?->getLastName() ?? ''
                    );
                    ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <?php echo sprintf('<input class="form-control" name="email" type="text" %s value="%s">',
                        $editable ? '' : 'disabled',
                        $student?->getLastName() ?? ''
                    );
                    ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Birth date</label>
                    <?php echo sprintf('<input class="form-control" name="birth_date" type="datetime-local" %s value="%s">',
                        $editable ? '' : 'disabled',
                        $student?->getBirthDate()->format('Y-m-d H:i') ?? ''
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