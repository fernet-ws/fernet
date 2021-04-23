/* eslint-env browser */
/* eslint eqeqeq: "off" */
(() => {
  const replace = (target, url) => {
    const waitToHide = 300
    const animate = setTimeout(() => {
      target.classList.add('__fernet_skeleton')
    }, waitToHide)
    target.querySelectorAll('a').forEach(el => {
      el.style.pointerEvents = 'none'
      el.setAttribute('aria-disabled', true)
    })
    const preventClicking = event => {
      event.preventDefault()
      event.stopPropagation()
    }
    target.addEventListener('click', preventClicking)

    const options = {
      method: 'PUT',
      body: 'fernet_replace'
    }
    fetch(url, options)
      .then(response => response.text())
      .then(html => {
        target.innerHTML = html
        clearInterval(animate)
        target.removeEventListener('click', preventClicking)
        target.classList.remove('__fernet_skeleton')
      })
      .catch(err => {
        console.error(err)
        window.location.href = url
      })
  }

  const load = () => {
    const routerElement = document.getElementById('__fr')
    document.addEventListener('click', event => {
      for (let target = event.target; target && target != this; target = target.parentNode) {
        if (target.matches && target.matches('a')) {
          if (target.href.match(/__fe/)) {
            replace(target.closest('.__fw'), target.href)
            event.preventDefault()
          }
          if (routerElement && target.classList.contains('__fl')) {
            document.querySelector('.active.__fl').classList.remove('active')
            target.classList.add('active')
            replace(routerElement, target.href)
            history.pushState({ link: target.innerHTML }, target.innerHTML, target.href)
            target.blur()
            event.preventDefault()
          }
        }
      }
    })
  }

  if (window.fetch) {
    try {
      load()
    } catch (err) {
      console.error && console.error(err)
    }
  }
})()
