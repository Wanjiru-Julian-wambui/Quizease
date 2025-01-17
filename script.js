const signUpButton = document.getElementById("signUpButton");
const signInButton = document.getElementById("signInButton");
const signInForm = document.getElementById("signin"); // Updated to match correct ID
const signUpForm = document.getElementById("signup"); // Updated to match correct ID

// Event listener for the Sign-Up button
signUpButton.addEventListener('click', function () {
    signInForm.style.display = "none";
    signUpForm.style.display = "block";
});

// Event listener for the Sign-In button
signInButton.addEventListener('click', function () {
    signInForm.style.display = "block";
    signUpForm.style.display = "none";
});
