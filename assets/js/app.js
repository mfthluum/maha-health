/*
====================================
MAHA Health
Global Javascript
====================================
*/

document.addEventListener("DOMContentLoaded", function () {

    console.log("🚀 MAHA Health Loaded");

});

// ===============================
// Format Angka
// ===============================

function formatNumber(number){

    return new Intl.NumberFormat("id-ID").format(number);

}

// ===============================
// Greeting
// ===============================

function greeting(){

    const hour = new Date().getHours();

    if(hour < 12){

        return "Selamat Pagi";

    }else if(hour < 15){

        return "Selamat Siang";

    }else if(hour < 18){

        return "Selamat Sore";

    }else{

        return "Selamat Malam";

    }

}

// ===============================
// Toast Notification
// ===============================

function showToast(message){

    alert(message);

}

// ===============================
// Loading Button
// ===============================

function loadingButton(button){

    button.disabled = true;

    button.innerHTML = `
        <span class="spinner-border spinner-border-sm"></span>
        Loading...
    `;

}

// ===============================
// Reset Button
// ===============================

function resetButton(button,text){

    button.disabled = false;

    button.innerHTML = text;

}