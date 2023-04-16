<?php
include dirname(__DIR__) .'/util/common/ide_helper.php';

/**
 *
 */
$title = 'Anubis dashboard';
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
            <p>Welkom bij Anubis, deze website is mede mogelijk gemaakt door Slimfony (het beste framework ooit).
                Hierboven is uw navigatie te vinden.
            </p>
        </div>
    </div>
</div>

<?php $end_block() ?>

<?php require $globals['basePath'].'util/common/main.php'; ?>
