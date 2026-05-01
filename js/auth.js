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
  loginForm.addEventListener("submit", function(e) {
    e.preventDefault();

    const role = document.querySelector("select").value;
    const email = document.querySelector('input[type="email"]').value;
    const password = document.querySelector('input[type="password"]').value;

    const storedUser = JSON.parse(localStorage.getItem("UIUUser_" + email));

    if (!storedUser) {
      alert("❌ No account found with this email. Please sign up first.");
      return;
    }

    if (storedUser.password !== password || storedUser.role !== role) {
      alert("❌ Incorrect credentials or role. Please try again.");
      return;
    }

    alert(`✅ Welcome back, ${storedUser.name} (${storedUser.role})!`);
    window.location.href = "index.html";
  });
}
