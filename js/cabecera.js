cont = 0;

document.getElementById("menu_responsive").addEventListener("click", (e) => {
    e.preventDefault();
    if (cont==0) {
        document.getElementById("contenedor_items_responsive").style.display = "none";
    }
    cont++

    if (document.getElementById("contenedor_items_responsive").style.display == "none") {
        document.getElementById("contenedor_items_responsive").style.display = "flex";
    } else {
        document.getElementById("contenedor_items_responsive").style.display = "none";
    }

})


addEventListener("resize", (e) => {
    e.preventDefault();
    
    if (window.innerWidth >= 1100) {
        document.getElementById("contenedor_items_responsive").style.display = "flex";
    } else {
        document.getElementById("contenedor_items_responsive").style.display = "none";
    }
})

