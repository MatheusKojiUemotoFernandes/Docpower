const email = document.getElementById("email");
const password = document.getElementById("password");

form.addEventListener("submit", (event) => {
    event.preventDefault();

    checkForm ();
})


function checkInputEmail(){
    const emailValue = email.value;
    
    if(emailValue === ""){
        errorInput(email, "Insira um e-mail!")
    }else{
        const formItem = email.parentElement;
        formItem.classList = "form-content"
    }
}

function checkInputPassword(){
    const passwordValue = password.value;
    
    if(passwordValue === ""){
        errorInput(password, "A senha é obrigatória!")
    }else if(passwordValue.length < 8){
        errorInput(password, "A senha precisa ter no mínimo 8 caracteres.")
    }else{
        const formItem = password.parentElement;
        formItem.className = "form-content"
    }
}

function checkForm(){

    checkInputEmail();
    checkInputPassword();

    const formItems = form.querySelectorAll(".form-content")

    const isValid = [...formItems].every( (item) => {
        return item.className === "form-content"
    });
    
    if(isValid){
        alert("Cadastrato com sucesso!");
    }
}

function errorInput(input, message){
    const formItem = input.parentElement;
    const textMessage = formItem.querySelector("a")

    textMessage.innerText = message;

    formItem.className = "form-content error";
}