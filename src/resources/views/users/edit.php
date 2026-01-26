<h1>Edit User</h1>
<form action='/users/update' method='POST'>
    <input type='hidden' name='id' value='<?= htmlspecialchars($user['id']) ?>'>
    <label>Name:</label><br>
    <input type='text' name='name' value='<?= htmlspecialchars($user['name']) ?>' required><br><br>
    <label>Email:</label><br>
    <input type='email' name='email' value='<?= htmlspecialchars($user['email']) ?>' required><br><br>
    <button type='submit'>Update</button> <a href="/users">Cancel</a>
</form>