<?php
include dirname(__DIR__, 3) .'/util/common/ide_helper.php';

/**
 * @var array<int, \App\Entity\Teacher> $teachers
 */
$title = 'Teachers';
$metaTitle = 'Teachers - Admin';
$metaDescription = $title;
?>

<?php $start_block('body'); ?>

<div class="container p-4">
    <div class="row mb-4">
        <div class="col-6">
            <h1><?php echo $title ?></h1>
        </div>
        <div class="col-6 d-flex justify-content-end align-items-end">
            <a href="/admin/teachers/create" class="btn btn-primary fw-bold px-3">+</a>
        </div>
    </div>
    <table class="table" data-table>
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">name</th>
            <th scope="col">email</th>
            <th scope="col">birth date</th>
            <th scope="col">action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($teachers as $teacher) {
            echo sprintf('
                    <tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href="%s" class="btn btn-sm btn-danger fw-bold px-3" data-confirm>x</a></td>
                    </tr>
                ',
                $teacher->getId(),
                sprintf('<a href="/admin/teachers/%s">%s %s</a>', $teacher->getId(), $teacher->getFirstName(), $teacher->getLastName()),
                $teacher->getEmail(),
                $teacher->getBirthDate()->format('Y-m-d H:i'),
                sprintf('/admin/teachers/%s/delete', $teacher->getId())
            );
        } ?>
        </tbody>
    </table>
</div>

<?php $end_block() ?>

<?php require $globals['basePath'].'util/common/main.php'; ?>
