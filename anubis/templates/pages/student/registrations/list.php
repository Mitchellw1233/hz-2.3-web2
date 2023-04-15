<?php
include dirname(__DIR__, 3) .'/util/common/ide_helper.php';

/**
 * @var array<int, \App\Entity\ExamRegistration> $registrations
 */
$title = 'Registrations';
$metaTitle = 'Registrations - Student';
$metaDescription = $title;
?>

<?php $start_block('body'); ?>

<div class="container p-4">
    <div class="row mb-4">
        <div class="col-6">
            <h1><?php echo $title ?></h1>
        </div>
        <div class="col-6 d-flex justify-content-end align-items-end">
            <a href="/" class="btn btn-primary fw-bold px-3">+</a>
        </div>
    </div>
    <table class="table" data-table>
        <thead>
        <tr>
            <th scope="col">exam</th>
            <th scope="col">grade</th>
            <th scope="col">graded_at</th>
            <th scope="col">exam date</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($registrations as $registration) {
            echo sprintf('
                    <tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                    </tr>
                ',
                $registration->getExam()->getName(),
                $registration->getGrade(),
                $registration->getGradedAt(),
                $registration->getExam()->getExamDate()->format('Y-m-d H:i'),
            );
        } ?>
        </tbody>
    </table>
</div>

<?php $end_block() ?>

<?php require $globals['basePath'].'util/common/main.php'; ?>
