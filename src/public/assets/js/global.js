// You can add JavaScript here if needed (GLOBAL SCOPE)

console.log('Global JS loaded.');

function formDataToJSON(formData) {
    return JSON.stringify(Object.fromEntries(formData));
}