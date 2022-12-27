export default function showMoreMessages(){
  let messagesContainer = document.querySelector('#messages-container');
  let allMessages = document.querySelectorAll('#messages-container > li');

  for(let i = 10; i < allMessages.length; i++){
    allMessages[i].classList.add("hidden");
  }

  let showMoreMessagesBtn = document.querySelector("#show_more_comments_btn");

  if(allMessages.length > 10){
    showMoreMessagesBtn.classList.remove("hidden");
  }

  showMoreMessagesBtn.addEventListener("click", () => {
    let hiddenLi = document.querySelectorAll("#messages-container > li.hidden");

    if(hiddenLi.length < 10){
      for(let i = 0; i < hiddenLi.length; i++){
        hiddenLi[i].classList.remove("hidden");
      }
    } else {
      for(let i = 0; i < 10; i++){
        hiddenLi[i].classList.remove("hidden");
      }
    }
    
    hiddenLi = document.querySelectorAll("#messages-container > li.hidden");
    if(hiddenLi.length === 0){
      showMoreMessagesBtn.classList.add("hidden");
    }
  })
}