document.getElementById("loginForm").addEventListener("submit", async (e) => {
  e.preventDefault();
  const formData = new FormData(e.target);

  const res = await fetch("api/login.php", { method: "POST", body: formData });
  const data = await res.json();

  if (data.status === "success") {
    window.location.href = data.redirect;
  } else {
    alert(data.message);
  }
});
