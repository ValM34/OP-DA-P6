export default function displayHeader(){
  let header = document.querySelector("#header");
  let value = 0;
  let scrollDown = false;
  
  window.addEventListener("scroll", (e) => {
    console.log(window.pageYOffset);
    if(window.pageYOffset >= 60 && scrollDown === true){
      header.classList.add("header-hidden");
    }

    if(window.pageYOffset >= 120 && scrollDown === false){
      header.classList.remove("header-hidden");
    }

    if(window.pageYOffset < 120){
      header.classList.remove("header-hidden");
    }
    
    if(value > window.pageYOffset){
      scrollDown = false;
    }

    if(value <= window.pageYOffset){
      scrollDown = true;
    }
    value = window.pageYOffset;
  })

}