document.addEventListener("DOMContentLoaded", () => {
  const foodList = document.getElementById("foodList");
  const priceRange = document.getElementById("priceRange");
  const priceValue = document.getElementById("priceValue");
  const applyFilter = document.getElementById("applyFilter");
  const canteenSelect = document.getElementById("canteenSelect");
  const cartIcon = document.getElementById("cartIcon");
  const cartCount = document.getElementById("cartCount");
  const cartModal = document.getElementById("cartModal");
  const closeCart = document.getElementById("closeCart");
  const cartItems = document.getElementById("cartItems");
  const cartTotal = document.getElementById("cartTotal");
  const deliveryForm = document.getElementById("deliveryForm");

  let cart = [];

  // ========== Load canteens ==========
  fetch("api/fetch_canteens.php")
    .then(res => res.json())
    .then(canteens => {
      canteens.forEach(c => {
        const option = document.createElement("option");
        option.value = c.name;
        option.textContent = c.name;
        canteenSelect.appendChild(option);
      });
    })
    .catch(err => console.error("Canteen fetch error:", err));

  // ========== Initial food load ==========
  loadFoods();

  // ========== Update price label ==========
  priceRange.oninput = () => {
    priceValue.textContent = `৳${priceRange.value}`;
  };

  // ========== Apply Filter ==========
  applyFilter.addEventListener("click", () => {
    const canteen = canteenSelect.value;
    const price = priceRange.value;
    loadFoods(canteen, price);
  });

  // ========== Fetch Foods ==========
  function loadFoods(canteen = "", price = "") {
    let url = "api/fetch_foods.php";
    const params = [];
    if (canteen) params.push(`canteen=${encodeURIComponent(canteen)}`);
    if (price) params.push(`price=${price}`);
    if (params.length > 0) url += "?" + params.join("&");

    fetch(url)
      .then(res => res.json())
      .then(foods => renderFoods(foods))
      .catch(err => console.error("Fetch error:", err));
  }

  // ========== Render Food Cards ==========
  function renderFoods(foods) {
    foodList.innerHTML = "";
    if (foods.length === 0) {
      foodList.innerHTML = "<p style='text-align:center;'>No foods found.</p>";
      return;
    }

    foods.forEach(f => {
      const card = document.createElement("div");
      card.className = "food-card";
      const imgSrc = f.image.startsWith("http") ? f.image : `uploads/${f.image}`;
      card.innerHTML = `
        <img src="${imgSrc}" alt="${f.name}">
        <h4>${f.name}</h4>
        <p>${f.description}</p>
        <span>৳${f.price}</span>
        <p class="canteen">${f.canteen_name}</p>
        <button class="add-btn">Add to Cart</button>
      `;

      const addBtn = card.querySelector(".add-btn");
      addBtn.addEventListener("click", () => addToCart(f));
      foodList.appendChild(card);
    });
  }

  // ========== Add to Cart ==========
  function addToCart(food) {
    const existing = cart.find(item => item.id === food.id);
    if (existing) {
      existing.quantity += 1;
    } else {
      // Include canteen_id for backend reference
      cart.push({
        id: food.id,
        name: food.name,
        price: parseFloat(food.price),
        canteen_id: food.canteen_id || food.canteenId || null,
        quantity: 1,
        image: food.image
      });
    }
    updateCart();
  }

  // ========== Update Cart ==========
  function updateCart() {
    cartCount.textContent = cart.length;
    cartItems.innerHTML = "";
    let total = 0;

    cart.forEach((item, index) => {
      total += item.price * item.quantity;
      const li = document.createElement("li");
      li.innerHTML = `
        <div class="cart-item-left">
          <img src="uploads/${item.image}" class="cart-item-img" alt="">
          <div class="cart-item-info">
            <p class="cart-item-name">${item.name}</p>
            <p class="cart-item-price">৳${item.price} × ${item.quantity}</p>
          </div>
        </div>
        <button class="remove-btn" data-index="${index}">&times;</button>
      `;
      cartItems.appendChild(li);
    });

    cartTotal.textContent = total.toFixed(2);

    // Remove buttons
    document.querySelectorAll(".remove-btn").forEach(btn => {
      btn.addEventListener("click", e => {
        const index = e.target.dataset.index;
        cart.splice(index, 1);
        updateCart();
      });
    });
  }

  // ========== Cart Modal Controls ==========
  cartIcon.addEventListener("click", () => {
    cartModal.style.display = "flex";
  });
  closeCart.addEventListener("click", () => {
    cartModal.style.display = "none";
  });

  // ========== Confirm Order ==========
  deliveryForm.addEventListener("submit", async e => {
    e.preventDefault();

    if (cart.length === 0) {
      alert("Please add items to the cart before confirming your order.");
      return;
    }

    const [student_id, full_name, room_number, building_block, phone_number] =
      Array.from(deliveryForm.querySelectorAll("input")).map(i => i.value.trim());

    const totalAmount = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);

    const orderData = {
      student_id,
      full_name,
      room_number,
      building_block,
      phone_number,
      total_amount: totalAmount,
      items: cart
    };

    try {
      const response = await fetch("api/place_order.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(orderData)
      });

      const text = await response.text();
      console.log("Order response:", text);

      if (text.includes("success")) {
        showSuccessPopup();
        cart = [];
        updateCart();
        deliveryForm.reset();
        cartModal.style.display = "none";
      } else {
        alert("❌ " + text);
      }
    } catch (error) {
      console.error("Order Error:", error);
      alert("An error occurred while placing your order.");
    }
  });
});

// ====== Success Popup ======
function showSuccessPopup() {
  const popup = document.getElementById("successPopup");
  const closeBtn = document.getElementById("closePopup");

  popup.style.display = "flex";

  // Wait until user closes it
  closeBtn.onclick = () => {
    popup.style.animation = "fadeOut 0.3s ease forwards";
    setTimeout(() => {
      popup.style.display = "none";
      popup.style.animation = "";
    }, 300);
  };

  window.onclick = event => {
    if (event.target === popup) {
      popup.style.animation = "fadeOut 0.3s ease forwards";
      setTimeout(() => {
        popup.style.display = "none";
        popup.style.animation = "";
      }, 300);
    }
  };
}
