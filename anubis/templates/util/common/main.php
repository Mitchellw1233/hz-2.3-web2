<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $metaTitle ?? '' ?></title>
    <meta name="description" content="<?php echo $metaDescription ?? '' ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <style >
        font-family: 'Manrope', sans-serif;
    </style>
    <?php echo $get_block('head') ?>
</head>
<body>
    <?php require $globals['basePath'].'util/common/navbar.php'; ?>
    <?php echo $get_block('body') ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function () {
            $('[data-table]').DataTable();
        });
        document.querySelectorAll('[data-confirm]').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                if (!confirm('Are you sure?')) {
                    e.preventDefault();
                }
            });
        })
    </script>
</body>
</html>
