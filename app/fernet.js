/* eslint-env browser */
/* eslint eqeqeq: "off" */
(() => {
  const replace = (target, url, options = {}) => {
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
    options.headers = {'X-Fernet-Js':  1}
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
      })
  }

  const load = () => {
    const routerElement = document.getElementById('__fr')
    document.addEventListener('click', event => {
      for (let target = event.target; target && target != this; target = target.parentNode) {
        if (target.matches && target.matches('a')) {
          if (target.href.match(/__fe/)) {
            event.preventDefault()
            replace(target.closest('.__fw'), target.href)
          }
          if (routerElement && target.classList.contains('__fl')) {
            event.preventDefault()
            let activeClass = target.dataset.activeClass
            document.querySelector('.__fl.' + activeClass)?.classList.remove(activeClass)
            target.classList.add(activeClass)
            replace(routerElement, target.href)
            history.pushState({ link: target.innerHTML }, target.innerHTML, target.href)
            target.blur()
          }
        }
      }
    })
    document.addEventListener('submit', event => {
      for (let target = event.target; target && target !== this; target = target.parentNode) {
        if (target.matches && target.matches('form')) {
          event.preventDefault()
          let options = {
            method: target.method,
            body: new FormData(target)
          }
          replace(target.closest('.__fw'), target.action, options)
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
