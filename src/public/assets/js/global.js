// You can add JavaScript here if needed (GLOBAL SCOPE)

function formDataToJSON(formData) {
    return JSON.stringify(Object.fromEntries(formData));
}

async function mapHttpResponse(response)
{
    const text = await response.text();
    if (response.ok) {
        return text ? JSON.parse(text) : null;
    } else {
        throw new Error(text);
    }
}
