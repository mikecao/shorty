<?php
require './config.php';
require './shorty.php';
require_once './header.php';

$statement = $connection->prepare(
    'SELECT * FROM hit_detail WHERE url_id = ?'
);
$statement->execute(array($_GET['id']));
$visitoDetail = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<h3>Visitor Detail</h3>
<table class="table table-bordered">
    <thead>
    <tr>
        <th>Accessed At</th>
        <th>Browser Name</th>
        <th>Browser Version</th>
        <th>Operating System</th>
        <th>IP</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($visitoDetail as $visitor){ ?>
        <tr>
            <td><?php echo $visitor['accessed'] ?></td>
            <td><?php echo $visitor['browser_name'] ?></td>
            <td><?php echo $visitor['browser_version'] ?></td>
            <td><?php echo $visitor['os'] ?></td>
            <td><?php echo $visitor['ip'] ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<?php require_once './footer.php' ?>
