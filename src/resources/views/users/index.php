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
                    <form class="delete" action="/api/users/<?= $user['id'] ?>" style='display:inline;'>
                        <button type='submit'>Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
<script>
    // You can add JavaScript here if needed

    window.addEventListener('load', function() {
        console.log('User index page loaded.');
    });

    document.querySelectorAll('.delete').forEach(element => {
        element.addEventListener('click', (e) => {
            e.preventDefault();

            if(confirm("Are you sure?")) {

                console.log('Submitting form via JSON...');
                fetch(element.action, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' }
                })
                .then(response => {
                    console.log('Response status:', response);
                    if (response.ok) {
                        //return response.json();
                    } else {
                        return response.text().then(text => {
                            // Error will be handled here as text
                            throw new Error(text);
                        });
                    }
                })
                .then(data => {
                    console.log('Success:', data);
                    window.location.href = '/users';
                }).catch((error) => {
                    console.error(error);
                });

            }
            
        });

    });

</script>