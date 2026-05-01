async function loadStats() {
  const res = await fetch("api/get_orders.php");
  const data = await res.json();
  document.getElementById("totalOrders").textContent = data.length;

  const users = await fetch("api/get_users.php").then(r=>r.json()).catch(()=>[]);
  document.getElementById("totalUsers").textContent = users.length;

  const canteens = await fetch("api/get_canteens.php").then(r=>r.json()).catch(()=>[]);
  document.getElementById("totalCanteens").textContent = canteens.length;

  const tbody = document.querySelector("#ordersTable tbody");
  tbody.innerHTML = data.map(o=>`
    <tr><td>${o.id}</td><td>${o.user_id}</td><td>${o.canteen_id}</td>
        <td>৳${o.total_price}</td><td>${o.status}</td></tr>`).join("");
}
loadStats();
