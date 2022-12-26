export default function showMedias(){
  let showMoreBtn = document.querySelector('#see_medias_btn');
  let trickMediasList = document.querySelector('#medias_container');
  showMoreBtn.addEventListener('click', () => {
    if(trickMediasList.getAttribute('data-display') === "false"){
      trickMediasList.style.display = 'block';
      trickMediasList.setAttribute('data-display', 'true');
    } else {
      trickMediasList.style.display = 'none';
      trickMediasList.setAttribute('data-display', 'false');
    }
  });
};