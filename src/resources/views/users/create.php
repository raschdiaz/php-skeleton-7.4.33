<h1>Create User</h1>
<form id="create" action="/api/users">
    <label>Name:</label><br>
    <input type="text" name="name" required><br><br>
    <label>Email:</label><br>
    <input type="email" name="email" required><br><br>
    <button type="submit">Save</button> <a href="/users">Cancel</a>
</form>

<script>

    document.getElementById('create').addEventListener('submit', function (e) {
        e.preventDefault();
        console.log('Submitting form via JSON...');
        const formData = new FormData(this);
        let json = formDataToJSON(formData);
        
        let formValid = true;
        // Validate JSON output
        console.log('Form Data as JSON:', json);

        if (!formValid) {
            console.log('Form validation failed.');
            return;
        }

        fetch(this.action, {
            method: 'POST',
            body: json,//'{"email":"test@test"}',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(mapHttpResponse) // Custom global function to map HTTP response
        .then(data => {
            console.log('Success:', data);
            window.location.href = '/users';
        }).catch((error) => {
            console.error(error);
        });
    });
</script>