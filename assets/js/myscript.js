
const buttonSetup = document.getElementById("accountSetupButton");

buttonSetup.addEventListener("click", function() {
  // Code to be executed when the button is clicked (optional)
  console.log("Button clicked!");


});

// Optionally, trigger the click programmatically


 const urlParams = new URLSearchParams(window.location.search);
  const registration_completion = urlParams.get('registration_completion');

  

  if(registration_completion == 'false'){
    buttonSetup.click(); // This will hide the button immediately
  }