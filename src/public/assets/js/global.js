// You can add JavaScript here if needed (GLOBAL SCOPE)

console.log('Global JS loaded.');

function formDataToJSON(formData) {
    //debugger;
    const obj = {};
    formData.forEach((value, key) => {
        obj[key] = value;
    });
    return JSON.stringify(obj);
}