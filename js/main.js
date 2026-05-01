const foodList = document.getElementById("foodList");
const canteenSelect = document.getElementById("canteenSelect");
const priceRange = document.getElementById("priceRange");
const priceValue = document.getElementById("priceValue");
const applyFilter = document.getElementById("applyFilter");

async function loadCanteens() {
  try {
    const res = await fetch("api/fetch_canteens.php");
    const canteens = await res.json();

    canteenSelect.innerHTML = `<option value="">All Canteens</option>`;

    canteens.forEach(canteen => {
      canteenSelect.innerHTML += `
        <option value="${canteen.id}">${canteen.name}</option>
      `;
    });
  } catch (error) {
    console.error("Canteen loading error:", error);
  }
}

async function loadFoods() {
  const canteenId = canteenSelect.value;
  const maxPrice = priceRange.value;

  let url = `api/fetch_foods.php?max_price=${maxPrice}`;

  if (canteenId !== "") {
    url += `&canteen_id=${canteenId}`;
  }

  try {
    const res = await fetch(url);
    const foods = await res.json();

    foodList.innerHTML = "";

    if (!Array.isArray(foods) || foods.length === 0) {
      foodList.innerHTML = `<p style="text-align:center;">No food items found.</p>`;
      return;
    }

    foods.forEach(food => {
      foodList.innerHTML += `
        <div class="food-card">
          <img src="${food.image}" alt="${food.name}">
          <h3>${food.name}</h3>
          <p>${food.description || ""}</p>
          <p><strong>Canteen:</strong> ${food.canteen_name || "N/A"}</p>
          <h4>৳${food.price}</h4>
          <button onclick="alert('Added to cart: ${food.name}')">Add to Cart</button>
        </div>
      `;
    });
  } catch (error) {
    console.error("Food loading error:", error);
    foodList.innerHTML = `<p style="text-align:center;color:red;">Error loading foods.</p>`;
  }
}

priceRange.addEventListener("input", () => {
  priceValue.textContent = `৳${priceRange.value}`;
});

applyFilter.addEventListener("click", loadFoods);

loadCanteens();
loadFoods();