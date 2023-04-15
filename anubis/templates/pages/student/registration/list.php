<?php
include dirname(__DIR__, 3) .'/util/common/ide_helper.php';

/**
 * @var array<int, \App\Entity\ExamRegistration> $registrations
 */
$title = 'Registrations';
$metaTitle = 'Registrations - Student';
$metaDescription = $title;
?>

<?php dump($all) ?>

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
            <th scope="col">exam date</th>
            <th scope="col">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($registrations as $registration) {
            echo sprintf('
                    <tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href="%s" class="btn btn-sm btn-danger fw-bold px-3" data-confirm>Uitschrijven</a></td>
                    </tr>
                ',
                $registration->getExam()->getName(),
                $registration->getExam()->getExamDate()->format('Y-m-d H:i'),
                sprintf('/registrations/%s/deregister', $registration),
            );
        }

        foreach ($exams as $exam) {
            if ($student !== null) {
                foreach ($registrations as $registration) {
                    if ($registration->getExam()->getId() === $exam->getId()) {
                        continue 2;
                    }
                }
            }

            echo sprintf('
                    <tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href="%s" class="btn btn-sm btn-danger fw-bold px-3" data-confirm>Inschrijven</a></td>
                    </tr>
                ',
                $exam->getName(),
                $exam->getExamDate()->format('Y-m-d H:i'),
                sprintf('/registrations/%s/register', $exam),
            );
        }
        ?>
        </tbody>
    </table>
</div>

<?php $end_block() ?>

<?php require $globals['basePath'].'util/common/main.php'; ?>
