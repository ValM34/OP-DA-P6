export default function deleteTrick(){
  let deleteBtns = document.querySelectorAll(".delete-btn");
  let modal = document.querySelector("#modal");
  let overlay = document.querySelector("#overlay");
  let selectedTrick = null;
  let form = document.querySelector("#modal_form");
  deleteBtns.forEach((deleteBtn) => {
    deleteBtn.addEventListener("click", (e) => {
      e.preventDefault();

      // déclencher la modale et rajouter l'id du trick via js
      modal.classList.toggle("hidden");
      overlay.classList.toggle("hidden");

      selectedTrick = deleteBtn.getAttribute("data-trick-id");
      form.action = deleteBtn.getAttribute("data-trick-action");
    })
  })

  let closeModalBtns = document.querySelectorAll("[data-close-modal]");
  closeModalBtns.forEach((closeModalBtn) => {
    closeModalBtn.addEventListener("click", (e) => {
      e.preventDefault();
      modal.classList.toggle("hidden");
      overlay.classList.toggle("hidden");
    })
  })
}