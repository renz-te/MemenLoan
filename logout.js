  const logoutBtn = document.getElementById("logoutBtn");
  const logoutPopup = document.getElementById("logoutPopup");
  const confirmLogout = document.getElementById("confirmLogout");
  const cancelLogout = document.getElementById("cancelLogout");

  // Show popup when clicking Logout
  logoutBtn.addEventListener("click", () => {
    logoutPopup.style.display = "flex";
  });

  // Confirm logout → redirect to PHP
  confirmLogout.addEventListener("click", () => {
    window.location.href = "logout.php";
  });

  // Cancel → hide popup
  cancelLogout.addEventListener("click", () => {
    logoutPopup.style.display = "none";
  });
  
  
  