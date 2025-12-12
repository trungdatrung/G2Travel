<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script>

  function alert(type,msg,position='body')
  {
    let bs_class = (type == 'success') ? 'alert-success' : 'alert-danger';
    let element = document.createElement('div');

    let strong = document.createElement('strong');
    strong.textContent = msg;
    strong.classList.add('me-3');
    
    let button = document.createElement('button');
    button.type = 'button';
    button.classList.add('btn-close');
    button.setAttribute('data-bs-dismiss','alert');
    button.setAttribute('aria-label','Close');

    element.innerHTML = `
      <div class="alert ${bs_class} alert-dismissible fade show" role="alert">
      </div>
    `;
    element.firstChild.appendChild(strong);
    element.firstChild.appendChild(button);


    if(position=='body'){
      document.body.append(element);
      element.classList.add('custom-alert');
    }
    else{
      document.getElementById(position).appendChild(element);
    }
    setTimeout(remAlert, 2000);
  }

  function remAlert(){
    document.getElementsByClassName('alert')[0].remove();
  }

    
  function setActive()
  {
    let navbar = document.getElementById('dashboard-menu');
    let a_tags = navbar.getElementsByTagName('a');

    for(i=0; i<a_tags.length; i++)
    {
      let file = a_tags[i].href.split('/').pop();
      let file_name = file.split('.')[0];

      if(document.location.href.indexOf(file_name) >= 0){
        a_tags[i].classList.add('active');
      }

    }
  }
  setActive();
</script>