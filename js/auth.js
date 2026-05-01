// --------------------- Sign Up ---------------------
document.getElementById("signupForm")?.addEventListener("submit", function(e) {
  e.preventDefault();

  const role = document.getElementById("signupRole").value;
  const name = document.getElementById("signupName").value;
  const email = document.getElementById("signupEmail").value;
  const password = document.getElementById("signupPassword").value;
  const confirm = document.getElementById("signupConfirmPassword").value;
  const errorMsg = document.getElementById("passwordError");

  if (password !== confirm) {
    errorMsg.style.display = "block";
    errorMsg.textContent = "❌ Passwords do not match!";
    return;
  } else {
    errorMsg.style.display = "none";
  }

  const newUser = { role, name, email, password };
  localStorage.setItem("UIUUser_" + email, JSON.stringify(newUser));

  alert("✅ Registration successful!");
  window.location.href = "login.html";
});


// --------------------- Login ---------------------
const loginForm = document.getElementById("loginForm");

if (loginForm) {
  loginForm.addEventListener("submit", async function(e) {
    e.preventDefault();

    const role = document.querySelector("select").value;
    const email = document.querySelector('input[type="email"]').value;
    const password = document.querySelector('input[type="password"]').value;

    const formData = new FormData();
    formData.append("role", role);
    formData.append("email", email);
    formData.append("password", password);

    try {
      const response = await fetch("api/login.php", {
        method: "POST",
        body: formData
      });

      const data = await response.json();

      if (data.status === "success") {
        alert("✅ Login successful!");
        window.location.href = data.redirect;
      } else {
        alert("❌ " + data.message);
      }

    } catch (error) {
      console.error(error);
      alert("Login request failed. Check console.");
    }
  });
}
