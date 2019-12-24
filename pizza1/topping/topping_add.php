<?php include '../view/header.php'; ?>

<main>
    <h1>Add Topping</h1>
    <form action="index.php" method="post" id="add_topping_form">
        <input type="hidden" name="action" value="add_topping">

        <label>Topping Name</label>
        <input type="text" name="topping_name" />
        <br>

        <label>Does this topping contain meat?</label>
        <input type="radio" name="is_meat" value="meat">meat
        <input type="radio" name="is_meat" value="meatless">meatless
        <br>

        <label>&nbsp;</label>
        <input type="submit" value="Add Topping" />
        <br>
    </form>
    <p>
        <a href="index.php?action=list_toppings">View Topping List</a>
    </p>
</main>

<?php include '../view/footer.php'; 



