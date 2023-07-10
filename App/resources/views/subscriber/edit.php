<?php
    use Session\Login;

    Login::requiredLogin();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Processing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!-- <link rel="stylesheet" type="text/css" href="./resources/styles/style.css" /> -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg bg-body-tertiary d-flex justify-content-between">
            <div class="mx-3">
                <div class="collapse navbar-collapse">
                    <a class="nav-link" href="<?= APP_URL.'/subscriber' ?>">Subscribers list</a>
                </div>
            </div>
            <div class="mx-3">
                <div class="collapse navbar-collapse">
                    <a class="nav-link" href="<?= APP_URL.'/user/logout' ?>">Logout</a>
                </div>
            </div>

        </nav>
        <div class="row">
            <div class="col-12">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="inputEmail" class="form-label">Email</label>
                    <input value="<?= $editData['email'] ?>" type="email" class="form-control" name="inputEmail" id="inputEmail" aria-describedby="emailHelp" required>
                </div>
                <div class="mb-3">
                    <label for="inputName" class="form-label">Name</label>
                    <input value="<?= $editData['name'] ?>" type="text" class="form-control" name="inputName" id="inputName" aria-describedby="emailHelp" required>
                </div>
                <div class="mb-3">
                    <label for="inputPhone" class="form-label">Phone</label>
                    <input value="<?= $editData['phone'] ?>" type="text" class="form-control" name="inputPhone" id="inputPhone" aria-describedby="emailHelp" required>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>