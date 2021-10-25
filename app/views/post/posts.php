<h1>Posts list:</h1>
<h3>Current controller: <?php echo $this->route['controller'] ?></h6>

    <?php foreach ($products as $product) : ?>
        <h2><?php echo $product['name'] ?></h2>
        <p><?php echo $product['description'] ?></p>
        <i><?php echo $product['price'] ?> rubles</i>
        <a href="/posts/<?php echo $product['id'] ?>">more details</a>
        <hr>
    <?php endforeach; ?>