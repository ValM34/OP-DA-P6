export default function burgerMenu(){
  
  let burgerBtn = document.querySelector('.burger-btn');
  burgerBtn.addEventListener('click', () => {
    // animation button
    let burgerLines = document.querySelectorAll('.burger-btn > span');
    burgerLines.forEach((burgerLine) => burgerLine.classList.toggle('is-active'));

    // display nav
    let nav = document.querySelector('nav');
    nav.classList.toggle('is-active');

    let overlay = document.querySelector('.overlay');
    overlay.classList.toggle('is-active');

    let headerContainer = document.querySelector('.header-container');
    let overlayIsActive = document.querySelector('.overlay.is-active');
    if(overlayIsActive){
      overlayIsActive.style.top = `${headerContainer.clientHeight + nav.clientHeight}px`;
    }
    
    // stop scrolling
    let body = document.querySelector('body');
    body.classList.toggle("overflow-hidden");
  })
}