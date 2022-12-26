export default function showMedias(){
  let showMoreBtn = document.querySelector('#see_medias_btn');
  let trickMediasList = document.querySelector('#medias_container');
  let chevronDown = document.querySelector('.fa-chevron-down');
  showMoreBtn.addEventListener('click', () => {
    if(trickMediasList.getAttribute('data-display') === "false"){
      trickMediasList.style.display = 'block';
      trickMediasList.setAttribute('data-display', 'true');
      chevronDown.classList.add('open');
    } else {
      trickMediasList.style.display = 'none';
      trickMediasList.setAttribute('data-display', 'false');
      chevronDown.classList.remove('open');
    }
  });
};