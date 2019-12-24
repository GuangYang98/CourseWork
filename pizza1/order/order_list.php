<?php include '../view/header.php'; ?>
<main>
    <section>
        <form action="index.php" method="post">
        <h1>Current Orders Report</h1>

        <!-- baked -->
        <h2>Need to be delivered</h2>
        <?php if ($baked_orders == NULL): ?>
        <p>No pizza needs delivery</p>
        <br>
        <?php else :?>
        <table>
            <tr>
                <th>order_id</th>
                <th>username</th>
                <th>status</th>
            </tr> 
        <?php foreach ($baked_orders as $orders) : ?>
        <tr>
            <td><?php echo $orders['order_id']; ?></td>
            <td><?php echo $orders['username']; ?></td>
            <td><?php echo $orders['status']; ?></td>
        </tr>
        <?php endforeach; ?>
        </table>
        <?php endif;?>
        
        
        <h2>Orders Preparing (in the oven)</h2>
        <?php if ($preparing_orders == NULL): ?>
        <p>No pizza needs bake</p>
        <br>
        <?php else :?>
        <table>
            <tr>
                <th>order_id</th>
                <th>username</th>
                <th>status</th>
            </tr>
        <?php foreach ($preparing_orders as $orders) : ?>
        <tr>
            <td><?php echo $orders['order_id']; ?></td>
            <td><?php echo $orders['username']; ?></td>
            <td><?php echo $orders['status']; ?></td>
        </tr>
        <?php endforeach; ?>
        </table>
        <?php endif;?>
        
        <br> 
        <label>&nbsp;</label>
            <input type="hidden" name="action" value="baked">
            <input type="submit" value="Make Oldest Pizza Baked" />
        </form>
        <!--Button for marking oldest preparing pizza as baked -->
        <br>  
    </section>
</main>
<?php include '../view/footer.php'; 