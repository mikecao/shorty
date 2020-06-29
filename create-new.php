<?php
require_once './header.php';
?>
<form action="">
    <h3>Shorty</h3>
    <div class="row">
        <div class="form-group col-xs-9">
            <input class="form-control input-lg" placeholder="URl to shorten" name="url" type="text">
        </div>
        <div class="form-group col-xs-3">
            <button type="submit" class="btn btn-info btn-lg">Shorten</button>
        </div>
    </div>
</form>

<?php require_once './footer.php' ?>
