<?php

$name = get_sub_field("name");
$address = get_sub_field("address");

?>

<br><br><br>
<section class="">
    <?php
        echo $name . " " . $address;
    ?>
</section>
