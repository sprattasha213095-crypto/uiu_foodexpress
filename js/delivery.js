document.getElementById("availability").addEventListener("change",async e=>{
  const fd = new FormData();
  fd.append("status",e.target.value);
  await fetch("api/update_availability.php",{method:"POST",body:fd});
});

async function loadMyOrders(){
  const res = await fetch("api/get_orders.php");
  const orders = await res.json();
  const tbody=document.querySelector("#deliveryTable tbody");
  tbody.innerHTML = orders.map(o=>`
    <tr>
      <td>${o.id}</td><td>${o.user_id}</td><td>${o.room_number}</td>
      <td>${o.status}</td>
      <td>
        <select onchange="updateStatus(${o.id},this.value)">
          <option value="">Change</option>
          <option>Out for Delivery</option>
          <option>Delivered</option>
        </select>
      </td>
    </tr>`).join("");
}
async function updateStatus(id,status){
  const fd = new FormData();
  fd.append("order_id",id);
  fd.append("status",status);
  const res = await fetch("api/update_status.php",{method:"POST",body:fd});
  const text=await res.text();
  alert(text.includes("success")?"Updated":"Error: "+text);
  loadMyOrders();
}
loadMyOrders();
