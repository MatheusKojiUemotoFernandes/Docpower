const form = document.getElementById("form");
const email = document.getElementById("email");
const password = document.getElementById("password");
const passwordConfirmetion = document.getElementById("password-confirmation");
const docu = document.getElementById("docu");
const tele = document.getElementById("tele");
const nome = document.getElementById("nome");

form.addEventListener("submit", (event) => {
    event.preventDefault();

    checkForm ();
})

function checkInputNome(){
    const nomeValue = nome.value;
    
    if(nomeValue === ""){
        errorInput(nome, "Insira seu nome completo!")
    }else{
        const formItem = nome.parentElement;
        formItem.classList = "form-content"
    }
}

function checkInputTele(){
    const teleValue = tele.value;
    
    if(teleValue === ""){
        errorInput(tele, "Telefone é obrigatório!")
    }else if(teleValue.length < 10){
        errorInput(tele, "O telefone está incorreto!")
    }else{
        const formItem = tele.parentElement;
        formItem.className = "form-content"
    }
}

function checkInputDocu(){
    const docuValue = docu.value;
    
    if(docuValue === ""){
        errorInput(docu, "O CNPJ/CPF é obrigatório!")
    }else if(docuValue.length < 11){
        errorInput(docu, "Os CNPJ/CPF está oncorreto")
    }else{
        const formItem = docu.parentElement;
        formItem.className = "form-content"
    }
}

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

function checkInputPasswordConfirmation(){
    const passwordValue = password.value;
    const confirmationPasswordValue = passwordConfirmetion.value
    
    if(confirmationPasswordValue === ""){
        errorInput(passwordConfirmetion, "A confirmação de senha é obrigatória!")
    }else if(confirmationPasswordValue !== passwordValue){
        errorInput(passwordConfirmetion, "As senhas não são iguais!")
    }else{
        const formItem = passwordConfirmetion.parentElement;
        formItem.className = "form-content"
    }
}

function checkForm(){

    checkInputEmail();
    checkInputPassword();
    checkInputPasswordConfirmation();
    checkInputDocu();
    checkInputTele();
    checkInputNome();

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
