<?php
include dirname(__DIR__, 2) .'/util/common/ide_helper.php';

/**
 * @var ?array<int, string> $errors
 */
$title = 'Login';
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
            <form method="post">
                <?php if (!empty($errors)) {
                    echo sprintf('
                        <div class="mb-3 text-danger">
                            <span>%s</span>
                        </div>
                    ', $errors);
                } ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Role</label>
                    <select class="form-select" name="role">
                        <option value="student" selected>Student</option>
                        <option value="teacher">Leraar</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input class="form-control" name="email" type="email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password</label>
                    <input class="form-control" name="password" type="password" required>
                </div>
                <div class="mb-3">
                    <input class="btn btn-primary" type="submit">
                </div>
            </form>
        </div>
    </div>
</div>

<?php $end_block() ?>

<?php require $globals['basePath'].'util/common/main.php'; ?>
