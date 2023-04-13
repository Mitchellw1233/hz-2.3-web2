<?php
include dirname(__DIR__, 3) .'/util/common/ide_helper.php';

/**
 * @var ?\App\Entity\Exam $exam
 * @var ?bool $editable
 * @var \App\Entity\Teacher[] $teachers
 */
$title = 'Exam - ' . ($exam?->getName() ?? 'create');
$metaTitle = 'Exam - ' . ($exam?->getName() ?? 'create') . ' - Admin';
$metaDescription = $title;
$editable ??= false;
?>

<?php $start_block('body'); ?>

<div class="container p-4">
    <div class="mb-4 row">
        <div class="col-6 d-flex align-items-center">
            <a href="/admin/exams" class="btn btn-secondary fw-bold px-3 me-4"><-</a>
            <h1><?php echo $title ?></h1>
        </div>
        <div class="col-6 d-flex justify-content-end align-items-center">
            <?php
                if (!$editable && $exam !== null) {
                    echo sprintf('<a href="/admin/exams/%s?edit=true" class="btn btn-primary">Edit</a>', $exam->getId());
                }
                if ($exam !== null) {
                    echo sprintf('<a href="/admin/exams/%s/delete" class="btn btn-danger ms-2" data-confirm>Delete</a>', $exam->getId());
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
                        $exam?->getId() ?? ''
                    ); ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Name</label>
                    <?php echo sprintf('<input class="form-control" name="name" type="text" %s value="%s">',
                        $editable ? '' : 'disabled',
                        $exam?->getName() ?? ''
                    );
                    ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Teacher</label>
                    <select class="form-select" name="teacher_id" <?php echo $editable ? '' : 'disabled' ?>>
                        <?php
                            if ($exam !== null) {
                                echo sprintf('<option value="%s" selected>%s</option>',
                                    $exam->getTeacher()->getId(), $exam->getTeacher()->getName());
                            } else {
                                echo '<option value selected></option>';
                            }

                            foreach ($teachers as $teacher) {
                                if ($teacher->getId() === $exam?->getTeacher()->getId()) {
                                    continue;
                                }
                                echo sprintf('<option value="%s">%s</option>',
                                    $teacher->getId(), $teacher->getName());
                            }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Exam date</label>
                    <?php echo sprintf('<input class="form-control" name="exam_date" type="datetime-local" %s value="%s">',
                        $editable ? '' : 'disabled',
                        $exam?->getExamDate()->format('Y-m-d H:i') ?? ''
                    ); ?>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Credits</label>
                    <?php echo sprintf('<input class="form-control" name="credits" type="number" %s value="%s">',
                        $editable ? '' : 'disabled',
                        $exam?->getCredits() ?? ''
                    );
                    ?>
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
