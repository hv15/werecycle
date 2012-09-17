<?php foreach ($recycle_types as $recycle_type): ?>

    <h2><?php echo $recycle_type['recycle_type'] ?></h2>
    <div id="main">
        <?php echo $recycle_type['name'] ?>
    </div>
    
<?php endforeach ?>