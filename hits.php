<?php
require './config.php';
require './shorty.php';
require_once './header.php';

$shorty = new Shorty($hostname, $connection);

$statement = $connection->prepare(
    'SELECT * FROM urls'
);
$statement->execute();
$urlViews = $statement->fetchAll(PDO::FETCH_ASSOC);
?>
    <h3>URL Hits</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Short URL</th>
            <th>Hits</th>
            <th>Real URL</th>
            <th>Created</th>
            <th>Accessed</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($urlViews as $url){ ?>
            <tr>
                <td><?php echo '<a href="'.$hostname.'/'.$shorty->encode($url['id']).'" target="_blank">'.$hostname.'/'.$shorty->encode($url['id']).'</a>' ?></td>
                <td class="p-3 mb-2 bg-info text-white"><?php echo $url['hits'] ?></td>
                <td><?php echo $url['url'] ?></td>
                <td><?php echo $url['created'] ?></td>
                <td><?php echo $url['accessed'] ?></td>
                <td><?php echo '<a href="'.$hostname.'/visitor-detail.php?id='.$url['id'].'">Visitor Detail</a>' ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php require_once './footer.php' ?>