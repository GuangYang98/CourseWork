<?php include '../view/header.php'; ?>
<main>
    <section>
        <h1>Topping List</h1>

	<h2>Toppings</h2>
        <table>
            <tr>
                <th>Topping Name</th>
                <th>Has Meat?</th>
                <th>&nbsp;</th>
            </tr>
        <!-- <p>TODO: table of toppings</p> -->
        <?php foreach ($toppings as $menu_toppings) : ?>
        <tr>
            <td><?php echo $menu_toppings['topping']; ?></td>
            <td><?php echo $menu_toppings['is_meat']; ?></td>
            <td><form action="." method="post" >
                    <input type="hidden" name="topping_name"
                            value="<?php echo $menu_toppings['topping']; ?>">
                    <input type="hidden" name="topping_id"
                            value="<?php echo $menu_toppings['id']; ?>">
                    <input type="submit" value="Delete" >
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </table>
        <p class = "last_paragraph">
            <a href="index.php?action=show_add_form">Add Topping</a>
        </p>
    </section>
</main>
<?php include '../view/footer.php';  
