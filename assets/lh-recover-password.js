function lh_recover_password_run_js(){

if (document.getElementById("lh_recover_password-front_end-form")){

document.getElementById("lh_recover_password-front_end-form-nonce").value = document.getElementById("lh_recover_password-front_end-form").getAttribute("data-lh_recover_password-front_end-nonce");

}

}

lh_recover_password_run_js();