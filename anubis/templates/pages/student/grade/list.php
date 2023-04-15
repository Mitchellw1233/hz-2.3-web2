<?php
include dirname(__DIR__, 3) .'/util/common/ide_helper.php';

/**
 * @var array<int, \App\Entity\ExamRegistration> $grades
 */
$title = 'Grades';
$metaTitle = 'Grades - Student';
$metaDescription = $title;
?>
<?php $start_block('body'); ?>

<div class="container p-4">
    <div class="row mb-4">
        <div class="col-6">
            <h1><?php echo $title ?></h1>
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
        <?php foreach ($grades as $grade) {
            echo sprintf('
                    <tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                    </tr>
                ',
                $grade->getExam()->getName(),
                $grade->getGrade(),
                $grade->getGradedAt(),
                $grade->getExam()->getExamDate()->format('Y-m-d H:i'),
            );
        } ?>
        </tbody>
    </table>
</div>

<?php $end_block() ?>

<?php require $globals['basePath'].'util/common/main.php'; ?>
