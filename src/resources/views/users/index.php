<h1>User Management</h1>
<a href="/">Home</a> | <a href="/users/create">Create New User</a><hr>

<?php if (empty($users)): ?>
    <p>No users found.</p>
<?php else: ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user['id'] ?></td>
                <td><?= $user['name'] ?></td>
                <td><?= $user['email'] ?></td>
                <td>
                    <a href='/users/edit/<?= $user['id'] ?>'>Edit</a>
                    <form action='/users/delete' method='POST' style='display:inline;' onsubmit='return confirm("Are you sure?");'>
                        <input type='hidden' name='id' value='<?= $user['id'] ?>'>
                        <button type='submit'>Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>