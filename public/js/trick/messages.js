export default function messages() {
  // Get slug
  let slug = document.URL;
  slug = slug.split("/");
  slug = slug[5];

  let showMoreMessagesBtn = document.querySelector("#show_more_comments_btn");

  let loadingSpinner = document.querySelector("#messages_spinner_container");

  // Call API (first page)
  fetch(`http://127.0.0.1:8000/api/message/${slug}?page=1&limit=10`, {
    method: "GET",
    headers: { "Content-Type": "Application/json" },
  })
    .then((res) => {
      if (res.ok === false) {
        return (res = null);
      }
      return res.json();
    })
    .then((res) => {
      loadingSpinner.style.display = 'none';
      if (res === null) {
        showMoreMessagesBtn.classList.add("hidden");
        return;
      }
      showMoreMessagesBtn.classList.remove("hidden");
      createMessages(res);
    });

  // Call API (next pages)
  let page = 2;
  showMoreMessagesBtn.addEventListener("click", () => {
    let messagesContainer = document.querySelector("#messages_container");
    messagesContainer.appendChild(loadingSpinner);
    loadingSpinner.style.display = 'flex';
    fetch(`http://127.0.0.1:8000/api/message/${slug}?page=${page}&limit=10`, {
      method: "GET",
      headers: { "Content-Type": "Application/json" },
    })
      .then((res) => {
        console.log(res.ok);
        if (res.ok === false) {
          return (res = null);
        }
        return res.json();
      })
      .then((res) => {
        loadingSpinner.style.display = 'none';
        if (res === null) {
          showMoreMessagesBtn.classList.add("hidden");
          return;
        }
        showMoreMessagesBtn.classList.remove("hidden");
        createMessages(res);
      });

    page++;
  });

  // Create messages elements
  function createMessages(res){
    let messagesContainer = document.querySelector("#messages_container");
    for (let i = 0; i < res.messages.length; i++) {
      let messageContainer = document.createElement("div");
      messageContainer.className = "message-container";
    
      let avatar = document.createElement("img");
      avatar.className = "avatar";
      avatar.src = "../../images/avatars/" + res.messages[i].user.avatar;
      messageContainer.appendChild(avatar);
    
      let li = document.createElement("li");
      messageContainer.appendChild(li);
    
      let infosMessage = document.createElement("div");
      infosMessage.className = "infos-message";
      let strong = document.createElement("strong");
      strong.innerText = res.messages[i].user.firstname + " " + res.messages[i].user.lastname;
      infosMessage.appendChild(strong);
    
      let div = document.createElement("div");
    
      if (res.actualUser === res.messages[i].user.id) {
        let linkUpdate = document.createElement("a");
        linkUpdate.href = "../../message/update/" + res.messages[i].id;
        linkUpdate.className = "icon-container";
    
        let iconUpdate = document.createElement("i");
        iconUpdate.className = "fas fa-edit";
        linkUpdate.appendChild(iconUpdate);
        div.appendChild(linkUpdate);
    
        let linkDelete = document.createElement("a");
        linkDelete.href = "../../message/delete/" + res.messages[i].id;
        linkDelete.className = "icon-container";
    
        let iconDelete = document.createElement("i");
        iconDelete.className = "fas fa-trash";
        linkDelete.appendChild(iconDelete);
        div.appendChild(linkDelete);
      }
    
      infosMessage.appendChild(div);
    
      li.appendChild(infosMessage);
    
      let p = document.createElement("p");
      p.innerText = res.messages[i].content;
      li.appendChild(p);
    
      messageContainer.appendChild(li);
    
      messagesContainer.appendChild(messageContainer);
    }
    if (res.messages.length < 3) {
      showMoreMessagesBtn.classList.add("hidden");
    }
  }
}

