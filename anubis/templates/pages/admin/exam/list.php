<?php
include dirname(__DIR__, 3) .'/util/common/ide_helper.php';

/**
 * @var array<int, \App\Entity\Exam> $exams
 */
$title = 'Exams';
$metaTitle = 'Exams - Admin';
$metaDescription = $title;
?>

<?php $start_block('body'); ?>

<div class="container p-4">
    <h1 class="mb-4"><?php echo $title ?></h1>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">name</th>
                <th scope="col">teacher</th>
                <th scope="col">exam_date</th>
                <th scope="col">credits</th>
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
                    </tr>
                ',
                $exam->getId(),
                sprintf('<a href="/admin/exam/%s">%s</a>', $exam->getId(), $exam->getName()),
                sprintf('<a href="/admin/teacher/%s">%s</a>', $exam->getTeacher()->getId(), $exam->getTeacher()->getName()),
                $exam->getExamDate()->format('Y-m-d H:i:s'),
                $exam->getCredits()
                );
            } ?>
        </tbody>
    </table>
</div>

<?php $end_block() ?>

<?php require $globals['basePath'].'util/common/main.php'; ?>
