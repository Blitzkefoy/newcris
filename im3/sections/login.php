<div class="login-container">
    <h2>Login</h2>
    <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    <form action="index.php?page=login" method="post">
        <label for="login_type">Login as:</label>
        <select name="login_type" id="login_type" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>
        
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Login</button>
    </form>
</div>
