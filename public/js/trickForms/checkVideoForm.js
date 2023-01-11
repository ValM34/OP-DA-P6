export default function checkVideoForm(inputId, labelFor){
  let videos = document.querySelector(inputId);
  let newValue = '';
  let label = document.querySelector(labelFor);
  console.log(label);
  let newErrorMessage = document.createElement('div');
  newErrorMessage.classList.add('js-form-error-message');
  let nbOfErrors = 0;

  videos.addEventListener('change', () => {
    nbOfErrors = 0;
    console.log(videos.value);
    newValue = videos.value.replace(/ /g, '');
    let separators = ", ;";
    let videosArray = newValue.split(new RegExp(`[${separators}]`));
    for (let video of videosArray) {
      if (video.startsWith('https://www.youtube.com') || video.startsWith('https://vimeo.com')) {
      } else {
        nbOfErrors++;
        videosArray = videosArray.filter(e => e !== video);
      }
    }
    videosArray = videosArray.map((e) => e.replace('https://www.youtube.com/watch?v=', 'https://www.youtube.com/embed/'));
    videosArray = videosArray.map((e) => e.replace('https://vimeo.com/', 'https://player.vimeo.com/video/'));
    console.log(videosArray);
    if(nbOfErrors > 0){
      newErrorMessage.innerText = `Le format de ${nbOfErrors} vidéos est erroné. Veuillez seulement ajouter des url provenant de youtube ou vimeo.`;
      label.appendChild(newErrorMessage);
    } else {
      newErrorMessage.remove();
    }
  })
}