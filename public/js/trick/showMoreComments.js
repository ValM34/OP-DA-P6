export default function showMoreComments(){
  let commentsContainer = document.querySelector('#comments-container');
  let allComments = document.querySelectorAll('#comments-container > li');

  for(let i = 10; i < allComments.length; i++){
    allComments[i].classList.add("hidden");
  }

  let showMoreCommentsBtn = document.querySelector("#show_more_comments_btn");

  showMoreCommentsBtn.addEventListener("click", () => {
    let hiddenLi = document.querySelectorAll("#comments-container > li.hidden");

    if(hiddenLi.length < 10){
      for(let i = 0; i < hiddenLi.length; i++){
        hiddenLi[i].classList.remove("hidden");
      }
    } else {
      for(let i = 0; i < 10; i++){
        hiddenLi[i].classList.remove("hidden");
      }
    }
    
    hiddenLi = document.querySelectorAll("#comments-container > li.hidden");
    if(hiddenLi.length === 0){
      showMoreCommentsBtn.classList.add("hidden");
    }
  })
}