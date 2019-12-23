<?php include '../view/header.php'; ?>
<main>
    <section>
        <h1>Today is day <?php echo $current_day[0]; ?></h1>
        
        <!-- for testability, please do not change these two buttons -->
        <form action="index.php" method="post">
            <input type="hidden" name="action" value="next_day">
            <input type="submit" value="Change to day <?php echo $current_day[0] + 1; ?>" />
        </form>

        <form  action="index.php" method="post">
            <input type="hidden" name="action" value="initial_db">           
            <input type="submit" value="Initialize DB (making day = 1)" />
            <br>
        </form>      
        <br>
        
        
        <h2>Today's Orders</h2>
        <?php if ($get_orders == NULL) :?>
        <p>No Orders Today</p>
        <?php else :?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Status</th>
                </tr>
                <?php foreach ($get_orders as $orders) : ?>
                <tr>
                    <td><?php echo $orders['order_id']; ?></td>
                    <td><?php echo $orders['username']; ?></td>
                    <td><?php echo $orders['status']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif;?>

    </section>
</main>
<?php include '../view/footer.php'; 