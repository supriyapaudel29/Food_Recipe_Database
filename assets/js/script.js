document.addEventListener("DOMContentLoaded", function () {
  const input = document.querySelector("input[name='ingredients']");
  const list = document.createElement("div");
  list.classList.add("autocomplete-suggestions");
  input.parentNode.appendChild(list);

  input.addEventListener("input", function () {
    const val = input.value;
    if (val.length < 1) {
      list.innerHTML = "";
      return;
    }

    fetch(`../ajax/ingredient_autocomplete.php?q=${val}`)
      .then((res) => res.json())
      .then((data) => {
        list.innerHTML = "";
        data.forEach((item) => {
          const div = document.createElement("div");
          div.textContent = item;
          div.addEventListener("click", () => {
            input.value = item;
            list.innerHTML = "";
          });
          list.appendChild(div);
        });
      });
  });
});
