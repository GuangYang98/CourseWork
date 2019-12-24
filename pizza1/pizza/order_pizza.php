<?php include '../view/header.php'; ?>
<main>
    <h1> Order Pizza </h1>
    <h2>Pizza Size</h2>
    <form action="index.php" method="post">
        <input type="hidden" name="action" value="finish">
        <?php foreach ($size_list as $sizes) : ?>
        <!-- get size-->
            <input type="radio" name="size" required ="required" value="<?php echo $sizes['size']; ?>"><?php echo $sizes['size']; ?><br>
        <?php endforeach;?>

        <h2>Topping</h2>
        <table>
            <tr>
                <th>meat</th>
                <th>meatless</th>
            </tr>
            <tr>
                <td><?php foreach( $meat_list as $meat ) : ?>
                    <!-- get meat-->
                    <input type="checkbox" name="meat" value="<?php echo $meat['topping']; ?>"><?php echo $meat['topping']; ?><br>
                    <?php endforeach;?></td>
                <td><?php foreach( $meatless_list as $meatless ) : ?>
                    <!-- get meatless-->
                    <input type="checkbox" name="meatless" value="<?php echo $meatless['topping']; ?>"><?php echo $meatless['topping']; ?><br>
                    <?php endforeach;?></td>
            </tr>
        </table>
        
        <!--get name-->
        <h2>name</h2>
        <select name="username" value = "<?php echo $users['username']; ?>" >
            <?php foreach ($user_list as $users) : ?>
            <option><?php echo $users['username']; ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" value="finish">done!</button><br>
    </form>

    <!-- make sure every item has selected, return $name $size $meat $meatless-->

    <p><a href="../user/user_add.php">Do not have you name?</a></p>
</main>
<?php include '../view/footer.php'; 