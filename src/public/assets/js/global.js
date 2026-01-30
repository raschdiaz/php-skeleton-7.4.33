// You can add JavaScript here if needed (GLOBAL SCOPE)

function formDataToJSON(formData) {
    return JSON.stringify(Object.fromEntries(formData));
}