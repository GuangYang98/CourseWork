<?php include '../view/header.php'; ?>
<main>
    
        <!-- first part for show -->
        <h1>Welcome Student!</h1>
        <h2>Available Sizes</h2>
        <table>
            <tr>
                <th>size</th>
                <th>diameter</th>
            </tr>
            <?php foreach ($size_list as $sizes) : ?>
            <tr>
                <td><?php echo $sizes['size']; ?></td>
                <td><?php echo $sizes['diameter']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br>
        <!-- second part for show -->
        <h2>Available Toppings</h2>
            <table>
                <tr>
                    <th>topping</th>
                    <th>is_meat</th>
                </tr>
                <?php foreach ($topping_list as $toppings) : ?>
                <tr>
                    <td><?php echo $toppings['topping']; ?></td>
                    <td><?php echo $toppings['is_meat']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <br>
        
        <!--operation (post userid)-->
        
        <!--get user--refresh the order list -->
        <!--if exist baked -- acknowledge -->
        <form action="index.php" method="post">
            <input type="hidden" name="action" value="check_status"> 
            <h2>select the user to check status: </h2>  
            <select name = "username">
                <?php foreach ($user_list as $users) : ?> 
                <option value = "<?php echo $users['username']; ?>"><?php echo $users['username']; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" value="check status" >check status</button>
        </form>
        
        <!--autopart -->
        <?php if ($username != NULL) : ?>
            <h2>Orders in progress for user <?php echo $username; ?></h2>
            <?php if ($get_personal_order == null) :?>
                <p>No orders in progress for this user</p>
            <?php else :?>
                <?php foreach( $get_personal_order as $order ) : ?><?php $flag=0; ?>
                <table>
                        <tr>
                            <td><?php echo $order['order_id']; ?></td>
                            <td><?php echo $order['username']; ?></td>
                            <td><?php echo $order['status']; ?></td>
                            <?php if ($order['status'] == "Baked"): $flag = 1;?> <?php endif;?>
                        </tr>
                </table>
                <?php endforeach; ?>
                <?php if ($flag) :?>
                    <form action="index.php" method="post">
                        <input type="hidden" name="username" value="<?php echo $username; ?>">
                        <input type="hidden" name="action" value="acknow">
                        <button type="submit" value="acknow" >Acknowledge Delivery of Baked Pizzas</button>
                    </form>
                <?php endif;?>
            <?php endif ;?>
        <?php endif;?>    
        

        
        <!--order more pizza-->
        <p class = "last_paragraph">
            <a href="?action=order_pizza">Order a New Pizza</a>
        </p>
    
</main>
<?php include '../view/footer.php';