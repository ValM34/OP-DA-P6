export default function slider(){
  
  let containerUl = document.querySelector('#trick_medias_list'); // Container du futur ul
  let medias = document.querySelectorAll("#trick_medias_list > li") // La liste des li non clonés
  let leftArrow = document.querySelector('#left-arrow');
  let rightArrow = document.querySelector('#right-arrow');
  

  leftArrow.addEventListener('click', () => {
    console.log(containerUl)
    containerUl.scrollTo({
      left: containerUl.scrollLeft - 330,
      behavior: 'smooth'
    });

    if(containerUl.scrollLeft === 0){
      containerUl.scrollTo({
        left: containerUl.scrollWidth,
        behavior: 'smooth'
      });
    }
  });

  rightArrow.addEventListener('click', () => {
    console.log(containerUl)
    containerUl.scrollTo({
      left: containerUl.scrollLeft + 330,
      behavior: 'smooth'
    });
    console.log(containerUl.scrollLeft + containerUl.offsetWidth)
    console.log(containerUl.scrollWidth)
    console.log(containerUl.width)
    if(containerUl.scrollLeft + containerUl.offsetWidth >= containerUl.scrollWidth - 50){
      containerUl.scrollTo({
        left: 0,
        behavior: 'smooth'
      });
    }
  });
}