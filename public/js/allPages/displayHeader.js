export default function displayHeader(){
  let header = document.querySelector("#header");
  let value = 0;
  let scrollDown = false;
  let stopEvent = 1;
  let anchor = 0;

  window.addEventListener("scroll", () => {

    if(window.location.href.includes("#tricks-container") && window.pageYOffset >= 120 && anchor === 0){
      header.classList.add("header-hidden");
      stopEvent = 0;
      anchor++;
    }

    if(window.pageYOffset >= 60 && scrollDown === true){
      header.classList.add("header-hidden");
    }

    if(window.pageYOffset >= 120 && scrollDown === false){
      if(stopEvent > 0){
        header.classList.remove("header-hidden");
      }
    }

    if(window.pageYOffset < 120){
      if(stopEvent > 0){
        header.classList.remove("header-hidden");
      }
    }
    
    if(value > window.pageYOffset){
      scrollDown = false;
    }

    if(value <= window.pageYOffset){
      scrollDown = true;
    }
    value = window.pageYOffset;
    stopEvent++;
  })

  let trickAnchor = document.querySelector("#trick_anchor");
  trickAnchor.addEventListener("click", () => {
    header.classList.add("header-hidden");
    stopEvent = 0;
  });
}