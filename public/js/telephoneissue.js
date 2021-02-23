function checkLogout() {
  if (document.getElementById('dropdown-menu').style.display == 'block') {
    document.getElementById('dropdown-menu').style.display = 'none'
  } else {
    document.getElementById('dropdown-menu').style.display = 'block'
  }
}