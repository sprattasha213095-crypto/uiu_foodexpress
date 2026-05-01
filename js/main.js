let cart = [];

const foodList = document.getElementById("foodList");
const canteenSelect = document.getElementById("canteenSelect");
const priceRange = document.getElementById("priceRange");
const priceValue = document.getElementById("priceValue");
const applyFilter = document.getElementById("applyFilter");

const cartIcon = document.getElementById("cartIcon");
const cartCount = document.getElementById("cartCount");
const cartModal = document.getElementById("cartModal");
const closeCart = document.getElementById("closeCart");
const cartItems = document.getElementById("cartItems");
const cartTotal = document.getElementById("cartTotal");
const deliveryForm = document.getElementById("deliveryForm");

const orderNowBtn = document.querySelector(".order-btn");

const successPopup = document.getElementById("successPopup");
const closePopup = document.getElementById("closePopup");

// Load canteens
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

// Load food items
async function loadFoods() {
  const canteenId = canteenSelect.value;
  const maxPrice = priceRange.value;

  let url = `api/fetch_foods.php?price=${maxPrice}`;

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
          <button 
            class="add-cart-btn"
            data-id="${food.id}"
            data-name="${food.name}"
            data-price="${food.price}"
            data-canteen="${food.canteen_id}"
          >
            Add to Cart
          </button>
        </div>
      `;
    });

    attachCartButtons();

  } catch (error) {
    console.error("Food loading error:", error);
    foodList.innerHTML = `<p style="text-align:center;color:red;">Error loading foods.</p>`;
  }
}

// Add click event to all Add to Cart buttons
function attachCartButtons() {
  const buttons = document.querySelectorAll(".add-cart-btn");

  buttons.forEach(button => {
    button.addEventListener("click", () => {
      const item = {
        id: button.dataset.id,
        name: button.dataset.name,
        price: parseFloat(button.dataset.price),
        canteen_id: button.dataset.canteen,
        quantity: 1
      };

      addToCart(item);
    });
  });
}

// Add item to cart
function addToCart(item) {
  const existingItem = cart.find(cartItem => cartItem.id === item.id);

  if (existingItem) {
    existingItem.quantity += 1;
  } else {
    cart.push(item);
  }

  updateCart();
  alert(`${item.name} added to cart`);
}

// Update cart UI
function updateCart() {
  cartItems.innerHTML = "";

  let total = 0;
  let count = 0;

  cart.forEach((item, index) => {
    const itemTotal = item.price * item.quantity;
    total += itemTotal;
    count += item.quantity;

    cartItems.innerHTML += `
      <li>
        ${item.name} - ৳${item.price} × ${item.quantity}
        <button onclick="removeFromCart(${index})">Remove</button>
      </li>
    `;
  });

  cartTotal.textContent = total.toFixed(2);
  cartCount.textContent = count;
}

// Remove item from cart
function removeFromCart(index) {
  cart.splice(index, 1);
  updateCart();
}

// Open cart modal
function openCart() {
  if (cart.length === 0) {
    alert("Please add food items to cart first.");
    return;
  }

  cartModal.style.display = "block";
}

// Close cart modal
function closeCartModal() {
  cartModal.style.display = "none";
}

// Show success popup
function showSuccessPopup() {
  successPopup.style.display = "block";
}

// Close success popup
function hideSuccessPopup() {
  successPopup.style.display = "none";
}

// Order Now button click
if (orderNowBtn) {
  orderNowBtn.addEventListener("click", openCart);
}

// Cart icon click
if (cartIcon) {
  cartIcon.addEventListener("click", openCart);
}

// Close cart button
if (closeCart) {
  closeCart.addEventListener("click", closeCartModal);
}

// Close success popup
if (closePopup) {
  closePopup.addEventListener("click", hideSuccessPopup);
}

// Click outside modal to close
window.addEventListener("click", event => {
  if (event.target === cartModal) {
    closeCartModal();
  }

  if (event.target === successPopup) {
    hideSuccessPopup();
  }
});

// Confirm order form
if (deliveryForm) {
  deliveryForm.addEventListener("submit", async function(e) {
    e.preventDefault();

    if (cart.length === 0) {
      alert("Your cart is empty.");
      return;
    }

    const inputs = deliveryForm.querySelectorAll("input");

    const student_id = inputs[0].value;
    const full_name = inputs[1].value;
    const room_number = inputs[2].value;
    const building_block = inputs[3].value;
    const phone_number = inputs[4].value;

    const total_amount = cart.reduce((sum, item) => {
      return sum + item.price * item.quantity;
    }, 0);

    const orderData = {
      student_id,
      full_name,
      room_number,
      building_block,
      phone_number,
      total_amount,
      cart
    };

    try {
      const res = await fetch("api/save_order.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(orderData)
      });

      const text = await res.text();
      console.log(text);

      if (text.includes("success")) {
        cart = [];
        updateCart();
        deliveryForm.reset();
        closeCartModal();
        showSuccessPopup();
      } else {
        alert("Order failed: " + text);
      }

    } catch (error) {
      console.error("Order error:", error);
      alert("Order failed. Check console.");
    }
  });
}

// Price range text update
if (priceRange) {
  priceRange.addEventListener("input", () => {
    priceValue.textContent = `৳${priceRange.value}`;
  });
}

// Apply filter
if (applyFilter) {
  applyFilter.addEventListener("click", loadFoods);
}

// Load initial data
loadCanteens();
loadFoods();