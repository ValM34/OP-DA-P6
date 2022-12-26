export default function arrowToUp(){
  let tricksContainer = document.querySelector('.tricks-container');
  let arrowToUp = document.querySelector('.arrow-to-up')
  let allTricksItems = document.querySelectorAll('.trick-item');
  if(allTricksItems.length > 15){
    arrowToUp.style.display = "flex";
  }
}