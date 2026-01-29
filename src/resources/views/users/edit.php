<h1>Edit User</h1>
<form id="edit" action='/api/users/<?= $user['id'] ?>'>
    <input type='hidden' name='id' value='<?= $user['id'] ?>'>
    <label>Name:</label><br>
    <input type='text' name='name' value='<?= $user['name'] ?>' required><br><br>
    <label>Email:</label><br>
    <input type='email' name='email' value='<?= $user['email'] ?>' required><br><br>
    <button type='submit'>Update</button> <a href="/users">Cancel</a>
</form>

<script>
    // Example to handle form submission via Fetch API with FormData
    /*document.getElementById('edit').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch(this.action, {
            method: 'PUT',
            body: formData,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        })
        .then(response => response.text())
        .then(data => {
            console.log('Success:', data);
            window.location.href = '/users';
        }).catch((error) => {
            console.error('Error:', error);
        });
    });*/

    document.getElementById('edit').addEventListener('submit', function (e) {
        e.preventDefault();
        console.log('Submitting form via JSON...');
        const formData = new FormData(this);
        const json = formDataToJSON(formData);

        // Validate JSON output
        console.log('Form Data as JSON:', json);

        fetch(this.action, {
            method: 'PUT',
            body: json,
            headers: { 'Content-Type': 'application/json' }
        })
        .then(response => response.text())
        .then(data => {
            console.log('Success:', data);
            window.location.href = '/users';
        }).catch((error) => {
            console.error('Error:', error);
        });
    });
</script>