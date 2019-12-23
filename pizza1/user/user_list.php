<?php include '../view/header.php'; ?>
<main>
    <section>
        <h1>User List</h1>
        <table>
            <tr>
                <th>User Name</th>
                <th>Room</th>
                <th>&nbsp;</th>
            </tr>
          <!-- <p>TODO: table of toppings</p> -->

        <?php foreach ($users as $shop_users) : ?>
        <tr>
            <td><?php echo $shop_users['username']; ?></cd>
            <td><?php echo $shop_users['room']; ?></td>
                <td><form action="." method="post">
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user_id" value="<?php echo $shop_users['id']; ?>">
                    <input type="submit" value="Delete" >
                </form>
                <!-- TODO: delete button -->
            </td>
        </tr>
        <?php endforeach; ?>
        </table>
        <p class = "last_paragraph">
            <a href="?action=show_add_form">Add User</a>
        </p>
    </section>
</main>
<?php include '../view/footer.php'; 
