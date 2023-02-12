export default function deleteUser(){
  let deleteBtn = document.querySelector("#delete_account_btn");
  let modal = document.querySelector("#modal");
  let overlay = document.querySelector("#overlay");
  let selectedUser = null;
  let form = document.querySelector("#modal_form");
  let validateUserDeletion = document.querySelector("#modal_submit_btn");

  deleteBtn.addEventListener("click", (e) => {
    e.preventDefault();

    modal.classList.toggle("hidden");
    overlay.classList.toggle("hidden");

    selectedUser = deleteBtn.getAttribute("data-user-id");
    form.action = deleteBtn.getAttribute("data-trick-action");
  })

  let closeModalBtns = document.querySelectorAll("[data-close-modal]");
  closeModalBtns.forEach((closeModalBtn) => {
    closeModalBtn.addEventListener("click", (e) => {
      e.preventDefault();
      modal.classList.toggle("hidden");
      overlay.classList.toggle("hidden");
    })
  })

  validateUserDeletion.addEventListener("click", (e) => {
    e.preventDefault();
    window.location.href="http://127.0.0.1:8000/user/delete/request";
  })
}