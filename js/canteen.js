const addBtn = document.getElementById("showAddForm");
const addForm = document.getElementById("addProductForm");

addBtn.onclick = ()=> addForm.style.display = addForm.style.display==="none"?"block":"none";

addForm.addEventListener("submit", async e=>{
  e.preventDefault();
  const fd = new FormData(addForm);
  const res = await fetch("api/add_product.php",{method:"POST",body:fd});
  const text = await res.text();
  alert(text.includes("success")?"✅ Product added":"❌ "+text);
  addForm.reset();
});

async function loadOrders(){
  const res = await fetch("api/get_orders.php");
  const orders = await res.json();
  const tbody=document.querySelector("#orderTable tbody");
  tbody.innerHTML = orders.map(o=>`
    <tr>
      <td>${o.id}</td><td>${o.user_id}</td>
      <td>৳${o.total_price}</td><td>${o.status}</td>
      <td>
        <select onchange="assignDelivery(${o.id},this.value)">
          <option value="">Assign</option>
        </select>
      </td>
    </tr>`).join("");
  loadAvailable();
}

async function loadAvailable(){
  const res = await fetch("api/get_available_delivery.php");
  const people = await res.json();
  document.querySelectorAll("#orderTable select").forEach(sel=>{
    people.forEach(p=>{
      const opt=document.createElement("option");
      opt.value=p.id; opt.textContent=p.name;
      sel.appendChild(opt);
    });
  });
}

async function assignDelivery(orderId,personId){
  if(!personId)return;
  const fd = new FormData();
  fd.append("order_id",orderId);
  fd.append("delivery_person_id",personId);
  const res = await fetch("api/assign_delivery.php",{method:"POST",body:fd});
  const text=await res.text();
  alert(text.includes("success")?"Assigned":"Error: "+text);
  loadOrders();
}
loadOrders();
