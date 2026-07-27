/*
====================================
Authentication Javascript
====================================
*/

document.addEventListener("DOMContentLoaded", function(){

    const password = document.getElementById("password");

    const confirm = document.getElementById("confirm_password");

    // ===============================
    // Konfirmasi Password
    // ===============================

    if(confirm){

        confirm.addEventListener("keyup", function(){

            if(password.value != confirm.value){

                confirm.classList.add("is-invalid");

                confirm.classList.remove("is-valid");

            }else{

                confirm.classList.remove("is-invalid");

                confirm.classList.add("is-valid");

            }

        });

    }

});

// ===============================
// Show Hide Password
// ===============================

function togglePassword(id){

    const input = document.getElementById(id);

    if(input.type === "password"){

        input.type = "text";

    }else{

        input.type = "password";

    }

}

// ===============================
// Validasi Email
// ===============================

function validateEmail(email){

    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    return regex.test(email);

}

// ===============================
// Validasi Login
// ===============================

function validateLogin(){

    const email = document.getElementById("email").value;

    const password = document.getElementById("password").value;

    if(email == ""){

        alert("Email harus diisi.");

        return false;

    }

    if(!validateEmail(email)){

        alert("Format email tidak valid.");

        return false;

    }

    if(password == ""){

        alert("Password harus diisi.");

        return false;

    }

    return true;

}

// ===============================
// Validasi Register
// ===============================

function validateRegister(){

    const password = document.getElementById("password").value;

    const confirm = document.getElementById("confirm_password").value;

    if(password.length < 8){

        alert("Password minimal 8 karakter.");

        return false;

    }

    if(password != confirm){

        alert("Konfirmasi password tidak sama.");

        return false;

    }

    return true;

}