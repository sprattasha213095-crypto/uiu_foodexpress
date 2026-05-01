async function loadOrders(){
  const res = await fetch("api/get_orders.php");
  const data = await res.json();
  const tbody=document.querySelector("#userOrders tbody");
  tbody.innerHTML = data.map(o=>`
    <tr><td>${o.id}</td><td>${o.canteen_id}</td>
        <td>৳${o.total_price}</td><td>${o.status}</td></tr>`).join("");
  const select=document.querySelector("#reviewForm select");
  select.innerHTML = data.filter(o=>o.status==="Delivered")
    .map(o=>`<option value="${o.id}">Order #${o.id}</option>`).join("");
}
loadOrders();

document.querySelectorAll(".star").forEach(star=>{
  star.addEventListener("click",()=>{
    document.querySelectorAll(".star").forEach(s=>s.classList.remove("active"));
    star.classList.add("active");
    document.getElementById("ratingVal").value = star.dataset.val;
  });
});

document.getElementById("reviewForm").addEventListener("submit",async e=>{
  e.preventDefault();
  const fd = new FormData(e.target);
  const res = await fetch("api/add_review.php",{method:"POST",body:fd});
  const text = await res.text();
  alert(text.includes("success")?"Thanks for your review!":"Error: "+text);
  e.target.reset();
  document.querySelectorAll(".star").forEach(s=>s.classList.remove("active"));
});
