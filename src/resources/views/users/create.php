<h1>Create User</h1>
<form action="/users/store" method="POST">
    <label>Name:</label><br>
    <input type="text" name="name" required><br><br>
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>
    <button type="submit">Save</button> <a href="/users">Cancel</a>
</form>