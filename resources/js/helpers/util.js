export default {
    showSuccess(title, message){
        Swal.fire({
            title: title,
            text: message,
            icon: 'success'
        })
    },
    showError(title, message){
        Swal.fire({
            type: "",
            title: title,
            text: message
        });
    }
}