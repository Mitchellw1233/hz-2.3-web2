<?php
include dirname(__DIR__, 3) .'/util/common/ide_helper.php';

/**
 * @var array<int, \App\Entity\Exam> $exams
 * @var bool $isTeacher
 */
$title = 'Exams';
$metaTitle = 'Exams - Admin';
$metaDescription = $title;
?>

<?php $start_block('body'); ?>

<div class="container p-4">
    <div class="row mb-4">
        <div class="col-6">
            <h1><?php echo $title ?></h1>
        </div>
        <?php if ($isTeacher) {
            echo '
            <div class="col-6 d-flex justify-content-end align-items-end">
                <a href="/admin/exams/create" class="btn btn-primary fw-bold px-3">+</a>
            </div>
            ';} ?>
    </div>
    <table class="table" data-table>
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">name</th>
                <th scope="col">teacher</th>
                <th scope="col">exam_date</th>
                <th scope="col">credits</th>
                <th scope="col">action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($exams as $exam) {
                echo sprintf('
                    <tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href="%s" class="btn btn-sm btn-danger fw-bold px-3" data-confirm>x</a></td>
                    </tr>
                ',
                $exam->getId(),
                sprintf('<a href="/admin/exams/%s">%s</a>', $exam->getId(), $exam->getName()),
                sprintf('<a href="/admin/teachers/%s">%s</a>', $exam->getTeacher()->getId(), $exam->getTeacher()->getName()),
                $exam->getExamDate()->format('Y-m-d H:i'),
                $exam->getCredits(),
                sprintf('/admin/exams/%s/delete', $exam->getId())
                );
            } ?>
        </tbody>
    </table>
</div>

<?php $end_block() ?>

<?php require $globals['basePath'].'util/common/main.php'; ?>
